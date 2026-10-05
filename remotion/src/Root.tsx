import React from "react";
import { Composition } from "remotion";
import { FORMATS, FPS, Format } from "./brand";
import { Checklist, checklistDuration } from "./templates/Checklist";
import { ListReel, listDuration } from "./templates/ListReel";
import checklist from "../data/checklist.json";
import showcase from "../data/showcase.json";
import services from "../data/services.json";

// Italian only (client, 2026-10). All videos share the approved checklist style.
// Music: client-supplied licensed tracks (data/music_credits.json), fitted by scripts/fit_music.py.
const formats: Format[] = ["9x16", "4x5"];
const CREDIT = {
  zay: "Music: ZAY YEZ by ZiMPL · CC BY 3.0",
  cat: "Music: cat cafe by Snoozy Beats · CC BY 3.0",
};

export const RemotionRoot: React.FC = () => (
  <>
    {formats.map((format) => (
      <Composition
        key={`checklist-it-${format}`}
        id={`checklist-it-${format}`}
        component={Checklist}
        durationInFrames={checklistDuration(checklist.photos.length)}
        fps={FPS}
        {...FORMATS[format]}
        defaultProps={{ format, ...checklist.it, photos: checklist.photos, music: "fit_checklist.wav", credit: CREDIT.cat }}
      />
    ))}
    {formats.map((format) => (
      <Composition
        key={`servizi-it-${format}`}
        id={`servizi-it-${format}`}
        component={ListReel}
        durationInFrames={listDuration(services.end, services.hold)}
        fps={FPS}
        {...FORMATS[format]}
        defaultProps={{ format, ...services, music: "fit_servizi.wav", credit: CREDIT.cat }}
      />
    ))}
    {formats.map((format) => (
      <Composition
        key={`galleria-it-${format}`}
        id={`galleria-it-${format}`}
        component={ListReel}
        durationInFrames={listDuration(showcase.end, showcase.hold)}
        fps={FPS}
        {...FORMATS[format]}
        defaultProps={{ format, ...showcase, music: "fit_galleria.wav", credit: CREDIT.zay }}
      />
    ))}
  </>
);
