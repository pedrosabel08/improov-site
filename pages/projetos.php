<main id="conteudo">
  <?php $projectsForGrid = all_projects();
  $projectGridClass = 'project-grid--catalog'; ?>
  <section class="projects-page projects-page--catalog container" aria-label="Projetos" data-i18n-aria="projects.label">
    <header class="section-heading">
      <span class="eyebrow" data-i18n="projects.eyebrow"><?= escape(ui_text('projects.eyebrow')) ?></span>
      <h1 data-i18n="projects.title"><?= escape(ui_text('projects.title')) ?></h1>
      <p data-i18n="projects.intro"><?= escape(ui_text('projects.intro')) ?></p>
    </header>
    <?php require APP_ROOT . '/partials/project-grid.php'; ?>
  </section>
  <script type="application/json" id="projects-data">
    <?= json_encode(['projects' => all_projects()], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?>
  </script>
</main>
