<?php
declare(strict_types=1);

require_once __DIR__ . '/email-service.php';

$projectRoot = dirname(__DIR__);
$once = in_array('--once', $argv ?? [], true);
$sleepSeconds = 10;

function workerLog(string $message): void
{
    fwrite(STDOUT, '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL);
}

function workerEnv(string $key, string $default = ''): string
{
    $value = getenv($key);
    return $value === false ? $default : trim($value);
}

function connectWorkerDatabase(): mysqli
{
    $db = @new mysqli(
        workerEnv('DB_HOST'),
        workerEnv('DB_USERNAME'),
        workerEnv('DB_PASSWORD'),
        workerEnv('DB_DATABASE'),
        (int) (workerEnv('DB_PORT') ?: '3306')
    );

    if ($db->connect_errno) {
        throw new RuntimeException('Falha de conexão com o banco: ' . $db->connect_error);
    }
    if (!$db->set_charset('utf8mb4')) {
        $db->close();
        throw new RuntimeException('Não foi possível configurar UTF-8 no banco.');
    }

    return $db;
}

function claimPendingCandidate(mysqli $db): ?array
{
    $result = $db->query(
        "SELECT id, nome, email, telefone, cidade_uf, area_cargo, disponibilidade_inicio,
                modelos_trabalho, portfolio_url, curriculo_url, linkedin_url, experiencia,
                mensagem, idioma
           FROM candidaturas
          WHERE email_status = 'pendente'
          ORDER BY id ASC
          LIMIT 1"
    );
    if (!$result) {
        throw new RuntimeException('Falha ao buscar candidaturas pendentes: ' . $db->error);
    }

    $candidate = $result->fetch_assoc() ?: null;
    $result->free();
    if ($candidate === null) {
        return null;
    }

    $statement = $db->prepare(
        "UPDATE candidaturas
            SET email_status = 'processando', email_erro = NULL
          WHERE id = ? AND email_status = 'pendente'"
    );
    if (!$statement) {
        throw new RuntimeException('Falha ao preparar bloqueio da candidatura: ' . $db->error);
    }

    $candidateId = (int) $candidate['id'];
    $statement->bind_param('i', $candidateId);
    $statement->execute();
    $claimed = $statement->affected_rows === 1;
    $statement->close();

    return $claimed ? $candidate : [];
}

function recoverStuckCandidates(mysqli $db): void
{
    $db->query(
        "UPDATE candidaturas
            SET email_status = 'pendente', email_erro = 'Worker reiniciado antes da conclusão do envio.'
          WHERE email_status = 'processando'
            AND atualizado_em < DATE_SUB(NOW(), INTERVAL 15 MINUTE)"
    );
}

function updateCandidateEmailStatus(mysqli $db, int $candidateId, string $status, ?string $error): void
{
    $sentAt = $status === 'enviado' ? date('Y-m-d H:i:s') : null;
    $statement = $db->prepare(
        'UPDATE candidaturas SET email_status = ?, email_enviado_em = ?, email_erro = ? WHERE id = ?'
    );
    if (!$statement) {
        throw new RuntimeException('Falha ao preparar status do e-mail: ' . $db->error);
    }

    $statement->bind_param('sssi', $status, $sentAt, $error, $candidateId);
    $statement->execute();
    $statement->close();
}

function registerWorkerEvent(mysqli $db, int $candidateId, string $type, string $description): void
{
    $statement = $db->prepare(
        'INSERT INTO candidatura_eventos (candidatura_id, tipo, descricao) VALUES (?, ?, ?)'
    );
    if (!$statement) {
        throw new RuntimeException('Falha ao preparar evento do worker: ' . $db->error);
    }

    $statement->bind_param('iss', $candidateId, $type, $description);
    $statement->execute();
    $statement->close();
}

improovEmailLoadEnv($projectRoot . DIRECTORY_SEPARATOR . '.env');

workerLog('Worker de e-mails iniciado.');
do {
    try {
        $db = connectWorkerDatabase();
        recoverStuckCandidates($db);
        $candidate = claimPendingCandidate($db);

        if ($candidate === null) {
            $db->close();
            if ($once) {
                break;
            }
            sleep($sleepSeconds);
            continue;
        }

        if ($candidate === []) {
            $db->close();
            continue;
        }

        $candidateId = (int) $candidate['id'];
        try {
            $result = improovSendCandidateEmail($candidate, $projectRoot);
        } catch (Throwable $exception) {
            $result = ['sent' => false, 'error' => $exception->getMessage()];
        }
        if ($result['sent']) {
            updateCandidateEmailStatus($db, $candidateId, 'enviado', null);
            registerWorkerEvent($db, $candidateId, 'notificacao_enviada', 'Notificação enviada pelo worker.');
            workerLog('E-mail enviado para a candidatura #' . $candidateId . '.');
        } else {
            $error = $result['error'] ?: 'Falha desconhecida no envio SMTP.';
            updateCandidateEmailStatus($db, $candidateId, 'falhou', $error);
            registerWorkerEvent($db, $candidateId, 'notificacao_falhou', 'Falha no envio pelo worker.');
            workerLog('Falha no e-mail da candidatura #' . $candidateId . ': ' . $error);
        }
        $db->close();
    } catch (Throwable $exception) {
        workerLog('Erro do worker: ' . $exception->getMessage());
        if ($once) {
            exit(1);
        }
        sleep($sleepSeconds);
    }
} while (!$once);

workerLog('Worker de e-mails finalizado.');
