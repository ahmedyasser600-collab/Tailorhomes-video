"""Tailor Homes: original modular hospitality assets. Blender 4.5 LTS.
Run in a COPY: blender -b --factory-startup -t 6 --python scripts/build_library.py -- --render
"""
import bpy,math,json,sys,argparse
from pathlib import Path
from mathutils import Vector,Matrix
P=Path(__file__).resolve().parents[1]
ap=argparse.ArgumentParser();ap.add_argument('--render',action='store_true');ap.add_argument('--only',default='')
A=ap.parse_args(sys.argv[sys.argv.index('--')+1:] if '--' in sys.argv else [])
M={};F={};C=None;R=None
CAT='f2124b71-f0a4-4d1e-9b2c-9a010dfd0942'
def lin(x):return x/12.92 if x<=.04045 else ((x+.055)/1.055)**2.4
def material(name,h,rough=.45,metal=0,trans=0):
 m=bpy.data.materials.new(name);m.use_nodes=True;m.use_fake_user=True
 rgb=tuple(lin(int(h[i:i+2],16)/255) for i in (0,2,4));m.diffuse_color=(*rgb,1)
 n=m.node_tree.nodes.get('Principled BSDF');n.inputs['Base Color'].default_value=(*rgb,1);n.inputs['Roughness'].default_value=rough;n.inputs['Metallic'].default_value=metal;n.inputs['Transmission Weight'].default_value=trans
 M[name]=m;return m

def setup_materials():
 for a in [('Cream','F5F0E8',.65,0),('Navy','1B2A4A',.45,0),('Terracotta','B5473A',.5,0),('Red','B8312F',.4,0),('Ink','1A1916',.4,0),('Warm_BG','E5E1DA',.7,0),('White','F5F3EF',.4,0),('Oak','AC8158',.5,0),('Metal','ACAFB1',.23,.85),('Brass','C2A279',.3,.8),('Linen','DDD2BC',.8,0),('Screen','152138',.3,0),('Olive','67715B',.7,0)]:material(*a)
 material('Glass','F1F6F5',.06,0,1)
 m=material('SVG_Original_Gradient','FFFFFF',.48);n=m.node_tree.nodes
 im=bpy.data.images.load(str(P/'brand/logo_gradient.png'));im.pack();tex=n.new('ShaderNodeTexImage');tex.image=im;tex.extension='EXTEND';tc=n.new('ShaderNodeTexCoord');m.node_tree.links.new(tc.outputs['Generated'],tex.inputs['Vector']);m.node_tree.links.new(tex.outputs['Color'],n.get('Principled BSDF').inputs['Base Color'])
 for key,file in [('sans','DMSans-Regular.ttf'),('serif','CormorantGaramond-Regular.ttf'),('label','Jost-Regular.ttf')]:F[key]=bpy.data.fonts.load(str(P/'brand/fonts'/file))

def empty(name,loc=(0,0,0),parent=True):
 o=bpy.data.objects.new(name,None);C.objects.link(o);o.location=loc;o.empty_display_size=.07
 if parent:o.parent=R
 return o

def finish(o,name,mat,bevel=0,smooth=False):
 o.name=name
 for c in list(o.users_collection):c.objects.unlink(o)
 C.objects.link(o);o.parent=R;o.data.materials.clear();o.data.materials.append(M[mat])
 if bevel:
  b=o.modifiers.new('Editable_edge_radius','BEVEL');b.width=bevel;b.segments=3
  n=o.modifiers.new('Weighted_normals','WEIGHTED_NORMAL');n.keep_sharp=True
 if smooth:
  for p in o.data.polygons:p.use_smooth=True
 return o

def box(name,loc,size,mat,bevel=.008):
 bpy.ops.mesh.primitive_cube_add(size=1,location=loc);o=bpy.context.object;o.dimensions=size;bpy.ops.object.transform_apply(location=False,rotation=False,scale=True);return finish(o,name,mat,bevel)
def cyl(name,loc,r,d,mat,rot=(0,0,0),bevel=.002):
 bpy.ops.mesh.primitive_cylinder_add(vertices=48,radius=r,depth=d,location=loc,rotation=rot);bpy.ops.object.transform_apply(location=False,rotation=False,scale=True);return finish(bpy.context.object,name,mat,bevel,True)
