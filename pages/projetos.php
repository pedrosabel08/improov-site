<main id="conteudo">
  <?php $projectsForGrid = all_projects();
  $leadProject = array_shift($projectsForGrid); ?>
  <section class="projects-lead container">
    <div class="projects-intro"><span class="eyebrow" data-i18n="projects.eyebrow">Projetos</span>
      <h1 data-i18n="projects.title">Imagens que revelam o essencial de cada projeto.</h1>
      <p data-i18n="projects.intro">Desenvolvemos imagens arquitetônicas e experiências visuais que valorizam a intenção do projeto.</p>
    </div>
    <?php if ($leadProject): ?><div class="projects-lead__card"><?php $project = $leadProject;
                                                                require APP_ROOT . '/partials/project-card.php'; ?></div><?php endif; ?>
  </section>
  <section class="projects-page container" aria-label="Projetos" data-i18n-aria="projects.label"><?php require APP_ROOT . '/partials/project-grid.php'; ?></section>
  <script type="application/json" id="projects-data">
    <?= json_encode(['projects' => all_projects()], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?>
  </script>
</main>