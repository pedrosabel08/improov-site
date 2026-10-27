"""Import audited service masters and prepare the shared media pipelines."""
import argparse
import hashlib
import importlib.util
import json
import shutil
from datetime import datetime, timezone
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
REPORT = ROOT / 'cache/services-media/inventory.json'
MASTERS = Path('C:/improov-media-masters')
DESTINATION = MASTERS / 'servicos'
SLUG = 'nossos-servicos'
spec=importlib.util.spec_from_file_location('videos',ROOT/'deploy/process-videos.py')
v=importlib.util.module_from_spec(spec);spec.loader.exec_module(v)

def write(path, data):
    path.parent.mkdir(parents=True,exist_ok=True)
    path.write_text(json.dumps(data,ensure_ascii=False,indent=2)+'\n',encoding='utf8')

def film_title(category, name):
    rules = {
        'concept': [('AYA_KAR','Kaario'),('ARS VIE','ARS Vieiras'),('GES_CHA','Gessele'),('PhacZ','Phacz Vibra'),('RDO_VAL','RDO Valence'),('HSA MON','HSantos')],
        'teasers': [('Triumph','Teaser do Senna Tower'),('OMNI','Teaser Leo Maia OMNI'),('Domini','Teaser do Alma Morro das pedras da Domini'),('N1','N1 Felipe Tito'),('GES_CHA','Gessele Charles'),('PhacZ','Phacz Vibra'),('EDIFY','Edify')],
        'documentaries': [('AYA_KAR','Kaario'),('ARS KAM','ARS Vieiras'),('GESSELE KIRCK','Gessele'),('LD10','LD Reserva'),('PhacZ','Phacz Vibra'),('GES_CHA','Gessele Charles')],
        'institutional': [('TALLS','Talls Solutions'),('ALPES','Alpes Catarina'),('N1','N1 Empreendimentos')],
    }
    for index,(match,title) in enumerate(rules.get(category,[])):
        if match.casefold() in name.casefold():
            if category=='teasers' and 'PhacZ'.casefold() in name.casefold():
                title += ' — Teaser 2' if 'TEASER2' in name.upper() else ' — Teaser 1'
            if category=='teasers' and 'EDIFY' in name.upper():
                title += ' — ' + ('Carro' if 'carro' in name else 'Helicóptero' if 'heli' in name else 'Iate')
            return title,index
    raise ValueError(f'Filme sem identificação editorial: {category}/{name}')

def project_title(name):
    lookup=[('ARS_VIE','Vieiras'),('AYA_KAR','Kaario'),('AYA_CAS','AYA Casas'),('AYA_EST','AYA Casas'),('RDO_VAL','Valence Residence'),('HSA_MON','Monticello Residenziale'),('HAS_MON','Monticello Residenziale'),('GES_CHA','Charles II by OKEAN'),('GT_LAC','La Città by Pininfarina'),('LD1_RES','LD Reserva'),('PHA_NET','Phacz Netuno'),('FG_TRI','Senna Tower'),('EDI_ONE','Edify One'),('FG_Talls','Talls Solutions')]
    return next((label for code,label in lookup if code.casefold() in name.casefold()),'')

