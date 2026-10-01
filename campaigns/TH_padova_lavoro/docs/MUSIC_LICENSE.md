# Music and sound — source and licence record

## Music bed: “Padova Morning” (working title), original composition

| | |
|---|---|
| Source | Composed and synthesised for this film by `scripts/make_audio.py` (function `music()`), 2026-10-01 |
| Method | Code-generated synthesis: FM electric piano, filtered-saw pad, sine bass, synthesised kick/rim/shaker and an algorithmic reverb. **No samples, loops, presets, stock libraries or third-party recordings are used.** |
| Musical content | 100 BPM, F major, progression Fmaj9–Am7–Dm9–B♭maj9, resolving to Fmaj9 on the end card; instrumental, no vocals |
| Rights | Original work made for Tailor Homes as part of this commission. No third-party licence is involved, and it may be used in paid advertising on any platform. No registration with a collecting society (e.g. SIAE) has been made. |
| Files | `audio/stems/music_bed_full.wav` (un-ducked), `audio/stems/music_bed_ducked.wav` (as used in the mix) |

Why this route: no licensed track was supplied. The only music generator
available in this session (Higgsfield `sonilo_music`) is restricted by
its provider to its game-building pipeline and may not be used for standalone
audio. No commercial song or unverified track was used.

**Not yet listened to.** The production environment has no audio playback. The bed
was checked by measurement only: loudness, true peak, spectrogram, fade-out and
a clean ending. Have someone listen before publication. If it is not good enough,
replace `audio/stems/music_bed_full.wav` with a track licensed for advertising
(e.g. from Artlist, Epidemic Sound or Musicbed under a licence that covers paid
social ads), record its licence ID here, and re-run the mix. To use an external
file, change `music()` in `make_audio.py` to load it.

## Sound effects

All sound effects are also synthesised in `make_audio.py` (`sfx_sound()`): suitcase
roll, soft landing thuds, transition whooshes, key jingle, door latch and paper
page. There are no third-party recordings. Placement and levels are in
`timeline.json → sfx`.

## Narration

AI-generated speech via Higgsfield (ElevenLabs engine). See `VOICEOVER.md`.

## Mix

- Music is automatically ducked by 9 dB under every narration segment, with
  0.22 s ramps (`duck_curve()`).
- Final mix is normalised to −14 LUFS integrated, −1.5 dBTP, with a 2 s fade at the
  end. Measurements are in `audio/mix/loudness_report.json`.
