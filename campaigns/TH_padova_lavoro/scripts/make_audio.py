"""Audio for the Tailor Homes Padova reel: narration edit, original music bed,
sound effects, automatic ducking and the final mix.

    python campaigns/TH_padova_lavoro/scripts/make_audio.py

Reads ../timeline.json. Writes 48 kHz / 24-bit WAV stems to ../audio/:
    stems/vo_edit.wav          narration placed on the film timeline (mono)
    stems/music_bed_full.wav   original music, not ducked (stereo)
    stems/music_bed_ducked.wav music with the narration ducking applied
    stems/sfx.wav              sound effects
    mix/TH_padova_mix_prelimiter.wav   sum before loudness normalisation
    mix/TH_padova_mix.wav      final mix, -14 LUFS integrated, -1.5 dBTP

The music is composed procedurally below (additive/FM synthesis, no samples),
so it carries no third-party rights. See ../docs/MUSIC_LICENSE.md.
Requires numpy, scipy and ffmpeg.
"""
import json
import subprocess
from pathlib import Path

import numpy as np
from scipy.io import wavfile
from scipy.signal import butter, fftconvolve, sosfilt

C = Path(__file__).resolve().parents[1]
TL = json.loads((C / "timeline.json").read_text())
SR = 48000
DUR = TL["duration"]
N = int(SR * DUR)
RNG = np.random.default_rng(7)


def db(x):
    return 10 ** (x / 20)


def t_axis(n):
    return np.arange(n) / SR


def env_adsr(n, a, d, s, r, total=None):
    a, d, r = int(a * SR), int(d * SR), int(r * SR)
    sus = max(n - a - d - r, 0)
    e = np.concatenate([np.linspace(0, 1, a, False), np.linspace(1, s, d, False), np.full(sus, s), np.linspace(s, 0, r)])
    return np.pad(e, (0, max(0, n - len(e))))[:n]


def lp(x, f, order=2):
    return sosfilt(butter(order, f, "low", fs=SR, output="sos"), x)


def hp(x, f, order=2):
    return sosfilt(butter(order, f, "high", fs=SR, output="sos"), x)


def bp(x, lo, hi, order=2):
    return sosfilt(butter(order, [lo, hi], "band", fs=SR, output="sos"), x)


def place(buf, sig, at):
    i = int(at * SR)
    if i >= len(buf):
        return
    j = min(len(buf), i + len(sig))
    buf[i:j] += sig[: j - i]


def midi(n):
    return 440.0 * 2 ** ((n - 69) / 12)


def write(path, data):
    path.parent.mkdir(parents=True, exist_ok=True)
    x = np.clip(data, -1, 1)
    wavfile.write(path, SR, (x * (2 ** 31 - 1)).astype(np.int32))


# ------------------------------------------------------------------ narration
def narration():
    src = C / TL["voiceover"]["file"]
    raw = subprocess.run(["ffmpeg", "-v", "error", "-i", str(src), "-ac", "1", "-ar", str(SR), "-f", "f32le", "-"],
                         capture_output=True, check=True).stdout
    vo = np.frombuffer(raw, np.float32).astype(np.float64)
    vo = hp(vo, 70)
    out = np.zeros(N)
    fade = int(0.02 * SR)
    for seg in TL["voiceover"]["segments"]:
        a, b = (int(s * SR) for s in seg["src"])
        piece = vo[a:min(b, len(vo))].copy()
        piece[:fade] *= np.linspace(0, 1, fade)
        piece[-fade:] *= np.linspace(1, 0, fade)
        place(out, piece, seg["film_start"])
    return out


# ------------------------------------------------------------------ music (original)
BPM = 100
BEAT = 60 / BPM
BAR = 4 * BEAT
# Fmaj9 | Am7 | Dm9 | Bbmaj9  (F major, warm and unresolved until the end card)
CHORDS = [[53, 57, 60, 64, 67], [57, 60, 64, 67, 72], [50, 57, 60, 64, 65], [46, 53, 57, 60, 62]]
ROOTS = [41, 45, 38, 46]


