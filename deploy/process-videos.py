#!/usr/bin/env python3
"""Process project video masters into web-ready H.264 MP4 assets.

The masters stay outside the repository.  The script is intentionally
project-agnostic: ``python deploy/process-videos.py <slug>`` discovers the
project under C:\\improov-media-masters\\projetos and updates one shared
manifest without touching other projects.
"""

from __future__ import annotations

import argparse
import hashlib
import json
import math
import os
import re
import shutil
import subprocess
import sys
import tempfile
import unicodedata
from datetime import datetime, timezone
from pathlib import Path
from typing import Any


ROOT = Path(__file__).resolve().parents[1]
DEFAULT_MASTERS_ROOT = Path(r"C:\improov-media-masters\projetos")
DEFAULT_OUTPUT_ROOT = ROOT / "assets" / "media"
MANIFEST_PATH = ROOT / "data" / "video-manifest.json"
CURATION_PATH = ROOT / "data" / "video-curation.json"
VIDEO_EXTENSIONS = {".mp4", ".mov", ".m4v", ".mkv", ".avi", ".webm", ".mts"}
CATEGORIES = {"animacoes", "filmes", "pilulas"}
PIPELINE_VERSION = 1
CURATION_VERSION = 1


def slugify(value: str) -> str:
    value = unicodedata.normalize("NFKD", value).encode("ascii", "ignore").decode("ascii")
    value = re.sub(r"[^a-zA-Z0-9]+", "-", value).strip("-").lower()
    return value or "video"


def category_for(path: Path, project_root: Path) -> str:
    relative = path.relative_to(project_root)
    top = slugify(relative.parts[0]) if relative.parts else "outros"
    return top if top in CATEGORIES else "outros"


def command_path(explicit: str | None, name: str) -> str:
    if explicit:
        candidate = Path(explicit)
        if candidate.is_file():
            return str(candidate)
        raise FileNotFoundError(f"Executável não encontrado: {candidate}")
    environment_path = os.environ.get(f"{name.upper()}_PATH")
    if environment_path and Path(environment_path).is_file():
        return environment_path
    found = shutil.which(name)
    if found:
        return found
    winget_root = Path.home() / "AppData" / "Local" / "Microsoft" / "WinGet" / "Packages"
    if winget_root.is_dir():
        candidates = sorted(winget_root.glob(f"*FFmpeg*/*/bin/{name}.exe"))
        if candidates:
            return str(candidates[0])
    raise FileNotFoundError(
        f"{name} nao esta no PATH. Instale FFmpeg ou passe --{name} com o caminho completo."
    )


def run_json(command: list[str]) -> dict[str, Any]:
    result = subprocess.run(command, capture_output=True, text=True, encoding="utf-8", errors="replace")
    if result.returncode != 0:
        raise RuntimeError(result.stderr.strip() or "ffprobe falhou")
    return json.loads(result.stdout)


def parse_rate(value: str | None) -> float | None:
    if not value or value in {"0/0", "N/A"}:
        return None
    try:
        numerator, denominator = value.split("/", 1)
        return round(float(numerator) / float(denominator), 3) if float(denominator) else None
    except (ValueError, ZeroDivisionError):
        return None


def simplify_ratio(width: int, height: int) -> str:
    if not width or not height:
        return "unknown"
    divisor = math.gcd(width, height)
    return f"{width // divisor}:{height // divisor}"


def rotation_from_probe(stream: dict[str, Any]) -> int:
    tags = stream.get("tags") or {}
    for key in ("rotate", "ROTATE"):
        if key in tags:
            try:
                return int(float(tags[key])) % 360
            except (TypeError, ValueError):
                pass
    for side_data in stream.get("side_data_list") or []:
        if "rotation" in side_data:
            try:
                return int(float(side_data["rotation"])) % 360
            except (TypeError, ValueError):
                pass
    return 0


def sha256(path: Path) -> str:
    digest = hashlib.sha256()
    with path.open("rb") as handle:
        for chunk in iter(lambda: handle.read(1024 * 1024), b""):
            digest.update(chunk)
    return digest.hexdigest()


