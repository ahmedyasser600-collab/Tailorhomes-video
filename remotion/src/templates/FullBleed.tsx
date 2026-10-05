import React from "react";
import { AbsoluteFill, Sequence, interpolate, staticFile, useCurrentFrame } from "remotion";
import { Audio } from "@remotion/media";
import { C, FONT, FPS, Format, SAFE, photo } from "../brand";
import { EndCard, Label, OUT, Photo, clamp, useLayout } from "../components";
import { FitText, FontGate, QA } from "../qa";

/**
 * Full-screen photo films (the look of Films 01-03): every shot is a real photo edge to edge,
 * slowly scaling, with large white type on a soft dark scrim. Two uses:
 *  - narrated: captions follow the voice-over phrases (`lines`),
 *  - board: a list of dated items (events), one per shot, cut on the beat.
 * All times are in seconds.
 */
export type Shot = { photo: string; start: number; pos?: string };
export type Line = { text: string; start: number; end: number };
export type BoardItem = { when: string; what: string; start: number };
export type FullBleedProps = {
  format: Format;
  label: string;
  shots: Shot[];
  cut: "fade" | "hard";
  lines?: Line[]; // narrated captions
  intro?: { text: string; start: number; end: number }; // board: opening title
  items?: BoardItem[]; // board: dated items
  outro?: { text: string; start: number }; // board: closing line
  end: number; // sign-off card
  tag: string;
  music: string;
  vo?: string;
  voLead?: number;
  credit: string;
};

export const fullDuration = (end: number, hold: number) => Math.ceil((end + hold) * FPS);

export const FullBleed: React.FC<FullBleedProps> = (p) => (
  <FontGate>
    <Inner {...p} />
  </FontGate>
);

const f = (s: number) => Math.round(s * FPS);