def torus(name,loc,major,minor,mat,rot=(0,0,0)):
 bpy.ops.mesh.primitive_torus_add(major_radius=major,minor_radius=minor,major_segments=64,minor_segments=12,location=loc,rotation=rot);return finish(bpy.context.object,name,mat,0,True)
def ball(name,loc,dim,mat):
 bpy.ops.mesh.primitive_uv_sphere_add(segments=32,ring_count=16,location=loc);o=bpy.context.object;o.scale=dim;bpy.ops.object.transform_apply(location=False,rotation=False,scale=True);return finish(o,name,mat,0,True)
def tube(name,pts,r,mat):
 cv=bpy.data.curves.new(name+'_Path','CURVE');cv.dimensions='3D';cv.bevel_depth=r;cv.bevel_resolution=3;cv.resolution_u=12;cv.use_fill_caps=True
 sp=cv.splines.new('BEZIER');sp.bezier_points.add(len(pts)-1)
 for p,co in zip(sp.bezier_points,pts):p.co=co;p.handle_left_type='AUTO';p.handle_right_type='AUTO'
 ob=bpy.data.objects.new(name,cv);C.objects.link(ob);ob.parent=R;cv.materials.append(M[mat]);return ob

def text(name,body,loc,size,mat='Navy',font='sans'):
 cu=bpy.data.curves.new(name+'_Editable_Text','FONT');cu.body=body;cu.font=F[font];cu.size=size;cu.align_x='CENTER';cu.align_y='CENTER';cu.extrude=.0001;cu.resolution_u=8
 ob=bpy.data.objects.new(name,cu);C.objects.link(ob);ob.parent=R;ob.location=loc;ob.rotation_euler.x=math.pi/2;cu.materials.append(M[mat]);return ob

def child(o,p):
 bpy.context.view_layer.update();w=o.matrix_world.copy();o.parent=p;o.matrix_world=w

def pivot(name,loc,axis,limits):
 o=empty(name,loc);o['animation_axis']=axis;o['range']=limits;return o

def bounds(obs):
 bpy.context.view_layer.update();pts=[o.matrix_world@Vector(v) for o in obs if o.type in {'MESH','CURVE','FONT'} for v in o.bound_box]
 return Vector(tuple(min(p[i] for p in pts) for i in range(3))),Vector(tuple(max(p[i] for p in pts) for i in range(3)))

def import_svg(file,width,base=(0,0,0),prefix='Logo',parent=None,depth=.004):
 before=set(bpy.data.objects);bpy.ops.import_curve.svg(filepath=str(P/'brand'/file));obs=sorted(set(bpy.data.objects)-before,key=lambda o:o.name)
 # Imported SVG paths are in the XY plane. Bake only existing placement into path coordinates.
 lo,hi=bounds(obs);factor=width/(hi.x-lo.x);cx=(hi.x+lo.x)/2
 for i,o in enumerate(obs):
  mw=o.matrix_world.copy()
  for s in o.data.splines:
   for p in s.bezier_points:
    for attr in ['co','handle_left','handle_right']:
     v=mw@getattr(p,attr);setattr(p,attr,((v.x-cx)*factor,(v.y-lo.y)*factor,0))
   for p in s.points:
    v=mw@Vector(p.co[:3]);p.co=((v.x-cx)*factor,(v.y-lo.y)*factor,0,1)
  o.matrix_world=Matrix.Identity(4);o.location=base;o.rotation_euler.x=math.pi/2
  o.data.dimensions='2D';o.data.fill_mode='BOTH';o.data.resolution_u=16;o.data.extrude=depth/2;o.data.bevel_depth=0
  for col in list(o.users_collection):col.objects.unlink(o)
  C.objects.link(o);o.parent=parent or R;o.name=prefix+'_Path_%02d'%i;o['svg_source']=file;o['no_outline_bevel']=True
  # Curves do not reliably provide Generated coordinates. Map original local X explicitly.
  bpy.context.view_layer.update();xs=[v[0] for v in o.bound_box]
  m=M['SVG_Original_Gradient'].copy();m.name='SVG_Gradient_'+o.name
  nodes=m.node_tree.nodes;tex=next(n for n in nodes if n.type=='TEX_IMAGE');tc=nodes.new('ShaderNodeTexCoord');tc.object=o
  sep=nodes.new('ShaderNodeSeparateXYZ');rng=nodes.new('ShaderNodeMapRange');rng.inputs['From Min'].default_value=min(xs);rng.inputs['From Max'].default_value=max(xs)
  comb=nodes.new('ShaderNodeCombineXYZ');comb.inputs['Y'].default_value=.5
  m.node_tree.links.new(tc.outputs['Object'],sep.inputs[0]);m.node_tree.links.new(sep.outputs['X'],rng.inputs['Value']);m.node_tree.links.new(rng.outputs['Result'],comb.inputs['X']);m.node_tree.links.new(comb.outputs[0],tex.inputs['Vector'])
  o.data.materials.clear();o.data.materials.append(m)
 return obs

