<?php
$galleryItems = $data['galleryItems'] ?? ($galleryItems ?? []);
$firstSlide = $slides[0] ?? null;
$heroImage = !empty($firstSlide['image']) ? '/storage/' . ltrim((string) $firstSlide['image'], '/') : '';
$heroTitle = trim((string) ($profile['name'] ?? '')) ?: SCHOOL_NAME;
$heroSummary = trim((string) ($profile['motto'] ?? '')) ?: trim((string) ($profile['tagline'] ?? ''));
$primaryHref = Security::safeLinkUrl((string) ($firstSlide['button_url'] ?? '')) ?: '/profil';
$primaryLabel = trim((string) ($firstSlide['button_text'] ?? '')) ?: $copy('home_primary_button_label', 'Tentang Kami');
?>
<?php
$homeScope = get_defined_vars();
$renderHome = static function (string $block) use ($homeScope): void {
    extract($homeScope, EXTR_SKIP);
    require __DIR__ . '/next/blocks/' . $block . '.php';
};
$homeLayouts = [
    'nawala-indigo' => 'portal',
    'arunika-emerald' => 'campus',
    'wibawa-crimson' => 'editorial',
    'samudra-cendekia' => 'explore',
];
require __DIR__ . '/next/' . $homeLayouts[$theme] . '.php';
