"""Composite the Tailor Homes reel: 3D renders + transitions + kinetic headlines,
logo, captions, end card, then encode with the audio mix.

    python composite.py --tier preview          # 540x960 review MP4
    python composite.py --tier final            # 1080x1920 master MP4
    python composite.py --tier preview --stills 15,200,400   # PNG checks only
    python composite.py --tier final --safe-guides --stills 450

Layout is designed in 1080x1920 coordinates and scaled for the preview.
All text is 2D overlay; nothing is baked into the 3D renders. Text sits directly
on the picture (no boxes): its colour follows the brightness of the plate behind
it (navy on light, cream on dark) and a soft shadow keeps it legible.
Requires Pillow, numpy and ffmpeg.
"""
import argparse
import json
import subprocess
from pathlib import Path

import numpy as np
from PIL import Image, ImageDraw, ImageFilter, ImageFont

C = Path(__file__).resolve().parents[1]
REPO = C.parents[1]
FONT_DIRS = [C / "fonts", REPO / "brand" / "fonts"]      # campaign bold weights, then library regulars
TL = json.loads((C / "timeline.json").read_text())
FPS, HANDLE = TL["fps"], TL["handle_frames"]
NFRAMES = int(TL["duration"] * FPS)
PAL = {k: tuple(int(v[i:i + 2], 16) for i in (1, 3, 5)) for k, v in
       json.loads((REPO / "brand" / "palette.json").read_text())["website_colors"].items()}
ACCENT_ON_DARK = (240, 163, 145)                      # lighter terracotta for cream-on-dark mode
SAFE = TL["safe_area"]
X0, X1 = SAFE["left"], 1080 - SAFE["right"]          # 60 .. 930
TEXT_CX = 520                                         # optical centre inside the safe area
HEAD_Y = 272                                          # top of the headline block (just below the top UI zone)

ap = argparse.ArgumentParser()
ap.add_argument("--tier", default="preview", choices=["preview", "final"])
ap.add_argument("--stills", default="")
ap.add_argument("--safe-guides", action="store_true")
ap.add_argument("--no-audio", action="store_true")
A = ap.parse_args()
S = 0.5 if A.tier == "preview" else 1.0
W, H = round(1080 * S), round(1920 * S)

_fonts = {}


def font(name, size):
    key = (name, size)
    if key not in _fonts:
        path = next(d / name for d in FONT_DIRS if (d / name).exists())
        _fonts[key] = ImageFont.truetype(str(path), max(8, round(size * S)))
    return _fonts[key]


F_HEAD = lambda: font("Jost-Bold.ttf", 108)
F_CAP = lambda: font("DMSans-Bold.ttf", 46)
F_LABEL = lambda: font("DMSans-Medium.ttf", 24)


def clamp(x):
    return min(1.0, max(0.0, x))


def sm(x):
    x = clamp(x)
    return x * x * (3 - 2 * x)


def ease_out(x):
    x = clamp(x)
    return 1 - (1 - x) ** 3


def ease_in(x):
    x = clamp(x)
    return x ** 3


def ease_out_back(x, c1=1.5):
    x = clamp(x)
    c3 = c1 + 1
    return 1 + c3 * (x - 1) ** 3 + c1 * (x - 1) ** 2


def p(v):
    return round(v * S)


def mix_rgb(a, b, k):
    return tuple(round(a[i] + (b[i] - a[i]) * k) for i in range(3))


# ------------------------------------------------------------------ 3D plates
SCENES = [(k, v) for k, v in TL["scenes"].items()]
_cache = {}


def plate(scene, film_frame):
    info = TL["scenes"][scene]
    local = film_frame - round(info["start"] * FPS) + HANDLE + 1
    path = C / "renders" / "final" / scene / f"f_{local:04d}.png"     # full-res plates (downscaled for preview)
    if not path.exists():
        path = C / "renders" / A.tier / scene / f"f_{local:04d}.png"
    if scene == "S5_logo_cta":
        return end_card(film_frame)
    if path not in _cache:
        _cache.clear()
        im = Image.open(path).convert("RGB")
        if im.size != (W, H):
            im = im.resize((W, H), Image.LANCZOS)
        _cache[path] = im
    return _cache[path]


