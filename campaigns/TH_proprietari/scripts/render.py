"""Film 03 - Tailor Homes for property owners (IT). Photo-showcase renderer.

    python scripts/render.py --mode animatic            # 540x960, 30 fps, with audio -> out/
    python scripts/render.py --mode final               # 1080x1920, 60 fps, CRF 16
    python scripts/render.py --mode final --stills 3.2,18.9 [--guides]

Shared type, easing and photo helpers come from Film 02 (campaigns/TH_studenti/scripts/render.py);
this file only defines Film 03's own layout and scenes. All timing comes from ../timing.json.
Photography: real Tailor Homes photos (crop/scale only), full-bleed, one slow steady push per
photo - no pans, drifts or re-crops. Layout: cream double hairline frame, navy rising from the
bottom, centred type directly on the photo (same system as the cover, scripts/make_cover.py). Claims: tailorhomes.it/our-servies/ (see docs).
"""
import argparse
import importlib.util
import json
import subprocess
import sys

import numpy as np
from pathlib import Path

from PIL import Image, ImageDraw, ImageFilter

C = Path(__file__).resolve().parents[1]
ap = argparse.ArgumentParser()
ap.add_argument("--mode", choices=["animatic", "final"], default="animatic")
ap.add_argument("--stills", default="")
ap.add_argument("--guides", action="store_true")
ap.add_argument("--no-audio", action="store_true")
A = ap.parse_args()

# load Film 02's helpers at the same scale (they read --mode from argv)
_argv = sys.argv
sys.argv = [_argv[0], "--mode", A.mode]
_spec = importlib.util.spec_from_file_location("f2", C.parent / "TH_studenti" / "scripts" / "render.py")
F = importlib.util.module_from_spec(_spec)
_spec.loader.exec_module(F)
sys.argv = _argv

p, R, font, tlen, tracked, reveal_lines, view, logo = F.p, F.R, F.font, F.tlen, F.tracked, F.reveal_lines, F.view, F.logo
prog, lerp, clamp, ease_io, ease_out, expo_out, expo_io = F.prog, F.lerp, F.clamp, F.ease_io, F.ease_out, F.expo_out, F.expo_io
NAVY, CREAM, SALMON, CORAL = F.NAVY, F.CREAM, F.SALMON, F.CORAL
SERIF, LABEL, SANS, SANS_B = F.SERIF, F.LABEL, F.SANS, F.SANS_B
W, H, FPS, S = F.W, F.H, F.FPS, F.S
F.FOCUS.update({"SE6": (0.46, 0.5), "SE7": (0.5, 0.55), "SE1": (0.40, 0.6), "VN1": (0.55, 0.6), "VR1": (0.62, 0.6), "VR2": (0.42, 0.55), "CD2": (0.6, 0.55),
                "SE2": (0.52, 0.5), "SE5": (0.62, 0.55), "VN2": (0.5, 0.6), "VN5": (0.5, 0.6),
                "SE4": (0.45, 0.6), "CD1": (0.5, 0.6), "SE3": (0.55, 0.6)})

def logo_mono(img, cx, cy, width, alpha, colour=CREAM, shadow=0.45):
    """The supplied logo as a one-colour (reversed) mark for use directly on photos: same artwork,
    recoloured, with a soft low-contrast shadow so it holds on both sky and foliage."""
    if alpha <= 0:
        return
    w = round(p(width))
    lg = F.LOGO.resize((w, round(F.LOGO.height * w / F.LOGO.width)), Image.LANCZOS)
    a = lg.getchannel("A").point(lambda v: round(v * alpha))
    pos = (round(p(cx) - lg.width / 2), round(p(cy) - lg.height / 2))
    sh = Image.new("RGBA", lg.size, (12, 18, 34, 0))
    sh.putalpha(a.filter(ImageFilter.GaussianBlur(p(6))).point(lambda v: round(v * shadow)))
    img.alpha_composite(sh, (pos[0], pos[1] + round(p(2))))
    solid = Image.new("RGBA", lg.size, colour + (0,))
    solid.putalpha(a)
    img.alpha_composite(solid, pos)


TM = json.loads((C / "timing.json").read_text())
SC, PH = TM["scenes"], TM["phrases"]
DUR = TM["film_duration"]


def P(k):
    return PH[k]["start"]


def mid(name):
    a, b = SC[name]
    return (a + b) / 2


# ------------------------------------------------------------------ photo timeline
# (switch time, photo). Each photo cross-dissolves in over XF and pushes in slowly and evenly.
XF = 0.6
TIMELINE = [
    (0.0, "SE6"), (P(1) - 0.25, "SE7"),
    (SC["ch1"][0], "VR1"), (mid("ch1") + 0.2, "CD2"),
    (SC["ch2"][0], "VR2"), (mid("ch2"), "SE5"),
    (SC["ch3"][0], "VN2"), (mid("ch3"), "VN5"),
    (SC["ch4"][0], "SE4"), (mid("ch4"), "CD1"),
    (SC["close"][0], "SE3"),
]
FREEZE = SC["endcard"][0]          # the last photo stops pushing when the end card starts


