<?php
$homeTestimonials = [];
foreach (['aya-kar' => 'filme-2026-aya-kar-depo-full-dc', 'ars-vie' => 'filme-2025-ars-kam-vieiras-depoimento-v2-leg'] as $testimonialSlug => $testimonialId) {
  $testimonialProject = find_project($testimonialSlug);
  $testimonialVideo = find_video($testimonialSlug, $testimonialId);
  $testimonialSource = $testimonialVideo !== null ? case_video_source($testimonialVideo) : null;
  if ($testimonialProject !== null && $testimonialVideo !== null && $testimonialSource !== null) {
    $homeTestimonials[] = [$testimonialProject, $testimonialVideo, $testimonialSource];
  }
}
?>
<?php if ($homeTestimonials !== []): ?>
  <section class="home-testimonials section container" aria-labelledby="testimonials-title">
    <div class="section-heading">
      <span class="eyebrow" data-i18n="testimonials.eyebrow"><?= escape(ui_text('testimonials.eyebrow')) ?></span>
      <h2 id="testimonials-title" data-i18n="testimonials.title"><?= escape(ui_text('testimonials.title')) ?></h2>
      <p data-i18n="testimonials.intro"><?= escape(ui_text('testimonials.intro')) ?></p>
    </div>
    <div class="home-testimonials__grid">
      <?php foreach ($homeTestimonials as [$testimonialProject, $testimonialVideo, $testimonialSource]): ?>
        <article class="home-testimonials__item">
          <div class="home-testimonials__video-wrap">
            <video class="case-v3-film__video" controls playsinline preload="none" data-lazy-poster="<?= escape(asset((string) $testimonialVideo['poster'])) ?>" width="<?= (int) ($testimonialSource['width'] ?? 1920) ?>" height="<?= (int) ($testimonialSource['height'] ?? 1080) ?>" aria-label="<?= escape($testimonialProject['detail']['info']['client'] . ' — ' . translated($testimonialProject['title'])) ?>" data-analytics-testimonial data-analytics-project="<?= escape($testimonialProject['slug']) ?>">
              <source src="<?= escape(asset($testimonialSource['src'])) ?>" type="video/mp4">
            </video>
            <button class="case-v3-film__play" type="button" data-case-film-play hidden data-i18n-aria="video.playFullscreen" aria-label="<?= escape(ui_text('video.playFullscreen')) ?>"><span><?= site_icon('play', 'case-v3-icon') ?></span></button>
          </div>
          <h3><?= escape($testimonialProject['detail']['info']['client']) ?></h3>
          <a class="text-link" href="<?= escape(base_url('projetos/' . $testimonialProject['slug'])) ?>" data-analytics-event="project_select" data-analytics-location="testimonials" data-project-slug="<?= escape($testimonialProject['slug']) ?>" data-project-name="<?= escape(translated($testimonialProject['title'])) ?>"><?= escape(translated($testimonialProject['title'])) ?><span aria-hidden="true">→</span></a>
        </article>
      <?php endforeach; ?>
    </div>
  </section>
<?php endif; ?>
