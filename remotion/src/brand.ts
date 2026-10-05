// Tailor Homes brand kit, taken from the website (tailorhomes.it services page):
// warm off-white cards on a light warm-grey page, terracotta accent for icons and the
// italic serif numerals, near-black bold geometric sans for titles, warm grey body text.
import { loadFont } from "@remotion/fonts";
import { staticFile } from "remotion";

export const C = {
  page: "#EAE9E5", // page background
  card: "#F4F0ED", // card background
  circle: "#EEECEA", // icon circles
  accent: "#9C5044", // terracotta: icons, numerals, rules
  ink: "#181818", // titles
  body: "#4C4844", // body copy
  line: "#DDD6D0", // hairline dividers
  white: "#FFFFFF",
} as const;

export const FONT = {
  sans: "DM Sans",
  serif: "Cormorant Garamond", // italic numerals only (its grave accents are faulty: never set à è ì ò ù in it)
  label: "Jost",
} as const;

/** Resolves once every brand font is loaded (text is only measured after this). */
export const fontsReady = Promise.all([
  loadFont({ family: FONT.sans, url: staticFile("fonts/DMSans-Regular.ttf"), weight: "400" }),
  loadFont({ family: FONT.sans, url: staticFile("fonts/DMSans-Medium.ttf"), weight: "500" }),
  loadFont({ family: FONT.sans, url: staticFile("fonts/DMSans-Bold.ttf"), weight: "700" }),
  loadFont({ family: FONT.serif, url: staticFile("fonts/CormorantGaramond-MediumItalic.ttf"), weight: "500", style: "italic" }),
  loadFont({ family: FONT.label, url: staticFile("fonts/Jost-SemiBold.ttf"), weight: "600" }),
]);

export const LOGO = staticFile("brand/logo.png"); // supplied artwork, used unmodified on light backgrounds
export const URL_TEXT = "tailorhomes.it";

export type Format = "9x16" | "4x5";
export type Lang = "it" | "en";

export const FORMATS: Record<Format, { width: number; height: number }> = {
  "9x16": { width: 1080, height: 1920 },
  "4x5": { width: 1080, height: 1350 },
};

// Areas where essential content may sit. 9:16 keeps clear of the Reels/TikTok UI
// (top bar, caption block, right-hand action buttons).
export const SAFE: Record<Format, { x0: number; x1: number; y0: number; y1: number }> = {
  "9x16": { x0: 60, x1: 960, y0: 250, y1: 1500 },
  "4x5": { x0: 64, x1: 1016, y0: 64, y1: 1286 },
};

export const FPS = 30;
export const photo = (id: string) => staticFile(`photos/${id}.jpg`);