def zoom_at(t, t0):
    return 1.02 + 0.011 * (min(t, FREEZE) - t0)


def photo_layer(t):
    """Full-bleed photo for time t, with the cross-dissolve between consecutive photos."""
    cur = max(i for i, (t0, _) in enumerate(TIMELINE) if t0 <= t)
    t0, pid = TIMELINE[cur]
    img = view(pid, W, H, zoom_at(t, t0)).convert("RGBA")
    k = ease_io(prog(t, t0, XF)) if cur > 0 else 1.0
    if k < 1:
        pt0, ppid = TIMELINE[cur - 1]
        prev = view(ppid, W, H, zoom_at(t, pt0)).convert("RGBA")
        img = Image.blend(prev, img, k)
    return img


# ------------------------------------------------------------------ hairline layout (matches the cover)
TX = 510                          # text axis: centred, nudged left of the Reels action buttons
CHAPTERS = [  # scene, numeral, title (site wording), services (site wording)
    ("ch1", "01", "Operatività e gestione", "PULIZIE · MANUTENZIONE · OSPITI"),
    ("ch2", "02", "Valorizzazione", "HOME STAGING · FOTOGRAFIA"),
    ("ch3", "03", "Marketing e performance", "CANALI ONLINE · DISTRIBUZIONE"),
    ("ch4", "04", "Supporto e consulenza", "IMMOBILIARE · LEGALE · FISCALE"),
]


def scrim(img, t):
    """Navy rising from the bottom edge; on the end card it climbs higher to hold the sign-off."""
    k = ease_io(prog(t, SC["endcard"][0] - 0.1, 0.9))
    y0 = lerp(700, 440, k)
    span = lerp(460, 520, k)
    ys = np.arange(H) / S
    a = np.clip((ys - y0) / span, 0, 1) ** 1.2 * 0.95
    a = np.where(ys < y0, 0, a)
    alpha = Image.fromarray(np.repeat((a * 255).astype(np.uint8)[:, None], W, 1), "L")
    lay = Image.new("RGBA", (W, H), NAVY + (0,))
    lay.putalpha(alpha)
    img.alpha_composite(lay)


def hairlines(img, t):
    k = ease_out(prog(t, 0.2, 0.9))
    if k <= 0:
        return
    lay = Image.new("RGBA", img.size, (0, 0, 0, 0))
    d = ImageDraw.Draw(lay)
    d.rectangle(R(40, 40, 1039, 1879), outline=CREAM + (round(255 * k),), width=max(1, round(p(3))))
    d.rectangle(R(54, 54, 1025, 1865), outline=CREAM + (round(150 * k),), width=1)
    img.alpha_composite(lay)


def chapter(img, t, scene, num, title, items):
    a, b = SC[scene]
    out = b - 0.32
    reveal_lines(img, [num], font(SERIF, 120), p(TX), p(1102), p(120), t, a + 0.1, SALMON, t_out=out, align="center")
    rl = 80 * ease_out(prog(t, a + 0.2, 0.5)) * (1 - ease_io(prog(t, out, 0.35)))
    if rl > 1:
        ImageDraw.Draw(img).rectangle(R(TX - rl / 2, 1262, TX + rl / 2, 1264), fill=SALMON)
    reveal_lines(img, [title], font(SANS_B, 56), p(TX), p(1290), p(64), t, a + 0.2, CREAM, t_out=out, stagger=0.05,
                 align="center")
    ki = ease_out(prog(t, a + 0.55, 0.5)) * (1 - ease_io(prog(t, out + 0.05, 0.3)))
    if ki > 0:
        tracked(img, (p(TX), p(1388) + (1 - ki) * p(10)), items, font(LABEL, 27), SALMON, 0.2, ki, align="center")