def epiano(freq, dur, vel=0.5):
    n = int((dur + 1.6) * SR)
    t = t_axis(n)
    idx = 1.4 * np.exp(-t * 5.0) + 0.25
    mod = np.sin(2 * np.pi * freq * t) * idx
    car = np.sin(2 * np.pi * freq * t + mod)
    bell = 0.12 * np.sin(2 * np.pi * freq * 4.0 * t) * np.exp(-t * 9)
    e = np.exp(-t * 1.35) * np.minimum(1, t / 0.004)
    rel = np.clip((dur + 1.6 - t) / 1.6, 0, 1) if dur < 1.6 else 1
    return (car + bell) * e * rel * vel


def pad(freqs, dur):
    n = int(dur * SR)
    t = t_axis(n)
    s = np.zeros(n)
    for f in freqs:
        for det in (-0.12, 0.0, 0.13):
            ph = RNG.uniform(0, 2 * np.pi)
            ff = f * 2 ** (det / 12)
            # band-limited saw approximation (first 8 harmonics)
            s += sum(np.sin(2 * np.pi * ff * k * t + ph * k) / k for k in range(1, 9)) * 0.33
    s = lp(s, 1400, 2)
    return s * env_adsr(n, 0.9, 0.5, 0.8, 1.2) / len(freqs)


def bass(freq, dur):
    n = int((dur + 0.3) * SR)
    t = t_axis(n)
    s = np.sin(2 * np.pi * freq * t) + 0.25 * np.sin(2 * np.pi * 2 * freq * t)
    return s * env_adsr(n, 0.02, 0.3, 0.7, 0.3)


def kick():
    n = int(0.45 * SR)
    t = t_axis(n)
    f = 48 + 70 * np.exp(-t * 28)
    return np.sin(2 * np.pi * np.cumsum(f) / SR) * np.exp(-t * 9)


def shaker():
    n = int(0.09 * SR)
    t = t_axis(n)
    return bp(RNG.standard_normal(n), 5000, 11000) * np.exp(-t * 55) * np.minimum(1, t / 0.006)


def rim():
    n = int(0.12 * SR)
    t = t_axis(n)
    return (bp(RNG.standard_normal(n), 1500, 4000) * 0.6 + np.sin(2 * np.pi * 820 * t) * 0.5) * np.exp(-t * 60)


def reverb_ir(seconds=1.9):
    n = int(seconds * SR)
    t = t_axis(n)
    ir = np.stack([RNG.standard_normal(n), RNG.standard_normal(n)]) * np.exp(-t * 6.9 / seconds / 2.2)
    ir = np.stack([lp(ch, 6500) for ch in ir])
    ir[:, : int(0.012 * SR)] = 0
    return ir / np.abs(ir).sum(axis=1, keepdims=True) * 6


