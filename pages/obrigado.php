<main id="conteudo" class="thank-you container">
  <div class="thank-you__content">
    <span class="eyebrow" data-i18n="contact.eyebrow"><?= escape(ui_text('contact.eyebrow')) ?></span>
    <h1 data-i18n="contact.received"><?= escape(ui_text('contact.received')) ?></h1>
    <p data-i18n="contact.thanksText"><?= escape(ui_text('contact.thanksText')) ?></p>
    <h2 data-i18n="contact.thanksNext"><?= escape(ui_text('contact.thanksNext')) ?></h2>
    <a class="button button--commercial" href="<?= escape(whatsapp_url()) ?>" target="_blank" rel="noopener" data-analytics-event="whatsapp_click" data-analytics-name="review_followup" data-analytics-location="thank_you"><span data-i18n="contact.thanksAction"><?= escape(ui_text('contact.thanksAction')) ?></span><?= site_icon('arrow-up-right') ?></a>
    <a class="text-link" href="<?= escape(base_url()) ?>" data-i18n="contact.backHome"><?= escape(ui_text('contact.backHome')) ?></a>
  </div>
</main>