def main():
    parser=argparse.ArgumentParser();parser.add_argument('--verify',action='store_true');args=parser.parse_args()
    report=json.loads(REPORT.read_text(encoding='utf8'));source=Path(report['sourceRoot'])
    received=list(p for p in source.rglob('*') if p.is_file())
    if len(report['files'])!=len(received):raise ValueError('Inventário ainda incompleto')
    if args.verify:
        for i,item in enumerate(report['files'],1):
            for file in (source/item['source'],Path(item['master'])):
                if v.sha256(file)!=item['sha256']:raise ValueError(f'Hash divergente: {file}')
            print(f'[verified {i}/{len(received)}] {item["source"]}',flush=True)
        return
    manifest=json.loads((ROOT/'deploy/media-manifest.json').read_text(encoding='utf8'))
    previous_media=json.loads((ROOT/'data/services-media.json').read_text(encoding='utf8')) if (ROOT/'data/services-media.json').exists() else {'groups':{}}
    previous_items={item['sourceSha256']:item for items in previous_media['groups'].values() for item in items}
    labels=json.loads((ROOT/'data/services-labels.json').read_text(encoding='utf8')).get('items',{}) if (ROOT/'data/services-labels.json').exists() else {}
    collection={'slug':SLUG,'mediaVersion':1,'media':[]}
    curation=json.loads((ROOT/'data/video-curation.json').read_text(encoding='utf8'))
    existing_videos=json.loads((ROOT/'data/video-manifest.json').read_text(encoding='utf8'))['projects'].get(SLUG,{}).get('videos',[])
    rule={'defaults':{'publish':False,'poster':False,'variants':[]},'sources':{}}
    groups={key:[] for key in ['images','plans','animations','concept','teasers','documentaries','institutional']}
    seen={}
    for index,item in enumerate(report['files'],1):
        original=source/item['source']; category=item['category']; kind=item['kind']
        filename=v.slugify(original.stem)[:110]+'-'+item['sha256'][:8]+original.suffix.lower()
        folder='animacoes' if category=='animations' else 'filmes/'+category if kind=='video' else 'plantas' if category=='plans' else 'imagens'
        master=Path(item['master']) if item.get('master') else DESTINATION/folder/filename
        if kind=='video' and not item.get('video'):master=DESTINATION/folder/filename
        if not master.resolve().is_relative_to(MASTERS.resolve()):raise ValueError(f'Destino fora da raiz: {master}')
        if not master.exists():
            master.parent.mkdir(parents=True,exist_ok=True);shutil.copy2(original,master)
        if master.stat().st_size!=item['bytes'] or v.sha256(master)!=item['sha256']:raise ValueError(f'Falha de integridade: {master}')
        item['master']=str(master)
        entry={'sourceSha256':item['sha256']}
        duplicate=seen.get((category,item['sha256']))
        if duplicate:
            item['duplicateOf']=duplicate;continue
        seen[(category,item['sha256'])]=item['source']
        if kind=='image':
            previous=previous_items.get(item['sha256'],{})
            preparation=previous.get('preparation')
            logical=previous['src'] if preparation else item.get('image') or 'assets/servicos/'+folder+'/'+filename
            entry.update({'src':logical,'width':item['metadata']['width'],'height':item['metadata']['height'],'project':project_title(original.name)})
            if preparation:
                entry.update({'preparation':preparation,'width':previous['width'],'height':previous['height']})
            if logical.startswith('assets/servicos/'):
                image_master=preparation['masterSource'] if preparation else master.relative_to(MASTERS).as_posix()
                collection['media'].append({'name':Path(logical).stem,'source':image_master,'publicSource':logical})
        else:
            entry.update(item.get('video') or {'manifest':SLUG,'id':v.output_id(master)})
            if not item.get('video'):
                rule['sources'][master.relative_to(DESTINATION).as_posix()]={'publish':True,'poster':True,'variants':['1080']}
            elif item['video']['manifest']==SLUG:
                previous=next(video for video in existing_videos if video['id']==item['video']['id'])
                rule['sources'][previous['source']]={'publish':True,'poster':True,'variants':['1080']}
            if category=='animations':entry['project']=project_title(original.name)
            else:entry['title'],entry['order']=film_title(category,original.name)
        label=labels.get(item['sha256'],{})
        if label.get('project') or label.get('showCaption') is False:
            entry.update({'project':label['project'],'name':label['name']})
        if label.get('showCaption') is False:entry['showCaption']=False
        if label.get('caption'):entry.update({'caption':label['caption'],'project':label.get('project',''),'name':label['name']})
        if label.get('visible',True):groups[category].append(entry)
        print(f'[imported {index}/{len(received)}] {item["source"]}',flush=True)
    for category in ['concept','teasers','documentaries','institutional']:groups[category].sort(key=lambda x:x['order'])
    manifest.setdefault('collections',{})[SLUG]=collection
    curation.setdefault('projects',{})[SLUG]=rule
    write(ROOT/'deploy/media-manifest.json',manifest);write(ROOT/'data/video-curation.json',curation)
    write(ROOT/'data/services-media.json',{'version':1,'groups':groups})
    report['preparedAt']=datetime.now(timezone.utc).isoformat()
    write(REPORT,report);write(DESTINATION/'master-index.json',report)
    print(f'Imported {len(received)} sources; collection images: {len(collection["media"])}; collection videos: {len(rule["sources"])}',flush=True)

if __name__=='__main__':main()
