<section class="py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <nav class="flex items-center text-sm text-slate-500 mb-4">
                <a href="/" class="hover:text-primary-600">Beranda</a>
                <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-slate-800">Berita</span>
            </nav>
            <h1 class="text-3xl lg:text-4xl font-bold text-slate-800">Berita & Pengumuman</h1>
            <p class="text-slate-600 mt-2">Informasi terkini seputar kegiatan sekolah</p>
        </div>

        <!-- Filter -->
        <div class="flex flex-wrap gap-2 mb-8">
            <a href="/berita"
                class="px-4 py-2 rounded-full text-sm font-medium <?= empty($category) ? 'bg-primary-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?> transition-colors">
                Semua
            </a>
            <a href="/berita?category=pengumuman"
                class="px-4 py-2 rounded-full text-sm font-medium <?= ($category ?? '') === 'pengumuman' ? 'bg-primary-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?> transition-colors">
                Pengumuman
            </a>
            <a href="/berita?category=prestasi"
                class="px-4 py-2 rounded-full text-sm font-medium <?= ($category ?? '') === 'prestasi' ? 'bg-primary-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?> transition-colors">
                Prestasi
            </a>
            <a href="/berita?category=kegiatan"
                class="px-4 py-2 rounded-full text-sm font-medium <?= ($category ?? '') === 'kegiatan' ? 'bg-primary-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?> transition-colors">
                Kegiatan
            </a>
        </div>

        <!-- News Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (!empty($news)): ?>
                <?php foreach ($news as $item): ?>
                    <article
                        class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-slate-200/50">
                        <div class="aspect-video bg-gradient-to-br from-primary-100 to-primary-200 relative overflow-hidden">
                            <?php if (!empty($item['image'])): ?>
                                <img src="/storage/<?= e($item['image']) ?>" alt="<?= e($item['title']) ?>"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    loading="lazy">
                            <?php else: ?>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                    </svg>
                                </div>
                            <?php endif; ?>
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 bg-primary-600 text-white text-xs font-medium rounded-full">
                                    <?= e(ucfirst($item['category'])) ?>
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center text-sm text-slate-500 mb-3">
                                <span>
                                    <?= date('d M Y', strtotime($item['published_at'] ?? $item['created_at'])) ?>
                                </span>
                                <span class="mx-2">•</span>
                                <span>
                                    <?= e($item['author_name'] ?? 'Admin') ?>
                                </span>
                            </div>
                            <h3
                                class="text-lg font-semibold text-slate-800 mb-2 group-hover:text-primary-600 transition-colors line-clamp-2">
                                <a href="/berita/<?= e($item['slug']) ?>">
                                    <?= e($item['title']) ?>
                                </a>
                            </h3>
                            <p class="text-slate-600 text-sm line-clamp-2">
                                <?= e($item['excerpt']) ?>
                            </p>
                            <a href="/berita/<?= e($item['slug']) ?>"
                                class="inline-flex items-center mt-4 text-primary-600 hover:text-primary-700 font-medium text-sm">
                                Baca Selengkapnya
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-3 text-center py-16">
                    <svg class="w-20 h-20 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    <h3 class="text-xl font-semibold text-slate-800 mb-2">Belum ada berita</h3>
                    <p class="text-slate-500">Berita akan segera ditambahkan</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if (!empty($pagination) && $pagination['total_pages'] > 1): ?>
            <div class="mt-12 flex items-center justify-center gap-2">
                <?php if ($pagination['current_page'] > 1): ?>
                    <a href="?page=<?= $pagination['current_page'] - 1 ?>"
                        class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <a href="?page=<?= $i ?>"
                        class="px-4 py-2 rounded-lg transition-colors <?= $i === $pagination['current_page'] ? 'bg-primary-600 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                    <a href="?page=<?= $pagination['current_page'] + 1 ?>"
                        class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>