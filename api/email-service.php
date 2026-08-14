<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/../vendor/autoload.php';

function improovEmailLoadEnv(string $path): void
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
        $value = trim($value);
        if ($key === '') {
            continue;
        }

        if (strlen($value) >= 2) {
            $first = $value[0];
            $last = $value[strlen($value) - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        putenv($key . '=' . $value);
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

/**
 * Envia a notificação de uma candidatura já registrada.
 * O array deve conter as colunas da tabela candidaturas.
 *
 * @return array{sent: bool, error: ?string}
 */
function improovSendCandidateEmail(array $candidate, string $projectRoot): array
{
    improovEmailLoadEnv($projectRoot . DIRECTORY_SEPARATOR . '.env');

    $mailTo = improovEmailEnv('MAIL_TO', 'pedrosabel08@gmail.com') ?: 'pedrosabel08@gmail.com';
    $smtpHost = improovEmailEnv('SMTP_HOST', 'smtp.gmail.com');
    $smtpPort = (int) (improovEmailEnv('SMTP_PORT') ?: '587');
    $smtpUsername = improovEmailEnv('SMTP_USERNAME');
    $smtpPassword = improovEmailEnv('SMTP_PASSWORD');
    $smtpAuth = strtolower(improovEmailEnv('SMTP_AUTH', 'true')) !== 'false';
    $smtpEncryption = strtolower(improovEmailEnv('SMTP_ENCRYPTION', 'tls'));
    $smtpTimeout = (int) (improovEmailEnv('SMTP_TIMEOUT') ?: '20');
    $smtpFrom = improovEmailEnv('SMTP_FROM') ?: ($smtpUsername ?: improovEmailEnv('MAIL_FROM', $mailTo));
    $smtpFromName = improovEmailEnv('SMTP_FROM_NAME', 'Improov');

    $value = static fn(string $key, string $default = ''): string =>
        (string) ($candidate[$key] ?? $default);

    $name = $value('nome');
    $email = $value('email');
    $models = $value('modelos_trabalho', 'Não informado');
    $experience = $value('experiencia', 'Não informado') ?: 'Não informado';
    $message = $value('mensagem', 'Não informado') ?: 'Não informado';
    $portfolio = $value('portfolio_url');
    $linkedin = $value('linkedin_url') ?: 'Não informado';
    $resumeRelativePath = ltrim($value('curriculo_url'), '/\\');
    $resumePath = $projectRoot . DIRECTORY_SEPARATOR . str_replace(
        ['/', '\\'],
        DIRECTORY_SEPARATOR,
        $resumeRelativePath
    );

    if ($resumeRelativePath === '' || !is_file($resumePath)) {
        throw new RuntimeException('Currículo não encontrado: ' . $resumePath);
    }

    $resumeName = basename($resumePath);
    $html = 'improovEmailHtml';
    $mailHtmlBody = <<<'HTML'
<!doctype html>
<html lang="pt-BR">
  <body style="margin:0;background:#f3f4f6;color:#17202a;font-family:Arial,Helvetica,sans-serif;">
    <div style="padding:32px 12px;">
      <div style="max-width:680px;margin:0 auto;background:#ffffff;border-radius:18px;overflow:hidden;">
        <div style="padding:30px 34px;background:#17202a;color:#ffffff;">
          <div style="font-size:12px;letter-spacing:2px;text-transform:uppercase;color:#f0c75e;font-weight:700;">IMPROOV</div>
          <h1 style="margin:12px 0 8px;font-size:28px;line-height:1.15;">Nova candidatura recebida</h1>
          <p style="margin:0;color:#d7dde3;font-size:15px;line-height:1.5;">Uma nova pessoa se candidatou pelo site.</p>
        </div>
        <div style="padding:30px 34px;">
          <h2 style="margin:18px 0 6px;font-size:22px;color:#17202a;">{{nome}}</h2>
          <p style="margin:0 0 24px;color:#68727d;font-size:14px;">Área/cargo de interesse: <strong style="color:#17202a;">{{area}}</strong></p>
          <h3 style="margin:0 0 10px;font-size:13px;letter-spacing:1px;text-transform:uppercase;color:#8b6f12;">Dados de contato</h3>
          <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;font-size:14px;">
            <tr><td style="padding:10px 0;border-bottom:1px solid #e8ebee;color:#68727d;width:38%;">E-mail</td><td style="padding:10px 0;border-bottom:1px solid #e8ebee;color:#17202a;"><a href="mailto:{{email}}" style="color:#856b00;text-decoration:none;">{{email}}</a></td></tr>
            <tr><td style="padding:10px 0;border-bottom:1px solid #e8ebee;color:#68727d;">Telefone</td><td style="padding:10px 0;border-bottom:1px solid #e8ebee;color:#17202a;">{{telefone}}</td></tr>
            <tr><td style="padding:10px 0;border-bottom:1px solid #e8ebee;color:#68727d;">Cidade/UF</td><td style="padding:10px 0;border-bottom:1px solid #e8ebee;color:#17202a;">{{cidade}}</td></tr>
            <tr><td style="padding:10px 0;border-bottom:1px solid #e8ebee;color:#68727d;">Disponibilidade</td><td style="padding:10px 0;border-bottom:1px solid #e8ebee;color:#17202a;">{{disponibilidade}}</td></tr>
            <tr><td style="padding:10px 0;color:#68727d;">Modelo de trabalho</td><td style="padding:10px 0;color:#17202a;">{{modelos}}</td></tr>
          </table>
          <div style="margin:26px 0 24px;padding:18px;border-radius:12px;background:#faf8f0;">
            <h3 style="margin:0 0 8px;font-size:13px;letter-spacing:1px;text-transform:uppercase;color:#8b6f12;">Materiais</h3>
            <p style="margin:8px 0;font-size:14px;"><strong>Portfólio:</strong> {{portfolio}}</p>
            <p style="margin:8px 0;font-size:14px;"><strong>Currículo:</strong> anexo neste e-mail</p>
            <p style="margin:8px 0;font-size:14px;"><strong>LinkedIn:</strong> {{linkedin}}</p>
          </div>
          <h3 style="margin:0 0 8px;font-size:13px;letter-spacing:1px;text-transform:uppercase;color:#8b6f12;">Resumo profissional</h3>
          <div style="padding:16px;border-left:3px solid #f0c75e;background:#f8f9fa;color:#3c4650;font-size:14px;line-height:1.6;white-space:pre-line;">{{experiencia}}</div>
          <h3 style="margin:24px 0 8px;font-size:13px;letter-spacing:1px;text-transform:uppercase;color:#8b6f12;">Mensagem</h3>
          <div style="padding:16px;border-left:3px solid #d7dde3;background:#f8f9fa;color:#3c4650;font-size:14px;line-height:1.6;white-space:pre-line;">{{mensagem}}</div>
        </div>
        <div style="padding:18px 34px;background:#f8f9fa;color:#8a949d;font-size:12px;line-height:1.5;">E-mail automático enviado pelo formulário de candidaturas da Improov.</div>
      </div>
    </div>
  </body>
</html>
HTML;
    $mailHtmlBody = strtr($mailHtmlBody, [
        '{{nome}}' => $html($name),
        '{{area}}' => $html($value('area_cargo')),
        '{{email}}' => $html($email),
        '{{telefone}}' => $html($value('telefone')),
        '{{cidade}}' => $html($value('cidade_uf')),
        '{{disponibilidade}}' => $html($value('disponibilidade_inicio', 'Não informada')),
        '{{modelos}}' => $html($models),
        '{{portfolio}}' => $portfolio !== '' ? '<a href="' . $html($portfolio) . '" style="color:#856b00;">Abrir link</a>' : 'Não informado',
        '{{linkedin}}' => $html($linkedin),
        '{{experiencia}}' => $html($experience),
        '{{mensagem}}' => $html($message),
    ]);

    $mailTextBody = implode("\n", [
        'New application received - Improov',
        '',
        'Name: ' . $name,
        'Email: ' . $email,
        'Phone: ' . $value('telefone'),
        'City/State: ' . $value('cidade_uf'),
        'Area/role: ' . $value('area_cargo'),
        'Availability: ' . $value('disponibilidade_inicio', 'Not provided'),
        'Work models: ' . $models,
        'Portfolio: ' . ($portfolio ?: 'Not provided'),
        'Resume: attached',
        'LinkedIn: ' . $linkedin,
        '',
        'Experience or summary:',
        $experience,
        '',
        'Message:',
        $message,
    ]);

    try {
        $mailer = new PHPMailer(true);
        $mailer->isSMTP();
        $mailer->Host = $smtpHost;
        $mailer->Port = $smtpPort > 0 ? $smtpPort : 587;
        $mailer->SMTPAuth = $smtpAuth;
        if ($smtpAuth) {
            $mailer->Username = $smtpUsername;
            $mailer->Password = $smtpPassword;
        }
        $mailer->Timeout = $smtpTimeout > 0 ? $smtpTimeout : 20;
        $mailer->CharSet = 'UTF-8';
        $mailer->SMTPSecure = ($smtpEncryption === 'ssl' || $smtpEncryption === 'smtps')
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;
        $mailer->setFrom($smtpFrom, $smtpFromName);
        $mailer->addAddress($mailTo);
        $mailer->addReplyTo($email, $name);
        $mailer->Subject = 'Nova candidatura | Improov | ' . trim(str_replace(["\r", "\n"], ' ', $name));
        $mailer->isHTML(true);
        $mailer->addAttachment($resumePath, $resumeName, PHPMailer::ENCODING_BASE64, 'application/pdf');
        $mailer->Body = $mailHtmlBody;
        $mailer->AltBody = $mailTextBody;
        $mailer->send();

        return ['sent' => true, 'error' => null];
    } catch (Throwable $exception) {
        return ['sent' => false, 'error' => $exception->getMessage()];
    }
}