def brand_logo():
 parts=[empty('Logo_Emblem'),empty('Logo_Monogram'),empty('Logo_Wordmark'),empty('Logo_Descriptor')]
 obs=import_svg('01-noBgColor.svg',1.6,prefix='Full_Logo',depth=.004)
 for i,o in enumerate(obs):child(o,parts[0 if i<11 else i-10])
 R['source']='01-noBgColor.svg';R['extrusion_m']=.004;R['outline_bevel_m']=0

def keys():
 torus('Key_Ring',(0,0,.225),.036,.004,'Metal',(math.pi/2,0,0))
 p=pivot('Key_Fob_Pivot',(0,0,.208),'Y rotation radians',[-.3,.3])
 child(box('Leather_Fob',(0,-.006,.128),(.095,.018,.15),'Terracotta',.014),p)
 child(torus('Fob_Eyelet',(0,-.018,.195),.008,.002,'Brass',(math.pi/2,0,0)),p)
 for o in import_svg('04-symbol.svg',.058,base=(0,-.018,.112),prefix='Fob_TH',depth=.0005):child(o,p)
 p=pivot('Key_Pivot',(.028,.014,.215),'Y rotation radians',[-.6,.6])
 child(torus('Key_Head',(.050,.015,.185),.019,.006,'Brass',(math.pi/2,0,0)),p)
 child(box('Key_Shaft',(.050,.015,.105),(.011,.007,.125),'Brass',.0015),p)
 for i in range(3):child(box('Key_Tooth',(.062,.015,.05+i*.018),(.025,.007,.007),'Brass',.001),p)

def door():
 for x in (-.535,.535):box('Door_Jamb',(x,0,1.08),(.09,.16,2.16),'Cream')
 box('Door_Lintel',(0,0,2.17),(1.16,.16,.10),'Cream');box('Door_Threshold',(0,0,.015),(1.08,.18,.03),'Oak',.003)
 p=pivot('Door_Hinge',(-.485,0,.04),'Z rotation radians',[-1.75,0])
 child(box('Door_Leaf',(0,0,1.075),(.965,.052,2.06),'Navy',.005),p)
 for z in (.60,1.52):
  child(box('Door_Raised_Panel',(0,-.03,z),(.72,.014,.60),'Navy',.006),p)
 child(box('Door_Smart_Lock',(.35,-.038,1.07),(.058,.035,.17),'Ink',.012),p)
 child(box('Door_Lock_Display',(.35,-.058,1.105),(.035,.003,.053),'Screen',.003),p)
 child(cyl('Door_Handle_Base',(.35,-.058,.985),.021,.02,'Brass',(math.pi/2,0,0)),p)
 child(box('Door_Handle',(.295,-.082,.985),(.14,.018,.02),'Brass'),p)
 for z in (.25,1.10,1.94):cyl('Door_Hinge_Barrel',(-.487,0,z),.012,.085,'Brass')
 R['door_width_m']=.965

def suitcase():
 box('Suitcase_Shell',(0,0,.34),(.39,.245,.53),'Terracotta',.05)
 box('Suitcase_Zip_Band',(0,0,.34),(.401,.016,.535),'Ink',.026)
 for x in [-.135,-.09,-.045,0,.045,.09,.135]:box('Suitcase_Rib',(x,-.128,.34),(.012,.01,.39),'Terracotta',.004)
 for x in (-.135,.135):
  for y in (-.07,.07):cyl('Suitcase_Wheel',(x,y,.052),.04,.025,'Ink',(math.pi/2,0,0))
 p=pivot('Suitcase_Telescopic_Handle',(0,0,0),'Z translation metres',[-.23,0])
 for x in (-.10,.10):child(box('Handle_Rail',(x,.065,.70),(.015,.014,.35),'Metal',.003),p)
 child(box('Handle_Grip',(0,.065,.88),(.23,.028,.037),'Navy',.012),p)
 box('Suitcase_Carry_Handle',(0,0,.624),(.13,.036,.026),'Navy')

