<main id="conteudo">
  <?php $heroVideos = home_hero_videos(); $heroProject = !empty($heroVideos) ? find_project($heroVideos[0]['slug']) : null; ?>
  <section class="hero hero--home">
    <div class="hero__media"><?php if ($heroProject !== null): ?><?= lazy_video(project_animation($heroProject), 'hero__image', true, ['data-home-hero' => 'true', 'data-i18n-alt' => 'home.heroAlt', 'aria-label' => 'Animação arquitetônica']) ?><?php else: ?><?= responsive_image('assets/projetos/AYA_KAR/6._AYA_KAR_Piscina_maior_EF_1_1.jpg', 'Arquitetura contemporânea integrada à paisagem', 1920, 1080, 'hero__image', '100vw', true, ['data-i18n-alt' => 'home.heroAlt']) ?><?php endif; ?></div>
    <div class="hero__shade"></div>
    <div class="hero__content container">
      <p class="eyebrow" data-i18n="home.eyebrow"><?= escape(ui_text('home.eyebrow')) ?></p>
      <h1 data-i18n="home.title"><?= escape(ui_text('home.title')) ?></h1>
      <p data-i18n="home.intro"><?= escape(ui_text('home.intro')) ?></p><a class="text-link text-link--on-media" href="<?= escape(base_url('projetos')) ?>"><span data-i18n="home.action">Conheça nosso trabalho</span><span aria-hidden="true">→</span></a>
    </div>
  </section>

  <?php if (count($heroVideos) > 1): ?><script type="application/json" id="home-hero-videos"><?= json_encode($heroVideos, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script><?php endif; ?>

  <section class="selected-projects section container">
    <div class="section-heading section-heading--row">
      <div><span class="eyebrow" data-i18n="projects.eyebrow">Projetos</span>
        <h2 data-i18n="home.projectsTitle"><?= escape(ui_text('home.projectsTitle')) ?></h2>
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
          <h3 data-i18n="<?= $title ?>"><?= escape(ui_text($title)) ?></h3>
          <p data-i18n="<?= $text ?>"><?= escape(ui_text($text)) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="faq section container" aria-labelledby="faq-title">
    <div class="section-heading">
      <span class="eyebrow" data-i18n="faq.eyebrow"><?= escape(ui_text('faq.eyebrow')) ?></span>
      <h2 id="faq-title" data-i18n="faq.title"><?= escape(ui_text('faq.title')) ?></h2>
    </div>
    <div class="faq__list">
      <?php foreach ([['faq.q1', 'faq.a1'], ['faq.q2', 'faq.a2'], ['faq.q3', 'faq.a3']] as [$question, $answer]): ?>
        <details class="faq__item">
          <summary data-i18n="<?= escape($question) ?>"><?= escape(ui_text($question)) ?></summary>
          <p data-i18n="<?= escape($answer) ?>"><?= escape(ui_text($answer)) ?></p>
        </details>
      <?php endforeach; ?>
    </div>
  </section>

  <div class="container"><?php require APP_ROOT . '/partials/closing-cta.php'; ?></div>
  <script type="application/json" id="projects-data">
    <?= json_encode(['projects' => all_projects()], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?>
  </script>
</main>
