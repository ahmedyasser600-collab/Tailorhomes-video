"""Render scenes of TH_padova_lavoro_shots.blend to PNG sequences.

    python render_shots.py -- --tier preview                 # all scenes, 540x960
    python render_shots.py -- --tier final --scenes S3_interni
    python render_shots.py -- --tier preview --frames 1,40,90 --scenes S1_arrivo --out stills

Tiers: preview = 50 % (540x960), 16 samples; final = 100 % (1080x1920), 48 samples.
Both use adaptive sampling + OpenImageDenoise. Existing frames are skipped
unless --overwrite, so an interrupted render can simply be restarted.
Output: ../renders/<tier>/<scene>/f_0001.png (scene-local frame numbers).
"""
import argparse
import sys
import time
from pathlib import Path

import bpy

HERE = Path(__file__).resolve().parent
TIERS = {"preview": (50, 16), "final": (100, 48)}

argv = sys.argv[sys.argv.index("--") + 1:] if "--" in sys.argv else sys.argv[1:]
ap = argparse.ArgumentParser()
ap.add_argument("--tier", default="preview", choices=TIERS)
ap.add_argument("--scenes", default="")
ap.add_argument("--frames", default="")
ap.add_argument("--samples", type=int, default=0)
ap.add_argument("--out", default="")
ap.add_argument("--overwrite", action="store_true")
ap.add_argument("--threads", type=int, default=0)
A = ap.parse_args(argv)

bpy.ops.wm.open_mainfile(filepath=str(HERE / "TH_padova_lavoro_shots.blend"))
pct, samples = TIERS[A.tier]
names = [s for s in A.scenes.split(",") if s] or sorted(s.name for s in bpy.data.scenes)
for name in names:
    sc = bpy.data.scenes[name]
    bpy.context.window.scene = sc if bpy.context.window else sc
    sc.render.resolution_percentage = pct
    sc.cycles.samples = A.samples or samples
    if A.threads:
        sc.render.threads_mode, sc.render.threads = "FIXED", A.threads
    out = HERE.parent / "renders" / (A.out or A.tier) / name
    out.mkdir(parents=True, exist_ok=True)
    frames = [int(f) for f in A.frames.split(",") if f] or range(sc.frame_start, sc.frame_end + 1)
    for f in frames:
        path = out / f"f_{f:04d}.png"
        if path.exists() and not A.overwrite:
            continue
        t = time.time()
        sc.frame_set(f)
        sc.render.filepath = str(path)
        bpy.ops.render.render(write_still=True, scene=sc.name)
        print(f"FRAME {name} {f}/{sc.frame_end} {time.time() - t:.1f}s", flush=True)
print("RENDER_DONE", flush=True)
