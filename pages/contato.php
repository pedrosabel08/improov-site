<main id="conteudo">
  <section class="editorial-hero editorial-hero--contact">
    <div class="editorial-hero__inner container">
      <div class="editorial-hero__content"><span class="eyebrow" data-i18n="contact.eyebrow"><?= escape(ui_text('contact.eyebrow')) ?></span>
        <h1 data-i18n="contact.title"><?= escape(ui_text('contact.title')) ?></h1>
        <p data-i18n="contact.intro"><?= escape(ui_text('contact.intro')) ?></p><a class="button button--commercial" href="<?= escape(whatsapp_url()) ?>" target="_blank" rel="noopener" data-analytics-event="whatsapp_click" data-analytics-location="hero" data-analytics-name="contact_whatsapp"><span data-i18n="contact.whatsapp"><?= escape(ui_text('contact.whatsapp')) ?></span><span aria-hidden="true">↗</span></a>
      </div>
      <div class="editorial-hero__media editorial-hero__media--contact">
        <?php $contactVideo = find_video('site', '6-aya-kar-piscina-maior'); ?>
        <?php if ($contactVideo !== null): ?><?= lazy_video($contactVideo, 'editorial-hero__image', false, ['data-i18n-alt' => 'contact.heroAlt', 'aria-label' => 'Animação da piscina AYA Karioó']) ?><?php else: ?><?= responsive_image('assets\media\ars-vie\v1\hero-1440.jpg', 'Fachada', 1920, 1280, 'editorial-hero__image', '(max-width: 767px) 100vw, (max-width: 1023px) 92vw, 38vw', true, ['data-i18n-alt' => 'contact.heroAlt']) ?><?php endif; ?>
      </div>
    </div>
  </section>
  <section class="form-layout form-layout--contact section container">
    <form class="form-card" data-async-form="contact" data-success-url="<?= escape(base_url('obrigado')) ?>" action="<?= escape(api_url('contacto.php')) ?>" method="post" novalidate>
      <div class="form-card__heading">
        <h2 data-i18n="contact.formTitle"><?= escape(ui_text('contact.formTitle')) ?></h2>
        <p data-i18n="contact.formIntro"><?= escape(ui_text('contact.formIntro')) ?></p>
      </div>
      <input type="hidden" name="idioma" value="pt-BR" data-language-input><input class="honeypot" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
      <div class="form-grid">
        <label><span data-i18n="form.name">Nome</span><input name="nome" autocomplete="name" maxlength="160" required></label>
        <label><span data-i18n="contact.phone"><?= escape(ui_text('contact.phone')) ?></span><input name="telefone" type="tel" inputmode="tel" autocomplete="tel" maxlength="40" data-whatsapp-phone required></label>
        <!-- Campo de e-mail temporario para teste. -->
        <label><span data-i18n="form.email">E-mail</span><input name="email" type="email" autocomplete="email" maxlength="254"></label>
        <label><span data-i18n="contact.company">Empresa</span><input name="empresa" autocomplete="organization" maxlength="160" required></label>
        <label><span data-i18n="contact.role"><?= escape(ui_text('contact.role')) ?></span><input name="cargo" autocomplete="organization-title" maxlength="160" required></label>
        <label><span data-i18n="contact.projectCity"><?= escape(ui_text('contact.projectCity')) ?></span><input name="cidade_uf" maxlength="120" required></label>
        <label><span data-i18n="contact.launch"><?= escape(ui_text('contact.launch')) ?></span><select name="prazo_lancamento" required>
            <option value="" data-i18n="form.select">Selecione</option>
            <?php foreach (['ate_6_meses' => 'contact.launch6', '6_a_12_meses' => 'contact.launch12', 'mais_de_12_meses' => 'contact.launchLater', 'em_vendas' => 'contact.launchNow'] as $launchValue => $launchLabel): ?>
              <option value="<?= escape($launchValue) ?>" data-i18n="<?= escape($launchLabel) ?>"><?= escape(ui_text($launchLabel)) ?></option>
            <?php endforeach; ?>
          </select></label>
      </div>
      <label class="privacy-check"><input name="lgpd" type="checkbox" value="Aceito" required><span><span data-i18n="form.contactPrivacy">Autorizo o uso dos meus dados para contato e envio de propostas conforme a</span> <a href="<?= escape(base_url('privacidade')) ?>" data-i18n="footer.privacy">Política de Privacidade</a>.</span></label>
      <button class="button button--submit" type="submit"><span data-i18n="contact.submit"><?= escape(ui_text('contact.submit')) ?></span><span aria-hidden="true">→</span></button>
      <p class="form-status" data-form-status aria-live="polite"></p>
    </form>
    <aside class="contact-aside">
      <div class="info-panel">
        <h2 data-i18n="contact.direct">Fale direto conosco</h2>
        <p><strong>E-mail</strong><a href="mailto:<?= escape($site['email']) ?>" data-analytics-event="email_click" data-analytics-location="contact_section"><?= escape($site['email']) ?></a></p>
        <p><strong data-i18n="contact.phoneLabel">Telefone / WhatsApp</strong><a href="<?= escape(whatsapp_url()) ?>" target="_blank" rel="noopener" data-analytics-event="whatsapp_click" data-analytics-location="contact_section" data-analytics-name="contact_whatsapp"><?= escape($site['phoneDisplay']) ?></a></p>
        <p><strong>Instagram</strong><a href="<?= escape($site['social']['Instagram']) ?>" target="_blank" rel="noopener" data-analytics-event="social_click" data-analytics-social="instagram" data-analytics-location="contact_section">@improovbr</a></p>
      </div>
      <div class="info-panel">
        <h2 data-i18n="contact.addressTitle">Nosso endereço</h2>
        <p data-i18n="contact.address"><?= escape($site['address']) ?></p>
        <hr>
        <h3 data-i18n="contact.hoursTitle">Horário de atendimento</h3>
        <p data-i18n="contact.hours"><?= escape($site['hours']) ?></p>
      </div>
    </aside>
  </section>
</main>
