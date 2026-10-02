"""Film 03 audio: narration placement, original felt-piano + strings bed, minimal SFX, ducking, mix.

    python scripts/make_audio.py

Voice: audio/voice/tailorhomes-proprietari-vo.wav (Higgsfield text2speech_v2 / ElevenLabs preset
"Gia", chosen by the client from three samples). Placed at timing.json lead_in, no time-stretching.
Music: original and code-synthesised (no samples, no third-party material). It shares low-level
helpers (filters, pad, reverb, loudness) with Film 01 but has its own instruments and writing:
72 BPM, F major, felt piano + legato strings + cello, no drums.
Outputs (48 kHz / 24-bit WAV): audio/stems/{vo,music_full,music_ducked,sfx}.wav,
audio/mix/tailorhomes-proprietari-mix.wav (-14 LUFS, -1.5 dBTP) + loudness report.
"""
import importlib.util
import json
import subprocess
import sys
from pathlib import Path

import numpy as np

C = Path(__file__).resolve().parents[1]
spec = importlib.util.spec_from_file_location(
    "film01_audio", C.parents[0] / "TH_padova_lavoro" / "scripts" / "make_audio.py")
B = importlib.util.module_from_spec(spec)
sys.argv = sys.argv[:1]
spec.loader.exec_module(B)

TM = json.loads((C / "timing.json").read_text())
SR = B.SR
DUR = TM["film_duration"]
N = int(SR * DUR)
SC, PH = TM["scenes"], TM["phrases"]
RNG = np.random.default_rng(23)


def place(buf, sig, at):
    i = int(at * SR)
    if i < 0 or i >= buf.shape[-1]:
        return
    j = min(buf.shape[-1], i + sig.shape[-1])
    buf[..., i:j] += sig[..., : j - i]


def voice():
    src = C / "audio" / "voice" / "tailorhomes-proprietari-vo.wav"
    raw = subprocess.run(["ffmpeg", "-v", "error", "-i", str(src), "-ac", "1", "-ar", str(SR), "-f", "f32le", "-"],
                         capture_output=True, check=True).stdout
    vo = B.hp(np.frombuffer(raw, np.float32).astype(np.float64), 75)
    out = np.zeros(N)
    place(out, vo, TM["lead_in"])
    return out, src


# ------------------------------------------------------------------ instruments
def felt_piano(freq, vel, length=3.2):
    """Soft, slightly inharmonic piano tone with a felt-damped attack."""
    n = int(length * SR)
    t = np.arange(n) / SR
    s = np.zeros(n)
    for k, amp in enumerate((1.0, 0.42, 0.22, 0.12, 0.07, 0.04), start=1):
        fk = freq * k * np.sqrt(1 + 0.00035 * k * k)               # string stiffness
        s += amp * np.sin(2 * np.pi * fk * t + RNG.uniform(0, 6.28)) * np.exp(-t * (0.9 + 0.55 * k))
    hammer = B.lp(RNG.standard_normal(n), 1200) * np.exp(-t * 90) * 0.06
    s = B.lp(s + hammer, 2600)
    att = np.minimum(1, t / 0.006)
    return s * att * vel


def strings(freqs, dur, attack=1.1):
    n = int(dur * SR)
    t = np.arange(n) / SR
    s = np.zeros(n)
    for f in freqs:
        for det in (-0.08, 0.0, 0.09):
            vib = 1 + 0.0025 * np.sin(2 * np.pi * (5.1 + det) * t + RNG.uniform(0, 6.28))
            ph = 2 * np.pi * np.cumsum(f * 2 ** (det / 12) * vib) / SR
            s += sum(np.sin(k * ph) / k ** 1.3 for k in range(1, 7))
    s = B.lp(s, 1800)
    env = np.minimum(1, t / attack) * np.clip((dur - t) / 1.0, 0, 1)
    return s * env / (3 * len(freqs))


BPM = 72
BEAT = 60 / BPM
BAR = 4 * BEAT
# Fmaj9 | Dm9 | Bbmaj7(#11) | Csus4 -> C        (voicings: MIDI, right hand)
CHORDS = [[60, 64, 67, 69, 72], [57, 60, 64, 65, 69], [58, 62, 65, 69, 64 + 12], [60, 65, 67, 72, 70]]
ROOTS = [41, 38, 46, 36]
# a short top-line motif that answers the arpeggio (bar-relative beats, MIDI)
MOTIF = [[(0.0, 81), (1.5, 79), (2.5, 77)], [(0.5, 77), (2.0, 76), (3.0, 74)],
         [(0.0, 74), (1.5, 76), (2.5, 77)], [(1.0, 79), (2.5, 76)]]


