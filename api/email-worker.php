<?php

declare(strict_types=1);

require_once __DIR__ . '/email-service.php';
$projectRoot = dirname(__DIR__);
$once = in_array('--once', $argv ?? [], true);
$sleepSeconds = 10;
improovEmailLoadEnv($projectRoot . '/.env');

function workerLog(string $message): void
{
    fwrite(STDOUT, '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL);
}
function workerDb(): mysqli
{
    $db = @new mysqli(improovEmailEnv('DB_HOST'), improovEmailEnv('DB_USERNAME'), improovEmailEnv('DB_PASSWORD'), improovEmailEnv('DB_DATABASE'), (int)improovEmailEnv('DB_PORT', '3306'));
    if ($db->connect_errno || !$db->set_charset('utf8mb4')) throw new RuntimeException('Falha de conexão com o banco.');
    return $db;
}

function workerDefinition(string $type): array
{
    if ($type === 'candidate') return ['table' => 'candidaturas', 'events' => 'candidatura_eventos', 'foreign' => 'candidatura_id', 'fields' => 'id,nome,email,telefone,cidade_uf,area_cargo,disponibilidade_inicio,modelos_trabalho,portfolio_url,curriculo_url,linkedin_url,experiencia,mensagem,idioma'];
    return ['table' => 'contatos', 'events' => 'contato_eventos', 'foreign' => 'contato_id', 'fields' => 'id,nome,empresa,email,telefone,cidade_uf,tipo_interesse,empreendimento,mensagem,anexo_url,anexo_nome,anexo_mime,anexo_tamanho,idioma'];
}

function claimPending(mysqli $db, string $type): ?array
{
    $d = workerDefinition($type);
    $result = $db->query("SELECT {$d['fields']} FROM {$d['table']} WHERE email_status='pendente' ORDER BY id ASC LIMIT 1");
    if (!$result) throw new RuntimeException('Falha ao consultar fila.');
    $row = $result->fetch_assoc() ?: null;
    $result->free();
    if ($row === null) return null;
    $id = (int)$row['id'];
    $statement = $db->prepare("UPDATE {$d['table']} SET email_status='processando',email_erro=NULL WHERE id=? AND email_status='pendente'");
    $statement->bind_param('i', $id);
    $statement->execute();
    $claimed = $statement->affected_rows === 1;
    $statement->close();
    return $claimed ? $row : [];
}

function recoverQueues(mysqli $db): void
{
    foreach (['candidaturas', 'contatos'] as $table) $db->query("UPDATE {$table} SET email_status='pendente',email_erro='Worker reiniciado antes da conclusão.' WHERE email_status='processando' AND atualizado_em<DATE_SUB(NOW(),INTERVAL 15 MINUTE)");
}
function finishQueue(mysqli $db, string $type, int $id, array $result): void
{
    $d = workerDefinition($type);
    $status = $result['sent'] ? 'enviado' : 'falhou';
    $sentAt = $result['sent'] ? date('Y-m-d H:i:s') : null;
    $error = $result['sent'] ? null : (string)($result['error'] ?? 'Falha SMTP');
    $statement = $db->prepare("UPDATE {$d['table']} SET email_status=?,email_enviado_em=?,email_erro=? WHERE id=?");
    $statement->bind_param('sssi', $status, $sentAt, $error, $id);
    $statement->execute();
    $statement->close();
    $eventType = $result['sent'] ? 'notificacao_enviada' : 'notificacao_falhou';
    $description = $result['sent'] ? 'Notificação enviada pelo worker.' : 'Falha no envio pelo worker.';
    $statement = $db->prepare("INSERT INTO {$d['events']} ({$d['foreign']},tipo,descricao) VALUES (?,?,?)");
    $statement->bind_param('iss', $id, $eventType, $description);
    $statement->execute();
    $statement->close();
}

workerLog('Worker de e-mails iniciado.');
do {
    try {
        $db = workerDb();
        recoverQueues($db);
        $processed = false;
        foreach (['candidate', 'contact'] as $type) {
            $item = claimPending($db, $type);
            if (!$item) continue;
            $processed = true;
            $result = $type === 'candidate' ? improovSendCandidateEmail($item, $projectRoot) : improovSendContactEmail($item, $projectRoot);
            finishQueue($db, $type, (int)$item['id'], $result);
            workerLog(($result['sent'] ? 'Enviado' : 'Falhou') . ' ' . $type . ' #' . $item['id'] . '.');
        }
        $db->close();
        if (!$processed && !$once) sleep($sleepSeconds);
    } catch (Throwable $error) {
        workerLog('Erro: ' . $error->getMessage());
        if ($once) exit(1);
        sleep($sleepSeconds);
    }
} while (!$once);
workerLog('Worker de e-mails finalizado.');
