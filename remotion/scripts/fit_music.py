"""Fit a client-supplied, licensed music track to one composition.

    python scripts/fit_music.py <track in public/audio/library/> <seconds> <out name> [--start 12.5] [--vo <vo name> --lead 0.5]

- Cuts the track from --start (s) to the composition length and fades in (0.3 s) and out (1.6 s), so it ends with the picture.
- With --vo, ducks the music smoothly under the narration (sidechain on data/vo/<vo>.json's wav, delayed by --lead seconds).
- Writes public/audio/<out name> (48 kHz, 24-bit stereo, -16 LUFS). The templates' final mix is levelled to -14 LUFS at render.
Credits for every supplied track belong in data/music_credits.json.
"""
import argparse
import json
import subprocess
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
ap = argparse.ArgumentParser()
ap.add_argument("track")
ap.add_argument("seconds", type=float)
ap.add_argument("out")
ap.add_argument("--start", type=float, default=0.0)
ap.add_argument("--vo", default="")
ap.add_argument("--lead", type=float, default=0.5)
A = ap.parse_args()

src = ROOT / "public" / "audio" / "library" / A.track
d = A.seconds
out = ROOT / "public" / "audio" / A.out
music = f"[0:a]atrim=start={A.start}:duration={d},asetpts=PTS-STARTPTS,aformat=channel_layouts=stereo,afade=t=in:st=0:d=0.3,afade=t=out:st={max(0, d - 1.6):.3f}:d=1.6"
cmd = ["ffmpeg", "-v", "error", "-y", "-i", str(src)]
if A.vo:
    # smooth duck: compress the music with the (delayed) narration as the sidechain
    vo_wav = ROOT / "public" / json.loads((ROOT / "data" / "vo" / f"{A.vo}.json").read_text())["wav"]
    cmd += ["-i", str(vo_wav)]
    graph = (f"{music}[m];[1:a]adelay={int(A.lead * 1000)}:all=1,apad,aformat=channel_layouts=stereo[sc];"
             f"[m][sc]sidechaincompress=threshold=0.02:ratio=8:attack=40:release=450:makeup=1[d];"
             f"[d]loudnorm=I=-16:TP=-1.5:LRA=11,aresample=48000[out]")
else:
    graph = f"{music},loudnorm=I=-16:TP=-1.5:LRA=11,aresample=48000[out]"
subprocess.run(cmd + ["-filter_complex", graph, "-map", "[out]", "-t", f"{d}", "-c:a", "pcm_s24le", str(out)], check=True)
print(out)
