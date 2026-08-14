<?php
// Gera uma versão JPEG redimensionada de uma imagem local e a mantém em cache.
// Uso: /improov-site/thumb.php?path=assets/foto.jpg&w=900&q=78

ini_set('display_errors', '0');

$baseDir = realpath(__DIR__);
$cacheDir = __DIR__ . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'thumbs';

if ($baseDir === false) {
    http_response_code(500);
    exit('Diretório base indisponível');
}

if (!is_dir($cacheDir) && !@mkdir($cacheDir, 0755, true)) {
    http_response_code(500);
    exit('Cache indisponível');
}

$path = isset($_GET['path']) ? rawurldecode((string) $_GET['path']) : '';
$width = isset($_GET['w']) ? (int) $_GET['w'] : 600;
$quality = isset($_GET['q']) ? (int) $_GET['q'] : 78;

$path = trim(str_replace("\0", '', $path));
$path = (string) parse_url($path, PHP_URL_PATH);
$path = str_replace('\\', '/', $path);
$path = ltrim($path, '/');

if (strpos($path, 'improov-site/') === 0) {
    $path = substr($path, strlen('improov-site/'));
}

if ($path === '' || strpos($path, '..') !== false || $width < 1 || $quality < 1 || $quality > 100) {
    http_response_code(400);
    exit('Parâmetros inválidos');
}

$source = realpath($baseDir . DIRECTORY_SEPARATOR . $path);
$basePrefix = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

if ($source === false || !is_file($source) || strpos($source, $basePrefix) !== 0) {
    http_response_code(404);
    exit('Imagem não encontrada');
}

$info = @getimagesize($source);
if (!$info || empty($info['mime'])) {
    http_response_code(415);
    exit('Arquivo não é uma imagem válida');
}

$sourceWidth = (int) $info[0];
$sourceHeight = (int) $info[1];
$mime = $info['mime'];

if ($sourceWidth < 1 || $sourceHeight < 1) {
    http_response_code(415);
    exit('Dimensões inválidas');
}

$newWidth = min($width, $sourceWidth);
$newHeight = max(1, (int) round($sourceHeight * ($newWidth / $sourceWidth)));

$cacheKey = sha1($source . '|' . @filemtime($source) . '|w=' . $newWidth . '|q=' . $quality);
$cacheFile = $cacheDir . DIRECTORY_SEPARATOR . $cacheKey . '.jpg';

function sendCachedThumbnail(string $file): void
{
    $mtime = @filemtime($file) ?: time();
    $etag = '"' . md5_file($file) . '"';

    if (
        (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) ||
        (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && @strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $mtime)
    ) {
        http_response_code(304);
        exit;
    }

    header('Content-Type: image/jpeg');
    header('Cache-Control: public, max-age=2592000, immutable');
    header('ETag: ' . $etag);
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $mtime) . ' GMT');
    readfile($file);
    exit;
}

if (is_file($cacheFile) && (@filemtime($cacheFile) ?: 0) >= (@filemtime($source) ?: 0)) {
    sendCachedThumbnail($cacheFile);
}

if (!function_exists('imagecreatetruecolor')) {
    // Mantém o site funcional caso a hospedagem ainda não tenha a extensão GD.
    // Para ativar o redimensionamento, habilite ext-gd no PHP do servidor.
    header('Content-Type: ' . ($mime ?: 'application/octet-stream'));
    header('Cache-Control: public, max-age=86400');
    readfile($source);
    exit;
}

switch ($mime) {
    case 'image/jpeg':
        $sourceImage = @imagecreatefromjpeg($source);
        break;
    case 'image/png':
        $sourceImage = @imagecreatefrompng($source);
        break;
    case 'image/gif':
        $sourceImage = @imagecreatefromgif($source);
        break;
    case 'image/webp':
        $sourceImage = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($source) : false;
        break;
    default:
        $sourceImage = false;
}

if (!$sourceImage) {
    http_response_code(415);
    exit('Formato de imagem não suportado');
}

$thumbnail = imagecreatetruecolor($newWidth, $newHeight);
$white = imagecolorallocate($thumbnail, 255, 255, 255);
imagefill($thumbnail, 0, 0, $white);

imagecopyresampled(
    $thumbnail,
    $sourceImage,
    0,
    0,
    0,
    0,
    $newWidth,
    $newHeight,
    $sourceWidth,
    $sourceHeight
);

$temporaryFile = $cacheFile . '.' . uniqid('', true) . '.tmp';
$written = imagejpeg($thumbnail, $temporaryFile, $quality);
imagedestroy($sourceImage);
imagedestroy($thumbnail);

if (!$written || !@rename($temporaryFile, $cacheFile)) {
    @unlink($temporaryFile);
    http_response_code(500);
    exit('Não foi possível criar a thumbnail');
}

sendCachedThumbnail($cacheFile);
