<?php
$item = $data['prestasiItem'] ?? [];
$categoryClasses = ($item['category'] ?? '') === 'Sekolah'
    ? 'bg-blue-100 text-blue-700'
    : (($item['category'] ?? '') === 'Guru' ? 'bg-purple-100 text-purple-700' : 'bg-amber-100 text-amber-700');
?>

<article class="py-12 lg:py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center text-sm text-slate-500 mb-8" aria-label="Breadcrumb">
            <a href="/" class="hover:text-primary-600">Beranda</a>
            <svg class="w-4 h-4 mx-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <a href="/prestasi" class="hover:text-primary-600">Prestasi</a>
            <svg class="w-4 h-4 mx-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-slate-800 truncate"><?= e($item['title'] ?? '') ?></span>
        </nav>

        <header class="mb-8">
            <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold <?= $categoryClasses ?>">
                Prestasi <?= e($item['category'] ?? '') ?>
            </span>
            <h1 class="text-3xl lg:text-5xl font-bold text-slate-900 leading-tight mt-4">
                <?= e($item['title'] ?? '') ?>
            </h1>
            <div class="flex flex-wrap items-center gap-x-5 gap-y-2 mt-5 text-sm text-slate-500">
                <span class="inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <?= !empty($item['date']) ? date('d F Y', strtotime($item['date'])) : '-' ?>
                </span>
                <?php if (!empty($item['author_name'])): ?>
                    <span>Ditulis oleh <?= e($item['author_name']) ?></span>
                <?php endif; ?>
            </div>
        </header>

        <?php if (!empty($item['image'])): ?>
            <div class="aspect-video rounded-2xl overflow-hidden bg-slate-100 mb-10 shadow-sm">
                <img src="/storage/<?= e($item['image']) ?>" alt="<?= e($item['title'] ?? '') ?>"
                    class="w-full h-full object-cover">
            </div>
        <?php endif; ?>

        <div class="prose prose-lg prose-slate max-w-none">
            <?php if (!empty($item['description'])): ?>
                <?= $item['description'] ?>
            <?php else: ?>
                <p>Informasi lengkap mengenai prestasi ini akan segera diperbarui.</p>
            <?php endif; ?>
        </div>

        <div class="mt-12 pt-8 border-t border-slate-200">
            <a href="/prestasi"
                class="inline-flex items-center font-semibold text-primary-600 hover:text-primary-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke daftar prestasi
            </a>
        </div>
    </div>
</article>
