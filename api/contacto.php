<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/contact-fields.php';
require_once __DIR__ . '/lib/rate-limit.php';

require_post_request();
$data = validate_fields(['nome' => [160, true], 'empresa' => [160, true], 'email' => [254, false], 'telefone' => [40, true], 'idioma' => [10, true], 'utm_atribuicao' => [3000, false]]);
try {
    $data = normalize_contact_fields($data);
} catch (InvalidArgumentException $error) {
    json_response(422, ['success' => false, 'message' => $error->getMessage()]);
}
if (post_text('lgpd') !== 'Aceito') json_response(422, ['success' => false, 'message' => 'É necessário aceitar a política de privacidade.']);

$db = null;
try {
    $db = database_connection();
    enforce_rate_limit($db, 'contato');
    $db->begin_transaction();
    $acceptedAt = date('Y-m-d H:i:s');
    $status = 'recebido';
    $emailStatus = 'pendente';
    $attachmentPath = '';
    $attachmentName = '';
    $attachmentMime = '';
    $attachmentSize = 0;
    $statement = $db->prepare('INSERT INTO contatos (nome,empresa,email,telefone,cidade_uf,tipo_interesse,empreendimento,mensagem,anexo_url,anexo_nome,anexo_mime,anexo_tamanho,idioma,utm_atribuicao,lgpd_aceito,lgpd_aceito_em,status,email_status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,1,?,?,?)');
    if (!$statement) throw new RuntimeException('Falha ao preparar contato: ' . $db->error);
    $statement->bind_param('sssssssssssisssss', $data['nome'], $data['empresa'], $data['email'], $data['telefone'], $data['cidade_uf'], $data['tipo_interesse'], $data['empreendimento'], $data['mensagem'], $attachmentPath, $attachmentName, $attachmentMime, $attachmentSize, $data['idioma'], $data['utm_atribuicao'], $acceptedAt, $status, $emailStatus);
    if (!$statement->execute()) throw new RuntimeException('Falha ao registrar contato: ' . $statement->error);
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
    error_log('Improov contato: ' . $error->getMessage());
    json_response(500, ['success' => false, 'message' => 'O contato está temporariamente indisponível.']);
}
