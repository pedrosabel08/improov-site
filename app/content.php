<?php

declare(strict_types=1);

function site_content(): array
{
    return [
        'name' => 'Improov',
        'email' => 'contato@improov.com.br',
        'phoneDisplay' => '+55 47 99108-7014',
        'phone' => '+5547991087014',
        'address' => 'Rua Bahia, 821 — Bairro Do Salto — Blumenau, SC — 89031-001 — Brasil',
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
        'home' => [
            'title' => 'IMPROOV | Visualização arquitetônica 3D para o mercado imobiliário',
            'description' => 'A IMPROOV transforma projetos imobiliários em experiências visuais com imagens 3D, animações e filmes que comunicam, encantam e valorizam cada empreendimento.',
            'path' => '',
            'image' => 'assets/media/aya-kar/v1/hero-1440.jpg',
        ],
        'quem-somos' => [
            'title' => 'IMPROOV | Comunicação e Arte para o Mercado Imobiliário',
            'description' => 'Conheça a filosofia, o estúdio e as pessoas por trás da Improov.',
            'path' => 'quem-somos',
            'image' => 'assets/media/site/v1/about-studio-01-1440.jpg',
        ],
        'projetos' => [
            'title' => 'IMPROOV | Portfólio de Imagens 3D e Filmes Imobiliários',
            'description' => 'Conheça projetos de imagens 3D, animações, filmes e materiais visuais produzidos pela Improov para o mercado imobiliário.',
            'path' => 'projetos',
            'image' => 'projetos/AYA_KAR/6._AYA_KAR_Piscina_maior_EF_1_1.jpg',
        ],
        'trabalhe-conosco' => [
            'title' => 'IMPROOV | Trabalhe Conosco',
            'description' => 'Faça parte do time que transforma ideias em experiências visuais.',
            'path' => 'trabalhe-conosco',
            'image' => 'assets/media/aya-kar/v1/hero-1024.jpg',
        ],
        'contato' => [
            'title' => 'Fale com a Improov | Imagens 3D e Filmes Imobiliários',
            'description' => 'Fale com a Improov sobre imagens 3D, animações, filmes e experiências visuais para o seu empreendimento.',
            'path' => 'contato',
            'image' => 'assets/media/aya-kar/v1/hero-1024.jpg',
        ],
        'privacidade' => [
            'title' => 'Política de Privacidade — Improov',
            'description' => 'Como a Improov trata dados enviados por formulários comerciais e de recrutamento.',
            'path' => 'privacidade',
            'image' => 'assets/media/aya-kar/v1/hero-1024.jpg',
        ],
        '404' => [
            'title' => 'Página não encontrada — Improov',
            'description' => 'A página solicitada não foi encontrada.',
            'path' => '',
            'image' => 'assets/media/aya-kar/v1/hero-1024.jpg',
        ],
    ];

    $page = $pages[$key] ?? $pages['404'];
    $localized = [
        'home' => [
            'en' => ['IMPROOV | 3D Architectural Visualization for the Real Estate Market', 'IMPROOV transforms real estate developments into visual experiences through 3D imagery, animations and films that communicate, inspire and add value.'],
            'es' => ['IMPROOV | Visualización arquitectónica 3D para el mercado inmobiliario', 'IMPROOV transforma proyectos inmobiliarios en experiencias visuales con imágenes 3D, animaciones y películas que comunican, inspiran y generan valor.'],
        ],
        'quem-somos' => [
            'en' => ['IMPROOV | Communication and Art for the Real Estate Market', 'Learn about Improov’s studio, philosophy and work for the real estate market.'],
            'es' => ['IMPROOV | Comunicación y Arte para el Mercado Inmobiliario', 'Conozca el estudio, la filosofía y el trabajo de Improov para el mercado inmobiliario.'],
        ],
        'projetos' => [
            'en' => ['IMPROOV | Portfolio of 3D Images and Real Estate Films', 'Explore 3D images, animations, films and visual materials produced by Improov for real estate.'],
            'es' => ['IMPROOV | Portafolio de Imágenes 3D y Películas Inmobiliarias', 'Conozca proyectos de imágenes 3D, animaciones, películas y materiales visuales producidos por Improov.'],
        ],
        'trabalhe-conosco' => [
            'en' => ['IMPROOV | Work With Us', 'Join the team that transforms ideas into visual experiences.'],
            'es' => ['IMPROOV | Trabaja con Nosotros', 'Forma parte del equipo que transforma ideas en experiencias visuales.'],
        ],
        'contato' => [
            'en' => ['Talk to Improov | 3D Images and Real Estate Films', 'Talk to Improov about 3D images, animations, films and visual experiences for your project.'],
            'es' => ['Habla con Improov | Imágenes 3D y Películas Inmobiliarias', 'Hable con Improov sobre imágenes 3D, animaciones, películas y experiencias visuales para su proyecto.'],
        ],
        'privacidade' => [
            'en' => ['Privacy Policy — Improov', 'How Improov handles data submitted through commercial and recruitment forms.'],
            'es' => ['Política de Privacidad — Improov', 'Cómo Improov trata los datos enviados mediante formularios comerciales y de selección.'],
        ],
        '404' => [
            'en' => ['Page Not Found — Improov', 'The requested page could not be found.'],
            'es' => ['Página no encontrada — Improov', 'La página solicitada no fue encontrada.'],
        ],
    ];
    $translation = $localized[$key][current_language()] ?? null;
    if ($translation !== null) {
        [$page['title'], $page['description']] = $translation;
    }
    return $page;
}
function thumbnail_url(string $source, int $width = 1200, int $quality = 80): string
{
    $derived = media_image_path($source, $width);
    if ($derived !== null) {
        return asset($derived);
    }
    $query = 'path=' . rawurlencode($source) . '&w=' . $width . '&q=' . $quality;
    $sourceMtime = asset_mtime($source);
    if ($sourceMtime !== null) {
        $query .= '&v=' . rawurlencode((string) $sourceMtime);
    }
    return raw_base_url('thumb.php?' . $query);
}

