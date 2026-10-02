"""Film 02 - Tailor Homes Students & Erasmus. Photographic motion-design renderer.

    python scripts/render.py --mode animatic            # 540x960, 30 fps, with audio -> out/
    python scripts/render.py --mode final               # 1080x1920, 60 fps, CRF 16
    python scripts/render.py --mode final --stills hero # one still per scene -> out/stills/
    python scripts/render.py --mode final --stills 3.2,18.9 --guides

All timing comes from ../timing.json (scripts/analyze_vo.py measures the voice).
Photography: real Tailor Homes photos in ../source/selected (crop/scale only).
Layout is designed in 1080x1920 coordinates.
"""
import argparse
import json
import math
import subprocess
from pathlib import Path

import numpy as np
from PIL import Image, ImageChops, ImageDraw, ImageFilter, ImageFont

C = Path(__file__).resolve().parents[1]
REPO = C.parents[1]
TM = json.loads((C / "timing.json").read_text())
SC = TM["scenes"]
PH = TM["phrases"]
EM = TM["emphasis"]

ap = argparse.ArgumentParser()
ap.add_argument("--mode", choices=["animatic", "final"], default="animatic")
ap.add_argument("--stills", default="")
ap.add_argument("--guides", action="store_true")
ap.add_argument("--no-audio", action="store_true")
A = ap.parse_args()
S = 0.5 if A.mode == "animatic" else 1.0
FPS = 30 if A.mode == "animatic" else 60
W, H = round(1080 * S), round(1920 * S)
DUR = TM["film_duration"]

NAVY = (31, 42, 72)
CREAM = (245, 240, 232)
SALMON = (233, 162, 139)
CORAL = (192, 83, 63)
INK = (26, 25, 22)
SAFE_L, SAFE_R, SAFE_T, SAFE_B = 60, 930, 250, 1500
CX = 495                      # optical centre of the safe area


def p(v):
    return v * S


def P(k):
    return PH[k]["start"]


# ------------------------------------------------------------------ easing
def clamp(x, a=0.0, b=1.0):
    return max(a, min(b, x))


def ease_io(x):
    x = clamp(x)
    return 4 * x ** 3 if x < 0.5 else 1 - (-2 * x + 2) ** 3 / 2


def ease_out(x):
    x = clamp(x)
    return 1 - (1 - x) ** 3


def expo_out(x):
    x = clamp(x)
    return 1 if x >= 1 else 1 - 2 ** (-10 * x)


def expo_io(x):
    x = clamp(x)
    if x in (0, 1):
        return x
    return 2 ** (20 * x - 10) / 2 if x < 0.5 else (2 - 2 ** (-20 * x + 10)) / 2


def prog(t, a, d):
    return clamp((t - a) / d)


def lerp(a, b, k):
    return a + (b - a) * k


# ------------------------------------------------------------------ fonts & type
_fc = {}


def font(name, size):
    k = (name, round(size * S))
    if k not in _fc:
        _fc[k] = ImageFont.truetype(str(C / "fonts" / name), max(6, round(size * S)))
    return _fc[k]


SERIF = "CormorantGaramond-MediumItalic.ttf"
SERIF_B = "CormorantGaramond-SemiBoldItalic.ttf"
LABEL = "Jost-SemiBold.ttf"
SANS = "DMSans-Medium.ttf"
SANS_B = "DMSans-Bold.ttf"
_md = ImageDraw.Draw(Image.new("L", (4, 4)))


def tlen(s, f):
    return _md.textlength(s, font=f)


# Cormorant Garamond Italic v4.001 draws the grave accents (à è ì ò ù) almost vertical, so "Più"
# reads like "Piu'". itext() draws those letters as base letter + the font's own acute accent,
# mirrored into a grave. Other text goes straight to ImageDraw.text.
GRAVE = {"à": "a", "è": "e", "ì": "ı", "ò": "o", "ù": "u"}
_acc = {}


