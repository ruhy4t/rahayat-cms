<?php
/**
 * Frontend - Prestasi Page
 */
$prestasi = $data['prestasi'] ?? [];
$currentCategory = $data['current_category'] ?? '';
?>

<!-- Hero Section -->
<div class="bg-gradient-to-br from-primary-900 via-primary-800 to-primary-900 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-[url('/img/pattern-grid.svg')] opacity-10"></div>
    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2">
    </div>
    <div
        class="absolute bottom-0 left-0 w-64 h-64 bg-primary-500/20 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2">
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-6 tracking-tight">Prestasi & Penghargaan</h1>
        <p class="text-lg md:text-xl text-primary-100 max-w-2xl mx-auto leading-relaxed">
            Mencetak generasi unggul dengan berbagai pencapaian membanggakan di bidang akademik dan non-akademik.
        </p>
    </div>
</div>

<!-- Main Content -->
<div class="py-16 md:py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Filter Categories -->
        <div class="mb-12 flex flex-wrap items-center justify-center gap-4">
            <a href="/prestasi"
                class="px-6 py-2.5 rounded-full text-sm font-semibold transition-all duration-300 <?= empty($currentCategory) ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' : 'bg-white text-slate-600 hover:bg-slate-100 shadow-sm border border-slate-200' ?>">
                Semua Kategori
            </a>
            <a href="/prestasi?kategori=Sekolah"
                class="px-6 py-2.5 rounded-full text-sm font-semibold transition-all duration-300 <?= $currentCategory === 'Sekolah' ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' : 'bg-white text-slate-600 hover:bg-slate-100 shadow-sm border border-slate-200' ?>">
                Prestasi Sekolah
            </a>
            <a href="/prestasi?kategori=Guru"
                class="px-6 py-2.5 rounded-full text-sm font-semibold transition-all duration-300 <?= $currentCategory === 'Guru' ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' : 'bg-white text-slate-600 hover:bg-slate-100 shadow-sm border border-slate-200' ?>">
                Prestasi Guru
            </a>
            <a href="/prestasi?kategori=Murid"
                class="px-6 py-2.5 rounded-full text-sm font-semibold transition-all duration-300 <?= $currentCategory === 'Murid' ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' : 'bg-white text-slate-600 hover:bg-slate-100 shadow-sm border border-slate-200' ?>">
                Prestasi Murid
            </a>
        </div>

        <?php if (empty($prestasi)): ?>
            <div class="text-center py-20 bg-white rounded-2xl shadow-sm border border-slate-100">
                <div
                    class="w-20 h-20 mx-auto bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-6 shadow-inner">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Belum ada data prestasi</h3>
                <p class="text-slate-500 max-w-md mx-auto">Kami sedang dalam proses mengumpulkan dan mendokumentasikan
                    berbagai prestasi yang telah dicapai.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($prestasi as $item): ?>
                    <div
                        class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 group flex flex-col h-full transform hover:-translate-y-1">
                        <!-- Image Container with Aspect Ratio -->
                        <div class="relative w-full pt-[60%] bg-slate-100 overflow-hidden">
                            <?php if (!empty($item['image'])): ?>
                                <img src="/storage/<?= htmlspecialchars($item['image']) ?>"
                                    alt="<?= htmlspecialchars($item['title']) ?>"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    loading="lazy">
                            <?php else: ?>
                                <div class="absolute inset-0 flex items-center justify-center text-slate-300 bg-slate-100">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            <?php endif; ?>

                            <!-- Category Badge -->
                            <div class="absolute top-4 left-4">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold backdrop-blur-md shadow-sm <?= $item['category'] === 'Sekolah' ? 'bg-blue-500/90 text-white' : ($item['category'] === 'Guru' ? 'bg-purple-500/90 text-white' : 'bg-amber-500/90 text-white') ?>">
                                    <?= htmlspecialchars($item['category']) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6 flex flex-col flex-grow">
                            <div class="flex items-center gap-3 text-sm text-slate-500 mb-3">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <time datetime="<?= $item['date'] ?>">
                                        <?= date('d M Y', strtotime($item['date'])) ?>
                                    </time>
                                </div>
                            </div>

                            <h3
                                class="text-xl font-bold text-slate-900 mb-3 leading-snug group-hover:text-primary-600 transition-colors">
                                <?= htmlspecialchars($item['title']) ?>
                            </h3>

                            <?php if (!empty($item['description'])): ?>
                                <p class="text-slate-600 line-clamp-3 text-sm flex-grow mb-4">
                                    <?= htmlspecialchars(strip_tags($item['description'])) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>