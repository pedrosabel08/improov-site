<main id="conteudo">
  <section class="hero hero--about">
    <div class="hero__media"><?= responsive_image('assets/BHE_INF_Coworking_EF.jpg', 'Estúdio criativo da Improov em Blumenau', 1920, 1280, 'hero__image', '100vw', true, ['data-i18n-alt' => 'about.heroAlt']) ?></div>
    <div class="hero__shade"></div>
    <div class="hero__content container">
      <p class="eyebrow" data-i18n="about.eyebrow">Quem Somos</p>
      <h1 data-i18n="about.title">Imaginação com intenção. Imagem com propósito.</h1>
      <p data-i18n="about.intro">Unimos tecnologia, sensibilidade e direção de arte para transformar ideias em experiências visuais.</p>
    </div>
  </section>
  <section class="manifesto section container">
    <div class="editorial-copy"><span class="eyebrow" data-i18n="about.manifestoEyebrow">Manifesto</span>
      <h2 data-i18n="about.manifestoTitle">Cada projeto carrega uma história, um lugar e um propósito únicos.</h2>
      <p data-i18n="about.manifestoP1">A Improov existe para traduzir tudo isso em imagens que despertam emoção e valorizam o essencial.</p>
      <p data-i18n="about.manifestoP2">Acreditamos que a melhor imagem não mostra apenas o que se vê, mas o que se sente.</p>
    </div>
    <div class="editorial-media"><?= responsive_image('assets/BHE_INF_Piscina_EF.jpg', 'Arquitetura residencial em meio à paisagem', 1920, 1280, '', '(max-width: 767px) 100vw, 60vw') ?></div>
  </section>
  <section class="studio section container">
    <div class="section-heading"><span class="eyebrow" data-i18n="about.studioEyebrow">Nosso Estúdio</span>
      <h2 data-i18n="about.studioTitle">O lugar onde as ideias ganham vida.</h2>
      <p data-i18n="about.studioText">Um ambiente acolhedor, técnico e criativo, onde colaboração e atenção aos detalhes se encontram todos os dias.</p>
    </div>
    <div class="studio-gallery"><?= responsive_image('assets/BHE_INF_Coworking_EF.jpg', 'Área de trabalho do estúdio', 1920, 1280, 'studio-gallery__wide', '(max-width: 767px) 100vw, 66vw') ?><?= responsive_image('assets/BHE_INF_Living_Diferenciado_EF.jpg', 'Espaço de convivência do estúdio', 1920, 1280, '', '(max-width: 767px) 100vw, 33vw') ?><?= responsive_image('assets/BHE_INF_Adega_EF.jpg', 'Detalhes de materiais e iluminação', 1920, 1280, '', '(max-width: 767px) 100vw, 33vw') ?><?= responsive_image('assets/BHE_INF_Fireplace_EF.jpg', 'Ambiente interno contemporâneo', 1920, 1280, '', '(max-width: 767px) 100vw, 33vw') ?></div>
  </section>
  <section class="location-section section container">
    <div><span class="eyebrow" data-i18n="about.locationEyebrow">Localização</span>
      <h2>Blumenau,<br>Santa Catarina</h2>
      <p><?= escape($site['address']) ?></p>
      <p><?= escape($site['hours']) ?></p><a class="text-link" href="https://www.google.com/maps/search/?api=1&query=Victor+Konder+Blumenau+SC" target="_blank" rel="noopener"><span data-i18n="about.directions">Como chegar</span><span aria-hidden="true">→</span></a>
    </div>
    <div class="location-visual" aria-hidden="true"><span>IMPROOV</span><strong>VICTOR KONDER</strong></div>
  </section>
  <div class="container"><?php require APP_ROOT . '/partials/closing-cta.php'; ?></div>
</main>