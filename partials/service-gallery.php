<?php
$isTour = $service['tour'] ?? false;
$sectionTitle = ui_text('services.' . $service['key']);
?>
<section class="service-section service-section--<?= escape($service['key']) ?>" id="<?= escape($service['id']) ?>" aria-labelledby="<?= escape($service['id']) ?>-title" data-service-section>
  <header class="service-section__heading container">
    <div class="service-section__title"><span class="service-section__number" aria-hidden="true"><?= sprintf('%02d', array_search($service['key'], array_column($services, 'key'), true) + 1) ?></span>
      <h2 id="<?= escape($service['id']) ?>-title"><?= $text($service['key']) ?></h2>
    </div>
  </header>
  <?php if ($service['items'] !== []): ?>
    <div class="service-carousel<?= $isTour ? ' service-carousel--tour container' : ' case-v3-gallery__carousel' ?>" <?= !$isTour ? ' data-service-carousel' : '' ?>>
      <?php if (!$isTour): ?><button class="case-v3-gallery__carousel-control case-v3-gallery__carousel-control--previous" type="button" data-carousel-prev aria-controls="<?= escape($service['id']) ?>-items" aria-label="<?= escape(ui_text('services.previous')) ?>" data-i18n-aria="services.previous"><?= site_icon('arrow-left', 'case-v3-icon') ?></button><?php endif; ?>
      <div class="service-carousel__track<?= !$isTour ? ' case-v3-gallery__rail' : '' ?>" id="<?= escape($service['id']) ?>-items" <?= !$isTour ? ' tabindex="0" role="region"' : '' ?> aria-label="<?= escape($sectionTitle) ?>" data-i18n-aria="services.<?= escape($service['key']) ?>">
        <?php foreach ($service['items'] as $itemIndex => $item):
          $itemTitle = service_media_title($item, $service['key'], $itemIndex);
          $translatedName = ($item['name'] ?? '') === 'Implantação em fotomontagem';
          $captionProject = (string) ($item['project'] ?? '');
          $captionPrefix = $captionProject . ' — ';
          $hasProject = $captionProject !== '' && ($itemTitle === $captionProject || str_starts_with($itemTitle, $captionPrefix));
          $captionName = $hasProject ? ($itemTitle === $captionProject ? '' : substr($itemTitle, strlen($captionPrefix))) : $itemTitle;
          $video = !$isTour && !isset($item['src']) ? find_video($item['manifest'], $item['id']) : null;
          $videoSource = $video !== null ? case_video_source($video) : null;
          $mediaWidth = $isTour ? 16 : (int) ($item['width'] ?? $video['width'] ?? 16);
          $mediaHeight = $isTour ? 9 : (int) ($item['height'] ?? $video['height'] ?? 9);
          $mediaRatio = number_format($mediaWidth / max(1, $mediaHeight), 8, '.', '');
        ?>
          <article class="service-card<?= !$isTour ? ' case-v3-gallery__rail-item' : '' ?><?= $service['key'] === 'plans' ? ' service-card--plan' : '' ?>" id="<?= escape($service['id']) ?>-slide-<?= $itemIndex ?>">
            <div class="service-card__frame<?= !$isTour ? ' case-v3-gallery__media-frame' : '' ?>" style="--service-media-ratio: <?= $mediaRatio ?>">
              <div class="case-v3-gallery__media-stage">
                <?php if ($isTour): ?>
                  <div class="service-tour" data-service-tour data-tour-src="<?= escape($item['src']) ?>">
                    <button class="service-tour__launch" type="button" data-tour-launch aria-label="<?= escape(ui_text('services.openTour')) ?>" data-i18n-aria="services.openTour">
                      <?= responsive_image(service_preview($service), '', 1920, 1080, 'service-tour__poster', '100vw') ?>
                      <span class="service-tour__prompt"><span aria-hidden="true">360º</span><?= $text('openTour') ?></span>
                    </button>
                  </div>
                <?php elseif (isset($item['src'])): ?>
                  <button class="service-card__image case-v3-gallery__image-trigger" type="button" data-service-image<?= $translatedName ? ' data-service-translated-name="services.sitePhotomontage"' : '' ?> data-image-src="<?= escape(thumbnail_url($item['src'], 1920)) ?>" data-image-title="<?= escape($itemTitle) ?>" data-image-project="<?= $hasProject ? escape($captionProject) : '' ?>" aria-label="<?= escape(ui_text('services.enlarge') . ': ' . $itemTitle) ?>">
                    <?= responsive_image($item['src'], $itemTitle, $mediaWidth, $mediaHeight, 'service-card__photo case-v3-image', '(max-width: 767px) 90vw, min(72vw, 1050px)') ?>
                  </button>
                <?php elseif ($videoSource !== null): ?>
                  <div class="service-card__video-wrap">
                    <video class="service-card__video case-v3-gallery__video" width="<?= $mediaWidth ?>" height="<?= $mediaHeight ?>"<?= $service['key'] === 'animations' ? ' data-service-animation muted loop' : ' controls' ?> playsinline preload="none" data-lazy-poster="<?= escape(asset($video['poster'])) ?>" aria-label="<?= escape($itemTitle) ?>" data-service-video data-service-video-src="<?= escape(asset($videoSource['src'])) ?>">
                      <source data-src="<?= escape(asset($videoSource['src'])) ?>" type="video/mp4">
                    </video>
                    <?php if ($service['key'] !== 'animations'): ?><button class="service-card__play" type="button" data-service-inline-play aria-label="<?= escape(ui_text('services.play') . ': ' . $itemTitle) ?>"><?= site_icon('play') ?></button><?php endif; ?>
                  </div>
                <?php endif; ?>
              </div>
              <div class="service-card__meta<?= !$isTour ? ' case-v3-gallery__item-meta' : '' ?>">
                <h3><?php if ($hasProject): ?><strong class="service-card__project"><?= escape($captionProject) ?></strong><?php if ($captionName !== ''): ?> — <?php endif; ?><?php endif; ?><?php if ($translatedName): ?><span data-i18n="services.sitePhotomontage"><?= escape($captionName) ?></span><?php else: ?><?= escape($captionName) ?><?php endif; ?></h3>
                <?php if ($isTour): ?>
                  <a class="service-card__action" href="<?= escape($item['src']) ?>" target="_blank" rel="noopener" aria-label="<?= escape(ui_text('services.tourNewTab') . ': ' . $itemTitle) ?>"><?= site_icon('arrow-up-right') ?></a>
                <?php else: ?>
                  <span><?= sprintf('%02d', $itemIndex + 1) ?> — <?= sprintf('%02d', count($service['items'])) ?></span>
                <?php endif; ?>
              </div>
              <?php if ($isTour): ?><p class="service-tour__help"><?= $text('dragTour') ?></p><?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
      <?php if (!$isTour): ?>
        <span class="service-carousel__status" aria-live="polite" aria-atomic="true"><span data-carousel-current>01</span> / <?= sprintf('%02d', count($service['items'])) ?></span>
        <button class="case-v3-gallery__carousel-control case-v3-gallery__carousel-control--next" type="button" data-carousel-next aria-controls="<?= escape($service['id']) ?>-items" aria-label="<?= escape(ui_text('services.next')) ?>" data-i18n-aria="services.next"><?= site_icon('arrow-right', 'case-v3-icon') ?></button>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <p><?= $text('soon') ?></p>
  <?php endif; ?>
</section>
