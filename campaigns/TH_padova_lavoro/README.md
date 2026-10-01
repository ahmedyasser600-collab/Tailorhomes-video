# Tailor Homes — “A Padova per lavoro. A casa, con Tailor Homes.”

A 30-second Italian Instagram Reel (1080 × 1920, 30 fps) for people travelling to
Padova for work and for companies arranging accommodation. It is built entirely
from the Tailor Homes 3D library in this repository. That library is used
read-only: assets are appended (copied) into a separate campaign file, and
nothing under `assets/`, `exports/`, `animation/` or `brand/` is modified.

The interiors are **illustrative 3D scenes**, not photographs of actual listings.
A “Rendering 3D illustrativo” label is shown during the interiors. The film makes
no claims about prices, availability, ratings, testimonials or guarantees. The
calendar and welcome folder are the library’s “ESEMPIO” props with fictional
sample content.

## Deliverables

| File | What it is |
|---|---|
| `preview/TH_Padova_Lavoro_PREVIEW_540x960.mp4` | Low-resolution complete review cut with narration, music and captions |
| `final/TH_Padova_Lavoro_1080x1920.mp4` | Full-resolution master (see `docs/REVIEW_NOTES.md` for status) |
| `subtitles/TH_Padova_Lavoro_it.srt` | Italian subtitles (also burned into the video) |
| `audio/stems/vo_edit.wav` | Narration on the film timeline (48 kHz/24-bit) |
| `audio/stems/music_bed_full.wav`, `music_bed_ducked.wav` | Original music bed, before and after ducking |
| `audio/stems/sfx.wav` | Sound effects |
| `audio/mix/TH_padova_mix.wav` | Final mix (−14 LUFS, −1.5 dBTP) |
| `audio/vo/*.mp3` | The two generated narration takes, untouched |
| `blender/TH_padova_lavoro_shots.blend` | Editable 3D shots, one scene per shot |
| `docs/` | Voice-over record, music/sound licence record, review notes |

## The film

| Time | Shot (Blender scene) | Library assets | Headline |
|---|---|---|---|
| 0–5 s | `S1_arrivo`: the suitcase rolls in and extends its handle; the Padova marker drops in | 04, 12 | A Padova per lavoro? |
| 5–10 s | `S2_chiavi_porta`: branded keys swing into focus, the door opens, and the camera pushes through into a warm lounge | 02, 03, 05 | Trova il tuo spazio. |
| 10–19 s | `S3_interni`: matched to the lounge, then pulls back to a three-room dollhouse; bedroom and workspace furniture drop in, the suitcase arrives, the laptop opens | 08, 05, 06, 04, 07 | Spazio per vivere e lavorare. |
| 19–25 s | `S4_calendario_benvenuto`: the calendar page falls into place, the welcome folder opens, and the camera pushes in on “La tua casa, anche in viaggio.” | 10, 11 | Soggiorni brevi e medi. |
| 25–30 s | End card (2D): supplied logo, tagline, CTA “Scopri le soluzioni su tailorhomes.it” | `brand/01-noBgColor.svg` | — |

Lighting is one warm “sun” from upper-left-front in every shot, with the AgX view
transform. Brand colours come from `brand/palette.json`. Type uses the bundled
fonts: Cormorant Garamond for headlines and tagline, DM Sans for captions and CTA,
and Jost for the URL and labels. The wordmark is never retyped; the logo is the
supplied SVG rasterised unmodified, with its aspect ratio preserved.

## How to edit and re-render

Requirements: Blender 4.5 LTS (or `pip install bpy==4.5.9` with Python 3.11),
Python 3 with Pillow, numpy and scipy, ffmpeg, and librsvg/Cairo for the logo raster.

All timing lives in **`timeline.json`**: scene ranges, animation beats, headlines,
end-card text, narration placement, caption cues and SFX. Run from the repository
root:

```sh
P=campaigns/TH_padova_lavoro
# 1. 3D: rebuild the shot file after changing build_shots.py or timeline beats
python $P/blender/build_shots.py            # or: blender -b --factory-startup --python ...
# 2. Render plates (resumable; existing frames are skipped)
python $P/blender/render_shots.py -- --tier preview      # 540x960, 16 spp
python $P/blender/render_shots.py -- --tier final        # 1080x1920, 48 spp
#    add --scenes S3_interni or --frames 1,50 to limit; --overwrite to redo
# 3. Logo raster from the supplied SVG (only needed once)
python $P/scripts/render_logo.py
# 4. Audio (narration edit, music, SFX, ducking, loudness) and subtitles
python $P/scripts/make_audio.py
python $P/scripts/make_srt.py
# 5. Composite and encode
python $P/scripts/composite.py --tier preview
python $P/scripts/composite.py --tier final
python $P/scripts/composite.py --tier final --stills 450 --safe-guides   # check stills
```

When run through `bpy`, use `render_shots.py -- --tier …` exactly as shown.

Common edits:

- **Text / timing.** Edit `headlines`, `captions` or `end_card` in `timeline.json`,
  then re-run steps 4 and 5. No re-render is needed.
- **Narration.** See `docs/VOICEOVER.md`. Replace the file, update the segment
  times, then run steps 4 and 5.
- **Music.** See `docs/MUSIC_LICENSE.md`.
- **Animation or camera.** Edit `build_shots.py` (each shot is one function) or
  open the `.blend` and keyframe directly; then re-render that scene with
  `--overwrite`. If you edit the `.blend` by hand, don’t re-run `build_shots.py`,
  which would overwrite it.
- **Safe areas.** Essential text stays within x 60–930 and y 250–1500 of 1920
  (Reels UI: top 250 px, bottom 420 px, right 150 px). Use `--safe-guides` to check.

Scene frame numbering: Blender frame 1 of each scene corresponds to film time
`scene.start − 8 frames`. The 8-frame handles at each end are used for the
dissolves.
