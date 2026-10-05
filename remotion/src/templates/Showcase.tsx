import React from "react";
import { AbsoluteFill, Sequence, interpolate, staticFile, useCurrentFrame } from "remotion";
import { Audio } from "@remotion/media";
import { C, FONT, FPS, Format, SAFE, photo } from "../brand";
import { EndCard, INOUT, Label, OUT, Photo, clamp, useLayout } from "../components";
import { FitText, FontGate, QA } from "../qa";

export type ShowcasePhoto = { id: string; label: string; pos: string };
export type ShowcaseProps = {
  format: Format;
  eyebrow: string;
  title: string;
  tag: string;
  photos: ShowcasePhoto[];
  cuts: number[]; // seconds: each photo starts on a kick of the track
  end: number; // seconds: sign-off card
  music: string; // fitted file in public/audio
  credit: string;
};

/** Best-of showcase: one photo per musical phrase, every cut on a kick. */
export const Showcase: React.FC<ShowcaseProps> = (p) => (
  <FontGate>
    <ShowcaseInner {...p} />
  </FontGate>
);

const ShowcaseInner: React.FC<ShowcaseProps> = ({ format, eyebrow, title, tag, photos, cuts, end, music, credit }) => {
  const frame = useCurrentFrame();
  const { width, tall } = useLayout();
  const s = SAFE[format];
  const f = (sec: number) => Math.round(sec * FPS);
  const cutF = cuts.map(f);
  const endF = f(end);
  const win = tall ? { y0: 540, y1: 1740 } : { y0: 300, y1: 1190 };
  const winH = win.y1 - win.y0;
  // the window opens on the drop and closes into the sign-off
  const open = interpolate(frame, [cutF[0] - 9, cutF[0]], [0, 1], { ...clamp, easing: OUT });
  const close = interpolate(frame, [endF - 8, endF + 4], [0, 1], { ...clamp, easing: INOUT });
  const h = winH * open * (1 - close);
  const idx = Math.max(0, cutF.filter((c) => frame >= c).length - 1);
  const cur = photos[idx];
  const since = frame - cutF[idx];
  const life = (cutF[idx + 1] ?? endF) - cutF[idx];
  const settle = interpolate(since, [0, 10], [1.035, 1], { ...clamp, easing: OUT });
  const push = interpolate(since, [0, life], [1, 1.03], clamp);
  const tagIn = interpolate(since, [2, 9], [0, 1], { ...clamp, easing: OUT });
  const headK = interpolate(frame, [2, 20], [0, 1], { ...clamp, easing: OUT });
  const showTags = h > winH * 0.9 && frame >= cutF[0];
  return (
    <AbsoluteFill style={{ background: C.card }}>
      <Audio src={staticFile(`audio/${music}`)} />
      {/* header */}
      <div style={{ position: "absolute", left: s.x0, top: s.y0, opacity: headK * (1 - close), translate: `0 ${(1 - headK) * 16}px` }}>
        <Label>{eyebrow}</Label>
        <FitText text={title} width={s.x1 - s.x0} maxLines={2} maxSize={tall ? 92 : 76} family={FONT.sans} qa="title" style={{ color: C.ink, marginTop: 14, letterSpacing: "-0.01em" }} />
      </div>
      {/* photo window: full width, hard cuts on the kicks */}
      <div style={{ position: "absolute", left: 0, width, top: win.y0 + (winH - h) / 2, height: h, overflow: "hidden" }}>
        <Photo src={photo(cur.id)} scale={settle * push} position={cur.pos} style={{ left: 0, width, top: -(winH - h) / 2, height: winH }} />
      </div>
      {showTags ? (
        <>
          <div
            data-qa-box="room-tag"
            style={{ position: "absolute", left: s.x0, top: (tall ? 1500 : win.y1 - 24) - 66, height: 66, padding: "0 26px", display: "flex", alignItems: "center", background: C.card, borderRadius: 14, opacity: tagIn }}
          >
            <Label size={26} qa="room-label" style={{ color: C.ink }}>
              {cur.label}
            </Label>
          </div>
          <div
            data-qa-box="counter-tag"
            style={{ position: "absolute", right: width - s.x1, top: win.y0 + 28, height: 66, padding: "0 22px", display: "flex", alignItems: "baseline", gap: 8, background: C.card, borderRadius: 14 }}
          >
            <div data-qa="counter" style={{ display: "flex", alignItems: "baseline", gap: 8, marginTop: 8 }}>
              <span style={{ fontFamily: FONT.serif, fontStyle: "italic", fontSize: 50, color: C.accent, lineHeight: 1 }}>{String(idx + 1).padStart(2, "0")}</span>
              <span style={{ fontFamily: FONT.sans, fontWeight: 500, fontSize: 24, color: C.body }}>/ {String(photos.length).padStart(2, "0")}</span>
            </div>
          </div>
        </>
      ) : null}
      <Sequence from={endF} layout="none">
        <EndCard start={0} tagline={tag} credit={credit} safe={s} />
      </Sequence>
      <QA safe={s} />
    </AbsoluteFill>
  );
};
