<main id="conteudo">
  <section class="hero hero--about">
    <div class="hero__media"><?php $aboutHero = find_video('site', 'fg-talls-f-1'); ?><?php if ($aboutHero !== null): ?><?= lazy_video($aboutHero, 'hero__image', true, ['data-i18n-alt' => 'about.heroAlt', 'aria-label' => 'Filme do estúdio Improov']) ?><?php else: ?><?= responsive_image('assets/BHE_INF_Coworking_EF.jpg', 'Estúdio criativo da Improov em Blumenau', 1920, 1280, 'hero__image', '100vw', true, ['data-i18n-alt' => 'about.heroAlt']) ?><?php endif; ?></div>
    <div class="hero__shade"></div>
    <div class="hero__content container">
      <p class="eyebrow" data-i18n="about.eyebrow"><?= escape(ui_text('about.eyebrow')) ?></p>
      <h1 data-i18n="about.title"><?= escape(ui_text('about.title')) ?></h1>
      <p data-i18n="about.intro"><?= escape(ui_text('about.intro')) ?></p>
    </div>
  </section>
  <section class="manifesto section container">
    <div class="editorial-copy"><span class="eyebrow" data-i18n="about.manifestoEyebrow">Nossa essência</span>
      <h2 data-i18n="about.manifestoTitle"><?= escape(ui_text('about.manifestoTitle')) ?></h2>
      <p data-i18n="about.manifestoP1"><?= escape(ui_text('about.manifestoP1')) ?></p>
      <p data-i18n="about.manifestoP2"><?= escape(ui_text('about.manifestoP2')) ?></p>
      <p data-i18n="about.manifestoP3"><?= escape(ui_text('about.manifestoP3')) ?></p>
      <p data-i18n="about.manifestoP4"><?= escape(ui_text('about.manifestoP4')) ?></p>
    </div>
    <div class="editorial-media"><?php $improovVideo = asset('assets/senna-tower.mp4'); ?><video class="editorial-media__video" width="1920" height="1080" muted loop playsinline preload="none" data-lazy-video data-video-src="<?= escape($improovVideo) ?>" aria-label="Improov"></video></div>
  </section>
  <section class="manifesto manifesto--reverse section container">
    <div class="editorial-copy">

      <!-- <p data-i18n="about.heartmadeDetails">
        Cada detalhe é pensado para comunicar com verdade. Cada enquadramento, cada luz, cada movimento e cada narrativa existem para despertar sentimentos.
      </p> -->

      <p id="pre-about-heartmade" data-i18n="about.heartmadeLead"><?= escape(ui_text('about.heartmadeLead')) ?></p>

      <h2 class="about-heartmade-title">
        <img
          class="about-heartmade-logo"
          src="<?= escape(asset('assets/IMPROOV_heartmade(black).gif')) ?>"
          alt="Heartmade"
          data-i18n-alt="about.heartmadeTitle">
      </h2>

      <p data-i18n="about.heartmadeBelief"><?= escape(ui_text('about.heartmadeBelief')) ?></p>

      <p data-i18n="about.heartmadeTeam"><?= escape(ui_text('about.heartmadeTeam')) ?></p>

      <p><span data-i18n="about.heartmadeExperience"><?= escape(ui_text('about.heartmadeExperience')) ?></span> <span data-i18n="about.heartmadeClosing"><?= escape(ui_text('about.heartmadeClosing')) ?></span></p>

      <p data-i18n="about.heartmadeFinal"><?= escape(ui_text('about.heartmadeFinal')) ?></p>
    </div>
    <div class="editorial-media"><?php $heartmadeVideo = find_video('site', '19-ars-vie-piscina-detalhe'); ?><?php if ($heartmadeVideo !== null): ?><?= lazy_video($heartmadeVideo, 'editorial-media__video', false, ['aria-label' => 'Heartmade']) ?><?php else: ?><?= responsive_image('assets/BHE_INF_Piscina_EF.jpg', 'Arquitetura residencial em meio à paisagem', 1920, 1280, '', '(max-width: 767px) 100vw, 60vw') ?><?php endif; ?></div>
  </section>
  <section class="studio section container">
    <div class="section-heading"><span class="eyebrow" data-i18n="about.studioEyebrow"><?= escape(ui_text('about.studioEyebrow')) ?></span>
      <h2 data-i18n="about.studioTitle"><?= escape(ui_text('about.studioTitle')) ?></h2>
      <p data-i18n="about.studioText"><?= escape(ui_text('about.studioText')) ?></p>
    </div>
    <div class="studio-gallery"><?= responsive_image('DSCF0764.JPG', 'Área de trabalho do estúdio', 1920, 1280, 'studio-gallery__wide', '(max-width: 767px) 100vw, 66vw') ?><?= responsive_image('DSCF5349.JPG', 'Espaço de convivência do estúdio', 1920, 1280, '', '(max-width: 767px) 100vw, 33vw') ?><?= responsive_image('DSCF5360.JPG', 'Detalhes de materiais e iluminação', 1920, 1280, '', '(max-width: 767px) 100vw, 33vw') ?><?= responsive_image('DSCF5369.JPG', 'Ambiente interno contemporâneo', 1920, 1280, '', '(max-width: 767px) 100vw, 33vw') ?></div>
  </section>
  <section class="location-section section container">
    <div class="location-info"><span class="eyebrow" data-i18n="about.locationEyebrow">Localização</span>
      <h2>Blumenau,<br>Santa Catarina</h2>
      <p data-i18n="contact.address"><?= escape($site['address']) ?></p>
      <p data-i18n="contact.hours"><?= escape($site['hours']) ?></p><a class="text-link" href="https://www.google.com/maps/search/?api=1&amp;query=Rua%20Bahia%2C%20988%20-%20Bairro%20do%20Salto%2C%20Blumenau%20-%20SC" target="_blank" rel="noopener"><span data-i18n="about.directions">Como chegar</span><span aria-hidden="true">→</span></a>
    </div>
    <div class="location-visual"><iframe title="Localização da Improov" src="https://maps.google.com/maps?q=Rua%20Bahia%2C%20988%20-%20Bairro%20do%20Salto%2C%20Blumenau%20-%20SC&amp;output=embed" allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe></div>
  </section>
  <div class="container"><?php require APP_ROOT . '/partials/closing-cta.php'; ?></div>
</main>
