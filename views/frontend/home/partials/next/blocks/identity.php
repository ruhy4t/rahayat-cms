<div class="composition-identity">
    <?php if (!empty($profile['tagline']) && $profile['tagline'] !== $heroSummary): ?><p class="composition-eyebrow"><?= e($profile['tagline']) ?></p><?php endif; ?>
    <h1><?= e($heroTitle) ?></h1>
    <?php if ($heroSummary !== ''): ?><p><?= e($heroSummary) ?></p><?php endif; ?>
    <div class="next-actions">
        <a class="next-button next-button--primary" href="<?= e($primaryHref) ?>"><?= e($primaryLabel) ?></a>
        <a class="next-button composition-contact" href="<?= !empty($spmbPublic['active']) ? e($spmbPublic['url']) : '/kontak' ?>"<?= !empty($spmbPublic['active']) && ($spmbPublic['target'] ?? '') === '_blank' ? ' target="_blank" rel="noopener noreferrer"' : '' ?>><?= e(!empty($spmbPublic['active']) ? ($spmbPublic['label'] ?? 'Info SPMB') : $copy('home_contact_button_label','Hubungi Kami')) ?></a>
    </div>
</div>
