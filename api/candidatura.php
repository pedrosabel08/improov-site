<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/upload.php';
require_once __DIR__ . '/lib/rate-limit.php';

require_post_request();
$data = validate_fields(['nome' => [160, true], 'email' => [254, true], 'telefone' => [40, true], 'cidade_uf' => [120, true], 'area_cargo' => [160, true], 'disponibilidade_inicio' => [160, true], 'portfolio' => [2048, false], 'linkedin' => [2048, false], 'experiencia' => [2000, true], 'idioma' => [10, true]]);
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) json_response(422, ['success' => false, 'message' => 'Informe um e-mail válido.']);
foreach (['portfolio', 'linkedin'] as $field) if ($data[$field] !== '' && !valid_http_url($data[$field])) json_response(422, ['success' => false, 'message' => 'Informe links válidos.']);
if (post_text('lgpd') !== 'Aceito') json_response(422, ['success' => false, 'message' => 'É necessário aceitar a política de privacidade.']);
$model = post_text('modelo_trabalho');
if (!in_array($model, ['Presencial', 'Híbrido', 'Remoto'], true)) json_response(422, ['success' => false, 'message' => 'Selecione um modelo de trabalho.']);

$db = null;
$upload = null;
try {
    $db = database_connection();
    enforce_rate_limit($db, 'candidatura');
    $upload = store_upload('curriculo', 'candidaturas', ['pdf' => ['application/pdf']], 10 * 1024 * 1024, true);
    $db->begin_transaction();
    $acceptedAt = date('Y-m-d H:i:s');
    $status = 'recebida';
    $emailStatus = 'pendente';
    $message = '';
    $statement = $db->prepare('INSERT INTO candidaturas (nome,email,telefone,cidade_uf,area_cargo,disponibilidade_inicio,modelos_trabalho,portfolio_url,curriculo_url,linkedin_url,experiencia,mensagem,idioma,lgpd_aceito,lgpd_aceito_em,status,email_status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,1,?,?,?)');
    if (!$statement) throw new RuntimeException('Falha ao preparar candidatura.');
    $statement->bind_param('ssssssssssssssss', $data['nome'], $data['email'], $data['telefone'], $data['cidade_uf'], $data['area_cargo'], $data['disponibilidade_inicio'], $model, $data['portfolio'], $upload['path'], $data['linkedin'], $data['experiencia'], $message, $data['idioma'], $acceptedAt, $status, $emailStatus);
    if (!$statement->execute()) throw new RuntimeException('Falha ao registrar candidatura.');
    $id = (int) $db->insert_id;
    $statement->close();
    insert_audit_event($db, 'candidatura_eventos', 'candidatura_id', $id, 'recebida', 'Candidatura registrada.');
    $db->commit();
    $db->close();
    json_response(200, ['success' => true, 'queued' => true]);
} catch (Throwable $error) {
    if ($db instanceof mysqli) {
        @$db->rollback();
        @$db->close();
    }
    if ($upload && is_file($upload['absolutePath'])) @unlink($upload['absolutePath']);
    error_log('Improov candidatura: ' . $error->getMessage());
    json_response(500, ['success' => false, 'message' => 'O recebimento de candidaturas está temporariamente indisponível.']);
}
