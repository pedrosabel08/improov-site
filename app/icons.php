<?php

declare(strict_types=1);

/**
 * Small local icon set used by the shared site components.
 * Keeping the SVGs here avoids font-dependent Unicode symbols and external icon libraries.
 */
function site_icon(string $name, string $class = ''): string
{
    $paths = [
        'eye' => '<path d="M2.5 12s3.5-5 9.5-5 9.5 5 9.5 5-3.5 5-9.5 5-9.5-5-9.5-5Z"/><circle cx="12" cy="12" r="2.5"/>',
        'layers' => '<rect x="5" y="5" width="10" height="10" rx="1.5"/><rect x="9" y="9" width="10" height="10" rx="1.5"/>',
        'users' => '<path d="M16 20v-1.5a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4V20"/><circle cx="9.5" cy="7" r="3"/><path d="M16 4.5a3 3 0 0 1 0 5.8M21 20v-1.5a4 4 0 0 0-3-3.87"/>',
        'spark' => '<path d="m12 2 1.35 6.65L20 10l-6.65 1.35L12 18l-1.35-6.65L4 10l6.65-1.35L12 2Z"/><path d="m19 16 .55 2.45L22 19l-2.45.55L19 22l-.55-2.45L16 19l2.45-.55L19 16Z"/>',
        'lightbulb' => '<path d="M9 18h6M10 21h4M8.5 14.5A6 6 0 1 1 15.5 14c-.9.7-1.5 1.5-1.5 2.5h-4c0-1-.6-1.8-1.5-2.5Z"/>',
        'monitor' => '<rect x="3" y="4" width="18" height="13" rx="1.5"/><path d="M8 21h8M12 17v4"/>',
        'growth' => '<path d="M4 19V5M4 19h17"/><path d="m7 15 4-4 3 2 6-7M16 6h4v4"/>',
        'pin' => '<path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>',
        'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>',
        'linkedin' => '<path d="M5 9v10M5 5.5v.01M10 19v-6a4 4 0 0 1 8 0v6M10 9v10"/><path d="M3 3h18v18H3z"/>',
        'youtube' => '<path d="m10 15 5-3-5-3v6Z" fill="currentColor" stroke="none"/><path d="M21 12c0 4-1 5-1 5s-1 1-5 1H9c-4 0-5-1-5-1s-1-1-1-5 1-5 1-5 1-1 5-1h6c4 0 5 1 5 1s1 1 1 5Z"/>',
        'whatsapp' => '<path d="M20.5 3.5A11 11 0 0 0 3.2 16.8L2 22l5.3-1.2A11 11 0 1 0 20.5 3.5Z"/><path d="M8.2 7.5c.3-.3.7-.3 1-.1l1.3 1.9c.2.3.2.6 0 .9l-.7.8c.8 1.5 1.9 2.6 3.4 3.4l.8-.7c.3-.2.6-.2.9 0l1.9 1.3c.3.2.3.7.1 1-.5.8-1.4 1.3-2.3 1.1-4.4-1-7.8-4.4-8.8-8.8-.2-.9.3-1.8 1.1-2.3Z"/>',
        'arrow-down' => '<path d="M12 4v15M6.5 13.5 12 19l5.5-5.5"/>',
        'arrow-left' => '<path d="M19 12H5M10.5 6.5 5 12l5.5 5.5"/>',
        'arrow-right' => '<path d="M5 12h14M13.5 6.5 19 12l-5.5 5.5"/>',
        'arrow-up-right' => '<path d="M6 18 18 6M9 6h9v9"/>',
        'close' => '<path d="m6 6 12 12M18 6 6 18"/>',
        'play' => '<path d="m9 6 9 6-9 6V6Z" fill="currentColor" stroke="none"/>',
        'pause' => '<path d="M8 6v12M16 6v12"/>',
    ];

    $path = $paths[$name] ?? $paths['spark'];
    $classAttribute = $class === '' ? '' : ' class="' . escape($class) . '"';
    return '<svg' . $classAttribute . ' width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $path . '</svg>';
}
