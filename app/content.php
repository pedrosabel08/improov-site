<?php

declare(strict_types=1);

function site_content(): array
{
    $postalAddress = [
        'streetAddress' => 'Rua Bahia, 988 - Bairro do Salto',
        'addressLocality' => 'Blumenau',
        'addressRegion' => 'SC',
        'addressCountry' => 'BR',
    ];

    return [
        'name' => 'Improov',
        'email' => 'contato@improov.com.br',
        'phoneDisplay' => '+55 47 99108-7014',
        'phone' => '+5547991087014',
        'address' => sprintf('%s, %s - %s', $postalAddress['streetAddress'], $postalAddress['addressLocality'], $postalAddress['addressRegion']),
        'postalAddress' => $postalAddress,
        'hours' => 'Segunda a sexta, das 9h às 18h',
        'social' => [
            'Instagram' => 'https://instagram.com/improovbr',
            'LinkedIn' => 'https://br.linkedin.com/company/improovbr',
            'YouTube' => 'https://www.youtube.com/@ImproovBR/videos',
        ],
    ];
}

function ui_text(string $key): string
{
    static $translations = [
        'pt-BR' => [
            'home.eyebrow' => 'Imagens que',
            'home.title' => 'Fazem sentir antes de existir',
            'home.intro' => 'Produzimos imagens 3D, animações 3D e filmes para apresentar empreendimentos imobiliários antes da construção, conectando arquitetura, arte e emoção.',
            'footer.description' => 'Criamos imagens 3D, animações 3D e filmes que dão vida a empreendimentos antes de serem construídos, conectando arquitetura, arte e emoção.',
            'cta.eyebrow' => 'Vamos conversar?',
            'cta.title' => 'Seu próximo projeto começa com uma boa imagem.',
            'cta.action' => 'Fale conosco',
            'home.projectsTitle' => 'Conheça alguns dos nossos trabalhos.',
            'home.pillar1' => 'Foco no essencial',
            'home.pillar1Text' => 'Valorizamos a intenção do projeto e o que realmente importa.',
            'home.pillar2' => 'Tecnologia e arte',
            'home.pillar2Text' => 'Ferramentas avançadas a serviço da criatividade.',
            'home.pillar3' => 'Parceria e entrega',
            'home.pillar3Text' => 'Caminhamos juntos em todas as etapas.',
            'home.pillar4' => 'Impacto real',
            'home.pillar4Text' => 'Imagens que comunicam valor e despertam desejo.',
            'faq.eyebrow' => 'Dúvidas comuns',
            'faq.title' => 'Sobre o trabalho da Improov',
            'faq.q1' => 'Que materiais a Improov produz?',
            'faq.a1' => 'Criamos imagens 3D, animações 3D, filmes, plantas humanizadas e outras experiências visuais para empreendimentos imobiliários.',
            'faq.q2' => 'Para quem são esses materiais?',
            'faq.a2' => 'Nosso trabalho apoia a comunicação de incorporadoras, construtoras e equipes envolvidas em empreendimentos imobiliários.',
            'faq.q3' => 'Como conversar sobre um projeto?',
            'faq.a3' => 'Entre em contato pelo formulário ou WhatsApp e conte à nossa equipe o que você precisa apresentar sobre o empreendimento.',
            'about.eyebrow' => 'Quem Somos',
            'about.title' => 'Artesãos Digitais',
            'about.intro' => 'A Improov tem 20 anos de experiência no desenvolvimento de materiais para lançamentos imobiliários. Acreditamos que grandes empreendimentos não são vendidos apenas por suas características: eles conquistam pessoas pelas emoções que despertam.',
            'about.manifestoTitle' => 'Grandes empreendimentos conquistam pessoas pelas emoções que despertam.',
            'about.manifestoP1' => 'Somos uma empresa especializada em comunicação para o mercado imobiliário, criando imagens, filmes, animações e experiências visuais capazes de transformar projetos em desejo.',
            'about.manifestoP2' => 'Mais do que representar aquilo que ainda será construído, traduzimos a essência de cada empreendimento. Buscamos revelar sua identidade, sua atmosfera e a história que existe por trás da arquitetura.',
            'about.manifestoP3' => 'Nossa metodologia une direção criativa, arte, estratégia e tecnologia para desenvolver materiais que fortalecem marcas, encantam clientes e potencializam resultados comerciais.',
            'about.manifestoP4' => 'Cada detalhe é pensado para comunicar com verdade. Cada enquadramento, cada luz, cada movimento e cada narrativa existem para despertar sentimentos.',
            'about.heartmadeLead' => 'Chamamos essa filosofia de',
            'about.heartmadeBelief' => 'Porque acreditamos que a tecnologia, por si só, impressiona. Mas é o olhar humano que emociona.',
            'about.heartmadeTeam' => 'Ao longo da nossa trajetória, reunimos uma equipe multidisciplinar apaixonada por excelência e comprometida em entregar materiais que elevam o posicionamento de incorporadoras, construtoras e empreendimentos.',
            'about.heartmadeExperience' => 'Não produzimos apenas imagens.',
            'about.heartmadeClosing' => 'Criamos experiências que fazem pessoas imaginarem, desejarem e acreditarem em um lugar antes mesmo de ele existir.',
            'about.heartmadeFinal' => 'É assim que transformamos arquitetura em comunicação. E comunicação em valor.',
            'about.studioEyebrow' => 'Nosso Estúdio',
            'about.studioTitle' => 'O lugar onde as ideias ganham vida.',
            'about.studioText' => 'Um ambiente acolhedor, técnico e criativo, onde colaboração e atenção aos detalhes se encontram todos os dias.',
            'projects.eyebrow' => 'Projetos',
            'projects.title' => 'Imagens que revelam o essencial de cada projeto.',
            'projects.intro' => 'Conheça projetos de visualização arquitetônica, imagens 3D, animações 3D e filmes para o mercado imobiliário.',
            'careers.eyebrow' => 'Trabalhe Conosco',
            'careers.title' => 'Faça parte do time que transforma ideias em experiências visuais.',
            'careers.intro' => 'Somos movidos por curiosidade, colaboração e paixão por imagem.',
            'contact.eyebrow' => 'Contato',
            'contact.title' => 'Vamos conversar sobre seu próximo projeto.',
            'contact.intro' => 'Criamos imagens e experiências visuais que transformam projetos de arquitetura e imobiliário em conexões reais.',
            'contact.address' => 'Rua Bahia, 988 - Bairro do Salto, Blumenau - SC',
            'contact.hours' => 'Segunda a sexta, das 9h às 18h',
        ],
        'en' => [
            'home.eyebrow' => 'Images that',
            'home.title' => 'Make you feel before they exist',
            'home.intro' => 'We create 3D architectural images, animations and films that bring real estate developments to life before they are built, connecting architecture, art and emotion.',
            'footer.description' => 'We create 3D images, animations and films that bring real estate developments to life before they are built, connecting architecture, art and emotion.',
            'cta.eyebrow' => 'Shall we talk?',
            'cta.title' => 'Your next project starts with a great image.',
            'cta.action' => 'Talk to us',
            'home.projectsTitle' => 'Discover some of our work.',
            'home.pillar1' => 'Focus on the essential',
            'home.pillar1Text' => 'We value the intention of each project and what truly matters.',
            'home.pillar2' => 'Technology and art',
            'home.pillar2Text' => 'Advanced tools at the service of creativity.',
            'home.pillar3' => 'Partnership and delivery',
            'home.pillar3Text' => 'We work together throughout every stage.',
            'home.pillar4' => 'Real impact',
            'home.pillar4Text' => 'Images that communicate value and inspire desire.',
            'faq.eyebrow' => 'Common questions',
            'faq.title' => 'About Improov’s work',
            'faq.q1' => 'What materials does Improov create?',
            'faq.a1' => 'We create 3D images, 3D animations, films, humanized floor plans and other visual experiences for real estate developments.',
            'faq.q2' => 'Who are these materials for?',
            'faq.a2' => 'Our work supports communication for real estate developers, builders and teams involved in property developments.',
            'faq.q3' => 'How can I discuss a project?',
            'faq.a3' => 'Contact us through the form or WhatsApp and tell our team what you need to present about the development.',
            'about.eyebrow' => 'About Us',
            'about.title' => 'About Us',
            'about.intro' => 'Improov has 20 years of experience creating materials for real estate launches. We believe great developments are not sold by their features alone: they win people over through the emotions they awaken.',
            'about.manifestoTitle' => 'Great developments win people over through the emotions they awaken.',
            'about.manifestoP1' => 'We specialize in communication for the real estate market, creating images, films, 3D animations and visual experiences capable of turning projects into desire.',
            'about.manifestoP2' => 'More than representing what has yet to be built, we translate the essence of each development. We seek to reveal its identity, atmosphere and the story behind its architecture.',
            'about.manifestoP3' => 'Our methodology combines creative direction, art, strategy and technology to develop materials that strengthen brands, delight clients and enhance commercial results.',
            'about.manifestoP4' => 'Every detail is designed to communicate truthfully. Every frame, light, movement and narrative exists to awaken feelings.',
            'about.heartmadeLead' => 'We call this philosophy',
            'about.heartmadeBelief' => 'Because we believe technology alone impresses. It is the human eye that moves us.',
            'about.heartmadeTeam' => 'Throughout our journey, we have built a multidisciplinary team passionate about excellence and committed to delivering materials that elevate the positioning of developers, builders and developments.',
            'about.heartmadeExperience' => 'We do not simply produce images.',
            'about.heartmadeClosing' => 'We create experiences that make people imagine, desire and believe in a place before it even exists.',
            'about.heartmadeFinal' => 'This is how we transform architecture into communication. And communication into value.',
            'about.studioEyebrow' => 'Our Studio',
            'about.studioTitle' => 'The place where ideas come to life.',
            'about.studioText' => 'A welcoming, technical and creative environment where collaboration and attention to detail meet every day.',
            'projects.eyebrow' => 'Projects',
            'projects.title' => 'Images that reveal the essence of every project.',
            'projects.intro' => 'Explore architectural visualization projects, including 3D images, 3D animations and films for real estate.',
            'careers.eyebrow' => 'Careers',
            'careers.title' => 'Join the team that transforms ideas into visual experiences.',
            'careers.intro' => 'We are driven by curiosity, collaboration and a passion for imagery.',
            'contact.eyebrow' => 'Contact',
            'contact.title' => 'Let’s talk about your next project.',
            'contact.intro' => 'We create visual experiences that turn architecture and real estate projects into real connections.',
            'contact.address' => 'Rua Bahia, 988 - Bairro do Salto, Blumenau - SC, Brazil',
            'contact.hours' => 'Monday to Friday, 9 am to 6 pm',
        ],
        'es' => [
            'home.eyebrow' => 'Imágenes que',
            'home.title' => 'Hacen sentir antes de existir',
            'home.intro' => 'Creamos imágenes arquitectónicas 3D, animaciones 3D y películas que dan vida a proyectos inmobiliarios antes de construirse, conectando arquitectura, arte y emoción.',
            'footer.description' => 'Creamos imágenes 3D, animaciones 3D y películas que dan vida a proyectos inmobiliarios antes de construirse, conectando arquitectura, arte y emoción.',
            'cta.eyebrow' => '¿Hablamos?',
            'cta.title' => 'Tu próximo proyecto comienza con una buena imagen.',
            'cta.action' => 'Hablar con nosotros',
            'home.projectsTitle' => 'Conoce algunos de nuestros trabajos.',
            'home.pillar1' => 'Foco en lo esencial',
            'home.pillar1Text' => 'Valoramos la intención del proyecto y lo que realmente importa.',
            'home.pillar2' => 'Tecnología y arte',
            'home.pillar2Text' => 'Herramientas avanzadas al servicio de la creatividad.',
            'home.pillar3' => 'Alianza y entrega',
            'home.pillar3Text' => 'Caminamos juntos en cada etapa.',
            'home.pillar4' => 'Impacto real',
            'home.pillar4Text' => 'Imágenes que comunican valor y despiertan deseo.',
            'faq.eyebrow' => 'Preguntas frecuentes',
            'faq.title' => 'Sobre el trabajo de Improov',
            'faq.q1' => '¿Qué materiales produce Improov?',
            'faq.a1' => 'Creamos imágenes 3D, animaciones 3D, películas, plantas humanizadas y otras experiencias visuales para proyectos inmobiliarios.',
            'faq.q2' => '¿Para quiénes son estos materiales?',
            'faq.a2' => 'Nuestro trabajo apoya la comunicación de desarrolladores inmobiliarios, constructoras y equipos involucrados en proyectos inmobiliarios.',
            'faq.q3' => '¿Cómo puedo conversar sobre un proyecto?',
            'faq.a3' => 'Contáctanos por el formulario o WhatsApp y cuéntale a nuestro equipo qué necesitas presentar sobre el proyecto.',
            'about.eyebrow' => 'Quiénes Somos',
            'about.title' => 'Quiénes Somos',
            'about.intro' => 'Improov tiene 20 años de experiencia desarrollando materiales para lanzamientos inmobiliarios. Creemos que los grandes proyectos no se venden solo por sus características: conquistan a las personas por las emociones que despiertan.',
            'about.manifestoTitle' => 'Los grandes proyectos conquistan a las personas por las emociones que despiertan.',
            'about.manifestoP1' => 'Somos una empresa especializada en comunicación para el mercado inmobiliario, creando imágenes, películas, animaciones 3D y experiencias visuales capaces de transformar proyectos en deseo.',
            'about.manifestoP2' => 'Más que representar aquello que aún será construido, traducimos la esencia de cada proyecto. Buscamos revelar su identidad, su atmósfera y la historia que existe detrás de la arquitectura.',
            'about.manifestoP3' => 'Nuestra metodología une dirección creativa, arte, estrategia y tecnología para desarrollar materiales que fortalecen marcas, encantan a clientes y potencian resultados comerciales.',
            'about.manifestoP4' => 'Cada detalle está pensado para comunicar con verdad. Cada encuadre, cada luz, cada movimiento y cada narrativa existen para despertar sentimientos.',
            'about.heartmadeLead' => 'Llamamos a esta filosofía',
            'about.heartmadeBelief' => 'Porque creemos que la tecnología, por sí sola, impresiona. Pero es la mirada humana la que emociona.',
            'about.heartmadeTeam' => 'A lo largo de nuestra trayectoria, reunimos un equipo multidisciplinario apasionado por la excelencia y comprometido con entregar materiales que elevan el posicionamiento de desarrolladores, constructoras y proyectos.',
            'about.heartmadeExperience' => 'No producimos solo imágenes.',
            'about.heartmadeClosing' => 'Creamos experiencias que hacen que las personas imaginen, deseen y crean en un lugar incluso antes de que exista.',
            'about.heartmadeFinal' => 'Así transformamos la arquitectura en comunicación. Y la comunicación en valor.',
            'about.studioEyebrow' => 'Nuestro Estudio',
            'about.studioTitle' => 'El lugar donde las ideas cobran vida.',
            'about.studioText' => 'Un ambiente acogedor, técnico y creativo donde colaboración y atención al detalle se encuentran cada día.',
            'projects.eyebrow' => 'Proyectos',
            'projects.title' => 'Imágenes que revelan lo esencial de cada proyecto.',
            'projects.intro' => 'Conoce proyectos de visualización arquitectónica, imágenes 3D, animaciones 3D y películas para el mercado inmobiliario.',
            'careers.eyebrow' => 'Trabaja con Nosotros',
            'careers.title' => 'Forma parte del equipo que transforma ideas en experiencias visuales.',
            'careers.intro' => 'Nos mueven la curiosidad, la colaboración y la pasión por la imagen.',
            'contact.eyebrow' => 'Contacto',
            'contact.title' => 'Hablemos de tu próximo proyecto.',
            'contact.intro' => 'Creamos experiencias visuales que convierten proyectos de arquitectura e inmobiliarios en conexiones reales.',
            'contact.address' => 'Rua Bahia, 988 - Bairro do Salto, Blumenau - SC, Brasil',
            'contact.hours' => 'Lunes a viernes, de 9h a 18h',
        ],
    ];

    $language = current_language();
    return $translations[$language][$key] ?? $translations['pt-BR'][$key] ?? $key;
}

