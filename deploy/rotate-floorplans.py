"""Create landscape floorplan copies without changing the source files."""
from __future__ import annotations

import argparse
import hashlib
import json
from pathlib import Path

from PIL import Image, ImageOps


def sha256(path: Path) -> str:
    with path.open('rb') as stream:
        return hashlib.file_digest(stream, 'sha256').hexdigest()


def rotate_floorplan(source: Path, destination: Path) -> dict:
    if source.resolve() == destination.resolve():
        raise ValueError('A saída não pode substituir o original.')
    if destination.exists():
        raise FileExistsError(f'A saída já existe: {destination}')
    original_hash = sha256(source)
    with Image.open(source) as original:
        # Orientation=8 already represents a 270° clockwise rotation.
        # Materialize EXIF first; only rotate a remaining portrait image.
        image = ImageOps.exif_transpose(original).convert('RGB')
        if image.width < image.height:
            image = image.transpose(Image.Transpose.ROTATE_90)
        destination.parent.mkdir(parents=True, exist_ok=True)
        # Remove EXIF so browsers and the media pipeline cannot rotate again.
        if destination.suffix.lower() == '.png':
            image.save(destination, 'PNG')
        elif destination.suffix.lower() in {'.jpg', '.jpeg'}:
            image.save(destination, 'JPEG', quality=100, subsampling=0)
        else:
            raise ValueError('Use .jpg, .jpeg ou .png para a saída.')
        dimensions = image.size
    if sha256(source) != original_hash:
        raise RuntimeError(f'O hash do original mudou: {source}')
    return {
        'source': str(source.resolve()),
        'destination': str(destination.resolve()),
        'sourceSha256': original_hash,
        'outputSha256': sha256(destination),
        'width': dimensions[0],
        'height': dimensions[1],
    }


def main() -> None:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--input', type=Path, required=True)
    parser.add_argument('--output', type=Path, required=True)
    args = parser.parse_args()
    source_root = args.input.resolve(strict=True)
    output_root = args.output.resolve()
    if not source_root.is_dir():
        parser.error('A entrada deve ser uma pasta.')
    if source_root == output_root or output_root.is_relative_to(source_root):
        parser.error('Use uma pasta de saída separada da entrada.')
    sources = sorted(p for p in source_root.iterdir() if p.is_file() and p.suffix.lower() in {'.jpg', '.jpeg', '.png'})
    if not sources:
        parser.error('Nenhuma imagem JPG ou PNG encontrada na pasta.')
    targets = [output_root / p.name for p in sources]
    if any(p.exists() for p in targets):
        parser.error('Já existem arquivos de saída. Escolha uma pasta nova; nenhuma imagem foi convertida.')
    records = []
    for source, target in zip(sources, targets):
        record = rotate_floorplan(source, target)
        records.append(record)
        print(f"{source.name}: {record['width']} x {record['height']}")
    (output_root / 'rotation-index.json').write_text(json.dumps(records, ensure_ascii=False, indent=2) + '\n', encoding='utf8')
    print(f'{len(records)} cópias horizontais criadas. Originais preservados.')


if __name__ == '__main__':
    main()
