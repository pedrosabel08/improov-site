<?php

declare(strict_types=1);

function resolve_route(string $requestUri): array
{
    $path = rawurldecode((string) parse_url($requestUri, PHP_URL_PATH));
    $base = APP_BASE_URL === '/' ? '' : APP_BASE_URL;
    if ($base !== '' && str_starts_with($path, $base)) {
        $path = substr($path, strlen($base));
    }
    $path = trim($path, '/');
    $normalized = strtolower($path);

    $static = [
        '' => ['page' => 'home', 'active' => 'home'],
        'quem-somos' => ['page' => 'quem-somos', 'active' => 'quem-somos'],
        'projetos' => ['page' => 'projetos', 'active' => 'projetos'],
        'trabalhe-conosco' => ['page' => 'trabalhe-conosco', 'active' => 'trabalhe-conosco'],
        'contato' => ['page' => 'contato', 'active' => 'contato'],
        'privacidade' => ['page' => 'privacidade', 'active' => 'privacidade'],
    ];
    if (isset($static[$normalized])) {
        return $static[$normalized] + ['status' => 200];
    }
    if (preg_match('#^projetos/([a-z0-9-]+)$#', $normalized, $matches)) {
        return ['page' => 'project-detail', 'active' => 'projetos', 'slug' => $matches[1], 'status' => 200];
    }
    return ['page' => '404', 'active' => '', 'status' => 404];
}
