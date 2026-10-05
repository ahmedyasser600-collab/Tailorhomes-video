import React from "react";
import { Composition } from "remotion";
import { FORMATS, FPS, Format, Lang } from "./brand";
import { Gallery, galleryDuration } from "./templates/Gallery";
import { Services, servicesDuration, Timing } from "./templates/Services";
import { Checklist, checklistDuration } from "./templates/Checklist";
import { Outcome, outcomeDuration } from "./templates/Outcome";
import galleries from "../data/galleries.json";
import services from "../data/services.json";
import checklist from "../data/checklist.json";
import outcome from "../data/outcome.json";
import vo01 from "../data/vo/services_01.json";
import vo02 from "../data/vo/services_02.json";
import vo03 from "../data/vo/services_03.json";
import vo04 from "../data/vo/services_04.json";
import voOutIt from "../data/vo/outcome_it.json";
import voOutEn from "../data/vo/outcome_en.json";

// Every video is one composition fed by data/*.json. Ids: <template>-<variant>-<lang>-<format>.
const formats: Format[] = ["9x16", "4x5"];
const langs: Lang[] = ["it", "en"];
const VO: Record<string, { timings: Timing[] }> = {
  services_01: vo01,
  services_02: vo02,
  services_03: vo03,
  services_04: vo04,
  outcome_it: voOutIt,
  outcome_en: voOutEn,
};
type G = { it: string; en: string; photos: string[] };

export const RemotionRoot: React.FC = () => {
  return (
    <>
      {/* A: themed galleries, music only, IT + EN */}
      {Object.entries(galleries as Record<string, G>).flatMap(([key, g]) =>
        langs.flatMap((lang) =>
          formats.map((format) => (
            <Composition
              key={`gallery-${key}-${lang}-${format}`}
              id={`gallery-${key}-${lang}-${format}`}
              component={Gallery}
              durationInFrames={galleryDuration(g.photos.length)}
              fps={FPS}
              {...FORMATS[format]}
              defaultProps={{ format, lang, title: g[lang], photos: g.photos, music: `music_gallery_${g.photos.length}.wav` }}
            />
          )),
        ),
      )}
      {/* B: owner services series, one video per pillar, IT narration */}
      {services.items.flatMap((it) =>
        formats.map((format) => (
          <Composition
            key={`services-${it.key}-it-${format}`}
            id={`services-${it.key}-it-${format}`}
            component={Services}
            durationInFrames={servicesDuration(VO[it.vo].timings)}
            fps={FPS}
            {...FORMATS[format]}
            defaultProps={{
              format,
              num: it.key,
              title: it.title,
              rows: it.rows,
              photos: it.photos,
              label: services.label,
              endTagline: services.endTagline,
              vo: it.vo,
              music: `music_${it.vo}.wav`,
              timings: VO[it.vo].timings,
            }}
          />
        )),
      )}
      {/* C: checklist, music only, IT + EN */}
      {langs.flatMap((lang) =>
        formats.map((format) => (
          <Composition
            key={`checklist-ready-${lang}-${format}`}
            id={`checklist-ready-${lang}-${format}`}
            component={Checklist}
            durationInFrames={checklistDuration(checklist.photos.length)}
            fps={FPS}
            {...FORMATS[format]}
            defaultProps={{ format, ...checklist[lang], photos: checklist.photos, music: "music_checklist.wav" }}
          />
        )),
      )}
      {/* D: outcome reel, narrated IT + EN, three hook variants for ad tests */}
      {Object.entries(outcome.hooks).flatMap(([hk, h]) =>
        langs.flatMap((lang) =>
          formats.map((format) => {
            const name = outcome.vo[lang];
            return (
              <Composition
                key={`outcome-${hk}-${lang}-${format}`}
                id={`outcome-${hk}-${lang}-${format}`}
                component={Outcome}
                durationInFrames={outcomeDuration(VO[name].timings)}
                fps={FPS}
                {...FORMATS[format]}
                defaultProps={{ format, hook: h[lang], photos: outcome.photos, vo: name, music: `music_${name}.wav`, timings: VO[name].timings }}
              />
            );
          }),
        ),
      )}
    </>
  );
};
