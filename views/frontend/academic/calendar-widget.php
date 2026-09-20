<?php
$calendarYear = $calendarData['year'] ?? null;
$calendarMonth = $calendarData['month'] ?? null;
$calendarEvents = $calendarData['events'] ?? [];
$calendarMonths = $calendarData['months'] ?? [];
?>
<section class="academic-card academic-calendar" data-academic-calendar aria-label="Kalender Pendidikan">
    <p class="academic-eyebrow">Kalender Pendidikan</p>
    <h2><?= $calendarYear ? AcademicCalendar::label((int) $calendarYear['start_year']) : 'Tahun pelajaran' ?></h2>
    <?php if (!empty($calendarData['years'])): ?>
        <form action="/kalender-pendidikan" method="get" class="academic-filter">
            <label>Tahun pelajaran<select name="tahun" aria-label="Tahun pelajaran">
                <?php if (!$calendarYear): ?><option value="<?= AcademicCalendar::currentYear() ?>"><?= AcademicCalendar::label(AcademicCalendar::currentYear()) ?> (belum tersedia)</option><?php endif; ?>
                <?php foreach ($calendarData['years'] as $year): ?><option value="<?= (int) $year['start_year'] ?>" <?= (int) ($calendarYear['id'] ?? 0) === (int) $year['id'] ? 'selected' : '' ?>><?= AcademicCalendar::label((int) $year['start_year']) ?></option><?php endforeach; ?>
            </select></label><button type="submit" class="academic-button academic-button--quiet">Lihat</button>
        </form>
    <?php endif; ?>
    <?php if (!$calendarYear): ?>
        <p class="academic-empty">Kalender tahun pelajaran ini belum dipublikasikan. Pilih tahun pelajaran lain untuk melihat arsip.</p>
    <?php else:
        $first = new DateTimeImmutable($calendarMonth . '-01');
        $days = (int) $first->format('t');
        $offset = (int) $first->format('N') - 1;
        $monthKeys = array_keys($calendarMonths);
        $monthIndex = array_search($calendarMonth, $monthKeys, true);
        $calendarUrl = '/kalender-pendidikan?tahun=' . (int) $calendarYear['start_year'] . '&amp;bulan=';
    ?>
        <div class="academic-month-nav">
            <?php if ($monthIndex > 0): ?><a href="<?= $calendarUrl . $monthKeys[$monthIndex - 1] ?>" aria-label="Bulan sebelumnya">‹</a><?php else: ?><span aria-hidden="true">‹</span><?php endif; ?>
            <strong><?= e($calendarMonths[$calendarMonth]) ?></strong>
            <?php if ($monthIndex < 11): ?><a href="<?= $calendarUrl . $monthKeys[$monthIndex + 1] ?>" aria-label="Bulan berikutnya">›</a><?php else: ?><span aria-hidden="true">›</span><?php endif; ?>
        </div>
        <form action="/kalender-pendidikan" method="get" class="academic-filter">
            <input type="hidden" name="tahun" value="<?= (int) $calendarYear['start_year'] ?>">
            <label class="academic-month-picker">Bulan<select name="bulan"><?php foreach ($calendarMonths as $key => $label): ?><option value="<?= $key ?>" <?= $key === $calendarMonth ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></label>
            <button type="submit" class="academic-button academic-button--quiet">Tampilkan</button>
        </form>
        <table class="academic-month"><caption class="academic-sr-only"><?= e($calendarMonths[$calendarMonth]) ?></caption><thead><tr><?php foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day): ?><th scope="col"><?= $day ?></th><?php endforeach; ?></tr></thead><tbody>
            <?php for ($cell = 0; $cell < (int) ceil(($offset + $days) / 7) * 7; $cell++): ?>
                <?php if ($cell % 7 === 0): ?><tr><?php endif; ?>
                <?php $dayNumber = $cell - $offset + 1; ?>
                <td><?php if ($dayNumber > 0 && $dayNumber <= $days):
                    $date = $calendarMonth . '-' . str_pad((string) $dayNumber, 2, '0', STR_PAD_LEFT);
                    $categories = [];
                    $dayEvents = 0;
                    foreach ($calendarEvents as $event) {
                        if ($event['start_date'] <= $date && $event['end_date'] >= $date) { $categories[$event['category']] = true; $dayEvents++; }
                    }
                ?><button type="button" data-calendar-date="<?= $date ?>" aria-pressed="false" <?= $date === date('Y-m-d') ? 'aria-current="date"' : '' ?> aria-label="<?= $dayNumber . ' ' . e($calendarMonths[$calendarMonth]) . ', ' . $dayEvents ?> kegiatan"><span><?= $dayNumber ?></span><span class="academic-dots" aria-hidden="true"><?php foreach ($categories as $category => $_): ?><i class="academic-dot academic-dot--<?= e($category) ?>"></i><?php endforeach; ?></span></button><?php endif; ?></td>
                <?php if ($cell % 7 === 6): ?></tr><?php endif; ?>
            <?php endfor; ?>
        </tbody></table>
        <div class="academic-legend"><?php foreach (AcademicCalendar::CATEGORIES as $category => $label): ?><span><i class="academic-dot academic-dot--<?= $category ?>"></i><?= $label ?></span><?php endforeach; ?></div>
        <div class="academic-day-heading"><h3 data-calendar-heading aria-live="polite">Kegiatan bulan ini</h3><button type="button" data-calendar-reset hidden>Semua tanggal</button></div>
        <p data-calendar-empty class="academic-empty" <?= $calendarEvents ? 'hidden' : '' ?> aria-live="polite">Belum ada kegiatan pada bulan ini.</p>
        <div class="academic-calendar-events">
            <?php foreach ($calendarEvents as $event): ?>
                <article class="academic-calendar-event" data-event-start="<?= e($event['start_date']) ?>" data-event-end="<?= e($event['end_date']) ?>">
                    <span class="academic-tag"><?= e(AcademicCalendar::CATEGORIES[$event['category']] ?? 'Kegiatan') ?></span>
                    <h3><a href="/agenda/<?= (int) $event['id'] ?>"><?= e($event['title']) ?></a></h3>
                    <p><?= e(date('d/m/Y', strtotime($event['start_date']))) ?><?= $event['end_date'] !== $event['start_date'] ? ' – ' . e(date('d/m/Y', strtotime($event['end_date']))) : '' ?><?= $event['event_time'] ? ' · ' . e(substr($event['event_time'], 0, 5)) : '' ?></p>
                    <?php if ($event['location']): ?><p><?= e($event['location']) ?></p><?php endif; ?>
                    <?php if ($event['description']): ?><p class="academic-calendar-description"><?= nl2br(e($event['description'])) ?></p><?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
        <?php if (!empty($calendarYear['pdf_path'])): ?><a class="academic-button academic-button--quiet" href="/kalender-pendidikan/pdf/<?= (int) $calendarYear['id'] ?>">Unduh PDF <?= AcademicCalendar::label((int) $calendarYear['start_year']) ?> ↓</a><?php endif; ?>
    <?php endif; ?>
</section>
