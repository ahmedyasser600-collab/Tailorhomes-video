# Tailor Homes: Remotion videos

Italian only (client decision). The checklist is the approved video and the style reference; the client rejected the outcome reel. Each video renders in **9:16** (Reels, Stories, TikTok) and **4:5** (Meta feed).

| Id | Video | Music (client-supplied, CC BY 3.0) |
|---|---|---|
| `showcase-<it\|en>-<format>` | Best-of showcase. It opens on the house, then cuts through 10 rooms, each cut on a kick of the track. | ZAY YEZ by ZiMPL |
| `outcome-<it\|en>-<format>` | Outcome reel. It sells the stay (arrive, door, coffee, work, rest, evening), narrated by Gia (IT) and Sienna (EN). | cat cafe by Snoozy Beats |
| `checklist-<it\|en>-<format>` | "Pronta per il prossimo ospite" / "Ready for the next guest". Six items tick off on the beat over real photos. | cat cafe by Snoozy Beats |

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
python scripts/vo_timing.py outcome_it outcome_en          # after a new narration
python scripts/fit_music.py <track> <seconds> <out> [...]  # see data/*.json for the start points used
node scripts/render-all.mjs [filter]                       # -> out/<id>.mp4 + out/qa-report.txt
```
