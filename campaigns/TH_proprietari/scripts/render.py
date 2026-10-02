"""Film 03 - Tailor Homes for property owners (IT). Photo-showcase renderer.

    python scripts/render.py --mode animatic            # 540x960, 30 fps, with audio -> out/
    python scripts/render.py --mode final               # 1080x1920, 60 fps, CRF 16
    python scripts/render.py --mode final --stills 3.2,18.9 [--guides]

Shared type, easing and photo helpers come from Film 02 (campaigns/TH_studenti/scripts/render.py);
this file only defines Film 03's own layout and scenes. All timing comes from ../timing.json.
Photography: real Tailor Homes photos (crop/scale only), full-bleed, one slow steady push per
photo - no pans, drifts or re-crops. Claims: tailorhomes.it/our-servies/ (see docs).
"""
import argparse
import importlib.util
import json
import subprocess
import sys
from pathlib import Path

from PIL import Image, ImageDraw

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
F.FOCUS.update({"SE1": (0.40, 0.6), "VN1": (0.55, 0.6), "VR1": (0.62, 0.6), "CD2": (0.6, 0.55),
                "SE2": (0.52, 0.5), "SE5": (0.62, 0.55), "VN2": (0.5, 0.6), "VN5": (0.5, 0.6),
                "SE4": (0.45, 0.6), "CD1": (0.5, 0.6), "SE3": (0.55, 0.6)})

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
    (0.0, "SE1"), (P(1) - 0.25, "VN1"),
    (SC["ch1"][0], "VR1"), (mid("ch1") + 0.2, "CD2"),
    (SC["ch2"][0], "SE2"), (mid("ch2"), "SE5"),
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


# ------------------------------------------------------------------ the navy caption card
BOX = (60, 1160, 930, 1420)
CHAPTERS = [  # scene, numeral, title (site wording), services (site wording)
    ("ch1", "01", "Operatività e gestione", "PULIZIE · MANUTENZIONE · OSPITI"),
    ("ch2", "02", "Valorizzazione", "HOME STAGING · FOTOGRAFIA"),
    ("ch3", "03", "Marketing e performance", "CANALI ONLINE · DISTRIBUZIONE"),
    ("ch4", "04", "Supporto e consulenza", "IMMOBILIARE · LEGALE · FISCALE"),
]


def card(img, t):
    """Navy card: opens left->right at the start, closes right->left into the end card."""
    a_in = expo_out(prog(t, 0.3, 0.8))
    a_out = expo_io(prog(t, SC["endcard"][0] - 0.05, 0.6))
    k = a_in * (1 - a_out)
    if k <= 0:
        return 0.0
    x0, y0, x1, y1 = BOX
    lay = Image.new("RGBA", img.size, (0, 0, 0, 0))
    ImageDraw.Draw(lay).rectangle(R(x0, y0, lerp(x0, x1, k), y1), fill=NAVY + (242,))
    img.alpha_composite(lay)
    return k


def chapter(img, t, scene, num, title, items):
    a, b = SC[scene]
    out = b - 0.32
    reveal_lines(img, [num], font(SERIF, 124), p(96), p(1176), p(124), t, a + 0.12, SALMON, t_out=out)
    hl = ease_out(prog(t, a + 0.15, 0.5)) * (1 - ease_io(prog(t, out, 0.35)))
    if hl > 0:
        ImageDraw.Draw(img).rectangle(R(214, 1196, 216, 1196 + 190 * hl), fill=SALMON)
    reveal_lines(img, [title], font(SANS_B, 50), p(240), p(1206), p(60), t, a + 0.2, CREAM, t_out=out, stagger=0.05)
    ki = ease_out(prog(t, a + 0.55, 0.5)) * (1 - ease_io(prog(t, out + 0.05, 0.3)))
    if ki > 0:
        tracked(img, (p(242), p(1312) + (1 - ki) * p(10)), items, font(LABEL, 27), SALMON, 0.18, ki)


def captions(img, t):
    if card(img, t) <= 0:
        return
    f_it = font(SERIF, 88)
    if t < SC["open"][1]:
        tracked(img, (p(98), p(1192)), "PER PROPRIETARI", font(LABEL, 24), SALMON, 0.3,
                ease_out(prog(t, 0.7, 0.4)) * (1 - ease_io(prog(t, SC["open"][1] - 0.45, 0.3))))
        reveal_lines(img, ["Hai una casa", "a Padova?"], f_it, p(94), p(1232), p(86), t, P(0) - 0.05, CREAM,
                     t_out=P(1) - 0.55)
        reveal_lines(img, ["Ce ne prendiamo cura,", "ogni giorno."], f_it, p(94), p(1232), p(86), t, P(1) - 0.05,
                     CREAM, t_out=SC["open"][1] - 0.45)
    for sc, num, title, items in CHAPTERS:
        a, b = SC[sc]
        if a <= t < b:
            chapter(img, t, sc, num, title, items)
    a, b = SC["close"]
    if a <= t < b + 0.2:
        reveal_lines(img, ["La tua casa,"], f_it, p(94), p(1206), p(86), t, a + 0.1, CREAM, t_out=b - 0.35)
        reveal_lines(img, ["su misura."], f_it, p(94), p(1292), p(86), t, P(6) + 0.6, SALMON, t_out=b - 0.3)


# ------------------------------------------------------------------ end card
WIN = (150, 290, 930, 850)       # final window onto the (unmoving) last photo


def endcard(img, photo, t):
    a = SC["endcard"][0]
    k = expo_io(prog(t, a + 0.1, 1.1))
    if k <= 0:
        img.alpha_composite(photo)
        return
    img.paste(Image.new("RGBA", (W, H), CREAM + (255,)), (0, 0))
    x0, y0, x1, y1 = WIN
    rect = R(lerp(0, x0, k), lerp(0, y0, k), lerp(1080, x1, k), lerp(1920, y1, k))
    m = Image.new("L", (W, H), 0)
    ImageDraw.Draw(m).rounded_rectangle(rect, radius=p(lerp(0, 8, k)), fill=255)
    img.paste(photo, (0, 0), m)
    cx = (x0 + x1) / 2
    f = font(SERIF, 96)
    reveal_lines(img, ["La tua casa,"], f, p(cx), p(905), p(96), t, a + 0.95, NAVY, align="center", stagger=0.06)
    reveal_lines(img, ["su misura."], f, p(cx), p(1000), p(96), t, a + 1.15, CORAL, align="center", stagger=0.06)
    kl = ease_out(prog(t, a + 1.45, 0.7))
    logo(img, cx, 1238 + (1 - kl) * 10, 560, kl)
    ku = ease_out(prog(t, a + 1.9, 0.5))
    fu = font(SANS, 50)
    url = "tailorhomes.it"
    tw = tlen(url, fu)
    tl = Image.new("RGBA", img.size, (0, 0, 0, 0))
    ImageDraw.Draw(tl).text((p(cx) - tw / 2, p(1385) + (1 - ku) * p(10)), url, font=fu, fill=NAVY + (round(255 * ku),))
    img.alpha_composite(tl)
    ul = tw * ease_io(prog(t, a + 2.2, 0.6))
    if ul > 1:
        ImageDraw.Draw(img).rectangle((p(cx) - tw / 2, p(1458), p(cx) - tw / 2 + ul, p(1461)), fill=SALMON)


def frame(t):
    img = Image.new("RGBA", (W, H), NAVY + (255,))
    photo = photo_layer(t)
    if t >= SC["endcard"][0]:
        endcard(img, photo, t)
    else:
        img.alpha_composite(photo)
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
