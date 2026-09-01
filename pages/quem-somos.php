<main id="conteudo">
  <section class="hero hero--about">
    <div class="hero__media"><?= responsive_image('assets/BHE_INF_Coworking_EF.jpg', 'Estúdio criativo da Improov em Blumenau', 1920, 1280, 'hero__image', '100vw', true, ['data-i18n-alt' => 'about.heroAlt']) ?></div>
    <div class="hero__shade"></div>
    <div class="hero__content container">
      <p class="eyebrow" data-i18n="about.eyebrow">Quem Somos</p>
      <h1 data-i18n="about.title">Quem Somos</h1>
      <p data-i18n="about.intro">A Improov nasceu da convicção de que grandes empreendimentos não são vendidos apenas por suas características. Eles conquistam pessoas pelas emoções que despertam.</p>
    </div>
  </section>
  <section class="manifesto section container">
    <div class="editorial-copy"><span class="eyebrow" data-i18n="about.manifestoEyebrow">Nossa essência</span>
      <h2 data-i18n="about.manifestoTitle">Grandes empreendimentos conquistam pessoas pelas emoções que despertam.</h2>
      <p data-i18n="about.manifestoP1">Somos uma empresa especializada em comunicação para o mercado imobiliário, criando imagens, filmes, animações e experiências visuais capazes de transformar projetos em desejo.</p>
      <p data-i18n="about.manifestoP2">Mais do que representar aquilo que ainda será construído, traduzimos a essência de cada empreendimento. Buscamos revelar sua identidade, sua atmosfera e a história que existe por trás da arquitetura.</p>
      <p data-i18n="about.heartmadeIntro">Nossa metodologia une direção criativa, arte, estratégia e tecnologia para desenvolver materiais que fortalecem marcas, encantam clientes e potencializam resultados comerciais.</p>
      <p data-i18n="about.heartmadeDetails">Cada detalhe é pensado para comunicar com verdade. Cada enquadramento, cada luz, cada movimento e cada narrativa existem para despertar sentimentos.</p>
      <p data-i18n="about.heartmadeLead">Chamamos essa filosofia de</p>
      <h2 class="about-heartmade-title"><img class="about-heartmade-logo" src="<?= escape(asset('assets/IMPROOV_heartmade(black).gif')) ?>" alt="Heartmade" data-i18n-alt="about.heartmadeTitle"></h2>
      <p data-i18n="about.heartmadeBelief">Porque acreditamos que a tecnologia, por si só, impressiona. Mas é o olhar humano que emociona.</p>
      <p data-i18n="about.heartmadeTeam">Ao longo da nossa trajetória, reunimos uma equipe multidisciplinar apaixonada por excelência e comprometida em entregar materiais que elevam o posicionamento de incorporadoras, construtoras e empreendimentos.</p>
      <p data-i18n="about.heartmadeExperience">Não produzimos apenas imagens.</p>
      <p data-i18n="about.heartmadeClosing">Criamos experiências que fazem pessoas imaginarem, desejarem e acreditarem em um lugar antes mesmo de ele existir.</p>
      <p data-i18n="about.heartmadeFinal">É assim que transformamos arquitetura em comunicação. E comunicação em valor.</p>
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
      <p><?= escape($site['hours']) ?></p><a class="text-link" href="https://www.google.com/maps/place/Improov+Studios+-+Produtora+3D+e+Filmes/@-26.8865701,-49.0965182,17z/data=!3m1!4b1!4m6!3m5!1s0x94df1fc528e2f16f:0x912761c98509824b!8m2!3d-26.8865701!4d-49.0939433!16s%2Fg%2F11w9pgt5mf?entry=ttu&g_ep=EgoyMDI2MDgxNi4wIKXMDSoASAFQAw%3D%3D" target="_blank" rel="noopener"><span data-i18n="about.directions">Como chegar</span><span aria-hidden="true">→</span></a>
    </div>
    <div class="location-visual"><iframe title="Localização da Improov" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d889.6339712370439!2d-49.09432759175186!3d-26.88648292242941!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94df1fc528e2f16f%3A0x912761c98509824b!2sImproov%20Studios%20-%20Produtora%203D%20e%20Filmes!5e0!3m2!1spt-BR!2sbr!4v1787008791848!5m2!1spt-BR!2sbr" allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe></div>
  </section>
  <div class="container"><?php require APP_ROOT . '/partials/closing-cta.php'; ?></div>
</main>