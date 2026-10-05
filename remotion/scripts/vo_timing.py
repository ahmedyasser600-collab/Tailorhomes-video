"""Measure a voice-over against its script and write phrase timings for Remotion.

    python scripts/vo_timing.py <name>

Reads data/vo/<name>.json ({"wav": "audio/vo_<name>.wav", "phrases": [...]}) and adds
"duration" and per-phrase "start"/"end" (seconds, in the WAV's own time). Each phrase boundary
is matched to a detected pause by ordered dynamic programming over expected positions
(by character count), the same method used for Films 02 and 03. A spec may list
"ignore_pauses" (pause start times) that fall inside a phrase, e.g. after a comma.
"""
import json
import re
import subprocess
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]


def run(cmd):
    return subprocess.run(cmd, capture_output=True, text=True)


def main(name):
    spec_path = ROOT / "data" / "vo" / f"{name}.json"
    spec = json.loads(spec_path.read_text())
    wav = ROOT / "public" / spec["wav"]
    phrases = spec["phrases"]
    dur = float(run(["ffprobe", "-v", "error", "-show_entries", "format=duration", "-of", "csv=p=0", str(wav)]).stdout)
    log = run(["ffmpeg", "-hide_banner", "-i", str(wav), "-af", "silencedetect=n=-42dB:d=0.16", "-f", "null", "-"]).stderr
    starts = [float(x) for x in re.findall(r"silence_start: ([0-9.]+)", log)]
    ends = [float(x) for x in re.findall(r"silence_end: ([0-9.]+)", log)]
    gaps = [(s, e) for s, e in zip(starts, ends) if s > 0.05 and e < dur - 0.05]
    # optional manual override: pauses (by start time, s) that sit inside a phrase, e.g. at a comma
    gaps = [g for g in gaps if not any(abs(g[0] - x) < 0.05 for x in spec.get("ignore_pauses", []))]
    head = ends[0] if starts and starts[0] <= 0.05 else 0.0
    tail = starts[-1] if starts and len(starts) > len(ends) else dur
    lens = [len(p) for p in phrases]
    total, acc, expect = sum(lens), 0, []
    for n in lens[:-1]:
        acc += n
        expect.append(head + (tail - head) * acc / total)
    nb, ng = len(expect), len(gaps)
    if ng < nb:
        raise SystemExit(f"{name}: only {ng} pauses for {nb} boundaries - check the read")
    INF = float("inf")
    cost = [[INF] * (ng + 1) for _ in range(nb + 1)]
    pick = [[None] * (ng + 1) for _ in range(nb + 1)]
    for j in range(ng + 1):
        cost[0][j] = 0.0
    for i in range(1, nb + 1):
        for j in range(i, ng + 1):
            use = cost[i - 1][j - 1] + abs(sum(gaps[j - 1]) / 2 - expect[i - 1]) - 0.15 * (gaps[j - 1][1] - gaps[j - 1][0])
            skip = cost[i][j - 1]
            cost[i][j], pick[i][j] = (use, True) if use <= skip else (skip, False)
    bounds, i, j = [], nb, ng
    while i > 0:
        if pick[i][j]:
            bounds.append(gaps[j - 1])
            i -= 1
        j -= 1
    bounds.reverse()
    out, t0 = [], head
    for k, text in enumerate(phrases):
        t1 = bounds[k][0] if k < nb else tail
        out.append({"text": text, "start": round(t0, 3), "end": round(t1, 3)})
        if k < nb:
            t0 = bounds[k][1]
    spec.update({"duration": round(dur, 3), "timings": out})
    spec_path.write_text(json.dumps(spec, indent=1, ensure_ascii=False))
    for p in out:
        print(f"{p['start']:6.2f}-{p['end']:6.2f}  {p['text']}")


if __name__ == "__main__":
    for n in sys.argv[1:]:
        main(n)
