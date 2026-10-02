"""Cover / thumbnail (1080x1920). Key content sits inside the central 3:4 area (y 240-1680) so it
survives the profile-grid crop.

    python scripts/make_cover.py [style] [out_name.png]
    style: passepartout | hairline | card   (default: passepartout)
"""
import importlib.util
import sys
from pathlib import Path

from PIL import Image, ImageDraw, ImageFilter

C = Path(__file__).resolve().parents[1]
STYLE = sys.argv[1] if len(sys.argv) > 1 else "passepartout"
NAME = sys.argv[2] if len(sys.argv) > 2 else "tailorhomes-proprietari-cover.png"
sys.argv = [sys.argv[0], "--mode", "final"]
spec = importlib.util.spec_from_file_location("r3", C / "scripts" / "render.py")
R = importlib.util.module_from_spec(spec)
spec.loader.exec_module(R)

HEAD = ["Più valore", "alla tua casa."]
SUB = "Gestione, staging e marketing a Padova."
PHOTO = "SE6"
W, H = R.W, R.H


def lay():
    return Image.new("RGBA", (W, H), (0, 0, 0, 0))


def passepartout():
    """Printed-photo look: the photo sits in a cream mount; type set on the mount below it."""
    img = Image.new("RGBA", (W, H), R.CREAM + (255,))
    x0, y0, x1, y1 = 56, 56, 1024, 1190
    img.paste(R.view(PHOTO, x1 - x0, y1 - y0, 1.0), (x0, y0))
    d = ImageDraw.Draw(img)
    d.rectangle((x0 - 14, y0 - 14, x1 + 14, y1 + 14), outline=R.SALMON, width=2)      # fine inner rule
    R.tracked(img, (96, 1262), "PER PROPRIETARI", R.font(R.LABEL, 30), R.CORAL, 0.3)
    d.rectangle((96, 1312, 256, 1315), fill=R.CORAL)
    R.reveal_lines(img, HEAD, R.font(R.SERIF, 128), 88, 1340, 124, 99, 0, R.NAVY)
    R.reveal_lines(img, [SUB], R.font(R.SERIF, 56), 92, 1604, 60, 99, 0, R.CORAL)
    d.rectangle((96, 1700, 984, 1701), fill=R.NAVY)
    R.tracked(img, (96, 1732), "TAILORHOMES.IT", R.font(R.LABEL, 28), R.NAVY, 0.28)
    R.logo(img, 846, 1752, 300, 1.0)
    return img


def hairline():
    """Full-bleed photo inside a fine cream double frame; type directly on a soft navy base."""
    img = R.view(PHOTO, W, H, 1.04).convert("RGBA")
    g = lay()
    ImageDraw.Draw(g).rectangle((0, 820, W, H), fill=R.NAVY + (255,))
    mask = Image.new("L", (W, H), 0)
    for y in range(820, H):                                     # navy rises from the bottom edge
        a = min(1.0, (y - 820) / 480) ** 1.2 * 0.95
        ImageDraw.Draw(mask).line((0, y, W, y), fill=round(255 * a))
    g.putalpha(mask)
    img.alpha_composite(g)
    d = ImageDraw.Draw(img)
    d.rectangle((40, 40, W - 41, H - 41), outline=R.CREAM, width=3)
    d.rectangle((54, 54, W - 55, H - 55), outline=R.CREAM + (150,), width=1)
    d.rounded_rectangle((350, 300, 730, 440), radius=8, fill=R.CREAM + (255,))
    R.logo(img, 540, 370, 320, 1.0)
    R.tracked(img, (540, 1238), "PER PROPRIETARI", R.font(R.LABEL, 30), R.SALMON, 0.3, align="center")
    R.reveal_lines(img, HEAD, R.font(R.SERIF, 132), 540, 1290, 128, 99, 0, R.CREAM, align="center")
    R.reveal_lines(img, [SUB], R.font(R.SERIF, 54), 540, 1560, 58, 99, 0, R.SALMON, align="center")
    R.tracked(img, (540, 1652), "TAILORHOMES.IT", R.font(R.LABEL, 27), R.CREAM, 0.28, align="center")
    return img


def card():
    """Light editorial card: cream, rounded, coral spine, soft shadow; logo inside the card."""
    img = R.view(PHOTO, W, H, 1.04).convert("RGBA")
    box = (100, 1010, 980, 1640)
    sh = lay()
    ImageDraw.Draw(sh).rounded_rectangle((box[0], box[1] + 18, box[2], box[3] + 18), radius=22, fill=(15, 20, 35, 110))
    img.alpha_composite(sh.filter(ImageFilter.GaussianBlur(26)))
    c = lay()
    ImageDraw.Draw(c).rounded_rectangle(box, radius=22, fill=R.CREAM + (250,))
    img.alpha_composite(c)
    d = ImageDraw.Draw(img)
    d.rectangle((box[0] + 44, box[1] + 60, box[0] + 50, box[3] - 60), fill=R.CORAL)
    R.logo(img, 760, 1080, 300, 1.0)
    R.tracked(img, (186, 1068), "PER PROPRIETARI", R.font(R.LABEL, 28), R.CORAL, 0.3)
    R.reveal_lines(img, HEAD, R.font(R.SERIF, 120), 178, 1150, 116, 99, 0, R.NAVY)
    R.reveal_lines(img, [SUB], R.font(R.SERIF, 46), 182, 1400, 52, 99, 0, R.CORAL)
    d.rectangle((186, 1500, 920, 1501), fill=R.SALMON)
    R.tracked(img, (186, 1540), "TAILORHOMES.IT", R.font(R.LABEL, 27), R.NAVY, 0.28)
    return img


img = {"passepartout": passepartout, "hairline": hairline, "card": card}[STYLE]()
out = C / "out" / NAME
img.convert("RGB").save(out)
print(out)
