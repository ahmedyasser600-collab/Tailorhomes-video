"""Composite the Tailor Homes reel: 3D renders + transitions + headlines, logo,
captions, end card, then encode with the audio mix.

    python composite.py --tier preview          # 540x960 review MP4
    python composite.py --tier final            # 1080x1920 master MP4
    python composite.py --tier preview --stills 15,200,400   # PNG checks only
    python composite.py --tier final --safe-guides --stills 450

Layout is designed in 1080x1920 coordinates and scaled for the preview.
All text is 2D overlay; nothing is baked into the 3D renders.
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
FONTS = REPO / "brand" / "fonts"
TL = json.loads((C / "timeline.json").read_text())
FPS, HANDLE = TL["fps"], TL["handle_frames"]
NFRAMES = int(TL["duration"] * FPS)
PAL = {k: tuple(int(v[i:i + 2], 16) for i in (1, 3, 5)) for k, v in
       json.loads((REPO / "brand" / "palette.json").read_text())["website_colors"].items()}
SAFE = TL["safe_area"]
X0, X1 = SAFE["left"], 1080 - SAFE["right"]          # 60 .. 930
TEXT_CX = 520                                         # optical centre inside the safe area

ap = argparse.ArgumentParser()
ap.add_argument("--tier", default="preview", choices=["preview", "final"])
ap.add_argument("--stills", default="")
ap.add_argument("--safe-guides", action="store_true")
ap.add_argument("--no-audio", action="store_true")
A = ap.parse_args()
S = 0.5 if A.tier == "preview" else 1.0
W, H = round(1080 * S), round(1920 * S)


def font(name, size):
    return ImageFont.truetype(str(FONTS / name), max(8, round(size * S)))


F_HEAD = lambda: font("CormorantGaramond-Regular.ttf", 92)
F_CAP = lambda: font("DMSans-Regular.ttf", 44)
F_LABEL = lambda: font("Jost-Regular.ttf", 24)


def sm(x):
    x = min(1.0, max(0.0, x))
    return x * x * (3 - 2 * x)


def ease_out(x):
    x = min(1.0, max(0.0, x))
    return 1 - (1 - x) ** 3


def p(v):
    return round(v * S)


# ------------------------------------------------------------------ 3D plates
SCENES = [(k, v) for k, v in TL["scenes"].items()]
_cache = {}


def plate(scene, film_frame):
    info = TL["scenes"][scene]
    local = film_frame - round(info["start"] * FPS) + HANDLE + 1
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


# ------------------------------------------------------------------ assets
LOGO = Image.open(C / "brand_renders" / "TH_full_logo_2400.png").convert("RGBA")
SYMBOL = Image.open(C / "brand_renders" / "TH_symbol_800.png").convert("RGBA")
SYMBOL = SYMBOL.crop(SYMBOL.getbbox())


def scaled(im, width):
    w = max(1, round(width * S))
    return im.resize((w, round(im.height * w / im.width)), Image.LANCZOS)


SYMBOL_BUG = scaled(SYMBOL, 96)
LOGO_CARD = scaled(LOGO, 780)


def alpha_paste(dst, src, xy, opacity):
    if opacity <= 0:
        return
    s = src.copy()
    s.putalpha(s.getchannel("A").point(lambda a: round(a * opacity)))
    dst.alpha_composite(s, (round(xy[0]), round(xy[1])))


def wrap(text, f, maxw, draw):
    """Greedy wrap; a two-line result is re-split to balance the lines (no orphans)."""
    lines = _greedy(text, f, maxw, draw)
    if len(lines) == 2:
        words = text.split()
        splits = [(" ".join(words[:i]), " ".join(words[i:])) for i in range(1, len(words))]
        fits = [sp for sp in splits if max(draw.textlength(x, font=f) for x in sp) <= maxw]
        if fits:
            lines = list(min(fits, key=lambda sp: max(draw.textlength(x, font=f) for x in sp)))
    return lines


def _greedy(text, f, maxw, draw):
    words, lines, cur = text.split(), [], ""
    for w_ in words:
        trial = (cur + " " + w_).strip()
        if draw.textlength(trial, font=f) <= maxw or not cur:
            cur = trial
        else:
            lines.append(cur)
            cur = w_
    return lines + [cur]


# ------------------------------------------------------------------ overlays
def headline(img, t):
    for h in TL["headlines"]:
        if not (h["in"] <= t < h["out"] + 0.3):
            continue
        layer = Image.new("RGBA", (W, H), (0, 0, 0, 0))
        d = ImageDraw.Draw(layer)
        f = F_HEAD()
        lines = wrap(h["text"], f, p(X1 - X0 - 84), d)
        lh = p(100)
        pad = p(34)
        box_w = max(d.textlength(l, font=f) for l in lines) + 2 * pad + p(10)
        box_h = lh * len(lines) + 2 * pad - p(6)
        k_in = ease_out((t - h["in"]) / 0.55)
        k_out = sm((t - h["out"]) / 0.3)
        x = p(X0) - (1 - k_in) * (box_w + p(X0)) * 0.35
        y = p(372)
        a = k_in * (1 - k_out)
        d.rounded_rectangle((x, y, x + box_w, y + box_h), radius=p(10), fill=PAL["cream"] + (round(236 * a),))
        d.rectangle((x, y + p(18), x + p(6), y + box_h - p(18)), fill=PAL["terracotta"] + (round(255 * a),))
        for i, line in enumerate(lines):
            li = ease_out((t - h["in"] - 0.12 - i * 0.1) / 0.5) * (1 - k_out)
            d.text((x + pad + p(10), y + pad - p(14) + i * lh + (1 - li) * p(22)), line, font=f,
                   fill=PAL["navy"] + (round(255 * li),))
        img.alpha_composite(layer)


def captions(img, t):
    for c in TL["captions"]:
        if not (c["in"] <= t < c["out"]):
            continue
        a = min(sm((t - c["in"]) / 0.12), sm((c["out"] - t) / 0.12))
        layer = Image.new("RGBA", (W, H), (0, 0, 0, 0))
        d = ImageDraw.Draw(layer)
        f = F_CAP()
        lh = p(58)
        bottom = p(1496)
        top = bottom - lh * len(c["lines"]) - p(20)
        for i, line in enumerate(c["lines"]):
            tw = d.textlength(line, font=f)
            x = p(TEXT_CX) - tw / 2
            y = top + p(10) + i * lh
            d.rounded_rectangle((x - p(18), y - p(4), x + tw + p(18), y + lh - p(2)), radius=p(12),
                                fill=(255, 255, 255, round(222 * a)))
            d.text((x, y + p(2)), line, font=f, fill=PAL["ink"] + (round(255 * a),))
        img.alpha_composite(layer)


def logo_bug(img, t):
    a = sm((t - 0.3) / 0.4) * (1 - sm((t - 24.6) / 0.4))
    if a <= 0:
        return
    # small cream badge so the red monogram reads over dark (door) and light plates alike
    layer = Image.new("RGBA", (W, H), (0, 0, 0, 0))
    pad = p(12)
    x, y = p(X0), p(262)
    ImageDraw.Draw(layer).rounded_rectangle((x, y, x + SYMBOL_BUG.width + 2 * pad, y + SYMBOL_BUG.height + 2 * pad),
                                            radius=p(8), fill=PAL["cream"] + (round(225 * a),))
    img.alpha_composite(layer)
    alpha_paste(img, SYMBOL_BUG, (x + pad, y + pad), a)


def illustrative_label(img, t):
    a = min(sm((t - 10.6) / 0.4), sm((18.8 - t) / 0.4))
    if a <= 0:
        return
    d = ImageDraw.Draw(img)
    d.text((p(X0), p(1300)), "RENDERING 3D ILLUSTRATIVO", font=F_LABEL(), fill=PAL["ink-mid"] + (round(170 * a),))


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
    d = ImageDraw.Draw(img)
    # logo: fade + gentle settle (uniform scale only, proportions preserved)
    k = ease_out((t - ec["logo_in"]) / 0.9)
    if k > 0:
        sc = 1.04 - 0.04 * k
        lg = LOGO_CARD.resize((round(LOGO_CARD.width * sc), round(LOGO_CARD.height * sc)), Image.LANCZOS)
        alpha_paste(img, lg, (p(TEXT_CX) - lg.width / 2, p(580) - lg.height / 2), k)
    f_tag = font("CormorantGaramond-Regular.ttf", 66)
    for i, line in enumerate(ec["tagline"]):
        k = ease_out((t - ec["tagline_in"] - i * 0.18) / 0.6)
        tw = d.textlength(line, font=f_tag)
        d.text((p(TEXT_CX) - tw / 2, p(850) + i * p(80) + (1 - k) * p(18)), line, font=f_tag,
               fill=PAL["navy"] + (round(255 * k),))
    k = ease_out((t - ec["cta_in"]) / 0.6)
    lead, url = ec["cta"].rsplit(" ", 1)
    f_lead, f_url = font("DMSans-Regular.ttf", 46), font("Jost-Regular.ttf", 76)
    tw = d.textlength(lead, font=f_lead)
    d.text((p(TEXT_CX) - tw / 2, p(1090) + (1 - k) * p(16)), lead, font=f_lead, fill=PAL["ink"] + (round(255 * k),))
    tw = d.textlength(url, font=f_url)
    d.text((p(TEXT_CX) - tw / 2, p(1160) + (1 - k) * p(16)), url, font=f_url, fill=PAL["terracotta"] + (round(255 * k),))
    u = ease_out((t - ec["cta_in"] - 0.35) / 0.7)
    d.rectangle((p(TEXT_CX) - tw / 2, p(1262), p(TEXT_CX) - tw / 2 + tw * u, p(1266)), fill=PAL["terracotta"] + (255,))
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
        logo_bug(img, t)
    headline(img, t)
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
