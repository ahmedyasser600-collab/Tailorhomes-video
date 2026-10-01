"""Package portable assets and verify archive CRC and content hashes."""
from pathlib import Path
import json,zipfile,hashlib,tempfile,shutil
P=Path(__file__).resolve().parents[1]
m=json.loads((P/'manifest.json').read_text())
required=['TailorHomes_Master.blend','README.md','CLAUDE_HANDOFF.md','VERIFICATION.md','TailorHomes_Catalog.png','blender_assets.cats.txt','animation/door_welcome.blend','animation/TailorHomes_Arrival_Review.mp4']
for a in m['assets']:required.extend([a['blend'],a['glb'],a['preview']])
required += [f'animation/frames/frame_{i:04d}.png' for i in range(1,91)]
for f in required:assert (P/f).is_file() and (P/f).stat().st_size>0,f
files=sorted(f for f in P.rglob('*') if f.is_file() and '__pycache__' not in f.parts and f.suffix not in ['.blend1','.pyc'] and f.name!='SHA256SUMS.json')
hashes={str(f.relative_to(P)):hashlib.sha256(f.read_bytes()).hexdigest() for f in files}
(P/'SHA256SUMS.json').write_text(json.dumps(hashes,indent=2));files.append(P/'SHA256SUMS.json')
ztemp=Path(tempfile.mkdtemp())/'TailorHomes_3D_Library.zip'
with zipfile.ZipFile(ztemp,'w',zipfile.ZIP_DEFLATED,6) as z:
 for f in files:z.write(f,str(Path(P.name)/f.relative_to(P)))
with zipfile.ZipFile(ztemp) as z:
 assert z.testzip() is None
 for f,h in hashes.items():assert hashlib.sha256(z.read(P.name+'/'+f)).hexdigest()==h
out=P.parent/ztemp.name;shutil.copyfile(ztemp,out)
with zipfile.ZipFile(out) as z:assert z.testzip() is None
report={'zip':out.name,'files':len(files),'bytes':out.stat().st_size,'sha256':hashlib.sha256(out.read_bytes()).hexdigest(),'crc_passed':True,'all_required_files_present':True,'all_content_hashes_match':True}
(P.parent/'PACKAGE_CHECK.json').write_text(json.dumps(report,indent=2));print(json.dumps(report))
