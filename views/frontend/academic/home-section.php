<?php if (!empty($upcomingEvents) || !empty($calendarData['years'])): ?>
<section class="academic-section academic-section--soft" aria-labelledby="academic-home-title">
    <div class="academic-shell">
        <div class="academic-heading"><div><p class="academic-eyebrow">Catat tanggalnya</p><h2 id="academic-home-title">Agenda & Kalender Pendidikan</h2><p>Ikuti kegiatan dan tanggal penting sekolah.</p></div></div>
        <div class="academic-home-grid<?= empty($upcomingEvents) || empty($calendarData['years']) ? ' academic-home-grid--single' : '' ?>">
            <?php if (!empty($upcomingEvents)): ?><section class="academic-card"><div class="academic-heading"><h3>Agenda terdekat</h3><a href="/agenda">Lihat Semua Agenda →</a></div>
                <?php foreach ($upcomingEvents as $event): ?><article class="academic-event">
                    <div class="academic-date-badge"><strong><?= date('d', strtotime($event['start_date'])) ?></strong><span><?= substr(AcademicCalendar::MONTHS[(int) date('n', strtotime($event['start_date']))], 0, 3) ?></span></div>
                    <div><span class="academic-tag"><?= e(AcademicCalendar::CATEGORIES[$event['category']] ?? 'Kegiatan') ?></span><h3><a href="/agenda/<?= (int) $event['id'] ?>"><?= e($event['title']) ?></a></h3><p><?= e(date('d/m/Y', strtotime($event['start_date']))) ?><?= $event['end_date'] !== $event['start_date'] ? ' – ' . e(date('d/m/Y', strtotime($event['end_date']))) : '' ?><?= $event['event_time'] ? ' · ' . e(substr($event['event_time'], 0, 5)) : '' ?></p><?php if ($event['location']): ?><p><?= e($event['location']) ?></p><?php endif; ?></div>
                </article><?php endforeach; ?>
                <a href="/agenda?arsip=1" class="academic-archive-link">Arsip kegiatan →</a>
            </section><?php endif; ?>
            <?php if (!empty($calendarData['years'])): ?><div><?php include __DIR__ . '/calendar-widget.php'; ?><a href="/kalender-pendidikan" class="academic-archive-link">Lihat Kalender & Arsip Tahun Pelajaran →</a></div><?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>
