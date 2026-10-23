<?php
$schoolName = (string) ($profile['name'] ?? SCHOOL_NAME);
$footerText = str_replace(['{year}', '{school}'], [date('Y'), $schoolName], (string) ($settings['footer_text'] ?? ''));

$renderFooterLinks = static function (array $items, bool $root = true) use (&$renderFooterLinks): void {
    echo $root ? '<ul class="next-footer-menu">' : '<ul>';
    foreach ($items as $item) {
        $href = Security::safeLinkUrl((string) ($item['url'] ?? '')) ?: '/';
        $external = ($item['target'] ?? '_self') === '_blank';
        echo '<li><a href="' . e($href) . '"' . ($external ? ' target="_blank" rel="noopener noreferrer"' : '') . '>' . e($item['title']) . '</a>';
        if (!empty($item['children'])) { $renderFooterLinks($item['children'], false); }
        echo '</li>';
    }
    echo '</ul>';
};
?>
<footer class="next-footer"><div class="next-shell">
    <div class="next-footer-grid"><div class="next-footer-identity">
        <?php if (!empty($profile['logo'])): ?><img src="/storage/<?= e($profile['logo']) ?>" alt="" loading="lazy"><?php endif; ?>
        <h2><?= e($schoolName) ?></h2><p><?= e($profile['motto'] ?? $profile['tagline'] ?? '') ?></p>
        <?php foreach (['address', 'phone', 'email'] as $contactKey): ?><?php if (!empty($profile[$contactKey])): ?><p><?= e($profile[$contactKey]) ?></p><?php endif; ?><?php endforeach; ?>
    </div><nav aria-label="Navigasi footer"><h2 class="next-footer-heading"><?= e(PublicTheme::text($settings,'explore')) ?></h2><?php $renderFooterLinks($footerMenus ?? []); ?></nav>
    <div class="next-footer-social"><h2 class="next-footer-heading"><?= e(PublicTheme::text($settings,'connect')) ?></h2><?php foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'twitter' => 'X', 'youtube' => 'YouTube'] as $socialKey => $socialName): ?><?php $socialUrl = Security::safeLinkUrl((string) ($settings['social_' . $socialKey] ?? '')); ?><?php if ($socialUrl !== ''): ?><a href="<?= e($socialUrl) ?>" target="_blank" rel="noopener noreferrer"><?= e($socialName) ?></a><?php endif; ?><?php endforeach; ?><?php include __DIR__ . '/visitor-statistics.php'; ?></div></div>
    <?php if ($footerText !== ''): ?><div class="next-copyright"><?= e($footerText) ?></div><?php endif; ?>
</div></footer>
