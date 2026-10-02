"""Poster / cover frame: out/tailorhomes-students-poster.png (1080x1920), built with render.py's helpers."""
import importlib.util
import sys
from pathlib import Path

from PIL import Image, ImageDraw

C = Path(__file__).resolve().parents[1]
sys.argv = [sys.argv[0], "--mode", "final"]
spec = importlib.util.spec_from_file_location("render", C / "scripts" / "render.py")
R = importlib.util.module_from_spec(spec)
spec.loader.exec_module(R)

img = Image.new("RGBA", (R.W, R.H), R.NAVY + (255,))
# photo stack: two real apartments, split by a hairline
img.paste(R.view("SE1", R.W, 760, 1.06), (0, 0))
img.paste(R.view("VN1", R.W, 520, 1.06), (0, 766))
ImageDraw.Draw(img).rectangle((0, 760, R.W, 766), fill=R.CREAM)
# navy offer band
R.tracked(img, (92, 1330), "UNIPD & ERASMUS STUDENTS", R.font(R.LABEL, 32), R.SALMON, 0.28)
ImageDraw.Draw(img).rectangle((92, 1378, 262, 1381), fill=R.SALMON)
R.reveal_lines(img, ["Your stay,", "tailored to you."], R.font(R.SERIF, 104), 86, 1400, 108, 99, 0, R.CREAM)
R.pill(img, 800, 1490, 240, 128, R.CORAL, "−15%", R.font(R.SANS_B, 72), R.CREAM)
R.tracked(img, (92, 1665), "TAILORHOMES.IT/STUDENTI", R.font(R.LABEL, 30), R.CREAM, 0.22)
out = C / "out" / "tailorhomes-students-poster.png"
img.convert("RGB").save(out)
print(out)
