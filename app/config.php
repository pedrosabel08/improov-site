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

function asset_url(string $path): string
{
    return base_url('assets/' . ltrim($path, '/'));
}

function api_url(string $path): string
{
    return base_url('api/' . ltrim($path, '/'));
}

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
