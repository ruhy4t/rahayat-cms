<?php if (!empty($profile['vision'])): ?>
<section class="composition-story next-shell"><div><p class="next-kicker"><?= e(PublicTheme::text($settings,'vision')) ?></p><h2><?= e($profile['name'] ?? SCHOOL_NAME) ?></h2></div><div class="next-richtext"><?= Security::sanitizeHtml((string)$profile['vision']) ?><a href="/profil"><?= e(PublicTheme::text($settings,'profile')) ?></a></div></section>
<?php endif; ?>
