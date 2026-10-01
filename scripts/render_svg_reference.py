"""Render supplied vectors unmodified using librsvg + Cairo, via ctypes. Linux helper."""
import ctypes as c,math,xml.etree.ElementTree as E
from pathlib import Path
P=Path(__file__).resolve().parents[1]
r=c.CDLL('librsvg-2.so.2');ca=c.CDLL('libcairo.so.2');g=c.CDLL('libgobject-2.0.so.0')
r.rsvg_handle_new_from_data.argtypes=[c.c_char_p,c.c_size_t,c.c_void_p];r.rsvg_handle_new_from_data.restype=c.c_void_p
r.rsvg_handle_render_cairo.argtypes=[c.c_void_p,c.c_void_p];r.rsvg_handle_render_cairo.restype=c.c_int
ca.cairo_image_surface_create.argtypes=[c.c_int,c.c_int,c.c_int];ca.cairo_image_surface_create.restype=c.c_void_p
ca.cairo_create.argtypes=[c.c_void_p];ca.cairo_create.restype=c.c_void_p
ca.cairo_scale.argtypes=[c.c_void_p,c.c_double,c.c_double]
ca.cairo_surface_write_to_png.argtypes=[c.c_void_p,c.c_char_p]
ca.cairo_destroy.argtypes=[c.c_void_p];ca.cairo_surface_destroy.argtypes=[c.c_void_p];g.g_object_unref.argtypes=[c.c_void_p]
for src,out in [('01-noBgColor.svg','full_logo_reference.png'),('04-symbol.svg','symbol_reference.png')]:
 data=(P/'brand'/src).read_bytes();root=E.fromstring(data);w=float(root.get('width'));h=float(root.get('height'));scale=1400/w
 sf=ca.cairo_image_surface_create(0,1400,math.ceil(h*scale));ctx=ca.cairo_create(sf);ca.cairo_scale(ctx,scale,scale)
 handle=r.rsvg_handle_new_from_data(data,len(data),None);assert handle
 assert r.rsvg_handle_render_cairo(handle,ctx);ca.cairo_surface_write_to_png(sf,str(P/'brand'/out).encode())
 ca.cairo_destroy(ctx);ca.cairo_surface_destroy(sf);g.g_object_unref(handle)
 print(out)
