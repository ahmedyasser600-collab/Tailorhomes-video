# Verification and limitations

Built and checked with Blender 4.5.9 LTS (Linux).

- Reopened all 12 asset files, master library and animation. Individual assets were subsequently saved as normal Blender projects with the intended scene active.
- Checked root hierarchy, named movable pivots, materials, mesh UV presence, Asset Browser collection metadata and thumbnails, cameras and lighting. Checked for linked libraries and unpacked file dependencies: none found.
- Reimported all 12 GLBs. Compared object names, parents, material names, triangle counts and evaluated world-space geometry with the source using bidirectional nearest-vertex comparisons (tolerance 0.00001 m). Results: verification/geometry_checks.json.
- Rendered representative GLBs with matching Blender cameras and lights; visually inspected logo, keys, bedroom and welcome-folder comparisons. This is not a browser-renderer test.
- Inspected all 12 previews in the catalog, plus representative movable poses. Corrected the laptop closing axis to positive X rotation (+pi/2); its closed-pose render was inspected after correction.
- Compared a front-on unlit logo render against the supplied SVG raster reference. Original SVG curves and gradient are retained. Normalized raster silhouette overlap is approximately 96.1%; fine distressed edges and raster sampling differ. See LOGO_COMPARISON.png. Studio lighting and AgX intentionally change the apparent colors in product previews.
- Checked the door animation at all 90 frame values for finite, monotonic rotation. Inspected all 90 frames in a contact sheet. All PNGs are RGBA, 640 x 640 and have nonempty alpha bounds clear of the frame edges.
- Encoded and probed the MP4: 640 x 640, 30 fps, 90 decoded frames, 3 seconds. Inspected decoded first, middle and last frames. It is silent. Real-time human playback review has not been performed.
- ZIP creation verifies required contents, CRC integrity and per-file SHA-256 hashes. See PACKAGE_CHECK.json alongside the ZIP.

## Material limitations

This is a simplified starter library for property management, corporate housing and hospitality explainers. Furniture and buildings are generic illustrative models, not surveyed Tailor Homes properties or verified product CAD. Sample dates and text are fictional and editable.

The source logo's distressed ring produces a relatively dense mesh (about 346k triangles for the full lockup) to preserve its outline. Prefer the separate supplied symbol or original SVG overlay for small browser graphics. Do not indiscriminately decimate the logo.

GLB exports retain mesh geometry, hierarchy, UVs and supported PBR materials. Editable Blender curves/text and modifiers become meshes; Blender studio lighting, cameras and AgX presentation are not a guaranteed match in a browser. Configure browser lighting and color management separately. The editable demonstration animation is in Blender; individual asset GLBs are static assemblies with movable named nodes.

700 px stills and the 640 px animation are review outputs. Higher-resolution production settings are documented, but have not been rendered. Tiny descriptor text and the thin logo ring need adequate on-screen size. Windows UI operation and browser playback have not been tested.
