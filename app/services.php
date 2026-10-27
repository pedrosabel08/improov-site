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
        ['id' => 'salas-imersivas', 'key' => 'immersive', 'items' => []],
    ];
}

function service_media_title(array $item, string $category, int $index): string
{
    if (!empty($item['caption'])) {
        return (string) $item['caption'];
    }
    if (!empty($item['project']) && !empty($item['name'])) {
        return $item['project'] . ' - ' . $item['name'];
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