def sofa():
 for x in (-.83,.83):
  for y in (-.29,.29):cyl('Sofa_Foot',(x,y,.075),.035,.15,'Oak')
 box('Sofa_Base',(0,0,.23),(1.95,.85,.24),'Linen',.06)
 for x in (-.91,.91):box('Sofa_Arm',(x,0,.49),(.20,.87,.42),'Linen',.065)
 for x in (-.43,.43):
  box('Sofa_Seat_Cushion',(x,-.085,.405),(.84,.64,.17),'Cream',.07)
  ob=box('Sofa_Back_Cushion',(x,.31,.67),(.84,.18,.56),'Linen',.07);ob.rotation_euler.x=-.12
 p=box('Accent_Cushion',(-.64,.07,.65),(.32,.14,.34),'Terracotta',.065);p.rotation_euler.y=-.20
 p=box('Accent_Cushion',(.64,.08,.65),(.30,.14,.31),'Navy',.06);p.rotation_euler.y=.2
 cyl('Coffee_Table_Top',(0,-1.02,.38),.36,.045,'Oak',bevel=.012)
 for a in (0,2.094,4.188):cyl('Coffee_Table_Leg',(.22*math.cos(a),-1.02+.22*math.sin(a),.19),.023,.37,'Ink')
 box('Coffee_Table_Book',(.02,-1.02,.417),(.21,.15,.025),'Cream',.002)

def bed():
 for x in (-.66,.66):
  for y in (-.84,.84):cyl('Bed_Foot',(x,y,.09),.04,.18,'Oak')
 box('Bed_Base',(0,0,.23),(1.65,2.10,.24),'Oak',.035)
 box('Mattress',(0,-.02,.445),(1.60,2,.23),'White',.09)
 box('Upholstered_Headboard',(0,1.03,.70),(1.76,.12,1.16),'Linen',.06)
 box('Duvet',(0,-.30,.59),(1.65,1.50,.12),'Cream',.085)
 box('Bed_Runner',(0,-.70,.659),(1.67,.40,.024),'Terracotta',.009)
 for x in (-.39,.39):box('Pillow',(x,.65,.622),(.67,.40,.16),'White',.075)
 for x in (-1.08,1.08):
  box('Nightstand',(x,.78,.34),(.34,.36,.46),'Navy',.02)
  cyl('Lamp_Base',(x,.78,.595),.09,.025,'Brass');cyl('Lamp_Stem',(x,.78,.735),.012,.27,'Brass')
  bpy.ops.mesh.primitive_cone_add(vertices=48,radius1=.125,radius2=.065,depth=.18,location=(x,.78,.895));finish(bpy.context.object,'Lamp_Shade','Cream',.003,True)

def desk():
 box('Desk_Top',(0,0,.745),(1.2,.6,.05),'Oak',.015)
 for x in (-.52,.52):
  for y in (-.23,.23):box('Desk_Leg',(x,y,.36),(.04,.04,.72),'Navy')
 box('Laptop_Base',(0,0,.782),(.34,.235,.015),'Metal',.004)
 p=pivot('Laptop_Screen_Hinge',(0,.108,.793),'X rotation radians',[-.10,1.5708])
 child(box('Laptop_Lid',(0,.109,.9),(.34,.014,.218),'Navy',.009),p)
 child(box('Laptop_Replaceable_Screen',(0,.099,.902),(.313,.003,.187),'Screen',.004),p)
 child(text('Laptop_Demo_Label','DEMO',(0,.096,.905),.025,'Cream','label'),p)
 box('Laptop_Keyboard',(0,.014,.792),(.28,.10,.003),'Ink',.002);box('Laptop_Trackpad',(0,-.07,.792),(.10,.052,.003),'Navy',.002)
 box('Notebook',(.40,-.02,.784),(.17,.22,.024),'Terracotta',.004)
 cyl('Desk_Mug',(-.40,.05,.819),.035,.10,'Cream');torus('Mug_Handle',(-.448,.05,.83),.025,.006,'Cream',(math.pi/2,0,0))
 for x in (-.21,.21):
  for y in (-.67,-1.02):box('Chair_Leg',(x,y,.23),(.027,.027,.46),'Oak')
 box('Chair_Seat',(0,-.84,.465),(.48,.45,.08),'Linen',.04);box('Chair_Back',(0,-1.045,.70),(.47,.06,.45),'Navy',.04)

