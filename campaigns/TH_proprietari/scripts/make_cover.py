"""Cover / thumbnail. Usage: make_cover.py ["Line 1|Line 2" "Subline" out_name.png]
Default: out/tailorhomes-proprietari-cover.png (1080x1920).
Key content sits inside the central 3:4 area (y 240-1680) so it survives the profile-grid crop."""
import importlib.util
import sys
from pathlib import Path

from PIL import Image, ImageDraw

C = Path(__file__).resolve().parents[1]
HEAD = sys.argv[1].split("|") if len(sys.argv) > 1 else ["Hai una casa", "a Padova?"]
SUB = sys.argv[2] if len(sys.argv) > 2 else "La tua casa, su misura."
NAME = sys.argv[3] if len(sys.argv) > 3 else "tailorhomes-proprietari-cover.png"
sys.argv = [sys.argv[0], "--mode", "final"]
spec = importlib.util.spec_from_file_location("r3", C / "scripts" / "render.py")
R = importlib.util.module_from_spec(spec)
spec.loader.exec_module(R)

img = R.view("SE1", R.W, R.H, 1.04).convert("RGBA")
d = ImageDraw.Draw(img)
# logo on a cream chip
d.rounded_rectangle((300, 300, 780, 476), radius=10, fill=R.CREAM + (255,))
R.logo(img, 540, 388, 400, 1.0)
# navy card
lay = Image.new("RGBA", img.size, (0, 0, 0, 0))
ImageDraw.Draw(lay).rectangle((90, 1060, 990, 1600), fill=R.NAVY + (242,))
img.alpha_composite(lay)
R.tracked(img, (140, 1110), "PER PROPRIETARI", R.font(R.LABEL, 30), R.SALMON, 0.3)
ImageDraw.Draw(img).rectangle((140, 1160, 300, 1163), fill=R.SALMON)
size = 118
while max(R.tlen(l, R.font(R.SERIF, size)) for l in HEAD) > 820:
    size -= 2
R.reveal_lines(img, HEAD, R.font(R.SERIF, size), 134, 1190 + (118 - size), size, 99, 0, R.CREAM)
R.reveal_lines(img, [SUB], R.font(R.SERIF, 58), 136, 1450, 60, 99, 0, R.SALMON)
R.tracked(img, (140, 1540), "TAILORHOMES.IT", R.font(R.LABEL, 26), R.CREAM, 0.26)
out = C / "out" / NAME
img.convert("RGB").save(out)
print(out)
