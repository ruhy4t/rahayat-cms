<?php
$messageSummary = trim((string) ($settings['principal_message_summary'] ?? ''));
if (!empty($settings['principal_message_enabled']) && $messageSummary !== ''):
?>
<section class="academic-section" aria-labelledby="principal-message-title">
    <div class="academic-shell academic-principal">
        <?php if (!empty($profile['principal_photo'])): ?><img class="academic-principal-photo" src="/storage/<?= e($profile['principal_photo']) ?>" alt="<?= e($profile['principal_name'] ?? 'Kepala Sekolah') ?>" loading="lazy" width="320" height="400"><?php endif; ?>
        <div class="academic-principal-copy"><p class="academic-eyebrow">Dari sekolah untuk Anda</p><h2 id="principal-message-title"><?= e($settings['principal_message_title'] ?? 'Pesan Kepala Sekolah') ?></h2>
            <p class="academic-principal-message"><?= nl2br(e($messageSummary)) ?></p>
            <p><strong><?= e($profile['principal_name'] ?? 'Kepala Sekolah') ?></strong><br><span>Kepala Sekolah</span></p>
            <?php if (trim(strip_tags($profile['welcome_message'] ?? '')) !== ''): ?><a class="academic-button" href="/pesan-kepala-sekolah"><?= e($settings['principal_message_button'] ?? 'Baca Selengkapnya') ?> →</a><?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>
