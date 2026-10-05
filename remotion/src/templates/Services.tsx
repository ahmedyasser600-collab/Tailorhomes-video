import React from "react";
import { AbsoluteFill, Sequence, interpolate, staticFile, useCurrentFrame } from "remotion";
import { Audio } from "@remotion/media";
import { C, FONT, FPS, Format, SAFE, photo } from "../brand";
import { EndCard, Label, Numeral, OUT, PhotoCard, Reveal, useLayout } from "../components";

export type Timing = { text: string; start: number; end: number };
export type ServicesProps = {
  format: Format;
  num: string;
  title: string;
  rows: string[];
  photos: string[]; // [hook, row1, row2, row3]
  label: string;
  endTagline: string;
  vo: string; // public/audio/vo_<vo>.wav
  music: string;
  timings: Timing[]; // [hook, row1, row2, row3, close, brand]
};

export const LEAD = 0.5; // s of picture before the first word
export const HOLD = 2.8; // s the end card holds after the last word
export const servicesDuration = (timings: Timing[]) => Math.ceil((LEAD + timings[timings.length - 1].end + HOLD) * FPS);

const clamp = { extrapolateLeft: "clamp", extrapolateRight: "clamp" } as const;

/** One owner-services pillar, laid out like the website's service card. */
export const Services: React.FC<ServicesProps> = ({ format, num, title, rows, photos, label, endTagline, vo, music, timings }) => {
  const frame = useCurrentFrame();
  const { width, height, tall } = useLayout();
  const s = SAFE[format];
  const f = (sec: number) => Math.round((LEAD + sec) * FPS);
  const [hook, r1, r2, r3, close, brand] = timings;
  const rowStarts = [r1, r2, r3].map((t) => f(t.start) - 4);
  const endAt = f(brand.start) - 6;
  // which photo is on top: hook photo, then one per row
  const photoStarts = [0, ...rowStarts];
  const pane = tall ? { y0: 250, y1: 860 } : { y0: 64, y1: 560 };
  const card = tall ? { y0: 900, y1: 1500 } : { y0: 596, y1: 1286 };
  const hookOut = f(r1.start) - 10;
  const bodyIn = interpolate(frame, [hookOut, hookOut + 16], [0, 1], { ...clamp, easing: OUT });
  return (
    <AbsoluteFill style={{ background: C.page }}>
      <Audio src={staticFile(`audio/${music}`)} volume={0.55} />
      <Sequence from={Math.round(LEAD * FPS)} layout="none">
        <Audio src={staticFile(`audio/vo_${vo}.wav`)} />
      </Sequence>
      {/* photo pane: each new photo rises over the last one */}
      {photos.map((id, i) => {
        const st = photoStarts[i];
        const k = interpolate(frame, [st - 2, st + 18], [i === 0 ? 1 : 0, 1], { ...clamp, easing: OUT });
        const next = photoStarts[i + 1];
        if (frame < st - 2 || (next !== undefined && frame > next + 24)) return null;
        return (
          <div
            key={id}
            style={{
              position: "absolute",
              left: s.x0,
              right: width - (tall ? 1020 : s.x1),
              top: pane.y0,
              height: pane.y1 - pane.y0,
              translate: `0 ${(1 - k) * 60}px`,
              opacity: i === 0 ? interpolate(frame, [0, 10], [0, 1], clamp) : k,
            }}
          >
            <PhotoCard src={photo(id)} start={st} life={(next ?? endAt) - st + 30} style={{ inset: 0 }} />
          </div>
        );
      })}
      {/* the service card */}
      <div
        style={{
          position: "absolute",
          left: s.x0,
          right: width - (tall ? 1020 : s.x1),
          top: card.y0,
          height: card.y1 - card.y0,
          background: C.card,
          borderRadius: 24,
          padding: tall ? "44px 48px" : "40px 44px",
          boxSizing: "border-box",
          boxShadow: "0 10px 40px rgba(24,24,24,0.06)",
        }}
      >
        {/* hook: the owner's question */}
        <div style={{ position: "absolute", left: 48, right: 48, top: 44, opacity: 1 - bodyIn }}>
          <Label size={24}>{label}</Label>
          <Reveal
            lines={[hook.text]}
            start={f(hook.start) - 4}
            style={{ fontFamily: FONT.sans, fontWeight: 700, fontSize: tall ? 66 : 58, color: C.ink, marginTop: 22, lineHeight: 1.1 }}
            lineHeight={1.12}
          />
        </div>
        {/* body: numeral, title, the three services from the site */}
        <div style={{ opacity: bodyIn, translate: `0 ${(1 - bodyIn) * 14}px`, display: "flex", gap: 30, height: "100%" }}>
          <div style={{ width: tall ? 110 : 100, borderRight: `2px solid ${C.line}`, flexShrink: 0 }}>
            <Numeral n={num} size={tall ? 96 : 86} />
          </div>
          <div style={{ flex: 1, display: "flex", flexDirection: "column" }}>
            <div style={{ fontFamily: FONT.sans, fontWeight: 700, fontSize: tall ? 60 : 48, color: C.ink, lineHeight: 1.1 }}>{title}</div>
            <div style={{ marginTop: tall ? 26 : 20 }}>
              {rows.map((r, i) => {
                const k = interpolate(frame, [rowStarts[i], rowStarts[i] + 14], [0, 1], { ...clamp, easing: OUT });
                return (
                  <div
                    key={r}
                    style={{
                      display: "flex",
                      alignItems: "center",
                      gap: 22,
                      padding: tall ? "28px 0" : "18px 0",
                      borderBottom: i < rows.length - 1 ? `1.5px solid ${C.line}` : undefined,
                      opacity: k,
                      translate: `${(1 - k) * 18}px 0`,
                    }}
                  >
                    <div style={{ width: 14, height: 14, borderRadius: 7, background: C.accent, flexShrink: 0 }} />
                    <div style={{ fontFamily: FONT.sans, fontWeight: 400, fontSize: tall ? 42 : 34, color: C.body, lineHeight: 1.2 }}>{r}</div>
                  </div>
                );
              })}
            </div>
            <div
              style={{
                marginTop: "auto",
                fontFamily: FONT.sans,
                fontWeight: 700,
                fontSize: tall ? 48 : 40,
                color: C.accent,
                opacity: interpolate(frame, [f(close.start) - 4, f(close.start) + 10], [0, 1], clamp),
              }}
            >
              {close.text}
            </div>
          </div>
        </div>
      </div>
      <Sequence from={endAt} layout="none">
        <EndCard start={0} tagline={endTagline} width={width} height={height} />
      </Sequence>
    </AbsoluteFill>
  );
};
