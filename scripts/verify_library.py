"""Reopen, inspect and compare every Blender asset and GLB; render representative poses."""
import bpy,json,sys,math,os
from pathlib import Path
from mathutils import Vector
from mathutils.kdtree import KDTree
P=Path(__file__).resolve().parents[1];sys.path.insert(0,str(P/'scripts'))
from build_library import settings,rig,bounds
manifest=json.loads((P/'manifest.json').read_text());reports=[]
def metrics(obs):
 dg=bpy.context.evaluated_depsgraph_get();clouds={};count=0;materials=set()
 for o in obs:
  if o.type not in {'MESH','CURVE','FONT'}:continue
  ev=o.evaluated_get(dg);me=ev.to_mesh();me.calc_loop_triangles();count+=len(me.loop_triangles);clouds[o.name]=[tuple(ev.matrix_world@v.co) for v in me.vertices];materials.update(m.name for m in me.materials if m);ev.to_mesh_clear()
 pts=[v for c in clouds.values() for v in c];bb=[[min(v[i] for v in pts) for i in range(3)],[max(v[i] for v in pts) for i in range(3)]]
 return {'triangles':count,'bounds':bb,'materials':sorted(materials)},clouds

def distance(a,b):
 worst=0
 for src,dst in [(a,b),(b,a)]:
  tree=KDTree(len(dst))
  for i,p in enumerate(dst):tree.insert(p,i)
  tree.balance();worst=max(worst,max(tree.find(p)[2] for p in src))
 return worst

def render(name):
 if os.environ.get('TH_VERIFY_NO_RENDER')=='1':return
 s=bpy.context.scene;s.render.filepath=str(P/'verification'/name);bpy.ops.render.render(write_still=True)

for a in manifest['assets']:
 bpy.ops.wm.open_mainfile(filepath=str(P/a['blend']));s=bpy.data.scenes[a['id']];bpy.context.window.scene=s;col=bpy.data.collections[a['collection']];root=bpy.data.objects[a['root']]
 assert root.parent is None and all(abs(v-1)<1e-6 for v in root.scale)
 assert col.asset_data and col.preview and col.preview.image_size[0]>0
 assert s.camera and sum(o.type=='LIGHT' for o in s.objects)>=3
 assert not bpy.data.libraries
 assert not any(im.filepath and not im.packed_file for im in bpy.data.images if im.source=='FILE')
 assert not any(f.filepath and f.filepath!='<builtin>' and not f.packed_file for f in bpy.data.fonts)
 for o in col.objects:
  if o==root:continue
  p=o.parent
  while p and p!=root:p=p.parent
  assert p==root,(a['id'],o.name)
  if o.type=='MESH':assert o.data.uv_layers and len(o.data.materials)>0
 for p in a['movable_parts']:assert bpy.data.objects[p['name']].type=='EMPTY' and len(bpy.data.objects[p['name']].children)>0
 orig,cl=metrics(col.objects);parents={o.name:o.parent.name if o.parent else None for o in col.objects};camera=s.camera.matrix_world.copy();ortho=s.camera.data.ortho_scale
 poses={'02_keys':('Key_Fob_Pivot',1,.30),'03_entrance':('Door_Hinge',2,-1.65),'04_luggage':('Suitcase_Telescopic_Handle',2,-.23),'07_workspace':('Laptop_Screen_Hinge',0,1.5708),'10_calendar':('Calendar_Front_Page',0,2.4),'11_welcome':('Welcome_Cover_Hinge',2,-1.8)}
 if a['id'] in poses:
  name,axis,val=poses[a['id']];p=bpy.data.objects[name]
  if a['id']=='04_luggage':p.location[axis]=val
  else:p.rotation_euler[axis]=val
  lo,hi=bounds(col.objects)
  # Reframe alternative pose for inspection, preserving source camera in the file.
  if a['id'] in {'03_entrance','10_calendar','11_welcome'}:rig(s,lo,hi)
  render(a['id']+'_pose.png')
 bpy.ops.wm.read_factory_settings(use_empty=True);bpy.ops.import_scene.gltf(filepath=str(P/a['glb']));imp,other=metrics(bpy.context.scene.objects)
 assert set(parents)<={o.name for o in bpy.data.objects}
 assert all((bpy.data.objects[n].parent.name if bpy.data.objects[n].parent else None)==p for n,p in parents.items())
 err=max(distance(cl[n],other[n]) for n in cl);assert err<1e-5,(a['id'],err)
 assert orig['triangles']==imp['triangles'],(a['id'],'triangles',orig['triangles'],imp['triangles'])
 assert orig['materials']==imp['materials'],(a['id'],'materials',orig['materials'],imp['materials'])
 if a['id'] in {'01_brand_logo','02_keys','06_bedroom','11_welcome'}:
  s=bpy.context.scene;settings(s);lo,hi=bounds(s.objects);rig(s,lo,hi);s.camera.matrix_world=camera;s.camera.data.ortho_scale=ortho;render(a['id']+'_GLB.png')
 reports.append({'asset':a['id'],'blend_reopened':True,'roots_hierarchy_pivots_checked':True,'mesh_uvs_present':True,'asset_browser_thumbnail':True,'external_unpacked_dependencies':0,'glb_reimported':True,'names_parents_material_names_match':True,'source':orig,'glb':imp,'world_vertex_error_m':err})
 print('VERIFIED',a['id'],flush=True)
(P/'verification/geometry_checks.json').write_text(json.dumps(reports,indent=2))
bpy.ops.wm.open_mainfile(filepath=str(P/'TailorHomes_Master.blend'))
assert all(a['collection'] in bpy.data.collections and bpy.data.collections[a['collection']].asset_data for a in manifest['assets'])
bpy.ops.wm.open_mainfile(filepath=str(P/'animation/door_welcome.blend'));s=bpy.data.scenes['Door_Welcome_90_Frames'];bpy.context.window.scene=s
assert s.frame_start==1 and s.frame_end==90 and s.render.fps==30
values=[]
for f in range(1,91):s.frame_set(f);values.append(bpy.data.objects['Door_Hinge'].rotation_euler.z)
assert all(math.isfinite(v) for v in values) and all(values[i+1]<=values[i]+1e-6 for i in range(89)) and abs(values[0])<1e-6 and abs(values[-1]+1.65)<1e-5
(P/'verification/animation_checks.json').write_text(json.dumps({'master_reopened':True,'animation_reopened':True,'frames_checked':90,'fps':30,'rotation_z_by_frame':values,'motion_monotonic':True},indent=2));print('ALL_CHECKS_PASSED',flush=True)
