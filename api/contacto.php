<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/upload.php';
require_once __DIR__ . '/lib/rate-limit.php';

require_post_request();
$data = validate_fields(['nome' => [160, true], 'empresa' => [160, false], 'email' => [254, true], 'telefone' => [40, true], 'cidade_uf' => [120, false], 'tipo_interesse' => [160, true], 'empreendimento' => [200, false], 'mensagem' => [3000, true], 'idioma' => [10, true]]);
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) json_response(422, ['success' => false, 'message' => 'Informe um e-mail válido.']);
if (post_text('lgpd') !== 'Aceito') json_response(422, ['success' => false, 'message' => 'É necessário aceitar a política de privacidade.']);

$db = null;
$upload = null;
try {
    $db = database_connection();
    enforce_rate_limit($db, 'contato');
    $upload = store_upload('anexo', 'contatos', ['pdf' => ['application/pdf'], 'jpg' => ['image/jpeg'], 'jpeg' => ['image/jpeg'], 'png' => ['image/png'], 'zip' => ['application/zip', 'application/x-zip-compressed', 'application/octet-stream']], 20 * 1024 * 1024, false);
    $db->begin_transaction();
    $acceptedAt = date('Y-m-d H:i:s');
    $status = 'recebido';
    $emailStatus = 'pendente';
    $attachmentPath = $upload['path'] ?? '';
    $attachmentName = $upload['originalName'] ?? '';
    $attachmentMime = $upload['mime'] ?? '';
    $attachmentSize = $upload['size'] ?? 0;
    $statement = $db->prepare('INSERT INTO contatos (nome,empresa,email,telefone,cidade_uf,tipo_interesse,empreendimento,mensagem,anexo_url,anexo_nome,anexo_mime,anexo_tamanho,idioma,lgpd_aceito,lgpd_aceito_em,status,email_status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,1,?,?,?)');
    if (!$statement) throw new RuntimeException('Falha ao preparar contato.');
    $statement->bind_param('sssssssssssissss', $data['nome'], $data['empresa'], $data['email'], $data['telefone'], $data['cidade_uf'], $data['tipo_interesse'], $data['empreendimento'], $data['mensagem'], $attachmentPath, $attachmentName, $attachmentMime, $attachmentSize, $data['idioma'], $acceptedAt, $status, $emailStatus);
    if (!$statement->execute()) throw new RuntimeException('Falha ao registrar contato.');
    $id = (int)$db->insert_id;
    $statement->close();
    insert_audit_event($db, 'contato_eventos', 'contato_id', $id, 'recebido', 'Contato comercial registrado.');
    $db->commit();
    $db->close();
    json_response(200, ['success' => true, 'queued' => true]);
} catch (Throwable $error) {
    if ($db instanceof mysqli) {
        @$db->rollback();
        @$db->close();
    }
    if ($upload && is_file($upload['absolutePath'])) @unlink($upload['absolutePath']);
    error_log('Improov contato: ' . $error->getMessage());
    json_response(500, ['success' => false, 'message' => 'O contato está temporariamente indisponível.']);
}
