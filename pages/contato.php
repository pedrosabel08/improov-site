<main id="conteudo">
  <section class="editorial-hero editorial-hero--contact">
    <div class="editorial-hero__inner container">
      <div class="editorial-hero__content"><span class="eyebrow" data-i18n="contact.eyebrow">Contato</span>
        <h1 data-i18n="contact.title">Vamos conversar sobre seu próximo projeto.</h1>
        <p data-i18n="contact.intro">Criamos imagens e experiências visuais que transformam projetos de arquitetura e imobiliário em conexões reais.</p><a class="text-link" href="https://wa.me/<?= escape(ltrim($site['phone'], '+')) ?>" target="_blank" rel="noopener"><span data-i18n="contact.whatsapp">Falar no WhatsApp</span><span aria-hidden="true">→</span></a>
      </div>
      <div class="editorial-hero__media editorial-hero__media--contact">
        <?php $contactVideo = find_video('site', '6-aya-kar-piscina-maior'); ?>
        <?php if ($contactVideo !== null): ?><?= lazy_video($contactVideo, 'editorial-hero__image', false, ['data-i18n-alt' => 'contact.heroAlt', 'aria-label' => 'Animação da piscina AYA Karioó']) ?><?php else: ?><?= responsive_image('assets\media\ars-vie\v1\hero-1440.jpg', 'Fachada', 1920, 1280, 'editorial-hero__image', '(max-width: 767px) 100vw, (max-width: 1023px) 92vw, 38vw', true, ['data-i18n-alt' => 'contact.heroAlt']) ?><?php endif; ?>
      </div>
    </div>
  </section>
  <section class="form-layout form-layout--contact section container">
    <form class="form-card" data-async-form="contact" action="<?= escape(api_url('contacto.php')) ?>" method="post" enctype="multipart/form-data" novalidate>
      <div class="form-card__heading">
        <h2 data-i18n="contact.formTitle">Envie sua mensagem</h2>
        <p data-i18n="contact.formIntro">Preencha os campos e retornaremos em breve.</p>
      </div>
      <input type="hidden" name="idioma" value="pt-BR" data-language-input><input class="honeypot" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
      <div class="form-grid"><label><span data-i18n="form.name">Nome</span><input name="nome" autocomplete="name" required></label><label><span data-i18n="contact.company">Empresa</span><input name="empresa" autocomplete="organization"></label><label><span data-i18n="form.email">E-mail</span><input name="email" type="email" autocomplete="email" required></label><label><span data-i18n="form.phone">Telefone / WhatsApp</span><input name="telefone" type="tel" required></label><label><span data-i18n="form.city">Cidade / Estado</span><input name="cidade_uf"></label><label><span data-i18n="contact.interest">Tipo de projeto / interesse</span><select name="tipo_interesse" required>
            <option value="" data-i18n="form.select">Selecione</option>
            <option value="Imagens 3D" data-i18n="contact.optionImages3d">Imagens 3D</option>
            <option value="Animações 3D" data-i18n="contact.optionAnimations3d">Animações 3D</option>
            <option value="Filmes" data-i18n="contact.optionFilms">Filmes</option>
            <option value="Experiências interativas" data-i18n="contact.optionInteractiveExperiences">Experiências interativas</option>
          </select></label><label class="form-wide"><span data-i18n="contact.development">Nome do empreendimento</span><input name="empreendimento"></label></div>
      <label><span data-i18n="contact.message">Como podemos ajudar?</span><textarea name="mensagem" rows="5" maxlength="3000" required></textarea><small class="character-count" data-character-count>0/3000</small></label>
      <label class="upload-field"><span data-i18n="contact.attachment">Anexo opcional</span><span class="upload-drop"><strong data-i18n="form.upload">Arraste seu arquivo ou clique para enviar</strong><small data-i18n="contact.attachmentHint">PDF, JPG, PNG ou ZIP — até 20 MB</small><input name="anexo" type="file" accept="application/pdf,image/jpeg,image/png,application/zip,.pdf,.jpg,.jpeg,.png,.zip" data-max-size="20971520"></span><small data-file-name></small></label>
      <label class="privacy-check"><input name="lgpd" type="checkbox" value="Aceito" required><span><span data-i18n="form.contactPrivacy">Autorizo o uso dos meus dados para contato e envio de propostas conforme a</span> <a href="<?= escape(base_url('privacidade')) ?>" data-i18n="footer.privacy">Política de Privacidade</a>.</span></label>
      <button class="button button--submit" type="submit"><span data-i18n="contact.submit">Enviar mensagem</span><span aria-hidden="true">→</span></button>
      <p class="form-status" data-form-status aria-live="polite"></p>
    </form>
    <aside class="contact-aside">
      <div class="info-panel">
        <h2 data-i18n="contact.direct">Fale direto conosco</h2>
        <p><strong>E-mail</strong><a href="mailto:<?= escape($site['email']) ?>"><?= escape($site['email']) ?></a></p>
        <p><strong data-i18n="contact.phoneLabel">Telefone / WhatsApp</strong><a href="tel:<?= escape($site['phone']) ?>"><?= escape($site['phoneDisplay']) ?></a></p>
        <p><strong>Instagram</strong><a href="<?= escape($site['social']['Instagram']) ?>" target="_blank" rel="noopener">@improovbr</a></p>
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
