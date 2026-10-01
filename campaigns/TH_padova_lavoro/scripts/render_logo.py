"""Rasterise the supplied Tailor Homes SVGs, unmodified, for compositing.

Uses librsvg + Cairo (same method as the library's scripts/render_svg_reference.py)
at a higher resolution. Source files in brand/ are only read. Outputs go to
../brand_renders/. Aspect ratio is taken from the SVG's own width/height.
"""
import ctypes as c
import math
import xml.etree.ElementTree as E
from pathlib import Path

C = Path(__file__).resolve().parents[1]
BRAND = C.parents[1] / "brand"
OUT = C / "brand_renders"

r = c.CDLL("librsvg-2.so.2")
ca = c.CDLL("libcairo.so.2")
g = c.CDLL("libgobject-2.0.so.0")
r.rsvg_handle_new_from_data.argtypes = [c.c_char_p, c.c_size_t, c.c_void_p]
r.rsvg_handle_new_from_data.restype = c.c_void_p
r.rsvg_handle_render_cairo.argtypes = [c.c_void_p, c.c_void_p]
r.rsvg_handle_render_cairo.restype = c.c_int
ca.cairo_image_surface_create.argtypes = [c.c_int, c.c_int, c.c_int]
ca.cairo_image_surface_create.restype = c.c_void_p
ca.cairo_create.argtypes = [c.c_void_p]
ca.cairo_create.restype = c.c_void_p
ca.cairo_scale.argtypes = [c.c_void_p, c.c_double, c.c_double]
ca.cairo_surface_write_to_png.argtypes = [c.c_void_p, c.c_char_p]
ca.cairo_destroy.argtypes = [c.c_void_p]
ca.cairo_surface_destroy.argtypes = [c.c_void_p]
g.g_object_unref.argtypes = [c.c_void_p]

OUT.mkdir(exist_ok=True)
for src, out, width in [("01-noBgColor.svg", "TH_full_logo_2400.png", 2400), ("04-symbol.svg", "TH_symbol_800.png", 800)]:
    data = (BRAND / src).read_bytes()
    root = E.fromstring(data)
    w, h = float(root.get("width")), float(root.get("height"))
    scale = width / w
    sf = ca.cairo_image_surface_create(0, width, math.ceil(h * scale))
    ctx = ca.cairo_create(sf)
    ca.cairo_scale(ctx, scale, scale)
    handle = r.rsvg_handle_new_from_data(data, len(data), None)
    assert handle and r.rsvg_handle_render_cairo(handle, ctx)
    ca.cairo_surface_write_to_png(sf, str(OUT / out).encode())
    ca.cairo_destroy(ctx)
    ca.cairo_surface_destroy(sf)
    g.g_object_unref(handle)
    print(out, width, "x", math.ceil(h * scale), "from", src)