def base_frame(ff):
    t = ff / FPS
    for tr in TL["transitions"]:
        b = round(tr["at"] * FPS)
        half = tr["frames"] // 2
        if b - half <= ff < b + half:
            a_scene = next(k for k, v in SCENES if v["end"] == tr["at"])
            b_scene = next(k for k, v in SCENES if v["start"] == tr["at"])
            mix = sm((ff - (b - half) + 0.5) / tr["frames"])
            img = Image.blend(plate(a_scene, ff), plate(b_scene, ff), mix)
            if tr["type"] == "light_through_door":     # warm bloom through the doorway
                glow = Image.new("RGB", (W, H), (252, 246, 236))
                img = Image.blend(img, glow, 0.35 * np.sin(np.pi * mix))
            return img
    scene = next(k for k, v in SCENES if v["start"] <= t < v["end"])
    return plate(scene, ff).copy()


# ------------------------------------------------------------------ adaptive contrast
class Tone:
    """0 = light background (navy text), 1 = dark background (cream text).
    Smoothed over time when frames are processed in order, so colour changes glide."""

    def __init__(self):
        self.k, self.last = None, None

    def update(self, img, box, ff):
        x0, y0, x1, y1 = (p(v) for v in box)
        lum = np.asarray(img.crop((x0, y0, x1, y1)).convert("L"), dtype=np.float32).mean() / 255
        target = 1.0 if lum < 0.55 else 0.0            # decisive: navy or cream, never a grey in between
        if self.k is None or self.last != ff - 1:
            self.k = target
        else:
            self.k += (target - self.k) * 0.15         # ~0.3 s glide when the background changes
        self.last = ff
        return self.k


HEAD_TONE, BUG_TONE = Tone(), Tone()


def soft_shadow(layer, dark_k, strength=1.0):
    """Shadow (on light text) or glow (on dark text) built from the layer's own alpha: no boxes."""
    a = layer.getchannel("A")
    blur = a.filter(ImageFilter.GaussianBlur(p(16)))
    tight = a.filter(ImageFilter.GaussianBlur(p(4)))
    glow_rgb = mix_rgb(PAL["cream"], (12, 16, 28), dark_k)
    op = (0.55 + 0.25 * dark_k) * strength
    sh = Image.new("RGBA", layer.size, glow_rgb + (0,))
    sh.putalpha(Image.blend(blur, tight, 0.35).point(lambda v: round(min(255, v * 1.6) * op)))
    return sh


# ------------------------------------------------------------------ assets
LOGO = Image.open(C / "brand_renders" / "TH_full_logo_2400.png").convert("RGBA")
SYMBOL = Image.open(C / "brand_renders" / "TH_symbol_800.png").convert("RGBA")
SYMBOL = SYMBOL.crop(SYMBOL.getbbox())


def scaled(im, width):
    w = max(1, round(width * S))
    return im.resize((w, round(im.height * w / im.width)), Image.LANCZOS)


SYMBOL_BUG = scaled(SYMBOL, 104)
LOGO_CARD = scaled(LOGO, 780)


def alpha_paste(dst, src, xy, opacity):
    if opacity <= 0:
        return
    s = src.copy()
    s.putalpha(s.getchannel("A").point(lambda a: round(a * opacity)))
    dst.alpha_composite(s, (round(xy[0]), round(xy[1])))


_measure = ImageDraw.Draw(Image.new("L", (8, 8)))


def tlen(s, f):
    return _measure.textlength(s, font=f)


def balanced_lines(text, f, maxw):
    """One line if it fits, otherwise the most balanced two-line split (no orphans)."""
    if tlen(text, f) <= maxw:
        return [text.split()]
    words = text.split()
    splits = [(words[:i], words[i:]) for i in range(1, len(words))]
    ok = [sp for sp in splits if max(tlen(" ".join(x), f) for x in sp) <= maxw] or splits
    return list(min(ok, key=lambda sp: max(tlen(" ".join(x), f) for x in sp)))