def room():
 box('Apartment_Floor',(0,0,-.07),(3.6,3.5,.14),'Oak')
 box('Apartment_Rear_Wall',(0,1.79,1.30),(3.76,.16,2.6),'Cream')
 # Left wall contains an actual opening for the window.
 box('Wall_Below_Window',(-1.88,0,.40),(.16,3.5,.80),'Cream')
 box('Wall_Above_Window',(-1.88,0,2.46),(.16,3.5,.28),'Cream')
 for y in (-1.24,1.24):box('Wall_Window_Side',(-1.88,y,1.56),(.16,1.02,1.52),'Cream')
 for y in (-.73,.73):box('Window_Jamb',(-1.88,y,1.56),(.19,.05,1.52),'Navy')
 for z in (.81,2.30):box('Window_Frame',(-1.88,0,z),(.19,1.51,.05),'Navy')
 box('Window_Mullion',(-1.88,0,1.56),(.19,.04,1.48),'Navy')
 box('Window_Glass',(-1.88,0,1.56),(.008,1.42,1.42),'Glass',.001)
 box('Right_Cutaway_Wall',(1.88,0,.23),(.16,3.5,.46),'Cream')
 for y in [-1.5,-1.0,-.5,0,.5,1.0,1.5]:box('Floor_Plank_Seam',(0,y,.001),(3.6,.004,.001),'Warm_BG',0)
 R['interior_width_m']=3.6;R['interior_depth_m']=3.5;R['wall_height_m']=2.6

def facade():
 box('Building_Base',(0,0,.04),(1.7,.85,.08),'Warm_BG')
 box('Building_Mass',(0,.10,.86),(1.42,.58,1.65),'Cream',.012)
 for z in (.16,.70,1.24,1.70):box('Facade_Cornice',(0,-.22,z),(1.50,.055,.035),'White',.003)
 for z in (.45,.99,1.49):
  for x in (-.47,0,.47):
   box('Facade_Window_Trim',(x,-.214,z),(.28,.03,.32),'White',.003)
   box('Facade_Window',(x,-.234,z),(.23,.013,.27),'Navy',.002)
   for xx in (x-.16,x+.16):box('Facade_Shutter',(xx,-.235,z),(.065,.027,.30),'Terracotta',.003)
 box('Building_Entrance',(0,-.239,.21),(.20,.025,.34),'Oak',.004)
 # A simple pitched roof, generic architecture rather than an identifiable real property.
 v=[(-.77,-.24,1.73),(.77,-.24,1.73),(-.77,.45,1.73),(.77,.45,1.73),(-.77,.105,1.96),(.77,.105,1.96)]
 me=bpy.data.meshes.new('Roof_Mesh');me.from_pydata(v,[],[(0,1,5,4),(4,5,3,2),(0,4,2),(1,3,5),(0,2,3,1)]);me.update();ob=bpy.data.objects.new('Pitched_Roof',me);C.objects.link(ob);ob.parent=R;me.materials.append(M['Terracotta'])

def calendar():
 box('Calendar_Back',(0,.018,.21),(.37,.027,.40),'Navy',.012)
 for z in (.15,.23,.31):box('Calendar_Page_Edge',(0,-.008,z),(.345,.005,.004),'Warm_BG',.001)
 p=pivot('Calendar_Front_Page',(0,-.016,.385),'X rotation radians',[0,2.5])
 child(box('Calendar_Page',(0,-.015,.215),(.34,.003,.34),'Cream',.002),p)
 child(text('Calendar_Title','SOGGIORNO',(0,-.019,.338),.028,'Navy','label'),p)
 child(text('Calendar_Sample_Label','ESEMPIO',(0,-.019,.307),.012,'Red','label'),p)
 for row in range(3):
  for col in range(7):
   x=-.126+col*.042;z=.261-row*.052
   if row==1 and col in (1,2,3):child(box('Calendar_Booked_Cell',(x,-.019,z),(.038,.001,.04),'Terracotta',.002),p)
   child(text('Calendar_Day',str(1+row*7+col).zfill(2),(x,-.021,z),.018,'White' if row==1 and col in (1,2,3) else 'Navy','label'),p)
 for x in (-.12,.12):torus('Calendar_Ring',(x,0,.398),.018,.003,'Brass',(0,math.pi/2,0))

