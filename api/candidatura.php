<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0');

function respond(int $status, array $payload): never
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function loadEnvFile(string $path): void
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

function envValue(string $key, string $default = ''): string
{
    $value = getenv($key);
    return $value === false ? $default : trim($value);
}

function postText(string $key): string
{
    $value = $_POST[$key] ?? '';
    return is_string($value) ? trim($value) : '';
}

function textLength(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

function isHttpUrl(string $value): bool
{
    if (!filter_var($value, FILTER_VALIDATE_URL)) {
        return false;
    }

    $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));
    return in_array($scheme, ['http', 'https'], true);
}

function insertEvent(mysqli $db, int $candidateId, string $type, string $description): bool
{
    $statement = $db->prepare(
        'INSERT INTO candidatura_eventos (candidatura_id, tipo, descricao) VALUES (?, ?, ?)'
    );
    if (!$statement) {
        return false;
    }

    $statement->bind_param('iss', $candidateId, $type, $description);
    $success = $statement->execute();
    $statement->close();
    return $success;
}

function safeHeaderValue(string $value): string
{
    return trim(str_replace(["\r", "\n"], ' ', $value));
}

function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, ['success' => false, 'message' => 'Método não permitido.']);
}

loadEnvFile(__DIR__ . '/../.env');

if (postText('website') !== '') {
    respond(400, ['success' => false, 'message' => 'Não foi possível processar a candidatura.']);
}

$fields = [
    'nome' => [160, true],
    'email' => [254, true],
    'telefone' => [40, true],
    'cidade_uf' => [120, true],
    'area_cargo' => [160, true],
    'disponibilidade_inicio' => [160, true],
    'portfolio' => [2048, false],
    'linkedin' => [2048, false],
    'experiencia' => [2000, true],
    'idioma' => [10, true],
];

$data = [];
foreach ($fields as $key => [$maxLength, $required]) {
    $value = postText($key);
    if ($required && $value === '') {
        respond(422, ['success' => false, 'message' => 'Preencha todos os campos obrigatórios.']);
    }
    if (textLength($value) > $maxLength) {
        respond(422, ['success' => false, 'message' => 'Um ou mais campos excedem o limite permitido.']);
    }
    $data[$key] = $value;
}

if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    respond(422, ['success' => false, 'message' => 'Informe um e-mail válido.']);
}

foreach (['portfolio'] as $urlField) {
    if ($data[$urlField] !== '' && !isHttpUrl($data[$urlField])) {
        respond(422, ['success' => false, 'message' => 'Informe um link válido para o portfólio.']);
    }
}

if ($data['linkedin'] !== '' && !isHttpUrl($data['linkedin'])) {
    respond(422, ['success' => false, 'message' => 'Informe um link válido para o LinkedIn.']);
}

$resumeUpload = $_FILES['curriculo'] ?? null;
if (!is_array($resumeUpload) || ($resumeUpload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    respond(422, ['success' => false, 'message' => 'Anexe seu currículo em PDF.']);
}

$resumeSize = (int) ($resumeUpload['size'] ?? 0);
$resumeOriginalName = basename((string) ($resumeUpload['name'] ?? 'curriculo.pdf'));
$resumeTmpPath = (string) ($resumeUpload['tmp_name'] ?? '');
$resumeExtension = strtolower((string) pathinfo($resumeOriginalName, PATHINFO_EXTENSION));
$resumeMime = '';
if (is_file($resumeTmpPath) && function_exists('finfo_open')) {
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    if ($fileInfo !== false) {
        $resumeMime = (string) finfo_file($fileInfo, $resumeTmpPath);
        finfo_close($fileInfo);
    }
}
if ($resumeExtension !== 'pdf' || ($resumeMime !== '' && $resumeMime !== 'application/pdf')) {
    respond(422, ['success' => false, 'message' => 'Envie apenas arquivos PDF.']);
}
if ($resumeSize < 1 || $resumeSize > 10 * 1024 * 1024) {
    respond(422, ['success' => false, 'message' => 'O currículo deve ter no máximo 10MB.']);
}

$uploadDirectory = __DIR__ . '/../uploads/candidaturas';
if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0750, true) && !is_dir($uploadDirectory)) {
    error_log('Improov: não foi possível criar o diretório de currículos.');
    respond(500, ['success' => false, 'message' => 'Não foi possível receber o currículo agora.']);
}
$storedResumeName = date('YmdHis') . '-' . bin2hex(random_bytes(8)) . '.pdf';
$storedResumePath = $uploadDirectory . DIRECTORY_SEPARATOR . $storedResumeName;
if (!move_uploaded_file($resumeTmpPath, $storedResumePath)) {
    error_log('Improov: não foi possível salvar o currículo enviado.');
    respond(500, ['success' => false, 'message' => 'Não foi possível salvar o currículo agora.']);
}
$data['curriculo'] = 'uploads/candidaturas/' . $storedResumeName;
$data['mensagem'] = '';

