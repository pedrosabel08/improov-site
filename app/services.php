<?php

declare(strict_types=1);

function service_sections(): array
{
    $media = json_decode((string) file_get_contents(APP_ROOT . '/data/services-media.json'), true, 512, JSON_THROW_ON_ERROR);
    $groups = $media['groups'];
    $tour = null;
    foreach (find_case_config('rdo-val')['sections'] ?? [] as $section) {
        if (($section['type'] ?? '') === 'tour') {
            $tour = $section;
            $tour['project'] = translated(find_project('rdo-val')['title']);
            $tour['name'] = ui_text('services.tourName');
            break;
        }
    }
    return [
        ['id' => 'imagens-3d', 'key' => 'images', 'items' => $groups['images']],
        ['id' => 'plantas-humanizadas', 'key' => 'plans', 'items' => $groups['plans']],
        ['id' => 'imagens-360', 'key' => 'tour', 'items' => $tour ? [$tour] : [], 'tour' => true],
        ['id' => 'animacoes-3d', 'key' => 'animations', 'items' => $groups['animations']],
        ['id' => 'filmes-conceito', 'key' => 'concept', 'items' => $groups['concept']],
        ['id' => 'filmes-teaser', 'key' => 'teasers', 'items' => $groups['teasers']],
        ['id' => 'filmes-documentais', 'key' => 'documentaries', 'items' => $groups['documentaries']],
        ['id' => 'filmes-institucionais', 'key' => 'institutional', 'items' => $groups['institutional']],
    ];
}

/** Resolve an existing image or video poster for service media. */
function service_preview(array $service): string
{
    $item = $service['items'][0] ?? [];
    if ($service['tour'] ?? false) {
        return 'assets/projetos/rdo-val/imagens/imagem-37-rdo-val-living-com-vista-real-fotografica-torre-2-final-12-ef.jpg';
    }
    if (isset($item['src'])) return $item['src'];
    $video = isset($item['manifest'], $item['id']) ? find_video($item['manifest'], $item['id']) : null;
    return $video['poster'] ?? '';
}

/** Reuse an available derivative of the case's exact hero source. */
function service_hero_video(string $slug): ?array
{
    $id = (string) (find_case_config($slug)['hero']['video'] ?? '');
    $video = case_videos($slug)[$id] ?? null;
    if ($video === null) return null;
    $available = static function (array $candidate): bool {
        $source = case_video_source($candidate);
        return $source !== null && !empty($candidate['poster'])
            && is_file(APP_ROOT . '/' . $source['src'])
            && is_file(APP_ROOT . '/' . $candidate['poster']);
    };
    if ($available($video)) return $video;
    if (empty($video['sourceSha256'])) return null;
    foreach (video_manifest()['projects'] ?? [] as $project) {
        foreach ($project['videos'] ?? [] as $candidate) {
            if (($candidate['sourceSha256'] ?? '') === $video['sourceSha256'] && $available($candidate)) return $candidate;
        }
    }
    return null;
}

function service_media_title(array $item, string $category, int $index): string
{
    if (in_array($category, ['concept', 'teasers', 'documentaries', 'institutional'], true) && !empty($item['project'])) {
        return (string) $item['project'];
    }
    if (!empty($item['caption'])) {
        return (string) $item['caption'];
    }
    if (!empty($item['project']) && !empty($item['name'])) {
        $name = $item['name'] === 'Implantação em fotomontagem' ? ui_text('services.sitePhotomontage') : $item['name'];
        return $item['project'] . ' — ' . $name;
    }
    if (($item['showCaption'] ?? true) === false && !empty($item['name'])) {
        return $item['name'];
    }
    if (!empty($item['title'])) {
        return (string) $item['title'];
    }
    $type = match ($category) {
        'plans' => 'plan',
        'animations' => 'animation',
        default => 'image',
    };
    $title = ui_text('services.' . $type) . ' ' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
    return empty($item['project']) ? $title : $item['project'] . ' — ' . $title;
}
