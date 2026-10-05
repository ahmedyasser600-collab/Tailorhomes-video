"""Original, code-synthesised music beds for the Remotion templates (no samples, no third-party audio).

    python scripts/make_music.py <style> <seconds> <out.wav> [--ticks 1.2,3.4] [--duck 0.5-2.1,3.0-4.2]

styles:
  gallery   96 BPM, A major, felt piano arpeggios + soft pulse + shaker (music-only galleries)
  checklist 100 BPM, F major, lighter piano + shaker; soft UI ticks at --ticks (seconds)
  warm      72 BPM, G major, felt piano + legato strings + cello (narrated films; --duck dips under speech)
Every bed resolves on a held chord ~3 s before the end and fades out exactly at <seconds>.
Writes 48 kHz 24-bit stereo WAV, normalised to -16 LUFS (narrated mixes are levelled later).
Low-level helpers (filters, reverb, ADSR, loudness) come from Film 01's make_audio.py.
"""
import argparse
import importlib.util
import subprocess
import sys
from pathlib import Path

import numpy as np

REPO = Path(__file__).resolve().parents[2]
spec = importlib.util.spec_from_file_location("film01_audio", REPO / "campaigns" / "TH_padova_lavoro" / "scripts" / "make_audio.py")
B = importlib.util.module_from_spec(spec)
_argv, sys.argv = sys.argv, sys.argv[:1]
spec.loader.exec_module(B)
sys.argv = _argv
SR = B.SR

ap = argparse.ArgumentParser()
ap.add_argument("style", choices=["gallery", "checklist", "warm"])
ap.add_argument("seconds", type=float)
ap.add_argument("out")
ap.add_argument("--ticks", default="")
ap.add_argument("--duck", default="")
ap.add_argument("--seed", type=int, default=7)
A = ap.parse_args()
RNG = np.random.default_rng(A.seed)
DUR = A.seconds
N = int(DUR * SR)


def place(buf, sig, at):
    i = int(at * SR)
    if i < 0 or i >= buf.shape[-1]:
        return
    j = min(buf.shape[-1], i + sig.shape[-1])
    buf[..., i:j] += sig[..., : j - i]


def piano(freq, vel, length=3.0):
    n = int(length * SR)
    t = np.arange(n) / SR
    s = np.zeros(n)
    for k, amp in enumerate((1.0, 0.42, 0.22, 0.12, 0.07, 0.04), start=1):
        fk = freq * k * np.sqrt(1 + 0.00035 * k * k)
        s += amp * np.sin(2 * np.pi * fk * t + RNG.uniform(0, 6.28)) * np.exp(-t * (0.9 + 0.55 * k))
    s = B.lp(s + B.lp(RNG.standard_normal(n), 1200) * np.exp(-t * 90) * 0.06, 2600)
    return s * np.minimum(1, t / 0.006) * vel


def strings(freqs, dur, attack=1.0):
    n = int(dur * SR)
    t = np.arange(n) / SR
    s = np.zeros(n)
    for f in freqs:
        for det in (-0.08, 0.0, 0.09):
            vib = 1 + 0.0025 * np.sin(2 * np.pi * (5.1 + det) * t + RNG.uniform(0, 6.28))
            ph = 2 * np.pi * np.cumsum(f * 2 ** (det / 12) * vib) / SR
            s += sum(np.sin(k * ph) / k ** 1.3 for k in range(1, 7))
    s = B.lp(s, 1800)
    return s * np.minimum(1, t / attack) * np.clip((dur - t) / 1.0, 0, 1) / (3 * len(freqs))


def tick():
    n = int(0.12 * SR)
    t = np.arange(n) / SR
    return np.sin(2 * np.pi * 1900 * t) * np.exp(-t * 70) * 0.6 + B.bp(RNG.standard_normal(n), 2500, 7000) * np.exp(-t * 150) * 0.25


STYLES = {
    "gallery": dict(bpm=96, chords=[[57, 61, 64, 68, 71], [54, 57, 61, 64, 68], [50, 57, 61, 64, 66], [52, 56, 59, 61, 66]],
                    roots=[45, 42, 38, 40], final=[33, 45, 52, 57, 61, 64, 68, 71], pulse=True, strings=False),
    "checklist": dict(bpm=100, chords=[[53, 57, 60, 64, 67], [50, 53, 57, 60, 64], [46, 50, 53, 57, 62], [48, 52, 55, 60, 65]],
                      roots=[41, 38, 46, 36], final=[29, 41, 48, 53, 57, 60, 64, 67], pulse=True, strings=False),
    "warm": dict(bpm=72, chords=[[55, 59, 62, 66, 69], [52, 55, 59, 62, 66], [48, 52, 55, 59, 64], [50, 54, 57, 62, 64]],
                 roots=[43, 40, 36, 38], final=[31, 43, 50, 55, 59, 62, 66, 69], pulse=False, strings=True),
}


