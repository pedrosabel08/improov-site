<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= escape($meta['title']) ?></title>
  <meta name="description" content="<?= escape($meta['description']) ?>">
  <link rel="canonical" href="<?= escape(canonical_url($meta['path'])) ?>">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="Improov">
  <meta property="og:title" content="<?= escape($meta['title']) ?>">
  <meta property="og:description" content="<?= escape($meta['description']) ?>">
  <meta property="og:url" content="<?= escape(canonical_url($meta['path'])) ?>">
  <?php $metaImage = str_starts_with($meta['image'], 'assets/') ? $meta['image'] : 'assets/' . $meta['image']; ?>
  <meta property="og:image" content="<?= escape(canonical_url('thumb.php?path=' . rawurlencode($metaImage) . '&w=1440&q=82')) ?>">
  <?php if (!in_array($pageKey, ['privacidade', '404'], true)): ?>
    <link rel="preload" as="image" href="<?= escape(thumbnail_url($metaImage, 1440, 82)) ?>" fetchpriority="high"><?php endif; ?>
  <meta name="theme-color" content="#050607">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&display=swap">
  <link rel="stylesheet" href="<?= escape(asset_url('css/tokens.css')) ?>">
  <link rel="stylesheet" href="<?= escape(asset_url('css/site.css')) ?>">
  <link rel="stylesheet" href="<?= escape(asset_url('css/pages.css')) ?>">
  <script>
    window.ImproovConfig = <?= json_encode(['baseUrl' => APP_BASE_URL, 'applicationEndpoint' => api_url('candidatura.php'), 'contactEndpoint' => api_url('contacto.php')], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
  </script>
  <script type="application/ld+json">
    <?= json_encode(['@context' => 'https://schema.org', '@type' => 'Organization', 'name' => 'Improov', 'url' => canonical_url(), 'email' => $site['email'], 'telephone' => $site['phoneDisplay'], 'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Blumenau', 'addressRegion' => 'SC', 'addressCountry' => 'BR'], 'sameAs' => array_values($site['social'])], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>
  </script>
  <?php if ($project !== null): ?><script type="application/ld+json">
      <?= json_encode(['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => [['@type' => 'ListItem', 'position' => 1, 'name' => 'Projetos', 'item' => canonical_url('projetos')], ['@type' => 'ListItem', 'position' => 2, 'name' => translated($project['title']), 'item' => canonical_url('projetos/' . $project['slug'])]]], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>
    </script><?php endif; ?>
</head>

<body class="page page--<?= escape($pageKey) ?>">
  <a class="skip-link" href="#conteudo" data-i18n="accessibility.skip">Pular para o conteúdo</a>