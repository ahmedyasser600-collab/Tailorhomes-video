"""Create source-versus-render comparison; normalized alpha silhouettes."""
from pathlib import Path
from PIL import Image,ImageDraw,ImageFont
import json
P=Path(__file__).resolve().parents[1]
images=[]
for f in ['brand/full_logo_reference.png','verification/logo_front_unlit.png']:
 im=Image.open(P/f).convert('RGBA');im=im.crop(im.getchannel('A').getbbox());im.thumbnail((1500,590),Image.Resampling.LANCZOS);images.append(im)
canvas=Image.new('RGB',(1600,1400),'#F5F0E8');d=ImageDraw.Draw(canvas);font=ImageFont.truetype(str(P/'brand/fonts/DMSans-Regular.ttf'),28)
for y,label,im in zip([70,750],['SUPPLIED SVG — original reference','BLENDER — front-on unlit render'],images):
 d.text((45,y-45),label,font=font,fill='#1B2A4A');canvas.paste(im,(45,y),im)
canvas.save(P/'verification/LOGO_COMPARISON.png')
a=images[0].getchannel('A').resize((1400,530));b=images[1].getchannel('A').resize((1400,530))
aa=[v>127 for v in a.getdata()];bb=[v>127 for v in b.getdata()]
r={'normalized_silhouette_iou':sum(x and y for x,y in zip(aa,bb))/sum(x or y for x,y in zip(aa,bb)),'note':'Independently cropped alpha bounds, resampled. Raster anti-aliasing and fine distressed details affect pixel scores; original SVG curves retained.'}
(P/'verification/logo_comparison.json').write_text(json.dumps(r,indent=2));print(r)
