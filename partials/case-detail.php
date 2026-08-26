<?php

/** @var array $case */
/** @var array $project */

$caseVideos = case_videos((string) $project['slug']);
$heroVideo = $caseVideos[$case['hero']['posterVideo'] ?? ''] ?? null;
$heroPoster = is_array($heroVideo) ? (string) ($heroVideo['poster'] ?? '') : '';
$caseTitle = translated($project['title']);
$renderImage = static function (string $source, string $class, string $sizes, string $reveal = 'default') use ($project): string {
    [$width, $height] = case_image_size($source);
    return responsive_image($source, translated($project['title']) . ' — imagem do projeto', $width, $height, $class, $sizes, false, ['data-case-reveal' => $reveal]);
};
$videoSource = static function (array $video): ?array {
    return case_video_source($video);
};
$renderPreviewVideo = static function (array $video, string $class, string $mode = 'preview'): string {
    $source = case_video_source($video);
    if ($source === null || empty($video['poster'])) {
        return '';
    }
    $loop = !empty($video['loopCandidate']) ? ' loop' : '';
    return sprintf(
        '<video class="%s" data-case-video data-video-mode="%s" data-video-source="%s" poster="%s" width="%d" height="%d" preload="none" muted playsinline%s></video>',
        escape($class),
        escape($mode),
        escape(asset((string) $source['src'])),
        escape(asset((string) $video['poster'])),
        (int) ($source['width'] ?? $video['width'] ?? 1920),
        (int) ($source['height'] ?? $video['height'] ?? 1080),
        $loop
    );
};
$duration = static function (array $video): string {
    $seconds = (int) round((float) ($video['duration'] ?? 0));
    return sprintf('%d:%02d', intdiv($seconds, 60), $seconds % 60);
};
$nextProject = case_next_project((string) $project['slug']);
$planLabels = ['Lazer', 'Tipo 01', 'Tipo 03 / Torres 1 e 2', 'Tipo 03 / Torre 3'];
?>
<main id="conteudo" class="case-v2" data-case-detail data-case-motion="<?= escape((string) ($case['motion'] ?? 'slow')) ?>">
  <section class="case-v2-hero" aria-labelledby="case-title">
    <?php if ($heroPoster !== ''): ?>
      <img class="case-v2-hero__media" src="<?= escape(asset($heroPoster)) ?>" width="<?= (int) ($heroVideo['width'] ?? 3840) ?>" height="<?= (int) ($heroVideo['height'] ?? 2160) ?>" alt="<?= escape(translated($project['media']['hero']['alt'])) ?>" fetchpriority="high" decoding="async">
    <?php else: ?>
      <?= responsive_image($project['media']['hero']['src'], translated($project['media']['hero']['alt']), (int) $project['media']['hero']['width'], (int) $project['media']['hero']['height'], 'case-v2-hero__media', '100vw', true) ?>
    <?php endif; ?>
    <div class="case-v2-hero__shade" aria-hidden="true"></div>
    <div class="case-v2-hero__inner">
      <p class="case-v2-kicker case-v2-hero__label"><?= escape((string) ($case['hero']['label'] ?? 'Improov / Case')) ?></p>
      <h1 id="case-title"><?= escape($caseTitle) ?></h1>
      <div class="case-v2-hero__footer"><span><?= escape(translated($project['location'])) ?></span><a href="#case-intro" aria-label="Ir para o conteúdo do case">↓</a></div>
    </div>
  </section>

  <section id="case-intro" class="case-v2-intro case-v2-shell" data-case-reveal="up">
    <p><?= escape(translated($project['title'])) ?></p>
    <h2><?= escape(translated($project['detail']['subtitle'])) ?></h2>
  </section>

  <nav class="case-v2-nav" aria-label="Capítulos do case" data-case-chapter-navigation>
    <div class="case-v2-nav__inner">
      <?php foreach ($case['chapters'] ?? [] as $chapter): ?>
        <a href="#<?= escape((string) $chapter['id']) ?>" data-case-chapter-link="<?= escape((string) $chapter['id']) ?>"><?= escape((string) $chapter['label']) ?></a>
      <?php endforeach; ?>
    </div>
  </nav>

  <?php foreach ($case['blocks'] ?? [] as $block): ?>
    <?php if (!is_array($block) || empty($block['type'])) { continue; } ?>
    <?php $type = (string) $block['type']; ?>
    <?php if ($type === 'images-chapter'): ?>
      <section id="<?= escape((string) $block['chapter']) ?>" class="case-v2-chapter case-v2-images" data-case-chapter="<?= escape((string) $block['chapter']) ?>">
        <div class="case-v2-shell case-v2-chapter__heading" data-case-reveal="up"><p class="case-v2-kicker">01 / Imagens</p><h2>Imagens</h2></div>
        <section class="case-v2-collection" aria-labelledby="apartamentos-title">
          <div class="case-v2-shell case-v2-collection__header"><h3 id="apartamentos-title">Apartamentos<br>&amp; unidades</h3><span data-case-rail-count>01 — <?= str_pad((string) count($block['apartments'] ?? []), 2, '0', STR_PAD_LEFT) ?></span></div>
          <div class="case-v2-rail case-v2-rail--apartments" data-case-rail tabindex="0" aria-label="Carrossel de apartamentos e unidades. Arraste ou deslize para explorar.">
            <?php foreach ($block['apartments'] ?? [] as $source): ?><figure class="case-v2-rail__item"><?= $renderImage((string) $source, 'case-v2-image', '(max-width: 767px) 88vw, 78vw', 'mask') ?></figure><?php endforeach; ?>
          </div>
        </section>
        <section class="case-v2-editorial case-v2-shell" aria-label="Seleção editorial de imagens">
          <?php foreach ($block['editorial'] ?? [] as $index => $source): ?>
            <figure class="case-v2-editorial__item case-v2-editorial__item--<?= (int) $index ?>"><?= $renderImage((string) $source, 'case-v2-image', $index === 0 ? '(max-width: 767px) 72vw, 40vw' : '100vw', $index === 0 ? 'up' : 'scale') ?></figure>
          <?php endforeach; ?>
        </section>
        <section class="case-v2-collection case-v2-collection--common" aria-labelledby="areas-title">
          <div class="case-v2-shell case-v2-collection__header"><h3 id="areas-title">Fachada<br>&amp; áreas comuns</h3><span data-case-rail-count>01 — <?= str_pad((string) count($block['commonAreas'] ?? []), 2, '0', STR_PAD_LEFT) ?></span></div>
          <div class="case-v2-rail case-v2-rail--common" data-case-rail tabindex="0" aria-label="Carrossel de fachada e áreas comuns. Arraste ou deslize para explorar.">
            <?php foreach ($block['commonAreas'] ?? [] as $source): ?><figure class="case-v2-rail__item"><?= $renderImage((string) $source, 'case-v2-image', '(max-width: 767px) 88vw, 72vw', 'mask') ?></figure><?php endforeach; ?>
          </div>
        </section>
        <section class="case-v2-plans case-v2-shell" data-case-plans aria-labelledby="plans-title">
          <div class="case-v2-plans__header"><p class="case-v2-kicker">Plantas</p><h3 id="plans-title">Explore o espaço.</h3></div>
          <div class="case-v2-plans__controls" role="tablist" aria-label="Selecionar planta">
            <?php foreach ($block['plans'] ?? [] as $index => $source): ?><button type="button" role="tab" aria-selected="<?= $index === 0 ? 'true' : 'false' ?>" data-case-plan="<?= (int) $index ?>">0<?= (int) $index + 1 ?></button><?php endforeach; ?>
          </div>
          <div class="case-v2-plans__stage">
            <?php foreach ($block['plans'] ?? [] as $index => $source): ?><figure class="case-v2-plans__item<?= $index === 0 ? ' is-active' : '' ?>" data-case-plan-panel="<?= (int) $index ?>"><?= $renderImage((string) $source, 'case-v2-plans__image', '(max-width: 767px) 100vw, 70vw') ?><figcaption><?= escape($planLabels[$index] ?? ('Planta ' . ($index + 1))) ?></figcaption></figure><?php endforeach; ?>
          </div>
        </section>
      </section>
    <?php elseif ($type === 'animations-chapter'): ?>
      <section id="<?= escape((string) $block['chapter']) ?>" class="case-v2-chapter case-v2-animations" data-case-chapter="<?= escape((string) $block['chapter']) ?>">
        <div class="case-v2-shell case-v2-chapter__heading" data-case-reveal="up"><p class="case-v2-kicker">02 / Animações</p><h2>Animações</h2></div>
        <div class="case-v2-motion-list case-v2-shell">
          <?php foreach ($block['items'] ?? [] as $index => $item): ?>
            <?php $video = $caseVideos[$item['video'] ?? ''] ?? null; if (!is_array($video)) { continue; } ?>
            <article class="case-v2-motion case-v2-motion--<?= (int) $index ?>" data-case-reveal="<?= $index % 2 === 0 ? 'mask' : 'up' ?>">
              <div class="case-v2-motion__media"><?= $renderPreviewVideo($video, 'case-v2-motion__video', 'animation') ?></div>
              <p>0<?= (int) $index + 1 ?> / 0<?= count($block['items'] ?? []) ?></p>
            </article>
          <?php endforeach; ?>
        </div>
      </section>
    <?php elseif ($type === 'films-chapter'): ?>
      <?php $concept = $caseVideos[$block['concept'] ?? ''] ?? null; $testimonial = $caseVideos[$block['testimonial'] ?? ''] ?? null; ?>
      <section id="<?= escape((string) $block['chapter']) ?>" class="case-v2-chapter case-v2-films" data-case-chapter="<?= escape((string) $block['chapter']) ?>">
        <div class="case-v2-shell case-v2-chapter__heading" data-case-reveal="up"><p class="case-v2-kicker">03 / Filmes</p><h2>Filmes</h2></div>
        <?php if (is_array($concept) && ($source = $videoSource($concept)) !== null): ?>
          <section class="case-v2-film case-v2-film--concept" data-case-reveal="mask"><button class="case-v2-film__trigger" type="button" data-case-film-open data-video-source="<?= escape(asset((string) $source['src'])) ?>" data-video-poster="<?= escape(asset((string) $concept['poster'])) ?>" data-video-title="Concept Film"><img src="<?= escape(asset((string) $concept['poster'])) ?>" width="<?= (int) ($concept['width'] ?? 1920) ?>" height="<?= (int) ($concept['height'] ?? 1080) ?>" alt="Poster do filme conceito ARS Vieiras" loading="lazy" decoding="async"><span aria-hidden="true">▶</span></button><div class="case-v2-film__caption"><span>Concept Film</span><span><?= escape($duration($concept)) ?></span></div></section>
        <?php endif; ?>
        <?php if (is_array($testimonial) && ($source = $videoSource($testimonial)) !== null): ?>
          <section class="case-v2-film case-v2-film--testimonial case-v2-shell" data-case-reveal="up"><p>02 / 02</p><button class="case-v2-film__trigger" type="button" data-case-film-open data-video-source="<?= escape(asset((string) $source['src'])) ?>" data-video-poster="<?= escape(asset((string) $testimonial['poster'])) ?>" data-video-title="Depoimento"><img src="<?= escape(asset((string) $testimonial['poster'])) ?>" width="<?= (int) ($testimonial['width'] ?? 1920) ?>" height="<?= (int) ($testimonial['height'] ?? 1080) ?>" alt="Poster do depoimento ARS Vieiras" loading="lazy" decoding="async"><span aria-hidden="true">▶</span></button><div class="case-v2-film__caption"><span>The vision</span><span>Assistir filme →</span></div></section>
        <?php endif; ?>
      </section>
    <?php elseif ($type === 'pills-chapter'): ?>
      <section id="<?= escape((string) $block['chapter']) ?>" class="case-v2-chapter case-v2-pills" data-case-chapter="<?= escape((string) $block['chapter']) ?>">
        <div class="case-v2-shell case-v2-chapter__heading" data-case-reveal="up"><p class="case-v2-kicker">04 / Pílulas</p><h2>Pílulas</h2></div>
        <div class="case-v2-pills__layout case-v2-shell">
          <?php foreach ($block['items'] ?? [] as $index => $id): ?>
            <?php $video = $caseVideos[$id] ?? null; if (!is_array($video)) { continue; } ?>
            <figure class="case-v2-pill case-v2-pill--<?= (int) $index ?>" data-case-reveal="up"><?= $renderPreviewVideo($video, 'case-v2-pill__video', 'pill') ?></figure>
          <?php endforeach; ?>
        </div>
      </section>
    <?php elseif ($type === 'credits'): ?>
      <section class="case-v2-credits case-v2-shell" data-case-reveal="up"><p class="case-v2-kicker">ARS_VIE</p><dl><div><dt>Cliente</dt><dd><?= escape((string) $project['detail']['info']['client']) ?></dd></div><div><dt>Arquitetura</dt><dd><?= escape((string) $project['detail']['info']['architect']) ?></dd></div><div><dt>Ano</dt><dd><?= escape((string) $project['detail']['info']['year']) ?></dd></div></dl></section>
    <?php elseif ($type === 'next-project'): ?>
      <section class="case-v2-next case-v2-shell" data-case-reveal="up"><p class="case-v2-kicker">Next project</p><?php if ($nextProject !== null): ?><a href="<?= escape(base_url('projetos/' . $nextProject['slug'])) ?>"><?= escape(translated($nextProject['title'])) ?></a><?php else: ?><a href="<?= escape(base_url('projetos')) ?>">Ver projetos</a><?php endif; ?></section>
    <?php endif; ?>
  <?php endforeach; ?>
</main>

<dialog class="case-v2-dialog" data-case-film-dialog aria-label="Reprodutor de filme"><div class="case-v2-dialog__bar"><p data-case-film-title>Film</p><button type="button" data-case-film-close aria-label="Fechar filme">×</button></div><video data-case-film-player controls playsinline preload="none"></video></dialog>
