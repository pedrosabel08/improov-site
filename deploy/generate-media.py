from __future__ import annotations

import argparse
import json
import re
from pathlib import Path

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
        path
        for path in root.rglob("*")
        if path.is_file()
        and path.suffix.lower()
        in {
            ".jpg",
            ".jpeg",
            ".png",
            ".webp",
            ".avif",
            ".gif",
        }
    ]


ALL_MASTERS = image_files(MASTERS)


def resolve_master(source: str) -> Path:
    source_path = Path(source.replace("/", "\\"))
    filename = source_path.name

    if len(source_path.parts) >= 3 and source_path.parts[:2] == ("assets", "projetos"):
        slug = source_path.parts[2].lower()

        project_roots = [
            path
            for path in (MASTERS / "projetos").iterdir()
            if path.is_dir() and normalize(path.name) == normalize(slug)
        ]

        project_root_texts = [
            str(path.resolve()).lower().rstrip("\\/") + "\\" for path in project_roots
        ]

        candidates = [
            path
            for path in ALL_MASTERS
            if any(
                str(path.resolve()).lower().startswith(root_text)
                for root_text in project_root_texts
            )
            and normalize(path.name) == normalize(filename)
        ]

    else:
        candidates = [
            path for path in ALL_MASTERS if normalize(path.name) == normalize(filename)
        ]

    if not candidates:
        raise FileNotFoundError(f"Master não encontrado para: {source}")

    candidates.sort(
        key=lambda path: (
            "original-assets" in path.parts,
            len(path.parts),
        )
    )

    return candidates[0]


def add_entry(
    mapping: dict,
    manifest_media: list,
    source: str,
    output_slug: str,
    name: str,
    master_source: str | None = None,
) -> None:
    master = resolve_master(master_source or source)

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

        entry["width"] = source_width
        entry["height"] = source_height

        out_dir = OUTPUT / output_slug / "v1"
        out_dir.mkdir(parents=True, exist_ok=True)

        generated_widths: set[int] = set()

        for width in WIDTHS:
            target_width = min(width, source_width)

            # Evita gerar o mesmo tamanho várias vezes caso
            # a imagem original seja menor que WIDTHS.
            if target_width in generated_widths:
                continue

            generated_widths.add(target_width)

            target_height = max(
                1,
                round(source_height * target_width / source_width),
            )

            if target_width != source_width:
                resized = image.resize(
                    (target_width, target_height),
                    Image.Resampling.LANCZOS,
                )
            else:
                resized = image

            entry["sources"][str(target_width)] = {}

            for fmt in FORMATS:
                suffix = "jpg" if fmt == "jpg" else fmt

                target = out_dir / f"{name}-{target_width}.{suffix}"

                if fmt == "avif":
                    resized.save(
                        target,
                        "AVIF",
                        quality=90,
                        speed=6,
                    )

                elif fmt == "webp":
                    resized.save(
                        target,
                        "WEBP",
                        quality=88,
                        method=6,
                    )

                else:
                    resized.save(
                        target,
                        "JPEG",
                        quality=90,
                        optimize=True,
                        progressive=True,
                    )

                entry["sources"][str(target_width)][fmt] = str(
                    target.relative_to(ROOT)
                ).replace("\\", "/")

    mapping[source] = entry

    master_relative = master.relative_to(MASTERS)

    if master_relative.parts and master_relative.parts[0] == "original-assets":
        master_relative = Path(*master_relative.parts[1:])

    manifest_media.append(
        {
            "name": name,
            "source": str(master_relative).replace("\\", "/"),
        }
    )


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(
        description=("Gera derivados AVIF, WebP e JPG " "das imagens do site.")
    )

    parser.add_argument(
        "--project",
        help=("Slug do projeto para geração parcial. " "Ex.: ars-vie"),
    )

    parser.add_argument(
        "--media",
        help=("Mídia específica para geração parcial. " "Ex.: hero ou gallery-30"),
    )

    return parser.parse_args()


