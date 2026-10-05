# Tailor Homes: Remotion videos

Italian only (client decision). The checklist is the approved video and the style reference; the client rejected the outcome reel. Each video renders in **9:16** (Reels, Stories, TikTok) and **4:5** (Meta feed).

| Id | Video | Music (client-supplied, CC BY 3.0) |
|---|---|---|
| `checklist-it-<format>` | "Pronta per il prossimo ospite". Six items tick off on the beat over real photos. This is the approved style reference. | cat cafe by Snoozy Beats |
| `servizi-it-<format>` | "Cosa facciamo per la tua casa". It shows the four service pillars from the website (01–04), each with a detail line, over real photos. | cat cafe by Snoozy Beats |
| `galleria-it-<format>` | "Scegli la tua casa a Padova". Six rooms (01–06), each changing on a kick of the track. No apartment names. | ZAY YEZ by ZiMPL |
| `ospedale-it-<format>` | "Soggiorni a Padova". For people in Padova to be near someone in hospital. Full-screen photos, narrated by Gia. Only some apartments are near the hospital, so the voiceover says "anche vicino all'ospedale". No hospital logo and no partnership claim. | cat cafe by Snoozy Beats |
| `eventi-it-<format>` | "Prossimi eventi a Padova". Seven upcoming fairs and festivals, one per photo, each cut on the beat. To reuse it, edit the dates in `data/eventi.json` (re-check them before each post). | ZAY YEZ by ZiMPL |

`checklist` uses `src/templates/Checklist.tsx`. `servizi` and `galleria` use `src/templates/ListReel.tsx`, a sibling with the same page, slots and motion, but numbered rows instead of ticks. Their content lives in `data/services.json` and `data/showcase.json`. `ospedale` and `eventi` use `src/templates/FullBleed.tsx`: full-screen photos with white type on a scrim, the look of Films 01–03.

## Quality system
- **Text fits its space.** Every text element sizes itself to its box (`FitText` / `fitBalanced` in `src/qa.tsx`). Two-line text breaks at the most even point.
- **Layout is checked automatically.** On every rendered frame, `QA` checks that no text overlaps other text, leaves its card or tag, or leaves the safe area. `scripts/render-all.mjs` writes the results to `out/qa-report.txt`, and that report must read "no layout issues".
- **Fonts load first.** Text is measured only after the fonts have loaded (`FontGate`).
- **Photos stay real.** They only scale slowly and evenly, with no pans and no retouching. They come from the client's Drive, and their provenance is recorded in `data/photos.json`.

## Music (client rule, see ../CLAUDE.md)
- Never generate music. Always ask the client, who supplies licensed tracks with credits.
- Supplied tracks live in `public/audio/library/`, and their credits in `data/music_credits.json`.
- `scripts/fit_music.py` fits a track to a video: it sets the start point and length, fades it out, and ducks it under any narration with a sidechain.
- Each end card carries a short credit line. Put the full credit text in the post caption.

## Rebuild
```
python scripts/fit_music.py <track> <seconds> <out> [...]  # see data/*.json for the start points used
node scripts/render-all.mjs [filter]                       # -> out/<id>.mp4 + out/qa-report.txt
```
