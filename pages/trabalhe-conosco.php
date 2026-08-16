<main id="conteudo">
  <section class="hero hero--careers">
    <div class="hero__media"><?= responsive_image('assets/BHE_INF_Coworking_EF.jpg', 'Equipe trabalhando no estúdio Improov', 1920, 1280, 'hero__image', '100vw', true, ['data-i18n-alt' => 'careers.heroAlt']) ?></div>
    <div class="hero__shade"></div>
    <div class="hero__content container"><span class="eyebrow" data-i18n="careers.eyebrow">Trabalhe Conosco</span>
      <h1 data-i18n="careers.title">Faça parte do time que transforma ideias em experiências visuais.</h1>
      <p data-i18n="careers.intro">Somos movidos por curiosidade, colaboração e paixão por imagem.</p>
    </div>
  </section>
  <section class="culture section container">
    <div class="section-heading"><span class="eyebrow" data-i18n="careers.cultureEyebrow">Nossa cultura</span>
      <h2 data-i18n="careers.cultureTitle">Um ambiente para criar, aprender e evoluir.</h2>
    </div>
    <div class="pillars__grid"><?php foreach ([['◎', 'careers.value1', 'careers.value1Text'], ['◇', 'careers.value2', 'careers.value2Text'], ['▣', 'careers.value3', 'careers.value3Text'], ['↗', 'careers.value4', 'careers.value4Text']] as [$icon, $title, $text]): ?><article class="pillar"><span class="pillar__icon" aria-hidden="true"><?= $icon ?></span>
          <h3 data-i18n="<?= $title ?>">Colaboração real</h3>
          <p data-i18n="<?= $text ?>">Acreditamos na força do trabalho em equipe.</p>
        </article><?php endforeach; ?></div>
  </section>
  <section class="form-layout section container">
    <form class="form-card" data-async-form="careers" action="<?= escape(api_url('candidatura.php')) ?>" method="post" enctype="multipart/form-data" novalidate>
      <div class="form-card__heading"><span class="eyebrow" data-i18n="careers.formEyebrow">Envie sua candidatura</span>
        <h2 data-i18n="careers.formTitle">Queremos conhecer você.</h2>
      </div>
      <input type="hidden" name="idioma" value="pt-BR" data-language-input><input class="honeypot" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
      <div class="form-grid"><label><span data-i18n="form.name">Nome completo</span><input name="nome" autocomplete="name" required></label><label><span data-i18n="form.email">E-mail</span><input name="email" type="email" autocomplete="email" required></label><label><span data-i18n="form.phone">Telefone / WhatsApp</span><input name="telefone" type="tel" autocomplete="tel" required></label><label><span data-i18n="form.city">Cidade / Estado</span><input name="cidade_uf" required></label><label><span data-i18n="form.role">Área ou cargo</span><select name="area_cargo" required>
            <option value="" data-i18n="form.select">Selecione</option>
            <option value="Direção de arte" data-i18n="form.roleArt">Direção de arte</option>
            <option value="Design">Design</option>
            <option value="Motion e 3D">Motion e 3D</option>
            <option value="Arquitetura" data-i18n="form.roleArchitecture">Arquitetura</option>
            <option value="Produção" data-i18n="form.roleProduction">Produção</option>
            <option value="Outra área" data-i18n="form.roleOther">Outra área</option>
          </select></label><label><span data-i18n="form.availability">Disponibilidade</span><select name="disponibilidade_inicio" required>
            <option value="" data-i18n="form.select">Selecione</option>
            <option value="Imediata" data-i18n="form.availabilityNow">Imediata</option>
            <option value="Em até 30 dias" data-i18n="form.availability30">Em até 30 dias</option>
            <option value="Em até 60 dias" data-i18n="form.availability60">Em até 60 dias</option>
            <option value="Outra data" data-i18n="form.availabilityOther">Outra data</option>
          </select></label></div>
      <fieldset>
        <legend data-i18n="form.workModel">Modelo de trabalho</legend>
        <div class="choice-cards"><label><input type="radio" name="modelo_trabalho" value="Presencial" required><span data-i18n="form.inPerson">Presencial</span></label><label><input type="radio" name="modelo_trabalho" value="Híbrido"><span data-i18n="form.hybrid">Híbrido</span></label><label><input type="radio" name="modelo_trabalho" value="Remoto"><span data-i18n="form.remote">Remoto</span></label></div>
      </fieldset>
      <div class="form-grid"><label><span data-i18n="form.portfolio">Portfólio</span><input name="portfolio" type="url" placeholder="https://"></label><label><span>LinkedIn</span><input name="linkedin" type="url" placeholder="https://"></label></div>
      <label class="upload-field"><span data-i18n="form.resume">Currículo PDF</span><span class="upload-drop"><strong data-i18n="form.upload">Arraste seu arquivo ou clique para enviar</strong><small data-i18n="form.resumeHint">PDF até 10 MB</small><input name="curriculo" type="file" accept="application/pdf,.pdf" required data-max-size="10485760"></span><small data-file-name></small></label>
      <label><span data-i18n="form.experience">Conte um pouco sobre você e sua experiência</span><textarea name="experiencia" maxlength="2000" rows="5" required></textarea><small class="character-count" data-character-count>0/2000</small></label>
      <label class="privacy-check"><input name="lgpd" type="checkbox" value="Aceito" required><span><span data-i18n="form.careersPrivacy">Autorizo o uso destes dados para avaliação da candidatura conforme a</span> <a href="<?= escape(base_url('privacidade')) ?>" data-i18n="footer.privacy">Política de Privacidade</a>.</span></label>
      <button class="button button--submit" type="submit"><span data-i18n="form.sendApplication">Enviar candidatura</span><span aria-hidden="true">→</span></button>
      <p class="form-status" data-form-status aria-live="polite"></p>
    </form>
    <aside class="info-panel"><span class="eyebrow" data-i18n="careers.studioEyebrow">Por dentro do estúdio</span>
      <ul>
        <li data-i18n="careers.studio1">Estúdio em Blumenau, SC</li>
        <li data-i18n="careers.studio2">Ambiente criativo e colaborativo</li>
        <li data-i18n="careers.studio3">Projetos desafiadores e autorais</li>
        <li data-i18n="careers.studio4">Tecnologia e processos de ponta</li>
      </ul>
      <hr>
      <h3 data-i18n="careers.where">Onde estamos</h3>
      <p data-i18n="contact.address"><?= escape($site['address']) ?></p>
    </aside>
  </section>
</main>