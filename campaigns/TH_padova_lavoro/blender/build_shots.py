"""Tailor Homes - "A Padova per lavoro" reel: build the editable Blender shot file.

Appends (copies) the named TH_* collections from the read-only asset masters in
../../../assets/ into a new campaign file, TH_padova_lavoro_shots.blend, with one
scene per 3D shot. The masters are only read, never saved.

Run from the repository root (Blender 4.5 LTS, or the `bpy` 4.5 module):
    blender -b --factory-startup --python campaigns/TH_padova_lavoro/blender/build_shots.py
    python  campaigns/TH_padova_lavoro/blender/build_shots.py      # with bpy installed

Timing comes from ../timeline.json. Each scene's frame 1 corresponds to film
frame (scene.start - HANDLE); see SHOT_RANGES in that file.
"""
import json
import math
from pathlib import Path

import bpy
from mathutils import Vector

HERE = Path(__file__).resolve().parent
CAMPAIGN = HERE.parent
REPO = CAMPAIGN.parents[1]
ASSETS = REPO / "assets"
OUT = HERE / "TH_padova_lavoro_shots.blend"
TL = json.loads((CAMPAIGN / "timeline.json").read_text())
FPS = TL["fps"]
HANDLE = TL["handle_frames"]

# One light direction for the whole film: soft sun from upper left, in front.
SUN_DIR = Vector((-0.55, -0.75, 1.0))


def lin(h):
    def c(x):
        x /= 255
        return x / 12.92 if x <= 0.04045 else ((x + 0.055) / 1.055) ** 2.4
    return tuple(c(int(h[i:i + 2], 16)) for i in (0, 2, 4)) + (1.0,)


def mat(name, hexcol, rough=0.6, emit=0.0):
    m = bpy.data.materials.get(name) or bpy.data.materials.new(name)
    m.use_nodes = True
    b = m.node_tree.nodes["Principled BSDF"]
    b.inputs["Base Color"].default_value = lin(hexcol)
    b.inputs["Roughness"].default_value = rough
    if emit:
        b.inputs["Emission Color"].default_value = lin(hexcol)
        b.inputs["Emission Strength"].default_value = emit
    m.diffuse_color = lin(hexcol)
    return m


# ----------------------------------------------------------------- helpers
def local_frame(scene_key, film_seconds):
    """Scene-local frame for a film time in seconds."""
    start = TL["scenes"][scene_key]["start"]
    return round((film_seconds - start) * FPS) + HANDLE + 1


def key(obj, path, frame, value, index=-1):
    if index >= 0:
        getattr(obj, path)[index] = value
    else:
        setattr(obj, path, value)
    obj.keyframe_insert(data_path=path, frame=frame, index=index)


def smooth(obj, interp="BEZIER"):
    ad = obj.animation_data
    if not ad or not ad.action:
        return
    for fc in ad.action.fcurves:
        for k in fc.keyframe_points:
            k.interpolation = interp
            k.handle_left_type = k.handle_right_type = "AUTO_CLAMPED"


def append(asset_id, scene):
    path = ASSETS / f"{asset_id}.blend"
    name = f"TH_{asset_id}"
    with bpy.data.libraries.load(str(path), link=False) as (src, dst):
        dst.collections = [name]
    col = dst.collections[0]
    scene.collection.children.link(col)
    return col


def duplicate(col, scene, suffix):
    """Copy an appended collection (objects copied, mesh data shared) - safe re-use."""
    new = bpy.data.collections.new(col.name + suffix)
    scene.collection.children.link(new)
    m = {}
    for o in col.objects:
        c = o.copy()
        if o.animation_data:
            c.animation_data_clear()
        new.objects.link(c)
        m[o] = c
    for o, c in m.items():
        if o.parent in m:
            c.parent = m[o.parent]
            c.matrix_parent_inverse = o.matrix_parent_inverse.copy()
    return new


def find(col, base):
    """Object in an appended collection by its manifest name (ignores .001 copies)."""
    hits = [o for o in col.objects if o.name == base or o.name.split(".")[0] == base.split(".")[0] and o.name.startswith(base)]
    exact = [o for o in hits if o.name == base]
    return (exact or sorted(hits, key=lambda o: o.name))[0]


