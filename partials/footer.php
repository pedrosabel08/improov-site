<footer class="site-footer">
  <div class="site-footer__grid">
    <div class="footer-brand">
      <img class="footer-brand__logo" src="<?= escape(asset('assets/IMPROOV_SQUAD(black).gif')) ?>" alt="Improov">
      <p data-i18n="footer.description"><?= escape(ui_text('footer.description')) ?></p>
    </div>
    <div>
      <p class="footer-label" data-i18n="footer.navigation">Navegação</p><a href="<?= escape(base_url('quem-somos')) ?>" data-i18n="nav.about">Quem Somos</a><a href="<?= escape(base_url('projetos')) ?>" data-i18n="nav.projects">Projetos</a><a href="<?= escape(base_url('trabalhe-conosco')) ?>" data-i18n="nav.careers">Trabalhe Conosco</a><a href="<?= escape(base_url('contato')) ?>" data-i18n="nav.contact">Contato</a>
    </div>
    <div>
      <p class="footer-label" data-i18n="footer.contact">Contato</p><a href="mailto:<?= escape($site['email']) ?>"><?= escape($site['email']) ?></a><a href="https://wa.me/<?= escape(ltrim($site['phone'], '+')) ?>?text=<?= rawurlencode('Olá! Vim pelo site da Improov e quero conversar sobre os materias de apresentação do meu empreendimento.') ?>" target="_blank" rel="noopener"><?= escape($site['phoneDisplay']) ?></a>
      <p>Blumenau, SC — Brasil</p>
    </div>
    <div>
      <p class="footer-label" data-i18n="footer.follow">Siga-nos</p>
      <div class="social-links"><?php $socialIcons = ['Instagram' => 'fa-instagram', 'LinkedIn' => 'fa-linkedin-in', 'YouTube' => 'fa-youtube'];
      foreach ($site['social'] as $name => $url): ?><a href="<?= escape($url) ?>" target="_blank" rel="noopener" aria-label="<?= escape($name) ?>"><i class="fa-brands <?= escape($socialIcons[$name] ?? 'fa-circle-question') ?>" aria-hidden="true"></i></a><?php endforeach; ?></div>
    </div>
  </div>
  <div class="site-footer__bottom"><span>© <?= date('Y') ?> Improov. <span data-i18n="footer.rights">Todos os direitos reservados.</span></span><a href="<?= escape(base_url('privacidade')) ?>" data-i18n="footer.privacy">Política de Privacidade</a></div>
</footer>
<script src="<?= escape(asset('assets/js/i18n.js')) ?>" defer></script>
<script src="<?= escape(asset('assets/js/site.js')) ?>" defer></script>
<script src="<?= escape(asset('assets/js/projects.js')) ?>" defer></script>
<script src="<?= escape(asset('assets/js/forms.js')) ?>" defer></script>
<script src="<?= escape(asset('assets/js/analytics.js')) ?>" defer></script>
<?php if ($case !== null): ?><script src="<?= escape(asset('assets/js/case.js')) ?>" defer></script><?php endif; ?>
</body>

</html>
