<?php

declare(strict_types=1);

require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/icons.php';
require_once __DIR__ . '/app/content.php';
require_once __DIR__ . '/app/projects.php';
require_once __DIR__ . '/app/cases.php';
require_once __DIR__ . '/app/routes.php';

$route = resolve_route($_SERVER['REQUEST_URI'] ?? base_url());
$project = null;
$case = null;
if ($route['page'] === 'project-detail') {
    $project = find_project((string) $route['slug']);
    if ($project === null) {
        $route = ['page' => '404', 'active' => '', 'status' => 404];
    } else {
        $case = find_case_config((string) $project['slug']);
    }
}

http_response_code((int) $route['status']);
$meta = page_metadata($route['page'] === 'project-detail' ? 'projetos' : $route['page']);
if ($project !== null) {
    $meta['title'] = translated($project['title']) . ' — Improov';
    $meta['description'] = (string) ($project['detail']['description']['pt-BR'][0] ?? $meta['description']);
    $meta['path'] = 'projetos/' . $project['slug'];
    $meta['image'] = $project['media']['hero']['src'];
}
$site = site_content();
$pageKey = $route['page'];
$activePage = $route['active'];

require APP_ROOT . '/partials/head.php';
require APP_ROOT . '/partials/header.php';
require APP_ROOT . '/pages/' . $pageKey . '.php';
require APP_ROOT . '/partials/whatsapp.php';
require APP_ROOT . '/partials/footer.php';
