#!/usr/bin/env python3
"""Create a development kit and a native test installer from an explicit checkpoint."""
import argparse, hashlib, json, re, subprocess, zipfile
from pathlib import Path
from package_update import write_member
parser=argparse.ArgumentParser()
parser.add_argument('--label',default='0.3.4-position-rc1')
parser.add_argument('--from-version',default='0.3.3')
args=parser.parse_args();label=args.label
prior=args.from_version
if not re.fullmatch(r'\d+\.\d+\.\d+',prior):raise SystemExit('Invalid prior version')
if not re.fullmatch(r'[a-zA-Z0-9.-]+',label):raise SystemExit('Invalid package label')
prefix='my-una-styled-posts-'+label
root=Path(__file__).resolve().parents[1]
module=root/'modules/newton/my_una_styled_posts'
out=root/'artifacts';out.mkdir(exist_ok=True)
revision=subprocess.check_output(['git','rev-parse','HEAD'],cwd=root,text=True).strip()
if subprocess.check_output(['git','status','--porcelain','--untracked-files=no'],cwd=root,text=True).strip():
    raise SystemExit('Refusing to package tracked changes outside the source checkpoint')
installer=out/(prefix+'-install.zip')
manifest_path=out/(prefix+'-manifest.json')
checksums_path=out/(prefix+'-SHA256SUMS.txt')
kit=out/(prefix+'-kit.zip')
version=re.search(r"'version'\s*=>\s*'([^']+)'",(module/'install/config.php').read_text()).group(1)
updater=out/(prefix+'-update-'+prior+'-to-'+version+'.zip')
for target in [installer,manifest_path,checksums_path,kit,updater]:
    if target.exists():raise SystemExit('Refusing to replace immutable artifact: '+str(target))
tracked=set(subprocess.check_output(['git','ls-files'],cwd=root,text=True).splitlines())
manifest=[]
with zipfile.ZipFile(installer,'w',zipfile.ZIP_DEFLATED) as z:
    for p in sorted(module.rglob('*')):
        if not p.is_file() or p.relative_to(root).as_posix() not in tracked:continue
        relative='my_una_styled_posts/'+p.relative_to(module).as_posix()
        data=p.read_bytes();write_member(z,relative,data)
        manifest.append({'path':relative,'bytes':len(data),'sha256':hashlib.sha256(data).hexdigest()})
with zipfile.ZipFile(installer) as z:
    assert z.testzip() is None
    assert 'my_una_styled_posts/install/config.php' in z.namelist()
    assert z.namelist()[0] == 'my_una_styled_posts/'
    assert all(n.startswith('my_una_styled_posts/') for n in z.namelist())
# Historical updater: update_0.2.0_0.3.0 remains immutable.
update_name='update_'+prior+'_'+version
update_root=root/'updates'/update_name
if "'version' => '"+version+"'" not in (module/'install/config.php').read_text() or "'version_to' => '"+version+"'" not in (update_root/'install/config.php').read_text():
    raise SystemExit('Updater/source version mismatch')
with zipfile.ZipFile(updater,'w',zipfile.ZIP_DEFLATED) as z:
    for p in sorted(update_root.rglob('*')):
        if p.is_file() and p.relative_to(root).as_posix() in tracked:
            write_member(z,update_name+'/'+p.relative_to(update_root).as_posix(),p.read_bytes())
    for p in sorted(module.rglob('*')):
        if p.is_file() and p.relative_to(root).as_posix() in tracked:
            write_member(z,update_name+'/source/'+p.relative_to(module).as_posix(),p.read_bytes())
with zipfile.ZipFile(updater) as z:
    assert z.testzip() is None
    assert update_name+'/install/config.php' in z.namelist()
    assert z.namelist()[0] == update_name+'/'
    assert all(n.startswith(update_name+'/') for n in z.namelist())
    for item in manifest:
        suffix=item['path'].removeprefix('my_una_styled_posts/')
        assert hashlib.sha256(z.read(update_name+'/source/'+suffix)).hexdigest()==item['sha256']
report={'module':'nw_styled_posts','version':version,'status':'development candidate; native site validation pending','source_commit':revision,'una_reference_commit':'05466c74ce6bbe4d8579fa901c6dc787d1dcff75','installer':installer.name,'installer_bytes':installer.stat().st_size,'installer_sha256':hashlib.sha256(installer.read_bytes()).hexdigest(),'updater':updater.name,'updater_bytes':updater.stat().st_size,'updater_sha256':hashlib.sha256(updater.read_bytes()).hexdigest(),'files':manifest}
manifest_path.write_text(json.dumps(report,indent=2)+'\n')
checksums_path.write_text(report['installer_sha256']+'  '+installer.name+'\n'+report['updater_sha256']+'  '+updater.name+'\n')
with zipfile.ZipFile(kit,'w',zipfile.ZIP_DEFLATED) as z:
    for p in [installer,updater,manifest_path,checksums_path]:z.write(p,p.name)
    for directory in ['modules','updates','tests','preview','scripts','docs']:
        for p in sorted((root/directory).rglob('*')):
            if p.is_file() and p.relative_to(root).as_posix() in tracked:z.write(p,'source/'+p.relative_to(root).as_posix())
    for name in ['README.md','LICENSE','THIRD_PARTY.md']:z.write(root/name,'source/'+name)
    write_member(z,'START-HERE.txt','My UNA Styled Posts ' +version+ ' — development kit\n\nOpen source/preview/index.html for the working editor preview.\nRead source/README.md before testing the native installer.\nSee source/docs/evidence/release-readiness-2026-10-26.md for live evidence and remaining release gates.\nThis kit is a release candidate preparation, not a final certified release.\n')
with zipfile.ZipFile(kit) as z:assert z.testzip() is None
print(json.dumps({'source_commit':revision,'installer':str(installer),'installer_files':len(manifest),'updater':str(updater),'development_kit':str(kit),'kit_bytes':kit.stat().st_size,'kit_sha256':hashlib.sha256(kit.read_bytes()).hexdigest()},indent=2))

