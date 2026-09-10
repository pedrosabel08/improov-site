<?php

declare(strict_types=1);

$requestPath = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
$projectRoot = realpath(dirname(__DIR__));
$publicPath = preg_replace('#^/improov-site(?=/|$)#i', '', $requestPath) ?: '/';
$relativePath = ltrim($publicPath, '/');
$candidate = $projectRoot === false
    ? false
    : realpath($projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath));
$projectPrefix = $projectRoot === false ? '' : rtrim($projectRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
if ($candidate !== false && is_file($candidate) && str_starts_with($candidate, $projectPrefix)) {
    return false;
}
require ($projectRoot ?: dirname(__DIR__)) . '/index.php';