# ------------------------------------------------------------------ kinetic type
def kinetic(lines, f, x, y, lh, t, t_in, t_out, colour, accent_colour, accent_words=(), align="left",
            stagger=0.07, dur=0.5, underline=True):
    """Words rise into place from behind a line mask (slight overshoot), then the
    accent words get a wiping underline; on exit the words lift out of the mask.
    Returns an RGBA layer (text only, no background)."""
    layer = Image.new("RGBA", (W, H), (0, 0, 0, 0))
    space = tlen(" ", f)
    asc, desc = f.getmetrics()
    n_total = sum(len(l) for l in lines)
    acc = set(accent_words)
    idx = 0
    spans = []
    for li, words in enumerate(lines):
        width = tlen(" ".join(words), f)
        lx = x if align == "left" else x - width / 2
        ly = y + li * lh
        clip_top, clip_bot = ly - p(6), ly + asc + desc + p(4)
        cx = lx
        for wd in words:
            ww = tlen(wd, f)
            k_in = ease_out_back((t - t_in - idx * stagger) / dur)
            k_out = ease_in((t - t_out - (n_total - 1 - idx) * 0.025) / 0.32)
            dy = (1 - k_in) * (asc + desc) * 1.05 - k_out * (asc + desc) * 1.05
            alpha = clamp((t - t_in - idx * stagger) / 0.12) * (1 - k_out)
            if alpha > 0:
                is_acc = wd in acc
                col = accent_colour if is_acc else colour
                wimg = Image.new("RGBA", (round(ww) + p(20), asc + desc + p(20)), (0, 0, 0, 0))
                ImageDraw.Draw(wimg).text((p(4), p(4)), wd, font=f, fill=col + (round(255 * alpha),))
                wy = ly + dy - p(4)
                top = max(0, round(clip_top - wy))
                bot = min(wimg.height, round(clip_bot - wy))
                if bot > top:
                    layer.alpha_composite(wimg.crop((0, top, wimg.width, bot)), (round(cx - p(4)), round(wy + top)))
            if wd in acc:
                spans.append((li, cx, cx + ww, ly + asc + p(10)))
            cx += ww + space
            idx += 1
    if underline and spans:
        d = ImageDraw.Draw(layer)
        u_in = ease_out((t - t_in - n_total * stagger - 0.15) / 0.45)
        u_out = ease_in((t - t_out) / 0.3)
        by_line = {}
        for li, a, b, yy in spans:
            s0 = by_line.setdefault(li, [a, b, yy])
            s0[0], s0[1] = min(s0[0], a), max(s0[1], b)
        for a, b, yy in by_line.values():
            if u_in > 0 and u_out < 1:
                ax = a + (b - a) * u_out
                bx = a + (b - a) * u_in
                if bx > ax:
                    d.rectangle((ax, yy, bx, yy + p(9)), fill=accent_colour + (255,))
    return layer


def headline(img, t, ff):
    for h in TL["headlines"]:
        if not (h["in"] <= t < h["out"] + 0.6):
            continue
        k = HEAD_TONE.update(img, (X0, HEAD_Y - 20, X1, HEAD_Y + 260), ff)
        f = font("Jost-Bold.ttf", h.get("size", 108))
        lines = balanced_lines(h["text"], f, p(X1 - X0))
        colour = mix_rgb(PAL["navy"], PAL["cream"], k)
        accent = mix_rgb(PAL["terracotta"], ACCENT_ON_DARK, k)
        layer = kinetic(lines, f, p(X0), p(HEAD_Y), p(h.get("size", 108) * 1.09), t, h["in"], h["out"], colour, accent,
                        h.get("accent", "").split())
        img.alpha_composite(soft_shadow(layer, k))
        img.alpha_composite(layer)


