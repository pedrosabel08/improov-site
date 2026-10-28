<?php
require_once APP_ROOT . '/app/services.php';
$services = service_sections();
$heroProject = find_project('hsa-mon');
$heroVideo = service_hero_video('hsa-mon');
$closingSource = 'assets/projetos/AYA_KAR/6._AYA_KAR_Piscina_maior_EF_1_1.jpg';
$text = static fn (string $key): string => '<span data-i18n="services.' . escape($key) . '">' . escape(ui_text('services.' . $key)) . '</span>';
?>
<main id="conteudo" class="services-page">
  <section class="hero services-hero" aria-labelledby="services-hero-title">
    <div class="hero__media">
      <?php if ($heroVideo !== null): ?>
        <?= lazy_video($heroVideo, 'hero__image', true, ['data-services-hero' => 'true', 'aria-label' => translated($heroProject['title']) . ' — ' . ui_text('services.animations')]) ?>
      <?php else: ?>
        <?= responsive_image($heroProject['media']['hero']['src'], '', ...array_merge(case_image_size($heroProject['media']['hero']['src']), ['hero__image', '100vw', true])) ?>
      <?php endif; ?>
    </div>
    <div class="hero__shade"></div>
    <div class="hero__content container">
      <p class="eyebrow"><?= $text('title') ?></p>
      <h1 id="services-hero-title"><?= $text('heroTitle') ?></h1>
      <p class="services-hero__intro"><?= $text('heroIntro') ?></p>
      <div class="hero__actions">
        <a class="button hero__action hero__action--primary" href="#servicos"><?= $text('discover') ?><?= site_icon('arrow-down') ?></a>
        <a class="button hero__action hero__action--secondary" href="<?= escape(whatsapp_url()) ?>" target="_blank" rel="noopener" data-analytics-event="whatsapp_click" data-analytics-name="contact_whatsapp" data-analytics-location="services_hero"><?= $text('contact') ?></a>
      </div>
    </div>
    <p class="services-hero__credit"><?= escape(translated($heroProject['title'])) ?> <span>— <?= $text('animations') ?></span></p>
  </section>

  <nav id="servicos" class="services-nav chapter-nav" aria-label="<?= escape(ui_text('services.navigation')) ?>" data-i18n-aria="services.navigation" data-services-nav>
    <ol class="chapter-nav__inner">
      <?php foreach ($services as $index => $service): ?>
        <li><a href="#<?= escape($service['id']) ?>"><span aria-hidden="true"><?= sprintf('%02d', $index + 1) ?></span><?= $text($service['key']) ?></a></li>
      <?php endforeach; ?>
    </ol>
  </nav>

  <div class="services-content">
    <?php foreach ($services as $index => $service): ?>
      <?php if ($service['key'] === 'concept'): ?>
      <?php endif; ?>
      <?php require APP_ROOT . '/partials/service-gallery.php'; ?>
    <?php endforeach; ?>
  </div>

  <section class="services-closing" aria-labelledby="services-closing-title">
    <div class="services-closing__media"><?= responsive_image($closingSource, '', 1920, 1080, '', '100vw') ?></div>
    <div class="container services-closing__content">
      <h2 id="services-closing-title"><?= $text('closingTitle') ?></h2>
      <div class="hero__actions">
        <a class="button hero__action hero__action--primary" href="<?= escape(whatsapp_url()) ?>" target="_blank" rel="noopener" data-analytics-event="whatsapp_click" data-analytics-name="contact_whatsapp" data-analytics-location="services_closing"><?= $text('contact') ?><?= site_icon('arrow-up-right') ?></a>
        <a class="button hero__action hero__action--secondary" href="<?= escape(base_url('projetos')) ?>"><?= $text('viewWork') ?></a>
      </div>
    </div>
  </section>

  <dialog class="service-lightbox" data-service-lightbox aria-label="<?= escape(ui_text('services.gallery')) ?>" data-i18n-aria="services.gallery">
    <div class="service-lightbox__toolbar">
      <p data-lightbox-title></p>
      <button type="button" data-lightbox-close aria-label="<?= escape(ui_text('services.close')) ?>" data-i18n-aria="services.close">×</button>
    </div>
    <img class="service-lightbox__image" data-lightbox-image alt="">
    <div class="service-lightbox__controls">
      <button type="button" data-lightbox-prev aria-label="<?= escape(ui_text('services.previous')) ?>" data-i18n-aria="services.previous"><?= site_icon('arrow-left') ?></button>
      <span data-lightbox-counter aria-live="polite"></span>
      <button type="button" data-lightbox-next aria-label="<?= escape(ui_text('services.next')) ?>" data-i18n-aria="services.next"><?= site_icon('arrow-right') ?></button>
    </div>
  </dialog>
</main>
