"""Make accurate labeled catalog/contact sheets from actual Blender renders."""
from pathlib import Path
from PIL import Image,ImageDraw,ImageFont
import json
P=Path(__file__).resolve().parents[1];font=lambda n:ImageFont.truetype(str(P/'brand/fonts/DMSans-Regular.ttf'),n)
serif=lambda n:ImageFont.truetype(str(P/'brand/fonts/CormorantGaramond-Regular.ttf'),n)
def paste(im,path,rect):
 src=Image.open(path).convert('RGBA');src.thumbnail(rect[2:],Image.Resampling.LANCZOS);im.paste(src,(rect[0]+(rect[2]-src.width)//2,rect[1]+(rect[3]-src.height)//2),src)
a=json.loads((P/'manifest.json').read_text())['assets']
im=Image.new('RGB',(1680,1780),'#F5F0E8');d=ImageDraw.Draw(im);d.rectangle((0,0,1680,10),fill='#B5473A');d.text((45,30),'Tailor Homes',font=serif(65),fill='#1B2A4A');d.text((47,108),'EDITABLE 3D LIBRARY  /  HOSPITALITY & PROPERTY MANAGEMENT',font=font(21),fill='#B5473A')
for i,v in enumerate(a):
 x=35+(i%4)*414;y=160+(i//4)*512;d.rounded_rectangle((x,y,x+395,y+490),radius=12,fill='#E5E1DA')
 paste(im,P/v['preview'],(x+8,y+8,379,365));d.text((x+18,y+375),v['id'][:2]+'  '+v['title'],font=font(20),fill='#1B2A4A')
 words=v['uses'].split();lines=['']
 for w in words:
  if d.textlength(lines[-1]+' '+w,font=font(16))>354:lines.append(w)
  else:lines[-1]=(lines[-1]+' '+w).strip()
 for j,line in enumerate(lines):d.text((x+18,y+410+j*23),line,font=font(16),fill='#3A3830')
d.text((45,1733),'12 original starter assets  •  Blender + GLB  •  Supplied vector logo preserved',font=font(20),fill='#3A3830');im.save(P/'TailorHomes_Catalog.png')
frames=P/'animation/frames'
if (frames/'frame_0090.png').exists():
 im=Image.new('RGB',(1600,790),'#F5F0E8');d=ImageDraw.Draw(im);d.text((30,18),'ARRIVAL / Opening door / 90 frames · 30 fps · 3 seconds',font=font(27),fill='#1B2A4A')
 for i,f in enumerate([1,10,20,30,40,50,60,70,80,90]):
  x=10+i%5*318;y=80+i//5*340;paste(im,frames/f'frame_{f:04d}.png',(x,y,310,286));d.text((x+12,y+297),f'Frame {f:02d} / {(f-1)/30:.2f}s',font=font(18),fill='#1B2A4A')
 im.save(P/'animation/CONTACT_SHEET.png')
 im=Image.new('RGB',(1600,1710),'#F5F0E8');d=ImageDraw.Draw(im)
 for i in range(90):
  x=i%10*160;y=i//10*190;paste(im,frames/f'frame_{i+1:04d}.png',(x,y,160,165));d.text((x+8,y+165),str(i+1),font=font(14),fill='#1B2A4A')
 im.save(P/'verification/ALL_90_FRAMES.png')
comp=[v for v in a if (P/'verification'/(v['id']+'_GLB.png')).exists()]
if comp:
 im=Image.new('RGB',(1000,70+len(comp)*320),'#F5F0E8');d=ImageDraw.Draw(im);d.text((70,15),'BLENDER SOURCE',font=font(25),fill='#1B2A4A');d.text((580,15),'REIMPORTED GLB',font=font(25),fill='#1B2A4A')
 for i,v in enumerate(comp):
  paste(im,P/v['preview'],(40,60+i*320,420,275));paste(im,P/'verification'/(v['id']+'_GLB.png'),(540,60+i*320,420,275));d.text((30,335+i*320),v['title'],font=font(18),fill='#1B2A4A')
 im.save(P/'verification/GLB_COMPARISON.png')
