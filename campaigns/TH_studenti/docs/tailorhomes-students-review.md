# Film 02 — self-review

Scores are 1–10. The target is that every criterion reaches at least 8 before the final render.

## Step 6: still states, scored before and after fixes

| Criterion | First stills | After fixes | Notes |
|---|---|---|---|
| Real-apartment honesty | 10 | 10 | 14 unaltered photos from 4 Drive apartments; resize and crop only; the AI image in Villa Graziosa is excluded |
| Offer clarity (−15%, who, how) | 9 | 9 | Coral −15% pill lands on “fifteen percent”; claim step shown as 3 glyphs |
| Claims compliance | 9 | 9 | No partner wording, no university or Erasmus marks; unverified benefits (Wi-Fi, bills, flexibility) omitted |
| Brand fidelity (navy / cream / salmon-coral, italic serif) | 8 | 9 | Palette and type match the supplied creative |
| Mobile readability | 7 | 8 | Labels raised to 30 px; headline moved off busy photos into a navy band; navy tags on bright photos |
| Visual continuity | 7 | 8 | Split screen parts onto cream and the benefit tiles grow from it; mosaic collapses into the end card |
| Premium feel / motion restraint | 7 | 8 | Opening panel drifts and lifts; ease-out and expo motion, no bounce, glow or particles |
| Reel safe zones | 9 | 9 | All essential text sits inside x 60–930, y 250–1500 |

The three weakest criteria were readability, continuity and premium feel. All three were fixed in the iteration described in the production log.

## Step 7: animatic review

Decoded at 2 fps across the whole film, and at 10 fps around every transition.

- **Fixed:** the URL and the pill text faded in early, because their alpha was ignored when drawn directly on the RGBA frame. Both now composite through a layer.
- **Fixed:** an empty cream frame at the split → benefits handover. The exit is now eased into the end of the split, and the tiles start 0.02 s later.
- **Fixed:** an analysis bug assigned the end-card phrases to the wrong pauses. Alignment now uses ordered dynamic programming.
- **OK:** VO sync on the scene cuts, the −15% hit, the WhatsApp check click and the logo on “Tailor Homes”.

## Step 8: final render check

- `out/tailorhomes-students-final.mp4` was checked with ffprobe:
  - H.264 High, 1080×1920, 60 fps, yuv420p, CRF 16.
  - 1897 frames, 31.62 s (after revision 1).
  - AAC audio, 31.62 s.
- A frame sheet was decoded every 2 s and matches the animatic.
- `out/tailorhomes-students-poster.png` (1080×1920):
  - Top: two real apartments (S. Eufemia, Via Nullo).
  - Navy offer band: “UNIPD & ERASMUS STUDENTS”, *Your stay, tailored to you.*, a −15% pill and the URL.

## Open items (not closed by this review)

1. **Master VO missing.** `audio/voice/tailorhomes-students-vo.wav` has not been supplied. The film is timed to a labelled AI scratch read. Re-run the pipeline once the master WAV is in place (see the production log); it re-times automatically.
2. **Student page not verified.** tailorhomes.it is blocked by the environment's network policy. The offer wording comes from the supplied creative only. Check it against https://tailorhomes.it/studenti/ before publishing.
3. **Audio not heard by a person.** The music and SFX are original and code-generated. Loudness was measured (−14 LUFS), but nobody has listened to the mix yet.
4. **Villa Graziosa not used.** Its photos are 12–26 MB and could not be fetched through the Drive connector.
5. **No official marks used.** The UniPD and Erasmus names appear as plain text only.
