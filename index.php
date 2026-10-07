<?php

declare(strict_types=1);

require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/icons.php';
require_once __DIR__ . '/app/content.php';
require_once __DIR__ . '/app/projects.php';
require_once __DIR__ . '/app/cases.php';
require_once __DIR__ . '/app/routes.php';

$route = resolve_route($_SERVER['REQUEST_URI'] ?? base_url());
$GLOBALS['improov_request_language'] = $route['language'];
$project = null;
$case = null;
if ($route['page'] === 'project-detail') {
    $project = find_project((string) $route['slug']);
    if ($project === null) {
        $route = ['page' => '404', 'active' => '', 'status' => 404, 'language' => current_language()];
    } else {
        $case = find_case_config((string) $project['slug']);
    }
}

http_response_code((int) $route['status']);
$meta = page_metadata($route['page'] === 'project-detail' ? 'projetos' : $route['page']);
if ($project !== null) {
    $meta['title'] = translated($project['metadata']['title'] ?? $project['title']) . (isset($project['metadata']['title']) ? '' : ' — Improov');
    $language = current_language();
    $projectDescription = translated($project['metadata']['description'] ?? []);
    if ($projectDescription === '') {
        $projectDescription = (string) ($project['detail']['description'][$language][0] ?? $project['detail']['description']['pt-BR'][0] ?? '');
    }
    if ($projectDescription === '' && $case !== null) {
        $services = array_slice(array_map(static fn (array $service): string => translated($service, $language), $case['services'] ?? []), 0, 3);
        $services = array_values(array_filter($services, static fn (string $service): bool => $service !== ''));
        $serviceList = implode(', ', $services);
        $projectName = translated($project['title'], $language);
        $location = translated($project['location'], $language);
        $projectDescription = match ($language) {
            'en' => sprintf('Improov created %s for the %s development in %s.', $serviceList, $projectName, $location),
            'es' => sprintf('Improov creó %s para el proyecto inmobiliario %s, en %s.', $serviceList, $projectName, $location),
            default => sprintf('A Improov criou %s para o empreendimento %s, em %s.', $serviceList, $projectName, $location),
        };
    }
    $meta['description'] = $projectDescription !== '' ? $projectDescription : $meta['description'];
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
