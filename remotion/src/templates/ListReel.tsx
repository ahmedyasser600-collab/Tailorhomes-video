import React from "react";
import { AbsoluteFill, Sequence, interpolate, staticFile, useCurrentFrame } from "remotion";
import { Audio } from "@remotion/media";
import { C, FONT, FPS, Format, SAFE, photo } from "../brand";
import { EndCard, Label, OUT, Photo, clamp, useLayout } from "../components";
import { FitText, FontGate, QA } from "../qa";

/**
 * Sibling of the approved Checklist (same page, same slots, same motion), for lists that are not
 * tasks: each row carries an italic numeral that lights up when its photo comes in, and an optional
 * detail line under the photo. Items change on musical beats given in seconds (`starts`).
 */
export type ListItem = { text: string; photo: string; pos?: string; detail?: string };
export type ListReelProps = {
  format: Format;
  label: string;
  title: string;
  sub: string;
  items: ListItem[];
  starts: number[]; // s: item i becomes active (first one opens the video)
  end: number; // s: sign-off card
  tag: string;
  music: string;
  credit: string;
};

export const listDuration = (end: number, hold: number) => Math.ceil((end + hold) * FPS);

export const ListReel: React.FC<ListReelProps> = (p) => (
  <FontGate>
    <ListInner {...p} />
  </FontGate>
);

const ListInner: React.FC<ListReelProps> = ({ format, label, title, sub, items, starts, end, tag, music, credit }) => {
  const frame = useCurrentFrame();
  const { tall } = useLayout();
  const s = SAFE[format];
  const w = s.x1 - s.x0;
  const hasDetail = items.some((i) => i.detail);
  // fixed vertical slots (px), as in the Checklist; the pane gives up room to the detail line if any
  const L = tall
    ? { title: 296, titleMax: 80, sub: 486, subSize: 34, pane: [556, hasDetail ? 984 : 1040], detail: 1002, list: 1068, row: 72, text: 38, num: 46 }
    : { title: 102, titleMax: 64, sub: 258, subSize: 30, pane: [312, hasDetail ? 652 : 708], detail: 668, list: 732, row: 92, text: 38, num: 50 };
  const sf = starts.map((t) => Math.round(t * FPS));
  const endF = Math.round(end * FPS);
  const headK = interpolate(frame, [2, 18], [0, 1], { ...clamp, easing: OUT });
  const cur = Math.max(0, sf.filter((x) => frame >= x - 4).length - 1);
  const out = interpolate(frame, [endF - 8, endF], [1, 0], clamp);
  return (
    <AbsoluteFill style={{ background: C.card }}>
      <Audio src={staticFile(`audio/${music}`)} />
      {frame < endF ? (
        <AbsoluteFill style={{ opacity: out }}>
          <div style={{ opacity: headK, translate: `0 ${(1 - headK) * 14}px` }}>
            <Label style={{ position: "absolute", left: s.x0, top: s.y0 }}>{label}</Label>
            <div style={{ position: "absolute", left: s.x0, top: L.title }}>
              <FitText text={title} width={w} maxLines={2} maxSize={L.titleMax} family={FONT.sans} qa="title" style={{ color: C.ink, letterSpacing: "-0.01em" }} />
            </div>
            <div data-qa="sub" style={{ position: "absolute", left: s.x0, top: L.sub, fontFamily: FONT.sans, fontSize: L.subSize, color: C.body, whiteSpace: "nowrap" }}>
              {sub}
            </div>
          </div>
          {items.map((it, i) => {
            const st = i === 0 ? 6 : sf[i] - 4;
            const next = i + 1 < items.length ? sf[i + 1] - 4 : endF + 10;
            if (frame < st || frame > next + 10) return null;
            const k = interpolate(frame, [st, st + 8], [0, 1], { ...clamp, easing: OUT });
            const push = interpolate(frame, [st, next + 10], [1, 1.05], clamp);
            return (
              <div key={it.photo + i} style={{ position: "absolute", left: s.x0, width: tall ? 960 : w, top: L.pane[0], height: L.pane[1] - L.pane[0], opacity: k }}>
                <Photo src={photo(it.photo)} scale={push} position={it.pos} radius={24} style={{ inset: 0 }} />
              </div>
            );
          })}
          {hasDetail
            ? items.map((it, i) => {
                if (!it.detail || i !== cur) return null;
                const k = interpolate(frame, [sf[i] - 2, sf[i] + 8], [0, 1], { ...clamp, easing: OUT });
                return (
                  <div key={i} style={{ position: "absolute", left: s.x0, width: w, top: L.detail, opacity: k }}>
                    <FitText text={it.detail} width={w} maxLines={1} maxSize={26} weight={600} family={FONT.label} qa="detail" style={{ color: C.accent, letterSpacing: "0.14em", textTransform: "uppercase" }} />
                  </div>
                );
              })
            : null}
          <div style={{ position: "absolute", left: s.x0, width: w, top: L.list }}>
            {items.map((it, i) => {
              const at = sf[i];
              const kIn = interpolate(frame, [10 + i * 3, 24 + i * 3], [0, 1], { ...clamp, easing: OUT });
              const on = interpolate(frame, [at - 3, at + 4], [0, 1], clamp);
              const active = i === cur;
              return (
                <div key={i} style={{ display: "flex", alignItems: "center", gap: 26, height: L.row, borderBottom: `1.5px solid ${C.line}`, opacity: kIn }}>
                  <div
                    data-qa={`num-${i}`}
                    style={{
                      width: L.num * 1.4,
                      flexShrink: 0,
                      fontFamily: FONT.serif,
                      fontStyle: "italic",
                      fontWeight: 500,
                      fontSize: L.num,
                      lineHeight: 1,
                      color: C.accent,
                      opacity: 0.35 + 0.65 * on,
                    }}
                  >
                    {String(i + 1).padStart(2, "0")}
                  </div>
                  <div
                    data-qa={`item-${i}`}
                    style={{ fontFamily: FONT.sans, fontWeight: active ? 700 : 500, fontSize: L.text, color: frame >= at - 3 ? C.ink : C.body, whiteSpace: "nowrap" }}
                  >
                    {it.text}
                  </div>
                </div>
              );
            })}
          </div>
        </AbsoluteFill>
      ) : null}
      <Sequence from={endF} layout="none">
        <EndCard start={0} tagline={tag} credit={credit} safe={s} />
      </Sequence>
      <QA safe={s} />
    </AbsoluteFill>
  );
};
