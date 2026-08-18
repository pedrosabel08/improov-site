<?php

declare(strict_types=1);

function enforce_rate_limit(mysqli $db, string $scope, int $limit = 5, int $windowSeconds = 3600): void
{
    $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $secret = env('RATE_LIMIT_SECRET', APP_ORIGIN . '|improov');
    $hash = hash_hmac('sha256', $ip, $secret);
    $now = date('Y-m-d H:i:s');
    $windowStart = date('Y-m-d H:i:s', time() - $windowSeconds);
    $db->query("DELETE FROM form_rate_limits WHERE atualizado_em < DATE_SUB(NOW(), INTERVAL 2 DAY)");
    $statement = $db->prepare('SELECT id, tentativas, janela_inicio FROM form_rate_limits WHERE escopo = ? AND ip_hash = ? LIMIT 1');
    if (!$statement) throw new RuntimeException('Rate limit indisponível.');
    $statement->bind_param('ss', $scope, $hash);
    $statement->execute();
    $row = $statement->get_result()->fetch_assoc();
    $statement->close();
    if ($row && $row['janela_inicio'] >= $windowStart && (int) $row['tentativas'] >= $limit) json_response(429, ['success' => false, 'message' => 'Muitas tentativas. Aguarde antes de enviar novamente.']);
    if (!$row || $row['janela_inicio'] < $windowStart) {
        $statement = $db->prepare('INSERT INTO form_rate_limits (escopo, ip_hash, tentativas, janela_inicio, atualizado_em) VALUES (?, ?, 1, ?, ?) ON DUPLICATE KEY UPDATE tentativas = 1, janela_inicio = VALUES(janela_inicio), atualizado_em = VALUES(atualizado_em)');
        $statement->bind_param('ssss', $scope, $hash, $now, $now);
    } else {
        $id = (int) $row['id'];
        $statement = $db->prepare('UPDATE form_rate_limits SET tentativas = tentativas + 1, atualizado_em = ? WHERE id = ?');
        $statement->bind_param('si', $now, $id);
    }
    $statement->execute();
    $statement->close();
}
