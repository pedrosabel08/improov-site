<?php

declare(strict_types=1);

function all_projects(): array
{
    static $projects;
    if ($projects !== null) {
        return $projects;
    }
    $path = APP_ROOT . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'projects.json';
    $decoded = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    $projects = array_values(array_filter($decoded['projects'] ?? [], static fn(array $item): bool => ($item['status'] ?? '') === 'published'));
    usort($projects, static fn(array $a, array $b): int => ($a['order'] ?? 999) <=> ($b['order'] ?? 999));
    return $projects;
}

function find_project(string $slug): ?array
{
    foreach (all_projects() as $project) {
        if (($project['slug'] ?? '') === $slug) {
            return $project;
        }
    }
    return null;
}

function translated(array $value, string $language = 'pt-BR'): string
{
    return (string) ($value[$language] ?? $value['pt-BR'] ?? '');
}

function home_projects(): array
{
    $projects = all_projects();
    $featured = array_values(array_filter($projects, static fn(array $project): bool => !empty($project['showOnHome'])));
    $remaining = array_values(array_filter($projects, static fn(array $project): bool => empty($project['showOnHome'])));
    usort($featured, static fn(array $a, array $b): int => ($a['homeOrder'] ?? 999) <=> ($b['homeOrder'] ?? 999));
    usort($remaining, static fn(array $a, array $b): int => ($a['order'] ?? 999) <=> ($b['order'] ?? 999));

    return array_slice(array_merge($featured, $remaining), 0, 6);
}
