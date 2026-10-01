# Tailor Homes — 3D Starter Library v1.0

12 editable models for property-management, arrival, furnished-stay, corporate
housing and home-staging videos. These are original simplified architectural and
product illustrations, not scans, manufacturer models or replicas of real listings.
The supplied logo is imported from its actual vectors, not reconstructed.

## Open and use

Open `TailorHomes_Master.blend` in Blender 4.5 LTS. It contains an overview and a
separate studio scene for each asset. Alternatively open one file under `assets/`.
Each has a camera, lighting, editable materials and a named ROOT empty. Move the
ROOT to move the whole assembly; use the pivot names in `manifest.json` to animate.
Save campaign versions separately from these reusable masters.

Register this folder in Blender's Asset Libraries settings to access the collection
assets and their custom thumbnails. `blender_assets.cats.txt` groups the collection
under Tailor Homes/Hospitality. No settings are changed automatically by the scripts.

| Asset | Video use |
|---|---|
| Vector brand lockup | Intro/outro, separately editable emblem, monogram, wordmark and descriptor |
| Branded key set | Arrival and property handover |
| Opening entrance | Door reveal and arrival transitions |
| Cabin suitcase | Corporate stays, relocation and travel |
| Lounge + table | Comfort, furnished living and home staging |
| Bed + nightstands | Ready-to-live accommodation |
| Work desk + laptop | Business and study stays |
| Apartment shell | Room assembly and layout scenes |
| Generic facade | Managed properties and portfolios |
| Booking calendar | Flexible stays, example reservations |
| Welcome folder | Guest care and arrival instructions |
| Padova marker | Location and arrival graphics |

## Brand grounding

Inspected on 1 October 2026:
- https://tailorhomes.it/
- https://tailorhomes.it/alloggi-aziendali/
- https://tailorhomes.it/apartments/
- https://tailorhomes.it/wp-content/themes/tailor-homes-theme/style.css?ver=4.2

The website's palette is recorded in `brand/palette.json`: cream #F5F0E8, navy
#1B2A4A, terracotta #B5473A, red #B8312F, warm background #E5E1DA and ink #1A1916.
These are verified website CSS inputs, not a newly invented official brand manual.
Materials convert sRGB inputs to linear values. Studio lighting and AgX alter their
rendered appearance; flat overlay colors should use the original hex values.

The supplied SVG artwork instead uses a burgundy-to-red gradient, #7D141D to
#FF1E27. That distinction is preserved. The two supplied symbol SVGs are duplicate
versions of the monogram. All original files remain in `brand/` unchanged.
The full lockup includes its irregular circular outline and original glyph paths.
Extrusion is 4 mm, with zero outline bevel. No simplification, manual tracing or
retyped replacement wordmark was used. Front-on fidelity images are in verification/.

DM Sans, Jost and Cormorant Garamond are the website's font families. Regular static
instances from the Google Fonts source repository are bundled for editable sample
text, together with their OFL notices. The logo lettering remains vector curves;
these text fonts do not replace it. The catalog title is document typography.

## Coordinates, dimensions and controls

Source geometry uses metres, X right, -Y front, Z up. GLBs use glTF's Y-up convention.
A source vector (x,y,z) becomes (x,z,-y). Preserve the imported parent hierarchy and
inspect local axes before animating in a browser. Root scales are one. Primitive
scale transforms are applied; object positions/rotations are deliberate assembly
placements. Edge modifiers, vector curves and editable text remain in Blender.

Documented controls include door Z rotation, fob/key Y rotation, suitcase handle Z
translation, laptop X rotation, calendar-page X rotation and folder-cover Z rotation.
Ranges are suggested animation poses, not mechanical constraint simulations.
The laptop ships open: +pi/2 on its hinge closes it. The door opens toward -Y.

The apartment interior is 3.6 × 3.5 m, with 2.6 m walls and an actual window opening.
It is unfurnished so the furniture can be staged freely. The facade is generic,
not a representation of a named Tailor Homes property. The calendar and welcome
folder are explicitly marked ESEMPIO; there are no real reservations, guest details,
access credentials or functioning booking interfaces. The laptop display is a
replaceable separate mesh. Change text data and material slots directly.

Meshes have UVs where image surfaces are intended. Curve paths remain editable;
exports convert them to meshes. Logo exports receive explicit gradient UVs. Text
exports become meshes, while source text retains the bundled fonts. Fonts and the
logo gradient image are packed; no machine-specific image/font dependencies are needed.

## Animation and rendering

`animation/door_welcome.blend` is a 90-frame, 30 fps, 3-second door-opening example.
The space behind the door is transparent for compositing the next shot. This is a
reusable transition element, not a finished narrated advertisement.

Review stills: 700 × 700, Cycles, 32 samples (96 for window glass). Animation: 640 ×
640, 16 samples, denoising, transparent RGBA. The H.264 review is composited on cream
and has no audio. It does not preserve alpha; use the 90 PNGs for compositing.

Final-production starting point: render 1440–2160 square for an overlay, or reframe
for a 1080 × 1920 reel; use 128–256 Cycles samples and inspect thin lettering and glass
at final resolution. These production settings are recommendations, not tested outputs.
The logo's fine distressed ring and small descriptor need enough pixels in the shot.

## Reproduce

Dependencies: Blender 4.5 LTS, Python 3 + Pillow, ffmpeg/ffprobe. The optional SVG
reference-rendering script uses Linux librsvg and Cairo; supplied reference PNGs
make that step unnecessary for normal Windows use. Blender's bundled SVG importer
is enabled only inside the build process. No paid assets or services are required.

Run from the package folder, in a working copy:

```sh
blender -b --factory-startup -t 6 --python scripts/build_library.py -- --render
blender -b --python scripts/finalize_blends.py
blender -b animation/door_welcome.blend -t 6 -o //frames/frame_ -a
blender -b --factory-startup -t 6 --python scripts/verify_library.py
blender -b -t 6 --python scripts/logo_front.py
python scripts/logo_compare.py
python scripts/encode_review.py
python scripts/catalog.py
python scripts/package_library.py
```

On Windows, substitute the quoted path to your blender.exe for `blender`. Rebuilding
overwrites generated files in the working copy. Supplied SVGs are never altered.
See `VERIFICATION.md` for actual checks and known limitations, and `CLAUDE_HANDOFF.md`
for video workflows. There is no promise that this starter set covers every future film.