def probe(path: Path, ffprobe: str, category: str, project_root: Path) -> dict[str, Any]:
    command = [
        ffprobe,
        "-v",
        "error",
        "-show_entries",
        "format=duration,size,bit_rate,format_name,format_long_name:stream=index,codec_type,codec_name,codec_long_name,width,height,r_frame_rate,avg_frame_rate,bit_rate,pix_fmt,channels,channel_layout,sample_rate,disposition:stream_tags=rotate",
        "-of",
        "json",
        str(path),
    ]
    data = run_json(command)
    streams = data.get("streams") or []
    video = next((item for item in streams if item.get("codec_type") == "video"), {})
    audio = next((item for item in streams if item.get("codec_type") == "audio"), None)
    width = int(video.get("width") or 0)
    height = int(video.get("height") or 0)
    format_data = data.get("format") or {}
    duration_value = format_data.get("duration") or video.get("duration")
    duration = round(float(duration_value), 3) if duration_value else None
    relative = path.relative_to(project_root).as_posix()
    return {
        "source": relative,
        "name": path.name,
        "category": category,
        "extension": path.suffix.lower().lstrip("."),
        "bytes": path.stat().st_size,
        "sha256": sha256(path),
        "modifiedAt": datetime.fromtimestamp(path.stat().st_mtime, timezone.utc).isoformat(),
        "duration": duration,
        "width": width,
        "height": height,
        "aspectRatio": simplify_ratio(width, height),
        "orientation": "portrait" if height > width else "landscape" if width > height else "square",
        "fps": parse_rate(video.get("avg_frame_rate") or video.get("r_frame_rate")),
        "videoCodec": video.get("codec_name"),
        "videoCodecLong": video.get("codec_long_name"),
        "videoBitrate": int(video["bit_rate"]) if str(video.get("bit_rate", "")).isdigit() else None,
        "audioCodec": audio.get("codec_name") if audio else None,
        "audioCodecLong": audio.get("codec_long_name") if audio else None,
        "audioBitrate": int(audio["bit_rate"]) if audio and str(audio.get("bit_rate", "")).isdigit() else None,
        "audioSampleRate": int(audio["sample_rate"]) if audio and str(audio.get("sample_rate", "")).isdigit() else None,
        "audioChannels": int(audio["channels"]) if audio and str(audio.get("channels", "")).isdigit() else None,
        "audioLayout": audio.get("channel_layout") if audio else None,
        "hasAudio": audio is not None,
        "pixelFormat": video.get("pix_fmt"),
        "rotation": rotation_from_probe(video),
        "format": format_data.get("format_name"),
        "formatLong": format_data.get("format_long_name"),
        "totalBitrate": int(format_data["bit_rate"]) if str(format_data.get("bit_rate", "")).isdigit() else None,
    }


def profile_for(category: str) -> dict[str, Any]:
    if category == "pilulas":
        return {"crf": 23, "preset": "medium", "audioBitrate": "128k"}
    if category == "filmes":
        return {"crf": 21, "preset": "slow", "audioBitrate": "160k"}
    return {"crf": 19, "preset": "slow", "audioBitrate": "160k"}


