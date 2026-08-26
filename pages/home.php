<main id="conteudo">
  <section class="hero hero--home">
    <div class="hero__media"><?= responsive_image('assets/projetos/AYA_KAR/6._AYA_KAR_Piscina_maior_EF_1_1.jpg', 'Arquitetura contemporânea integrada à paisagem', 1920, 1080, 'hero__image', '100vw', true, ['data-i18n-alt' => 'home.heroAlt']) ?></div>
    <div class="hero__shade"></div>
    <div class="hero__content container">
      <p class="eyebrow" data-i18n="home.eyebrow">Imagens que</p>
      <h1 data-i18n="home.title">transformam projetos</h1>
      <p data-i18n="home.intro">Criamos imagens arquitetônicas e experiências visuais que conectam pessoas a projetos de arquitetura e ao futuro.</p><a class="text-link" href="<?= escape(base_url('projetos')) ?>"><span data-i18n="home.action">Conheça nosso trabalho</span><span aria-hidden="true">→</span></a>
    </div>
  </section>

  <section class="selected-projects section container">
    <div class="section-heading section-heading--row">
      <div><span class="eyebrow" data-i18n="projects.eyebrow">Projetos</span>
        <h2 data-i18n="home.projectsTitle">Conheça alguns dos nossos trabalhos.</h2>
      </div><a class="text-link" href="<?= escape(base_url('projetos')) ?>"><span data-i18n="home.allProjects">Ver todos os projetos</span><span aria-hidden="true">→</span></a>
    </div>
    <?php $projectsForGrid = home_projects();
    $projectGridClass = 'project-grid--home';
    require APP_ROOT . '/partials/project-grid.php'; ?>
  </section>

  <section class="pillars section container" aria-labelledby="pillars-title">
    <div class="section-heading"><span class="eyebrow" data-i18n="home.pillarsEyebrow">Nossa proposta</span>
      <h2 id="pillars-title" data-i18n="home.pillarsTitle">Imagem com intenção. Experiência com propósito.</h2>
    </div>
    <div class="pillars__grid">
      <?php foreach ([['eye', 'home.pillar1', 'home.pillar1Text'], ['layers', 'home.pillar2', 'home.pillar2Text'], ['users', 'home.pillar3', 'home.pillar3Text'], ['spark', 'home.pillar4', 'home.pillar4Text']] as [$icon, $title, $text]): ?>
        <article class="pillar"><span class="pillar__icon"><?= site_icon($icon) ?></span>
          <h3 data-i18n="<?= $title ?>">Foco no essencial</h3>
          <p data-i18n="<?= $text ?>">Valorizamos a intenção do projeto e o que realmente importa.</p>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <div class="container"><?php require APP_ROOT . '/partials/closing-cta.php'; ?></div>
  <script type="application/json" id="projects-data">
    <?= json_encode(['projects' => all_projects()], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?>
  </script>
</main>