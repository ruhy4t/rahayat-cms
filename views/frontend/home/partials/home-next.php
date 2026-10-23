<?php
$galleryItems = $data['galleryItems'] ?? ($galleryItems ?? []);
$firstSlide = $slides[0] ?? null;
$heroImage = !empty($firstSlide['image']) ? '/storage/' . ltrim((string) $firstSlide['image'], '/') : '';
$heroTitle = trim((string) ($profile['name'] ?? '')) ?: SCHOOL_NAME;
$heroSummary = trim((string) ($profile['motto'] ?? '')) ?: trim((string) ($profile['tagline'] ?? ''));
$primaryHref = Security::safeLinkUrl((string) ($firstSlide['button_url'] ?? '')) ?: '/profil';
$primaryLabel = trim((string) ($firstSlide['button_text'] ?? '')) ?: $copy('home_primary_button_label', 'Tentang Kami');
?>
<section class="next-hero"<?= $heroImage !== '' ? ' style="--hero-image:url(\'' . e($heroImage) . '\')"' : '' ?>>
    <div class="next-hero__veil"></div>
    <div class="next-shell next-hero__content">
        <div class="next-hero__copy">
            <h1><?= e($heroTitle) ?></h1>
            <?php if ($heroSummary !== ''): ?><p class="next-hero__summary"><?= e($heroSummary) ?></p><?php endif; ?>
            <div class="next-actions">
                <a class="next-button next-button--primary" href="<?= e($primaryHref) ?>"><?= e($primaryLabel) ?></a>
                <a class="next-button next-button--ghost" href="<?= !empty($spmbPublic['active']) ? e($spmbPublic['url']) : '/kontak' ?>"<?= !empty($spmbPublic['active']) ? ' target="' . e($spmbPublic['target'] ?? '_self') . '"' : '' ?>><?= e(!empty($spmbPublic['active']) ? ($spmbPublic['label'] ?? 'Info SPMB') : $copy('home_contact_button_label', 'Hubungi Kami')) ?></a>
            </div>
        </div>
    </div>
</section>

<section class="next-stats" aria-label="Statistik sekolah">
    <div class="next-shell next-stats__grid">
        <?php foreach ([
            [$profile['total_students'] ?? null, $copy('home_students_label', 'Murid Aktif')],
            [$profile['total_teachers'] ?? null, $copy('home_teachers_label', 'Guru Profesional')],
            [$profile['accreditation'] ?? null, $copy('home_accreditation_label', 'Akreditasi')],
            [$profile['graduation_rate'] ?? null, $copy('home_graduation_label', 'Kelulusan')],
        ] as [$statValue, $statLabel]): ?>
            <?php if ($statValue !== null && $statValue !== ''): ?><div class="next-stat"><strong><?= e($statValue) ?></strong><span><?= e($statLabel) ?></span></div><?php endif; ?>
        <?php endforeach; ?>
    </div>
</section>

<?php include dirname(__DIR__, 2) . '/academic/principal-card.php'; ?>

<section class="next-section next-section--soft">
    <div class="next-shell">
        <div class="next-heading"><div><p class="next-kicker"><?= e($copy('home_news_eyebrow', 'Informasi Terkini')) ?></p><h2><?= e($copy('home_news_title', 'Berita & Pengumuman')) ?></h2></div><a href="/berita"><?= e(PublicTheme::text($settings,'all')) ?></a></div>
        <div class="next-news-layout">
            <div class="next-news-grid">
                <?php foreach (array_slice($news, 0, 4) as $item): ?>
                    <article class="next-news-card">
                        <a href="/berita/<?= e($item['slug']) ?>" class="next-media">
                            <?php if (!empty($item['image'])): ?><img src="/storage/<?= e($item['image']) ?>" alt="<?= e($item['title']) ?>" loading="lazy" decoding="async"><?php endif; ?>
                        </a>
                        <div class="next-card-body"><p class="next-meta"><?= e(date('d M Y', strtotime($item['published_at'] ?? $item['created_at']))) ?> · <?= e(ucfirst($item['category'] ?? 'Berita')) ?></p><h3><a href="/berita/<?= e($item['slug']) ?>"><?= e($item['title']) ?></a></h3><p><?= e($item['excerpt'] ?? '') ?></p></div>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php if (!empty($upcomingEvents)): ?><aside class="next-agenda"><div class="next-heading next-heading--compact"><h2><?= e($copy('home_agenda_title', PublicTheme::text($settings,'agenda'))) ?></h2><a href="/agenda"><?= e(PublicTheme::text($settings,'all')) ?></a></div><?php foreach ($upcomingEvents as $event): ?><a class="next-event" href="/agenda/<?= (int) $event['id'] ?>"><time><strong><?= e(date('d', strtotime($event['start_date']))) ?></strong><span><?= e(substr(AcademicCalendar::MONTHS[(int) date('n', strtotime($event['start_date']))], 0, 3)) ?></span></time><span><strong><?= e($event['title']) ?></strong><small><?= e($event['location'] ?? '') ?></small></span></a><?php endforeach; ?></aside><?php endif; ?>
        </div>
    </div>
