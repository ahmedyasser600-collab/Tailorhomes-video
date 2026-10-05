# Tailor Homes video: working rules

## Music (client rule)
- **Always ask the client before adding music.** Never compose or generate music on your own initiative.
- The client downloads licensed tracks from their audio library and supplies them together with the credits.
- In `remotion/`:
  - Put supplied tracks in `public/audio/library/`.
  - Record each track in `data/music_credits.json`: track, artist, library, licence or ID, and the videos that use it.
  - Fit each track to a video with `scripts/fit_music.py`.
- Put the credits in the delivery note for every video that uses a track.

## Content rules (from the client's briefs)
- **Photos:** real Tailor Homes photos only, from the client's Drive.
  - Crop and scale only.
  - No generated or retouched interiors.
  - Skip phone photos.
  - Never use the AI-generated image in the Villa Graziosa folder.
- **Apartment names:** do not show them in the generic templates. They are galleries only.
- **Claims:** service claims come only from the website's services page. Make no claims about prices, earnings, occupancy, reviews or partnerships.
- **Brand kit:** take it from the website (see `remotion/src/brand.ts`).
- **Language:** Italian only. No English versions.
- **Style reference:** the client liked the checklist video (`remotion/src/templates/Checklist.tsx`). They rejected the outcome reel (its sequence felt odd).
- **Planning first:** when the client says "planning mode", discuss and agree before generating anything (voices, music, renders).
