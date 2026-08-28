<?php

/** @var array $case */
/** @var array $project */

$caseVideos = case_videos((string) $project['slug']);
$caseTitle = translated($project['title']);
$caseText = static function (mixed $value): string {
  if (is_array($value)) {
    return translated($value);
  }
  return is_string($value) ? $value : '';
};
$imageExists = static function (mixed $source): bool {
  if (!is_string($source) || $source === '' || str_contains($source, '..')) {
    return false;
  }
  $mapped = media_map()[$source] ?? null;
  if (is_array($mapped) && !empty($mapped['sources'])) {
    return true;
  }
  return is_file(APP_ROOT . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, ltrim($source, '/')));
};
$environmentMap = [];
foreach ($case['environments'] ?? [] as $environment) {
  if (is_array($environment) && !empty($environment['id'])) {
    $environmentMap[(string) $environment['id']] = $environment;
  }
}
$videoFor = static function (mixed $id) use ($caseVideos): ?array {
  if (!is_string($id) || $id === '') {
    return null;
  }
  $video = $caseVideos[$id] ?? null;
  return is_array($video) && case_video_source($video) !== null ? $video : null;
};
$renderImage = static function (string $source, string $class, string $sizes, string $alt, string $reveal = 'up') use ($caseTitle): string {
  [$width, $height] = case_image_size($source);
  return responsive_image($source, $caseTitle . ' — ' . $alt, $width, $height, $class, $sizes, false, ['data-case-reveal' => $reveal]);
};
$renderVideo = static function (array $video, string $class, string $kind, string $label = ''): string {
  $source = case_video_source($video);
  if ($source === null) {
    return '';
  }
  $loop = !empty($video['loopCandidate']) ? ' loop' : '';
  return sprintf(
    '<video class="%s" data-case-video data-case-media-kind="%s" data-case-video-source="%s" poster="%s" width="%d" height="%d" preload="none" muted playsinline%s%s></video>',
    escape($class),
    escape($kind),
    escape(asset((string) $source['src'])),
    escape(asset((string) ($video['poster'] ?? ''))),
    (int) ($source['width'] ?? $video['width'] ?? 1920),
    (int) ($source['height'] ?? $video['height'] ?? 1080),
    $loop,
    $label !== '' ? ' aria-label="' . escape($label) . '"' : ''
  );
};
$sectionHasContent = static function (array $section) use ($environmentMap, $imageExists, $videoFor): bool {
  $type = (string) ($section['type'] ?? '');
  if ($type === 'gallery') {
    $groups = $section['groups'] ?? [$section];
    foreach ($groups as $group) {
      if (!is_array($group)) {
        continue;
      }
      foreach ($group['items'] ?? [] as $item) {
        if (!is_array($item)) {
          continue;
        }
        if ($imageExists($item['src'] ?? null)) {
          return true;
        }
        foreach ($item['images'] ?? [] as $source) {
          if ($imageExists($source)) {
            return true;
          }
        }
        foreach ($item['items'] ?? [] as $image) {
          if (is_array($image) && $imageExists($image['src'] ?? null)) {
            return true;
          }
        }
      }
    }
    return false;
  }
  if ($type === 'stillMotion') {
    $environment = $environmentMap[(string) ($section['environment'] ?? '')] ?? null;
    return is_array($environment) && $imageExists($environment['still'] ?? null);
  }
  if ($type === 'motion') {
    foreach ($section['items'] ?? [] as $id) {
      if ($videoFor($id) !== null) {
        return true;
      }
    }
    return false;
  }
  if ($type === 'animations') {
    foreach ($section['steps'] ?? [] as $step) {
      if (!is_array($step)) {
        continue;
      }
      foreach ($step['items'] ?? [] as $item) {
        $mediaId = is_array($item) ? ($item['mediaId'] ?? $item['id'] ?? null) : $item;
        if ($videoFor($mediaId) !== null) {
          return true;
        }
      }
    }
    return false;
  }
  if ($type === 'floorplans') {
    foreach ($section['items'] ?? [] as $item) {
      if (is_array($item) && $imageExists($item['src'] ?? null)) {
        return true;
      }
    }
    return false;
  }
  if ($type === 'moments') {
    foreach ($section['items'] ?? [] as $environmentId) {
      $environment = $environmentMap[(string) $environmentId] ?? null;
      if (is_array($environment) && $videoFor($environment['pill'] ?? null) !== null) {
        return true;
      }
    }
    return false;
  }
  if ($type === 'closing') {
    return true;
  }
  if ($type === 'film') {
    $filmItems = $section['videos'] ?? [];
    if (!is_array($filmItems) || $filmItems === []) {
      $filmItems = [['video' => $section['video'] ?? null]];
    }
    foreach ($filmItems as $item) {
      $mediaId = is_array($item) ? ($item['video'] ?? $item['mediaId'] ?? null) : $item;
      if ($videoFor($mediaId) !== null) {
        return true;
      }
    }
  }
  return false;
};
$sections = [];
foreach ($case['sections'] ?? [] as $section) {
  if (is_array($section) && $sectionHasContent($section)) {
    $sections[] = $section;
  }
}
$heroVideo = $caseVideos[(string) ($case['hero']['video'] ?? $case['hero']['posterVideo'] ?? '')] ?? null;
$heroSource = is_array($heroVideo) ? case_video_source($heroVideo) : null;
$heroPoster = is_array($heroVideo) ? (string) ($heroVideo['poster'] ?? '') : '';
$heroMedia = $heroPoster !== '' ? $heroPoster : (string) ($project['media']['hero']['src'] ?? '');
$nextRule = is_array($case['nextProject'] ?? null) ? $case['nextProject'] : [];
$nextProject = case_next_project((string) $project['slug'], $nextRule);
?>
<main id="conteudo" class="case-v3" data-case-detail data-case-motion="<?= escape((string) ($case['motion'] ?? 'slow')) ?>">
  <section id="case-hero" class="case-v3-hero" aria-labelledby="case-title" data-case-chapter="case-hero">
    <?php if ($heroSource !== null): ?>
      <video class="case-v3-hero__media" data-case-hero-video muted playsinline loop preload="auto" poster="<?= escape(asset($heroMedia)) ?>" width="<?= (int) ($heroSource['width'] ?? $heroVideo['width'] ?? 3840) ?>" height="<?= (int) ($heroSource['height'] ?? $heroVideo['height'] ?? 2160) ?>">
        <source src="<?= escape(asset((string) $heroSource['src'])) ?>" type="video/mp4">
      </video>
    <?php elseif ($heroMedia !== ''): ?>
      <?= responsive_image($heroMedia, translated($project['media']['hero']['alt']), (int) $project['media']['hero']['width'], (int) $project['media']['hero']['height'], 'case-v3-hero__media', '100vw', true) ?>
    <?php endif; ?>
    <div class="case-v3-hero__shade" aria-hidden="true"></div>
    <div class="case-v3-shell case-v3-hero__inner">
      <p class="case-v3-kicker"><?= escape($caseText($case['hero']['label'] ?? 'Improov / Case')) ?></p>
      <div class="case-v3-hero__title">
        <h1 id="case-title"><?= escape($caseTitle) ?></h1>
        <div class="case-v3-hero__facts">
          <span><?= escape(translated($project['location'])) ?></span>
          <?php if (!empty($project['detail']['info']['client'])): ?><span><?= escape((string) $project['detail']['info']['client']) ?></span><?php endif; ?>
          <?php if (!empty($project['detail']['info']['year'])): ?><span><?= escape((string) $project['detail']['info']['year']) ?></span><?php endif; ?>
        </div>
      </div>
      <div class="case-v3-hero__footer">
        <?php $heroFooter = $caseText($case['heroFooter'] ?? '');
        if ($heroFooter === '') {
          $heroFooter = implode(' · ', array_filter(array_map($caseText, $case['services'] ?? [])));
        } ?>
        <?php if ($heroFooter !== ''): ?><p><?= escape($heroFooter) ?></p><?php endif; ?>
        <a href="#case-imagens" aria-label="Ir para as imagens do case"><?= site_icon('arrow-down', 'case-v3-icon') ?></a>
      </div>
    </div>
  </section>

  <?php if ($sections !== []): ?>
    <nav class="case-v3-nav" aria-label="Capítulos do case" data-case-chapter-navigation>
      <div class="case-v3-shell case-v3-nav__inner">
        <a href="#case-hero" data-case-chapter-link="case-hero"><span>01</span>Hero</a>
        <?php foreach ($sections as $index => $section): ?>
          <a href="#case-<?= escape((string) $section['id']) ?>" data-case-chapter-link="case-<?= escape((string) $section['id']) ?>"><span><?= str_pad((string) ($index + 2), 2, '0', STR_PAD_LEFT) ?></span><?= escape($caseText($section['label'] ?? $section['type'])) ?></a>
        <?php endforeach; ?>
      </div>
    </nav>
  <?php endif; ?>

  <?php foreach ($sections as $index => $section): ?>
    <?php $type = (string) $section['type'];
    $chapterId = 'case-' . (string) $section['id'];
    $chapterLabel = $caseText($section['label'] ?? $type);
    $number = str_pad((string) ($index + 2), 2, '0', STR_PAD_LEFT); ?>
    <?php if ($type === 'gallery'): ?>
      <section id="<?= escape($chapterId) ?>" class="case-v3-section case-v3-gallery" data-case-chapter="<?= escape($chapterId) ?>">
        <header class="case-v3-section__heading case-v3-shell" data-case-reveal="up">
          <p class="case-v3-kicker"><?= escape($number . ' / ' . $chapterLabel) ?></p>
          <h2><?= escape($caseText($section['title'] ?? $chapterLabel)) ?></h2>
        </header>
        <?php foreach ($section['groups'] ?? [] as $group): ?>
          <?php if (!is_array($group)) {
            continue;
          }
          $groupType = (string) ($group['type'] ?? 'editorial');
          $groupTitle = $caseText($group['title'] ?? ''); ?>
          <section class="case-v3-gallery__group case-v3-gallery__group--<?= escape($groupType) ?>" aria-label="<?= escape($groupTitle) ?>">
            <?php if ($groupTitle !== ''): ?><div class="case-v3-gallery__group-heading case-v3-shell">
                <p class="case-v3-kicker"><?= escape($groupTitle) ?></p>
              </div><?php endif; ?>
            <?php if ($groupType === 'immersiveRail'): ?>
              <?php $slides = array_values(array_filter($group['items'] ?? [], static fn($item): bool => is_array($item) && $imageExists($item['src'] ?? null)));
              $firstSlide = $slides[0] ?? []; ?>
              <div class="case-v3-gallery__carousel" data-case-gallery-carousel>
                <button class="case-v3-gallery__carousel-control case-v3-gallery__carousel-control--previous" type="button" data-case-gallery-previous aria-label="Imagem anterior"><?= site_icon('arrow-left', 'case-v3-icon') ?></button>
                <div class="case-v3-gallery__rail" data-case-gallery-rail tabindex="0" aria-label="<?= escape($groupTitle) ?>. Use as setas ou deslize para explorar.">
                  <?php foreach ($slides as $slideIndex => $item): $slideLabel = $caseText($item['label'] ?? $groupTitle); ?><figure class="case-v3-gallery__rail-item" data-case-gallery-slide data-gallery-label="<?= escape($slideLabel) ?>" role="group" aria-roledescription="slide" aria-label="<?= escape(($slideIndex + 1) . ' de ' . count($slides) . ': ' . $slideLabel) ?>"><?= $renderImage((string) $item['src'], 'case-v3-image', '(max-width: 767px) 94vw, 70vw', $slideLabel) ?><figcaption><?= escape($slideLabel) ?></figcaption>
                    </figure><?php endforeach; ?>
                </div>
                <button class="case-v3-gallery__carousel-control case-v3-gallery__carousel-control--next" type="button" data-case-gallery-next aria-label="Próxima imagem"><?= site_icon('arrow-right', 'case-v3-icon') ?></button>
                <div class="case-v3-gallery__carousel-meta case-v3-shell">
                  <p data-case-gallery-label><?= escape($caseText($firstSlide['label'] ?? $groupTitle)) ?></p>
                  <p data-case-gallery-count aria-live="polite">01 — <?= str_pad((string) count($slides), 2, '0', STR_PAD_LEFT) ?></p>
                </div>
              </div>
            <?php else: ?>
              <div class="case-v3-gallery__items case-v3-shell">
                <?php foreach ($group['items'] ?? [] as $composition): ?>
                  <?php if (!is_array($composition)) {
                    continue;
                  }
                  $layout = (string) ($composition['layout'] ?? 'impact'); ?>
                  <div class="case-v3-gallery__composition case-v3-gallery__composition--<?= escape($layout) ?>" data-case-reveal="up">
                    <?php foreach ($composition['items'] ?? [] as $image): ?>
                      <?php if (!is_array($image) || !$imageExists($image['src'] ?? null)) {
                        continue;
                      }
                      $imageLabel = $caseText($image['label'] ?? $groupTitle); ?>
                      <figure class="case-v3-gallery__image"><?= $renderImage((string) $image['src'], 'case-v3-image', '(max-width: 767px) 100vw, 55vw', $imageLabel) ?><figcaption><?= escape($imageLabel) ?></figcaption>
                      </figure>
                    <?php endforeach; ?>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </section>
        <?php endforeach; ?>
      </section>
    <?php elseif ($type === 'stillMotion'): ?>
      <?php $environment = $environmentMap[(string) ($section['environment'] ?? '')];
      $motionVideo = $videoFor($environment['motion'] ?? null); ?>
      <section id="<?= escape($chapterId) ?>" class="case-v3-section case-v3-still-motion" data-case-chapter="<?= escape($chapterId) ?>">
        <header class="case-v3-section__heading case-v3-shell" data-case-reveal="up">
          <p class="case-v3-kicker"><?= escape($number . ' / ' . $chapterLabel) ?></p>
          <h2><?= escape($caseText($environment['label'] ?? $chapterLabel)) ?></h2>
        </header>
        <div class="case-v3-shell">
          <?php if ($motionVideo !== null): ?>
            <button class="case-v3-still-motion__stage" type="button" data-case-still-motion aria-pressed="false" aria-label="Reproduzir animação de <?= escape($caseText($environment['label'] ?? $chapterLabel)) ?>">
              <?= $renderImage((string) $environment['still'], 'case-v3-still-motion__still', '100vw', $caseText($environment['label'] ?? $chapterLabel), 'mask') ?>
              <?= $renderVideo($motionVideo, 'case-v3-still-motion__video', 'still-motion', 'Animação de ' . $caseText($environment['label'] ?? $chapterLabel)) ?>
              <span class="case-v3-still-motion__hint"><span>View motion</span><?= site_icon('arrow-up-right', 'case-v3-icon') ?></span>
            </button>
          <?php else: ?>
            <figure class="case-v3-still-motion__static"><?= $renderImage((string) $environment['still'], 'case-v3-image', '100vw', $caseText($environment['label'] ?? $chapterLabel), 'mask') ?></figure>
          <?php endif; ?>
        </div>
      </section>
    <?php elseif ($type === 'animations'): ?>
      <?php
      $animationSteps = [];
      foreach ($section['steps'] ?? [] as $step) {
        if (!is_array($step)) {
          continue;
        }
        $stepItems = [];
        foreach ($step['items'] ?? [] as $item) {
          if (!is_array($item)) {
            $item = ['mediaId' => $item];
          }
          $video = $videoFor($item['mediaId'] ?? $item['id'] ?? null);
          if ($video === null) {
            continue;
          }
          $stepItems[] = [
            'video' => $video,
            'label' => $caseText($item['label'] ?? 'Animação'),
          ];
        }
        if ($stepItems !== []) {
          $layout = (string) ($step['layout'] ?? 'single');
          if (!in_array($layout, ['single', 'double'], true)) {
            $layout = count($stepItems) > 1 ? 'double' : 'single';
          }
          $animationSteps[] = [
            'layout' => $layout,
            'items' => $stepItems,
          ];
        }
      }
      $animationStepCount = count($animationSteps);
      $animationScrollExtra = $animationStepCount > 0 ? ($animationStepCount + 1) * 60 : 0;
      ?>
      <section id="<?= escape($chapterId) ?>" class="case-v3-section case-v3-animations" data-case-chapter="<?= escape($chapterId) ?>" data-case-animations style="--case-animation-scroll-extra: <?= (int) $animationScrollExtra ?>vh">
        <header class="case-v3-animations__header case-v3-shell" data-case-reveal="up">
          <p class="case-v3-kicker"><?= escape($number . ' / ' . $chapterLabel) ?></p>
          <div class="case-v3-animations__title-row">
            <h2><?= escape($caseText($section['title'] ?? $chapterLabel)) ?></h2>
            <p class="case-v3-animations__counter" data-case-animation-counter>01 — <?= str_pad((string) max(1, $animationStepCount), 2, '0', STR_PAD_LEFT) ?></p>
          </div>
        </header>
        <div class="case-v3-animations__scroll">
          <div class="case-v3-animations__sticky">
            <div class="case-v3-animations__stage case-v3-shell" data-case-animation-stage>
              <?php foreach ($animationSteps as $stepIndex => $animationStep): ?>
                <article class="case-v3-animation-step case-v3-animation-step--<?= escape($animationStep['layout']) ?><?= $stepIndex === 0 ? ' is-active' : '' ?>" data-case-animation-step data-case-animation-index="<?= (int) $stepIndex ?>" data-case-animation-layout="<?= escape($animationStep['layout']) ?>" aria-hidden="<?= $stepIndex === 0 ? 'false' : 'true' ?>">
                  <div class="case-v3-animation-step__items">
                    <?php foreach ($animationStep['items'] as $item): ?>
                      <figure class="case-v3-animation-card">
                        <?= $renderVideo($item['video'], 'case-v3-animation-card__video', 'animation', $item['label']) ?>
                        <figcaption><?= escape($item['label']) ?></figcaption>
                      </figure>
                    <?php endforeach; ?>
                  </div>
                </article>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </section>
    <?php elseif ($type === 'motion'): ?>
      <?php $motionItems = array_values(array_filter(array_map($videoFor, $section['items'] ?? []))); ?>
      <section id="<?= escape($chapterId) ?>" class="case-v3-section case-v3-motion" data-case-chapter="<?= escape($chapterId) ?>">
        <header class="case-v3-section__heading case-v3-shell" data-case-reveal="up">
          <p class="case-v3-kicker"><?= escape($number . ' / ' . $chapterLabel) ?></p>
          <h2><?= escape($caseText($section['title'] ?? $chapterLabel)) ?></h2>
        </header>
        <div class="case-v3-shell case-v3-motion__list">
          <?php foreach ($motionItems as $motionIndex => $video): ?><article class="case-v3-motion__item case-v3-motion__item--<?= (int) ($motionIndex % 2) ?>" data-case-reveal="up">
              <div><?= $renderVideo($video, 'case-v3-motion__video', 'motion', 'Animação ' . ($motionIndex + 1)) ?></div>
              <p><?= str_pad((string) ($motionIndex + 1), 2, '0', STR_PAD_LEFT) ?> / <?= str_pad((string) count($motionItems), 2, '0', STR_PAD_LEFT) ?></p>
            </article><?php endforeach; ?>
        </div>
      </section>
    <?php elseif ($type === 'floorplans'): ?>
      <?php $plans = array_values(array_filter($section['items'] ?? [], static fn($item): bool => is_array($item) && $imageExists($item['src'] ?? null))); ?>
      <?php $floorplanScrollExtra = count($plans) > 0 ? (count($plans) + 1) * 60 : 0; ?>
      <section id="<?= escape($chapterId) ?>" class="case-v3-section case-v3-floorplans" data-case-chapter="<?= escape($chapterId) ?>" data-case-plans data-case-focus-scroll style="--case-focus-scroll-extra: <?= (int) $floorplanScrollExtra ?>vh">
        <header class="case-v3-floorplans__heading case-v3-shell" data-case-reveal="up">
          <p class="case-v3-kicker"><?= escape($number . ' / ' . $chapterLabel) ?></p>
          <div>
            <p class="case-v3-kicker"><?= escape($caseText($section['eyebrow'] ?? 'Plantas')) ?></p>
            <h2><?= escape($caseText($section['title'] ?? $chapterLabel)) ?></h2>
          </div>
        </header>
        <div class="case-v3-floorplans__scroll" data-case-focus-track>
          <div class="case-v3-floorplans__sticky" data-case-focus-sticky>
            <div class="case-v3-floorplans__viewer case-v3-shell" data-case-focus-stage>
              <div class="case-v3-floorplans__tabs" role="tablist" aria-label="Selecionar planta">
                <?php foreach ($plans as $planIndex => $plan): $planId = $chapterId . '-' . (string) $plan['id']; ?><button id="<?= escape($planId) ?>-tab" type="button" role="tab" aria-selected="<?= $planIndex === 0 ? 'true' : 'false' ?>" aria-controls="<?= escape($planId) ?>-panel" tabindex="<?= $planIndex === 0 ? '0' : '-1' ?>" data-case-plan="<?= (int) $planIndex ?>"><?= escape($caseText($plan['label'] ?? ('Planta ' . ($planIndex + 1)))) ?></button><?php endforeach; ?>
              </div>
              <div class="case-v3-floorplans__stage">
                <?php foreach ($plans as $planIndex => $plan): $planId = $chapterId . '-' . (string) $plan['id']; ?><div id="<?= escape($planId) ?>-panel" class="case-v3-floorplans__panel<?= $planIndex === 0 ? ' is-active' : '' ?>" role="tabpanel" aria-labelledby="<?= escape($planId) ?>-tab" tabindex="0" data-case-plan-panel="<?= (int) $planIndex ?>" data-case-focus-step aria-hidden="<?= $planIndex === 0 ? 'false' : 'true' ?>" <?= $planIndex === 0 ? '' : ' hidden' ?>>
                    <figure><?= $renderImage((string) $plan['src'], 'case-v3-floorplans__image', '(max-width: 767px) 100vw, 76vw', $caseText($plan['label'] ?? 'Planta')) ?><figcaption><?= escape($caseText($plan['label'] ?? 'Planta')) ?></figcaption>
                    </figure>
                  </div><?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      </section>
    <?php elseif ($type === 'moments'): ?>
      <?php $momentItems = [];
      foreach ($section['items'] ?? [] as $environmentId) {
        $environment = $environmentMap[(string) $environmentId] ?? null;
        $video = is_array($environment) ? $videoFor($environment['pill'] ?? null) : null;
        if ($video !== null) {
          $momentItems[] = ['environment' => $environment, 'video' => $video];
        }
      } ?>
      <section id="<?= escape($chapterId) ?>" class="case-v3-section case-v3-moments" data-case-chapter="<?= escape($chapterId) ?>">
        <header class="case-v3-section__heading case-v3-shell" data-case-reveal="up">
          <p class="case-v3-kicker"><?= escape($number . ' / ' . $chapterLabel) ?></p>
          <h2><?= escape($caseText($section['title'] ?? $chapterLabel)) ?></h2>
        </header>
        <div class="case-v3-moments__rail" data-case-moments tabindex="0" aria-label="Momentos em vídeo. Deslize horizontalmente para explorar.">
          <?php foreach ($momentItems as $momentIndex => $item): ?><figure class="case-v3-moment case-v3-moment--<?= (int) $momentIndex ?>"><?= $renderVideo($item['video'], 'case-v3-moment__video', 'moment', $caseText($item['environment']['label'] ?? 'Moment')) ?><figcaption><?= escape($caseText($item['environment']['label'] ?? 'Moment')) ?></figcaption>
            </figure><?php endforeach; ?>
        </div>
      </section>
    <?php elseif ($type === 'film'): ?>
      <?php
      $filmItems = $section['videos'] ?? [];
      if (!is_array($filmItems) || $filmItems === []) {
        $filmItems = [['video' => $section['video'] ?? null]];
      }
      $films = [];
      foreach ($filmItems as $filmItem) {
        if (!is_array($filmItem)) {
          $filmItem = ['video' => $filmItem];
        }
        $film = $videoFor($filmItem['video'] ?? $filmItem['mediaId'] ?? null);
        $source = $film !== null ? case_video_source($film) : null;
        if ($film === null || $source === null) {
          continue;
        }
        $films[] = [
          'video' => $film,
          'source' => $source,
          'label' => $caseText($filmItem['label'] ?? $filmItem['eyebrow'] ?? $section['eyebrow'] ?? 'Filme'),
          'title' => $caseText($filmItem['title'] ?? $filmItem['label'] ?? $filmItem['eyebrow'] ?? $section['eyebrow'] ?? 'Filme'),
        ];
      }
      ?>
      <?php if ($films !== []): ?>
        <?php $filmScrollExtra = (count($films) + 1) * 60; ?>
        <section id="<?= escape($chapterId) ?>" class="case-v3-section case-v3-film" data-case-chapter="<?= escape($chapterId) ?>" data-case-focus-scroll style="--case-focus-scroll-extra: <?= (int) $filmScrollExtra ?>vh">
          <header class="case-v3-film__heading case-v3-shell" data-case-reveal="up">
            <p class="case-v3-kicker"><?= escape($number . ' / ' . $chapterLabel) ?></p>
          </header>
          <div class="case-v3-film__scroll" data-case-focus-track>
            <div class="case-v3-film__sticky" data-case-focus-sticky>
              <div class="case-v3-film__medias case-v3-film__medias--<?= count($films) > 1 ? 'multiple' : 'single' ?> case-v3-shell" data-case-focus-stage data-case-reveal="mask">
                <?php foreach ($films as $filmIndex => $film): ?><div class="case-v3-film__media case-v3-film__media--<?= $filmIndex === 0 ? 'primary' : 'secondary' ?><?= $filmIndex === 0 ? ' is-active' : '' ?>" data-case-focus-step aria-hidden="<?= $filmIndex === 0 ? 'false' : 'true' ?>">
                    <p class="case-v3-film__label"><?= escape($film['label']) ?></p><button class="case-v3-film__trigger" type="button" data-case-film-open aria-label="Assistir <?= escape($film['label']) ?>" data-video-source="<?= escape(asset((string) $film['source']['src'])) ?>" data-video-poster="<?= escape(asset((string) ($film['video']['poster'] ?? ''))) ?>" data-video-title="<?= escape($film['title']) ?>"><img src="<?= escape(asset((string) ($film['video']['poster'] ?? ''))) ?>" width="<?= (int) ($film['video']['width'] ?? 1920) ?>" height="<?= (int) ($film['video']['height'] ?? 1080) ?>" alt="Poster de <?= escape($film['label']) ?> — <?= escape($caseTitle) ?>" loading="lazy" decoding="async"><span class="case-v3-film__play" aria-hidden="true"><?= site_icon('play', 'case-v3-icon') ?></span><span class="sr-only">Assistir <?= escape($film['label']) ?></span></button>
                  </div><?php endforeach; ?>
              </div>
            </div>
          </div>
        </section>
      <?php endif; ?>
    <?php elseif ($type === 'closing'): ?>
      <section id="<?= escape($chapterId) ?>" class="case-v3-section case-v3-closing" data-case-chapter="<?= escape($chapterId) ?>">
        <header class="case-v3-closing__heading case-v3-shell" data-case-reveal="up">
          <p class="case-v3-kicker"><?= escape($number . ' / ' . $chapterLabel) ?></p>
        </header>
        <?php if (!empty($case['credits']['items'])): ?>
          <div class="case-v3-credits case-v3-shell" data-case-reveal="up">
            <dl><?php foreach ($case['credits']['items'] as $credit): if (!is_array($credit) || $caseText($credit['value'] ?? '') === '') {
                    continue;
                  } ?><div>
                  <dt><?= escape($caseText($credit['label'] ?? '')) ?></dt>
                  <dd><?= escape($caseText($credit['value'])) ?></dd>
                </div><?php endforeach; ?></dl>
          </div>
        <?php endif; ?>
        <div class="case-v3-next case-v3-shell" data-case-reveal="up">
          <p class="case-v3-kicker"><?= escape($caseText($nextRule['eyebrow'] ?? 'Próximo projeto')) ?></p>
          <?php if ($nextProject !== null): ?>
            <?php $nextHero = $nextProject['media']['hero']; ?><a class="case-v3-next__project" href="<?= escape(base_url('projetos/' . $nextProject['slug'])) ?>"><span class="case-v3-next__media"><?= responsive_image($nextHero['src'], translated($nextHero['alt']), (int) $nextHero['width'], (int) $nextHero['height'], 'case-v3-next__image', '100vw') ?></span><span class="case-v3-next__copy"><strong><?= escape(translated($nextProject['title'])) ?><?= site_icon('arrow-up-right', 'case-v3-next__project-icon') ?></strong><small><?= escape(translated($nextProject['location'])) ?></small></span></a>
          <?php endif; ?>
          <a class="case-v3-next__all" href="<?= escape(base_url('projetos')) ?>"><?= escape($caseText($nextRule['allProjectsLabel'] ?? 'Ver todos os projetos')) ?> <?= site_icon('arrow-right', 'case-v3-icon') ?></a>
        </div>
      </section>
    <?php endif; ?>
  <?php endforeach; ?>
</main>

<dialog class="case-v3-dialog" data-case-film-dialog aria-label="Reprodutor de filme">
  <div class="case-v3-dialog__bar">
    <p data-case-film-title>Filme</p><button type="button" data-case-film-close aria-label="Fechar filme"><?= site_icon('close', 'case-v3-icon') ?></button>
  </div><video data-case-film-player controls playsinline preload="none"></video>
</dialog>