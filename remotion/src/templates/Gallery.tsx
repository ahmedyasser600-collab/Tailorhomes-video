import React from "react";
import { AbsoluteFill, Sequence, interpolate, staticFile, useCurrentFrame } from "remotion";
import { Audio } from "@remotion/media";
import { C, FONT, Format, Lang, SAFE, photo } from "../brand";
import { EndCard, Label, Numeral, OUT, PhotoCard, Reveal, useLayout } from "../components";

export type GalleryProps = {
  format: Format;
  lang: Lang;
  title: string;
  photos: string[];
  music?: string; // file in public/audio
};

const COPY = {
  it: { label: "Galleria", sub: "Appartamenti gestiti da Tailor Homes a Padova.", tag: "La tua casa a Padova, su misura." },
  en: { label: "Gallery", sub: "Apartments managed by Tailor Homes in Padova.", tag: "Your home in Padova, tailored." },
};

export const INTRO = 12; // frames before the first card arrives
export const PER = 48; // frames each photo holds the front of the stack (1.6 s)
export const OUTRO = 84;
export const galleryDuration = (n: number) => INTRO + n * PER + OUTRO;

export const Gallery: React.FC<GalleryProps> = ({ format, lang, title, photos, music }) => {
  const frame = useCurrentFrame();
  const { width, height, tall } = useLayout();
  const s = SAFE[format];
  const t = COPY[lang];
  const end = INTRO + photos.length * PER;
  const card = tall ? { x0: 60, x1: 1020, y0: 540, y1: 1640 } : { x0: 64, x1: 1016, y0: 300, y1: 1286 };
  const cardH = card.y1 - card.y0;
  const idx = Math.min(photos.length - 1, Math.max(0, Math.floor((frame - INTRO) / PER)));
  return (
    <AbsoluteFill style={{ background: C.card }}>
      {music ? <Audio src={staticFile(`audio/${music}`)} /> : null}
      {/* header: eyebrow, title, line, counter */}
      <div style={{ position: "absolute", left: s.x0, top: s.y0, right: width - s.x1 }}>
        <Label style={{ opacity: interpolate(frame, [2, 14], [0, 1], { extrapolateRight: "clamp" }) }}>{t.label}</Label>
        <Reveal
          lines={[title]}
          start={4}
          style={{ fontFamily: FONT.sans, fontWeight: 700, fontSize: tall ? 104 : 90, color: C.ink, marginTop: tall ? 18 : 12, letterSpacing: "-0.01em" }}
        />
        <Reveal lines={[t.sub]} start={10} style={{ fontFamily: FONT.sans, fontWeight: 400, fontSize: tall ? 34 : 30, color: C.body, marginTop: 10 }} />
      </div>
      <div style={{ position: "absolute", right: width - s.x1, top: s.y0 + (tall ? 40 : 30), display: "flex", alignItems: "baseline", gap: 10 }}>
        <Numeral n={String(idx + 1).padStart(2, "0")} size={tall ? 96 : 84} />
        <div style={{ fontFamily: FONT.sans, fontWeight: 500, fontSize: 28, color: C.body }}>/ {String(photos.length).padStart(2, "0")}</div>
      </div>
      {/* the stack: each card rises from below and settles over the previous one */}
      {photos.map((id, i) => {
        const start = INTRO + i * PER;
        const k = interpolate(frame, [start - 2, start + 20], [0, 1], { extrapolateLeft: "clamp", extrapolateRight: "clamp", easing: OUT });
        if (frame < start - 2 || frame > end + 24) return null;
        return (
          <div
            key={id}
            style={{
              position: "absolute",
              left: card.x0,
              top: card.y0,
              width: card.x1 - card.x0,
              height: cardH,
              translate: `0 ${(1 - k) * (height - card.y0)}px`,
              boxShadow: i > 0 ? `0 -18px 48px rgba(24,24,24,${0.12 * k})` : undefined,
              borderRadius: 24,
            }}
          >
            <PhotoCard src={photo(id)} start={start} life={PER * 2} style={{ inset: 0 }} />
          </div>
        );
      })}
      <Sequence from={end} durationInFrames={OUTRO} layout="none">
        <EndCard start={0} tagline={t.tag} width={width} height={height} />
      </Sequence>
    </AbsoluteFill>
  );
};
