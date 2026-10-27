#!/usr/bin/env python3
"""Create a development kit and a native test installer from an explicit checkpoint."""
import argparse, hashlib, json, re, subprocess, zipfile
from pathlib import Path
from package_update import write_member
parser=argparse.ArgumentParser()
parser.add_argument('--label',default='0.3.0-development')
label=parser.parse_args().label
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
updater=out/(prefix+'-update-0.2.0-to-0.3.0.zip')
for target in [installer,manifest_path,checksums_path,kit,updater]:
    if target.exists():raise SystemExit('Refusing to replace immutable artifact: '+str(target))
tracked=set(subprocess.check_output(['git','ls-files'],cwd=root,text=True).splitlines())
manifest=[]
with zipfile.ZipFile(installer,'w',zipfile.ZIP_DEFLATED) as z:
    for p in sorted(module.rglob('*')):
        if not p.is_file() or p.relative_to(root).as_posix() not in tracked:continue
        relative='newton/my_una_styled_posts/'+p.relative_to(module).as_posix()
        data=p.read_bytes();write_member(z,relative,data)
        manifest.append({'path':relative,'bytes':len(data),'sha256':hashlib.sha256(data).hexdigest()})
with zipfile.ZipFile(installer) as z:
    assert z.testzip() is None
    assert 'newton/my_una_styled_posts/install/config.php' in z.namelist()
update_root=root/'updates/update_0.2.0_0.3.0'
if "'version' => '0.3.0'" not in (module/'install/config.php').read_text() or "'version_to' => '0.3.0'" not in (update_root/'install/config.php').read_text():
    raise SystemExit('Updater/source version mismatch')
with zipfile.ZipFile(updater,'w',zipfile.ZIP_DEFLATED) as z:
    for p in sorted(update_root.rglob('*')):
        if p.is_file() and p.relative_to(root).as_posix() in tracked:
            write_member(z,'update_0.2.0_0.3.0/'+p.relative_to(update_root).as_posix(),p.read_bytes())
    for p in sorted(module.rglob('*')):
        if p.is_file() and p.relative_to(root).as_posix() in tracked:
            write_member(z,'update_0.2.0_0.3.0/source/'+p.relative_to(module).as_posix(),p.read_bytes())
with zipfile.ZipFile(updater) as z:
    assert z.testzip() is None
    assert 'update_0.2.0_0.3.0/install/config.php' in z.namelist()
    for item in manifest:
        suffix=item['path'].removeprefix('newton/my_una_styled_posts/')
        assert hashlib.sha256(z.read('update_0.2.0_0.3.0/source/'+suffix)).hexdigest()==item['sha256']
report={'module':'nw_styled_posts','version':'0.3.0','status':'development candidate; native site validation pending','source_commit':revision,'una_reference_commit':'05466c74ce6bbe4d8579fa901c6dc787d1dcff75','installer':installer.name,'installer_bytes':installer.stat().st_size,'installer_sha256':hashlib.sha256(installer.read_bytes()).hexdigest(),'updater':updater.name,'updater_bytes':updater.stat().st_size,'updater_sha256':hashlib.sha256(updater.read_bytes()).hexdigest(),'files':manifest}
manifest_path.write_text(json.dumps(report,indent=2)+'\n')
checksums_path.write_text(report['installer_sha256']+'  '+installer.name+'\n'+report['updater_sha256']+'  '+updater.name+'\n')
with zipfile.ZipFile(kit,'w',zipfile.ZIP_DEFLATED) as z:
    for p in [installer,updater,manifest_path,checksums_path]:z.write(p,p.name)
    for directory in ['modules','updates','tests','preview','scripts','docs']:
        for p in sorted((root/directory).rglob('*')):
            if p.is_file() and p.relative_to(root).as_posix() in tracked:z.write(p,'source/'+p.relative_to(root).as_posix())
    for name in ['README.md','LICENSE','THIRD_PARTY.md']:z.write(root/name,'source/'+name)
    write_member(z,'START-HERE.txt','My UNA Styled Posts 0.3.0 — development kit\n\nOpen source/preview/index.html for the working editor preview.\nRead source/README.md before testing the native installer.\nSee source/docs/evidence/release-readiness-2026-10-26.md for live evidence and remaining release gates.\nThis kit is a release candidate preparation, not a final certified release.\n')
with zipfile.ZipFile(kit) as z:assert z.testzip() is None
print(json.dumps({'source_commit':revision,'installer':str(installer),'installer_files':len(manifest),'updater':str(updater),'development_kit':str(kit),'kit_bytes':kit.stat().st_size,'kit_sha256':hashlib.sha256(kit.read_bytes()).hexdigest()},indent=2))
