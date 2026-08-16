<?php

declare(strict_types=1);

function site_content(): array
{
    return [
        'name' => 'Improov',
        'email' => 'contato@improov.com.br',
        'phoneDisplay' => '+55 47 99108-7014',
        'phone' => '+5547991087014',
        'address' => 'Rua 7 de Setembro, 1234 — Bairro Victor Konder — Blumenau, SC — 89012-050 — Brasil',
        'hours' => 'Segunda a sexta, das 9h às 18h',
        'social' => [
            'Instagram' => 'https://instagram.com/improovbr',
            'LinkedIn' => 'https://br.linkedin.com/company/improovbr',
            'YouTube' => 'https://www.youtube.com/@ImproovBR/videos',
        ],
    ];
}

function page_metadata(string $key): array
{
    $pages = [
        'home' => ['title' => 'Improov — Imagens que transformam projetos', 'description' => 'Imagens arquitetônicas e experiências visuais que conectam pessoas a projetos.', 'path' => '', 'image' => 'BHE_INF_Fachada_EF.jpg'],
        'quem-somos' => ['title' => 'Quem Somos — Improov', 'description' => 'Conheça a filosofia, o estúdio e as pessoas por trás da Improov.', 'path' => 'quem-somos', 'image' => 'BHE_INF_Coworking_EF.jpg'],
        'projetos' => ['title' => 'Projetos — Improov', 'description' => 'Uma seleção editorial de imagens e experiências arquitetônicas criadas pela Improov.', 'path' => 'projetos', 'image' => 'projetos/AYA_KAR/6._AYA_KAR_Piscina_maior_EF_1_1.jpg'],
        'trabalhe-conosco' => ['title' => 'Trabalhe Conosco — Improov', 'description' => 'Faça parte do time que transforma ideias em experiências visuais.', 'path' => 'trabalhe-conosco', 'image' => 'BHE_INF_Coworking_EF.jpg'],
        'contato' => ['title' => 'Contato — Improov', 'description' => 'Converse com a Improov sobre seu próximo projeto de arquitetura ou empreendimento.', 'path' => 'contato', 'image' => 'BHE_INF_Fachada_Extra.jpg'],
        'privacidade' => ['title' => 'Política de Privacidade — Improov', 'description' => 'Como a Improov trata dados enviados por formulários comerciais e de recrutamento.', 'path' => 'privacidade', 'image' => 'BHE_INF_Fachada_EF.jpg'],
        '404' => ['title' => 'Página não encontrada — Improov', 'description' => 'A página solicitada não foi encontrada.', 'path' => '', 'image' => 'BHE_INF_Fachada_EF.jpg'],
    ];
    return $pages[$key] ?? $pages['404'];
}

function thumbnail_url(string $source, int $width = 1200, int $quality = 80): string
{
    return base_url('thumb.php?path=' . rawurlencode($source) . '&w=' . $width . '&q=' . $quality);
}

function responsive_image(
    string $source,
    string $alt,
    int $width,
    int $height,
    string $class = '',
    string $sizes = '100vw',
    bool $priority = false,
    array $attributes = []
): string {
    $srcset = [];
    foreach ([640, 1024, 1440, 1920] as $candidate) {
        if ($candidate <= $width || $candidate === 640) {
            $srcset[] = thumbnail_url($source, min($candidate, $width)) . ' ' . min($candidate, $width) . 'w';
        }
    }
    $extraAttributes = '';
    foreach ($attributes as $name => $value) {
        if (preg_match('/^(data-[a-z0-9-]+|aria-[a-z0-9-]+)$/', (string) $name) === 1) {
            $extraAttributes .= sprintf(' %s="%s"', $name, escape((string) $value));
        }
    }
    return sprintf(
        '<img src="%s" srcset="%s" sizes="%s" width="%d" height="%d" alt="%s" class="%s" loading="%s" decoding="async"%s%s>',
        escape(thumbnail_url($source, min(1440, $width))),
        escape(implode(', ', array_unique($srcset))),
        escape($sizes),
        $width,
        $height,
        escape($alt),
        escape($class),
        $priority ? 'eager' : 'lazy',
        $priority ? ' fetchpriority="high"' : '',
        $extraAttributes
    );
}
