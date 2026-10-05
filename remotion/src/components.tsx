import React from "react";
import { AbsoluteFill, Easing, Img, interpolate, useCurrentFrame, useVideoConfig } from "remotion";
import { C, FONT, LOGO, URL_TEXT } from "./brand";

const clamp = { extrapolateLeft: "clamp", extrapolateRight: "clamp" } as const;
export const OUT = Easing.bezier(0.16, 1, 0.3, 1); // calm expo-out, no overshoot
export const INOUT = Easing.bezier(0.65, 0, 0.35, 1);

/** 0→1 progress of a transition that starts at `start` (frames) and lasts `dur` frames. */
export const useProgress = (start: number, dur: number, easing = OUT) => {
  const frame = useCurrentFrame();
  return interpolate(frame, [start, start + dur], [0, 1], { ...clamp, easing });
};

/** A real photo in a rounded card. It only ever scales slowly and evenly: no pans, no drift. */
export const PhotoCard: React.FC<{
  src: string;
  start: number; // frame the photo first appears
  life: number; // frames it stays visible (drives the push)
  radius?: number;
  position?: string;
  style?: React.CSSProperties;
}> = ({ src, start, life, radius = 24, position = "50% 50%", style }) => {
  const frame = useCurrentFrame();
  return (
    <div style={{ position: "absolute", overflow: "hidden", borderRadius: radius, background: C.circle, ...style }}>
      <Img
        src={src}
        style={{
          width: "100%",
          height: "100%",
          objectFit: "cover",
          objectPosition: position,
          scale: interpolate(frame, [start, start + life], [1.0, 1.06], clamp),
        }}
      />
    </div>
  );
};

/** Tracked uppercase label (terracotta), as used for eyebrow text on the site. */
export const Label: React.FC<{ children: React.ReactNode; size?: number; color?: string; style?: React.CSSProperties }> = ({
  children,
  size = 26,
  color = C.accent,
  style,
}) => (
  <div style={{ fontFamily: FONT.label, fontWeight: 600, fontSize: size, letterSpacing: "0.28em", textTransform: "uppercase", color, ...style }}>
    {children}
  </div>
);

/** Line-by-line reveal: each line rises from behind its own mask. */
export const Reveal: React.FC<{
  lines: string[];
  start: number;
  end?: number; // frame the lines leave (optional)
  style?: React.CSSProperties;
  lineHeight?: number;
  stagger?: number;
}> = ({ lines, start, end, style, lineHeight = 1.08, stagger = 4 }) => {
  const frame = useCurrentFrame();
  return (
    <div style={{ ...style, lineHeight }}>
      {lines.map((l, i) => {
        const k = interpolate(frame, [start + i * stagger, start + i * stagger + 18], [1, 0], { ...clamp, easing: OUT });
        const ko = end === undefined ? 0 : interpolate(frame, [end + i * 2, end + i * 2 + 12], [0, 1], { ...clamp, easing: INOUT });
        return (
          <div key={i} style={{ overflow: "hidden", paddingBottom: "0.08em" }}>
            <div style={{ translate: `0 ${(k - ko) * 110}%`, opacity: 1 - ko }}>{l}</div>
          </div>
        );
      })}
    </div>
  );
};

/** Terracotta rule that draws from left to right. */
export const Rule: React.FC<{ start: number; width: number; style?: React.CSSProperties; color?: string; thickness?: number }> = ({
  start,
  width,
  style,
  color = C.accent,
  thickness = 3,
}) => {
  const k = useProgress(start, 20);
  return <div style={{ height: thickness, width: width * k, background: color, ...style }} />;
};

/** Italic serif numeral in terracotta (the site's 01–04 card numbering). */
export const Numeral: React.FC<{ n: string; size?: number; style?: React.CSSProperties }> = ({ n, size = 120, style }) => (
  <div style={{ fontFamily: FONT.serif, fontStyle: "italic", fontWeight: 500, fontSize: size, color: C.accent, lineHeight: 1, ...style }}>{n}</div>
);

/** End card on the site's card colour: logo, tagline, URL. */
export const EndCard: React.FC<{ start: number; tagline?: string; width: number; height: number }> = ({ start, tagline, width, height }) => {
  const kIn = useProgress(start, 16);
  const kLogo = useProgress(start + 6, 22);
  const kText = useProgress(start + 14, 20);
  return (
    <AbsoluteFill style={{ background: C.card, opacity: kIn, justifyContent: "center", alignItems: "center" }}>
      <div style={{ display: "flex", flexDirection: "column", alignItems: "center", gap: 44, translate: `0 ${(1 - kLogo) * 16}px` }}>
        <Img src={LOGO} style={{ width: Math.min(640, width * 0.6), opacity: kLogo }} />
        <div style={{ height: 3, width: 120 * kText, background: C.accent }} />
        {tagline ? (
          <div style={{ fontFamily: FONT.sans, fontWeight: 700, fontSize: height > 1500 ? 56 : 50, color: C.ink, opacity: kText, textAlign: "center", maxWidth: width - 160 }}>
            {tagline}
          </div>
        ) : null}
        <div style={{ fontFamily: FONT.sans, fontWeight: 500, fontSize: 40, color: C.body, opacity: kText, letterSpacing: "0.02em" }}>{URL_TEXT}</div>
      </div>
    </AbsoluteFill>
  );
};

export const useLayout = () => {
  const { width, height } = useVideoConfig();
  return { width, height, tall: height > 1500 };
};
