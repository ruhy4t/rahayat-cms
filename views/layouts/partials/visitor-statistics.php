<?php
/** Shared footer widget. Only aggregate counts are exposed to the public. */
$statisticLabels = [
    'today' => 'Hari ini',
    'week' => 'Minggu ini',
    'month' => 'Bulan ini',
    'total' => 'Total akses',
];
?>
<section class="visitor-statistics<?= ($themeName ?? '') === 'emerald-campus' ? ' visitor-statistics--light' : '' ?>" aria-labelledby="visitor-statistics-title">
    <h2 id="visitor-statistics-title" class="visitor-statistics__title">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 20V10m8 10V4m8 16v-7" stroke-linecap="round"/></svg>
        Statistik Pengunjung
    </h2>
    <dl class="visitor-statistics__grid">
        <?php foreach ($statisticLabels as $statisticKey => $statisticLabel): ?>
            <div class="visitor-statistics__item">
                <dt><?= e($statisticLabel) ?></dt>
                <dd><?= isset($visitorStatistics[$statisticKey]) ? number_format(max(0, (int) $visitorStatistics[$statisticKey]), 0, ',', '.') : '&mdash;' ?></dd>
            </div>
        <?php endforeach; ?>
    </dl>
    <?php if ($visitorStatistics === null): ?>
        <p class="visitor-statistics__note">Statistik sementara tidak tersedia.</p>
    <?php endif; ?>
</section>
