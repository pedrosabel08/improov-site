"""Validate every services-page media reference against its actual public files."""
import argparse
import importlib.util
import json
from pathlib import Path
from PIL import Image

parser=argparse.ArgumentParser(description=__doc__)
parser.add_argument('--kind', choices=['all','images','videos'], default='all')
args=parser.parse_args()
ROOT=Path(__file__).resolve().parents[1]
spec=importlib.util.spec_from_file_location('videos',ROOT/'deploy/process-videos.py')
v=importlib.util.module_from_spec(spec);spec.loader.exec_module(v)
probe=v.command_path(None,'ffprobe')
groups=json.loads((ROOT/'data/services-media.json').read_text(encoding='utf8'))['groups']
images=json.loads((ROOT/'data/media-map.json').read_text(encoding='utf8'))
videos=json.loads((ROOT/'data/video-manifest.json').read_text(encoding='utf8'))['projects']
files=set();errors=[];counts={}
for category,items in groups.items():
    items=[item for item in items if args.kind=='all' or ('src' in item)==(args.kind=='images')]
    if not items:continue
    counts[category]=len(items)
    for item in items:
        try:
            if 'src' in item:
                media=images[item['src']]
                if media.get('width')!=item['width'] or media.get('height')!=item['height']:
                    raise ValueError('Image dimensions do not match inventory')
                for width,formats in media['sources'].items():
                    for fmt,filename in formats.items():
                        file=ROOT/filename
                        with Image.open(file) as im:
                            if im.width!=int(width) or im.width>item['width']:raise ValueError('Wrong image width')
                            if abs(im.height/im.width-item['height']/item['width'])>0.003:raise ValueError('Wrong image aspect ratio')
                            im.verify()
                        files.add(filename)
            else:
                video=next(x for x in videos[item['manifest']]['videos'] if x['id']==item['id'])
                if video['sourceSha256']!=item['sourceSha256']:raise ValueError('Video source hash does not match curation')
                v.validate_poster(ROOT/video['poster'],probe);files.add(video['poster'])
                for source in video['sources'].values():
                    result=v.validate_output(ROOT/source['src'],probe,source['width'],source['height'],video['hasAudio'])
                    if not result['faststart']:raise ValueError('Video is not faststart')
                    if abs(result['duration']-video['duration'])>0.5:raise ValueError('Video duration changed')
                    files.add(source['src'])
        except Exception as exc:errors.append({'category':category,'item':item,'error':str(exc)})
    print(f'[validated] {category}: {len(items)}',flush=True)
report={'counts':counts,'items':sum(counts.values()),'publicFiles':len(files),'publicBytes':sum((ROOT/f).stat().st_size for f in files),'errors':errors}
output=ROOT/f'cache/services-media/validation-{args.kind}.json';output.write_text(json.dumps(report,ensure_ascii=False,indent=2)+'\n',encoding='utf8')
print(json.dumps(report,ensure_ascii=False,indent=2),flush=True)
raise SystemExit(1 if errors else 0)