def welcome():
 box('Folder_Back',(0,.014,.165),(.25,.018,.33),'Navy',.008)
 p=pivot('Welcome_Cover_Hinge',(-.122,0,0),'Z rotation radians',[-2.0,0])
 child(box('Folder_Cover',(0,-.008,.165),(.25,.008,.33),'Cream',.005),p)
 for o in import_svg('04-symbol.svg',.072,base=(0,-.014,.212),prefix='Welcome_TH',depth=.0004):child(o,p)
 child(text('Welcome_Title','Benvenuti',(0,-.014,.151),.037,'Navy','serif'),p)
 child(text('Welcome_Label','CASA ESEMPIO',(0,-.014,.114),.015,'Red','label'),p)
 box('Welcome_Inside_Card',(0,-.001,.164),(.221,.004,.285),'White',.003)
 text('Welcome_Inside_Title','La tua casa,\nanche in viaggio.',(0,-.004,.20),.022,'Navy','serif')
 text('Welcome_Inside_Demo','ESEMPIO · NESSUN DATO OSPITE',(0,-.004,.095),.0065,'Terracotta','label')

def marker():
 cyl('Pin_Platform',(0,0,.025),.30,.05,'Cream',bevel=.009)
 # Outline with a real hole formed by nested closed 2D splines.
 cv=bpy.data.curves.new('Pin_Editable_Outline','CURVE');cv.dimensions='2D';cv.fill_mode='BOTH';cv.extrude=.022;cv.resolution_u=24
 outer=cv.splines.new('BEZIER');coords=[(0,.10),(-.12,.29),(-.19,.49),(-.135,.65),(0,.71),(.135,.65),(.19,.49),(.12,.29)];outer.bezier_points.add(len(coords)-1)
 for p,co in zip(outer.bezier_points,coords):p.co=(*co,0);p.handle_left_type='AUTO';p.handle_right_type='AUTO'
 outer.bezier_points[0].handle_left_type='VECTOR';outer.bezier_points[0].handle_right_type='VECTOR';outer.use_cyclic_u=True
 inner=cv.splines.new('BEZIER');inner.bezier_points.add(7)
 for i,p in enumerate(inner.bezier_points):a=-2*math.pi*i/8;p.co=(.084*math.cos(a),.506+.084*math.sin(a),0);p.handle_left_type='AUTO';p.handle_right_type='AUTO'
 inner.use_cyclic_u=True
 ob=bpy.data.objects.new('Location_Pin',cv);C.objects.link(ob);ob.parent=R;ob.rotation_euler.x=math.pi/2;cv.materials.append(M['Terracotta'])
 text('Location_Label','PADOVA',(0,-.22,.069),.052,'Navy','label').rotation_euler.x=0

BUILDERS=[('01_brand_logo','Vector brand lockup',brand_logo,'Brand reveals; original vector paths'),('02_keys','Branded key set',keys,'Arrival and property handover'),('03_entrance','Opening entrance door',door,'Welcome transitions; smart access'),('04_luggage','Cabin suitcase',suitcase,'Business stays and relocation'),('05_lounge','Lounge and coffee table',sofa,'Comfort and home staging'),('06_bedroom','Bed and nightstands',bed,'Furnished stay and sleep comfort'),('07_workspace','Desk and laptop',desk,'Corporate housing and study stays'),('08_apartment','Cutaway apartment shell',room,'Layout and room assembly'),('09_building','Generic apartment facade',facade,'Property management and portfolios'),('10_calendar','Booking calendar',calendar,'Flexible stays; editable sample dates'),('11_welcome','Welcome folder',welcome,'Guest welcome; opening cover'),('12_location','Padova location marker',marker,'Location and arrival sequences')]

