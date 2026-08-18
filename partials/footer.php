<footer class="site-footer">
  <div class="site-footer__grid">
    <div class="footer-brand"><strong>IMPROOV</strong>
      <p data-i18n="footer.description">Criamos imagens e experiências visuais que conectam pessoas a projetos de arquitetura e ao futuro.</p>
    </div>
    <div>
      <p class="footer-label" data-i18n="footer.navigation">Navegação</p><a href="<?= escape(base_url('quem-somos')) ?>" data-i18n="nav.about">Quem Somos</a><a href="<?= escape(base_url('projetos')) ?>" data-i18n="nav.projects">Projetos</a><a href="<?= escape(base_url('trabalhe-conosco')) ?>" data-i18n="nav.careers">Trabalhe Conosco</a><a href="<?= escape(base_url('contato')) ?>" data-i18n="nav.contact">Contato</a>
    </div>
    <div>
      <p class="footer-label" data-i18n="footer.contact">Contato</p><a href="mailto:<?= escape($site['email']) ?>"><?= escape($site['email']) ?></a><a href="tel:<?= escape($site['phone']) ?>"><?= escape($site['phoneDisplay']) ?></a>
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
<script src="<?= escape(asset_url('js/i18n.js')) ?>" defer></script>
<script src="<?= escape(asset_url('js/site.js')) ?>" defer></script>
<script src="<?= escape(asset_url('js/projects.js')) ?>" defer></script>
<script src="<?= escape(asset_url('js/forms.js')) ?>" defer></script>
</body>

</html>
