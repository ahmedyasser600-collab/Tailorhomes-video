import React from "react";
import { AbsoluteFill, Easing, Img, interpolate, useCurrentFrame, useVideoConfig } from "remotion";
import { C, FONT, LOGO, URL_TEXT } from "./brand";
import { FitText } from "./qa";

export const clamp = { extrapolateLeft: "clamp", extrapolateRight: "clamp" } as const;
export const OUT = Easing.bezier(0.16, 1, 0.3, 1); // calm expo-out, no overshoot
export const INOUT = Easing.bezier(0.65, 0, 0.35, 1);

export const useLayout = () => {
  const { width, height } = useVideoConfig();
  return { width, height, tall: height > 1500 };
};

/** A real photo. It only ever scales (slowly, evenly): no pans, no drift. */
export const Photo: React.FC<{
  src: string;
  scale: number;
  position?: string;
  style?: React.CSSProperties;
  radius?: number;
}> = ({ src, scale, position = "50% 50%", style, radius = 0 }) => (
  <div style={{ position: "absolute", overflow: "hidden", borderRadius: radius, background: C.circle, ...style }}>
    <Img src={src} style={{ width: "100%", height: "100%", objectFit: "cover", objectPosition: position, scale }} />
  </div>
);

/** Tracked uppercase eyebrow label in terracotta, as on the website. */
export const Label: React.FC<{ children: string; size?: number; color?: string; style?: React.CSSProperties; qa?: string }> = ({
  children,
  size = 26,
  color = C.accent,
  style,
  qa = "label",
}) => (
  <div data-qa={qa} style={{ fontFamily: FONT.label, fontWeight: 600, fontSize: size, letterSpacing: "0.26em", textTransform: "uppercase", color, whiteSpace: "nowrap", ...style }}>
    {children}
  </div>
);

/**
 * Cream sign-off card: logo, tagline (fitted), URL, and the music credit (CC BY attribution).
 * Every element sits in its own vertical slot, so nothing can collide.
 */
export const EndCard: React.FC<{ start: number; tagline: string; credit: string; safe: { x0: number; x1: number; y0: number; y1: number } }> = ({
  start,
  tagline,
  credit,
  safe,
}) => {
  const frame = useCurrentFrame();
  const { tall } = useLayout();
  const k = (d: number, len = 18) => interpolate(frame, [start + d, start + d + len], [0, 1], { ...clamp, easing: OUT });
  const w = safe.x1 - safe.x0;
  const cx = (safe.x0 + safe.x1) / 2;
  const mid = (safe.y0 + safe.y1) / 2;
  const logoW = tall ? 600 : 520;
  return (
    <AbsoluteFill style={{ background: C.card, opacity: k(0, 12) }}>
      <div style={{ position: "absolute", left: cx - logoW / 2, top: mid - (tall ? 330 : 290), width: logoW, opacity: k(4), translate: `0 ${(1 - k(4)) * 14}px` }}>
        <Img src={LOGO} style={{ width: "100%" }} />
      </div>
      <div style={{ position: "absolute", left: cx - 60, top: mid - 40, width: 120 * k(10), height: 3, background: C.accent }} />
      <div style={{ position: "absolute", left: safe.x0, top: mid + 4, width: w, display: "flex", justifyContent: "center", opacity: k(12) }}>
        <FitText
          text={tagline}
          width={w - 40}
          maxLines={2}
          maxSize={tall ? 60 : 54}
          family={FONT.sans}
          qa="end-tagline"
          style={{ color: C.ink, textAlign: "center" }}
        />
      </div>
      <div
        data-qa="end-url"
        style={{ position: "absolute", left: safe.x0, width: w, top: mid + (tall ? 170 : 150), textAlign: "center", fontFamily: FONT.sans, fontWeight: 500, fontSize: 40, color: C.body, opacity: k(18) }}
      >
        {URL_TEXT}
      </div>
      <div
        data-qa="end-credit"
        style={{ position: "absolute", left: safe.x0, width: w, top: safe.y1 - 34, textAlign: "center", fontFamily: FONT.sans, fontWeight: 400, fontSize: 22, color: C.body, opacity: 0.85 * k(22) }}
      >
        {credit}
      </div>
    </AbsoluteFill>
  );
};
