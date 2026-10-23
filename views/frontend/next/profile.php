<?php $schoolName = (string) ($profile['name'] ?? SCHOOL_NAME); ?>
<section class="next-page-banner"><div class="next-shell"><p><?= e($schoolName) ?></p><h1><?= e(PublicTheme::text($settings,'profile')) ?></h1><p><?= e($profile['motto'] ?? '') ?></p></div></section>
<div class="next-shell next-profile-layout">
    <aside class="next-local-nav"><nav aria-label="Bagian profil">
        <?php foreach (['vision', 'mission', 'history', 'welcome_message'] as $key): ?><?php if (!empty($profile[$key])): ?><a href="#profile-<?= e($key) ?>"><?= e(PublicTheme::text($settings,$key)) ?></a><?php endif; ?><?php endforeach; ?>
    </nav><dl><?php foreach (['npsn', 'accreditation', 'established_year'] as $key): ?><?php if (!empty($profile[$key])): ?><div><dt><?= e(PublicTheme::text($settings,$key)) ?></dt><dd><?= e($profile[$key]) ?></dd></div><?php endif; ?><?php endforeach; ?></dl></aside>
    <div class="next-profile-content">
        <?php foreach (['vision' => 'Visi', 'mission' => 'Misi', 'history' => 'Sejarah', 'welcome_message' => 'Sambutan'] as $key => $label): ?><?php if (!empty($profile[$key])): ?>
        <section id="profile-<?= e($key) ?>" class="next-document-panel"><h2><?= e(PublicTheme::text($settings,$key)) ?></h2><?php if ($key === 'welcome_message' && !empty($profile['principal_name'])): ?><p class="next-byline"><?= e($profile['principal_name']) ?></p><?php endif; ?><div class="next-richtext"><?= Security::sanitizeHtml((string) $profile[$key]) ?></div></section>
        <?php endif; ?><?php endforeach; ?>
        <?php if (!empty($facilities)): ?><section class="next-document-panel"><h2><?= e($settings['home_facilities_title'] ?? 'Fasilitas Sekolah') ?></h2><div class="next-profile-facilities"><?php foreach ($facilities as $facility): ?><article><?php if (!empty($facility['image'])): ?><img src="/storage/<?= e($facility['image']) ?>" alt="" loading="lazy"><?php endif; ?><h3><?= e($facility['name']) ?></h3><p><?= e($facility['description'] ?? '') ?></p></article><?php endforeach; ?></div></section><?php endif; ?>
    </div>
</div>
