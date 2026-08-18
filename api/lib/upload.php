<?php

declare(strict_types=1);

function store_upload(string $field, string $directoryName, array $allowedTypes, int $maxBytes, bool $required): ?array
{
    $upload = $_FILES[$field] ?? null;
    $error = is_array($upload) ? (int) ($upload['error'] ?? UPLOAD_ERR_NO_FILE) : UPLOAD_ERR_NO_FILE;
    if ($error === UPLOAD_ERR_NO_FILE && !$required) return null;
    if (!is_array($upload) || $error !== UPLOAD_ERR_OK) json_response(422, ['success' => false, 'message' => $required ? 'Anexe o arquivo solicitado.' : 'Não foi possível receber o anexo.']);

    $size = (int) ($upload['size'] ?? 0);
    $original = basename((string) ($upload['name'] ?? 'arquivo'));
    $temporary = (string) ($upload['tmp_name'] ?? '');
    $extension = strtolower((string) pathinfo($original, PATHINFO_EXTENSION));
    if ($size < 1 || $size > $maxBytes || !is_uploaded_file($temporary)) json_response(422, ['success' => false, 'message' => 'O arquivo excede o limite permitido ou é inválido.']);

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = (string) $finfo->file($temporary);
    $valid = false;
    foreach ($allowedTypes as $allowedExtension => $mimes) {
        if ($extension === $allowedExtension && in_array($mime, $mimes, true)) {
            $valid = true;
            break;
        }
    }
    if (!$valid) json_response(422, ['success' => false, 'message' => 'Formato de arquivo não permitido.']);

    $directory = dirname(__DIR__, 2) . '/uploads/' . trim($directoryName, '/');
    if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) throw new RuntimeException('Não foi possível preparar o armazenamento.');
    $storedName = date('YmdHis') . '-' . bin2hex(random_bytes(12)) . '.' . $extension;
    $absolutePath = $directory . DIRECTORY_SEPARATOR . $storedName;
    if (!move_uploaded_file($temporary, $absolutePath)) throw new RuntimeException('Não foi possível armazenar o arquivo.');
    return ['path' => 'uploads/' . trim($directoryName, '/') . '/' . $storedName, 'absolutePath' => $absolutePath, 'originalName' => $original, 'mime' => $mime, 'size' => $size];
}