$models = $_POST['modelo_trabalho'] ?? [];
$models = is_array($models) ? $models : [$models];
$allowedModels = ['Presencial', 'Híbrido', 'HÃ­brido', 'Remoto'];
$selectedModels = [];
foreach ($models as $model) {
    if (!is_string($model)) {
        continue;
    }

    $normalizedModel = preg_replace('/[^a-z]/', '', strtolower($model));
    $canonicalModel = [
        'presencial' => 'Presencial',
        'hbrido' => 'Hibrido',
        'remoto' => 'Remoto',
    ][$normalizedModel] ?? null;
    if ($canonicalModel !== null && !in_array($canonicalModel, $selectedModels, true)) {
        $selectedModels[] = $canonicalModel;
    }
}
if ($selectedModels === []) {
    respond(422, ['success' => false, 'message' => 'Selecione pelo menos um modelo de trabalho.']);
}

$lgpd = postText('lgpd');
if ($lgpd !== 'Aceito') {
    respond(422, ['success' => false, 'message' => 'É necessário aceitar a política de privacidade.']);
}

$dbHost = envValue('DB_HOST');
$dbUser = envValue('DB_USERNAME');
$dbPassword = envValue('DB_PASSWORD');
$dbName = envValue('DB_DATABASE');
$dbPort = (int) (envValue('DB_PORT') ?: '3306');

if ($dbHost === '' || $dbUser === '' || $dbName === '' || $dbPort < 1) {
    error_log('Improov: configuração do banco incompleta.');
    respond(500, ['success' => false, 'message' => 'O recebimento de candidaturas está temporariamente indisponível.']);
}

mysqli_report(MYSQLI_REPORT_OFF);
$db = @new mysqli($dbHost, $dbUser, $dbPassword, $dbName, $dbPort);
if ($db->connect_errno) {
    error_log('Improov: falha de conexão com o banco: ' . $db->connect_error);
    respond(500, ['success' => false, 'message' => 'O recebimento de candidaturas está temporariamente indisponível.']);
}

if (!$db->set_charset('utf8mb4')) {
    error_log('Improov: não foi possível configurar UTF-8 no banco.');
    $db->close();
    respond(500, ['success' => false, 'message' => 'O recebimento de candidaturas está temporariamente indisponível.']);
}

$acceptedAt = date('Y-m-d H:i:s');
$modelsValue = implode(', ', $selectedModels);
$status = 'recebida';
$emailStatus = 'pendente';

$db->begin_transaction();
$statement = $db->prepare(
    'INSERT INTO candidaturas
        (nome, email, telefone, cidade_uf, area_cargo, disponibilidade_inicio,
         modelos_trabalho, portfolio_url, curriculo_url, linkedin_url, experiencia,
         mensagem, idioma, lgpd_aceito, lgpd_aceito_em, status, email_status)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);

if (!$statement) {
    $db->rollback();
    error_log('Improov: falha ao preparar o registro da candidatura: ' . $db->error);
    $db->close();
    respond(500, ['success' => false, 'message' => 'Não foi possível registrar a candidatura.']);
}

$lgpdAccepted = 1;
$statement->bind_param(
    'sssssssssssssisss',
    $data['nome'],
    $data['email'],
    $data['telefone'],
    $data['cidade_uf'],
    $data['area_cargo'],
    $data['disponibilidade_inicio'],
    $modelsValue,
    $data['portfolio'],
    $data['curriculo'],
    $data['linkedin'],
    $data['experiencia'],
    $data['mensagem'],
    $data['idioma'],
    $lgpdAccepted,
    $acceptedAt,
    $status,
    $emailStatus
);

