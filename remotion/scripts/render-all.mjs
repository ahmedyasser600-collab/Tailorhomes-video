// Batch-render every composition (or those matching a filter) to out/<id>.mp4,
// checks every frame's layout (src/qa.tsx) and writes out/qa-report.txt, then levels the audio to -14 LUFS integrated / -1.5 dBTP (video stream copied untouched).
//
//   node scripts/render-all.mjs                 # everything
//   node scripts/render-all.mjs gallery-camere  # ids containing the filter
//
// Uses the pre-installed headless Chromium if present (CHROME env var overrides).
import { bundle } from "@remotion/bundler";
import { renderMedia, selectComposition, getCompositions } from "@remotion/renderer";
import { execFileSync } from "node:child_process";
import { existsSync, mkdirSync, writeFileSync } from "node:fs";
import path from "node:path";

const root = path.resolve(path.dirname(new URL(import.meta.url).pathname), "..");
const filter = process.argv[2] ?? "";
const browserExecutable =
  process.env.CHROME ??
  (existsSync("/opt/pw-browsers/chromium_headless_shell-1194/chrome-linux/headless_shell")
    ? "/opt/pw-browsers/chromium_headless_shell-1194/chrome-linux/headless_shell"
    : null);

const serveUrl = await bundle({ entryPoint: path.join(root, "src/index.ts") });
const comps = (await getCompositions(serveUrl, { browserExecutable })).filter((c) => c.id.includes(filter));
mkdirSync(path.join(root, "out"), { recursive: true });
console.log(`rendering ${comps.length} compositions`);
const qa = [];

for (const c of comps) {
  const out = path.join(root, "out", `${c.id}.mp4`);
  const raw = out.replace(/\.mp4$/, ".raw.mp4");
  const composition = await selectComposition({ serveUrl, id: c.id, browserExecutable });
  const t0 = Date.now();
  await renderMedia({
    composition,
    serveUrl,
    codec: "h264",
    crf: 18,
    pixelFormat: "yuv420p",
    audioCodec: "aac",
    audioBitrate: "256k",
    outputLocation: raw,
    browserExecutable,
    concurrency: 4,
    // layout QA from src/qa.tsx: every overlap / overflow is logged as QA_FAIL by the page
    onBrowserLog: (log) => {
      if (log.text.includes("QA_FAIL")) qa.push(`${c.id} ${log.text}`);
    },
  });
  execFileSync("ffmpeg", [
    "-v", "error", "-y", "-i", raw, "-c:v", "copy",
    "-af", "loudnorm=I=-14:TP=-1.5:LRA=11", "-ar", "48000", "-c:a", "aac", "-b:a", "256k",
    "-movflags", "+faststart", out,
  ]);
  execFileSync("rm", ["-f", raw]);
  console.log(`${c.id}  ${(c.durationInFrames / c.fps).toFixed(1)}s  ${((Date.now() - t0) / 1000).toFixed(0)}s render`);
}

writeFileSync(path.join(root, "out", "qa-report.txt"), qa.length ? qa.join("\n") + "\n" : "no layout issues\n");
console.log(qa.length ? `QA: ${qa.length} layout issues, see out/qa-report.txt` : "QA: no layout issues");
