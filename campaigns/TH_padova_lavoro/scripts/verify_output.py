"""Probe an exported MP4 and write a JSON report + a contact sheet (1 frame/second).

    python verify_output.py ../final/TH_Padova_Lavoro_1080x1920.mp4
"""
import json
import subprocess
import sys
from pathlib import Path

from PIL import Image, ImageDraw

mp4 = Path(sys.argv[1]).resolve()
C = Path(__file__).resolve().parents[1]
out_dir = C / "docs" / "verification"
out_dir.mkdir(parents=True, exist_ok=True)


def run(cmd):
    return subprocess.run(cmd, capture_output=True, text=True)


probe = json.loads(run(["ffprobe", "-v", "error", "-show_streams", "-show_format", "-of", "json", str(mp4)]).stdout)
v = next(s for s in probe["streams"] if s["codec_type"] == "video")
a = next((s for s in probe["streams"] if s["codec_type"] == "audio"), None)
frames = int(run(["ffprobe", "-v", "error", "-count_frames", "-select_streams", "v:0", "-show_entries",
                  "stream=nb_read_frames", "-of", "csv=p=0", str(mp4)]).stdout.strip())
report = {
    "file": mp4.name,
    "size_mb": round(int(probe["format"]["size"]) / 1e6, 2),
    "container_duration_s": float(probe["format"]["duration"]),
    "video": {"codec": v["codec_name"], "profile": v.get("profile"), "width": v["width"], "height": v["height"],
              "fps": v["r_frame_rate"], "pix_fmt": v["pix_fmt"], "decoded_frames": frames,
              "duration_s": float(v["duration"])},
}
if a:
    r = run(["ffmpeg", "-hide_banner", "-nostats", "-i", str(mp4), "-map", "0:a", "-af", "ebur128=peak=true", "-f", "null", "-"]).stderr
    tail = r[r.rfind("Summary:"):]
    report["audio"] = {"codec": a["codec_name"], "sample_rate": a["sample_rate"], "channels": a["channels"],
                       "duration_s": float(a["duration"]),
                       "integrated_lufs": float(tail.split("I:")[1].split()[0]),
                       "true_peak_dbfs": float(tail.split("Peak:")[1].split()[0])}
    report["av_duration_diff_s"] = round(report["audio"]["duration_s"] - report["video"]["duration_s"], 3)

# contact sheet: one decoded frame per second
tmp = out_dir / "_tmp"
tmp.mkdir(exist_ok=True)
run(["ffmpeg", "-v", "error", "-y", "-i", str(mp4), "-vf", "fps=1,scale=216:384", str(tmp / "s_%02d.png")])
shots = sorted(tmp.glob("s_*.png"))
cols = 10
rows = (len(shots) + cols - 1) // cols
sheet = Image.new("RGB", (cols * 216, rows * 404), "white")
d = ImageDraw.Draw(sheet)
for i, f in enumerate(shots):
    x, y = i % cols * 216, i // cols * 404
    sheet.paste(Image.open(f), (x, y + 20))
    d.text((x + 4, y + 4), f"~{i}s", fill="black")
    f.unlink()
tmp.rmdir()
sheet_path = out_dir / f"{mp4.stem}_contact_sheet.png"
sheet.save(sheet_path)
report["contact_sheet"] = str(sheet_path.relative_to(C))
(out_dir / f"{mp4.stem}_probe.json").write_text(json.dumps(report, indent=2))
print(json.dumps(report, indent=2))
