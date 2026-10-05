import React, { useEffect, useLayoutEffect, useRef, useState } from "react";
import { continueRender, delayRender, useCurrentFrame } from "remotion";
import { fitTextOnNLines, measureText } from "@remotion/layout-utils";
import { fontsReady } from "./brand";

/** Renders children only after the brand fonts are loaded, so text measurement is exact. */
export const FontGate: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [ready, setReady] = useState(false);
  const [handle] = useState(() => delayRender("brand fonts"));
  useEffect(() => {
    fontsReady.then(() => {
      setReady(true);
      continueRender(handle);
    });
  }, [handle]);
  return ready ? <>{children}</> : null;
};

/**
 * Fit text on at most `maxLines` lines, then re-break two-line text at the most even point
 * (smallest widest line), so headlines never strand a short word like "a" or "su".
 */
export const fitBalanced = (o: { text: string; width: number; maxLines: number; maxSize: number; family: string; weight: number }) => {
  const { fontSize, lines } = fitTextOnNLines({ text: o.text, maxLines: o.maxLines, maxBoxWidth: o.width * 0.98, fontFamily: o.family, fontWeight: o.weight, maxFontSize: o.maxSize });
  if (lines.length !== 2) return { fontSize, lines };
  const words = o.text.split(" ");
  const w = (t: string) => measureText({ text: t, fontFamily: o.family, fontWeight: o.weight, fontSize }).width;
  let best = lines;
  let bestW = Math.max(...lines.map(w));
  for (let i = 1; i < words.length; i++) {
    const a = words.slice(0, i).join(" ");
    const b = words.slice(i).join(" ");
    const m = Math.max(w(a), w(b));
    if (m <= o.width * 0.98 && m < bestW - 0.5) {
      best = [a, b];
      bestW = m;
    }
  }
  return { fontSize, lines: best };
};

/**
 * Text that is sized to fit its box: at most `maxLines` lines within `width`, never above `maxSize`.
 * Lines are broken by the measurer, then rendered explicitly so wrapping cannot differ.
 */
export const FitText: React.FC<{
  text: string;
  width: number;
  maxLines: number;
  maxSize: number;
  weight?: number;
  family: string;
  lineHeight?: number;
  style?: React.CSSProperties;
  qa?: string;
  renderLine?: (line: string, i: number, fontSize: number) => React.ReactNode;
}> = ({ text, width, maxLines, maxSize, weight = 700, family, lineHeight = 1.1, style, qa, renderLine }) => {
  const { fontSize, lines } = fitBalanced({ text, width, maxLines, maxSize, family, weight });
  return (
    <div data-qa={qa ?? "text"} style={{ fontFamily: family, fontWeight: weight, fontSize, lineHeight, width, ...style }}>
      {lines.map((l, i) => (
        <div key={i} style={{ whiteSpace: "nowrap" }}>
          {renderLine ? renderLine(l, i, fontSize) : l}
        </div>
      ))}
    </div>
  );
};

type Box = { x0: number; y0: number; x1: number; y1: number };

const rectOf = (el: Element): Box => {
  const r = el.getBoundingClientRect();
  return { x0: r.left, y0: r.top, x1: r.right, y1: r.bottom };
};

const visibleRect = (el: Element): Box | null => {
  let r = el.getBoundingClientRect();
  let box: Box = { x0: r.left, y0: r.top, x1: r.right, y1: r.bottom };
  let opacity = 1;
  for (let p: Element | null = el; p; p = p.parentElement) {
    const cs = getComputedStyle(p);
    opacity *= parseFloat(cs.opacity || "1");
    if (p !== el && (cs.overflow === "hidden" || cs.overflowY === "hidden")) {
      r = p.getBoundingClientRect();
      box = { x0: Math.max(box.x0, r.left), y0: Math.max(box.y0, r.top), x1: Math.min(box.x1, r.right), y1: Math.min(box.y1, r.bottom) };
    }
  }
  if (opacity < 0.15 || box.x1 - box.x0 < 2 || box.y1 - box.y0 < 2) return null;
  return box;
};

/**
 * Automatic layout QA. On every rendered frame it checks, for all visible `[data-qa]` text:
 *  - no two text blocks overlap,
 *  - text stays inside its `[data-qa-box]` container (cards, tags),
 *  - text stays inside the format's safe area (`safe`).
 * Problems are logged as `QA_FAIL ...` to the browser console; scripts/render-all.mjs collects them.
 */
export const QA: React.FC<{ safe: Box; scale?: number }> = ({ safe }) => {
  const frame = useCurrentFrame();
  const ref = useRef<HTMLDivElement>(null);
  useLayoutEffect(() => {
    const root = ref.current?.parentElement;
    if (!root) return;
    const rootR = root.getBoundingClientRect();
    const sx = rootR.width / root.offsetWidth || 1; // studio preview may be scaled
    const toLocal = (b: Box): Box => ({
      x0: (b.x0 - rootR.left) / sx,
      y0: (b.y0 - rootR.top) / sx,
      x1: (b.x1 - rootR.left) / sx,
      y1: (b.y1 - rootR.top) / sx,
    });
    const items = Array.from(root.querySelectorAll("[data-qa]"))
      .map((el) => ({ el, name: el.getAttribute("data-qa") ?? "text", box: visibleRect(el) }))
      .filter((x): x is { el: Element; name: string; box: Box } => x.box !== null)
      .map((x) => ({ ...x, box: toLocal(x.box) }));
    const fails: string[] = [];
    const tol = 2;
    for (const it of items) {
      const b = it.box;
      if (b.x0 < safe.x0 - tol || b.x1 > safe.x1 + tol || b.y0 < safe.y0 - tol || b.y1 > safe.y1 + tol) {
        fails.push(`${it.name} outside safe area ${JSON.stringify(b)}`);
      }
      const box = it.el.closest("[data-qa-box]");
      if (box) {
        const cb = toLocal(rectOf(box));
        if (b.x0 < cb.x0 - tol || b.x1 > cb.x1 + tol || b.y0 < cb.y0 - tol || b.y1 > cb.y1 + tol) {
          fails.push(`${it.name} overflows ${box.getAttribute("data-qa-box")}`);
        }
      }
    }
    for (let i = 0; i < items.length; i++) {
      for (let j = i + 1; j < items.length; j++) {
        const a = items[i].box;
        const c = items[j].box;
        const ox = Math.min(a.x1, c.x1) - Math.max(a.x0, c.x0);
        const oy = Math.min(a.y1, c.y1) - Math.max(a.y0, c.y0);
        if (ox > tol && oy > tol) fails.push(`${items[i].name} overlaps ${items[j].name}`);
      }
    }
    for (const f of fails) console.warn(`QA_FAIL frame=${frame} ${f}`);
  });
  return <div ref={ref} style={{ display: "none" }} />;
};