def get_project_media_source(
    project: dict,
    media_name: str,
) -> str:
    if media_name == "hero":
        return project["media"]["hero"]["src"]

    match = re.fullmatch(
        r"gallery-(\d+)",
        media_name,
    )

    if not match:
        raise ValueError(
            f"Mídia inválida: {media_name}. " "Use 'hero' ou 'gallery-XX'."
        )

    gallery_number = int(match.group(1))
    gallery_index = gallery_number - 1

    gallery = project["media"].get(
        "gallery",
        [],
    )

    if gallery_index < 0 or gallery_index >= len(gallery):
        raise ValueError(f"{media_name} não existe no projeto " f"{project['slug']}.")

    return gallery[gallery_index]["src"]


def regenerate_single_media(
    projects: list[dict],
    project_slug: str,
    media_name: str,
) -> None:
    project = next(
        (item for item in projects if item["slug"] == project_slug),
        None,
    )

    if project is None:
        raise ValueError(f"Projeto não encontrado: {project_slug}")

    source = get_project_media_source(
        project,
        media_name,
    )

    if MAP_PATH.exists():
        mapping = json.loads(MAP_PATH.read_text(encoding="utf-8"))
    else:
        mapping = {}

    if not MANIFEST_PATH.exists():
        raise FileNotFoundError(
            "Manifesto existente não encontrado: " f"{MANIFEST_PATH}"
        )

    manifest = json.loads(MANIFEST_PATH.read_text(encoding="utf-8"))

    manifest_project = next(
        (
            item
            for item in manifest.get(
                "projects",
                [],
            )
            if item["slug"] == project_slug
        ),
        None,
    )

    if manifest_project is None:
        raise ValueError(
            f"Projeto {project_slug} não existe " "no media-manifest.json."
        )

    existing_manifest_entry = next(
        (
            item
            for item in manifest_project.get(
                "media",
                [],
            )
            if item["name"] == media_name
        ),
        None,
    )

    #
    # Remove do media-map uma entrada antiga caso o
    # source lógico da mídia tenha sido alterado.
    #
    if existing_manifest_entry:
        old_manifest_source = existing_manifest_entry.get("source")

        #
        # O "source" do manifest aponta para o master,
        # enquanto o mapping usa o source de projects.json.
        #
        # Portanto procuramos também a entrada cujo
        # base termine no mesmo media_name.
        #
        stale_mapping_keys = [
            key
            for key, value in mapping.items()
            if value.get("base") == (f"assets/media/" f"{project_slug}/v1/{media_name}")
            and key != source
        ]

        for key in stale_mapping_keys:
            mapping.pop(key, None)

    generated_media: list = []

    print()
    print("Regenerando mídia...")
    print(f"Projeto : {project_slug}")
    print(f"Mídia   : {media_name}")
    print(f"Source  : {source}")

    master = resolve_master(source)

    print(f"Master  : {master}")
    print()

    add_entry(
        mapping=mapping,
        manifest_media=generated_media,
        source=source,
        output_slug=project_slug,
        name=media_name,
    )

    generated_manifest_entry = generated_media[0]

    if existing_manifest_entry:
        existing_manifest_entry.clear()
        existing_manifest_entry.update(generated_manifest_entry)
    else:
        manifest_project.setdefault(
            "media",
            [],
        ).append(generated_manifest_entry)

    MAP_PATH.write_text(
        json.dumps(
            mapping,
            ensure_ascii=False,
            indent=2,
        )
        + "\n",
        encoding="utf-8",
    )

    MANIFEST_PATH.write_text(
        json.dumps(
            manifest,
            ensure_ascii=False,
            indent=2,
        )
        + "\n",
        encoding="utf-8",
    )

    print("Regeneração parcial concluída " "com sucesso.")
    print(f"Projeto : {project_slug}")
    print(f"Mídia   : {media_name}")
    print(f"Mapa    : {MAP_PATH}")
    print(f"Manifest: {MANIFEST_PATH}")


