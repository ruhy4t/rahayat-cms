<?php $pageHeading = PublicTheme::text($settings, 'gtk'); include __DIR__ . '/banner.php'; $hasStaff = false; ?>
<div class="next-shell next-directory protected-content">
<?php foreach ($groupedStaff ?? [] as $groupKey => $group): ?><?php if (empty($group['items'])) { continue; } $hasStaff = true; ?>
<section class="next-staff-group"><div class="next-heading"><h2><?= e($group['name']) ?></h2></div><div class="next-staff-grid<?= $groupKey === 'principal' ? ' next-staff-grid--principal' : '' ?>">
<?php foreach ($group['items'] as $staff): ?><article class="next-staff-card"><?php if (!empty($staff['photo'])): ?><img src="/storage/<?= e($staff['photo']) ?>" alt="<?= e($staff['name']) ?>" class="protected-img" loading="lazy" decoding="async" draggable="false"><?php else: ?><div class="next-avatar" aria-hidden="true"><?= e(mb_substr($staff['name'], 0, 1)) ?></div><?php endif; ?><div><h3><?= e($staff['name']) ?></h3><p><?= e($staff['position'] ?? '') ?></p></div></article><?php endforeach; ?>
</div></section><?php endforeach; ?><?php if (!$hasStaff): ?><p class="next-empty"><?= e(PublicTheme::text($settings, 'empty')) ?></p><?php endif; ?></div>