function page_metadata(string $key): array
{
    $pages = [
        'home' => [
            'title' => 'IMPROOV | Visualização arquitetônica 3D para o mercado imobiliário',
            'description' => 'A IMPROOV transforma projetos imobiliários em experiências visuais com imagens 3D, animações 3D e filmes que comunicam, encantam e valorizam cada empreendimento.',
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
            'description' => 'Conheça projetos de imagens 3D, animações 3D, filmes e materiais visuais produzidos pela Improov para o mercado imobiliário.',
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
            'description' => 'Fale com a Improov sobre imagens 3D, animações 3D, filmes e experiências visuais para o seu empreendimento.',
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
            'es' => ['IMPROOV | Visualización arquitectónica 3D para el mercado inmobiliario', 'IMPROOV transforma proyectos inmobiliarios en experiencias visuales con imágenes 3D, animaciones 3D y películas que comunican, inspiran y generan valor.'],
        ],
        'quem-somos' => [
            'en' => ['IMPROOV | Communication and Art for the Real Estate Market', 'Learn about Improov’s studio, philosophy and work for the real estate market.'],
            'es' => ['IMPROOV | Comunicación y Arte para el Mercado Inmobiliario', 'Conozca el estudio, la filosofía y el trabajo de Improov para el mercado inmobiliario.'],
        ],
        'projetos' => [
            'en' => ['IMPROOV | Portfolio of 3D Images and Real Estate Films', 'Explore 3D images, animations, films and visual materials produced by Improov for real estate.'],
            'es' => ['IMPROOV | Portafolio de Imágenes 3D y Películas Inmobiliarias', 'Conozca proyectos de imágenes 3D, animaciones 3D, películas y materiales visuales producidos por Improov.'],
        ],
        'trabalhe-conosco' => [
            'en' => ['IMPROOV | Work With Us', 'Join the team that transforms ideas into visual experiences.'],
            'es' => ['IMPROOV | Trabaja con Nosotros', 'Forma parte del equipo que transforma ideas en experiencias visuales.'],
        ],
        'contato' => [
            'en' => ['Talk to Improov | 3D Images and Real Estate Films', 'Talk to Improov about 3D images, animations, films and visual experiences for your project.'],
            'es' => ['Habla con Improov | Imágenes 3D y Películas Inmobiliarias', 'Hable con Improov sobre imágenes 3D, animaciones 3D, películas y experiencias visuales para su proyecto.'],
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
