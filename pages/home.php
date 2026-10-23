<main id="conteudo">
  <?php $heroVideos = home_hero_videos();
  $heroProject = !empty($heroVideos) ? find_project($heroVideos[0]['slug']) : null; ?>
  <section class="hero hero--home">
    <div class="hero__media"><?php if ($heroProject !== null): ?><?= lazy_video(project_animation($heroProject), 'hero__image', true, ['data-home-hero' => 'true', 'data-i18n-alt' => 'home.heroAlt', 'aria-label' => 'Animação arquitetônica']) ?><?php else: ?><?= responsive_image('assets/projetos/AYA_KAR/6._AYA_KAR_Piscina_maior_EF_1_1.jpg', 'Arquitetura contemporânea integrada à paisagem', 1920, 1080, 'hero__image', '100vw', true, ['data-i18n-alt' => 'home.heroAlt']) ?><?php endif; ?></div>
    <div class="hero__shade"></div>
    <div class="hero__content container">
      <p class="eyebrow" data-i18n="home.eyebrow"><?= escape(ui_text('home.eyebrow')) ?></p>
      <h1 data-i18n="home.title"><?= escape(ui_text('home.title')) ?></h1>
      <p class="hero__intro" data-i18n="home.intro"><?= escape(ui_text('home.intro')) ?></p>
      <div class="hero__actions">
        <a class="button hero__action hero__action--primary" href="<?= escape(whatsapp_url()) ?>" target="_blank" rel="noopener" data-analytics-event="whatsapp_click" data-analytics-name="evaluate_launch" data-analytics-location="hero"><span data-i18n="home.contactAction"><?= escape(ui_text('home.contactAction')) ?></span><span aria-hidden="true">↗</span></a>
        <a class="button hero__action hero__action--secondary" href="<?= escape(base_url('projetos')) ?>" data-analytics-event="cta_click" data-analytics-name="view_projects" data-analytics-location="hero"><span data-i18n="home.action"><?= escape(ui_text('home.action')) ?></span><span aria-hidden="true">→</span></a>
        <div class="home-proof__stats hero__stats">
          <p><strong>20</strong><span data-i18n="home.proofYears"><?= escape(ui_text('home.proofYears')) ?></span></p>
          <p><strong>+1.000</strong><span data-i18n="home.proofProjects"><?= escape(ui_text('home.proofProjects')) ?></span></p>
        </div>
      </div>
      <p class="hero__signature" data-i18n="home.signature"><?= escape(ui_text('home.signature')) ?></p>
    </div>
  </section>

  <?php if (count($heroVideos) > 1): ?><script type="application/json" id="home-hero-videos">
      <?= json_encode($heroVideos, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?>
    </script><?php endif; ?>

  <div class="home-proof">
    <div class="home-proof__inner container">
      <ul class="home-proof__logos">
        <?php foreach ([['fg.png', 'FG Empreendimentos', 'fg', 554, 361], ['pininfarina.png', 'Pininfarina', 'pininfarina', 447, 447], ['ars-kammer.webp', 'ARS Kammer Incorporadora', 'ars', 350, 350], ['rdo-logo.svg', 'RDO', 'rdo', 2000, 549], ['hsantos.jpg', 'H. Santos Empreendimentos', 'hsantos', 700, 320], ['aya.png', 'AYA Empreendimentos', 'aya', 709, 709]] as [$logoFile, $logoName, $logoClass, $logoWidth, $logoHeight]): ?>
          <li class="home-proof__logo home-proof__logo--<?= escape($logoClass) ?>"><img src="<?= escape(asset('assets/logos/' . $logoFile)) ?>" alt="<?= escape($logoName) ?>" width="<?= $logoWidth ?>" height="<?= $logoHeight ?>" loading="lazy" decoding="async"></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

  <section class="selected-projects section container">
    <div class="section-heading section-heading--row">
      <div><span class="eyebrow" data-i18n="projects.eyebrow">Projetos</span>
        <h2 data-i18n="home.projectsTitle"><?= escape(ui_text('home.projectsTitle')) ?></h2>
      </div><a class="text-link" href="<?= escape(base_url('projetos')) ?>" data-analytics-event="cta_click" data-analytics-name="view_all_projects" data-analytics-location="portfolio"><span data-i18n="home.allProjects">Ver todos os projetos</span><span aria-hidden="true">→</span></a>
    </div>
    <?php $projectsForGrid = home_projects();
    $projectGridClass = 'project-grid--home';
    require APP_ROOT . '/partials/project-grid.php'; ?>
  </section>

  <section class="launch-invite container" aria-labelledby="launch-invite-title">
    <div>
      <h2 id="launch-invite-title" data-i18n="home.inviteTitle"><?= escape(ui_text('home.inviteTitle')) ?></h2>
      <p data-i18n="home.inviteText"><?= escape(ui_text('home.inviteText')) ?></p>
    </div>
    <a class="button button--commercial" href="<?= escape(whatsapp_url()) ?>" target="_blank" rel="noopener" data-analytics-event="whatsapp_click" data-analytics-name="launch_review" data-analytics-location="after_projects"><span data-i18n="contact.whatsapp"><?= escape(ui_text('contact.whatsapp')) ?></span><span aria-hidden="true">↗</span></a>
  </section>

  <section class="work-process section container" aria-labelledby="process-title">
    <div class="section-heading"><span class="eyebrow" data-i18n="process.eyebrow"><?= escape(ui_text('process.eyebrow')) ?></span>
      <h2 id="process-title" data-i18n="process.title"><?= escape(ui_text('process.title')) ?></h2>
    </div>
    <ol class="work-process__steps">
      <?php for ($step = 1; $step <= 4; $step++): ?>
        <li><span class="work-process__number" aria-hidden="true"><?= str_pad((string) $step, 2, '0', STR_PAD_LEFT) ?></span>
          <h3 data-i18n="process.step<?= $step ?>"><?= escape(ui_text('process.step' . $step)) ?></h3>
          <p data-i18n="process.text<?= $step ?>"><?= escape(ui_text('process.text' . $step)) ?></p>
        </li>
      <?php endfor; ?>
    </ol>
    <div class="work-process__timelines">
      <h3 data-i18n="process.timelines"><?= escape(ui_text('process.timelines')) ?></h3>
      <p class="work-process__timelines-intro" data-i18n="process.timelinesIntro"><?= escape(ui_text('process.timelinesIntro')) ?></p>
      <dl>
        <?php foreach (['images', 'animations', 'films'] as $service): ?>
          <div>
            <dt data-i18n="process.<?= $service ?>"><?= escape(ui_text('process.' . $service)) ?></dt>
            <dd>
              <strong class="work-process__range" data-i18n="process.<?= $service ?>Range"><?= escape(ui_text('process.' . $service . 'Range')) ?></strong>
              <span class="work-process__unit" data-i18n="process.<?= $service === 'films' ? 'weeks' : 'businessDays' ?>"><?= escape(ui_text('process.' . ($service === 'films' ? 'weeks' : 'businessDays'))) ?></span>
            </dd>
          </div>
        <?php endforeach; ?>
      </dl>
      <p class="work-process__timelines-note" data-i18n="process.note"><?= escape(ui_text('process.note')) ?></p>
    </div>
  </section>

  <?php require APP_ROOT . '/partials/home-testimonials.php'; ?>
  <section class="faq section container" aria-labelledby="faq-title">
    <div class="section-heading">
      <span class="eyebrow" data-i18n="faq.eyebrow"><?= escape(ui_text('faq.eyebrow')) ?></span>
      <h2 id="faq-title" data-i18n="faq.title"><?= escape(ui_text('faq.title')) ?></h2>
    </div>
    <div class="faq__list">
      <?php foreach ([['faq.q1', 'faq.a1'], ['faq.q2', 'faq.a2'], ['faq.q3', 'faq.a3'], ['faq.q4', 'faq.a4'], ['faq.q5', 'faq.a5'], ['faq.q6', 'faq.a6']] as [$question, $answer]): ?>
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