def root(col):
    return next(o for o in col.objects if o.name.split(".")[0].endswith("_ROOT"))


def box(name, loc, size, material, col):
    me = bpy.data.meshes.new(name)
    sx, sy, sz = (s / 2 for s in size)
    v = [(x, y, z) for x in (-sx, sx) for y in (-sy, sy) for z in (-sz, sz)]
    f = [(0, 1, 3, 2), (4, 6, 7, 5), (0, 4, 5, 1), (2, 3, 7, 6), (0, 2, 6, 4), (1, 5, 7, 3)]
    me.from_pydata(v, [], f)
    me.materials.append(material)
    o = bpy.data.objects.new(name, me)
    o.location = loc
    col.objects.link(o)
    bev = o.modifiers.new("Edge", "BEVEL")
    bev.width, bev.segments = min(0.006, min(size) / 4), 2
    return o


def cyclorama(scene, col, back_y=4.0, radius=1.6, material=None, z0=0.0):
    """Seamless floor + curved backdrop, extruded along X."""
    prof = [(-40.0, 0.0), (back_y - radius, 0.0)]
    for i in range(1, 12):
        a = (math.pi / 2) * i / 12
        prof.append((back_y - radius + radius * math.sin(a), radius - radius * math.cos(a)))
    prof += [(back_y, radius), (back_y, 25.0)]
    v, f = [], []
    for x in (-40.0, 40.0):
        v += [(x, y, z) for y, z in prof]
    n = len(prof)
    f = [(i, i + 1, n + i + 1, n + i) for i in range(n - 1)]
    me = bpy.data.meshes.new("Cyc")
    me.from_pydata(v, [], f)
    for p in me.polygons:
        p.use_smooth = True
    me.materials.append(material)
    o = bpy.data.objects.new("Studio_Cyclorama", me)
    o.location.z = z0
    col.objects.link(o)
    return o


def studio(scene, cyc_back_y=4.0, sun_strength=3.2, world_strength=0.55, cyc_z=0.0):
    col = bpy.data.collections.new(scene.name + "_STUDIO")
    scene.collection.children.link(col)
    cyclorama(scene, col, cyc_back_y, material=MATS["cyc"], z0=cyc_z)
    sun = bpy.data.lights.new(scene.name + "_Sun", "SUN")
    sun.energy, sun.angle = sun_strength, math.radians(14)
    sun.color = (1.0, 0.96, 0.9)
    so = bpy.data.objects.new(scene.name + "_Sun", sun)
    so.rotation_euler = SUN_DIR.to_track_quat("Z", "Y").to_euler()
    col.objects.link(so)
    w = bpy.data.worlds.new(scene.name + "_World")
    w.use_nodes = True
    bg = w.node_tree.nodes["Background"]
    bg.inputs[0].default_value = lin("EDE6DC")
    bg.inputs[1].default_value = world_strength
    scene.world = w
    return col


def camera(scene, col, lens=50, dof=None):
    cam = bpy.data.cameras.new(scene.name + "_Cam")
    cam.sensor_fit, cam.sensor_height, cam.lens = "VERTICAL", 36, lens
    cam.clip_start, cam.clip_end = 0.02, 200
    o = bpy.data.objects.new(scene.name + "_Cam", cam)
    col.objects.link(o)
    tgt = bpy.data.objects.new(scene.name + "_CamTarget", None)
    tgt.empty_display_size = 0.1
    col.objects.link(tgt)
    c = o.constraints.new("TRACK_TO")
    c.target, c.track_axis, c.up_axis = tgt, "TRACK_NEGATIVE_Z", "UP_Y"
    if dof:
        cam.dof.use_dof, cam.dof.aperture_fstop = True, dof
    scene.camera = o
    return o, tgt


