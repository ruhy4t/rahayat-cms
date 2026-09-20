<section class="academic-section"><article class="academic-shell academic-card academic-detail">
    <a href="<?= $event['kind'] === 'calendar' ? '/kalender-pendidikan?tahun=' . (int) $event['start_year'] : '/agenda' ?>">← <?= $event['kind'] === 'calendar' ? 'Kalender Pendidikan' : 'Agenda Kegiatan' ?></a>
    <p class="academic-eyebrow"><?= e(AcademicCalendar::CATEGORIES[$event['category']] ?? 'Kegiatan') ?> · <?= AcademicCalendar::label((int) $event['start_year']) ?></p>
    <h1><?= e($event['title']) ?></h1>
    <p><?= e(date('d/m/Y', strtotime($event['start_date']))) ?> – <?= e(date('d/m/Y', strtotime($event['end_date']))) ?><?= $event['event_time'] ? ' · ' . e(substr($event['event_time'], 0, 5)) : '' ?></p>
    <?php if ($event['location']): ?><p>Lokasi: <?= e($event['location']) ?></p><?php endif; ?>
    <div class="academic-description"><?= nl2br(e($event['description'] ?? '')) ?></div>
</article></section>
