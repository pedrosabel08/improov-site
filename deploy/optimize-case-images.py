"""Reduce oversized AVIF derivatives; originals and JPEG fallbacks stay intact."""
from __future__ import annotations

import argparse
import io
import json
import shutil
from datetime import datetime
from pathlib import Path

from PIL import Image

ROOT = Path(__file__).resolve().parents[1]
LIMITS = {640: 100_000, 1024: 200_000, 1440: 350_000}


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument('--project', required=True)
    parser.add_argument('--apply', action='store_true')
    args = parser.parse_args()
    if not args.project or any(c not in 'abcdefghijklmnopqrstuvwxyz0123456789-' for c in args.project):
        parser.error('Invalid project slug')
    mapping = json.loads((ROOT / 'data/media-map.json').read_text(encoding='utf-8'))
    backup_root = ROOT / 'cache/media-backups' / datetime.now().strftime('%Y%m%d-%H%M%S')
    rows = []
    for entry in mapping.values():
        for width, limit in LIMITS.items():
            sources = entry.get('sources', {}).get(str(width), {})
            avif = sources.get('avif', '')
            if not avif.startswith(f'assets/media/{args.project}/'):
                continue
            target = (ROOT / avif).resolve()
            fallback = (ROOT / sources.get('jpg', '')).resolve()
            if not target.is_relative_to(ROOT / 'assets/media') or not fallback.is_relative_to(ROOT / 'assets/media'):
                raise ValueError('Image path outside media directory')
            if not target.is_file() or not fallback.is_file() or target.stat().st_size <= limit:
                continue
            before = target.stat().st_size
            with Image.open(fallback) as image:
                output = io.BytesIO()
                image.convert('RGB').save(output, 'AVIF', quality=65, speed=6)
            optimized = output.getvalue()
            if len(optimized) >= before:
                continue
            if args.apply:
                backup = backup_root / target.relative_to(ROOT)
                backup.parent.mkdir(parents=True, exist_ok=True)
                shutil.copy2(target, backup)
                target.write_bytes(optimized)
            rows.append({'path': avif, 'before': before, 'after': len(optimized)})
    report = {'project': args.project, 'applied': args.apply, 'files': rows, 'before': sum(r['before'] for r in rows), 'after': sum(r['after'] for r in rows)}
    if args.apply:
        backup_root.mkdir(parents=True, exist_ok=True)
        (backup_root / 'report.json').write_text(json.dumps(report, indent=2) + '\n', encoding='utf-8')
    print(json.dumps(report, indent=2))


if __name__ == '__main__':
    main()
