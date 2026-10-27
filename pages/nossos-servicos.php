<?php
require_once APP_ROOT . '/app/services.php';
$services = service_sections();
?>
<main id="conteudo" class="services-page">
  <header class="services-page__heading container">
    <h1 data-i18n="services.title"><?= escape(ui_text('services.title')) ?></h1>
  </header>
  <div class="services-layout container">
    <nav class="services-nav" aria-label="<?= escape(ui_text('services.navigation')) ?>" data-i18n-aria="services.navigation" data-services-nav>
      <p class="services-nav__label" data-i18n="services.navigation"><?= escape(ui_text('services.navigation')) ?></p>
      <ol>
        <?php foreach ($services as $index => $service): ?>
          <li><a href="#<?= escape($service['id']) ?>"<?= $index === 0 ? ' aria-current="location"' : '' ?>><span class="services-nav__number" aria-hidden="true"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span><span data-i18n="services.<?= escape($service['key']) ?>"><?= escape(ui_text('services.' . $service['key'])) ?></span></a></li>
        <?php endforeach; ?>
      </ol>
    </nav>
    <div class="services-content">
      <?php foreach ($services as $index => $service):
          $isTour = $service['tour'] ?? false;
          $sectionTitle = ui_text('services.' . $service['key']);
      ?>
        <section class="service-section service-section--<?= escape($service['key']) ?>" id="<?= escape($service['id']) ?>" aria-labelledby="<?= escape($service['id']) ?>-title" data-service-section>
          <header class="service-section__heading">
            <span class="service-section__number" aria-hidden="true"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
            <h2 id="<?= escape($service['id']) ?>-title" data-i18n="services.<?= escape($service['key']) ?>"><?= escape($sectionTitle) ?></h2>
          </header>
          <?php if ($service['items'] !== []): ?>
            <div class="service-carousel service-carousel--editorial<?= $isTour ? ' service-carousel--tour' : '' ?>"<?= !$isTour ? ' data-service-carousel data-carousel-loop' : '' ?>>
              <div class="service-carousel__track" id="<?= escape($service['id']) ?>-items"<?= !$isTour ? ' tabindex="0" role="region"' : '' ?> aria-label="<?= escape($sectionTitle) ?>" data-i18n-aria="services.<?= escape($service['key']) ?>">
                <?php foreach ($service['items'] as $itemIndex => $item): ?>
                  <?php $itemTitle = service_media_title($item, $service['key'], $itemIndex); ?>
                  <article class="service-card<?= $service['key'] === 'plans' ? ' service-card--plan' : '' ?><?= $service['key'] === 'animations' ? ' service-card--animation' : '' ?>">
                    <?php if ($isTour): ?>
                      <div class="service-tour" data-service-tour data-tour-src="<?= escape($item['src']) ?>">
                        <button class="service-tour__launch" type="button" data-tour-launch aria-label="<?= escape(ui_text('services.openTour')) ?>" data-i18n-aria="services.openTour">
                          <?= responsive_image(find_project('rdo-val')['media']['hero']['src'], $itemTitle, 1920, 1080, 'service-tour__poster', '(max-width: 767px) 100vw, 75vw') ?>
                          <span class="service-tour__prompt"><span aria-hidden="true">360º</span><span data-i18n="services.openTour"><?= escape(ui_text('services.openTour')) ?></span></span>
                        </button>
                      </div>
                      <noscript><a href="<?= escape($item['src']) ?>" target="_blank" rel="noopener"><?= escape(ui_text('services.openTour')) ?></a></noscript>
                    <?php elseif (isset($item['src'])): ?>
                      <button class="service-card__image" style="--service-media-ratio: <?= (int) $item['width'] ?> / <?= (int) $item['height'] ?>" type="button" data-service-image data-image-src="<?= escape(thumbnail_url($item['src'], 1920)) ?>" data-image-title="<?= escape($itemTitle) ?>" data-image-caption="<?= ($item['showCaption'] ?? true) ? 'true' : 'false' ?>" aria-label="<?= escape(ui_text('services.enlarge') . ': ' . $itemTitle) ?>">
                        <?= responsive_image($item['src'], $itemTitle, (int) $item['width'], (int) $item['height'], 'service-card__photo', '(max-width: 767px) 80vw, (max-width: 1023px) 52vw, 65vw') ?>
                        <span class="service-card__expand" aria-hidden="true">↗</span>
                      </button>
                    <?php else:
                        $video = find_video($item['manifest'], $item['id']);
                        $videoSource = $video !== null ? case_video_source($video) : null;
                    ?>
                      <?php if ($videoSource !== null): ?>
                        <div class="service-card__video-wrap">
                          <video class="service-card__video" width="<?= (int) $video['width'] ?>" height="<?= (int) $video['height'] ?>" controls playsinline preload="none" data-lazy-poster="<?= escape(asset($video['poster'])) ?>" aria-label="<?= escape($itemTitle) ?>" data-service-video<?= $service['key'] === 'animations' ? ' data-service-animation muted' . (!empty($video['loopCandidate']) ? ' loop' : '') : '' ?> data-service-video-src="<?= escape(asset($videoSource['src'])) ?>">
                            <source data-src="<?= escape(asset($videoSource['src'])) ?>" type="video/mp4">
                          </video>
                          <button class="service-card__play" type="button" data-service-play hidden aria-label="<?= escape(ui_text('services.play') . ': ' . $itemTitle) ?>"><span aria-hidden="true">▶</span></button>
                        </div>
                      <?php endif; ?>
                    <?php endif; ?>
                    <?php if (($item['showCaption'] ?? true) === true): ?><h3><?= escape($itemTitle) ?></h3><?php endif; ?>
                  </article>
                <?php endforeach; ?>
              </div>
              <?php if (!$isTour): ?>
                <div class="service-carousel__controls" hidden>
                  <span class="service-carousel__counter" aria-live="polite" aria-atomic="true"><span data-carousel-current>01</span> <span aria-hidden="true">/</span> <?= str_pad((string) count($service['items']), 2, '0', STR_PAD_LEFT) ?></span>
                  <div class="service-carousel__arrows">
                    <button type="button" data-carousel-prev aria-controls="<?= escape($service['id']) ?>-items" aria-label="<?= escape(ui_text('services.previous')) ?>" data-i18n-aria="services.previous" disabled><span aria-hidden="true">←</span></button>
                    <button type="button" data-carousel-next aria-controls="<?= escape($service['id']) ?>-items" aria-label="<?= escape(ui_text('services.next')) ?>" data-i18n-aria="services.next"><span aria-hidden="true">→</span></button>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </section>
      <?php endforeach; ?>
    </div>
  </div>
  <dialog class="service-lightbox" data-service-lightbox aria-label="<?= escape(ui_text('services.gallery')) ?>" data-i18n-aria="services.gallery">
    <div class="service-lightbox__toolbar">
      <p data-lightbox-title></p>
      <button type="button" data-lightbox-close aria-label="<?= escape(ui_text('services.close')) ?>" data-i18n-aria="services.close">×</button>
    </div>
    <img class="service-lightbox__image" data-lightbox-image alt="">
    <div class="service-lightbox__controls">
      <button type="button" data-lightbox-prev aria-label="<?= escape(ui_text('services.previous')) ?>" data-i18n-aria="services.previous">←</button>
      <span data-lightbox-counter aria-live="polite"></span>
      <button type="button" data-lightbox-next aria-label="<?= escape(ui_text('services.next')) ?>" data-i18n-aria="services.next">→</button>
    </div>
  </dialog>
</main>