def settings(s):
 s.unit_settings.system='METRIC';s.unit_settings.scale_length=1;s.render.engine='CYCLES';s.cycles.samples=32;s.cycles.use_denoising=True;s.cycles.max_bounces=8;s.cycles.film_transparent_glass=True
 s.render.resolution_x=700;s.render.resolution_y=700;s.render.resolution_percentage=100;s.render.image_settings.file_format='PNG';s.render.image_settings.color_mode='RGBA';s.render.film_transparent=True;s.render.fps=30;s.frame_start=1;s.frame_end=90;s.view_settings.view_transform='AgX'
 w=bpy.data.worlds.new(s.name+'_World');w.use_nodes=True;w.node_tree.nodes['Background'].inputs[0].default_value=(.40,.39,.37,1);w.node_tree.nodes['Background'].inputs[1].default_value=.4;s.world=w

def rig(s,lo,hi):
 rc=bpy.data.collections.new(s.name+'_STUDIO');s.collection.children.link(rc);center=(lo+hi)/2;span=max(max(hi-lo),.2)
 ca=bpy.data.cameras.new('Camera');o=bpy.data.objects.new('Camera',ca);rc.objects.link(o);o.location=center+Vector((1.2,-1.9,1.05))*span;o.rotation_euler=(center-o.location).to_track_quat('-Z','Y').to_euler();ca.type='ORTHO';ca.ortho_scale=span*1.60;s.camera=o
 for name,v,en,size in [('Key',(-1.5,-2,3),450,2.2),('Fill',(2,-.8,1.8),260,1.8),('Rim',(.5,2,2.8),600,2)]:
  l=bpy.data.lights.new(name,'AREA');l.energy=en*span**2;l.shape='DISK';l.size=size*span;o=bpy.data.objects.new(name,l);rc.objects.link(o);o.location=center+Vector(v)*span;o.rotation_euler=(center-o.location).to_track_quat('-Z','Y').to_euler()
 return rc

def uv_meshes(col):
 for o in col.objects:
  if o.type!='MESH' or o.data.uv_layers:continue
  bpy.ops.object.select_all(action='DESELECT');o.select_set(True);bpy.context.view_layer.objects.active=o;bpy.ops.object.mode_set(mode='EDIT');bpy.ops.mesh.select_all(action='SELECT');bpy.ops.uv.smart_project(island_margin=.02);bpy.ops.object.mode_set(mode='OBJECT')

def export(s,col,path):
 temp=bpy.data.collections.new('EXPORT_TEMP');s.collection.children.link(temp);copies={};names={o:o.name for o in col.objects}
 for src in col.objects:
  ob=src.copy()
  if src.data:ob.data=src.data.copy()
  temp.objects.link(ob);copies[src]=ob
 for src,o in copies.items():o.parent=copies.get(src.parent);o.matrix_local=src.matrix_local.copy()
 bpy.ops.object.select_all(action='DESELECT')
 for o in copies.values():
  if o.type in {'CURVE','FONT'}:
   o.select_set(True);bpy.context.view_layer.objects.active=o;bpy.ops.object.convert(target='MESH');o.select_set(False)
  if o.type=='MESH' and 'svg_source' in o:
   me=o.data;uv=me.uv_layers.new(name='SVG_Gradient_UV');lo=min(v.co.x for v in me.vertices);hi=max(v.co.x for v in me.vertices)
   for p in me.polygons:
    for idx in p.loop_indices:uv.data[idx].uv=((me.vertices[me.loops[idx].vertex_index].co.x-lo)/(hi-lo),.5)
 for o in col.objects:o.name='SOURCE_'+names[o]
 for src,o in copies.items():o.name=names[src];o.select_set(True)
 bpy.ops.export_scene.gltf(filepath=str(path),export_format='GLB',use_selection=True,export_apply=True,export_animations=False,export_cameras=False,export_lights=False,export_yup=True)
 for o in list(temp.objects):bpy.data.objects.remove(o,do_unlink=True)
 bpy.data.collections.remove(temp)
 for o,n in names.items():o.name=n

