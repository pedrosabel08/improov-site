<?php $hero = $project['media']['hero']; ?>
<main id="conteudo" data-project-detail="<?= escape($project['slug']) ?>">
  <section class="project-detail-hero"><?= responsive_image($hero['src'], translated($hero['alt']), (int)$hero['width'], (int)$hero['height'], 'project-detail-hero__image', '100vw', true) ?><div class="project-detail-hero__shade"></div>
    <div class="project-detail-hero__title container"><span class="eyebrow" data-i18n="projects.project">Projeto</span>
      <h1 data-project-title><?= escape(translated($project['title'])) ?></h1>
      <p data-project-location><span class="project-card__location-icon"><?= site_icon('pin') ?></span><span data-project-location-text><?= escape(translated($project['location'])) ?></span></p>
    </div>
  </section>
  <section class="project-overview section container">
    <div><span class="eyebrow" data-i18n="project.concept">Conceito</span>
      <h2 data-project-subtitle><?= escape(translated($project['detail']['subtitle'])) ?></h2>
    </div>
    <div class="project-description" data-project-description><?php foreach ($project['detail']['description']['pt-BR'] as $paragraph): ?><p><?= escape($paragraph) ?></p><?php endforeach; ?></div>
    <dl class="project-info">
      <div>
        <dt data-i18n="project.client">Cliente</dt>
        <dd><?= escape($project['detail']['info']['client']) ?></dd>
      </div>
      <div>
        <dt data-i18n="project.architect">Arquiteto</dt>
        <dd><?= escape($project['detail']['info']['architect']) ?></dd>
      </div>
      <div>
        <dt data-i18n="project.year">Ano</dt>
        <dd><?= escape($project['detail']['info']['year']) ?></dd>
      </div>
    </dl>
  </section>
  <section class="project-gallery section container"><?php foreach ($project['media']['gallery'] as $index => $image): ?><?= responsive_image($image['src'], translated($hero['alt']) . ' — ' . ($index + 1), (int)$image['width'], (int)$image['height'], $index === 0 ? 'project-gallery__wide' : '', '(max-width: 767px) 100vw, 50vw') ?><?php endforeach; ?></section>
  <?php if (!empty($project['media']['film']['src'])): ?><section class="project-film section container"><span class="eyebrow" data-i18n="project.film">Filme</span><video controls playsinline preload="metadata" poster="<?= escape(thumbnail_url($project['media']['film']['poster'], 1440)) ?>">
        <source src="<?= escape(asset($project['media']['film']['src'])) ?>" type="video/mp4">
      </video></section><?php endif; ?>
  <div class="container"><?php require APP_ROOT . '/partials/closing-cta.php'; ?></div>
  <script type="application/json" id="projects-data">
    <?= json_encode(['projects' => all_projects()], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?>
  </script>
</main>