<?php

declare(strict_types=1);

$requestPath = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
$documentRoot = dirname(__DIR__, 2);
$candidate = realpath($documentRoot . str_replace('/', DIRECTORY_SEPARATOR, $requestPath));
if ($candidate !== false && is_file($candidate)) {
    return false;
}
require dirname(__DIR__) . '/index.php';
