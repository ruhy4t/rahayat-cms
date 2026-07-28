<?php $item = $data['alumni'] ?? []; ?>
<article class="py-12 lg:py-16 bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="/alumni" class="text-primary-600 font-semibold">← Kembali ke Direktori Alumni</a>
        <div class="mt-7 bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="grid md:grid-cols-[280px_1fr]">
                <div class="aspect-square md:aspect-auto bg-primary-50">
                    <?php if (!empty($item['photo'])): ?><img src="/storage/<?= e($item['photo']) ?>" alt="Foto <?= e($item['name']) ?>" class="w-full h-full object-cover"><?php else: ?><div class="h-full min-h-72 flex items-center justify-center text-7xl font-bold text-primary-300"><?= e(mb_strtoupper(mb_substr($item['name'], 0, 1))) ?></div><?php endif; ?>
                </div>
                <header class="p-7 lg:p-10">
                    <?php if (!empty($item['is_featured'])): ?><span class="inline-flex px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-sm font-bold">Alumni Inspiratif</span><?php endif; ?>
                    <h1 class="text-3xl lg:text-4xl font-bold text-slate-900 mt-3"><?= e($item['name']) ?></h1>
                    <p class="text-primary-600 font-semibold mt-2">Angkatan <?= (int) $item['graduation_year'] ?><?= !empty($item['final_class']) ? ' · ' . e($item['final_class']) : '' ?></p>
                    <?php if (!empty($item['occupation'])): ?><p class="text-slate-600 mt-5"><?= e($item['occupation']) ?><?= !empty($item['institution']) ? ' di ' . e($item['institution']) : '' ?></p><?php endif; ?>
                    <?php if (!empty($item['city'])): ?><p class="text-sm text-slate-500 mt-2">Domisili: <?= e($item['city']) ?></p><?php endif; ?>
                    <?php if (!empty($item['further_education'])): ?><p class="text-sm text-slate-500 mt-2">Pendidikan lanjutan: <?= e($item['further_education']) ?></p><?php endif; ?>
                </header>
            </div>
            <?php if (!empty($item['story']) || !empty($item['achievement'])): ?>
                <div class="p-7 lg:p-10 border-t border-slate-200 grid md:grid-cols-2 gap-8">
                    <?php if (!empty($item['story'])): ?><section><h2 class="text-xl font-bold text-slate-900 mb-3">Cerita Setelah Lulus</h2><p class="text-slate-600 whitespace-pre-line leading-relaxed"><?= e($item['story']) ?></p></section><?php endif; ?>
                    <?php if (!empty($item['achievement'])): ?><section><h2 class="text-xl font-bold text-slate-900 mb-3">Prestasi & Pencapaian</h2><p class="text-slate-600 whitespace-pre-line leading-relaxed"><?= e($item['achievement']) ?></p></section><?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</article>
