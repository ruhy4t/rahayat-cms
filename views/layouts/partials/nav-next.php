<?php
$schoolName = (string) ($profile['name'] ?? SCHOOL_NAME);
$renderNextMenus = static function (array $items) use (&$renderNextMenus): void {
    echo '<ul class="next-menu-list">';
    foreach ($items as $item) {
        $href = Security::safeLinkUrl((string) ($item['url'] ?? '')) ?: '/';
        $external = ($item['target'] ?? '_self') === '_blank';
        echo '<li>';
        if (!empty($item['children'])) {
            echo '<div class="next-menu-parent"><a href="' . e($href) . '"' . ($external ? ' target="_blank" rel="noopener noreferrer"' : '') . '>' . e($item['title']) . '</a><details class="next-menu-branch"><summary aria-label="' . e($item['title']) . '"><span aria-hidden="true">&#9662;</span></summary><div class="next-submenu">';
            $renderNextMenus($item['children']);
            echo '</div></details></div>';
        } else {
            echo '<a href="' . e($href) . '"' . ($external ? ' target="_blank" rel="noopener noreferrer"' : '') . '>' . e($item['title']) . '</a>';
        }
        echo '</li>';
    }
    echo '</ul>';
};
?>
<header class="next-header">
    <div class="next-utility"><div class="next-shell"><span><?= e($profile['motto'] ?? '') ?></span><div><?php if (!empty($profile['phone'])): ?><a href="tel:<?= e(preg_replace('/[^+0-9]/', '', (string) $profile['phone'])) ?>"><?= e($profile['phone']) ?></a><?php endif; ?><?php if (!empty($profile['email'])): ?><span><?= e($profile['email']) ?></span><?php endif; ?></div></div></div>
    <div class="next-shell next-masthead"><a class="next-brand" href="/">
        <?php if (!empty($profile['logo'])): ?><img src="/storage/<?= e($profile['logo']) ?>" alt=""><?php endif; ?>
        <span><strong><?= e($schoolName) ?></strong><?php if (!empty($profile['tagline'])): ?><small><?= e($profile['tagline']) ?></small><?php endif; ?></span>
    </a><nav class="next-desktop-nav" aria-label="Navigasi utama"><?php $renderNextMenus($headerMenus ?? []); ?></nav>
    <details class="next-mobile-nav"><summary aria-label="Buka menu navigasi"><span aria-hidden="true">☰</span></summary><nav aria-label="Navigasi mobile"><?php $renderNextMenus($headerMenus ?? []); ?></nav></details></div>
</header>
