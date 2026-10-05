import React from "react";
import { AbsoluteFill, Sequence, interpolate, staticFile, useCurrentFrame } from "remotion";
import { Audio } from "@remotion/media";
import { C, FONT, FPS, Format, SAFE, photo } from "../brand";
import { EndCard, OUT, PhotoCard, useLayout } from "../components";
import type { Timing } from "./Services";

export type OutcomeProps = {
  format: Format;
  hook: string; // opening on-screen line (A/B test variable)
  photos: string[]; // one per narrated moment (timings[0..5])
  vo: string;
  music: string;
  timings: Timing[]; // 6 moments, then brand, then tagline
};

export const O_LEAD = 0.6;
export const O_HOLD = 2.6;
export const outcomeDuration = (t: Timing[]) => Math.ceil((O_LEAD + t[t.length - 1].end + O_HOLD) * FPS);

const clamp = { extrapolateLeft: "clamp", extrapolateRight: "clamp" } as const;

/** Sells the stay, not the features: full-bleed real photos, one moment per line. */
export const Outcome: React.FC<OutcomeProps> = ({ format, hook, photos, vo, music, timings }) => {
  const frame = useCurrentFrame();
  const { width, height, tall } = useLayout();
  const s = SAFE[format];
  const f = (sec: number) => Math.round((O_LEAD + sec) * FPS);
  const starts = photos.map((_, i) => (i === 0 ? 0 : f(timings[i].start) - 8));
  const endAt = f(timings[6].start) - 6;
  const tagline = timings[7].text;
  const right = width - (tall ? 1020 : s.x1);
  const capTop = tall ? 1210 : 1010;
  return (
    <AbsoluteFill style={{ background: C.ink }}>
      <Audio src={staticFile(`audio/${music}`)} volume={0.55} />
      <Sequence from={Math.round(O_LEAD * FPS)} layout="none">
        <Audio src={staticFile(`audio/vo_${vo}.wav`)} />
      </Sequence>
      {photos.map((id, i) => {
        const st = starts[i];
        const next = starts[i + 1] ?? endAt;
        const k = i === 0 ? interpolate(frame, [0, 12], [0, 1], clamp) : interpolate(frame, [st, st + 14], [0, 1], { ...clamp, easing: OUT });
        if (frame < st || frame > next + 16) return null;
        return (
          <AbsoluteFill key={id} style={{ opacity: k }}>
            <PhotoCard src={photo(id)} start={st} life={next - st + 16} radius={0} style={{ inset: 0 }} />
          </AbsoluteFill>
        );
      })}
      {/* hook (first moment) and captions, in the website's card style */}
      {timings.slice(0, 6).map((t, i) => {
        const a = i === 0 ? 4 : f(t.start) - 4;
        const b = i < 5 ? f(timings[i + 1].start) - 8 : endAt - 4;
        const k = interpolate(frame, [a, a + 12], [0, 1], { ...clamp, easing: OUT });
        const ko = interpolate(frame, [b - 6, b + 4], [0, 1], clamp);
        if (frame < a || frame > b + 4) return null;
        return (
          <div
            key={i}
            style={{
              position: "absolute",
              left: s.x0,
              right,
              top: capTop - (i === 0 ? (tall ? 150 : 120) : 0),
              background: C.card,
              borderRadius: 22,
              padding: tall ? "34px 40px" : "30px 36px",
              opacity: k * (1 - ko),
              translate: `0 ${(1 - k) * 24}px`,
              boxShadow: "0 12px 40px rgba(0,0,0,0.18)",
            }}
          >
            <div style={{ height: 3, width: 70, background: C.accent, marginBottom: 18 }} />
            {i === 0 ? (
              <>
                <div style={{ fontFamily: FONT.sans, fontWeight: 700, fontSize: tall ? 66 : 58, color: C.ink, lineHeight: 1.1 }}>{hook}</div>
                <div style={{ fontFamily: FONT.sans, fontWeight: 500, fontSize: tall ? 36 : 32, color: C.body, marginTop: 14 }}>{t.text}</div>
              </>
            ) : (
              <div style={{ fontFamily: FONT.sans, fontWeight: 700, fontSize: tall ? 54 : 48, color: C.ink, lineHeight: 1.12 }}>{t.text}</div>
            )}
          </div>
        );
      })}
      <Sequence from={endAt} layout="none">
        <EndCard start={0} tagline={tagline} width={width} height={height} />
      </Sequence>
    </AbsoluteFill>
  );
};
