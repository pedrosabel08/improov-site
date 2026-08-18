<div class="project-grid<?= isset($projectGridClass) ? ' ' . escape($projectGridClass) : '' ?>">
  <?php foreach ($projectsForGrid as $project): require APP_ROOT . '/partials/project-card.php';
  endforeach; ?>
</div>