def render_settings(scene, frames):
    r = scene.render
    scene.render.engine = "CYCLES"
    scene.cycles.device = "CPU"
    scene.cycles.samples = 16
    scene.cycles.use_adaptive_sampling = True
    scene.cycles.adaptive_threshold = 0.04
    scene.cycles.use_denoising = True
    scene.cycles.max_bounces = 6
    scene.cycles.caustics_reflective = scene.cycles.caustics_refractive = False
    r.use_persistent_data = True
    r.resolution_x, r.resolution_y, r.resolution_percentage = 1080, 1920, 50
    r.fps = FPS
    r.film_transparent = False
    r.use_motion_blur, r.motion_blur_shutter = True, 0.5
    r.image_settings.file_format, r.image_settings.color_mode = "PNG", "RGB"
    scene.view_settings.view_transform = "AgX"
    scene.view_settings.exposure = 0.35
    scene.frame_start, scene.frame_end = 1, frames


def new_scene(key_):
    sc = bpy.data.scenes.new(key_)
    info = TL["scenes"][key_]
    frames = round((info["end"] - info["start"]) * FPS) + 2 * HANDLE
    render_settings(sc, frames)
    return sc, info


MATS = {}


# ----------------------------------------------------------------- S1
def s1():
    sc, info = new_scene("S1_arrivo")
    st = studio(sc)
    F = lambda t: local_frame("S1_arrivo", t)
    bag = append("04_luggage", sc)
    pin = append("12_location", sc)
    bag_root, pin_root = root(bag), root(pin)

    # Suitcase rolls in from the right on its wheels, leaning as if pulled.
    mover = bpy.data.objects.new("S1_Suitcase_Mover", None)
    st.objects.link(mover)
    bag_root.parent = mover
    bag_root.location = (0.17, 0, 0)            # pivot at the leading wheel line
    for f, x in [(1, 0.88), (F(1.9), -0.40), (F(5.2), -0.40)]:
        key(mover, "location", f, x, 0)
    for f, r in [(1, -0.13), (F(1.5), -0.13), (F(2.1), 0.02), (F(2.35), 0)]:
        key(mover, "rotation_euler", f, r, 1)
    smooth(mover)
    h = find(bag, "Suitcase_Telescopic_Handle")
    for f, z in [(1, -0.23), (F(2.4), -0.23), (F(3.0), 0.0)]:
        key(h, "location", f, z, 2)
    smooth(h)

    # Padova marker drops onto its platform position with a soft settle.
    pin_root.location = (0.30, 0.42, 0)
    for f, z in [(1, 2.6), (F(1.2), 2.6), (F(1.95), 0.0), (F(2.15), 0.045), (F(2.35), 0.0)]:
        key(pin_root, "location", f, z, 2)
    for f, r in [(1, -0.5), (F(1.2), -0.5), (F(2.35), -0.18), (F(5.2), -0.12)]:
        key(pin_root, "rotation_euler", f, r, 2)
    smooth(pin_root)

    cam, tgt = camera(sc, st, lens=45)
    for f, loc in [(1, (0.80, -3.05, 1.02)), (F(5.2), (0.36, -2.45, 0.88))]:
        key(cam, "location", f, Vector(loc))
    for f, loc in [(1, (0.05, 0.1, 0.46)), (F(5.2), (0.02, 0.12, 0.44))]:
        key(tgt, "location", f, Vector(loc))
    smooth(cam)
    smooth(tgt)
    return sc


