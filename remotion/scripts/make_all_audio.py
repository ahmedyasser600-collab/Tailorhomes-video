"""Build every music bed at the exact length of its composition (mirrors the duration formulas in src/templates).

    python scripts/make_all_audio.py
"""
import json
import math
import subprocess
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
PY = sys.executable
FPS = 30


def vo(name):
    return json.loads((ROOT / "data" / "vo" / f"{name}.json").read_text())["timings"]


def make(style, frames, out, **kw):
    cmd = [PY, str(ROOT / "scripts" / "make_music.py"), style, f"{frames / FPS:.3f}", str(ROOT / "public" / "audio" / out)]
    for k, v in kw.items():
        cmd += [f"--{k}", str(v)]
    subprocess.run(cmd, check=True)


def duck(t, lead):
    return ",".join(f"{p['start'] + lead:.2f}-{p['end'] + lead:.2f}" for p in t)


# B: owner services (LEAD 0.5 s, HOLD 2.8 s)
for i, item in enumerate(json.loads((ROOT / "data" / "services.json").read_text())["items"]):
    t = vo(item["vo"])
    make("warm", math.ceil((0.5 + t[-1]["end"] + 2.8) * FPS), f"music_{item['vo']}.wav", duck=duck(t, 0.5), seed=20 + i)
# C: checklist (INTRO 45, PER 45, +20, OUTRO 84; ticks 12 frames after each item starts)
n = len(json.loads((ROOT / "data" / "checklist.json").read_text())["photos"])
ticks = ",".join(f"{(45 + i * 45 + 12) / FPS:.3f}" for i in range(n))
make("checklist", 45 + n * 45 + 20 + 84, "music_checklist.wav", ticks=ticks, seed=31)
# D: outcome (LEAD 0.6 s, HOLD 2.6 s)
for lang, name in json.loads((ROOT / "data" / "outcome.json").read_text())["vo"].items():
    t = vo(name)
    make("warm", math.ceil((0.6 + t[-1]["end"] + 2.6) * FPS), f"music_{name}.wav", duck=duck(t, 0.6), seed=40)
