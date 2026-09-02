<?php
$hero = $project['media']['hero'];
$cardTitle = translated($project['title']);
$cardLocation = translated($project['location']);
$cardAlt = translated($hero['alt']);
$animation = project_animation($project);
?>
<a class="project-card project-card--<?= escape($project['placement']) ?>" href="<?= escape(base_url('projetos/' . $project['slug'])) ?>" data-project-slug="<?= escape($project['slug']) ?>">
  <?php if ($animation !== null): ?>
    <?= lazy_video($animation, 'project-card__image', false, ['aria-label' => $cardAlt]) ?>
  <?php else: ?>
    <?= responsive_image($hero['src'], $cardAlt, (int) $hero['width'], (int) $hero['height'], 'project-card__image', '(max-width: 767px) 100vw, (max-width: 1023px) 50vw, 60vw') ?>
  <?php endif; ?>
  <span class="project-card__overlay"><strong data-project-title><?= escape($cardTitle) ?></strong><small data-project-location><span class="project-card__location-icon"><?= site_icon('pin') ?></span><span data-project-location-text><?= escape($cardLocation) ?></span></small></span>
</a>
