<section class="closing-cta whatsapp-banner">
  <div><span class="eyebrow" data-i18n="cta.eyebrow"><?= escape(ui_text('cta.eyebrow')) ?></span>
    <h2 data-i18n="cta.title"><?= escape(ui_text('cta.title')) ?></h2>
  </div>
  <div class="closing-cta__actions">
    <a class="button button--commercial button--whatsapp whatsapp-banner__link" href="<?= escape(whatsapp_url()) ?>" target="_blank" rel="noopener" data-analytics-event="whatsapp_click" data-analytics-name="contact_us" data-analytics-location="closing"><span data-i18n="cta.action"><?= escape(ui_text('cta.action')) ?></span><?= site_icon('arrow-up-right') ?></a>
    <!-- <a class="closing-cta__form" href="<?= escape(base_url('contato')) ?>" data-analytics-event="cta_click" data-analytics-name="contact_form" data-analytics-location="closing" data-i18n="cta.form"><?= escape(ui_text('cta.form')) ?></a> -->
  </div>
</section>