function media_image_path(string $source, int $width = 1440): ?string
{
    $sources = media_map()[$source]['sources'] ?? [];
    $widths = array_map('intval', array_keys($sources));
    sort($widths, SORT_NUMERIC);
    $fallback = null;
    foreach ($widths as $candidate) {
        $path = $sources[(string) $candidate]['jpg'] ?? null;
        if (!is_string($path) || $path === '') {
            continue;
        }
        $fallback = $path;
        if ($candidate >= $width) {
            return $path;
        }
    }
    return $fallback;
}

function media_map(): array
{
    static $map;
    if ($map !== null) {
        return $map;
    }
    $path = APP_ROOT . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'media-map.json';
    if (!is_file($path)) {
        return $map = [];
    }
    $decoded = json_decode((string) file_get_contents($path), true);
    return $map = is_array($decoded) ? $decoded : [];
}

function responsive_image(
    string $source,
    string $alt,
    int $width,
    int $height,
    string $class = '',
    string $sizes = '100vw',
    bool $priority = false,
    array $attributes = [],
): string {
    $media = media_map()[$source] ?? null;
    if (is_array($media) && !empty($media['sources'])) {
        $availableWidths = array_map('intval', array_keys($media['sources']));
        sort($availableWidths);
        $srcsetByFormat = ['avif' => [], 'webp' => [], 'jpg' => []];
        foreach ($availableWidths as $candidate) {
            if ($candidate > $width) {
                continue;
            }
            foreach (array_keys($srcsetByFormat) as $format) {
                $path = $media['sources'][(string) $candidate][$format] ?? null;
                if (is_string($path) && $path !== '') {
                    $srcsetByFormat[$format][] = asset($path) . ' ' . $candidate . 'w';
                }
            }
        }
        if (empty($srcsetByFormat['jpg'])) {
            $srcsetByFormat['jpg'][] = asset((string) ($media['sources'][(string) end($availableWidths)]['jpg'] ?? '')) . ' ' . end($availableWidths) . 'w';
        }
        $fallback = $media['sources'][(string) min(1440, max($availableWidths))]['jpg'] ?? $media['sources'][(string) end($availableWidths)]['jpg'];
        $loading = $priority ? 'eager' : 'lazy';
        $fetchPriority = $priority ? ' fetchpriority="high"' : '';
        $extraAttributes = '';
        foreach ($attributes as $name => $value) {
            if (preg_match('/^(data-[a-z0-9-]+|aria-[a-z0-9-]+)$/', (string) $name) === 1) {
                $extraAttributes .= sprintf(' %s="%s"', $name, escape((string) $value));
            }
        }
        $picture = '<picture>';
        foreach (['avif' => 'image/avif', 'webp' => 'image/webp'] as $format => $mime) {
            if (!empty($srcsetByFormat[$format])) {
                $picture .= sprintf('<source type="%s" srcset="%s" sizes="%s">', $mime, escape(implode(', ', $srcsetByFormat[$format])), escape($sizes));
            }
        }
        $picture .= sprintf('<img src="%s" srcset="%s" sizes="%s" width="%d" height="%d" alt="%s" class="%s" loading="%s" decoding="async"%s%s></picture>', escape(asset((string) $fallback)), escape(implode(', ', $srcsetByFormat['jpg'])), escape($sizes), (int) $media['width'], (int) $media['height'], escape($alt), escape($class), $loading, $fetchPriority, $extraAttributes);
        return $picture;
    }
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
        $extraAttributes,
    );
}

