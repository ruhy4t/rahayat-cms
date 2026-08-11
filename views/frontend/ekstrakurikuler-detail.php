<?php
$item = $data['ekskulItem'] ?? [];
$supervisors = $item['supervisors'] ?? [];
$schedules = $item['schedules'] ?? [];
$achievements = $item['achievements'] ?? [];
?>

<article class="py-12 lg:py-16 bg-slate-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center text-sm text-slate-500 mb-8" aria-label="Breadcrumb">
            <a href="/" class="hover:text-primary-600">Beranda</a>
            <svg class="w-4 h-4 mx-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span>Ekstrakurikuler</span>
            <svg class="w-4 h-4 mx-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-slate-800 truncate"><?= e($item['name'] ?? '') ?></span>
        </nav>

        <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-slate-200">
            <?php if (!empty($item['image'])): ?>
                <div class="aspect-[16/7] bg-slate-100">
                    <img src="/storage/<?= e($item['image']) ?>" alt="<?= e($item['name'] ?? '') ?>"
                        class="w-full h-full object-cover">
                </div>
            <?php endif; ?>

            <div class="p-6 sm:p-10 lg:p-12">
                <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">Pengembangan Diri</span>
                <h1 class="text-3xl lg:text-5xl font-bold text-slate-900 mt-2 mb-7">
                    <?= e($item['name'] ?? '') ?>
                </h1>

                <?php if (!empty($schedules) || !empty($supervisors)): ?>
                    <div class="grid sm:grid-cols-2 gap-4 mb-9">
                        <?php if (!empty($schedules)): ?>
                            <div class="flex gap-3 p-4 rounded-xl bg-primary-50 text-slate-700">
                                <svg class="w-6 h-6 text-primary-600 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-xs text-slate-500 uppercase tracking-wide">Jadwal Kegiatan</p>
                                    <ul class="mt-2 space-y-2">
                                        <?php foreach ($schedules as $schedule): ?>
                                            <li>
                                                <strong class="block"><?= e($schedule['day'] ?? '') ?><?= !empty($schedule['time']) ? ', ' . e($schedule['time']) : '' ?></strong>
                                                <?php if (!empty($schedule['note'])): ?><span class="text-sm text-slate-600"><?= e($schedule['note']) ?></span><?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($supervisors)): ?>
                            <div class="flex gap-3 p-4 rounded-xl bg-primary-50 text-slate-700">
                                <svg class="w-6 h-6 text-primary-600 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <div>
                                    <p class="text-xs text-slate-500 uppercase tracking-wide">Pembina dan Pelatih</p>
                                    <ul class="mt-2 space-y-2">
                                        <?php foreach ($supervisors as $person): ?>
                                            <li><strong><?= e($person['name'] ?? '') ?></strong><span class="block text-sm text-slate-600"><?= e($person['role'] ?? 'Pembina') ?></span></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="prose prose-lg prose-slate max-w-none">
                    <?php if (!empty($item['description'])): ?>
                        <p><?= nl2br(e($item['description'])) ?></p>
                    <?php else: ?>
                        <p>Informasi lengkap ekstrakurikuler ini akan segera diperbarui.</p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($achievements)): ?>
                    <section class="mt-10 pt-8 border-t border-slate-200" aria-labelledby="ekskul-achievements">
                        <span class="text-amber-600 font-semibold text-sm uppercase tracking-wider">Pencapaian</span>
                        <h2 id="ekskul-achievements" class="mt-2 text-2xl font-bold text-slate-900">Prestasi yang Pernah Diraih</h2>
                        <div class="mt-5 grid sm:grid-cols-2 gap-3">
                            <?php foreach ($achievements as $achievement): ?>
                                <div class="flex gap-3 p-4 rounded-xl border border-amber-200 bg-amber-50 text-slate-800">
                                    <svg class="w-6 h-6 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21h8m-4-4v4M7 4h10v4a5 5 0 01-10 0V4zm0 2H4v1a4 4 0 004 4m9-5h3v1a4 4 0 01-4 4" />
                                    </svg>
                                    <div>
                                        <strong class="block"><?= e($achievement['title'] ?? '') ?></strong>
                                        <?php if (!empty($achievement['year'])): ?><span class="text-sm text-slate-600">Tahun <?= e($achievement['year']) ?></span><?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <div class="mt-10 pt-7 border-t border-slate-200">
                    <a href="/#ekstrakurikuler"
                        class="inline-flex items-center font-semibold text-primary-600 hover:text-primary-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19l-7-7 7-7" />
                        </svg>
                        Kembali ke beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</article>