def music():
    L = np.zeros((2, N + 4 * SR))
    end_t = SC["endcard"][0]
    strings_from = SC["ch1"][0]
    nbars = int(np.ceil(end_t / BAR)) + 1
    for b in range(nbars):
        t0 = b * BAR
        if t0 >= end_t:
            break
        ch, root = CHORDS[b % 4], ROOTS[b % 4]
        # left hand: low octave on 1, fifth on 3
        for beat, note, v in ((0, root + 12, 0.30), (2, root + 19, 0.20)):
            s = felt_piano(B.midi(note), v, 3.0)
            place(L[0], s * 1.05, t0 + beat * BEAT)
            place(L[1], s * 0.85, t0 + beat * BEAT)
        # right hand: gentle broken chord in 8ths, slightly humanised
        for e, idx in enumerate([0, 2, 1, 3, 2, 4, 3, 1]):
            tt = t0 + e * BEAT / 2 + RNG.uniform(-0.012, 0.012)
            if tt >= end_t:
                break
            s = felt_piano(B.midi(ch[idx]), 0.12 if e % 2 else 0.16, 2.4)
            pan = 0.4 + 0.2 * (idx / 4)
            place(L[0], s * (1.2 - pan), tt)
            place(L[1], s * (0.8 + pan), tt)
        if t0 >= strings_from - BAR:                       # melody joins one bar before the strings
            for beat, note in MOTIF[b % 4]:
                s = felt_piano(B.midi(note), 0.17, 3.0)
                place(L[0], s * 0.9, t0 + beat * BEAT)
                place(L[1], s, t0 + beat * BEAT)
        if t0 + BAR > strings_from:
            st = strings([B.midi(n) for n in ch[1:4]], BAR + 0.8) * 0.16
            place(L[0], st, t0)
            place(L[1], np.roll(st, 240), t0)
            vc = strings([B.midi(root)], BAR + 0.6, attack=0.5) * 0.22
            place(L[0], vc, t0)
            place(L[1], vc, t0)
    # end card: rolled Fmaj9 on the piano + held strings
    for i, n in enumerate([29, 41, 48, 57, 60, 64, 67, 69, 72]):
        s = felt_piano(B.midi(n), 0.26 if n < 50 else 0.18, 5.0)
        place(L[0], s * (1.1 - i * 0.04), end_t + i * 0.07)
        place(L[1], s * (0.8 + i * 0.04), end_t + i * 0.07)
    st = strings([B.midi(n) for n in (53, 57, 60, 64)], DUR - end_t + 1, attack=0.6) * 0.17
    place(L[0], st, end_t)
    place(L[1], np.roll(st, 240), end_t)
    ir = B.reverb_ir(2.6)
    wet = np.stack([B.fftconvolve(L[c], ir[c])[: L.shape[1]] for c in range(2)])
    mix = (L * 0.8 + wet * 0.42)[:, :N]
    mix = np.stack([B.hp(c, 32) for c in mix])
    t = np.arange(N) / SR
    fade = np.clip(t / 0.6, 0, 1) * np.clip((DUR - t) / 1.8, 0, 1) ** 1.3
    return mix * fade


SFX = [  # (time, kind, gain dB) - deliberately sparse
    (SC["ch1"][0] - 0.1, "whoosh", -38), (SC["ch2"][0] - 0.1, "whoosh", -39),
    (SC["ch3"][0] - 0.1, "whoosh", -39), (SC["ch4"][0] - 0.1, "whoosh", -39),
    (SC["endcard"][0] + 0.05, "whoosh", -35),
]


def effects():
    out = np.zeros((2, N))
    for at, kind, g in SFX:
        s = B.sfx_sound(kind)
        s = s / (np.max(np.abs(s)) + 1e-9) * B.db(g + 12)
        place(out[0], s, at)
        place(out[1], s, at)
    return out * B.db(-6)


def duck(depth_db=-7.5, pre=0.15, post=0.3, ramp=0.25):
    g = np.zeros(N)
    t = np.arange(N) / SR
    for ph in PH:
        a, b = ph["start"] - pre, ph["end"] + post
        g = np.maximum(g, np.minimum(np.clip((t - (a - ramp)) / ramp, 0, 1), np.clip(((b + ramp) - t) / ramp, 0, 1)))
    return B.db(depth_db * g)


def main():
    out = C / "audio"
    (out / "stems").mkdir(parents=True, exist_ok=True)
    (out / "mix").mkdir(parents=True, exist_ok=True)
    vo, src = voice()
    mus = music()
    fx = effects()
    vo = vo / (np.max(np.abs(vo)) + 1e-9) * B.db(-3)
    mus = mus / (np.max(np.abs(mus)) + 1e-9) * B.db(-8)
    d = duck()
    B.write(out / "stems/vo.wav", vo)
    B.write(out / "stems/music_full.wav", mus.T)
    B.write(out / "stems/music_ducked.wav", (mus * d).T)
    B.write(out / "stems/sfx.wav", fx.T)
    mix = mus * d + fx + vo[None, :] * 0.92
    mix = mix / max(1.0, np.max(np.abs(mix)) / B.db(-1))
    pre = out / "mix/tailorhomes-proprietari-mix_prelimiter.wav"
    B.write(pre, mix.T)
    m = subprocess.run(["ffmpeg", "-hide_banner", "-i", str(pre), "-af", "loudnorm=I=-14:TP=-1.5:LRA=9:print_format=json",
                        "-f", "null", "-"], capture_output=True, text=True).stderr
    js = json.loads(m[m.rfind("{"):m.rfind("}") + 1])
    final = out / "mix/tailorhomes-proprietari-mix.wav"
    subprocess.run(["ffmpeg", "-v", "error", "-y", "-i", str(pre), "-af",
                    f"loudnorm=I=-14:TP=-1.5:LRA=9:measured_I={js['input_i']}:measured_TP={js['input_tp']}:"
                    f"measured_LRA={js['input_lra']}:measured_thresh={js['input_thresh']}:offset={js['target_offset']}:linear=true,"
                    f"aresample={SR}", "-c:a", "pcm_s24le", str(final)], check=True)
    pre.unlink()
    rep = {"voice_source": str(src.relative_to(C)), **{p.name: B.loudness(p) for p in
           [out / "stems/vo.wav", out / "stems/music_full.wav", out / "stems/music_ducked.wav", out / "stems/sfx.wav", final]}}
    (out / "mix/loudness_report.json").write_text(json.dumps(rep, indent=2))
    print(json.dumps(rep, indent=2))


if __name__ == "__main__":
    main()