def main():
 global C,R
 bpy.ops.wm.read_factory_settings(use_empty=True);bpy.ops.preferences.addon_enable(module='io_curve_svg');setup_materials();made=[];records=[]
 for key,title,build,use in BUILDERS:
  if A.only and A.only!=key:continue
  s=bpy.data.scenes.new(key);bpy.context.window.scene=s;settings(s);C=bpy.data.collections.new('TH_'+key);s.collection.children.link(C);R=empty('TH_'+key+'_ROOT',parent=False);R['units']='metres';R['front']='-Y'
  build();uv_meshes(C);lo,hi=bounds(C.objects);rig(s,lo,hi)
  if key=='01_brand_logo':
   center=(lo+hi)/2;s.camera.location=center+Vector((.14,-3,.15));s.camera.rotation_euler=(center-s.camera.location).to_track_quat('-Z','Y').to_euler();s.camera.data.ortho_scale=1.9
  if key=='08_apartment':s.cycles.samples=96
  C.asset_mark();C.asset_data.catalog_id=CAT;C.asset_data.description=title+' | '+use
  rec={'id':key,'title':title,'uses':use,'blend':'assets/'+key+'.blend','glb':'exports/'+key+'.glb','preview':'previews/'+key+'.png','collection':C.name,'root':R.name,'objects':[o.name for o in C.objects],'movable_parts':[{'name':o.name,'axis':o['animation_axis'],'range':list(o['range'])} for o in C.objects if 'animation_axis' in o],'dimensions_m':[round(x,5) for x in hi-lo]}
  export(s,C,P/rec['glb']);bpy.ops.file.pack_all()
  if A.render:
   s.render.filepath=str(P/rec['preview']);bpy.ops.render.render(write_still=True)
   with bpy.context.temp_override(id=C):bpy.ops.ed.lib_id_load_custom_preview(filepath=str(P/rec['preview']))
  s.render.filepath='//../previews/'+key+'.png';bpy.data.libraries.write(str(P/rec['blend']),{s},fake_user=True,compress=True)
  records.append(rec);made.append((s,C,R));print('ASSET_COMPLETE',key,flush=True)
 if A.only:return
 master=bpy.data.scenes.new('00_LIBRARY_OVERVIEW');settings(master)
 for i,(s,c,r) in enumerate(made):
  ob=bpy.data.objects.new(c.name+'_INSTANCE',None);ob.instance_type='COLLECTION';ob.instance_collection=c;master.collection.objects.link(ob);ob.location=((i%4)*5.5,(i//4)*5.5,0);s.render.filepath='//previews/'+s.name+'.png'
 rig(master,Vector((-2,-2,0)),Vector((19,14,3)));bpy.context.window.scene=master;bpy.ops.file.pack_all();bpy.ops.wm.save_as_mainfile(filepath=str(P/'TailorHomes_Master.blend'),compress=True)
 (P/'manifest.json').write_text(json.dumps({'version':'1.0','blender':bpy.app.version_string,'coordinates':{'source':'X right, -Y front, Z up','glb':'standard glTF Y up','units':'metres'},'brand':'brand/palette.json','assets':records},indent=2))
 (P/'blender_assets.cats.txt').write_text('VERSION 1\n'+CAT+':Tailor Homes/Hospitality:Tailor Homes Hospitality\n')
 animation(made[2])

def animation(source):
 s,col,root=source;s=s.copy();s.name='Door_Welcome_90_Frames';bpy.context.window.scene=s
 p=bpy.data.objects['Door_Hinge']
 for f,v in [(1,0),(12,0),(72,-1.65),(90,-1.65)]:p.rotation_euler.z=v;p.keyframe_insert(data_path='rotation_euler',frame=f)
 for fc in p.animation_data.action.fcurves:
  for k in fc.keyframe_points:k.interpolation='BEZIER';k.handle_left_type='AUTO_CLAMPED';k.handle_right_type='AUTO_CLAMPED'
 for c in list(s.collection.children):
  if c!=col:s.collection.children.unlink(c)
 rig(s,Vector((-.60,-1.05,0)),Vector((.6,.2,2.25)))
 s.frame_set(1);s.cycles.samples=16;s.render.resolution_x=640;s.render.resolution_y=640;s.render.filepath='//frames/frame_';bpy.data.libraries.write(str(P/'animation/door_welcome.blend'),{s},fake_user=True,compress=True)
 print('ANIMATION_SAVED',flush=True)
if __name__=='__main__':main()
