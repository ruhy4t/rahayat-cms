<?php
$base = $kind === 'agenda' ? '/admin/agenda' : '/admin/kalender-pendidikan';
$form = $edit;
$selectedId = (int) ($form['academic_year_id'] ?? $selectedYearId);
?>
<link rel="stylesheet" href="/css/academic.css?v=<?= filemtime(ROOT_PATH . '/public/css/academic.css') ?>">
<div class="academic-admin">
    <div class="academic-heading"><div><p class="academic-eyebrow">Informasi akademik</p><h1><?= e($title) ?></h1><p>Kelola kegiatan dan arsip tahun pelajaran Juli–Juni.</p></div><a class="academic-button academic-button--quiet" href="<?= $kind === 'agenda' ? '/agenda' : '/kalender-pendidikan' ?>" target="_blank" rel="noopener">Lihat halaman publik ↗</a></div>
    <?php if (!empty($flash['message'])): ?><p role="status" class="academic-notice"><?= e($flash['message']) ?></p><?php endif; ?>

    <?php if ($kind === 'calendar'): ?>
        <section class="academic-card">
            <h2>Tahun pelajaran & PDF</h2>
            <p>Publikasikan tahun pelajaran agar kegiatan berstatus publik tampil. Menyembunyikan tahun pelajaran tidak menghapus arsip.</p>
            <details class="academic-year" <?= !$years ? 'open' : '' ?>>
                <summary>Tambah tahun pelajaran</summary>
                <form action="/admin/kalender-pendidikan/tahun/save" method="post" enctype="multipart/form-data" class="academic-form">
                    <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">
                    <label>Tahun awal <input type="number" name="start_year" min="1900" max="2200" value="<?= AcademicCalendar::currentYear() ?>" required><small>Contoh: 2026 untuk Juli 2026–Juni 2027.</small></label>
                    <label>PDF kalender (opsional, maksimal 10 MB)<input type="file" name="pdf" accept="application/pdf,.pdf"></label>
                    <label class="academic-check"><input type="checkbox" name="is_published" value="1"> Publikasikan tahun pelajaran</label>
                    <button class="academic-button" type="submit">Simpan tahun pelajaran</button>
                </form>
            </details>
            <?php foreach ($years as $year): ?>
                <details class="academic-year">
                    <summary><?= AcademicCalendar::label((int) $year['start_year']) ?> · <?= $year['is_published'] ? 'Publik' : 'Draft' ?><?= $year['pdf_path'] ? ' · PDF tersedia' : '' ?></summary>
                    <form action="/admin/kalender-pendidikan/tahun/save" method="post" enctype="multipart/form-data" class="academic-form">
                        <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">
                        <input type="hidden" name="start_year" value="<?= (int) $year['start_year'] ?>">
                        <p>Juli <?= (int) $year['start_year'] ?>–Juni <?= (int) $year['start_year'] + 1 ?>. Unggahan baru mengganti PDF untuk tahun ini saja.</p>
                        <label>Ganti/tambahkan PDF (maksimal 10 MB)<input type="file" name="pdf" accept="application/pdf,.pdf"></label>
                        <?php if ($year['pdf_path']): ?><label class="academic-check"><input type="checkbox" name="remove_pdf" value="1"> Lepaskan PDF dari kalender</label><?php endif; ?>
                        <label class="academic-check"><input type="checkbox" name="is_published" value="1" <?= $year['is_published'] ? 'checked' : '' ?>> Publikasikan tahun pelajaran</label>
                        <button type="submit" class="academic-button">Perbarui tahun pelajaran</button>
                    </form>
                </details>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <section class="academic-card" id="event-form">
        <h2><?= !empty($form['id']) ? 'Edit kegiatan' : 'Tambah kegiatan' ?></h2>
        <?php if (!$years): ?>
            <p>Buat tahun pelajaran terlebih dahulu di <a href="/admin/kalender-pendidikan">Kalender Pendidikan</a>.</p>
        <?php else: ?>
            <form action="/admin/kegiatan/save" method="post" class="academic-form">
                <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">
                <input type="hidden" name="kind" value="<?= $kind ?>">
                <input type="hidden" name="id" value="<?= (int) ($form['id'] ?? 0) ?>">
                <div class="academic-form-grid">
                    <label>Tahun pelajaran<select name="academic_year_id" required><?php foreach ($years as $year): ?><option value="<?= (int) $year['id'] ?>" <?= $selectedId === (int) $year['id'] ? 'selected' : '' ?>><?= AcademicCalendar::label((int) $year['start_year']) ?><?= !$year['is_published'] ? ' (Draft)' : '' ?></option><?php endforeach; ?></select></label>
                    <label>Judul kegiatan<input name="title" maxlength="180" required value="<?= e($form['title'] ?? '') ?>"></label>
                    <label>Tanggal mulai<input type="date" name="start_date" required value="<?= e($form['start_date'] ?? '') ?>"></label>
                    <label>Tanggal selesai<input type="date" name="end_date" required value="<?= e($form['end_date'] ?? '') ?>"><small>Untuk kegiatan satu hari, gunakan tanggal yang sama.</small></label>
                    <label>Kategori<select name="category"><?php foreach (AcademicCalendar::CATEGORIES as $key => $label): ?><option value="<?= $key ?>" <?= ($form['category'] ?? 'kegiatan') === $key ? 'selected' : '' ?>><?= $label ?></option><?php endforeach; ?></select></label>
                    <label>Waktu (opsional)<input type="time" name="event_time" value="<?= e(substr($form['event_time'] ?? '', 0, 5)) ?>"></label>
                    <label>Lokasi (opsional)<input name="location" maxlength="200" value="<?= e($form['location'] ?? '') ?>"></label>
                    <label>Status<select name="status"><option value="draft" <?= ($form['status'] ?? '') !== 'published' ? 'selected' : '' ?>>Draft</option><option value="published" <?= ($form['status'] ?? '') === 'published' ? 'selected' : '' ?>>Publik</option></select></label>
                </div>
                <label>Keterangan<textarea name="description" rows="5" maxlength="20000"><?= e($form['description'] ?? '') ?></textarea></label>
                <?php if ($kind === 'agenda'): ?><label class="academic-check"><input type="checkbox" name="show_in_calendar" value="1" <?= !empty($form['show_in_calendar']) ? 'checked' : '' ?>> Tampilkan juga di Kalender Pendidikan</label><?php endif; ?>
                <div class="academic-actions"><button class="academic-button" type="submit">Simpan kegiatan</button><?php if ($form): ?><a href="<?= $base ?>" class="academic-button academic-button--quiet">Batal edit</a><?php endif; ?></div>
            </form>
        <?php endif; ?>
    </section>

    <section class="academic-card">
        <h2>Daftar kegiatan</h2>
        <?php if ($kind === 'calendar'): ?><p>Agenda yang ditandai untuk kalender ikut muncul otomatis di frontend. Edit agenda tersebut melalui <a href="/admin/agenda">Agenda Kegiatan</a>.</p><?php endif; ?>
        <form method="get" action="<?= $base ?>" class="academic-filter"><label>Tahun pelajaran<select name="year"><?php foreach ($years as $year): ?><option value="<?= (int) $year['id'] ?>" <?= $selectedYearId === (int) $year['id'] ? 'selected' : '' ?>><?= AcademicCalendar::label((int) $year['start_year']) ?></option><?php endforeach; ?></select></label><button class="academic-button" type="submit">Tampilkan</button></form>
        <?php if (!$events): ?><p class="academic-empty">Belum ada kegiatan pada pilihan ini.</p><?php endif; ?>
        <div class="academic-event-list">
            <?php foreach ($events as $event): ?>
                <article class="academic-event">
                    <div><span class="academic-tag"><?= $event['status'] === 'published' ? 'Publik' : 'Draft' ?></span><h3><?= e($event['title']) ?></h3><p><?= e($event['start_date']) ?> – <?= e($event['end_date']) ?> · <?= AcademicCalendar::label((int) $event['start_year']) ?></p></div>
                    <div class="academic-actions"><a class="academic-button academic-button--quiet" href="<?= $base ?>?year=<?= (int) $event['academic_year_id'] ?>&amp;edit=<?= (int) $event['id'] ?>#event-form">Edit</a>
                        <form action="/admin/kegiatan/delete/<?= (int) $event['id'] ?>" method="post" onsubmit="return confirm('Hapus kegiatan ini?')"><input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>"><button type="submit" class="academic-button academic-button--quiet">Hapus</button></form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <nav class="academic-actions" aria-label="Halaman daftar kegiatan"><?php if ($page > 1): ?><a href="<?= $base ?>?year=<?= $selectedYearId ?>&amp;page=<?= $page - 1 ?>">← Sebelumnya</a><?php endif; ?><?php if (count($events) === 30): ?><a href="<?= $base ?>?year=<?= $selectedYearId ?>&amp;page=<?= $page + 1 ?>">Berikutnya →</a><?php endif; ?></nav>
    </section>
</div>
