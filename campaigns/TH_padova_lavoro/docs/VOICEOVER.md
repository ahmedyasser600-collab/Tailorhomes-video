# Voice-over — Tailor Homes, “A Padova per lavoro”

## What is in the film

The narration is **AI-generated speech**, not a recording of a human voice actor.

| | |
|---|---|
| Service | Higgsfield (connected to the client’s account), model `text2speech_v2`, engine `elevenlabs` |
| Generated | 2026-10-01, 1.05 credits per take |
| Take used | `audio/vo/vo_take2_julian_source.mp3`, preset voice “Julian” (id `95429266-c0ac-4137-a209-63b8812b0f23`), 19.8 s |
| Alternate | `audio/vo/vo_take1_elena_source.mp3`, preset voice “Elena” (id `ca83ca7f-c186-493d-bd69-0d765fa861b2`), 20.7 s |
| Edit | `audio/stems/vo_edit.wav`: the take split at its natural sentence pauses and placed on the film timeline (`timeline.json → voiceover.segments`). There is no time-stretching or pitch change; only a 70 Hz high-pass filter and 20 ms edge fades. |

Built-in preset voices only: no voice cloning and no imitation of a real person.

Rights: Higgsfield’s help centre states that users own their generations and may
use them commercially, on every plan including Basic
([Higgsfield help centre](https://higgsfield.ai/creator-hub/help-center/account-and-privacy/who-owns-my-generations-and-can-i-use-them-commercially)).
**Confirm this against the terms in force on your account before paid publication.**
Higgsfield keeps a licence to use outputs for model training and promotion.

### Checks performed, and what was not checked

- Duration, sample rate, peak level (−1.9 dBFS) and pause structure were measured.
  Take 2 has exactly five sentence-length pauses (0.35–0.54 s), giving six segments.
  This matches the six sentences of the script, at a steady ~17 characters/s.
- **No one has listened to it yet.** The production environment has no audio
  playback, and speech recognition models could not be downloaded, so pronunciation
  (e.g. “Tailor Homes”, “tailorhomes punto it”) is **unverified**. Please listen
  before publication and choose between take 1 and take 2.

## Exact script (unchanged from the brief)

> A Padova per lavoro? Trova uno spazio da chiamare casa. Con Tailor Homes, scopri
> appartamenti arredati per soggiorni brevi e medi. Spazio per rilassarti, vivere e
> lavorare, anche quando sei lontano da casa. Viaggi da solo o cerchi una soluzione
> per il tuo team? Scopri le proposte su tailorhomes punto it.

## Replacing it with a Google AI Studio recording (or a human voice)

Style prompt for AI Studio:

```
Leggi in italiano con una voce adulta naturale e calda, tono calmo e sicuro,
colloquiale, senza enfasi pubblicitaria. Brevi pause naturali tra le frasi.
Pronuncia "Tailor Homes" all'inglese, chiaramente. "tailorhomes punto it" lento e chiaro.
```

Timing the film is built around (each sentence starts at the time shown):

| # | Sentence | Starts at | Fits within |
|---|---|---|---|
| 1 | A Padova per lavoro? | 0.80 s | ≤ 3.8 s |
| 2 | Trova uno spazio da chiamare casa. | 5.50 s | ≤ 4.0 s |
| 3 | Con Tailor Homes, scopri appartamenti arredati per soggiorni brevi e medi. | 10.30 s | ≤ 4.6 s |
| 4 | Spazio per rilassarti, vivere e lavorare, anche quando sei lontano da casa. | 15.00 s | ≤ 4.8 s |
| 5 | Viaggi da solo o cerchi una soluzione per il tuo team? | 20.00 s | ≤ 4.8 s |
| 6 | Scopri le proposte su tailorhomes punto it. | 25.70 s | ≤ 3.6 s |

To swap it in:

1. Export WAV (48 kHz, mono), save it as `audio/vo/<name>.wav` and set
   `timeline.json → voiceover.file`.
2. Find the sentence boundaries:
   `ffmpeg -i audio/vo/<name>.wav -af silencedetect=n=-45dB:d=0.25 -f null -`
3. Update each segment’s `src` [start, end]. Keep `film_start`, or nudge it.
4. Re-run `scripts/make_audio.py`, `scripts/make_srt.py` (if wording changed) and
   `scripts/composite.py`.