def music():
    st = STYLES[A.style]
    beat = 60 / st["bpm"]
    bar = 4 * beat
    end_t = max(bar, DUR - 3.0)
    L = np.zeros((2, N + 5 * SR))
    for b in range(int(np.ceil(end_t / bar)) + 1):
        t0 = b * bar
        if t0 >= end_t:
            break
        ch, root = st["chords"][b % 4], st["roots"][b % 4]
        for beat_i, note, v in ((0, root + 12, 0.30), (2, root + 19, 0.2)):
            s = piano(B.midi(note), v)
            place(L[0], s * 1.05, t0 + beat_i * beat)
            place(L[1], s * 0.85, t0 + beat_i * beat)
        for e, i in enumerate([0, 2, 1, 3, 2, 4, 3, 1]):
            tt = t0 + e * beat / 2 + RNG.uniform(-0.01, 0.01)
            if tt >= end_t:
                break
            s = piano(B.midi(ch[i] + (12 if A.style != "warm" else 0)), 0.11 if e % 2 else 0.15, 2.2)
            pan = 0.4 + 0.2 * i / 4
            place(L[0], s * (1.2 - pan), tt)
            place(L[1], s * (0.8 + pan), tt)
        if st["pulse"] and b >= 1:
            for k in (0, 2):
                kk = B.kick() * 0.12
                place(L[0], kk, t0 + k * beat)
                place(L[1], kk, t0 + k * beat)
            for e in range(8):
                sh = B.shaker() * (0.035 if e % 2 else 0.022)
                place(L[0], sh * 0.8, t0 + e * beat / 2 + 0.01)
                place(L[1], sh, t0 + e * beat / 2)
        if st["strings"]:
            s = strings([B.midi(n) for n in ch[1:4]], bar + 0.8) * 0.15
            place(L[0], s, t0)
            place(L[1], np.roll(s, 240), t0)
            vc = strings([B.midi(root)], bar + 0.6, attack=0.5) * 0.2
            place(L[0], vc, t0)
            place(L[1], vc, t0)
    for i, n in enumerate(st["final"]):
        s = piano(B.midi(n), 0.25 if n < 45 else 0.17, 5.0)
        place(L[0], s * (1.1 - i * 0.04), end_t + i * 0.06)
        place(L[1], s * (0.8 + i * 0.04), end_t + i * 0.06)
    if st["strings"]:
        s = strings([B.midi(n) for n in st["final"][3:7]], DUR - end_t + 1, attack=0.6) * 0.16
        place(L[0], s, end_t)
        place(L[1], np.roll(s, 240), end_t)
    for tt in [float(x) for x in A.ticks.split(",") if x]:
        tk = tick() * 0.35
        place(L[0], tk, tt)
        place(L[1], tk, tt)
    ir = B.reverb_ir(2.2)
    wet = np.stack([B.fftconvolve(L[c], ir[c])[: L.shape[1]] for c in range(2)])
    mix = np.stack([B.hp(c, 32) for c in (L * 0.8 + wet * 0.38)[:, :N]])
    t = np.arange(N) / SR
    mix *= np.clip(t / 0.4, 0, 1) * np.clip((DUR - t) / 1.6, 0, 1) ** 1.3
    if A.duck:
        g = np.zeros(N)
        for seg in A.duck.split(","):
            a, b = (float(x) for x in seg.split("-"))
            g = np.maximum(g, np.minimum(np.clip((t - (a - 0.35)) / 0.25, 0, 1), np.clip(((b + 0.45) - t) / 0.25, 0, 1)))
        mix *= B.db(-8.0 * g)
    return mix / (np.max(np.abs(mix)) + 1e-9) * B.db(-3)


out = Path(A.out)
out.parent.mkdir(parents=True, exist_ok=True)
tmp = out.with_suffix(".raw.wav")
B.write(tmp, music().T)
subprocess.run(["ffmpeg", "-v", "error", "-y", "-i", str(tmp), "-af", f"loudnorm=I=-16:TP=-1.5:LRA=9,aresample={SR}",
                "-c:a", "pcm_s24le", str(out)], check=True)
tmp.unlink()
print(out, B.loudness(out))
