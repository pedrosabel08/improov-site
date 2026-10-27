"""Read-only inventory of received services media, with content-based reuse."""
import argparse
import importlib.util
import json
from collections import defaultdict
from pathlib import Path
from PIL import Image, ImageOps

ROOT = Path(__file__).resolve().parents[1]
parser = argparse.ArgumentParser(description=__doc__)
parser.add_argument('--source-dir', required=True)
SOURCE = Path(parser.parse_args().source_dir)
MASTERS = Path('C:/improov-media-masters')
REPORT = ROOT / 'cache/services-media/inventory.json'
def module(name, filename):
    spec = importlib.util.spec_from_file_location(name, ROOT / 'deploy' / filename)
    value = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(value)
    return value
v = module('videos', 'process-videos.py')
g = module('images', 'generate-media.py')
ffprobe = v.command_path(None, 'ffprobe')
files = sorted((p for p in SOURCE.rglob('*') if p.is_file()), key=lambda p:p.as_posix().casefold())
by_size = defaultdict(list)
sizes = {p.stat().st_size for p in files}
for p in MASTERS.rglob('*'):
    if p.is_file() and p.suffix.lower() in v.VIDEO_EXTENSIONS | {'.jpg','.jpeg','.png','.webp','.avif'}:
        size = p.stat().st_size
        if size in sizes: by_size[size].append(p)
hashes = {}
def digest(p):
    key = str(p)
    if key not in hashes: hashes[key] = v.sha256(p)
    return hashes[key]
image_reuse = {}
media_map = json.loads((ROOT/'data/media-map.json').read_text(encoding='utf8'))
for logical, media in media_map.items():
    try:
        p = g.resolve_master(logical)
        if p.stat().st_size in sizes and all((ROOT / f).is_file() for formats in media['sources'].values() for f in formats.values()):
            image_reuse.setdefault(digest(p), logical)
    except (FileNotFoundError,KeyError): pass
video_reuse = {}
manifest = json.loads((ROOT/'data/video-manifest.json').read_text(encoding='utf8'))
for key, project in manifest['projects'].items():
    for video in project.get('videos',[]):
        sources = video.get('sources',{})
        if sources and (ROOT/video['poster']).is_file() and all((ROOT/s['src']).is_file() for s in sources.values()):
            video_reuse.setdefault(video.get('sourceSha256'), {'manifest':key,'id':video['id']})
report = {'sourceRoot':str(SOURCE),'mastersRoot':str(MASTERS),'files':[]}
for i,p in enumerate(files,1):
    rel=p.relative_to(SOURCE).as_posix()
    category = {'Still 3D':'images','Plantas Humanizadas':'plans','Animações 3D':'animations','Filmes conceito de lançamento':'concept','Filmes Teaser':'teasers','Filmes documentais':'documentaries','Filmes Institucionais':'institutional'}[p.relative_to(SOURCE).parts[0]]
    is_video=p.suffix.lower() in v.VIDEO_EXTENSIONS
    if not is_video and category=='animations':category='images'
    if is_video:
        metadata=v.probe(p,ffprobe,'animacoes' if category=='animations' else 'filmes',SOURCE)
        sha=metadata['sha256']
    else:
        sha=digest(p)
        with Image.open(p) as im:
            oriented=ImageOps.exif_transpose(im)
            metadata={'width':oriented.width,'height':oriented.height}
    master=next((candidate for candidate in by_size[p.stat().st_size] if digest(candidate)==sha),None)
    item={'source':rel,'bytes':p.stat().st_size,'sha256':sha,'category':category,'kind':'video' if is_video else 'image','master':str(master) if master else None,'metadata':metadata}
    if sha in video_reuse and is_video:item['video']=video_reuse[sha]
    if sha in image_reuse and not is_video:item['image']=image_reuse[sha]
    report['files'].append(item)
    REPORT.parent.mkdir(parents=True,exist_ok=True)
    REPORT.write_text(json.dumps(report,ensure_ascii=False,indent=2)+'\n',encoding='utf8')
    print(f'[{i}/{len(files)}] {rel} | {"reused" if item.get("video") or item.get("image") else "new"}',flush=True)
print('Inventory complete',flush=True)
