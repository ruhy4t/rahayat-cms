<section class="next-section next-section--soft">
    <div class="next-shell">
        <div class="next-heading"><div><p class="next-kicker"><?= e($copy('home_news_eyebrow', 'Informasi Terkini')) ?></p><h2><?= e($copy('home_news_title', 'Berita & Pengumuman')) ?></h2></div><a href="/berita"><?= e(PublicTheme::text($settings,'all')) ?></a></div>
        <div class="next-news-layout">
            <div class="next-news-grid">
                <?php foreach (array_slice($news, 0, 4) as $item): ?>
                    <article class="next-news-card<?= empty($item['image']) ? ' next-news-card--text' : '' ?>">
                        <a href="/berita/<?= e($item['slug']) ?>" class="next-media">
                            <?php if (!empty($item['image'])): ?><img src="/storage/<?= e($item['image']) ?>" alt="<?= e($item['title']) ?>" loading="lazy" decoding="async"><?php endif; ?>
                        </a>
                        <div class="next-card-body"><p class="next-meta"><time datetime="<?= e(date('Y-m-d', strtotime($item['published_at'] ?? $item['created_at']))) ?>"><?= e(date('d M Y', strtotime($item['published_at'] ?? $item['created_at']))) ?></time><span><?= e(ucfirst($item['category'] ?? 'Berita')) ?></span></p><h3><a href="/berita/<?= e($item['slug']) ?>"><?= e($item['title']) ?></a></h3><p><?= e($item['excerpt'] ?? '') ?></p><a class="next-story-link" href="/berita/<?= e($item['slug']) ?>" aria-label="<?= e($item['title']) ?>"><span aria-hidden="true">↗</span></a></div>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php if (!empty($upcomingEvents)): ?><aside class="next-agenda"><div class="next-heading next-heading--compact"><h2><?= e($copy('home_agenda_title', PublicTheme::text($settings,'agenda'))) ?></h2><a href="/agenda"><?= e(PublicTheme::text($settings,'all')) ?></a></div><?php foreach ($upcomingEvents as $event): ?><a class="next-event" href="/agenda/<?= (int) $event['id'] ?>"><time><strong><?= e(date('d', strtotime($event['start_date']))) ?></strong><span><?= e(substr(AcademicCalendar::MONTHS[(int) date('n', strtotime($event['start_date']))], 0, 3)) ?></span></time><span><strong><?= e($event['title']) ?></strong><small><?= e($event['location'] ?? '') ?></small></span></a><?php endforeach; ?></aside><?php endif; ?>
        </div>
    </div>
</section>
