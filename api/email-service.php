<?php

declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/../vendor/autoload.php';

function improovEmailLoadEnv(string $path): void
{
    if (!is_file($path)) return;
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        if ($key !== '' && getenv($key) === false) putenv($key . '=' . $value);
    }
}

function improovEmailEnv(string $key, string $default = ''): string
{
    $value = getenv($key);
    return $value === false ? $default : trim($value);
}

function improovEmailHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function improovCreateMailer(string $to, string $replyEmail, string $replyName): PHPMailer
{
    $mailer = new PHPMailer(true);
    $mailer->isSMTP();
    $mailer->Host = improovEmailEnv('SMTP_HOST', 'smtp.gmail.com');
    $mailer->Port = (int) (improovEmailEnv('SMTP_PORT', '587'));
    $mailer->SMTPAuth = strtolower(improovEmailEnv('SMTP_AUTH', 'true')) !== 'false';
    if ($mailer->SMTPAuth) {
        $mailer->Username = improovEmailEnv('SMTP_USERNAME');
        $mailer->Password = improovEmailEnv('SMTP_PASSWORD');
    }
    $mailer->Timeout = (int) improovEmailEnv('SMTP_TIMEOUT', '20');
    $mailer->CharSet = 'UTF-8';
    $encryption = strtolower(improovEmailEnv('SMTP_ENCRYPTION', 'tls'));
    $mailer->SMTPSecure = in_array($encryption, ['ssl', 'smtps'], true) ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
    $from = improovEmailEnv('SMTP_FROM', improovEmailEnv('SMTP_USERNAME', improovEmailEnv('MAIL_FROM', $to)));
    $mailer->setFrom($from, improovEmailEnv('SMTP_FROM_NAME', 'Improov'));
    $mailer->addAddress($to);
    $mailer->addReplyTo($replyEmail, $replyName);
    $mailer->isHTML(true);
    return $mailer;
}

function improovStoredFile(string $projectRoot, string $relativePath): ?string
{
    if ($relativePath === '') return null;
    $uploadsRoot = realpath($projectRoot . DIRECTORY_SEPARATOR . 'uploads');
    $file = realpath($projectRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ltrim($relativePath, '/\\')));
    if ($uploadsRoot === false || $file === false || !is_file($file) || !str_starts_with($file, $uploadsRoot . DIRECTORY_SEPARATOR)) return null;
    return $file;
}

function improovEmailLayout(string $eyebrow, string $title, string $intro, string $content): string
{
    return '<!doctype html><html lang="pt-BR"><body style="margin:0;background:#eef0f2;font-family:Arial,sans-serif;font-size:16px;line-height:1.5;color:#17202a"><div style="padding:28px 12px"><div style="max-width:680px;margin:auto;background:#fff;border-radius:14px;overflow:hidden"><header style="padding:30px 34px;background:#101416;color:#fff"><div style="font-size:12px;letter-spacing:2px;color:#9bb8cc">' . improovEmailHtml($eyebrow) . '</div><h1 style="font-size:26px;line-height:1.15;margin:10px 0 8px">' . improovEmailHtml($title) . '</h1><p style="margin:0;color:#bac2c8">' . improovEmailHtml($intro) . '</p></header><main style="padding:30px 34px;line-height:1.65">' . $content . '</main></div></div></body></html>';
}

function improovSendCandidateEmail(array $candidate, string $projectRoot): array
{
    improovEmailLoadEnv($projectRoot . '/.env');
    try {
        $resume = improovStoredFile($projectRoot, (string) ($candidate['curriculo_url'] ?? ''));
        if ($resume === null) throw new RuntimeException('Currículo não encontrado.');
        $h = 'improovEmailHtml';
        $content = '<h2 style="margin-top:0">' . $h((string)$candidate['nome']) . '</h2><p><strong>Área:</strong> ' . $h((string)$candidate['area_cargo']) . '<br><strong>E-mail:</strong> ' . $h((string)$candidate['email']) . '<br><strong>Telefone:</strong> ' . $h((string)$candidate['telefone']) . '<br><strong>Cidade:</strong> ' . $h((string)$candidate['cidade_uf']) . '<br><strong>Disponibilidade:</strong> ' . $h((string)$candidate['disponibilidade_inicio']) . '<br><strong>Modelo:</strong> ' . $h((string)$candidate['modelos_trabalho']) . '</p><p><strong>Portfólio:</strong> ' . $h((string)$candidate['portfolio_url']) . '<br><strong>LinkedIn:</strong> ' . $h((string)$candidate['linkedin_url']) . '</p><h3>Experiência</h3><p>' . nl2br($h((string)$candidate['experiencia'])) . '</p>';
        $mailer = improovCreateMailer(improovEmailEnv('MAIL_TO', 'contato@improov.com.br'), (string)$candidate['email'], (string)$candidate['nome']);
        $mailer->Subject = 'Nova candidatura | Improov | ' . trim(str_replace(["\r", "\n"], ' ', (string)$candidate['nome']));
        $mailer->Body = improovEmailLayout('TRABALHE CONOSCO', 'Nova candidatura recebida', 'Uma nova pessoa se candidatou pelo site.', $content);
        $mailer->AltBody = strip_tags(str_replace('<br>', "\n", $content));
        $mailer->addAttachment($resume, basename($resume));
        $mailer->send();
        return ['sent' => true, 'error' => null];
    } catch (Throwable $error) {
        return ['sent' => false, 'error' => $error->getMessage()];
    }
}

function improovSendContactEmail(array $contact, string $projectRoot): array
{
    improovEmailLoadEnv($projectRoot . '/.env');
    try {
        $h = 'improovEmailHtml';
        $content = '<h2 style="margin-top:0">' . $h((string)$contact['nome']) . '</h2><p><strong>Empresa:</strong> ' . $h((string)$contact['empresa']) . '<br><strong>E-mail:</strong> ' . $h((string)$contact['email']) . '<br><strong>Telefone:</strong> ' . $h((string)$contact['telefone']) . '<br><strong>Cidade:</strong> ' . $h((string)$contact['cidade_uf']) . '<br><strong>Interesse:</strong> ' . $h((string)$contact['tipo_interesse']) . '<br><strong>Empreendimento:</strong> ' . $h((string)$contact['empreendimento']) . '</p><h3>Como podemos ajudar?</h3><p>' . nl2br($h((string)$contact['mensagem'])) . '</p>';
        $to = improovEmailEnv('CONTACT_MAIL_TO', improovEmailEnv('MAIL_TO', 'contato@improov.com.br'));
        $mailer = improovCreateMailer($to, (string)$contact['email'], (string)$contact['nome']);
        $mailer->Subject = 'Novo contato comercial | Improov | ' . trim(str_replace(["\r", "\n"], ' ', (string)$contact['nome']));
        $mailer->Body = improovEmailLayout('CONTATO COMERCIAL', 'Nova mensagem recebida', 'Um novo contato comercial chegou pelo site.', $content);
        $mailer->AltBody = strip_tags(str_replace('<br>', "\n", $content));
        $attachment = improovStoredFile($projectRoot, (string)($contact['anexo_url'] ?? ''));
        if ($attachment !== null) $mailer->addAttachment($attachment, (string)($contact['anexo_nome'] ?: basename($attachment)));
        $mailer->send();
        return ['sent' => true, 'error' => null];
    } catch (Throwable $error) {
        return ['sent' => false, 'error' => $error->getMessage()];
    }
}
