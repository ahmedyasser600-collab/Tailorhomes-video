# Film 02 — production log

## 2 Oct 2026

**Drive audit**
- Listed all nine property folders through the Google Drive connector.
- Single-folder queries return 5 files per page; OR-queries return 50.
- Downloads arrive as base64 files that `scripts/` decodes to `source/drive/`
  (30 photos, 104 MB).
- Files above about 6 MB time out and reset the connector session, so the
  12–26 MB Villa Graziosa frames could not be fetched.
- Villa Graziosa also holds an AI-generated PNG (“ChatGPT Image…”), which is
  excluded.

**Student page**
- https://tailorhomes.it/studenti/ is blocked by the environment's network
  policy (direct request, the web-fetch tool and the Wayback Machine were all
  denied).
- The offer wording therefore relies on the supplied creative: “15% di sconto per
  studenti UniPD ed Erasmus… Verifica la tua tessera via WhatsApp e ottieni il 15%
  su ogni soggiorno — per te, amici e famiglia.”
- **Unverified and so not shown:** Wi-Fi, bills, flexible stays, neighbourhoods.

**Selection**
- 21 candidates reviewed, 14 selected across four apartments (S. Eufemia,
  Via Nullo, Vicolo Romano, Colore & Design).
- `scripts/prepare_photos.py` makes orientation-corrected 3000 px masters
  (resize only).

**Fonts**
- Cormorant Garamond Italic (Medium and SemiBold), DM Sans and Jost from Google
  Fonts (SIL OFL; licences in `fonts/`).
- The logo is the supplied SVG rasterised unmodified (reused from Film 01's
  `brand_renders`).

**Voice-over**
- The supplied master `audio/voice/tailorhomes-students-vo.wav` is **not present
  yet**.
- A scratch read (Higgsfield `text2speech_v2` / ElevenLabs, preset voice
  “Sienna”, about 1 credit) is used only for timing:
  `audio/voice/scratch/SCRATCH_students_vo.wav`.
- `scripts/analyze_vo.py` measures 28.64 s and 9 pauses, giving 10 phrases
  aligned by ordered dynamic programming.
- Film length = lead-in 0.5 s + VO + 3.2 s CTA hold = 32.34 s.

**Stills**
- `render.py --stills hero` → `out/tailorhomes-students-contact-sheet.png`.

**Iteration 1 (first stills)**
- The headline over full-bleed photos was illegible (busy furniture).
- Changed to an editorial layout: photo above, navy band with type below.
- Split-screen labels became navy tags.

**Self-critique (scores before → after fixes)**

| Criterion | Before | After |
|---|---|---|
| Mobile readability | 7 | 8 |
| Visual continuity | 7 | 8 |
| Premium feeling | 7 | 8 |

Fixes:
- Labels raised to 30 px.
- The split now parts onto cream and the tiles grow from it.
- The opening panel drifts and lifts.

Details in `docs/tailorhomes-students-review.md`.

**Animatic**
- `out/tailorhomes-students-animatic.mp4` (540×960, 30 fps, with the mix).
- Reviewed at 2 decoded frames per second, plus 10 fps around transitions.
- Fixed fading text drawn directly onto frames (the URL was visible too early).
- Removed an empty cream frame at the split → benefits handover.

**Audio**
- Original code-generated music (104 BPM, D major; synthesis helpers shared with
  Film 01; no samples, no third-party material).
- Synthesised SFX: whooshes, a soft impact on −15%, a UI click on the check.
- Music is ducked 10 dB under every phrase.
- Mix −14.0 LUFS integrated.

**Final**
- `render.py --mode final`: 1080×1920, 60 fps, H.264 High, CRF 16, yuv420p.

## To finish when inputs arrive

1. Put the supplied WAV at `audio/voice/tailorhomes-students-vo.wav`.
2. Run:
   `python scripts/analyze_vo.py && python scripts/make_shotlist.py && python scripts/make_audio.py && python scripts/render.py --mode animatic`
3. Check the animatic.
4. Run `python scripts/render.py --mode final` and `python scripts/make_poster.py`.

The whole edit re-times to the real delivery; no frame timings are hand-entered.
