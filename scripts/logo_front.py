"""Front-on, unlit logo fidelity render; preserves source curves and does not save changes."""
import bpy
from mathutils import Vector
from pathlib import Path
P=Path(__file__).resolve().parents[1]
bpy.ops.wm.open_mainfile(filepath=str(P/'assets/01_brand_logo.blend'));s=bpy.data.scenes['01_brand_logo'];bpy.context.window.scene=s
pts=[o.matrix_world@Vector(v) for o in s.objects if o.type=='CURVE' for v in o.bound_box];lo=Vector(tuple(min(v[i] for v in pts) for i in range(3)));hi=Vector(tuple(max(v[i] for v in pts) for i in range(3)));center=(lo+hi)/2
s.camera.location=center+Vector((0,-3,0));s.camera.rotation_euler=(center-s.camera.location).to_track_quat('-Z','Y').to_euler();s.camera.data.ortho_scale=(hi.x-lo.x)*1.04
s.render.resolution_x=1600;s.render.resolution_y=650;s.view_settings.view_transform='Standard';s.world.node_tree.nodes['Background'].inputs[1].default_value=0
for o in s.objects:
 if o.type=='LIGHT':o.hide_render=True
for m in bpy.data.materials:
 if not m.use_nodes:continue
 n=m.node_tree.nodes.get('Principled BSDF')
 if n and n.inputs['Base Color'].is_linked:
  link=n.inputs['Base Color'].links[0];m.node_tree.links.new(link.from_socket,n.inputs['Emission Color']);m.node_tree.links.remove(link);n.inputs['Base Color'].default_value=(0,0,0,1);n.inputs['Emission Strength'].default_value=1
s.cycles.samples=32;s.render.filepath=str(P/'verification/logo_front_unlit.png');bpy.ops.render.render(write_still=True)