def generate_all(
    projects: list[dict],
) -> None:
    mapping: dict = {}

    manifest = {
        "version": 1,
        "widths": list(WIDTHS),
        "formats": list(FORMATS),
        "projects": [],
        "site": [],
    }

    #
    # Projetos
    #
    for project in projects:
        slug = project["slug"]
        media = []

        hero = project["media"]["hero"]

        add_entry(
            mapping,
            media,
            hero["src"],
            slug,
            "hero",
        )

        for index, gallery in enumerate(
            project["media"].get(
                "gallery",
                [],
            ),
            start=1,
        ):
            add_entry(
                mapping,
                media,
                gallery["src"],
                slug,
                f"gallery-{index:02d}",
            )

        manifest["projects"].append(
            {
                "slug": slug,
                "mediaVersion": 1,
                "media": media,
            }
        )

    #
    # Mídias gerais do site
    #
    site_sources = {
        "home-hero": (
            "assets/projetos/AYA_KAR/" "6._AYA_KAR_Piscina_maior_EF_1_1.jpg",
            None,
        ),
        "careers-hero": (
            "assets/BHE_INF_Coworking_EF.jpg",
            None,
        ),
        "contact-hero": (
            "assets/BHE_INF_Fachada_Extra.jpg",
            None,
        ),
        "about-hero": (
            "assets/BHE_INF_Coworking_EF.jpg",
            None,
        ),
        "about-manifesto": (
            "assets/BHE_INF_Piscina_EF.jpg",
            None,
        ),
        "about-studio-wide": (
            "assets/BHE_INF_Coworking_EF.jpg",
            None,
        ),
        "about-studio-living": (
            "assets/BHE_INF_Living_Diferenciado_EF.jpg",
            None,
        ),
        "about-studio-adega": (
            "assets/BHE_INF_Adega_EF.jpg",
            None,
        ),
        "about-studio-fireplace": (
            "assets/BHE_INF_Fireplace_EF.jpg",
            None,
        ),
        "site/closing-cta": (
            "1.GT_LAC_Fotomontagem_aerea_com_"
            "insercao_do_empreendimento_em_"
            "terreno_real_angulo_1_EF.jpg",
            "1.GT_LAC_Fotomontagem_aerea_com_"
            "insercao_do_empreendimento_em_"
            "terreno_real_angulo_1_EF.jpg",
        ),
        "site/about-studio-01": (
            "DSCF0764.JPG",
            "DSCF0764.JPG",
        ),
        "site/about-studio-02": (
            "DSCF5349.JPG",
            "DSCF5349.JPG",
        ),
        "site/about-studio-03": (
            "DSCF5360.JPG",
            "DSCF5360.JPG",
        ),
        "site/about-studio-04": (
            "DSCF5369.JPG",
            "DSCF5369.JPG",
        ),
    }

    for name, (
        source,
        master_source,
    ) in site_sources.items():
        media = []

        add_entry(
            mapping,
            media,
            source,
            "site",
            name.replace(
                "site/",
                "",
            ),
            master_source,
        )

        manifest["site"].extend(media)

    #
    # Salva manifests
    #
    MAP_PATH.write_text(
        json.dumps(
            mapping,
            ensure_ascii=False,
            indent=2,
        )
        + "\n",
        encoding="utf-8",
    )

    MANIFEST_PATH.write_text(
        json.dumps(
            manifest,
            ensure_ascii=False,
            indent=2,
        )
        + "\n",
        encoding="utf-8",
    )

    print()
    print(f"Derivados gerados: " f"{len(mapping)} imagens-fonte")
    print(f"Mapa: {MAP_PATH}")
    print(f"Manifesto: {MANIFEST_PATH}")


def main() -> int:
    args = parse_args()

    projects_data = json.loads(PROJECTS_PATH.read_text(encoding="utf-8"))

    projects = projects_data["projects"]

    #
    # Modo parcial
    #
    if args.project or args.media:
        if not args.project or not args.media:
            raise ValueError(
                "Para regeneração parcial, " "informe --project e --media juntos."
            )

        regenerate_single_media(
            projects=projects,
            project_slug=args.project,
            media_name=args.media,
        )

        return 0

    #
    # Modo completo
    #
    generate_all(projects)

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
