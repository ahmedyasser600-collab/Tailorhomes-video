# Tailor Homes: Remotion template system (Phase 1)

One composition per concept, fed by data files. One build renders every variant in
**9:16** (Reels / TikTok / Stories) and **4:5** (Meta feed ads), Italian and English.

## Templates

| Id pattern | Concept | Audience | Audio | Data |
|---|---|---|---|---|
| `gallery-<gallery>-<it\|en>-<9x16\|4x5>` | **A**: themed photo gallery (no apartment names) | Guests, ads | Music only | `data/galleries.json` |
| `services-<01..04>-it-<format>` | **B**: owner services, one pillar per video, question hook + 3 services from the site | Owners | Narration (Gia) + music | `data/services.json`, `data/vo/services_*.json` |
| `checklist-ready-<it\|en>-<format>` | **C**: “Pronta per il prossimo ospite”, items tick off over real photos | Owners, guests | Music + soft ticks | `data/checklist.json` |
| `outcome-<h1\|h2\|h3>-<it\|en>-<format>` | **D**: outcome reel (the stay, not the features); three hook variants for ad tests | Guests, ads | Narration (Gia IT / Sienna EN) + music | `data/outcome.json`, `data/vo/outcome_*.json` |

Phase 1 output is 44 videos:

| Template | Count | How it breaks down |
|---|---|---|
| A | 20 | 5 galleries × 2 languages × 2 formats |
| B | 8 | 4 pillars × 2 formats |
| C | 4 | 2 languages × 2 formats |
| D | 12 | 3 hooks × 2 languages × 2 formats |

## Brand kit (from the website)

`src/brand.ts` holds the brand tokens:

- **Colours:**
  - card `#F4F0ED`
  - page `#EAE9E5`
  - terracotta `#9C5044`
  - ink `#181818`
  - body `#4C4844`
- **Type:**
  - DM Sans for titles and body.
  - Cormorant Garamond Italic for the 01–04 numerals only. Its grave accents are faulty, so it is never used to set Italian text.
  - Jost for tracked labels.
- **Logo:** the supplied artwork, unmodified, on light backgrounds.

Safe areas are defined per format. The 9:16 format keeps text clear of the Reels user interface.

## Photos

`public/photos/pNNN.jpg` are real Tailor Homes photos from the client's Drive.

- They are resized only, to a 2400 px long side. They are never generated or retouched.
- Provenance (Drive id and file name for each photo) is in `data/photos.json`.
- On screen, photos only scale slowly and evenly. There are no pans or drifts.

## Adding content without touching code

- **New gallery:** add an entry to `data/galleries.json` with a title for each language and a list of photo ids. Then run `scripts/make_music.py gallery <seconds> public/audio/music_gallery_<n>.wav` if that photo count is new.
- **New narration:**
  1. Add `data/vo/<name>.json` with the wav path and the phrase list.
  2. Run `python scripts/vo_timing.py <name>`.
  3. Run `python scripts/make_all_audio.py`.
- **Render:** `node scripts/render-all.mjs [filter]` renders the videos to `out/<id>.mp4`, with audio levelled to −14 LUFS.

A Google Sheet can later export straight into these JSON files.

## Claims policy

Owner-service wording comes only from the services page (`tailorhomes.it/our-servies/`, client screenshot).

The videos make no claims about prices, earnings, occupancy, reviews or locations.

Music is original and code-synthesised. Narration is ElevenLabs preset voices via Higgsfield.
