<?php

declare(strict_types=1);

function load_environment(string $path): void
{
    if (!is_file($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        if ($key !== '' && getenv($key) === false) {
            putenv($key . '=' . $value);
        }
    }
}

load_environment(dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env');

function env(string $key, string $default = ''): string
{
    $value = getenv($key);
    return $value === false ? $default : trim($value);
}

define('APP_ROOT', dirname(__DIR__));
define('APP_BASE_URL', '/' . trim(env('APP_BASE_URL', '/improov-site'), '/'));
define('APP_ORIGIN', rtrim(env('APP_ORIGIN', 'https://improov.com.br'), '/'));

function base_url(string $path = ''): string
{
    $base = APP_BASE_URL === '/' ? '' : APP_BASE_URL;
    $path = ltrim($path, '/');
    return $base . ($path === '' ? '/' : '/' . $path);
}

function canonical_url(string $path = ''): string
{
    return APP_ORIGIN . base_url($path);
}

/**
 * Resolve a public asset path to the matching file under APP_ROOT.
 *
 * The filesystem root and the public base URL are deliberately kept
 * separate, so the application also works when APP_BASE_URL is changed to
 * the domain root in production.
 */
function asset(string $path): string
{
    $path = trim($path);
    if ($path === '' || str_contains($path, "\0")) {
        return $path;
    }

    $parts = parse_url($path);
    if ($parts === false) {
        return $path;
    }

    $scheme = strtolower((string) ($parts['scheme'] ?? ''));
    $rawPath = (string) ($parts['path'] ?? '');
    if ($scheme !== '' || str_starts_with($path, '//')) {
        return $path;
    }

    $relativePath = ltrim(str_replace('\\', '/', $rawPath), '/');
    $basePath = trim(APP_BASE_URL, '/');
    if ($basePath !== '' && ($relativePath === $basePath || str_starts_with($relativePath, $basePath . '/'))) {
        $relativePath = ltrim(substr($relativePath, strlen($basePath)), '/');
    }

    if ($relativePath === '' || str_contains($relativePath, '..')) {
        return $path;
    }

    $filesystemPath = APP_ROOT . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
    $version = is_file($filesystemPath) ? @filemtime($filesystemPath) : false;
    $publicUrl = base_url($relativePath);

    $query = isset($parts['query']) ? (string) $parts['query'] : '';
    if ($version !== false) {
        $query .= ($query === '' ? '' : '&') . 'v=' . rawurlencode((string) $version);
    }
    if ($query !== '') {
        $publicUrl .= '?' . $query;
    }
    if (isset($parts['fragment'])) {
        $publicUrl .= '#' . $parts['fragment'];
    }

    return $publicUrl;
}

/**
 * Return the current mtime for a local public path without emitting PHP
 * warnings. Used by generated thumbnail URLs, whose response is not the
 * source file itself.
 */
function asset_mtime(string $path): ?int
{
    $parts = parse_url($path);
    if ($parts === false || isset($parts['scheme']) || str_starts_with($path, '//')) {
        return null;
    }

    $relativePath = ltrim(str_replace('\\', '/', (string) ($parts['path'] ?? '')), '/');
    $basePath = trim(APP_BASE_URL, '/');
    if ($basePath !== '' && ($relativePath === $basePath || str_starts_with($relativePath, $basePath . '/'))) {
        $relativePath = ltrim(substr($relativePath, strlen($basePath)), '/');
    }
    if ($relativePath === '' || str_contains($relativePath, '..')) {
        return null;
    }

    $filesystemPath = APP_ROOT . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
    $mtime = is_file($filesystemPath) ? @filemtime($filesystemPath) : false;
    return $mtime === false ? null : (int) $mtime;
}

function asset_url(string $path): string
{
    return asset('assets/' . ltrim($path, '/'));
}

function api_url(string $path): string
{
    return base_url('api/' . ltrim($path, '/'));
}

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
