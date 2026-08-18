<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/config.php';
require_once __DIR__ . '/response.php';

ini_set('display_errors', '0');

function post_text(string $key): string
{
    $value = $_POST[$key] ?? '';
    return is_string($value) ? trim($value) : '';
}

function text_length(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

function valid_http_url(string $value): bool
{
    if (!filter_var($value, FILTER_VALIDATE_URL)) return false;
    return in_array(strtolower((string) parse_url($value, PHP_URL_SCHEME)), ['http', 'https'], true);
}

function database_connection(): mysqli
{
    mysqli_report(MYSQLI_REPORT_OFF);
    $db = @new mysqli(env('DB_HOST'), env('DB_USERNAME'), env('DB_PASSWORD'), env('DB_DATABASE'), (int) (env('DB_PORT', '3306')));
    if ($db->connect_errno || !$db->set_charset('utf8mb4')) {
        throw new RuntimeException('Falha de conexão com o banco.');
    }
    return $db;
}

function require_post_request(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') json_response(405, ['success' => false, 'message' => 'Método não permitido.']);
    if (post_text('website') !== '') json_response(400, ['success' => false, 'message' => 'Não foi possível processar o envio.']);
}

function validate_fields(array $definitions): array
{
    $data = [];
    foreach ($definitions as $key => [$max, $required]) {
        $value = post_text($key);
        if ($required && $value === '') json_response(422, ['success' => false, 'message' => 'Preencha todos os campos obrigatórios.']);
        if (text_length($value) > $max) json_response(422, ['success' => false, 'message' => 'Um ou mais campos excedem o limite permitido.']);
        $data[$key] = $value;
    }
    return $data;
}

function insert_audit_event(mysqli $db, string $table, string $foreignKey, int $id, string $type, string $description): void
{
    $allowed = ['candidatura_eventos' => 'candidatura_id', 'contato_eventos' => 'contato_id'];
    if (($allowed[$table] ?? '') !== $foreignKey) throw new InvalidArgumentException('Tabela de auditoria inválida.');
    $statement = $db->prepare("INSERT INTO {$table} ({$foreignKey}, tipo, descricao) VALUES (?, ?, ?)");
    if (!$statement) throw new RuntimeException('Falha ao preparar evento.');
    $statement->bind_param('iss', $id, $type, $description);
    if (!$statement->execute()) {
        $statement->close();
        throw new RuntimeException('Falha ao registrar evento.');
    }
    $statement->close();
}