# ----------------------------------------------------------------- S2
def s2():
    sc, info = new_scene("S2_chiavi_porta")
    st = studio(sc, cyc_back_y=8.0)
    F = lambda t: local_frame("S2_chiavi_porta", t)
    door = append("03_entrance", sc)
    keys = append("02_keys", sc)

    # A simple wall around the supplied door frame (door 1.16 m wide, 2.22 m tall).
    wall = MATS["wall"]
    box("S2_Wall_L", (-3.08, 0.0, 1.75), (5.0, 0.16, 3.5), wall, st)
    box("S2_Wall_R", (3.08, 0.0, 1.75), (5.0, 0.16, 3.5), wall, st)
    box("S2_Wall_Top", (0, 0.0, 2.86), (1.16, 0.16, 1.28), wall, st)
    # The space beyond the door: oak floor, warm back wall and the supplied lounge.
    box("S2_Interior_Floor", (0, 2.5, -0.02), (5.0, 4.6, 0.046), MATS["oak"], st)
    box("S2_Interior_Back", (0, 4.6, 1.75), (5.0, 0.1, 3.5), MATS["glow"], st)
    lounge = append("05_lounge", sc)
    root(lounge).location = (0.1, 3.55, 0.003)
    lt = bpy.data.lights.new("S2_Interior_Light", "AREA")
    lt.energy, lt.size, lt.color = 450, 2.0, (1.0, 0.88, 0.72)
    lo = bpy.data.objects.new("S2_Interior_Light", lt)
    lo.location = (0.4, 2.4, 2.9)
    st.objects.link(lo)
    hinge = find(door, "Door_Hinge")
    for f, r in [(1, 0.0), (F(7.2), 0.0), (F(8.9), -1.62)]:
        key(hinge, "rotation_euler", f, r, 2)
    smooth(hinge)

    # Keys descend on a swing in front of the door, then lift away.
    kr = root(keys)
    kr.location = (0.0, -2.05, 1.95)
    for f, z in [(1, 1.95), (F(5.0), 1.95), (F(6.0), 1.04), (F(7.0), 1.06), (F(7.9), 2.0)]:
        key(kr, "location", f, z, 2)
    for f, r in [(1, 0.9), (F(5.0), 0.9), (F(6.4), -0.12), (F(7.4), 0.12)]:
        key(kr, "rotation_euler", f, r, 2)
    smooth(kr)
    fob, kp = find(keys, "Key_Fob_Pivot"), find(keys, "Key_Pivot")
    for i, (amp, ph) in enumerate([(0.26, 0), (0.5, 1.4)]):
        piv = (fob, kp)[i]
        for n in range(0, 22):
            t = 5.6 + n * 0.12
            a = amp * math.exp(-1.6 * (t - 5.6)) * math.sin(7.5 * (t - 5.6) + ph)
            key(piv, "rotation_euler", F(t), a, 1)
        smooth(piv)

    cam, tgt = camera(sc, st, lens=55, dof=2.0)
    cam.data.dof.focus_object = None
    for f, loc in [(1, (0.12, -2.86, 1.25)), (F(7.0), (0.10, -2.78, 1.25)), (F(8.7), (0.06, -2.15, 1.25)), (F(10.2), (0.02, -0.25, 1.30))]:
        key(cam, "location", f, Vector(loc))
    for f, loc in [(1, (0.0, -2.05, 1.16)), (F(7.0), (0.0, -2.0, 1.18)), (F(7.9), (0.0, 0.0, 1.12)), (F(8.7), (0.0, 0.5, 1.08)), (F(10.2), (0.0, 3.0, 0.75))]:
        key(tgt, "location", f, Vector(loc))
    for f, d in [(1, 0.80), (F(7.0), 0.75), (F(7.9), 2.7), (F(8.7), 4.6), (F(10.2), 3.8)]:   # door, then sofa
        key(cam.data.dof, "focus_distance", f, d)
    smooth(cam)
    smooth(tgt)
    smooth(cam.data)
    return sc


