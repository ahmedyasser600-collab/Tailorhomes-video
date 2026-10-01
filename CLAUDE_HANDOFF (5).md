# Claude Code handoff — Tailor Homes videos

Read README.md, manifest.json, brand/palette.json and VERIFICATION.md. Inspect
TailorHomes_Catalog.png. Use this folder as a read-only master library; create a
separate campaign folder and preserve existing videos.

## Scope and messaging

This is Tailor Homes: property management, furnished short/medium stays, corporate
housing and guest experience. Select the objective and audience for each film:
property owner, corporate booker, relocating professional, student or leisure guest.
Request 2–3 motion references if none accompany the brief. Do independent project
inspection while waiting. Do not transfer another brand's colors, offers or claims.
Do not invent occupancy, returns, prices, ratings, discounts or financing terms.

## Workflow A: Blender shots and compositing

Append the named TH collection from an asset .blend. Move its ROOT, use the named
hinges and controls, and keyframe explicit frames at 30 fps. Use source curves and
fonts for logo/text edits. Preserve the original logo proportions and irregular ring;
there is no outline bevel. Keep headlines, subtitles and CTAs outside product artwork.

Render RGBA PNG sequences, then composite into the existing film pipeline. The
included 3-second arrival demo maps frame_0001.png to t=0 and frame_0090.png to
89/30 seconds. Display all 90 frames for 3 seconds. Replace its transparent doorway
with a room shot or transition to a new scene. Do not use the review MP4 as an alpha asset.

A suggested 20–30 second story: arrival/keys → suitcase → furnished comfort →
workspace → welcome folder → supplied brand lockup and approved CTA. The room shell
can stage the separate furniture, but does not replicate a specific listing.
Use actual property photos supplied for a campaign when making property-specific claims.

Use website palette values for 2D graphics; use the supplied SVG gradient for the
logo. DM Sans is body copy, Jost labels, Cormorant Garamond editorial accents. Fonts
and licenses are bundled. Do not retype the official wordmark in these fonts.

## Workflow B: GLB in a browser renderer

Load exports/<asset>.glb in the existing renderer. Each has named roots, controls and
PBR materials; add your own camera, studio/environment lights and exposure settings.
GLB is Y-up: source (x,y,z) maps to (x,z,-y). For example, a source Z hinge rotation
maps to Y in a converted scene; inspect actual imported local axes and parent rotations.
Find objects by manifest names, save their initial transforms, and evaluate animation
from frame/time instead of cumulatively adding deltas.

GLBs are static assemblies, not baked animation clips. Editable source text/curves
and edge modifiers are evaluated into meshes. The logo's gradient is an embedded
image with explicit UVs; the source uses object-space mapping to preserve SVG spans.
Lighting, AgX, reflection/transmission and shadows will not transfer pixel-identically.
Test your actual browser renderer before publishing; only Blender reimport was verified.

## Voice, music and output

This library contains no narration or music. If a film requires them, request the
approved script and licensed audio sources. If the user chooses Google AI Studio,
request its exported WAV/MP3 when no authorized TTS integration is available. Never
claim that generated speech is a recording by a human actor. Continue the animatic
while missing audio is resolved; clearly label unfinished previews.

Deliver an editable campaign project, final-resolution MP4, clean 3D frame sequences,
separate narration/music tracks, subtitles when requested, and music source/license.
Inspect geometry, moving parts, type, mobile safe areas and audio intelligibility;
state honestly which checks were performed and which still require human playback.
