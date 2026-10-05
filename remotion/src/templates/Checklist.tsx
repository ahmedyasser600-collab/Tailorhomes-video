import React from "react";
import {
  AbsoluteFill,
  Sequence,
  interpolate,
  staticFile,
  useCurrentFrame,
} from "remotion";
import { Audio } from "@remotion/media";
import { C, FONT, Format, SAFE, photo } from "../brand";
import { EndCard, Label, OUT, Photo, clamp, useLayout } from "../components";
import { FitText, FontGate, QA } from "../qa";

export type ChecklistProps = {
  format: Format;
  label: string;
  title: string;
  sub: string;
  items: string[];
  photos: string[]; // one per item
  tag: string;
  music: string;
  credit: string;
};

// The music starts 1.5 s before a bar line of the track, so every tick falls on a beat (2 beats = 1.5 s).
export const CK_INTRO = 45;
export const CK_PER = 45;
export const CK_OUTRO = 90;
export const checklistDuration = (n: number) =>
  CK_INTRO + n * CK_PER + CK_OUTRO;

const Tick: React.FC<{ at: number; size: number }> = ({ at, size }) => {
  const frame = useCurrentFrame();
  const fill = interpolate(frame, [at - 3, at + 2], [0, 1], clamp);
  const k = interpolate(frame, [at, at + 9], [0, 1], { ...clamp, easing: OUT });
  return (
    <svg
      width={size}
      height={size}
      viewBox="0 0 40 40"
      style={{ flexShrink: 0 }}
    >
      <rect
        x="2"
        y="2"
        width="36"
        height="36"
        rx="9"
        fill={C.accent}
        fillOpacity={fill}
        stroke={C.accent}
        strokeWidth="3"
      />
      <path
        d="M11 21 L18 28 L30 13"
        fill="none"
        stroke={C.white}
        strokeWidth="4"
        strokeLinecap="round"
        strokeLinejoin="round"
        pathLength={1}
        strokeDasharray={1}
        strokeDashoffset={1 - k}
      />
    </svg>
  );
};

/** "Ready for the next guest": items from the services page tick off, each over its own real photo. */
export const Checklist: React.FC<ChecklistProps> = (p) => (
  <FontGate>
    <ChecklistInner {...p} />
  </FontGate>
);

const ChecklistInner: React.FC<ChecklistProps> = ({
  format,
  label,
  title,
  sub,
  items,
  photos,
  tag,
  music,
  credit,
}) => {
  const frame = useCurrentFrame();
  const { tall } = useLayout();
  const s = SAFE[format];
  const w = s.x1 - s.x0;
  // fixed vertical slots (px): nothing can collide whatever the copy length
  const L = tall
    ? {
        title: 296,
        titleMax: 80,
        sub: 486,
        subSize: 34,
        pane: [556, 1040],
        list: 1068,
        row: 72,
        text: 38,
        box: 40,
      }
    : {
        title: 102,
        titleMax: 64,
        sub: 258,
        subSize: 30,
        pane: [312, 708],
        list: 732,
        row: 92,
        text: 38,
        box: 44,
      };
  const end = CK_INTRO + items.length * CK_PER;
  const headK = interpolate(frame, [2, 18], [0, 1], { ...clamp, easing: OUT });
  const cur = Math.min(
    items.length - 1,
    Math.max(0, Math.floor((frame - CK_INTRO + 4) / CK_PER)),
  );
  // the page clears just before the sign-off card arrives, so the two never share the screen
  const out = interpolate(frame, [end - 8, end], [1, 0], clamp);
  return (
    <AbsoluteFill style={{ background: C.card }}>
      <Audio src={staticFile(`audio/${music}`)} />
      {frame < end ? (
        <AbsoluteFill style={{ opacity: out }}>
          <div style={{ opacity: headK, translate: `0 ${(1 - headK) * 14}px` }}>
            <Label style={{ position: "absolute", left: s.x0, top: s.y0 }}>
              {label}
            </Label>
            <div style={{ position: "absolute", left: s.x0, top: L.title }}>
              <FitText
                text={title}
                width={w}
                maxLines={2}
                maxSize={L.titleMax}
                family={FONT.sans}
                qa="title"
                style={{ color: C.ink, letterSpacing: "-0.01em" }}
              />
            </div>
            <div
              data-qa="sub"
              style={{
                position: "absolute",
                left: s.x0,
                top: L.sub,
                fontFamily: FONT.sans,
                fontSize: L.subSize,
                color: C.body,
                whiteSpace: "nowrap",
              }}
            >
              {sub}
            </div>
          </div>
          {photos.map((id, i) => {
            const st = i === 0 ? 10 : CK_INTRO + i * CK_PER - 6;
            const next =
              i + 1 < photos.length
                ? CK_INTRO + (i + 1) * CK_PER - 6
                : end + 10;
            if (frame < st || frame > next + 12) return null;
            const k = interpolate(frame, [st, st + 12], [0, 1], {
              ...clamp,
              easing: OUT,
            });
            const push = interpolate(frame, [st, next + 12], [1, 1.05], clamp);
            return (
              <div
                key={id}
                style={{
                  position: "absolute",
                  left: s.x0,
                  width: tall ? 960 : w,
                  top: L.pane[0],
                  height: L.pane[1] - L.pane[0],
                  opacity: k,
                }}
              >
                <Photo
                  src={photo(id)}
                  scale={push}
                  radius={24}
                  style={{ inset: 0 }}
                />
              </div>
            );
          })}
          <div
            style={{ position: "absolute", left: s.x0, width: w, top: L.list }}
          >
            {items.map((it, i) => {
              const at = CK_INTRO + i * CK_PER;
              const kIn = interpolate(frame, [10 + i * 3, 24 + i * 3], [0, 1], {
                ...clamp,
                easing: OUT,
              });
              const active = i === cur && frame >= CK_INTRO - 4 && frame < end;
              return (
                <div
                  key={it}
                  style={{
                    display: "flex",
                    alignItems: "center",
                    gap: 24,
                    height: L.row,
                    borderBottom: `1.5px solid ${C.line}`,
                    opacity: kIn,
                  }}
                >
                  <Tick at={at} size={L.box} />
                  <div
                    data-qa={`item-${i}`}
                    style={{
                      fontFamily: FONT.sans,
                      fontWeight: active ? 700 : 500,
                      fontSize: L.text,
                      color: frame >= at ? C.ink : C.body,
                      whiteSpace: "nowrap",
                    }}
                  >
                    {it}
                  </div>
                </div>
              );
            })}
          </div>
        </AbsoluteFill>
      ) : null}
      <Sequence from={end} layout="none">
        <EndCard start={0} tagline={tag} credit={credit} safe={s} />
      </Sequence>
      <QA safe={s} />
    </AbsoluteFill>
  );
};