def target_sizes(width: int, height: int) -> list[tuple[str, int, int]]:
    if not width or not height:
        return []
    long_side = max(width, height)
    targets: list[tuple[str, int]] = []
    if long_side >= 1920:
        targets.append(("1080", 1920))
    if long_side >= 1280:
        targets.append(("720", 1280))
    if not targets:
        even_width = width - width % 2
        even_height = height - height % 2
        return [("source", max(even_width, 2), max(even_height, 2))]

    result: list[tuple[str, int, int]] = []
    for label, target_long in targets:
        scale = min(1.0, target_long / long_side)
        target_width = max(2, int(round(width * scale)) // 2 * 2)
        target_height = max(2, int(round(height * scale)) // 2 * 2)
        result.append((label, target_width, target_height))
    return result


def output_id(path: Path) -> str:
    return slugify(path.stem)


def run_process(command: list[str]) -> None:
    result = subprocess.run(command, capture_output=True, text=True, encoding="utf-8", errors="replace")
    if result.returncode != 0:
        raise RuntimeError(result.stderr.strip() or "ffmpeg falhou")


def faststart(path: Path) -> bool:
    with path.open("rb") as handle:
        sample = handle.read(4 * 1024 * 1024)
    moov = sample.find(b"moov")
    mdat = sample.find(b"mdat")
    return moov >= 0 and (mdat < 0 or moov < mdat)


def validate_output(
    path: Path,
    ffprobe: str,
    expected_width: int,
    expected_height: int,
    expected_audio: bool | None = None,
) -> dict[str, Any]:
    data = run_json(
        [
            ffprobe,
            "-v",
            "error",
            "-show_entries",
            "stream=codec_type,codec_name,width,height:format=duration",
            "-of",
            "json",
            str(path),
        ]
    )
    video = next((item for item in data.get("streams", []) if item.get("codec_type") == "video"), {})
    audio = next((item for item in data.get("streams", []) if item.get("codec_type") == "audio"), None)
    has_audio = any(item.get("codec_type") == "audio" for item in data.get("streams", []))
    if video.get("codec_name") != "h264":
        raise RuntimeError(f"codec inesperado em {path.name}: {video.get('codec_name')}")
    if int(video.get("width") or 0) > expected_width or int(video.get("height") or 0) > expected_height:
        raise RuntimeError(f"resolução acima do esperado em {path.name}")
    if expected_audio is not None and has_audio != expected_audio:
        estado = "presente" if has_audio else "ausente"
        esperado = "presente" if expected_audio else "ausente"
        raise RuntimeError(f"áudio {estado} em {path.name}; esperado {esperado}")
    if has_audio and audio.get("codec_name") != "aac":
        raise RuntimeError(f"codec de áudio inesperado em {path.name}: {audio.get('codec_name')}")
    if path.stat().st_size <= 0:
        raise RuntimeError(f"arquivo vazio: {path.name}")
    return {
        "width": int(video.get("width") or 0),
        "height": int(video.get("height") or 0),
        "duration": round(float((data.get("format") or {}).get("duration") or 0), 3),
        "hasAudio": has_audio,
        "audioCodec": audio.get("codec_name") if audio else None,
        "faststart": faststart(path),
        "bytes": path.stat().st_size,
    }


def validate_poster(path: Path, ffprobe: str) -> dict[str, Any]:
    data = run_json(
        [
            ffprobe,
            "-v",
            "error",
            "-show_entries",
            "stream=codec_type,codec_name,width,height",
            "-of",
            "json",
            str(path),
        ]
    )
    stream = next((item for item in data.get("streams", []) if item.get("codec_type") == "video"), {})
    if stream.get("codec_name") != "webp":
        raise RuntimeError(f"poster não é WebP: {path.name}")
    if int(stream.get("width") or 0) <= 0 or int(stream.get("height") or 0) <= 0:
        raise RuntimeError(f"dimensões inválidas no poster: {path.name}")
    if path.stat().st_size <= 0:
        raise RuntimeError(f"poster vazio: {path.name}")
    return {
        "width": int(stream["width"]),
        "height": int(stream["height"]),
        "bytes": path.stat().st_size,
    }


def poster(path: Path, destination: Path, duration: float | None, ffmpeg: str) -> None:
    destination.parent.mkdir(parents=True, exist_ok=True)
    duration_value = duration or 1.0
    timestamp = min(max(0.1, duration_value * 0.35), max(0.0, duration_value - 0.05))
    filter_expression = "scale=w='if(gt(iw,ih),min(iw,1600),-2)':h='if(gt(iw,ih),-2,min(ih,1600))'"
    command = [
        ffmpeg,
        "-hide_banner",
        "-loglevel",
        "error",
        "-y",
        "-ss",
        f"{timestamp:.3f}",
        "-i",
        str(path),
        "-frames:v",
        "1",
        "-vf",
        filter_expression,
        "-an",
        "-c:v",
        "libwebp",
        "-quality",
        "82",
        "-compression_level",
        "6",
        str(destination),
    ]
    run_process(command)
    if destination.stat().st_size <= 0:
        raise RuntimeError(f"poster vazio: {destination.name}")


def encode(source: Path, destination: Path, width: int, height: int, profile: dict[str, Any], has_audio: bool, audio_bitrate: str, ffmpeg: str) -> None:
    destination.parent.mkdir(parents=True, exist_ok=True)
    with tempfile.NamedTemporaryFile(prefix=f".{destination.stem}-", suffix=".tmp.mp4", dir=destination.parent, delete=False) as handle:
        temporary = Path(handle.name)
    command = [
        ffmpeg,
        "-hide_banner",
        "-loglevel",
        "error",
        "-y",
        "-i",
        str(source),
        "-map",
        "0:v:0",
    ]
    if has_audio:
        command += ["-map", "0:a:0?"]
    command += [
        "-vf",
        f"scale={width}:{height}:flags=lanczos",
        "-c:v",
        "libx264",
        "-preset",
        profile["preset"],
        "-crf",
        str(profile["crf"]),
        "-pix_fmt",
        "yuv420p",
        "-movflags",
        "+faststart",
        "-metadata:s:v:0",
        "rotate=0",
    ]
    if has_audio:
        command += ["-c:a", "aac", "-b:a", audio_bitrate, "-ar", "48000"]
    else:
        command += ["-an"]
    command += [str(temporary)]
    try:
        run_process(command)
        temporary.replace(destination)
    finally:
        temporary.unlink(missing_ok=True)


def load_manifest() -> dict[str, Any]:
    if not MANIFEST_PATH.is_file():
        return {"version": 1, "projects": {}}
    try:
        data = json.loads(MANIFEST_PATH.read_text(encoding="utf-8"))
        return data if isinstance(data, dict) else {"version": 1, "projects": {}}
    except json.JSONDecodeError:
        return {"version": 1, "projects": {}}


def write_manifest(manifest: dict[str, Any]) -> None:
    MANIFEST_PATH.parent.mkdir(parents=True, exist_ok=True)
    temporary = MANIFEST_PATH.with_suffix(".tmp")
    temporary.write_text(json.dumps(manifest, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
    temporary.replace(MANIFEST_PATH)


def load_curation(path: Path) -> dict[str, Any]:
    if not path.is_file():
        return {"version": CURATION_VERSION, "projects": {}}
    try:
        data = json.loads(path.read_text(encoding="utf-8"))
        return data if isinstance(data, dict) else {"version": CURATION_VERSION, "projects": {}}
    except json.JSONDecodeError as error:
        raise RuntimeError(f"configuração de curadoria inválida: {path}: {error}") from error


def curation_for(curation: dict[str, Any], slug: str) -> dict[str, Any] | None:
    projects = curation.get("projects") or {}
    project = projects.get(slug)
    return project if isinstance(project, dict) else None


def publication_rule(item: dict[str, Any], project_curation: dict[str, Any] | None) -> dict[str, Any]:
    """Resolve uma regra exata de publicação sem alterar o inventário master."""
    if project_curation is None:
        return {"publish": True, "poster": True, "variants": "all"}

    defaults = project_curation.get("defaults") or {"publish": False, "poster": False, "variants": []}
    rule: dict[str, Any] = dict(defaults) if isinstance(defaults, dict) else {}
    category_defaults = project_curation.get("categoryDefaults") or {}
    category_rule = category_defaults.get(item["category"])
    if isinstance(category_rule, dict):
        rule.update(category_rule)
    source_rules = project_curation.get("sources") or {}
    source_rule = source_rules.get(item["source"])
    if isinstance(source_rule, dict):
        rule.update(source_rule)
    rule.setdefault("publish", False)
    rule.setdefault("poster", bool(rule["publish"]))
    rule.setdefault("variants", "all" if rule["publish"] else [])
    return rule


def selected_sizes(item: dict[str, Any], rule: dict[str, Any]) -> list[tuple[str, int, int]]:
    available = target_sizes(int(item["width"]), int(item["height"]))
    requested = rule.get("variants", "all")
    if requested == "all" or requested is None:
        return available
    if not isinstance(requested, list):
        raise ValueError(f"variants inválidas para {item['source']}: {requested!r}")
    by_label = {label: (label, width, height) for label, width, height in available}
    selected: list[tuple[str, int, int]] = []
    for label in requested:
        if label in by_label:
            selected.append(by_label[label])
        elif label == "1080" and "source" in by_label:
            selected.append(by_label["source"])
        else:
            raise ValueError(
                f"variante {label!r} indisponível para {item['source']} "
                f"(disponíveis: {', '.join(by_label) or 'nenhuma'})"
            )
    return selected


def publication_metadata(item: dict[str, Any], rule: dict[str, Any]) -> dict[str, Any]:
    sizes = selected_sizes(item, rule)
    return {
        "available": True,
        "published": bool(rule.get("publish")),
        "publication": "video" if rule.get("publish") else "poster-only" if rule.get("poster") else "inventory-only",
        "requestedVariants": rule.get("variants", []),
        "publicVariants": [label for label, _width, _height in sizes] if rule.get("publish") else [],
    }


def reindex_existing(
    inventory: list[dict[str, Any]],
    output_project: Path,
    ffprobe: str,
    project_curation: dict[str, Any] | None,
) -> tuple[list[dict[str, Any]], list[dict[str, Any]], list[dict[str, str]]]:
    profile_by_category = {category: profile_for(category) for category in CATEGORIES | {"outros"}}
    videos: list[dict[str, Any]] = []
    poster_only: list[dict[str, Any]] = []
    errors: list[dict[str, str]] = []
    for item in inventory:
        category = item["category"]
        video_id = output_id(Path(item["name"]))
        variants: dict[str, dict[str, Any]] = {}
        rule = publication_rule(item, project_curation)
        try:
            sizes = selected_sizes(item, rule)
            poster_path = output_project / "posters" / category / f"{video_id}-poster.webp"
            if rule.get("poster"):
                if not poster_path.is_file():
                    raise FileNotFoundError(poster_path)
                validate_poster(poster_path, ffprobe)
            if not rule.get("publish"):
                if rule.get("poster"):
                    poster_only.append({
                        "id": video_id,
                        "source": item["source"],
                        "sourceSha256": item["sha256"],
                        "category": category,
                        "poster": poster_path.relative_to(ROOT).as_posix(),
                        "duration": item["duration"],
                        "width": item["width"],
                        "height": item["height"],
                        "aspectRatio": item["aspectRatio"],
                        "orientation": item["orientation"],
                    })
                continue
            for label, width, height in sizes:
                destination = output_project / "videos" / category / f"{video_id}-{label}.mp4"
                if not destination.is_file():
                    raise FileNotFoundError(destination)
                validation = validate_output(destination, ffprobe, width, height, bool(item["hasAudio"]))
                variants[label] = {"src": destination.relative_to(ROOT).as_posix(), **validation}
            videos.append({
                "id": video_id,
                "source": item["source"],
                "sourceSha256": item["sha256"],
                "category": category,
                "poster": poster_path.relative_to(ROOT).as_posix(),
                "duration": item["duration"],
                "width": item["width"],
                "height": item["height"],
                "aspectRatio": item["aspectRatio"],
                "orientation": item["orientation"],
                "hasAudio": item["hasAudio"],
                "loopCandidate": category in {"pilulas", "animacoes"}
                and bool(item["duration"] is not None and item["duration"] <= 15),
                "audioCodec": item["audioCodec"],
                "videoCodec": item["videoCodec"],
                "profile": profile_by_category[category],
                "pipelineVersion": PIPELINE_VERSION,
                "sources": variants,
            })
        except Exception as error:
            errors.append({"source": item["source"], "error": str(error)})
    return videos, poster_only, errors


def main() -> int:
    parser = argparse.ArgumentParser(description="Processa videos master de um projeto para H.264 web.")
    parser.add_argument("slug", help="slug do projeto, por exemplo ars-vie")
    parser.add_argument("--masters-root", default=str(DEFAULT_MASTERS_ROOT), help="raiz de projetos master")
    parser.add_argument("--output-root", default=str(DEFAULT_OUTPUT_ROOT), help="raiz publica de assets")
    parser.add_argument("--curation", default=str(CURATION_PATH), help="configuração de publicação por projeto")
    parser.add_argument("--ffmpeg", default=None, help="caminho explícito para ffmpeg")
    parser.add_argument("--ffprobe", default=None, help="caminho explícito para ffprobe")
    parser.add_argument("--inventory-only", action="store_true", help="gera inventario sem transcodificar")
    parser.add_argument("--reindex-existing", action="store_true", help="reconstroi o manifest validando derivados ja gerados")
    parser.add_argument("--no-clean", action="store_true", help="nao remove derivados obsoletos do projeto")
    args = parser.parse_args()

    project_root = Path(args.masters_root) / args.slug
    if not project_root.is_dir():
        print(f"Projeto master não encontrado: {project_root}", file=sys.stderr)
        return 2
    try:
        ffprobe = command_path(args.ffprobe, "ffprobe")
        ffmpeg = None if args.inventory_only else command_path(args.ffmpeg, "ffmpeg")
        curation = load_curation(Path(args.curation))
    except FileNotFoundError as error:
        print(str(error), file=sys.stderr)
        return 2
    except RuntimeError as error:
        print(str(error), file=sys.stderr)
        return 2

    project_curation = curation_for(curation, args.slug)

    sources = sorted((path for path in project_root.rglob("*") if path.is_file() and path.suffix.lower() in VIDEO_EXTENSIONS), key=lambda item: item.as_posix().lower())
    print(f"[{args.slug}] Encontrados {len(sources)} videos")
    inventory: list[dict[str, Any]] = []
    errors: list[dict[str, str]] = []
    for source in sources:
        category = category_for(source, project_root)
        try:
            item = probe(source, ffprobe, category, project_root)
            inventory.append(item)
            print(f"[{category.upper()}] {source.name} | {item['width']}x{item['height']} | {item['duration'] or 0:.2f}s | audio={'sim' if item['hasAudio'] else 'nao'}")
        except Exception as error:  # keep one corrupt source from stopping the project
            errors.append({"source": source.relative_to(project_root).as_posix(), "error": str(error)})
            print(f"[ERRO] {source.name}: {error}", file=sys.stderr)

    for item in inventory:
        try:
            item.update(publication_metadata(item, publication_rule(item, project_curation)))
        except Exception as error:
            errors.append({"source": item["source"], "error": str(error)})

    if args.inventory_only:
        manifest = load_manifest()
        existing = (manifest.get("projects") or {}).get(args.slug) or {}
        project_data = dict(existing)
        project_data.update({"slug": args.slug, "generatedAt": datetime.now(timezone.utc).isoformat(), "pipelineVersion": PIPELINE_VERSION, "curation": project_curation, "inventory": inventory, "errors": errors})
        if args.reindex_existing:
            output_project = Path(args.output_root) / args.slug / "v1"
            videos, poster_only, reindex_errors = reindex_existing(inventory, output_project, ffprobe, project_curation)
            project_data.update({"publicRoot": output_project.relative_to(ROOT).as_posix(), "profiles": {category: profile_for(category) for category in CATEGORIES | {"outros"}}, "videos": videos, "posterOnly": poster_only, "curation": project_curation, "errors": errors + reindex_errors, "summary": {"found": len(sources), "inventoried": len(inventory), "processed": 0, "skipped": sum(len(video["sources"]) for video in videos), "failed": len(reindex_errors), "publishedVideos": len(videos), "publishedPosterOnly": len(poster_only), "publishedVariants": sum(len(video["sources"]) for video in videos), "posters": len(videos) + len(poster_only)}})
        manifest.setdefault("projects", {})[args.slug] = project_data
        write_manifest(manifest)
        return 1 if errors else 0

    output_project = Path(args.output_root) / args.slug / "v1"
    video_root = output_project / "videos"
    poster_root = output_project / "posters"
    manifest = load_manifest()
    previous = (manifest.get("projects") or {}).get(args.slug) or {}
    previous_by_source = {item.get("source"): item for item in previous.get("videos", [])}
    previous_poster_by_source = {item.get("source"): item for item in previous.get("posterOnly", [])}
    profile_by_category = {category: profile_for(category) for category in CATEGORIES | {"outros"}}
    generated_videos: list[dict[str, Any]] = []
    generated_poster_only: list[dict[str, Any]] = []
    expected_files: set[Path] = set()
    processed = skipped = failed = 0
    posters_processed = posters_skipped = 0

    for item in inventory:
        source = project_root / Path(item["source"])
        video_id = output_id(source)
        category = item["category"]
        profile = profile_by_category[category]
        variants: dict[str, dict[str, Any]] = {}
        rule = publication_rule(item, project_curation)
        previous_item = previous_by_source.get(item["source"])
        previous_poster_item = previous_poster_by_source.get(item["source"])
        previous_media = previous_item or previous_poster_item
        unchanged = bool(previous_media and previous_media.get("sourceSha256") == item["sha256"] and (not previous_item or previous_item.get("profile") == profile) and previous_media.get("pipelineVersion", PIPELINE_VERSION) == PIPELINE_VERSION)
        try:
            sizes = selected_sizes(item, rule)
            publish_video = bool(rule.get("publish"))
            poster_needed = publish_video or bool(rule.get("poster"))
            if publish_video:
                for label, width, height in sizes:
                    destination = video_root / category / f"{video_id}-{label}.mp4"
                    expected_files.add(destination)
                    if not (unchanged and destination.is_file()):
                        print(f"  -> {category}/{destination.name}")
                        encode(source, destination, width, height, profile, bool(item["hasAudio"]), str(profile["audioBitrate"]), ffmpeg)
                        processed += 1
                    else:
                        skipped += 1
                    variants[label] = {"src": destination.relative_to(ROOT).as_posix(), "width": width, "height": height, "bytes": destination.stat().st_size, "faststart": faststart(destination)}
                    validate_output(destination, ffprobe, width, height, bool(item["hasAudio"]))

            poster_path = poster_root / category / f"{video_id}-poster.webp"
            if poster_needed:
                expected_files.add(poster_path)
                if not (unchanged and poster_path.is_file()):
                    poster(source, poster_path, item["duration"], ffmpeg)
                    posters_processed += 1
                else:
                    posters_skipped += 1
                validate_poster(poster_path, ffprobe)
                item["publicPoster"] = poster_path.relative_to(ROOT).as_posix()
            else:
                item.pop("publicPoster", None)
            item["publicSources"] = {label: value["src"] for label, value in variants.items()}

            if publish_video:
                generated_videos.append({
                    "id": video_id,
                    "source": item["source"],
                    "sourceSha256": item["sha256"],
                    "category": category,
                    "poster": poster_path.relative_to(ROOT).as_posix(),
                    "duration": item["duration"],
                    "width": item["width"],
                    "height": item["height"],
                    "aspectRatio": item["aspectRatio"],
                    "orientation": item["orientation"],
                    "hasAudio": item["hasAudio"],
                    "loopCandidate": category in {"pilulas", "animacoes"}
                    and bool(item["duration"] is not None and item["duration"] <= 15),
                    "audioCodec": item["audioCodec"],
                    "videoCodec": item["videoCodec"],
                    "profile": profile,
                    "pipelineVersion": PIPELINE_VERSION,
                    "sources": variants,
                })
            elif poster_needed:
                generated_poster_only.append({
                    "id": video_id,
                    "source": item["source"],
                    "sourceSha256": item["sha256"],
                    "category": category,
                    "poster": poster_path.relative_to(ROOT).as_posix(),
                    "duration": item["duration"],
                    "width": item["width"],
                    "height": item["height"],
                    "aspectRatio": item["aspectRatio"],
                    "orientation": item["orientation"],
                    "pipelineVersion": PIPELINE_VERSION,
                })
        except Exception as error:
            failed += 1
            errors.append({"source": item["source"], "error": str(error)})
            print(f"[ERRO] processamento {item['name']}: {error}", file=sys.stderr)

    if not args.no_clean:
        for root in (video_root, poster_root):
            if not root.is_dir():
                continue
            for existing in root.rglob("*"):
                if existing.is_file() and existing not in expected_files:
                    existing.unlink()

    project_manifest = {
        "slug": args.slug,
        "generatedAt": datetime.now(timezone.utc).isoformat(),
        "pipelineVersion": PIPELINE_VERSION,
        "publicRoot": output_project.relative_to(ROOT).as_posix(),
        "profiles": profile_by_category,
        "curation": project_curation,
        "inventory": inventory,
        "videos": generated_videos,
        "posterOnly": generated_poster_only,
        "errors": errors,
        "summary": {"found": len(sources), "inventoried": len(inventory), "processed": processed, "skipped": skipped, "failed": failed, "postersProcessed": posters_processed, "postersSkipped": posters_skipped, "publishedVideos": len(generated_videos), "publishedPosterOnly": len(generated_poster_only), "publishedVariants": sum(len(video["sources"]) for video in generated_videos), "posters": len(generated_videos) + len(generated_poster_only)},
    }
    manifest.setdefault("projects", {})[args.slug] = project_manifest
    write_manifest(manifest)
    master_bytes = sum(item["bytes"] for item in inventory)
    web_bytes = sum(
        (ROOT / item["src"]).stat().st_size
        for video in generated_videos
        for item in video["sources"].values()
    ) + sum((ROOT / video["poster"]).stat().st_size for video in generated_videos + generated_poster_only)
    print(f"Processados: {processed} | Ignorados: {skipped} | Erros: {failed}")
    print(f"Masters: {master_bytes / (1024 * 1024):.1f} MiB")
    print(f"Web: {web_bytes / (1024 * 1024):.1f} MiB")
    print(f"Manifesto: {MANIFEST_PATH}")
    return 1 if errors or failed else 0


if __name__ == "__main__":
    raise SystemExit(main())
