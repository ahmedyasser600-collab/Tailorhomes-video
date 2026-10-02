# Film 03: Tailor Homes per proprietari

An Italian vertical ad for **property owners** in Padova (1080×1920, 60 fps, 30.45 s).

## Approach
- **Photo showcase.** Full-bleed real Tailor Homes photos, cropped and scaled only.
  - Each photo has one slow, even push-in. There are no pans, drifts or re-crops.
  - Photos change with a 0.6 s cross-dissolve.
- **Captions.** One navy caption card holds every line of text, opening at the start and closing into the end card.
- **Chapters.** The four chapters copy the numbering and titles of https://tailorhomes.it/our-servies/:
  - italic serif numerals
  - bold sans titles
  - tracked service labels
- **End card.** The last photo stays still while a window closes around it and settles into a frame on cream. Then come the tagline, the supplied logo and **tailorhomes.it**.

## Claims
Every service claim comes from the client's services page, supplied as a screenshot. tailorhomes.it itself is blocked in this environment.

| On screen / voice | Source line on the page |
|---|---|
| Pulizie, manutenzione, ospiti | 01 Operatività e gestione: pulizie professionali e lavanderia; manutenzione ordinaria e straordinaria; gestione ospiti e comunicazione |
| Home staging, fotografia professionale | 02 Valorizzazione e presentazione: interior design e home staging; fotografia professionale |
| Canali online, marketing, distribuzione | 03 Marketing e performance: gestione canali e presenza online; strategie di marketing e distribuzione |
| Consulenza immobiliare, legale, fiscale | 04 Supporto tecnico e consulenziale: consulenza immobiliare; gestione aspetti legali e fiscali |

The film makes no claims about earnings, occupancy, prices or guarantees.

## Voice-over
The client approved this script. It is read by an ElevenLabs preset voice (“Gia”) via Higgsfield, chosen by the client from three samples kept in `audio/voice/samples/`.

> Hai una casa a Padova? Tailor Homes se ne prende cura, ogni giorno. Pulizie, manutenzione, ospiti: ci pensiamo noi. Valorizziamo la tua casa, con home staging e fotografia professionale. Gestiamo canali online, marketing e distribuzione. E ti affianchiamo nella consulenza immobiliare, legale e fiscale. La tua casa, su misura. Tailor Homes.

## Shotlist (times come from `timing.json`)
| Scene | Photos | Card |
|---|---|---|
| open | S. Eufemia living room → Via Nullo living room | PER PROPRIETARI / *Hai una casa a Padova?* → *Ce ne prendiamo cura, ogni giorno.* |
| ch1 | Vicolo Romano bedroom → Colore & Design kitchen | 01 Operatività e gestione: PULIZIE · MANUTENZIONE · OSPITI |
| ch2 | S. Eufemia orange wall → hammock loft | 02 Valorizzazione: HOME STAGING · FOTOGRAFIA |
| ch3 | Via Nullo dining → table detail | 03 Marketing e performance: CANALI ONLINE · DISTRIBUZIONE |
| ch4 | S. Eufemia beams bedroom → Colore & Design reading corner | 04 Supporto e consulenza: IMMOBILIARE · LEGALE · FISCALE |
| close | S. Eufemia dining | *La tua casa, su misura.* |
| endcard | Same photo, framed on cream | *La tua casa, su misura.* / logo / tailorhomes.it |

## Audio
- **Music.** Original and code-synthesised, with no samples:
  - 72 BPM, F major (Fmaj9, Dm9, B♭maj7, Csus4)
  - felt piano, legato strings and cello; no drums
  - a rolled Fmaj9 chord on the end card
- **Effects.** Four very soft whooshes on the chapter changes and one on the end card.
- **Mix.**
  - Music is ducked 7.5 dB under the voice.
  - Final level: −14.0 LUFS integrated, −1.5 dBTP.
  - Stems are in `audio/stems/`.

## Rebuild
```
python scripts/analyze_vo.py && python scripts/make_audio.py && python scripts/render.py --mode animatic
python scripts/render.py --mode final
```

## Open items
- Nobody has listened to the music and the mix yet.
- The services page wording was checked against the client's screenshot, not the live site.
