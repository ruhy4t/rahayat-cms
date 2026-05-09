<?php
// news-crimson.php
$publishedNews = array_filter($news, function ($item) {
    return $item['status'] === 'published'; });
$publishedNews = array_values($publishedNews);
?>
<section class="py-20 lg:py-28 bg-slate-50 relative border-b border-slate-200">
    <!-- Subtle pattern -->
    <div class="absolute inset-0 opacity-[0.03]"
        style="background-image: linear-gradient(45deg, #000 25%, transparent 25%, transparent 75%, #000 75%, #000), linear-gradient(45deg, #000 25%, transparent 25%, transparent 75%, #000 75%, #000); background-size: 20px 20px; background-position: 0 0, 10px 10px;">
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div
            class="flex flex-col md:flex-row md:items-end justify-between mb-16 border-l-[6px] border-primary-600 pl-6 bg-white py-4 pr-6 shadow-sm border-r border-t border-b border-slate-100">
            <div>
                <span class="text-primary-600 font-bold text-xs tracking-[0.2em] uppercase mb-2 block">Pusat
                    Informasi</span>
                <h2 class="text-3xl lg:text-4xl font-black text-slate-900 uppercase tracking-tight">Kabar Terbaru</h2>
            </div>
            <a href="/berita"
                class="mt-6 md:mt-0 px-6 py-3 bg-slate-900 hover:bg-primary-600 text-white font-bold uppercase tracking-wider text-xs transition-colors shadow-[4px_4px_0_theme(colors.slate.300)] flex items-center border border-slate-800">
                Lihat Semua Berita
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>

        <div class="grid lg:grid-cols-2 gap-8 lg:gap-10">
            <?php if (!empty($publishedNews)): ?>
                <?php foreach (array_slice($publishedNews, 0, 4) as $index => $item): ?>
                    <article
                        class="group bg-white flex flex-col sm:flex-row shadow-[6px_6px_0_theme(colors.slate.200)] border-2 border-slate-200 hover:border-primary-500 hover:shadow-[6px_6px_0_theme(colors.primary.600)] transition-all duration-300 h-full sm:h-64 relative overflow-hidden">
                        <!-- Image Left -->
                        <div
                            class="w-full sm:w-2/5 h-48 sm:h-full relative overflow-hidden bg-slate-100 border-b-2 sm:border-b-0 sm:border-r-2 border-slate-200">
                            <a href="/berita/<?= e($item['slug']) ?>" class="absolute inset-0 z-20"></a>
                            <?php if (!empty($item['image'])): ?>
                                <img src="/storage/<?= e($item['image']) ?>" alt="<?= e($item['title']) ?>"
                                    class="w-full h-full object-cover group-hover:scale-110 group-hover:rotate-1 transition-transform duration-700 grayscale-[30%] group-hover:grayscale-0">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center p-6 bg-slate-200">
                                    <svg class="w-16 h-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                    </svg>
                                </div>
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-primary-900/10 group-hover:bg-transparent transition-colors z-10">
                            </div>
                        </div>

                        <!-- Content Right -->
                        <div class="w-full sm:w-3/5 p-6 md:p-8 flex flex-col justify-between relative bg-white z-10">
                            <div>
                                <div class="flex items-center justify-between mb-4 border-b-2 border-slate-100 pb-3">
                                    <span
                                        class="text-[10px] font-black bg-slate-900 text-white px-2.5 py-1 uppercase tracking-widest border border-slate-800">
                                        <?= e($item['category']) ?>
                                    </span>
                                    <span class="text-xs font-bold text-slate-400 flex items-center">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <?= date('d M Y', strtotime($item['published_at'] ?? $item['created_at'])) ?>
                                    </span>
                                </div>
                                <h3
                                    class="text-lg font-bold text-slate-900 mb-3 leading-snug group-hover:text-primary-600 transition-colors line-clamp-2">
                                    <a href="/berita/<?= e($item['slug']) ?>" class="before:absolute before:inset-0">
                                        <?= e($item['title']) ?>
                                    </a>
                                </h3>
                                <p class="text-slate-500 text-sm line-clamp-2 mb-4 font-medium">
                                    <?= e($item['excerpt']) ?>
                                </p>
                            </div>

                            <div
                                class="flex items-center text-[11px] font-black text-slate-900 uppercase tracking-widest group-hover:text-primary-600 mt-2">
                                <span class="w-6 h-0.5 bg-primary-600 mr-2 group-hover:w-12 transition-all duration-300"></span>
                                Baca Selengkapnya
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="lg:col-span-2 text-center py-24 bg-white border-[3px] border-dashed border-slate-300 shadow-sm">
                    <p class="text-slate-500 font-bold uppercase tracking-widest text-sm">Belum ada publikasi berita</p>
                    <p class="text-slate-400 text-xs mt-2">Publikasi baru akan muncul di bagian ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>