# ----------------------------------------------------------------- S3
def s3():
    sc, info = new_scene("S3_interni")
    st = studio(sc, cyc_back_y=7.0, cyc_z=-0.142)
    F = lambda t: local_frame("S3_interni", t)
    pitch = 3.92
    rooms = []
    shell = append("08_apartment", sc)
    for i in range(3):
        col = shell if i == 0 else duplicate(shell, sc, f"_room{i}")
        r = root(col)
        r.location.x = i * pitch
        for o in col.objects:
            b = o.name.split(".")[0]
            if b == "Right_Cutaway_Wall" and i < 2:
                o.hide_render = o.hide_viewport = True
            if i > 0 and b in {"Wall_Below_Window", "Wall_Above_Window", "Wall_Window_Side", "Window_Jamb",
                               "Window_Frame", "Window_Mullion", "Window_Glass"}:
                o.hide_render = o.hide_viewport = True
        if i > 0:
            box(f"S3_Partition_{i}", (i * pitch - 1.88, 0, 1.30), (0.16, 3.5, 2.6), MATS["wall_cream"], st)
        rooms.append(col)

    def drop(col, x, y, t0, rz=0.0, height=1.6, dur=0.75):
        r = root(col)
        r.location = (x, y, height)
        r.rotation_euler.z = rz
        for f, z in [(1, height), (F(t0), height), (F(t0 + dur), 0.0), (F(t0 + dur + 0.12), 0.012), (F(t0 + dur + 0.24), 0.0)]:
            key(r, "location", f, z, 2)
        for f, s in [(1, 0.0), (F(t0) - 1, 0.0), (F(t0), 1.0)]:
            key(r, "scale", f, Vector((s, s, s)))
        smooth(r)
        # scale snaps (constant) so pieces are invisible until they start to fall
        for fc in r.animation_data.action.fcurves:
            if fc.data_path == "scale":
                for k in fc.keyframe_points:
                    k.interpolation = "CONSTANT"
        return r

    s = TL["scenes"]["S3_interni"]["beats"]
    lounge = append("05_lounge", sc)
    root(lounge).location = (0.0, 0.62, 0.0)     # already seen through the door in S2
    bed = append("06_bedroom", sc)
    drop(bed, pitch - 0.1, 0.52, s["bed_drop"])
    bag = append("04_luggage", sc)
    br = root(bag)
    br.location = (pitch + 1.35, -0.95, 0)
    br.rotation_euler.z = -0.5
    for f, x in [(1, pitch + 2.6), (F(s["bag_in"]), pitch + 2.6), (F(s["bag_in"] + 0.8), pitch + 1.30)]:
        key(br, "location", f, x, 0)
    smooth(br)
    desk = append("07_workspace", sc)
    drop(desk, 2 * pitch + 0.2, 1.12, s["desk_drop"], rz=0.0)
    lid = find(desk, "Laptop_Screen_Hinge")
    for f, r in [(1, 1.5708), (F(s["laptop_open"]), 1.5708), (F(s["laptop_open"] + 0.8), 0.0)]:
        key(lid, "rotation_euler", f, r, 0)
    smooth(lid)

    cam, tgt = camera(sc, st, lens=55)
    # Opens matched to the end of S2 (camera in front of the sofa), then pulls back to the dollhouse view.
    key(cam, "location", 1, Vector((-0.08, -3.18, 1.30)))
    key(tgt, "location", 1, Vector((-0.10, 0.07, 0.75)))
    for f, l in [(1, 55), (F(s["pullback"]), 44)]:
        key(cam.data, "lens", f, l)
    path = [(F(s["pullback"]), 0.0, -6.7, 3.4), (F(s["move1"]), 0.0, -6.55, 3.35), (F(s["move1"] + 1.0), pitch, -6.7, 3.4),
            (F(s["move2"]), pitch, -6.5, 3.35), (F(s["move2"] + 1.0), 2 * pitch, -6.5, 3.35),
            (sc.frame_end, 2 * pitch, -5.8, 3.1)]
    for f, x, y, z in path:
        key(cam, "location", f, Vector((x + 0.25, y, z)))
        key(tgt, "location", f, Vector((x + 0.05, 0.35, 0.85)))
    smooth(cam)
    smooth(tgt)
    smooth(cam.data)
    return sc