function video_manifest(): array
{
    static $manifest;
    if ($manifest !== null) {
        return $manifest;
    }
    $path = APP_ROOT . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'video-manifest.json';
    if (!is_file($path)) {
        return $manifest = [];
    }
    $decoded = json_decode((string) file_get_contents($path), true);
    return $manifest = is_array($decoded) ? $decoded : [];
}

function find_video(string $manifestKey, string $videoId): ?array
{
    $videos = video_manifest()['projects'][$manifestKey]['videos'] ?? [];
    if (!is_array($videos)) {
        return null;
    }
    foreach ($videos as $video) {
        if (is_array($video) && ($video['id'] ?? '') === $videoId) {
            return $video;
        }
    }
    return null;
}

function project_animation(array $project): ?array
{
    $animation = $project['media']['animation'] ?? null;
    if (!is_array($animation)) {
        return null;
    }
    $manifest = (string) ($animation['manifest'] ?? '');
    $id = (string) ($animation['id'] ?? '');
    return $manifest !== '' && $id !== '' ? find_video($manifest, $id) : null;
}

function lazy_video(array $video, string $class = '', bool $priority = false, array $attributes = []): string
{
    $sources = $video['sources'] ?? [];
    if (!is_array($sources) || $sources === []) {
        return '';
    }
    krsort($sources, SORT_NUMERIC);
    $source = reset($sources);
    $src = is_array($source) ? (string) ($source['src'] ?? '') : '';
    $poster = (string) ($video['poster'] ?? '');
    $width = max(1, (int) ($video['width'] ?? 1920));
    $height = max(1, (int) ($video['height'] ?? 1080));
    if ($src === '' || $poster === '') {
        return '';
    }
    $extra = '';
    foreach ($attributes as $name => $value) {
        if (preg_match('/^(data-[a-z0-9-]+|aria-[a-z0-9-]+)$/', (string) $name) === 1) {
            $extra .= sprintf(' %s="%s"', $name, escape((string) $value));
        }
    }
    return sprintf(
        '<video class="%s" width="%d" height="%d" poster="%s" preload="none" muted loop playsinline data-lazy-video data-video-src="%s"%s%s></video>',
        escape($class),
        $width,
        $height,
        escape(asset($poster)),
        escape(asset($src)),
        $priority ? ' data-lazy-video-priority' : '',
        $extra,
    );
}