def captions(img, t):
    f = F_CAP()
    for c in TL["captions"]:
        if not (c["in"] <= t < c["out"]):
            continue
        layer = Image.new("RGBA", (W, H), (0, 0, 0, 0))
        d = ImageDraw.Draw(layer)
        lh = p(62)
        bottom = p(1490)
        top = bottom - lh * len(c["lines"])
        for i, line in enumerate(c["lines"]):
            k = ease_out((t - c["in"] - i * 0.08) / 0.22)
            a = k * sm((c["out"] - t) / 0.12)
            if a <= 0:
                continue
            tw = d.textlength(line, font=f)
            d.text((p(TEXT_CX) - tw / 2, top + i * lh + (1 - k) * p(18)), line, font=f,
                   fill=(255, 255, 255, round(255 * a)), stroke_width=max(1, p(3)),
                   stroke_fill=PAL["ink"] + (round(235 * a),))
        sh = Image.new("RGBA", (W, H), (0, 0, 0, 0))
        sh.putalpha(layer.getchannel("A").filter(ImageFilter.GaussianBlur(p(8))).point(lambda v: round(v * 0.5)))
        img.alpha_composite(sh, (0, p(3)))
        img.alpha_composite(layer)


def logo_bug(img, t, ff):
    k_in = ease_out_back((t - 0.25) / 0.5, 1.2)
    a = clamp((t - 0.25) / 0.2) * (1 - sm((t - 24.6) / 0.4))
    if a <= 0:
        return
    bx = X1 - 104                                       # top-right corner, inside the safe area
    tone = BUG_TONE.update(img, (bx, 262, X1, 340), ff)
    sc = 0.7 + 0.3 * k_in
    sym = SYMBOL_BUG.resize((max(1, round(SYMBOL_BUG.width * sc)), max(1, round(SYMBOL_BUG.height * sc))), Image.LANCZOS)
    layer = Image.new("RGBA", (W, H), (0, 0, 0, 0))
    alpha_paste(layer, sym, (p(bx) + (SYMBOL_BUG.width - sym.width) / 2, p(268) + (SYMBOL_BUG.height - sym.height) / 2), a)
    # glow only where the background is dark (the navy door); nothing on light plates
    glow = Image.new("RGBA", (W, H), PAL["cream"] + (0,))
    glow.putalpha(layer.getchannel("A").filter(ImageFilter.GaussianBlur(p(10))).point(lambda v: round(min(255, v * 2.2) * 0.85 * tone)))
    img.alpha_composite(glow)
    img.alpha_composite(layer)


def illustrative_label(img, t):
    a = min(sm((t - 10.6) / 0.4), sm((18.8 - t) / 0.4))
    if a <= 0:
        return
    layer = Image.new("RGBA", (W, H), (0, 0, 0, 0))
    ImageDraw.Draw(layer).text((p(X0), p(1300)), "RENDERING 3D ILLUSTRATIVO", font=F_LABEL(),
                               fill=PAL["ink-mid"] + (round(200 * a),))
    img.alpha_composite(soft_shadow(layer, 0.0, 0.8))
    img.alpha_composite(layer)


def end_card(ff):
    t = ff / FPS
    ec = TL["end_card"]
    img = Image.new("RGBA", (W, H), PAL["cream"] + (255,))
    # soft warm vignette for depth
    g = Image.new("L", (W, H), 0)
    ImageDraw.Draw(g).ellipse((-W * 0.3, H * 0.05, W * 1.3, H * 0.95), fill=255)
    g = g.filter(ImageFilter.GaussianBlur(p(220)))
    warm = Image.new("RGBA", (W, H), PAL["bg-warm"] + (255,))
    img = Image.composite(img, warm, g)
    # logo: fade + gentle settle (uniform scale only, proportions preserved)
    k = ease_out((t - ec["logo_in"]) / 0.9)
    if k > 0:
        sc = 1.06 - 0.06 * k
        lg = LOGO_CARD.resize((round(LOGO_CARD.width * sc), round(LOGO_CARD.height * sc)), Image.LANCZOS)
        alpha_paste(img, lg, (p(TEXT_CX) - lg.width / 2, p(560) - lg.height / 2), k)
    never = 1e9
    f_tag = font("Jost-Bold.ttf", 64)
    tag_lines = [l.split() for l in ec["tagline"]]
    img.alpha_composite(kinetic(tag_lines, f_tag, p(TEXT_CX), p(830), p(80), t, ec["tagline_in"], never,
                                PAL["navy"], PAL["terracotta"], ec.get("tagline_accent", "").split(),
                                align="center", stagger=0.06, underline=False))
    lead, url = ec["cta"].rsplit(" ", 1)
    img.alpha_composite(kinetic([lead.split()], font("DMSans-Medium.ttf", 46), p(TEXT_CX), p(1072), p(60), t,
                                ec["cta_in"], never, PAL["ink"], PAL["ink"], align="center", stagger=0.05,
                                underline=False))
    # URL: bold, rises in, underline wipes, then one soft "pop" to draw the eye
    f_url = font("Jost-ExtraBold.ttf", 92)
    url_layer = kinetic([[url]], f_url, p(TEXT_CX), p(1140), p(100), t, ec["cta_in"] + 0.3, never,
                        PAL["terracotta"], PAL["terracotta"], [url], align="center", dur=0.55)
    pop = np.sin(np.pi * clamp((t - (ec["cta_in"] + 1.4)) / 0.5)) * 0.05
    if pop > 0.001:
        bb = url_layer.getbbox()
        if bb:
            crop = url_layer.crop(bb)
            big = crop.resize((round(crop.width * (1 + pop)), round(crop.height * (1 + pop))), Image.LANCZOS)
            url_layer = Image.new("RGBA", (W, H), (0, 0, 0, 0))
            url_layer.alpha_composite(big, (round((bb[0] + bb[2]) / 2 - big.width / 2), round((bb[1] + bb[3]) / 2 - big.height / 2)))
    img.alpha_composite(url_layer)
    return img.convert("RGB")