# ----------------------------------------------------------------- S4
def s4():
    sc, info = new_scene("S4_calendario_benvenuto")
    st = studio(sc, cyc_back_y=1.6)
    F = lambda t: local_frame("S4_calendario_benvenuto", t)
    s = TL["scenes"]["S4_calendario_benvenuto"]["beats"]
    # Oak console table (campaign set dressing, not a library asset).
    top_z = 0.78
    box("S4_Console_Top", (0, 0.05, top_z - 0.02), (1.1, 0.46, 0.04), MATS["oak"], st)
    for x in (-0.5, 0.5):
        for y in (-0.13, 0.23):
            box("S4_Console_Leg", (x, y, (top_z - 0.04) / 2), (0.035, 0.035, top_z - 0.04), MATS["navy"], st)

    cal = append("10_calendar", sc)
    cr = root(cal)
    cr.location = (-0.2, 0.16, top_z)
    cr.rotation_euler = (math.radians(-8), 0, math.radians(14))
    # Small easel stand behind the calendar so it stands believably.
    stand = box("S4_Calendar_Stand", (0, 0.10, 0.12), (0.24, 0.012, 0.26), MATS["navy"], st)
    stand.parent = cr
    stand.rotation_euler.x = math.radians(28)
    page = find(cal, "Calendar_Front_Page")
    for f, r in [(1, 2.5), (F(s["page_drop"]), 2.5), (F(s["page_drop"] + 0.7), 0.0), (F(s["page_drop"] + 0.82), 0.06), (F(s["page_drop"] + 0.95), 0.0)]:
        key(page, "rotation_euler", f, r, 0)
    smooth(page)

    fol = append("11_welcome", sc)
    fr = root(fol)
    fr.location = (0.2, -0.17, top_z + 0.0235)
    fr.rotation_euler = (-math.pi / 2, 0, math.radians(-12))
    cover = find(fol, "Welcome_Cover_Hinge")
    for f, r in [(1, 0.0), (F(s["folder_open"]), 0.0), (F(s["folder_open"] + 1.1), -1.95)]:
        key(cover, "rotation_euler", f, r, 2)
    smooth(cover)

    cam, tgt = camera(sc, st, lens=50, dof=4.0)
    pts = [(1, (0.42, -1.55, 1.62), (0.0, 0.02, 1.07)),          # aimed high: calendar sits below the headline panel
           (F(s["push"]), (0.30, -1.30, 1.55), (0.05, 0.0, 1.03)),
           (sc.frame_end, (0.30, -0.62, 1.38), (0.17, -0.06, 0.82))]
    for f, c, t in pts:
        key(cam, "location", f, Vector(c))
        key(tgt, "location", f, Vector(t))
    for f, d in [(1, 1.75), (F(s["push"]), 1.5), (sc.frame_end, 0.85)]:
        key(cam.data.dof, "focus_distance", f, d)
    smooth(cam)
    smooth(tgt)
    smooth(cam.data)
    return sc


# ----------------------------------------------------------------- printed labels
# Blender's FONT objects in the library props (calendar, welcome folder, marker)
# fill some glyph counters incorrectly at these tiny sizes ("SOGGIO?NO", "Benv=nuti",
# solid A's), and the marker label floats above its platform. Each label is replaced
# by a flat card carrying the same text typeset with Pillow, laid on its surface.
LABEL_PX_PER_EM = 220
LABEL_SINK = {                      # how far (m) to move the card from the text back onto its surface
    "Calendar_Title": 0.002, "Calendar_Sample_Label": 0.002, "Calendar_Day": 0.0012,
    "Welcome_Title": 0.0015, "Welcome_Label": 0.0015, "Welcome_Inside_Title": 0.0006,
    "Welcome_Inside_Demo": 0.0006, "Laptop_Demo_Label": 0.0, "Location_Label": 0.0,
}


def _label_image(body, font_file, rgb, name):
    from PIL import Image, ImageDraw, ImageFont
    f = ImageFont.truetype(str(font_file), LABEL_PX_PER_EM)
    lines = body.split("\n")
    d = ImageDraw.Draw(Image.new("L", (8, 8)))
    lh = round(LABEL_PX_PER_EM * 1.0)
    widths = [d.textlength(l, font=f) for l in lines]
    pad = 40
    w, h = round(max(widths)) + 2 * pad, lh * len(lines) + 2 * pad
    im = Image.new("RGBA", (w, h), rgb + (0,))
    dr = ImageDraw.Draw(im)
    for i, (l, lw) in enumerate(zip(lines, widths)):
        dr.text(((w - lw) / 2, pad + i * lh + lh / 2), l, font=f, fill=rgb + (255,), anchor="lm")
    path = HERE / "label_textures" / f"{name}.png"
    path.parent.mkdir(exist_ok=True)
    im.save(path)
    return path, w, h


