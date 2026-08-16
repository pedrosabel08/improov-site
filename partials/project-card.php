<?php
$hero = $project['media']['hero'];
$cardTitle = translated($project['title']);
$cardLocation = translated($project['location']);
$cardAlt = translated($hero['alt']);
?>
<a class="project-card project-card--<?= escape($project['placement']) ?>" href="<?= escape(base_url('projetos/' . $project['slug'])) ?>" data-project-slug="<?= escape($project['slug']) ?>">
  <?= responsive_image($hero['src'], $cardAlt, (int) $hero['width'], (int) $hero['height'], 'project-card__image', '(max-width: 767px) 100vw, (max-width: 1023px) 50vw, 60vw') ?>
  <span class="project-card__overlay"><strong data-project-title><?= escape($cardTitle) ?></strong><small data-project-location><span aria-hidden="true">⌖</span> <?= escape($cardLocation) ?></small></span>
</a>