if (!$statement->execute()) {
    $statement->close();
    $db->rollback();
    error_log('Improov: falha ao salvar a candidatura: ' . $db->error);
    $db->close();
    respond(500, ['success' => false, 'message' => 'Não foi possível registrar a candidatura.']);
}

$candidateId = (int) $db->insert_id;
$statement->close();

if (!insertEvent($db, $candidateId, 'recebida', 'Candidatura registrada.')) {
    $db->rollback();
    error_log('Improov: falha ao registrar evento da candidatura: ' . $db->error);
    $db->close();
    respond(500, ['success' => false, 'message' => 'Não foi possível registrar a candidatura.']);
}

$db->commit();

$mailTo = envValue('MAIL_TO', 'pedrosabel08@gmail.com') ?: 'pedrosabel08@gmail.com';
$mailFrom = envValue('MAIL_FROM') ?: 'pedrosabel08@gmail.com';
$subject = 'Nova candidatura | Improov | ' . safeHeaderValue($data['nome']);
$mailBody = implode("\n", [
    'Nova candidatura recebida pelo site Improov',
    '',
    'Nome: ' . $data['nome'],
    'E-mail: ' . $data['email'],
    'Telefone: ' . $data['telefone'],
    'Cidade/UF: ' . $data['cidade_uf'],
    'Área/cargo: ' . $data['area_cargo'],
    'Disponibilidade: ' . ($data['disponibilidade_inicio'] ?: 'Não informado'),
    'Modelos de trabalho: ' . $modelsValue,
    'Portfólio: ' . $data['portfolio'],
    'Currículo: ' . $data['curriculo'],
    'LinkedIn: ' . ($data['linkedin'] ?: 'Não informado'),
    '',
    'Experiência ou resumo:',
    $data['experiencia'] ?: 'Não informado',
    '',
    'Mensagem:',
    $data['mensagem'] ?: 'Não informado',
    '',
    'Idioma: ' . $data['idioma'],
    'Aceite LGPD: ' . $lgpd,
]);
$mailBody = implode("\n", [
    'New application received from the Improov website',
    '',
    'Name: ' . $data['nome'],
    'Email: ' . $data['email'],
    'Phone: ' . $data['telefone'],
    'City/State: ' . $data['cidade_uf'],
    'Area/role: ' . $data['area_cargo'],
    'Availability: ' . ($data['disponibilidade_inicio'] ?: 'Not provided'),
    'Work models: ' . $modelsValue,
    'Portfolio: ' . $data['portfolio'],
    'Resume: ' . $data['curriculo'],
    'LinkedIn: ' . ($data['linkedin'] ?: 'Not provided'),
    '',
    'Experience or summary:',
    $data['experiencia'] ?: 'Not provided',
    '',
    'Message:',
    $data['mensagem'] ?: 'Not provided',
    '',
    'Language: ' . $data['idioma'],
    'LGPD consent: ' . $lgpd,
]);

$mailBody = implode("\n", [
    'Olá, ' . $data['nome'] . '!',
    '',
    'Recebemos sua candidatura para a área/cargo de ' . $data['area_cargo'] . '.',
    '',
    'A equipe da Improov avaliará as informações e entrará em contato caso haja uma oportunidade compatível.',
    '',
    'Obrigado pelo interesse em fazer parte da Improov.',
]);

$mailTextBody = implode("\n", [
    'New application received - Improov',
    '',
    'Name: ' . $data['nome'],
    'Email: ' . $data['email'],
    'Phone: ' . $data['telefone'],
    'City/State: ' . $data['cidade_uf'],
    'Area/role: ' . $data['area_cargo'],
    'Availability: ' . ($data['disponibilidade_inicio'] ?: 'Not provided'),
    'Work models: ' . $modelsValue,
    'Portfolio: ' . $data['portfolio'],
    'Resume: ' . $data['curriculo'],
    'LinkedIn: ' . ($data['linkedin'] ?: 'Not provided'),
    '',
    'Experience or summary:',
    $data['experiencia'] ?: 'Not provided',
    '',
    'Message:',
    $data['mensagem'] ?: 'Not provided',
]);

