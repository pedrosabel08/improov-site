<main id="conteudo">
  <?php $projectsForGrid = all_projects();
  $projectGridClass = 'project-grid--catalog'; ?>
  <section class="projects-page projects-page--catalog container" aria-label="Projetos" data-i18n-aria="projects.label"><?php require APP_ROOT . '/partials/project-grid.php'; ?></section>
  <script type="application/json" id="projects-data">
    <?= json_encode(['projects' => all_projects()], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?>
  </script>
</main>
