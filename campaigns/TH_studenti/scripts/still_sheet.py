"""Assemble out/tailorhomes-students-contact-sheet.png from out/stills/<scene>.png (render.py --stills hero)."""
import json
from pathlib import Path
from PIL import Image, ImageDraw, ImageFont

C = Path(__file__).resolve().parents[1]
TM = json.loads((C / "timing.json").read_text())
names = ["open", "variety1", "variety2", "split", "benefits", "discount", "claim", "mosaic", "endcard"]
tw, th, cols = 432, 768, 5
rows = (len(names) + cols - 1) // cols
sheet = Image.new("RGB", (cols * (tw + 24) + 24, rows * (th + 90) + 110), (245, 240, 232))
d = ImageDraw.Draw(sheet)
f1 = ImageFont.truetype(str(C / "fonts" / "CormorantGaramond-MediumItalic.ttf"), 46)
f2 = ImageFont.truetype(str(C / "fonts" / "Jost-SemiBold.ttf"), 20)
d.text((24, 26), "Tailor Homes - Students & Erasmus - still states", font=f1, fill=(31, 42, 72))
for i, n in enumerate(names):
    x, y = 24 + (i % cols) * (tw + 24), 110 + (i // cols) * (th + 90)
    sheet.paste(Image.open(C / "out" / "stills" / f"{n}.png").resize((tw, th), Image.LANCZOS), (x, y))
    a, b = TM["scenes"][n]
    d.text((x, y + th + 14), f"{i + 1}. {n.upper()}   {a:.2f}-{b:.2f} s", font=f2, fill=(31, 42, 72))
out = C / "out" / "tailorhomes-students-contact-sheet.png"
sheet.save(out)
print(out)
