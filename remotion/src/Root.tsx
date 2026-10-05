import React from "react";
import { Composition } from "remotion";
import { FORMATS, FPS, Format, Lang } from "./brand";
import { Showcase } from "./templates/Showcase";
import { Outcome, outcomeDuration } from "./templates/Outcome";
import { Checklist, checklistDuration } from "./templates/Checklist";
import showcase from "../data/showcase.json";
import outcome from "../data/outcome.json";
import checklist from "../data/checklist.json";
import voIt from "../data/vo/outcome_it.json";
import voEn from "../data/vo/outcome_en.json";

// Three polished videos, each in IT/EN and 9:16 (Reels) / 4:5 (feed). Ids: <video>-<lang>-<format>.
// Music: client-supplied licensed tracks (data/music_credits.json), fitted by scripts/fit_music.py.
const formats: Format[] = ["9x16", "4x5"];
const langs: Lang[] = ["it"]; // Italian only (client, 2026-10)
const CREDIT = {
  zay: "Music: ZAY YEZ by ZiMPL · CC BY 3.0",
  cat: "Music: cat cafe by Snoozy Beats · CC BY 3.0",
};
const VO = { it: voIt, en: voEn };

export const RemotionRoot: React.FC = () => (
  <>
    {langs.flatMap((lang) =>
      formats.map((format) => (
        <Composition
          key={`showcase-${lang}-${format}`}
          id={`showcase-${lang}-${format}`}
          component={Showcase}
          durationInFrames={Math.ceil(showcase.duration * FPS)}
          fps={FPS}
          {...FORMATS[format]}
          defaultProps={{
            format,
            ...showcase[lang],
            photos: showcase.photos.map((p) => ({ id: p.id, label: p[lang], pos: p.pos })),
            cuts: showcase.cuts,
            end: showcase.end,
            music: "fit_showcase.wav",
            credit: CREDIT.zay,
          }}
        />
      )),
    )}
    {langs.flatMap((lang) =>
      formats.map((format) => (
        <Composition
          key={`outcome-${lang}-${format}`}
          id={`outcome-${lang}-${format}`}
          component={Outcome}
          durationInFrames={outcomeDuration(VO[lang].timings)}
          fps={FPS}
          {...FORMATS[format]}
          defaultProps={{
            format,
            hook: outcome.hook[lang],
            photos: outcome.photos,
            vo: outcome.vo[lang],
            music: `fit_outcome_${lang}.wav`,
            credit: CREDIT.cat,
            timings: VO[lang].timings,
          }}
        />
      )),
    )}
    {langs.flatMap((lang) =>
      formats.map((format) => (
        <Composition
          key={`checklist-${lang}-${format}`}
          id={`checklist-${lang}-${format}`}
          component={Checklist}
          durationInFrames={checklistDuration(checklist.photos.length)}
          fps={FPS}
          {...FORMATS[format]}
          defaultProps={{ format, ...checklist[lang], photos: checklist.photos, music: "fit_checklist.wav", credit: CREDIT.cat }}
        />
      )),
    )}
  </>
);
