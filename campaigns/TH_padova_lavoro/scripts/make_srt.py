"""Write Italian SRT subtitles from the caption cues in ../timeline.json."""
import json
from pathlib import Path

C = Path(__file__).resolve().parents[1]
TL = json.loads((C / "timeline.json").read_text())


def ts(s):
    ms = round(s * 1000)
    return f"{ms // 3600000:02d}:{ms // 60000 % 60:02d}:{ms // 1000 % 60:02d},{ms % 1000:03d}"


out = C / "subtitles" / "TH_Padova_Lavoro_it.srt"
out.parent.mkdir(exist_ok=True)
blocks = [f"{i}\n{ts(c['in'])} --> {ts(c['out'])}\n" + "\n".join(c["lines"]) + "\n"
          for i, c in enumerate(TL["captions"], 1)]
out.write_text("\n".join(blocks), encoding="utf-8")
print(out)