def music():
    L = np.zeros((2, N + 3 * SR))
    keys = np.zeros_like(L)
    nbars = int(np.ceil(25.2 / BAR))           # progression until the end card
    pattern = [(0.0, 0, 0.55), (1.5, 2, 0.40), (2.0, 1, 0.45), (3.0, 3, 0.42), (3.5, 4, 0.36)]
    for b in range(nbars):
        t0 = b * BAR
        ch = CHORDS[b % 4]
        # pad
        p = pad([midi(n) for n in ch[1:4]], BAR + 0.6) * 0.16
        place(L[0], p, t0)
        place(L[1], np.roll(p, 240), t0)
        # electric piano broken chord
        for beat, i, vel in pattern:
            note = ch[i] + 12 * (i == 4 and b % 2 == 1)
            s = epiano(midi(note), 0.7, vel * 0.22)
            pan = 0.5 + 0.25 * np.sin(i * 1.7)
            place(keys[0], s * (1 - pan) * 1.4, t0 + beat * BEAT)
            place(keys[1], s * pan * 1.4, t0 + beat * BEAT)
        # bass from bar 2
        if b >= 1:
            for beat in (0, 2):
                s = bass(midi(ROOTS[b % 4] - 12 * (ROOTS[b % 4] > 44)), BEAT * 1.8) * 0.22
                place(L[0], s, t0 + beat * BEAT)
                place(L[1], s, t0 + beat * BEAT)
        # light rhythm from bar 3, thinning before the end card
        if 2 <= b < nbars - 1:
            for beat in (0, 2):
                k = kick() * 0.26
                place(L[0], k, t0 + beat * BEAT)
                place(L[1], k, t0 + beat * BEAT)
            for beat in (1, 3):
                r = rim() * 0.05
                place(L[0], r * 0.8, t0 + beat * BEAT)
                place(L[1], r * 1.0, t0 + beat * BEAT)
            for e in range(8):
                s = shaker() * (0.05 if e % 2 else 0.032)
                place(L[0], s * 0.7, t0 + e * BEAT / 2 + 0.012)
                place(L[1], s, t0 + e * BEAT / 2)
    # end-card resolution: Fmaj9 spread, held and fading
    tend = nbars * BAR
    for i, n in enumerate([41, 53, 60, 64, 67, 72]):
        s = epiano(midi(n), 3.5, 0.2) * (0.7 if n < 50 else 1)
        place(keys[0], s, tend + i * 0.06)
        place(keys[1], s, tend + i * 0.06 + 0.004)
    p = pad([midi(n) for n in (57, 60, 64)], 6.0) * 0.14
    place(L[0], p, tend)
    place(L[1], np.roll(p, 240), tend)

    dry = L + keys
    ir = reverb_ir()
    wet = np.stack([fftconvolve(dry[c], ir[c])[: dry.shape[1]] for c in range(2)])
    mix = dry * 0.82 + wet * 0.38
    mix = np.stack([hp(ch, 35) for ch in mix])[:, :N]
    # fade in first 0.25 s, fade out over the last 2 s
    t = t_axis(N)
    fade = np.clip(t / 0.25, 0, 1) * np.clip((DUR - t) / 2.0, 0, 1) ** 1.5
    return mix * fade


# ------------------------------------------------------------------ sound effects
def sfx_sound(kind, dur=None):
    if kind == "roll":
        n = int((dur or 1.5) * SR)
        t = t_axis(n)
        rumble = lp(RNG.standard_normal(n), 220) * (1 + 0.5 * np.sin(2 * np.pi * 9 * t))
        return rumble * env_adsr(n, 0.25, 0.1, 0.9, 0.5) * 3.0
    if kind == "soft_thud":
        n = int(0.5 * SR)
        t = t_axis(n)
        f = 70 + 60 * np.exp(-t * 30)
        return (np.sin(2 * np.pi * np.cumsum(f) / SR) * np.exp(-t * 14) + lp(RNG.standard_normal(n), 900) * np.exp(-t * 60) * 0.5)
    if kind == "whoosh":
        n = int(0.8 * SR)
        t = t_axis(n)
        noise = RNG.standard_normal(n)
        out = np.zeros(n)
        for k in range(8):  # stepped band sweep
            a, b = k * n // 8, (k + 1) * n // 8
            lo = 300 + 2200 * np.sin(np.pi * (k + 0.5) / 8)
            out[a:b] = bp(noise, lo, lo * 2.2)[a:b]
        return lp(out, 6000) * np.sin(np.pi * t / t[-1]) ** 2 * 1.4
    if kind == "jingle":
        n = int(0.9 * SR)
        t = t_axis(n)
        out = np.zeros(n)
        for hit in (0.0, 0.11, 0.19, 0.33):
            s = int(hit * SR)
            tt = t[: n - s]
            ring = sum(np.sin(2 * np.pi * f * tt) for f in RNG.uniform(2800, 7800, 5)) * np.exp(-tt * 18)
            out[s:] += ring * RNG.uniform(0.5, 1)
        return out * 0.35
    if kind == "latch":
        n = int(0.25 * SR)
        t = t_axis(n)
        return (bp(RNG.standard_normal(n), 1800, 5000) * np.exp(-t * 90) + np.sin(2 * np.pi * 1900 * t) * np.exp(-t * 70) * 0.4)
    if kind == "page":
        n = int(0.45 * SR)
        t = t_axis(n)
        return bp(RNG.standard_normal(n), 1200, 7000) * np.sin(np.pi * t / t[-1]) ** 1.5 * (1 + 0.6 * np.sin(2 * np.pi * 23 * t)) * 0.8
    raise ValueError(kind)


