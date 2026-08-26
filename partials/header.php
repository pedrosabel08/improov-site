<header class="site-header" data-site-header>
  <a class="brand" href="<?= escape(base_url()) ?>" aria-label="Improov — Início" data-i18n-aria="nav.home">
    <img src="<?= escape(asset('assets/IMPROOV_TOP.gif')) ?>" alt="Improov" id="gif-header">
  </a>
  <nav class="desktop-nav" aria-label="Navegação principal" data-i18n-aria="nav.label">
    <?php foreach (
      [
        'quem-somos' => ['quem-somos', 'nav.about', 'Quem Somos'],
        'projetos' => ['projetos', 'nav.projects', 'Projetos'],
        'trabalhe-conosco' => ['trabalhe-conosco', 'nav.careers', 'Trabalhe Conosco'],
        'contato' => ['contato', 'nav.contact', 'Contato'],
      ] as $key => [$path, $i18n, $label]
    ): ?>
      <a href="<?= escape(base_url($path)) ?>" class="<?= $activePage === $key ? 'is-active' : '' ?>" <?= $activePage === $key ? 'aria-current="page"' : '' ?> data-i18n="<?= escape($i18n) ?>"><?= escape($label) ?></a>
    <?php endforeach; ?>
  </nav>
  <div class="language-switcher" role="group" aria-label="Selecionar idioma" data-i18n-aria="language.label">
    <button type="button" data-language="pt-BR" aria-pressed="true">PT</button>
    <button type="button" data-language="en" aria-pressed="false">EN</button>
    <button type="button" data-language="es" aria-pressed="false">ES</button>
  </div>
  <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" data-i18n-aria="menu.open"><span></span><span></span><span></span></button>
  <nav class="mobile-menu" id="mobile-menu" aria-hidden="true" aria-label="Navegação principal" data-i18n-aria="nav.label">
    <?php foreach (
      [
        'quem-somos' => ['quem-somos', 'nav.about', 'Quem Somos'],
        'projetos' => ['projetos', 'nav.projects', 'Projetos'],
        'trabalhe-conosco' => ['trabalhe-conosco', 'nav.careers', 'Trabalhe Conosco'],
        'contato' => ['contato', 'nav.contact', 'Contato'],
      ] as $key => [$path, $i18n, $label]
    ): ?>
      <a href="<?= escape(base_url($path)) ?>" class="<?= $activePage === $key ? 'is-active' : '' ?>" <?= $activePage === $key ? 'aria-current="page"' : '' ?> data-i18n="<?= escape($i18n) ?>"><?= escape($label) ?></a>
    <?php endforeach; ?>
    <div class="language-switcher" role="group" aria-label="Selecionar idioma" data-i18n-aria="language.label">
      <button type="button" data-language="pt-BR" aria-pressed="true">PT</button><button type="button" data-language="en">EN</button><button type="button" data-language="es">ES</button>
    </div>
  </nav>
</header>
