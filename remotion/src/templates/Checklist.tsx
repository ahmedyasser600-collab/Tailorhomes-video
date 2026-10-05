import React from "react";
import { AbsoluteFill, Sequence, interpolate, staticFile, useCurrentFrame } from "remotion";
import { Audio } from "@remotion/media";
import { C, FONT, Format, SAFE, photo } from "../brand";
import { EndCard, Label, OUT, PhotoCard, Reveal, useLayout } from "../components";

export type ChecklistProps = {
  format: Format;
  label: string;
  title: string;
  sub: string;
  items: string[];
  photos: string[]; // one per item
  tag: string;
  music: string;
};

export const CK_INTRO = 45; // frames before the first tick
export const CK_PER = 45; // frames per item (1.5 s)
export const CK_OUTRO = 84;
export const checklistDuration = (n: number) => CK_INTRO + n * CK_PER + 20 + CK_OUTRO;
/** Seconds at which each box ticks (used to place the soft UI ticks in the music). */
export const tickTimes = (n: number, fps = 30) => Array.from({ length: n }, (_, i) => (CK_INTRO + i * CK_PER + 12) / fps);

const clamp = { extrapolateLeft: "clamp", extrapolateRight: "clamp" } as const;

const Tick: React.FC<{ start: number; size: number }> = ({ start, size }) => {
  const frame = useCurrentFrame();
  const k = interpolate(frame, [start, start + 10], [0, 1], { ...clamp, easing: OUT });
  const fill = interpolate(frame, [start - 4, start + 2], [0, 1], clamp);
  return (
    <svg width={size} height={size} viewBox="0 0 40 40" style={{ flexShrink: 0 }}>
      <rect x="2" y="2" width="36" height="36" rx="8" fill={fill > 0 ? C.accent : "none"} fillOpacity={fill} stroke={C.accent} strokeWidth="3" />
      <path d="M11 21 L18 28 L30 13" fill="none" stroke={C.white} strokeWidth="4" strokeLinecap="round" strokeLinejoin="round" pathLength={1} strokeDasharray={1} strokeDashoffset={1 - k} />
    </svg>
  );
};

/** "Ready for the next guest": items from the services page tick off over real photos. */
export const Checklist: React.FC<ChecklistProps> = ({ format, label, title, sub, items, photos, tag, music }) => {
  const frame = useCurrentFrame();
  const { width, height, tall } = useLayout();
  const s = SAFE[format];
  const end = CK_INTRO + items.length * CK_PER + 20;
  const pane = tall ? { y0: 520, y1: 1010 } : { y0: 250, y1: 700 };
  const list = tall ? { y0: 1040, rowH: 74 } : { y0: 730, rowH: 90 };
  const right = width - (tall ? 1020 : s.x1);
  return (
    <AbsoluteFill style={{ background: C.card }}>
      <Audio src={staticFile(`audio/${music}`)} />
      <div style={{ position: "absolute", left: s.x0, right, top: s.y0 }}>
        <Label>{label}</Label>
        <Reveal lines={[title]} start={4} style={{ fontFamily: FONT.sans, fontWeight: 700, fontSize: tall ? 72 : 64, color: C.ink, marginTop: 14, lineHeight: 1.08 }} />
        <Reveal lines={[sub]} start={10} style={{ fontFamily: FONT.sans, fontWeight: 400, fontSize: tall ? 34 : 30, color: C.body, marginTop: 8 }} />
      </div>
      {photos.map((id, i) => {
        const st = i === 0 ? 8 : CK_INTRO + i * CK_PER;
        const k = interpolate(frame, [st, st + 16], [0, 1], { ...clamp, easing: OUT });
        const next = i + 1 < photos.length ? CK_INTRO + (i + 1) * CK_PER : end;
        if (frame < st || frame > next + 18) return null;
        return (
          <div key={id} style={{ position: "absolute", left: s.x0, right, top: pane.y0, height: pane.y1 - pane.y0, opacity: k, translate: `0 ${(1 - k) * 40}px` }}>
            <PhotoCard src={photo(id)} start={st} life={next - st + 20} style={{ inset: 0 }} />
          </div>
        );
      })}
      <div style={{ position: "absolute", left: s.x0, right, top: list.y0 }}>
        {items.map((it, i) => {
          const tickAt = CK_INTRO + i * CK_PER + 8;
          const kIn = interpolate(frame, [12 + i * 3, 26 + i * 3], [0, 1], { ...clamp, easing: OUT });
          const active = frame >= tickAt - 6 && frame < tickAt + CK_PER - 6;
          return (
            <div key={it} style={{ display: "flex", alignItems: "center", gap: 24, height: list.rowH, borderBottom: `1.5px solid ${C.line}`, opacity: kIn }}>
              <Tick start={tickAt} size={tall ? 40 : 44} />
              <div style={{ fontFamily: FONT.sans, fontWeight: active ? 700 : 500, fontSize: tall ? 36 : 38, color: frame >= tickAt ? C.ink : C.body }}>{it}</div>
            </div>
          );
        })}
      </div>
      <Sequence from={end} layout="none">
        <EndCard start={0} tagline={tag} width={width} height={height} />
      </Sequence>
    </AbsoluteFill>
  );
};
