"""Film 02 audio: voice-over placement, original music bed, restrained SFX, ducking, mix.

    python scripts/make_audio.py

Voice: audio/voice/tailorhomes-students-vo.wav (supplied master) if present, else the
labelled scratch read. Placed at timing.json lead_in, no time-stretching.
Music: original, code-synthesised (no samples) - reuses the synthesis helpers from
Film 01 (campaigns/TH_padova_lavoro/scripts/make_audio.py) with a new arrangement:
104 BPM, D major, plucked arpeggios + soft pad + light drums. See docs/production-log.
Outputs (48 kHz / 24-bit WAV): audio/stems/{vo,music_full,music_ducked,sfx}.wav,
audio/mix/tailorhomes-students-mix.wav (-14 LUFS, -1.5 dBTP) + loudness report.
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
SC, PH, EM = TM["scenes"], TM["phrases"], TM["emphasis"]
RNG = np.random.default_rng(11)


def place(buf, sig, at):
    i = int(at * SR)
    if i < 0 or i >= buf.shape[-1]:
        return
    j = min(buf.shape[-1], i + sig.shape[-1])
    buf[..., i:j] += sig[..., : j - i]


def voice():
    src = C / "audio" / "voice" / "tailorhomes-students-vo.wav"
    if not src.exists():
        src = C / TM["source"]
    raw = subprocess.run(["ffmpeg", "-v", "error", "-i", str(src), "-ac", "1", "-ar", str(SR), "-f", "f32le", "-"],
                         capture_output=True, check=True).stdout
    vo = B.hp(np.frombuffer(raw, np.float32).astype(np.float64), 75)
    out = np.zeros(N)
    place(out, vo, TM["lead_in"])
    return out, src


BPM = 104
BEAT = 60 / BPM
BAR = 4 * BEAT
# Dmaj9 | Bm7(add11) | Gmaj7 | Asus4 -> A   (bright, unresolved until the end card)
CHORDS = [[50, 57, 61, 64, 66], [47, 54, 57, 62, 64], [43, 50, 54, 59, 62], [45, 52, 57, 62, 64]]
ROOTS = [38, 35, 43, 45]


def pluck(freq, vel):
    n = int(0.9 * SR)
    t = np.arange(n) / SR
    s = np.sin(2 * np.pi * freq * t + 0.8 * np.exp(-t * 18) * np.sin(2 * np.pi * freq * 2 * t))
    s += 0.25 * np.sin(2 * np.pi * freq * 3 * t) * np.exp(-t * 12)
    return s * np.exp(-t * 6.5) * np.minimum(1, t / 0.003) * vel


def clap():
    n = int(0.25 * SR)
    t = np.arange(n) / SR
    burst = sum(np.exp(-np.clip(t - d, 0, None) * 120) * (t >= d) for d in (0, 0.008, 0.017))
    return B.bp(RNG.standard_normal(n), 900, 5000) * (burst + 0.3 * np.exp(-t * 25))


def music():
    L = np.zeros((2, N + 3 * SR))
    end_t = SC["endcard"][0]
    nbars = int(np.ceil(end_t / BAR)) + 1
    groove_from = SC["variety1"][0]
    lift_from, lift_to = SC["discount"][0], SC["claim"][0]
    for b in range(nbars):
        t0 = b * BAR
        if t0 >= end_t:
            break
        ch = CHORDS[b % 4]
        pd = B.pad([B.midi(n) for n in ch[1:4]], BAR + 0.5) * 0.11
        place(L[0], pd, t0)
        place(L[1], np.roll(pd, 300), t0)
        for e in range(8):                                  # 8th-note plucked arpeggio
            tt = t0 + e * BEAT / 2
            if tt >= end_t:
                break
            note = ch[[1, 2, 3, 4, 3, 2, 4, 1][e]] + 12
            s = pluck(B.midi(note), 0.13 if e % 2 == 0 else 0.09)
            pan = 0.35 + 0.3 * (e % 3) / 2
            place(L[0], s * (1 - pan) * 1.4, tt)
            place(L[1], s * pan * 1.4, tt)
        if t0 >= groove_from - 0.01:
            for beat in (0, 2):
                bs = B.bass(B.midi(ROOTS[b % 4]), BEAT * 1.6) * 0.2
                place(L[0], bs, t0 + beat * BEAT)
                place(L[1], bs, t0 + beat * BEAT)
            for beat in (0, 2):
                k = B.kick() * (0.24 if lift_from <= t0 < lift_to else 0.18)
                place(L[0], k, t0 + beat * BEAT)
                place(L[1], k, t0 + beat * BEAT)
            for beat in (1, 3):
                c = clap() * 0.06
                place(L[0], c * 0.9, t0 + beat * BEAT)
                place(L[1], c, t0 + beat * BEAT)
            for e in range(8):
                sh = B.shaker() * (0.045 if e % 2 else 0.028)
                place(L[0], sh * 0.8, t0 + e * BEAT / 2 + 0.01)
                place(L[1], sh, t0 + e * BEAT / 2)
    # end card: Dmaj9 spread, held
    for i, n in enumerate([38, 50, 57, 61, 64, 66, 69]):
        s = B.epiano(B.midi(n), 3.5, 0.18) * (0.7 if n < 45 else 1)
        place(L[0], s, end_t + i * 0.05)
        place(L[1], s, end_t + i * 0.05 + 0.004)
    pd = B.pad([B.midi(n) for n in (57, 61, 66)], DUR - end_t + 1) * 0.12
    place(L[0], pd, end_t)
    place(L[1], np.roll(pd, 300), end_t)
    ir = B.reverb_ir(1.6)
    wet = np.stack([B.fftconvolve(L[c], ir[c])[: L.shape[1]] for c in range(2)])
    mix = (L * 0.85 + wet * 0.3)[:, :N]
    mix = np.stack([B.hp(c, 35) for c in mix])
    t = np.arange(N) / SR
    fade = np.clip(t / 0.4, 0, 1) * np.clip((DUR - t) / 2.2, 0, 1) ** 1.4
    return mix * fade


SFX = [  # (time, kind, gain dB)
    (SC["open"][1] - 0.75, "whoosh", -31), (SC["variety2"][0] + 0.05, "whoosh", -32),
    (SC["split"][0], "whoosh", -31), (SC["benefits"][0], "whoosh", -32),
    (SC["discount"][0], "whoosh", -32), (EM["fifteen percent"] - 0.12, "impact", -24),
    (SC["claim"][0], "whoosh", -33), (PH[6]["start"] + 0.75, "click", -27),
    (SC["mosaic"][0], "ticks", -33), (SC["mosaic"][1] - 0.3, "whoosh", -31),
]


def sfx_sound(kind):
    if kind == "impact":
        n = int(0.9 * SR)
        t = np.arange(n) / SR
        f = 55 + 90 * np.exp(-t * 22)
        body = np.sin(2 * np.pi * np.cumsum(f) / SR) * np.exp(-t * 7)
        air = B.lp(RNG.standard_normal(n), 2500) * np.exp(-t * 30) * 0.4
        shimmer = sum(np.sin(2 * np.pi * fr * t) for fr in (880, 1320, 1760)) * np.exp(-t * 5) * 0.05
        return body + air + shimmer
    if kind == "click":
        n = int(0.12 * SR)
        t = np.arange(n) / SR
        return (np.sin(2 * np.pi * 2300 * t) * np.exp(-t * 90) + B.bp(RNG.standard_normal(n), 2000, 6000) * np.exp(-t * 160) * 0.5)
    if kind == "ticks":
        n = int(1.2 * SR)
        out = np.zeros(n)
        for i in range(9):
            tt = np.arange(int(0.05 * SR)) / SR
            s = np.sin(2 * np.pi * (1500 + 60 * i) * tt) * np.exp(-tt * 120)
            place(out, s * 0.6, 0.08 * (i // 1) * 0.9)
        return out
    return B.sfx_sound(kind)


def effects():
    out = np.zeros((2, N))
    for at, kind, g in SFX:
        s = sfx_sound(kind)
        s = s / (np.max(np.abs(s)) + 1e-9) * B.db(g + 12)
        place(out[0], s, at)
        place(out[1], s, at)
    return out * B.db(-6)


def duck(depth_db=-10.0, pre=0.15, post=0.3, ramp=0.2):
    g = np.zeros(N)
    t = np.arange(N) / SR
    for ph in PH:
        a, b = ph["start"] - pre, ph["end"] + post
        g = np.maximum(g, np.minimum(np.clip((t - (a - ramp)) / ramp, 0, 1), np.clip(((b + ramp) - t) / ramp, 0, 1)))
    return B.db(depth_db * g)


def main():
    out = C / "audio"
    vo, src = voice()
    mus = music()
    fx = effects()
    vo = vo / (np.max(np.abs(vo)) + 1e-9) * B.db(-3)
    mus = mus / (np.max(np.abs(mus)) + 1e-9) * B.db(-12)
    d = duck()
    B.write(out / "stems/vo.wav", vo)
    B.write(out / "stems/music_full.wav", mus.T)
    B.write(out / "stems/music_ducked.wav", (mus * d).T)
    B.write(out / "stems/sfx.wav", fx.T)
    mix = mus * d + fx + vo[None, :] * 0.92
    mix = mix / max(1.0, np.max(np.abs(mix)) / B.db(-1))
    pre = out / "mix/tailorhomes-students-mix_prelimiter.wav"
    B.write(pre, mix.T)
    m = subprocess.run(["ffmpeg", "-hide_banner", "-i", str(pre), "-af", "loudnorm=I=-14:TP=-1.5:LRA=9:print_format=json",
                        "-f", "null", "-"], capture_output=True, text=True).stderr
    js = json.loads(m[m.rfind("{"):m.rfind("}") + 1])
    final = out / "mix/tailorhomes-students-mix.wav"
    subprocess.run(["ffmpeg", "-v", "error", "-y", "-i", str(pre), "-af",
                    f"loudnorm=I=-14:TP=-1.5:LRA=9:measured_I={js['input_i']}:measured_TP={js['input_tp']}:"
                    f"measured_LRA={js['input_lra']}:measured_thresh={js['input_thresh']}:offset={js['target_offset']}:linear=true,"
                    f"aresample={SR}", "-c:a", "pcm_s24le", str(final)], check=True)
    rep = {"voice_source": str(src.relative_to(C)), **{p.name: B.loudness(p) for p in
           [out / "stems/vo.wav", out / "stems/music_full.wav", out / "stems/music_ducked.wav", out / "stems/sfx.wav", final]}}
    (out / "mix/loudness_report.json").write_text(json.dumps(rep, indent=2))
    print(json.dumps(rep, indent=2))


if __name__ == "__main__":
    main()
