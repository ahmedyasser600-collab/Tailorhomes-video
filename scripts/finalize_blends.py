"""Save data-library files as directly openable Blender projects."""
import bpy,json
from pathlib import Path
P=Path(__file__).resolve().parents[1]
m=json.loads((P/'manifest.json').read_text())
for a in m['assets']:
 path=P/a['blend'];bpy.ops.wm.open_mainfile(filepath=str(path));bpy.context.window.scene=bpy.data.scenes[a['id']]
 bpy.context.preferences.filepaths.save_version=0
 bpy.ops.wm.save_as_mainfile(filepath=str(path),compress=True)
path=P/'animation/door_welcome.blend';bpy.ops.wm.open_mainfile(filepath=str(path))
s=next(s for s in bpy.data.scenes if s.camera and s.frame_end==90);bpy.context.window.scene=s;s.frame_set(1)
bpy.context.preferences.filepaths.save_version=0;bpy.ops.wm.save_as_mainfile(filepath=str(path),compress=True)
