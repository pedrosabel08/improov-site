<?php

declare(strict_types=1);

function resolve_route(string $requestUri): array
{
    $path = rawurldecode((string) parse_url($requestUri, PHP_URL_PATH));
    $base = APP_BASE_URL === '/' ? '' : APP_BASE_URL;
    if ($base !== '' && str_starts_with($path, $base)) {
        $path = substr($path, strlen($base));
    }
    // The public root may be internally rewritten to the physical project
    // directory before PHP receives the request. Do not expose that prefix in
    // generated URLs, but accept it while resolving the route.
    $physicalPrefix = '/' . trim(basename(APP_ROOT), '/');
    if ($physicalPrefix !== '/' && ($path === $physicalPrefix || str_starts_with($path, $physicalPrefix . '/'))) {
        $path = substr($path, strlen($physicalPrefix));
    }
    $path = trim($path, '/');
    $language = 'pt-BR';
    if (preg_match('#^(en|es)(?:/(.*))?$#i', $path, $matches)) {
        $language = strtolower($matches[1]);
        $path = trim((string) ($matches[2] ?? ''), '/');
    }
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
        return $static[$normalized] + ['status' => 200, 'language' => $language];
    }
    if (preg_match('#^projetos/([a-z0-9-]+)$#', $normalized, $matches)) {
        return ['page' => 'project-detail', 'active' => 'projetos', 'slug' => $matches[1], 'status' => 200, 'language' => $language];
    }
    return ['page' => '404', 'active' => '', 'status' => 404, 'language' => $language];
}