def captions(img, t):
    f_it = font(SERIF, 104)
    if t < SC["open"][1]:
        tracked(img, (p(TX), p(1150)), "PER PROPRIETARI", font(LABEL, 28), SALMON, 0.3,
                ease_out(prog(t, 0.6, 0.5)) * (1 - ease_io(prog(t, SC["open"][1] - 0.45, 0.3))), align="center")
        reveal_lines(img, ["Hai una casa", "a Padova?"], f_it, p(TX), p(1205), p(104), t, P(0) - 0.05, CREAM,
                     t_out=P(1) - 0.55, align="center")
        reveal_lines(img, ["Ce ne prendiamo cura,", "ogni giorno."], f_it, p(TX), p(1205), p(104), t, P(1) - 0.05,
                     CREAM, t_out=SC["open"][1] - 0.45, align="center")
    for sc, num, title, items in CHAPTERS:
        a, b = SC[sc]
        if a <= t < b:
            chapter(img, t, sc, num, title, items)
    a, b = SC["close"]
    if a <= t < b:                     # tagline on the last photo...
        reveal_lines(img, ["La tua casa,"], f_it, p(TX), p(1205), p(104), t, a + 0.1, CREAM, t_out=b - 0.3,
                     align="center")
        reveal_lines(img, ["su misura."], f_it, p(TX), p(1309), p(104), t, P(6) + 0.6, SALMON, t_out=b - 0.25,
                     align="center")
    if t >= SC["endcard"][0]:          # ...then the sign-off, same layout as the cover
        e = SC["endcard"][0]
        reveal_lines(img, ["La tua casa,"], f_it, p(TX), p(990), p(104), t, e + 0.25, CREAM, align="center")
        reveal_lines(img, ["su misura."], f_it, p(TX), p(1094), p(104), t, e + 0.4, SALMON, align="center")
        rl = 80 * ease_out(prog(t, e + 0.7, 0.5))
        if rl > 1:
            ImageDraw.Draw(img).rectangle(R(TX - rl / 2, 1240, TX + rl / 2, 1241), fill=SALMON)
        logo_mono(img, TX, 1340, 420, ease_out(prog(t, e + 0.85, 0.7)), shadow=0)
        tracked(img, (p(TX), p(1452)), "TAILORHOMES.IT", font(LABEL, 30), CREAM, 0.28,
                ease_out(prog(t, e + 1.3, 0.5)), align="center")


def frame(t):
    img = Image.new("RGBA", (W, H), NAVY + (255,))
    img.alpha_composite(photo_layer(t))
    scrim(img, t)
    hairlines(img, t)
    captions(img, t)
    fi = clamp(t / 0.5)                     # fade up from navy
    if fi < 1:
        img = Image.blend(Image.new("RGBA", (W, H), NAVY + (255,)), img, fi)
    if A.guides:
        g = Image.new("RGBA", img.size, (0, 0, 0, 0))
        d = ImageDraw.Draw(g)
        red = (220, 0, 0, 70)
        d.rectangle((0, 0, W, p(F.SAFE_T)), fill=red)
        d.rectangle((0, p(F.SAFE_B), W, H), fill=red)
        d.rectangle((p(F.SAFE_R), p(F.SAFE_T), W, p(F.SAFE_B)), fill=red)
        img.alpha_composite(g)
    return img.convert("RGB")


HERO = {"open": lambda: P(0) + 1.2, "care": lambda: P(1) + 1.5, "ch1": lambda: SC["ch1"][1] - 0.6,
        "ch2": lambda: SC["ch2"][1] - 0.7, "ch3": lambda: SC["ch3"][1] - 0.6, "ch4": lambda: SC["ch4"][1] - 0.6,
        "close": lambda: SC["close"][1] - 0.4, "endcard": lambda: DUR - 0.5}


def main():
    out = C / "out"
    out.mkdir(exist_ok=True)
    if A.stills:
        sd = out / "stills"
        sd.mkdir(exist_ok=True)
        items = [(k, f()) for k, f in HERO.items()] if A.stills == "hero" else \
            [(f"t{float(x):05.2f}", float(x)) for x in A.stills.split(",")]
        for name, t in items:
            frame(t).save(sd / f"{name}{'_guides' if A.guides else ''}.png")
        print("stills ->", sd)
        return
    name = f"tailorhomes-proprietari-{A.mode}.mp4"
    n = int(round(DUR * FPS))
    mix = C / "audio" / "mix" / "tailorhomes-proprietari-mix.wav"
    cmd = ["ffmpeg", "-v", "error", "-y", "-f", "rawvideo", "-pix_fmt", "rgb24", "-s", f"{W}x{H}", "-r", str(FPS), "-i", "-"]
    audio = not A.no_audio and mix.exists()
    if audio:
        cmd += ["-i", str(mix)]
    q = ["-crf", "23", "-preset", "veryfast"] if A.mode == "animatic" else ["-crf", "16", "-preset", "slow", "-profile:v", "high"]
    cmd += ["-c:v", "libx264", *q, "-pix_fmt", "yuv420p", "-color_primaries", "bt709", "-color_trc", "bt709",
            "-colorspace", "bt709", "-movflags", "+faststart"]
    if audio:
        cmd += ["-c:a", "aac", "-b:a", "256k", "-ar", "48000", "-shortest"]
    cmd += [str(out / name)]
    pr = subprocess.Popen(cmd, stdin=subprocess.PIPE)
    for i in range(n):
        pr.stdin.write(frame(i / FPS).tobytes())
    pr.stdin.close()
    assert pr.wait() == 0
    print(out / name, n, "frames")


if __name__ == "__main__":
    main()
