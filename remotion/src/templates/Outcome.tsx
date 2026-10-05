import React from "react";
import { AbsoluteFill, Sequence, interpolate, staticFile, useCurrentFrame } from "remotion";
import { Audio } from "@remotion/media";
import { measureText } from "@remotion/layout-utils";
import { C, FONT, FPS, Format, SAFE, photo } from "../brand";
import { EndCard, OUT, Photo, clamp, useLayout } from "../components";
import { FontGate, QA, fitBalanced } from "../qa";

export type Timing = { text: string; start: number; end: number };
export type OutcomeProps = {
  format: Format;
  hook: string; // opening line on screen
  photos: string[]; // one per narrated moment (timings[0..5])
  vo: string; // public/audio/vo_<vo>.wav
  music: string; // fitted, ducked file in public/audio
  credit: string;
  timings: Timing[]; // 6 moments, then "Tailor Homes.", then the tagline
};

export const O_LEAD = 0.6; // s of picture before the first word
export const O_HOLD = 1.8; // s the sign-off holds after the last word
export const outcomeDuration = (t: Timing[]) => Math.ceil((O_LEAD + t[t.length - 1].end + O_HOLD) * FPS);

/** Caption card that hugs its text (max two lines), in the website's card style. */
const Caption: React.FC<{ text: string; sub?: string; maxW: number; maxSize: number; left: number; bottom: number; k: number }> = ({
  text,
  sub,
  maxW,
  maxSize,
  left,
  bottom,
  k,
}) => {
  const pad = 40;
  const { fontSize, lines } = fitBalanced({ text, width: maxW - 2 * pad, maxLines: 2, maxSize, family: FONT.sans, weight: 700 });
  const subSize = Math.round(maxSize * 0.56);
  const widths = lines.map((l) => measureText({ text: l, fontFamily: FONT.sans, fontWeight: 700, fontSize }).width);
  if (sub) widths.push(measureText({ text: sub, fontFamily: FONT.sans, fontWeight: 500, fontSize: subSize }).width);
  const w = Math.min(maxW, Math.max(...widths) + 2 * pad + 6);
  return (
    <div
      data-qa-box="caption"
      style={{
        position: "absolute",
        left,
        bottom,
        width: w,
        boxSizing: "border-box",
        padding: `30px ${pad}px 34px`,
        background: C.card,
        borderRadius: 22,
        boxShadow: "0 12px 40px rgba(0,0,0,0.18)",
        opacity: k,
        translate: `0 ${(1 - k) * 20}px`,
      }}
    >
      <div style={{ height: 3, width: 64, background: C.accent, marginBottom: 18 }} />
      <div data-qa="caption-text" style={{ fontFamily: FONT.sans, fontWeight: 700, fontSize, color: C.ink, lineHeight: 1.1 }}>
        {lines.map((l, i) => (
          <div key={i} style={{ whiteSpace: "nowrap" }}>
            {l}
          </div>
        ))}
      </div>
      {sub ? (
        <div data-qa="caption-sub" style={{ fontFamily: FONT.sans, fontWeight: 500, fontSize: subSize, color: C.body, marginTop: 12, whiteSpace: "nowrap" }}>
          {sub}
        </div>
      ) : null}
    </div>
  );
};

/** Sells the stay, not the features: full-bleed real photos, one moment per narrated line. */
export const Outcome: React.FC<OutcomeProps> = (p) => (
  <FontGate>
    <OutcomeInner {...p} />
  </FontGate>
);

const OutcomeInner: React.FC<OutcomeProps> = ({ format, hook, photos, vo, music, credit, timings }) => {
  const frame = useCurrentFrame();
  const { height, tall } = useLayout();
  const s = SAFE[format];
  const f = (sec: number) => Math.round((O_LEAD + sec) * FPS);
  const starts = photos.map((_, i) => (i === 0 ? 0 : f(timings[i].start) - 6));
  const endAt = f(timings[6].start) - 6;
  return (
    <AbsoluteFill style={{ background: C.ink }}>
      <Audio src={staticFile(`audio/${music}`)} />
      <Sequence from={Math.round(O_LEAD * FPS)} layout="none">
        <Audio src={staticFile(`audio/vo_${vo}.wav`)} />
      </Sequence>
      {photos.map((id, i) => {
        const st = starts[i];
        const next = starts[i + 1] ?? endAt;
        if (frame < st || frame > next + 12) return null;
        const k = i === 0 ? interpolate(frame, [0, 10], [0, 1], clamp) : interpolate(frame, [st, st + 12], [0, 1], { ...clamp, easing: OUT });
        const push = interpolate(frame, [st, next + 12], [1, 1.05], clamp);
        return (
          <AbsoluteFill key={id} style={{ opacity: k }}>
            <Photo src={photo(id)} scale={push} style={{ inset: 0 }} />
          </AbsoluteFill>
        );
      })}
      {timings.slice(0, 6).map((t, i) => {
        const a = i === 0 ? 6 : f(t.start) - 4;
        const b = i < 5 ? f(timings[i + 1].start) - 8 : endAt;
        if (frame < a || frame >= b) return null;
        const k = interpolate(frame, [a, a + 12], [0, 1], { ...clamp, easing: OUT }) * interpolate(frame, [b - 7, b - 1], [1, 0], clamp);
        return (
          <Caption
            key={i}
            text={i === 0 ? hook : t.text}
            sub={i === 0 ? t.text : undefined}
            maxW={s.x1 - s.x0}
            maxSize={i === 0 ? (tall ? 70 : 62) : tall ? 58 : 52}
            left={s.x0}
            bottom={height - s.y1}
            k={k}
          />
        );
      })}
      <Sequence from={endAt} layout="none">
        <EndCard start={0} tagline={timings[7].text} credit={credit} safe={s} />
      </Sequence>
      <QA safe={s} />
    </AbsoluteFill>
  );
};