def _grave(f):
    if f not in _acc:
        size = (f.size * 2, f.size * 2)
        a, b = Image.new("L", size, 0), Image.new("L", size, 0)
        ImageDraw.Draw(a).text((f.size // 2, 0), "é", font=f, fill=255)
        ImageDraw.Draw(b).text((f.size // 2, 0), "e", font=f, fill=255)
        acc = ImageChops.subtract(a, b)
        bx, be = acc.getbbox(), b.getbbox()
        mark = acc.crop(bx).transpose(Image.FLIP_LEFT_RIGHT)
        # accent centre relative to the base letter's ink centre (x) and to the drawing origin (y)
        _acc[f] = (mark, (bx[0] + bx[2]) / 2 - (be[0] + be[2]) / 2, bx[1])
    return _acc[f]


def itext(d, xy, s, font, fill):
    if not any(c in GRAVE for c in s):
        d.text(xy, s, font=font, fill=fill)
        return
    mark, dx, top = _grave(font)
    x, y = xy
    for c in s:
        base = GRAVE.get(c, c)
        d.text((x, y), base, font=font, fill=fill)
        if c in GRAVE:
            ib = font.getbbox(base)
            cx = x + (ib[0] + ib[2]) / 2 + dx * 0.15 - mark.width * 0.55   # centred over the bowl
            d.bitmap((round(cx - mark.width / 2), round(y + top)), mark, fill=fill)
        x += tlen(base, font)


def tracked(img, xy, text, f, fill, track_em=0.28, alpha=1.0, align="left"):
    """Uppercase label with letter-spacing."""
    sp = f.size * track_em
    width = sum(tlen(ch, f) + sp for ch in text) - sp
    x, y = xy
    if align == "center":
        x -= width / 2
    layer = Image.new("RGBA", img.size, (0, 0, 0, 0))
    d = ImageDraw.Draw(layer)
    for ch in text:
        d.text((x, y), ch, font=f, fill=fill + (round(255 * alpha),))
        x += tlen(ch, f) + sp
    img.alpha_composite(layer)
    return width


def reveal_lines(img, lines, f, x, y, lh, t, t_in, colour, t_out=None, align="left", stagger=0.09,
                 dur=0.55, shadow=None):
    """Editorial reveal: words slide up from behind each line's mask; exit lifts them out."""
    layer = Image.new("RGBA", img.size, (0, 0, 0, 0))
    asc, desc = f.getmetrics()
    sp = tlen(" ", f)
    idx = 0
    nwords = sum(len(l.split()) for l in lines)
    for li, line in enumerate(lines):
        words = line.split()
        width = tlen(line, f)
        lx = x if align == "left" else x - width / 2
        ly = y + li * lh
        top, bot = ly - p(4), ly + asc + desc + p(6)
        cx = lx
        for w in words:
            ww = tlen(w, f)
            k = expo_out((t - t_in - idx * stagger) / dur)
            ko = 0.0 if t_out is None else ease_io((t - t_out - (nwords - idx) * 0.03) / 0.4)
            dy = (1 - k) * (asc + desc) - ko * (asc + desc)
            a = clamp((t - t_in - idx * stagger) / 0.18) * (1 - ko)
            if a > 0:
                wi = Image.new("RGBA", (int(ww + p(30)), int(asc + desc + p(30))), (0, 0, 0, 0))
                itext(ImageDraw.Draw(wi), (p(8), p(6)), w, f, colour + (round(255 * a),))
                wy = ly + dy - p(6)
                c0, c1 = max(0, round(top - wy)), min(wi.height, round(bot - wy))
                if c1 > c0:
                    layer.alpha_composite(wi.crop((0, c0, wi.width, c1)), (round(cx - p(8)), round(wy + c0)))
            cx += ww + sp
            idx += 1
    if shadow:
        sh = Image.new("RGBA", img.size, shadow + (0,))
        sh.putalpha(layer.getchannel("A").filter(ImageFilter.GaussianBlur(p(14))).point(lambda v: round(v * 0.55)))
        img.alpha_composite(sh)
    img.alpha_composite(layer)


# ------------------------------------------------------------------ photography
PHOTO = {}
FOCUS = {  # normalised focal point used for every crop (subject stays in frame)
    "SE1": (0.40, 0.62), "SE2": (0.55, 0.5), "SE3": (0.5, 0.55), "SE4": (0.46, 0.55), "SE5": (0.62, 0.5),
    "VN1": (0.46, 0.55), "VN2": (0.5, 0.55), "VN3": (0.36, 0.55), "VN4": (0.6, 0.55), "VN5": (0.5, 0.6),
    "VR1": (0.6, 0.55), "VR2": (0.45, 0.5), "CD1": (0.5, 0.55), "CD2": (0.62, 0.55),
}


def photo(pid):
    if pid not in PHOTO:
        im = Image.open(C / "source" / "selected" / f"{pid}.jpg").convert("RGB")
        if S < 1:
            im = im.resize((round(im.width * 0.6), round(im.height * 0.6)), Image.LANCZOS)
        PHOTO[pid] = im
    return PHOTO[pid]


def view(pid, w, h, zoom=1.0, dx=0.0, dy=0.0):
    """Cover-crop of photo `pid` into w x h around its focal point. zoom>1 pushes in;
    dx/dy pan in fractions of the output size. Crop/scale only."""
    im = photo(pid)
    w, h = max(1, round(w)), max(1, round(h))
    base = max(w / im.width, h / im.height) * zoom
    cw, ch = w / base, h / base
    fx, fy = FOCUS[pid]
    cx = clamp(fx * im.width + dx * cw, cw / 2, im.width - cw / 2)
    cy = clamp(fy * im.height + dy * ch, ch / 2, im.height - ch / 2)
    box = (cx - cw / 2, cy - ch / 2, cx + cw / 2, cy + ch / 2)
    return im.resize((w, h), Image.BICUBIC, box=box, reducing_gap=2.0)


def panel(img, pid, rect, zoom=1.0, dx=0.0, dy=0.0, radius=10, alpha=1.0):
    x0, y0, x1, y1 = rect
    w, h = x1 - x0, y1 - y0
    if w < 2 or h < 2 or alpha <= 0:
        return
    v = view(pid, w, h, zoom, dx, dy).convert("RGBA")
    m = Image.new("L", v.size, 0)
    ImageDraw.Draw(m).rounded_rectangle((0, 0, v.width - 1, v.height - 1), radius=p(radius), fill=round(255 * alpha))
    img.paste(v, (round(x0), round(y0)), m)


def full(img, pid, zoom=1.0, dx=0.0, dy=0.0):
    img.paste(view(pid, W, H, zoom, dx, dy), (0, 0))


def scrim(img, y0, y1, colour=NAVY, a0=0.0, a1=0.6):
    g = np.linspace(a0, a1, max(1, round(p(y1 - y0))))
    alpha = Image.fromarray((np.repeat(g[:, None], W, 1) * 255).astype(np.uint8), "L")
    layer = Image.new("RGBA", (W, alpha.height), colour + (0,))
    layer.putalpha(alpha)
    img.alpha_composite(layer, (0, round(p(y0))))


def R(*v):
    return tuple(p(x) for x in v)


# ------------------------------------------------------------------ brand assets
LOGO = Image.open(REPO / "campaigns" / "TH_padova_lavoro" / "brand_renders" / "TH_full_logo_2400.png").convert("RGBA")


def logo(img, cx, cy, width, alpha):
    if alpha <= 0:
        return
    w = round(p(width))
    lg = LOGO.resize((w, round(LOGO.height * w / LOGO.width)), Image.LANCZOS)
    if alpha < 1:
        lg.putalpha(lg.getchannel("A").point(lambda v: round(v * alpha)))
    img.alpha_composite(lg, (round(p(cx) - lg.width / 2), round(p(cy) - lg.height / 2)))


def pill(img, cx, cy, w, h, fill, text, f, tcol, scale=1.0, alpha=1.0, track=None):
    if alpha <= 0 or scale <= 0:
        return
    w, h = w * scale, h * scale
    layer = Image.new("RGBA", img.size, (0, 0, 0, 0))
    d = ImageDraw.Draw(layer)
    d.rounded_rectangle(R(cx - w / 2, cy - h / 2, cx + w / 2, cy + h / 2), radius=p(h / 2), fill=fill + (round(255 * alpha),))
    img.alpha_composite(layer)
    if track is None:
        tw = tlen(text, f)
        asc, desc = f.getmetrics()
        tl = Image.new("RGBA", img.size, (0, 0, 0, 0))
        ImageDraw.Draw(tl).text((p(cx) - tw / 2, p(cy) - (asc + desc) / 2 - p(2)), text, font=f,
                                fill=tcol + (round(255 * alpha),))
        img.alpha_composite(tl)
    else:
        asc, desc = f.getmetrics()
        tracked(img, (p(cx), p(cy) - (asc + desc) / 2), text, f, tcol, track, alpha, "center")


def tag(img, x0, y_bottom, text, alpha):
    """Small navy tag with a tracked cream label, sitting on a photo's bottom-left corner."""
    if alpha <= 0:
        return
    f = font(LABEL, 30)
    tw = tracked(Image.new("RGBA", (4, 4)), (0, 0), text, f, CREAM, 0.26, 0)
    pad = p(16)
    lay = Image.new("RGBA", img.size, (0, 0, 0, 0))
    ImageDraw.Draw(lay).rectangle((p(x0), p(y_bottom) - p(66), p(x0) + tw + 2 * pad + p(6), p(y_bottom)),
                                  fill=NAVY + (round(240 * alpha),))
    img.alpha_composite(lay)
    tracked(img, (p(x0) + pad, p(y_bottom) - p(52)), text, f, CREAM, 0.26, alpha)


# ------------------------------------------------------------------ scenes
def s_open(img, t):
    """Navy identity + italic question; a real photo panel opens, then expands to full."""
    a0, a1 = SC["open"]
    img.paste(Image.new("RGB", (W, H), NAVY), (0, 0))
    out = a1 - 0.85                                   # expansion starts
    kx = expo_io(prog(t, out, 0.85))
    # the photo is never re-cropped, zoomed or panned: a window onto it opens from a line over
    # the stairs and armchairs, then window and photo rise together in one move to the full frame
    ko = expo_out(prog(t, 1.25, 1.0))
    ph = round(p(BAND))
    still = view("SE1", W, ph, 1.05)                 # identical to variety1's first frame
    cy, hh, off = 990, 260, 430 * (1 - kx)           # off: photo top while the window is small
    x0, y0 = lerp(90, 0, kx), lerp(cy - hh * ko, 0, kx)
    x1, y1 = lerp(990, 1080, kx), lerp(cy + hh * ko, BAND, kx)
    if ko > 0 and y1 - y0 > 2:
        m = Image.new("L", (W, ph), 0)
        ImageDraw.Draw(m).rounded_rectangle(R(x0, y0 - off, x1, y1 - off), radius=p(lerp(10, 0, kx)), fill=255)
        img.paste(still, (0, round(p(off))), m)
    # type
    lab_a = ease_out(prog(t, 0.25, 0.5)) * (1 - ease_io(prog(t, out - 0.1, 0.35)))
    tracked(img, (p(92), p(318) + (1 - ease_out(prog(t, 0.25, 0.5))) * p(14)), "UNIPD · ERASMUS",
            font(LABEL, 34), SALMON, 0.32, lab_a)
    rl = p(lerp(0, 170, ease_out(prog(t, 0.35, 0.7)))) * (1 - kx)
    if rl > 1:
        ImageDraw.Draw(img).rectangle((p(92), p(372), p(92) + rl, p(375)), fill=SALMON)
    reveal_lines(img, ["Coming to", "Padova?"], font(SERIF, 150), p(86), p(400), p(150), t, P(0) + 0.05,
                 CREAM, t_out=out - 0.15)


BAND = 1235                    # photo above, navy editorial band below (variety scenes)


def s_variety(img, t):
    """S. Eufemia, then a wall-edge wipe to Via Nullo; headline set in the navy band below."""
    v1a, v1b = SC["variety1"]
    v2a, v2b = SC["variety2"]
    img.paste(Image.new("RGB", (W, H), NAVY), (0, 0))
    ph = round(p(BAND))
    z1 = 1.05 + 0.05 * prog(t, v1a, v2b - v1a)
    img.paste(view("SE1", W, ph, z1, dx=-0.02 * prog(t, v1a, v2b - v1a)), (0, 0))
    # wall-edge wipe: a vertical edge travels right->left; Via Nullo enters with parallax
    kw = expo_io(prog(t, v2a, 0.8))
    if kw > 0:
        edge = round(W * (1 - kw))
        vn = view("VN1", W, ph, 1.06 - 0.04 * prog(t, v2a, v2b - v2a), dx=0.06 * (1 - kw))
        img.paste(vn.crop((edge, 0, W, ph)), (edge, 0))
        if 0 < kw < 1:
            ImageDraw.Draw(img).rectangle((edge - p(3), 0, edge, ph), fill=CREAM)
    a1 = ease_out(prog(t, P(1) + 0.1, 0.4)) * (1 - ease_io(prog(t, v2a, 0.3)))
    tracked(img, (p(94), p(1278)), "YOUR OWN SPACE", font(LABEL, 28), SALMON, 0.3, a1)
    a2 = ease_out(prog(t, v2a + 0.35, 0.4)) * (1 - ease_io(prog(t, v2b - 0.35, 0.3)))
    tracked(img, (p(94), p(1278)), "FURNISHED", font(LABEL, 28), SALMON, 0.3, a2)
    reveal_lines(img, ["A space that feels", "like yours."], font(SERIF, 94), p(88), p(1318), p(96),
                 t, P(1) + 0.15, CREAM, t_out=v2b - 0.45)


def s_split(img, t):
    """Two apartments at once: S. Eufemia bedroom / Via Nullo study corner, with a navy band."""
    a, b = SC["split"]
    k = expo_out(prog(t, a, 0.75))
    ko = ease_io(prog(t, b - 0.5, 0.56))             # exit: halves part vertically onto cream
    img.paste(Image.new("RGB", (W, H), NAVY if ko <= 0 else CREAM), (0, 0))
    if 0 < ko < 1:   # the navy band thins out as the halves part
        bh = 320 * (1 - ko)
        ImageDraw.Draw(img).rectangle(R(0, 960 - bh / 2, 1080, 960 + bh / 2), fill=NAVY)
    drift = prog(t, a, b - a)
    top = (lerp(-1080, 0, k), -lerp(0, 820, ko), lerp(0, 1080, k), 800 - lerp(0, 820, ko))
    bot = (lerp(1080, 0, k), 1120 + lerp(0, 820, ko), lerp(2160, 1080, k), 1920 + lerp(0, 820, ko))
    panel(img, "SE4", R(*top), zoom=1.06, dx=0.03 - 0.06 * drift, radius=0)
    panel(img, "VN3", R(*bot), zoom=1.06, dx=-0.03 + 0.06 * drift, radius=0)
    # labels on each half
    la = ease_out(prog(t, a + 0.7, 0.4)) * (1 - ko)
    tag(img, 60 - lerp(0, 1100, ko), 780 - lerp(0, 820, ko), "A ROOM TO REST", la)
    tag(img, 60, 1186 + lerp(0, 820, ko), "A DESK TO STUDY", la)
    reveal_lines(img, ["Furnished apartments", "across Padova."], font(SERIF, 88), p(CX), p(850), p(96), t,
                 P(2) + 0.1, CREAM, t_out=b - 0.55, align="center")


def s_benefits(img, t):
    """Cream editorial: three real rooms as tiles + visible-fact labels."""
    a, b = SC["benefits"]
    img.paste(Image.new("RGB", (W, H), CREAM), (0, 0))
    tiles = [("VR1", (60, 270, 930, 880), "FURNISHED"), ("VN4", (60, 900, 485, 1330), "KITCHEN"),
             ("CD1", (505, 900, 930, 1330), "READING CORNER")]
    for i, (pid, (x0, y0, x1, y1), lab) in enumerate(tiles):
        kt = expo_out(prog(t, a + 0.02 + i * 0.1, 0.6))
        if kt <= 0:
            continue
        hh = (y1 - y0) * kt
        rect = (x0, y0 + (1 - kt) * 40, x1, y0 + (1 - kt) * 40 + hh)
        panel(img, pid, R(*rect), zoom=1.08 - 0.04 * prog(t, a, b - a), radius=8)
        la = ease_out(prog(t, a + 0.55 + i * 0.12, 0.35))
        tag(img, x0, y1, lab, la)
    reveal_lines(img, ["Ready for your stay."], font(SERIF, 84), p(62), p(1370), p(90), t, P(3) - 0.05, NAVY)


def s_discount(img, t):
    """The strongest graphic moment: navy, label, coral -15% pill landing on 'fifteen percent'."""
    a, b = SC["discount"]
    img.paste(Image.new("RGB", (W, H), NAVY), (0, 0))
    hit = EM["fifteen percent"] - 0.12
    la = ease_out(prog(t, a + 0.15, 0.45))
    tracked(img, (p(92), p(400) + (1 - la) * p(12)), "UNIPD & ERASMUS STUDENTS", font(LABEL, 34), SALMON, 0.28, la)
    rl = p(170) * ease_out(prog(t, a + 0.25, 0.6))
    if rl > 1:
        ImageDraw.Draw(img).rectangle((p(92), p(452), p(92) + rl, p(455)), fill=SALMON)
    reveal_lines(img, ["UniPD or", "Erasmus student?"], font(SERIF, 112), p(86), p(500), p(118), t, P(4) + 0.05,
                 CREAM, t_out=hit - 0.35)
    kp = expo_out(prog(t, hit, 0.5))
    if kp > 0:
        pill(img, CX, 860, 760, 330, CORAL, "−15%", font(SANS_B, 210), CREAM, scale=0.88 + 0.12 * kp,
             alpha=clamp(kp * 1.6))
    reveal_lines(img, ["15% off your booking."], font(SERIF, 96), p(CX), p(1100), p(100), t, hit + 0.55, CREAM,
                 align="center")


def card_glyph(img, cx, cy, a):
    lay = Image.new("RGBA", img.size, (0, 0, 0, 0))
    d = ImageDraw.Draw(lay)
    w, h = 250, 160
    d.rounded_rectangle(R(cx - w / 2, cy - h / 2, cx + w / 2, cy + h / 2), radius=p(18), fill=NAVY + (round(255 * a),))
    d.ellipse(R(cx - 95, cy - 45, cx - 45, cy + 5), fill=SALMON + (round(255 * a),))
    for i, ww in enumerate((120, 90, 60)):
        d.rounded_rectangle(R(cx - 25, cy - 40 + i * 30, cx - 25 + ww, cy - 30 + i * 30), radius=p(5),
                            fill=CREAM + (round(200 * a),))
    img.alpha_composite(lay)


def bubble_glyph(img, cx, cy, a, check):
    lay = Image.new("RGBA", img.size, (0, 0, 0, 0))
    d = ImageDraw.Draw(lay)
    r = 95
    d.ellipse(R(cx - r, cy - r, cx + r, cy + r), outline=NAVY + (round(255 * a),), width=max(1, round(p(10))))
    d.polygon([R(cx - 70, cy + 52)[0:2], R(cx - 98, cy + 98)[0:2], R(cx - 40, cy + 78)[0:2]], fill=NAVY + (round(255 * a),))
    if check > 0:   # check mark draws on
        pts = [(cx - 42, cy + 2), (cx - 12, cy + 32), (cx + 46, cy - 30)]
        seg = check * 2
        p1 = pts[0]
        p2 = pts[1] if seg >= 1 else (lerp(pts[0][0], pts[1][0], seg), lerp(pts[0][1], pts[1][1], seg))
        d.line([R(*p1), R(*p2)], fill=CORAL + (255,), width=max(1, round(p(16))), joint="curve")
        if seg > 1:
            s2 = seg - 1
            p3 = (lerp(pts[1][0], pts[2][0], s2), lerp(pts[1][1], pts[2][1], s2))
            d.line([R(*pts[1]), R(*p3)], fill=CORAL + (255,), width=max(1, round(p(16))), joint="curve")
    img.alpha_composite(lay)


def s_claim(img, t):
    """Student card -> WhatsApp verification -> check -> discount. Clean, no fake chat UI."""
    a, b = SC["claim"]
    img.paste(Image.new("RGB", (W, H), NAVY), (0, 0))
    k = expo_out(prog(t, a, 0.5))
    cream = Image.new("RGB", (W, H), CREAM)
    edge = round(W * (1 - k))
    img.paste(cream.crop((edge, 0, W, H)), (edge, 0))
    la = ease_out(prog(t, a + 0.3, 0.4))
    tracked(img, (p(92), p(400)), "HOW TO CLAIM", font(LABEL, 32), CORAL, 0.3, la)
    xs = (195, 495, 795)
    half = (135, 108, 125)          # glyph half-widths: card, bubble, pill
    labels = ("STUDENT CARD", "WHATSAPP", "DISCOUNT")
    t0 = P(6)
    for i, x in enumerate(xs):
        ki = expo_out(prog(t, t0 + 0.1 + i * 0.55, 0.5))
        if ki <= 0:
            continue
        y = 640 + (1 - ki) * 30
        if i == 0:
            card_glyph(img, x, y, ki)
        elif i == 1:
            bubble_glyph(img, x, y, ki, ease_io(prog(t, t0 + 0.75, 0.4)))
        else:
            pill(img, x, y, 230, 120, CORAL, "−15%", font(SANS_B, 66), CREAM, scale=0.9 + 0.1 * ki, alpha=ki)
        tracked(img, (p(x), p(790)), labels[i], font(LABEL, 30), NAVY, 0.18, ki, "center")
        if i < 2:   # connector draws between steps
            kc = ease_io(prog(t, t0 + 0.35 + i * 0.55, 0.35))
            if kc > 0:
                x0, x1 = x + half[i] + 14, xs[i + 1] - half[i + 1] - 14
                ImageDraw.Draw(img).rectangle((p(x0), p(638), p(x0 + (x1 - x0) * kc), p(642)), fill=SALMON)
    reveal_lines(img, ["Verify your student card", "via WhatsApp."], font(SERIF, 92), p(92), p(950), p(100), t,
                 t0 + 0.2, NAVY)


MOSAIC = ["SE5", "VN5", "VR2", "SE2", None, "CD2", "SE3", "VN2", "VN1"]


def s_mosaic(img, t):
    """Fast but calm mosaic of all apartments; centre cell carries 'Your stay,'"""
    a, b = SC["mosaic"]
    img.paste(Image.new("RGB", (W, H), CREAM), (0, 0))
    gx0, gy0, gw, gh, gap = 60, 270, 870, 1230, 12
    cw, ch = (gw - 2 * gap) / 3, (gh - 2 * gap) / 3
    kc = expo_io(prog(t, b - 0.35, 0.5))             # collapse towards the centre
    for i, pid in enumerate(MOSAIC):
        r_, c_ = divmod(i, 3)
        x0, y0 = gx0 + c_ * (cw + gap), gy0 + r_ * (ch + gap)
        order = r_ + c_
        ki = expo_out(prog(t, a + 0.04 * order * 2, 0.35))
        if ki <= 0:
            continue
        cx_, cy_ = x0 + cw / 2, y0 + ch / 2
        mx, my = gx0 + gw / 2, gy0 + gh / 2
        cx_, cy_ = lerp(cx_, mx, kc), lerp(cy_, my, kc)
        sw, sh = cw * (0.9 + 0.1 * ki) * (1 - 0.6 * kc), ch * (0.9 + 0.1 * ki) * (1 - 0.6 * kc)
        rect = (cx_ - sw / 2, cy_ - sh / 2, cx_ + sw / 2, cy_ + sh / 2)
        if pid:
            panel(img, pid, R(*rect), zoom=1.1, radius=6, alpha=ki * (1 - kc))
    reveal_lines(img, ["Your", "stay,"], font(SERIF, 86), p(gx0 + gw / 2), p(gy0 + gh / 2 - 92), p(88), t,
                 P(7) - 0.05, NAVY, align="center", t_out=b - 0.3)


def s_end(img, t):
    a, b = SC["endcard"]
    img.paste(Image.new("RGB", (W, H), CREAM), (0, 0))
    reveal_lines(img, ["Your stay,"], font(SERIF, 128), p(CX), p(700), p(130), t, a + 0.02, NAVY, align="center",
                 stagger=0.0, dur=0.45)
    reveal_lines(img, ["tailored to you."], font(SERIF, 128), p(CX), p(840), p(130), t, P(8) - 0.05, CORAL,
                 align="center")
    kl = ease_out(prog(t, P(9) - 0.1, 0.7))
    logo(img, CX, 430 + (1 - kl) * 10, 640, kl)
    kp = expo_out(prog(t, P(9) + 0.5, 0.5))
    pill(img, CX, 1090, 640, 92, CORAL, "15% STUDENT DISCOUNT", font(LABEL, 30), CREAM, scale=0.94 + 0.06 * kp,
         alpha=kp, track=0.24)
    ku = ease_out(prog(t, P(9) + 0.85, 0.5))
    f = font(SANS, 52)
    url = "tailorhomes.it/studenti/"
    tw = tlen(url, f)
    tl = Image.new("RGBA", img.size, (0, 0, 0, 0))
    ImageDraw.Draw(tl).text((p(CX) - tw / 2, p(1190) + (1 - ku) * p(10)), url, font=f, fill=NAVY + (round(255 * ku),))
    img.alpha_composite(tl)
    d = ImageDraw.Draw(img)
    ul = tw * ease_io(prog(t, P(9) + 1.2, 0.6))
    if ul > 1:
        d.rectangle((p(CX) - tw / 2, p(1268), p(CX) - tw / 2 + ul, p(1271)), fill=SALMON)


SEQ = [("open", s_open), ("variety1", s_variety), ("variety2", s_variety), ("split", s_split),
       ("benefits", s_benefits), ("discount", s_discount), ("claim", s_claim), ("mosaic", s_mosaic),
       ("endcard", s_end)]


def frame(t):
    img = Image.new("RGBA", (W, H), NAVY + (255,))
    for name, fn in SEQ:
        a, b = SC[name]
        if a <= t < b or (name == "endcard" and t >= a):
            fn(img, t)
            break
    if A.guides:
        g = Image.new("RGBA", img.size, (0, 0, 0, 0))
        d = ImageDraw.Draw(g)
        red = (220, 0, 0, 70)
        d.rectangle((0, 0, W, p(SAFE_T)), fill=red)
        d.rectangle((0, p(SAFE_B), W, H), fill=red)
        d.rectangle((p(SAFE_R), p(SAFE_T), W, p(SAFE_B)), fill=red)
        d.rectangle((0, p(SAFE_T), p(SAFE_L), p(SAFE_B)), fill=red)
        img.alpha_composite(g)
    return img.convert("RGB")


HERO = {"open": lambda: P(0) + 2.6, "variety1": lambda: P(1) + 1.6, "variety2": lambda: SC["variety2"][0] + 1.2,
        "split": lambda: P(2) + 1.9, "benefits": lambda: SC["benefits"][1] - 0.25,
        "discount": lambda: EM["fifteen percent"] + 1.5, "claim": lambda: SC["claim"][1] - 0.4,
        "mosaic": lambda: SC["mosaic"][0] + 1.0, "endcard": lambda: DUR - 0.5}


def main():
    out = C / "out"
    out.mkdir(exist_ok=True)
    if A.stills:
        sd = out / "stills"
        sd.mkdir(exist_ok=True)
        if A.stills == "hero":
            items = [(k, f()) for k, f in HERO.items()]
        else:
            items = [(f"t{float(x):05.2f}", float(x)) for x in A.stills.split(",")]
        for name, t in items:
            frame(t).save(sd / f"{name}{'_guides' if A.guides else ''}.png")
        print("stills ->", sd)
        return
    name = "tailorhomes-students-animatic.mp4" if A.mode == "animatic" else "tailorhomes-students-final.mp4"
    n = int(round(DUR * FPS))
    mix = C / "audio" / "mix" / "tailorhomes-students-mix.wav"
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
