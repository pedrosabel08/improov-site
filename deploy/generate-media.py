from __future__ import annotations

import json
import re
import shutil
import sys
from pathlib import Path
from typing import Iterable

from PIL import Image, ImageOps

ROOT = Path(__file__).resolve().parents[1]
MASTERS = Path(r"C:\improov-media-masters")
OUTPUT = ROOT / "assets" / "media"
PROJECTS_PATH = ROOT / "data" / "projects.json"
MAP_PATH = ROOT / "data" / "media-map.json"
MANIFEST_PATH = ROOT / "deploy" / "media-manifest.json"
WIDTHS = (640, 1024, 1440, 1920)
FORMATS = ("avif", "webp", "jpg")


def normalize(value: str) -> str:
    return re.sub(r"[^a-z0-9]+", "", value.lower())


def image_files(root: Path) -> list[Path]:
    return [
        p
        for p in root.rglob("*")
        if p.is_file()
        and p.suffix.lower() in {".jpg", ".jpeg", ".png", ".webp", ".avif", ".gif"}
    ]


ALL_MASTERS = image_files(MASTERS)


def resolve_master(source: str) -> Path:
    source_path = Path(source.replace("/", "\\"))
    filename = source_path.name
    if source_path.parts[:2] == ("assets", "projetos"):
        slug = source_path.parts[2].lower()
        candidates = [
            p
            for p in ALL_MASTERS
            if p.parent.name.lower() == slug
            and normalize(p.name) == normalize(filename)
        ]
    else:
        candidates = [
            p for p in ALL_MASTERS if normalize(p.name) == normalize(filename)
        ]
    if not candidates:
        raise FileNotFoundError(f"Master não encontrado para {source}")
    candidates.sort(key=lambda path: ("original-assets" in path.parts, len(path.parts)))
    return candidates[0]


def add_entry(
    mapping: dict, manifest_media: list, source: str, output_slug: str, name: str
) -> None:
    master = resolve_master(source)
    relative_base = f"assets/media/{output_slug}/v1/{name}"
    entry = {
        "source": source,
        "base": relative_base,
        "width": None,
        "height": None,
        "sources": {},
    }
    with Image.open(master) as original:
        image = ImageOps.exif_transpose(original).convert("RGB")
        source_width, source_height = image.size
        entry["width"], entry["height"] = source_width, source_height
        for width in WIDTHS:
            target_width = min(width, source_width)
            target_height = max(1, round(source_height * target_width / source_width))
            resized = (
                image.resize((target_width, target_height), Image.Resampling.LANCZOS)
                if target_width != source_width
                else image
            )
            entry["sources"][str(target_width)] = {}
            out_dir = OUTPUT / output_slug / "v1"
            out_dir.mkdir(parents=True, exist_ok=True)
            for fmt in FORMATS:
                suffix = "jpg" if fmt == "jpg" else fmt
                target = out_dir / f"{name}-{target_width}.{suffix}"
                if fmt == "avif":
                    resized.save(target, "AVIF", quality=90, speed=6)
                elif fmt == "webp":
                    resized.save(target, "WEBP", quality=88, method=6)
                else:
                    resized.save(
                        target, "JPEG", quality=90, optimize=True, progressive=True
                    )
                entry["sources"][str(target_width)][fmt] = str(
                    target.relative_to(ROOT)
                ).replace("\\", "/")
        mapping[source] = entry
        master_relative = master.relative_to(MASTERS)
        if master_relative.parts and master_relative.parts[0] == "original-assets":
            master_relative = Path(*master_relative.parts[1:])
        manifest_media.append(
            {"name": name, "source": str(master_relative).replace("\\", "/")}
        )


def main() -> int:
    projects = json.loads(PROJECTS_PATH.read_text(encoding="utf-8"))["projects"]
    mapping: dict = {}
    manifest = {
        "version": 1,
        "widths": list(WIDTHS),
        "formats": list(FORMATS),
        "projects": [],
        "site": [],
    }
    for project in projects:
        slug = project["slug"]
        media = []
        hero = project["media"]["hero"]
        add_entry(mapping, media, hero["src"], slug, "hero")
        for index, gallery in enumerate(project["media"].get("gallery", []), start=1):
            add_entry(mapping, media, gallery["src"], slug, f"gallery-{index:02d}")
        manifest["projects"].append({"slug": slug, "mediaVersion": 1, "media": media})

    site_sources = {
        "home-hero": "assets/projetos/AYA_KAR/6._AYA_KAR_Piscina_maior_EF_1_1.jpg",
        "careers-hero": "assets/BHE_INF_Coworking_EF.jpg",
        "contact-hero": "assets/BHE_INF_Fachada_Extra.jpg",
        "about-hero": "assets/BHE_INF_Coworking_EF.jpg",
        "about-manifesto": "assets/BHE_INF_Piscina_EF.jpg",
        "about-studio-wide": "assets/BHE_INF_Coworking_EF.jpg",
        "about-studio-living": "assets/BHE_INF_Living_Diferenciado_EF.jpg",
        "about-studio-adega": "assets/BHE_INF_Adega_EF.jpg",
        "about-studio-fireplace": "assets/BHE_INF_Fireplace_EF.jpg",
    }
    for name, source in site_sources.items():
        media = []
        add_entry(mapping, media, source, "site", name)
        manifest["site"].extend(media)

    MAP_PATH.write_text(
        json.dumps(mapping, ensure_ascii=False, indent=2) + "\n", encoding="utf-8"
    )
    MANIFEST_PATH.write_text(
        json.dumps(manifest, ensure_ascii=False, indent=2) + "\n", encoding="utf-8"
    )
    print(f"Derivados gerados: {len(mapping)} imagens-fonte")
    print(f"Mapa: {MAP_PATH}")
    print(f"Manifesto: {MANIFEST_PATH}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
