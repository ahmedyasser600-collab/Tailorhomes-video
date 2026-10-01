from pathlib import Path
import subprocess,json,tempfile,os
T=Path(tempfile.mkdtemp(prefix="tailor-encode-"))/"review.mp4"
from PIL import Image
P=Path(__file__).resolve().parents[1];frames=P/'animation/frames';assert len(list(frames.glob('frame_*.png')))==90
boxes=[]
for i in range(1,91):
 im=Image.open(frames/f'frame_{i:04d}.png');assert im.size==(640,640) and im.mode=='RGBA';b=im.getchannel('A').getbbox();assert b and b[0]>0 and b[1]>0 and b[2]<640 and b[3]<640,(i,b);boxes.append(b)
subprocess.run(['ffmpeg','-y','-f','lavfi','-i','color=c=0xF5F0E8:s=640x640:r=30:d=3','-framerate','30','-start_number','1','-i',str(frames/'frame_%04d.png'),'-filter_complex','[0:v][1:v]overlay=shortest=1:format=auto,format=yuv420p[v]','-map','[v]','-frames:v','90','-r','30','-c:v','libx264','-crf','18','-movflags','+faststart',str(T)],check=True)
d=json.loads(subprocess.check_output(['ffprobe','-v','error','-count_frames','-show_streams','-show_format','-of','json',str(T)]))
v=d['streams'][0];assert v['width']==640 and v['height']==640 and v['r_frame_rate']=='30/1' and int(v['nb_read_frames'])==90 and abs(float(d['format']['duration'])-3)<.01
(P/'verification/video_probe.json').write_text(json.dumps(d,indent=2));(P/'verification/frame_bounds.json').write_text(json.dumps(boxes));print('Verified 90 RGBA frames, no edge clipping; MP4 640x640, 30fps, 3s')

(P/"animation/TailorHomes_Arrival_Review.mp4").write_bytes(T.read_bytes())
