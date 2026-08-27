<?php

declare(strict_types=1);

function case_configs(): array
{
    static $configs;
    if ($configs !== null) {
        return $configs;
    }

    $path = APP_ROOT . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'cases.json';
    if (!is_file($path)) {
        return $configs = [];
    }

    $decoded = json_decode((string) file_get_contents($path), true);
    return $configs = is_array($decoded['cases'] ?? null) ? $decoded['cases'] : [];
}

function find_case_config(string $slug): ?array
{
    $case = case_configs()[$slug] ?? null;
    return is_array($case) ? $case : null;
}

function case_videos(string $slug): array
{
    static $manifests = [];
    if (isset($manifests[$slug])) {
        return $manifests[$slug];
    }

    $path = APP_ROOT . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'video-manifest.json';
    if (!is_file($path)) {
        return $manifests[$slug] = [];
    }

    $manifest = json_decode((string) file_get_contents($path), true);
    $project = $manifest['projects'][$slug] ?? [];
    $videos = [];
    foreach ($project['videos'] ?? [] as $video) {
        if (is_array($video) && !empty($video['id'])) {
            $videos[$video['id']] = $video;
        }
    }
    $posterOnlyEntries = $project['posterOnly'] ?? [];
    if (is_array($posterOnlyEntries) && !empty($posterOnlyEntries['id'])) {
        $posterOnlyEntries = [$posterOnlyEntries];
    }
    foreach ($posterOnlyEntries as $posterOnly) {
        if (is_array($posterOnly) && !empty($posterOnly['id'])) {
            $videos[$posterOnly['id']] = $posterOnly;
        }
    }

    return $manifests[$slug] = $videos;
}

function case_video_source(array $video): ?array
{
    $sources = $video['sources'] ?? [];
    if (!is_array($sources) || $sources === []) {
        return null;
    }
    krsort($sources, SORT_NUMERIC);
    foreach ($sources as $source) {
        if (is_array($source) && !empty($source['src'])) {
            return $source;
        }
    }
    return null;
}

function case_image_size(string $source): array
{
    $media = media_map()[$source] ?? [];
    return [
        (int) ($media['width'] ?? 1600),
        (int) ($media['height'] ?? 1000),
    ];
}

function case_next_project(string $slug, array $rule = []): ?array
{
    $explicitSlug = (string) ($rule['slug'] ?? '');
    if ($explicitSlug !== '') {
        return find_project($explicitSlug);
    }

    $projects = all_projects();
    $mode = (string) ($rule['mode'] ?? 'editorial');
    foreach ($projects as $index => $project) {
        if (($project['slug'] ?? '') !== $slug) {
            continue;
        }
        for ($offset = 1; $offset < count($projects); $offset++) {
            $candidate = $projects[($index + $offset) % count($projects)] ?? null;
            if (!is_array($candidate)) {
                continue;
            }
            if ($mode === 'catalog' || find_case_config((string) ($candidate['slug'] ?? '')) !== null) {
                return $candidate;
            }
        }
    }
    return null;
}
