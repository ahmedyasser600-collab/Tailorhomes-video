# Review notes and verification

## Status

| Item | Status |
|---|---|
| 3D shots (Blender) | Done. 4 scenes, 814 frames incl. handles |
| Review preview 540×960 | Done: `preview/TH_Padova_Lavoro_PREVIEW_540x960.mp4` |
| Final 1080×1920 | Done: `final/TH_Padova_Lavoro_1080x1920.mp4` (Cycles 16 spp + OIDN, 814 frames at 27–43 s each) |
| Narration | AI-generated (Higgsfield / ElevenLabs engine). **Not yet listened to by a person** |
| Music | Original synthesised bed, owned outright. **Not yet listened to by a person** |
| Subtitles | Done: `subtitles/TH_Padova_Lavoro_it.srt`, also burned in |

## Checks performed (by inspection, not playback)

- **Masters preserved.** `git status` shows nothing under `assets/`, `exports/`,
  `animation/`, `brand/` or the master `.blend` modified. Assets are appended into
  `blender/TH_padova_lavoro_shots.blend`.
- **Framing.** Stills from every shot were rendered and inspected, and corrected
  in four rounds: black floors caused by a backdrop at floor level, an early
  white-out at the door, the end-card logo entering the right UI rail, an orphaned
  headline word, a soft focus pull and the panel covering the calendar title.
- **Safe areas.** The end card and all headline and caption positions were checked
  with `--safe-guides` against Reels UI zones (top 250 px, bottom 420 px, right
  150 px). The small logo bug (y 262–347) sits just inside the top safe line.
- **Logo fidelity.** The supplied `01-noBgColor.svg` is rasterised unmodified with
  librsvg. Aspect ratio is 2.484 vs 2.482 for the supplied reference PNG (rounding),
  and mask overlap with that reference is 95.4%; the difference is antialiasing on
  the thin distressed ring. Scaling is uniform only, and nothing is retyped or
  traced. The small bug is `04-symbol.svg` on a cream badge for contrast.
- **Italian text.** Headlines, tagline and CTA match the brief exactly. Captions
  match the narration script, with the URL written as “tailorhomes.it”. Caption
  reading speed is 10.9–16.6 characters/s, with a maximum line length of 30
  characters.
- **Audio, by measurement only.** Mix −13.9 LUFS / −1.4 dBTP. Narration sits about
  12 dB above the ducked music, and nothing clips. Sentence placement was verified
  on the waveform and spectrogram. The film ends with a 2 s music fade.
- **Exported preview.** ffprobe and a full decode give H.264 High, 540×960, 30 fps,
  900 frames, 30.0 s, AAC 48 kHz stereo, with zero A/V duration difference. A
  one-frame-per-second contact sheet of the decoded file was inspected
  (`docs/verification/`).
- **Exported final.** ffprobe and a full decode give H.264 High, 1080×1920, 30 fps,
  yuv420p, 900 frames, 30.0 s, 8.5 MB; AAC 48 kHz stereo, −13.9 LUFS, −1.5 dBTP,
  zero A/V duration difference. All 814 rendered PNGs were validated as
  1080×1920. Its one-frame-per-second contact sheet and four full-resolution frames
  extracted from the MP4 (8.8, 16.5, 23.9 and 28.0 s) were inspected. Both
  post-preview fixes are confirmed: the lounge is in focus at 8–10 s, and the
  calendar sits below the headline panel.
- **Claims.** No prices, availability, ratings, testimonials or guarantees. The
  calendar dates and folder are the library’s “ESEMPIO” sample props. “Rendering
  3D illustrativo” is shown over the interiors.

## Typography revision (client feedback, 2 Oct)

Cream headline boxes and the logo badge were removed. Headlines are now bold Jost
with kinetic word reveals and accent underlines; captions are bold with a stroke.
Text colour adapts to the background (measured per frame: navy on light, cream on
dark). Layout changes:
- The logo bug moved to the top-right.
- Headlines start at y 272, which clears the suitcase handle in shot 1 and the
  calendar in shot 4.
- “Trova il tuo spazio.” enters at 7.7 s, once the keys have lifted away, so it
  never covers them.
- “Soggiorni brevi e medi.” exits at 22.75 s, before the camera push brings the
  calendar into the text area.

Re-encoded and re-verified: 900 frames, 30.0 s, A/V aligned, −13.9 LUFS. The
contact sheet was inspected again. The preview is now also built from the final
plates, so it matches the final.

## Not verified — needs a person

1. **Listen to the narration.** Check pronunciation of “Tailor Homes” and
   “tailorhomes punto it”, the naturalness of the delivery, and whether you prefer
   take 1 (female) or take 2 (male, used). No speech recognition or playback was
   available here.
2. **Listen to the music bed and the mix** on a phone speaker. If the generated bed
   isn’t good enough, replace it with a track licensed for ads; see
   `MUSIC_LICENSE.md`.
3. **Real-time playback** of the final MP4: motion smoothness and transitions at
   speed. Only decoded frames were inspected.
4. **Confirm Higgsfield commercial-use terms** on your account before paid
   publication (`VOICEOVER.md`).

## Known limitations

- The models are simplified starter-library illustrations; the furniture is
  stylised.
- The “PADOVA” label on the marker platform is small and not legible in the film.
  The headline carries the location.
- The S2→S3 transition is a match dissolve from the warm interior to the neutral
  dollhouse lighting, so there is a brief shift in colour temperature, as intended.
- The final render was interrupted twice by container restarts and resumed from
  the completed frames. Every frame was validated after each resume.