def text_to_cards(sc):
    fonts = HERE.parent / "fonts"
    done = {}
    for ob in [o for o in sc.objects if o.type == "FONT"]:
        base = ob.name.split(".")[0]
        cu = ob.data
        font_file = fonts / ("CormorantGaramond-Regular.ttf" if "Cormorant" in cu.font.name else "Jost-Regular.ttf")
        bc = cu.materials[0].node_tree.nodes["Principled BSDF"].inputs["Base Color"].default_value
        srgb = tuple(round(255 * (12.92 * c if c <= 0.0031308 else 1.055 * c ** (1 / 2.4) - 0.055)) for c in bc[:3])
        key = (cu.body, font_file.name, srgb)
        if key not in done:
            tag = f"label_{len(done):02d}_{base}"
            path, w, h = _label_image(cu.body, font_file, srgb, tag)
            img = bpy.data.images.load(str(path))
            img.pack()
            m = bpy.data.materials.new(tag)
            m.use_nodes = True
            nt = m.node_tree
            tex = nt.nodes.new("ShaderNodeTexImage")
            tex.image = img
            bsdf = nt.nodes["Principled BSDF"]
            nt.links.new(tex.outputs["Color"], bsdf.inputs["Base Color"])
            nt.links.new(tex.outputs["Alpha"], bsdf.inputs["Alpha"])
            bsdf.inputs["Roughness"].default_value = 0.6
            done[key] = (m, w, h)
        m, w, h = done[key]
        sx, sy = w / LABEL_PX_PER_EM * cu.size / 2, h / LABEL_PX_PER_EM * cu.size / 2
        z = -LABEL_SINK.get(base, 0.0)
        me = bpy.data.meshes.new(ob.name + "_Card")
        me.from_pydata([(-sx, -sy, z), (sx, -sy, z), (sx, sy, z), (-sx, sy, z)], [], [(0, 1, 2, 3)])
        uv = me.uv_layers.new()
        for li, co in zip(range(4), [(0, 0), (1, 0), (1, 1), (0, 1)]):
            uv.data[li].uv = co
        me.materials.append(m)
        card = bpy.data.objects.new(ob.name + "_Card", me)
        for col in ob.users_collection:
            col.objects.link(card)
        card.parent = ob.parent
        card.matrix_parent_inverse = ob.matrix_parent_inverse.copy()
        card.location, card.rotation_euler, card.scale = ob.location.copy(), ob.rotation_euler.copy(), ob.scale.copy()
        if base == "Location_Label":                 # lay it on the platform top (z 0.05), not 2 cm above
            card.location.z = 0.0505
        card.visible_shadow = False
        ob.hide_render = ob.hide_viewport = True


def main():
    bpy.ops.wm.read_factory_settings(use_empty=True)
    MATS.update(
        cyc=mat("TH_Cyc_WarmBG", "E5E1DA", 0.8),
        wall=mat("TH_Wall_Cream", "F5F0E8", 0.7),
        wall_cream=mat("TH_Partition_Cream", "F5F0E8", 0.65),
        glow=mat("TH_Room_Glow", "F5EBDD", 0.7, emit=0.9),
        oak=mat("TH_Console_Oak", "AC8158", 0.5),
        navy=mat("TH_Console_Navy", "1B2A4A", 0.45),
    )
    for build in (s1, s2, s3, s4):
        sc = build()
        text_to_cards(sc)
        sc.frame_set(1)
        print("SCENE_BUILT", sc.name, sc.frame_end, flush=True)
    for s in list(bpy.data.scenes):
        if s.name == "Scene":
            bpy.data.scenes.remove(s)
    if bpy.context.window:
        bpy.context.window.scene = bpy.data.scenes["S1_arrivo"]
    bpy.ops.file.pack_all()
    bpy.ops.wm.save_as_mainfile(filepath=str(OUT), compress=True)
    print("SAVED", OUT)


if __name__ == "__main__":
    main()