$html = static fn(string $value): string => escapeHtml($value);
$mailHtmlBody = <<<'HTML'
<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova candidatura | Improov</title>
  </head>
  <body style="margin:0;background:#f3f4f6;color:#17202a;font-family:Arial,Helvetica,sans-serif;">
    <div style="padding:32px 12px;">
      <div style="max-width:680px;margin:0 auto;background:#ffffff;border-radius:18px;overflow:hidden;box-shadow:0 8px 30px rgba(23,32,42,.10);">
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
            <p style="margin:8px 0;font-size:14px;"><strong>Portfólio:</strong> <a href="{{portfolio}}" style="color:#856b00;">Abrir link</a></p>
            <p style="margin:8px 0;font-size:14px;"><strong>Currículo:</strong> <a href="{{curriculo}}" style="color:#856b00;">Abrir link</a></p>
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
    '{{nome}}' => $html($data['nome']),
    '{{area}}' => $html($data['area_cargo']),
    '{{email}}' => $html($data['email']),
    '{{telefone}}' => $html($data['telefone']),
    '{{cidade}}' => $html($data['cidade_uf']),
    '{{disponibilidade}}' => $html($data['disponibilidade_inicio'] ?: 'Nao informada'),
    '{{modelos}}' => $html($modelsValue),
    '{{portfolio}}' => $html($data['portfolio']),
    '{{curriculo}}' => $html($data['curriculo']),
    '{{linkedin}}' => $html($data['linkedin'] ?: 'Nao informado'),
    '{{experiencia}}' => $html($data['experiencia'] ?: 'Nao informado'),
    '{{mensagem}}' => $html($data['mensagem'] ?: 'Nao informada'),
]);

$smtpHost = envValue('SMTP_HOST', 'smtp.gmail.com');
$smtpPort = (int) (envValue('SMTP_PORT') ?: '587');
$smtpUsername = envValue('SMTP_USERNAME');
$smtpPassword = envValue('SMTP_PASSWORD');
$smtpAuth = strtolower(envValue('SMTP_AUTH', 'true')) !== 'false';
$smtpEncryption = strtolower(envValue('SMTP_ENCRYPTION', 'tls'));
$smtpTimeout = (int) (envValue('SMTP_TIMEOUT') ?: '20');
$smtpFrom = envValue('SMTP_FROM') ?: ($smtpUsername ?: $mailFrom);
$smtpFromName = envValue('SMTP_FROM_NAME', 'Improov');
$emailSent = false;
$emailError = null;

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

    if ($smtpEncryption === 'ssl' || $smtpEncryption === 'smtps') {
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } else {
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    }

    $mailer->setFrom($smtpFrom, $smtpFromName);
    $mailer->addAddress($mailTo);
    $mailer->addReplyTo($data['email'], $data['nome']);
    $mailer->Subject = $subject;
    $mailer->isHTML(true);
    $mailer->addAttachment($storedResumePath, $resumeOriginalName, PHPMailer::ENCODING_BASE64, 'application/pdf');
    $mailer->Body = $mailHtmlBody;
    $mailer->AltBody = $mailTextBody;
    $mailer->send();
    $emailSent = true;
} catch (Throwable $exception) {
    $emailError = $exception->getMessage();
    error_log('Improov: falha no envio SMTP da candidatura ' . $candidateId . ': ' . $emailError);
}

$emailStatus = $emailSent ? 'enviado' : 'falhou';
$sentAt = $emailSent ? date('Y-m-d H:i:s') : null;
$emailError = $emailSent ? null : ($emailError ?: 'Falha no envio SMTP.');

$update = $db->prepare(
    'UPDATE candidaturas SET email_status = ?, email_enviado_em = ?, email_erro = ? WHERE id = ?'
);
if ($update) {
    $update->bind_param('sssi', $emailStatus, $sentAt, $emailError, $candidateId);
    if (!$update->execute()) {
        error_log('Improov: falha ao atualizar status do e-mail da candidatura ' . $candidateId . '.');
    }
    $update->close();
} else {
    error_log('Improov: falha ao preparar status do e-mail da candidatura ' . $candidateId . '.');
}

$eventType = $emailSent ? 'notificacao_enviada' : 'notificacao_falhou';
$eventDescription = $emailSent ? 'Notificação enviada por e-mail.' : 'mail() retornou false.';
$eventDescription = $emailSent ? 'SMTP notification sent.' : 'SMTP delivery failed.';
if (!insertEvent($db, $candidateId, $eventType, $eventDescription)) {
    error_log('Improov: falha ao registrar evento de notificação da candidatura ' . $candidateId . '.');
}

$db->close();
respond(200, ['success' => true, 'emailSent' => $emailSent]);
