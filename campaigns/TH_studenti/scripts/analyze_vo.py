"""Measure the voice-over and write ../timing.json (the film's master timeline).

Uses audio/voice/tailorhomes-students-vo.wav if present (the supplied master),
otherwise the labelled scratch read in audio/voice/scratch/.

1. Measures duration and silences (ffmpeg silencedetect).
2. Aligns the 10 script phrases to the detected speech segments: each phrase
   boundary snaps to the pause closest to its expected position (from character
   count), so it also works when the speaker pauses differently.
3. Estimates emphasis points (e.g. "fifteen percent") by character position.
4. Derives scene start/end times from phrase times (VO lead-in + CTA hold).

    python scripts/analyze_vo.py
"""
import json
import re
import subprocess
from pathlib import Path

C = Path(__file__).resolve().parents[1]
MASTER = C / "audio" / "voice" / "tailorhomes-students-vo.wav"
SCRATCH = C / "audio" / "voice" / "scratch" / "SCRATCH_students_vo.wav"
LEAD_IN = 0.5          # film seconds before the first word
CTA_HOLD = 3.2         # end card stays this long after the last word

PHRASES = [
    "Coming to Padova for UniPD or Erasmus?",
    "Find a space that feels like yours from day one.",
    "Tailor Homes offers furnished apartments across Padova,",
    "ready for your stay.",
    "And if you're a UniPD or Erasmus student,",
    "you get fifteen percent off your booking.",
    "Just verify your student card on WhatsApp.",
    "Your stay,",
    "tailored to you.",
    "Tailor Homes.",
]
EMPHASIS = {"fifteen percent": 5, "Your stay,": 7, "tailored to you.": 8}


def run(cmd):
    return subprocess.run(cmd, capture_output=True, text=True)


def main():
    wav = MASTER if MASTER.exists() else SCRATCH
    dur = float(run(["ffprobe", "-v", "error", "-show_entries", "format=duration", "-of", "csv=p=0", str(wav)]).stdout)
    log = run(["ffmpeg", "-hide_banner", "-i", str(wav), "-af", "silencedetect=n=-42dB:d=0.16", "-f", "null", "-"]).stderr
    starts = [float(x) for x in re.findall(r"silence_start: ([0-9.]+)", log)]
    ends = [float(x) for x in re.findall(r"silence_end: ([0-9.]+)", log)]
    gaps = [(s, e) for s, e in zip(starts, ends) if s > 0.05 and e < dur - 0.05]
    head = ends[0] if starts and starts[0] <= 0.05 else 0.0
    tail = starts[-1] if starts and len(starts) > len(ends) else dur
    speech = tail - head

    # expected boundary positions by character count; choose an ordered subset of the
    # detected pauses (one per boundary) that best matches them (dynamic programming)
    lens = [len(p) for p in PHRASES]
    total = sum(lens)
    expect, acc = [], 0
    for n in lens[:-1]:
        acc += n
        expect.append(head + speech * acc / total)
    nb, ng = len(expect), len(gaps)
    if ng >= nb:
        INF = float("inf")
        cost = [[INF] * (ng + 1) for _ in range(nb + 1)]
        pick = [[None] * (ng + 1) for _ in range(nb + 1)]
        for j in range(ng + 1):
            cost[0][j] = 0.0
        for i in range(1, nb + 1):
            for j in range(i, ng + 1):
                mid = sum(gaps[j - 1]) / 2
                use = cost[i - 1][j - 1] + abs(mid - expect[i - 1]) - 0.15 * (gaps[j - 1][1] - gaps[j - 1][0])
                skip = cost[i][j - 1]
                cost[i][j], pick[i][j] = (use, True) if use <= skip else (skip, False)
        bounds, i, j = [], nb, ng
        while i > 0:
            if pick[i][j]:
                bounds.append(gaps[j - 1]); i -= 1
            j -= 1
        bounds.reverse()
    else:   # fewer pauses than boundaries: use estimates where no pause is close
        bounds = []
        for e in expect:
            near = min(gaps, key=lambda g: abs(sum(g) / 2 - e)) if gaps else None
            bounds.append(near if near and abs(sum(near) / 2 - e) < 0.8 and near not in bounds else (e - 0.05, e + 0.05))
        bounds.sort()
    phrases = []
    t0 = head
    for k, text in enumerate(PHRASES):
        t1 = bounds[k][0] if k < len(bounds) else tail
        phrases.append({"i": k, "text": text, "vo_start": round(t0, 3), "vo_end": round(t1, 3),
                        "start": round(t0 + LEAD_IN, 3), "end": round(t1 + LEAD_IN, 3)})
        if k < len(bounds):
            t0 = bounds[k][1]
    emph = {}
    for word, k in EMPHASIS.items():
        p = phrases[k]
        frac = p["text"].find(word) / max(1, len(p["text"]))
        emph[word] = round(p["start"] + (p["end"] - p["start"]) * frac, 3)

    P = lambda k: phrases[k]["start"]
    film_end = round(phrases[-1]["end"] + CTA_HOLD, 2)
    scenes = {
        "open":     [0.0, P(1) - 0.15],
        "variety1": [P(1) - 0.15, P(1) + (phrases[1]["end"] - P(1)) * 0.62],   # S. Eufemia living
        "variety2": [P(1) + (phrases[1]["end"] - P(1)) * 0.62, P(2) - 0.05],     # wall-edge wipe -> Via Nullo
        "split":    [P(2) - 0.05, P(3) - 0.35],                                  # two apartments, split
        "benefits": [P(3) - 0.35, P(4) - 0.1],                                   # editorial tiles
        "discount": [P(4) - 0.1, P(6) - 0.2],
        "claim":    [P(6) - 0.2, P(7) - 0.45],
        "mosaic":   [P(7) - 0.45, P(8) - 0.05],
        "endcard":  [P(8) - 0.05, film_end],
    }
    scenes = {k: [round(a, 3), round(b, 3)] for k, (a, b) in scenes.items()}
    out = {"source": str(wav.relative_to(C)), "is_master": wav == MASTER, "vo_duration": round(dur, 3),
           "lead_in": LEAD_IN, "film_duration": film_end, "phrases": phrases, "emphasis": emph,
           "scenes": scenes, "pauses": [[round(s, 3), round(e, 3)] for s, e in gaps]}
    (C / "timing.json").write_text(json.dumps(out, indent=2))
    print(json.dumps({k: out[k] for k in ("source", "is_master", "vo_duration", "film_duration", "emphasis")}, indent=1))
    for p in phrases:
        print(f"{p['start']:6.2f}-{p['end']:6.2f}  {p['text']}")
    for k, v in scenes.items():
        print(f"{k:9s} {v[0]:6.2f} -> {v[1]:6.2f}  ({v[1]-v[0]:.2f}s)")


if __name__ == "__main__":
    main()