const Inner: React.FC<FullBleedProps> = ({ format, label, shots, cut, lines, intro, items, outro, end, tag, music, vo, voLead = 0, credit }) => {
  const frame = useCurrentFrame();
  const { tall } = useLayout();
  const s = SAFE[format];
  const w = s.x1 - s.x0;
  const endF = f(end);
  const out = interpolate(frame, [endF - 8, endF], [1, 0], clamp);
  const fadeIn = interpolate(frame, [0, 10], [0, 1], clamp);
  // caption block: anchored to the bottom of the safe area
  const capBottom = s.y1 - (tall ? 40 : 30);
  const appear = (st: number, len = 10) => interpolate(frame, [st, st + len], [0, 1], { ...clamp, easing: OUT });
  return (
    <AbsoluteFill style={{ background: C.ink }}>
      <Audio src={staticFile(`audio/${music}`)} />
      {vo ? (
        <Sequence from={f(voLead)} layout="none">
          <Audio src={staticFile(`audio/${vo}`)} />
        </Sequence>
      ) : null}
      {frame < endF ? (
        <AbsoluteFill style={{ opacity: out * fadeIn }}>
          {shots.map((sh, i) => {
            const st = f(sh.start);
            const next = i + 1 < shots.length ? f(shots[i + 1].start) : endF;
            const xf = cut === "fade" ? 12 : 0;
            if (frame < st || frame >= next + xf) return null;
            const k = cut === "fade" && i > 0 ? interpolate(frame, [st, st + xf], [0, 1], clamp) : 1;
            const push = interpolate(frame, [st, next + xf], [1.0, cut === "fade" ? 1.07 : 1.05], clamp);
            return (
              <AbsoluteFill key={i} style={{ opacity: k }}>
                <Photo src={photo(sh.photo)} scale={push} position={sh.pos} style={{ inset: 0 }} />
              </AbsoluteFill>
            );
          })}
          {/* scrims: top for the label, bottom for the type */}
          <AbsoluteFill style={{ background: "linear-gradient(180deg, rgba(10,8,6,0.55) 0%, rgba(10,8,6,0) 22%, rgba(10,8,6,0) 42%, rgba(10,8,6,0.72) 78%, rgba(10,8,6,0.82) 100%)" }} />
          <Label color={C.white} qa="label" style={{ position: "absolute", left: s.x0, top: s.y0, opacity: appear(4, 14) }}>
            {label}
          </Label>
          {(lines ?? []).map((ln, i) => {
            const st = f(ln.start) - 3;
            const nx = i + 1 < (lines ?? []).length ? f(lines![i + 1].start) - 3 : endF;
            const gone = Math.min(f(ln.end) + 18, nx);
            if (frame < st || frame >= gone) return null;
            const k = appear(st, 9) * interpolate(frame, [gone - 6, gone], [1, 0], clamp);
            return (
              <Caption key={i} k={k} bottom={capBottom} left={s.x0} width={w}>
                <FitText text={ln.text} width={w} maxLines={3} maxSize={tall ? 72 : 64} family={FONT.sans} qa={`line-${i}`} lineHeight={1.12} style={{ color: C.white, letterSpacing: "-0.01em" }} />
              </Caption>
            );
          })}
          {intro && frame >= f(intro.start) && frame < f(intro.end) ? (
            <Caption k={appear(f(intro.start), 12) * interpolate(frame, [f(intro.end) - 5, f(intro.end)], [1, 0], clamp)} bottom={capBottom} left={s.x0} width={w}>
              <FitText text={intro.text} width={w} maxLines={2} maxSize={tall ? 104 : 92} family={FONT.sans} qa="intro" lineHeight={1.04} style={{ color: C.white, letterSpacing: "-0.02em" }} />
            </Caption>
          ) : null}
          {(items ?? []).map((it, i) => {
            const st = f(it.start);
            const nx = i + 1 < items!.length ? f(items![i + 1].start) : outro ? f(outro.start) : endF;
            if (frame < st || frame >= nx) return null;
            const k = appear(st, 7);
            return (
              <Caption key={i} k={1} bottom={capBottom} left={s.x0} width={w}>
                <div style={{ opacity: k, translate: `0 ${(1 - k) * 18}px` }}>
                  <FitText text={it.when} width={w} maxLines={1} maxSize={tall ? 46 : 40} weight={600} family={FONT.label} qa={`when-${i}`} style={{ color: C.white, letterSpacing: "0.18em", textTransform: "uppercase" }} />
                  <div style={{ width: 90, height: 3, background: C.white, opacity: 0.85, margin: tall ? "22px 0 22px" : "16px 0 16px" }} />
                  <FitText text={it.what} width={w} maxLines={2} maxSize={tall ? 104 : 88} family={FONT.sans} qa={`what-${i}`} lineHeight={1.02} style={{ color: C.white, letterSpacing: "-0.02em" }} />
                </div>
              </Caption>
            );
          })}
          {outro && frame >= f(outro.start) ? (
            <Caption k={appear(f(outro.start), 10)} bottom={capBottom} left={s.x0} width={w}>
              <FitText text={outro.text} width={w} maxLines={2} maxSize={tall ? 96 : 84} family={FONT.sans} qa="outro" lineHeight={1.04} style={{ color: C.white, letterSpacing: "-0.02em" }} />
            </Caption>
          ) : null}
        </AbsoluteFill>
      ) : null}
      <Sequence from={endF} layout="none">
        <EndCard start={0} tagline={tag} credit={credit} safe={s} />
      </Sequence>
      <QA safe={s} />
    </AbsoluteFill>
  );
};

/** Text block whose bottom edge sits on a fixed baseline, so 1-3 lines grow upwards. */
const Caption: React.FC<{ k: number; bottom: number; left: number; width: number; children: React.ReactNode }> = ({ k, bottom, left, width, children }) => {
  const { height } = useLayout();
  return (
    <div style={{ position: "absolute", left, width, bottom: height - bottom, opacity: k, translate: `0 ${(1 - k) * 12}px`, textShadow: "0 2px 18px rgba(0,0,0,0.35)" }}>
      {children}
    </div>
  );
};