</section>

<?php include dirname(__DIR__, 2) . '/academic/home-section.php'; ?>

<?php if (!empty($facilities) || !empty($ekskul)): ?>
<section class="next-section"><div class="next-shell"><div class="next-heading"><div><p class="next-kicker"><?= e($copy('home_extracurricular_eyebrow', 'Pengembangan Diri')) ?></p><h2><?= e($copy('home_extracurricular_title', 'Program & Fasilitas')) ?></h2></div></div><div class="next-program-grid">
<?php foreach (array_slice($facilities, 0, 4) as $program): ?><article class="next-program"><div><?php if (!empty($program['image'])): ?><img src="/storage/<?= e($program['image']) ?>" alt="" loading="lazy"><?php endif; ?></div><h3><a href="/profil"><?= e($program['name']) ?></a></h3><p><?= e($program['description'] ?? '') ?></p></article><?php endforeach; ?>
<?php foreach (array_slice($ekskul, 0, 4) as $program): ?><article class="next-program"><div><?php if (!empty($program['image'])): ?><img src="/storage/<?= e($program['image']) ?>" alt="" loading="lazy"><?php endif; ?></div><h3><a href="/ekstrakurikuler/<?= (int)$program['id'] ?>"><?= e($program['name']) ?></a></h3><p><?= e($program['description'] ?? '') ?></p></article><?php endforeach; ?>
</div></div></section>
<?php endif; ?>

<?php if (!empty($galleryItems)): ?><section class="next-section next-section--dark"><div class="next-shell"><div class="next-heading"><div><p class="next-kicker"><?= e($copy('home_gallery_eyebrow','Dokumentasi')) ?></p><h2><?= e($copy('home_gallery_title',PublicTheme::text($settings,'gallery'))) ?></h2></div><a href="/galeri"><?= e(PublicTheme::text($settings,'all')) ?></a></div><div class="next-gallery"><?php foreach ($galleryItems as $gallery): ?><?php if (($gallery['type'] ?? 'image') === 'image' && !empty($gallery['file_path'])): ?><figure><img src="/storage/<?= e($gallery['file_path']) ?>" alt="<?= e($gallery['title'] ?? '') ?>" loading="lazy" decoding="async"><figcaption><?= e($gallery['title'] ?? '') ?></figcaption></figure><?php endif; ?><?php endforeach; ?></div></div></section><?php endif; ?>

<?php if (!empty($testimonials)): ?><section class="next-section"><div class="next-shell"><div class="next-heading"><div><p class="next-kicker"><?= e($copy('home_testimonials_eyebrow','Cerita Mereka')) ?></p><h2><?= e($copy('home_testimonials_title',PublicTheme::text($settings,'testimonials'))) ?></h2></div><a href="/testimoni"><?= e(PublicTheme::text($settings,'all')) ?></a></div><div class="next-archive-grid"><?php foreach ($testimonials as $testimonial): ?><figure class="next-quote"><blockquote><?= e($testimonial['testimonial']) ?></blockquote><figcaption><span><strong><?= e($testimonial['name']) ?></strong><small><?= e($testimonial['relationship']) ?></small></span></figcaption></figure><?php endforeach; ?></div></div></section><?php endif; ?>
<section class="next-cta"><div class="next-shell"><div><h2><?= e($copy('home_cta_title', 'Bergabunglah Bersama Kami')) ?></h2><p><?= e($copy('home_cta_description', 'Hubungi sekolah untuk memperoleh informasi selengkapnya.')) ?></p></div><a class="next-button next-button--light" href="<?= !empty($spmbPublic['active']) ? e($spmbPublic['url']) : '/kontak' ?>"><?= e(!empty($spmbPublic['active']) ? ($spmbPublic['label'] ?? 'Info SPMB') : $copy('home_contact_button_label', 'Hubungi Kami')) ?></a></div></section>
