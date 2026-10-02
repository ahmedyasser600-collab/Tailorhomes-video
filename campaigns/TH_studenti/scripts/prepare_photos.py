"""Film 02 - select real Tailor Homes photos and prepare working masters.

Reads the Drive downloads in ../source/drive (file name = <driveId>__<original title>),
writes:
  ../source/selected/<ID>.jpg   orientation-corrected, sRGB, long side 3000 px (no retouching)
  ../source/photos.json         metadata for every candidate + the selection
  ../docs/tailorhomes-candidates-contact-sheet.png
Pixels are only resized/re-encoded - nothing is painted, generated or altered.
"""
import json
from pathlib import Path

from PIL import Image, ImageDraw, ImageFont, ImageOps

C = Path(__file__).resolve().parents[1]
DRIVE = C / "source" / "drive"
SEL = C / "source" / "selected"
FONT = C / "fonts" / "DMSans-Medium.ttf"

FOLDERS = {"VIA SANT'EUFEMIA": "S. Eufemia", "VIA NULLO": "Via Nullo", "DSC_81": "Vicolo Romano",
           "IMG_0824": "Vicolo Romano", "NS4_": "Colore & Design", "IMG_45": "Fitness and charme"}

# candidate pool (~20 interiors) -> (short id or None, room type)
CANDIDATES = {
    "EUFEMIA-TAILOR HOMES-76.jpg": ("SE1", "living room, staircase, orange wall"),
    "EUFEMIA-TAILOR HOMES-80.jpg": ("SE2", "entrance / orange wall into living"),
    "EUFEMIA-TAILOR HOMES-47.jpg": ("SE3", "dining, glass table"),
    "EUFEMIA-TAILOR HOMES-72.jpg": ("SE4", "bedroom, wooden beams"),
    "EUFEMIA-TAILOR HOMES-63.jpg": ("SE5", "loft living, hammock chair"),
    "EUFEMIA-TAILOR HOMES-67.jpg": (None, "bedroom, beams (duplicate angle)"),
    "EUFEMIA-TAILOR HOMES-56.jpg": (None, "bedroom, antique wardrobe"),
    "EUFEMIA-TAILOR HOMES-11.jpg": (None, "doorway detail, plant"),
    "VIA NULLO - PADOVA-20.jpg": ("VN1", "living + dining, herringbone, chandelier"),
    "VIA NULLO - PADOVA-13.jpg": ("VN2", "dining by the window (vertical)"),
    "VIA NULLO - PADOVA-30.jpg": ("VN3", "bedroom with desk / study corner"),
    "VIA NULLO - PADOVA-29.jpg": ("VN4", "kitchen, terrazzo"),
    "VIA NULLO - PADOVA-43.jpg": ("VN5", "table detail, warm light"),
    "VIA NULLO - PADOVA-17.jpg": (None, "bedroom (similar to VN3)"),
    "DSC_8115.jpg": ("VR1", "bedroom, yellow throw"),
    "DSC_8172.jpg": ("VR2", "table setting detail"),
    "DSC_8166.jpg": (None, "kitchen through doorway (vertical)"),
    "DSC_8177.jpg": (None, "kitchen worktop detail"),
    "NS4_013.JPG": ("CD1", "reading corner by the window (vertical)"),
    "NS4_021.JPG": ("CD2", "kitchen, grey cabinets"),
    "IMG_4575.JPG": (None, "kitchen (phone photo)"),
}


def folder_of(title):
    return next((v for k, v in FOLDERS.items() if k in title), "?")


def main():
    SEL.mkdir(parents=True, exist_ok=True)
    files = {p.name.split("__", 1)[1]: p for p in DRIVE.iterdir() if "__" in p.name}
    records, tiles = [], []
    for key, (sid, room) in CANDIDATES.items():
        title, path = next((t, p) for t, p in files.items() if t.endswith(key))
        im = ImageOps.exif_transpose(Image.open(path)).convert("RGB")
        w, h = im.size
        rec = {"id": sid, "drive_id": path.name.split("__")[0], "title": title, "property": folder_of(title),
               "room": room, "width": w, "height": h, "orientation": "portrait" if h > w else "landscape",
               "selected": bool(sid)}
        if sid:
            m = im.copy()
            m.thumbnail((3000, 3000), Image.LANCZOS)
            m.save(SEL / f"{sid}.jpg", quality=93, subsampling=0)
            rec["master"] = f"source/selected/{sid}.jpg"
        records.append(rec)
        tiles.append((rec, im))
    (C / "source" / "photos.json").write_text(json.dumps(records, indent=2, ensure_ascii=False))

    # contact sheet: selected outlined in coral
    tw, th, cols = 380, 285, 5
    rows = (len(tiles) + cols - 1) // cols
    sheet = Image.new("RGB", (cols * tw + 20, rows * (th + 56) + 70), (245, 240, 232))
    d = ImageDraw.Draw(sheet)
    f = ImageFont.truetype(str(FONT), 15)
    d.text((14, 16), "Film 02 - candidate photos (real Tailor Homes Drive photography). Coral frame = selected.",
           font=ImageFont.truetype(str(FONT), 22), fill=(31, 42, 74))
    for i, (rec, im) in enumerate(tiles):
        x, y = 10 + (i % cols) * tw, 60 + (i // cols) * (th + 56)
        t = im.copy()
        t.thumbnail((tw - 16, th - 10))
        if rec["selected"]:
            d.rectangle((x + 2, y, x + tw - 6, y + th + 2), outline=(227, 140, 120), width=6)
        sheet.paste(t, (x + (tw - t.width) // 2 - 2, y + (th - t.height) // 2 + 1))
        label = f"{rec['id'] or '-'} | {rec['property']} | {rec['title'][-26:]}"
        d.text((x + 4, y + th + 8), label, font=f, fill=(31, 42, 74))
        d.text((x + 4, y + th + 28), f"{rec['room'][:44]}  {rec['width']}x{rec['height']}", font=f, fill=(90, 90, 90))
    out = C / "docs" / "tailorhomes-candidates-contact-sheet.png"
    sheet.save(out)
    print(out, sum(r["selected"] for r in records), "selected of", len(records))


if __name__ == "__main__":
    main()
