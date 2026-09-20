<section class="academic-section"><article class="academic-shell academic-detail academic-card">
    <a href="/">← Beranda</a><p class="academic-eyebrow">Kepala Sekolah</p><h1><?= e($title) ?></h1>
    <?php if (!empty($profile['principal_photo'])): ?><img class="academic-principal-photo" src="/storage/<?= e($profile['principal_photo']) ?>" alt="<?= e($profile['principal_name'] ?? 'Kepala Sekolah') ?>" width="320" height="400"><?php endif; ?>
    <p><strong><?= e($profile['principal_name'] ?? 'Kepala Sekolah') ?></strong></p>
    <div class="prose max-w-none"><?= Security::sanitizeHtml($profile['welcome_message'] ?? '') ?></div>
</article></section>