def guides(img):
    layer = Image.new("RGBA", (W, H), (0, 0, 0, 0))
    d = ImageDraw.Draw(layer)
    red = (220, 0, 0, 70)
    d.rectangle((0, 0, W, p(SAFE["top"])), fill=red)
    d.rectangle((0, H - p(SAFE["bottom"]), W, H), fill=red)
    d.rectangle((W - p(SAFE["right"]), p(SAFE["top"]), W, H - p(SAFE["bottom"])), fill=red)
    d.rectangle((p(SAFE["left"]) - 1, 0, p(SAFE["left"]), H), fill=(220, 0, 0, 160))
    img.alpha_composite(layer)


def frame(ff):
    t = ff / FPS
    img = base_frame(ff).convert("RGBA")
    if t < 25.0:
        logo_bug(img, t, ff)
    headline(img, t, ff)
    illustrative_label(img, t)
    captions(img, t)
    if A.safe_guides:
        guides(img)
    return img.convert("RGB")


def main():
    out_dir = C / ("preview" if A.tier == "preview" else "final")
    out_dir.mkdir(exist_ok=True)
    if A.stills:
        sd = C / "stills" / A.tier
        sd.mkdir(parents=True, exist_ok=True)
        for f in [int(x) for x in A.stills.split(",")]:
            frame(f).save(sd / f"comp_{f:04d}{'_guides' if A.safe_guides else ''}.png")
        print("stills ->", sd)
        return
    name = "TH_Padova_Lavoro_PREVIEW_540x960.mp4" if A.tier == "preview" else "TH_Padova_Lavoro_1080x1920.mp4"
    out = out_dir / name
    mix = C / "audio" / "mix" / "TH_padova_mix.wav"
    cmd = ["ffmpeg", "-v", "error", "-y", "-f", "rawvideo", "-pix_fmt", "rgb24", "-s", f"{W}x{H}", "-r", str(FPS), "-i", "-"]
    if not A.no_audio:
        cmd += ["-i", str(mix)]
    vq = ["-crf", "23", "-preset", "medium"] if A.tier == "preview" else ["-crf", "17", "-preset", "slow", "-profile:v", "high"]
    cmd += ["-c:v", "libx264", *vq, "-pix_fmt", "yuv420p", "-color_primaries", "bt709", "-color_trc", "bt709",
            "-colorspace", "bt709", "-movflags", "+faststart"]
    if not A.no_audio:
        cmd += ["-c:a", "aac", "-b:a", "192k" if A.tier == "preview" else "256k", "-ar", "48000", "-shortest"]
    cmd += [str(out)]
    proc = subprocess.Popen(cmd, stdin=subprocess.PIPE)
    for ff in range(NFRAMES):
        proc.stdin.write(frame(ff).tobytes())
    proc.stdin.close()
    assert proc.wait() == 0
    print(out)


if __name__ == "__main__":
    main()