def effects():
    out = np.zeros((2, N))
    for e in TL["sfx"]:
        s = sfx_sound(e["type"], e.get("dur"))
        s = s / (np.max(np.abs(s)) + 1e-9) * db(e["gain_db"] + 12)
        pan = 0.5
        place(out[0], s * np.sqrt(1 - pan) * 1.41, e["at"])
        place(out[1], s * np.sqrt(pan) * 1.41, e["at"])
    return out * db(-6)


# ------------------------------------------------------------------ ducking + mix
def duck_curve(depth_db=-9.0, pre=0.18, post=0.35, ramp=0.22):
    g = np.zeros(N)
    t = t_axis(N)
    for seg in TL["voiceover"]["segments"]:
        a = seg["film_start"] - pre
        b = seg["film_start"] + (seg["src"][1] - seg["src"][0]) + post
        up = np.clip((t - (a - ramp)) / ramp, 0, 1)
        down = np.clip(((b + ramp) - t) / ramp, 0, 1)
        g = np.maximum(g, np.minimum(up, down))
    return db(depth_db * g)


def loudness(path):
    r = subprocess.run(["ffmpeg", "-hide_banner", "-nostats", "-i", str(path), "-af", "ebur128=peak=true", "-f", "null", "-"],
                       capture_output=True, text=True).stderr
    tail = r[r.rfind("Summary:"):]
    get = lambda k: float(tail.split(k)[1].split()[0])
    return {"integrated_lufs": get("I:"), "true_peak_dbfs": get("Peak:")}


def main():
    out = C / "audio"
    vo = narration()
    mus = music()
    fx = effects()
    # level the stems before mixing
    vo = vo / (np.max(np.abs(vo)) + 1e-9) * db(-3)
    mus = mus / (np.max(np.abs(mus)) + 1e-9) * db(-12)
    duck = duck_curve()
    write(out / "stems/vo_edit.wav", vo)
    write(out / "stems/music_bed_full.wav", mus.T)
    write(out / "stems/music_bed_ducked.wav", (mus * duck).T)
    write(out / "stems/sfx.wav", fx.T)
    mix = (mus * duck) + fx + vo[None, :] * 0.92
    mix = mix / max(1.0, np.max(np.abs(mix)) / db(-1))
    pre = out / "mix/TH_padova_mix_prelimiter.wav"
    write(pre, mix.T)
    # two-pass loudness normalisation for social playback
    m = subprocess.run(["ffmpeg", "-hide_banner", "-i", str(pre), "-af", "loudnorm=I=-14:TP=-1.5:LRA=9:print_format=json",
                        "-f", "null", "-"], capture_output=True, text=True).stderr
    js = json.loads(m[m.rfind("{"):m.rfind("}") + 1])
    final = out / "mix/TH_padova_mix.wav"
    subprocess.run(["ffmpeg", "-v", "error", "-y", "-i", str(pre), "-af",
                    f"loudnorm=I=-14:TP=-1.5:LRA=9:measured_I={js['input_i']}:measured_TP={js['input_tp']}:"
                    f"measured_LRA={js['input_lra']}:measured_thresh={js['input_thresh']}:offset={js['target_offset']}:linear=true,"
                    f"aresample={SR}", "-c:a", "pcm_s24le", str(final)], check=True)
    report = {p.name: loudness(p) for p in [out / "stems/vo_edit.wav", out / "stems/music_bed_full.wav",
                                            out / "stems/music_bed_ducked.wav", out / "stems/sfx.wav", final]}
    (out / "mix/loudness_report.json").write_text(json.dumps(report, indent=2))
    print(json.dumps(report, indent=2))


if __name__ == "__main__":
    main()
