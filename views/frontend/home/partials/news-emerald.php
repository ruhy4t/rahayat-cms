<?php
// news-emerald.php
$publishedNews = array_filter($news, function($item) { return $item['status'] === 'published'; });
$publishedNews = array_values($publishedNews);
?>
<section class="py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 border-b border-slate-200 pb-4">
            <div>
                <h2 class="text-3xl font-bold text-slate-800">Berita & Informasi</h2>
                <div class="h-1.5 w-16 bg-primary-600 rounded-full mt-3"></div>
            </div>
            <a href="/berita" class="mt-4 md:mt-0 text-slate-500 hover:text-primary-600 font-bold text-sm tracking-wide flex items-center group transition-colors uppercase">
                Semua Berita
                <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
            </a>
        </div>

        <?php if (!empty($publishedNews)): ?>
            <div class="grid lg:grid-cols-12 gap-8">
                <!-- Featured News -->
                <?php $featured = $publishedNews[0]; ?>
                <div class="lg:col-span-7 group cursor-pointer relative rounded-[2rem] overflow-hidden shadow-lg hover:shadow-2xl hover:shadow-primary-600/20 transition-all duration-500 h-[450px] border border-slate-200/50">
                    <a href="/berita/<?= e($featured['slug']) ?>" class="absolute inset-0 z-20"></a>
                    <?php if (!empty($featured['image'])): ?>
                        <img src="/storage/<?= e($featured['image']) ?>" alt="<?= e($featured['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    <?php else: ?>
                        <div class="w-full h-full bg-slate-200 flex items-center justify-center">
                            <svg class="w-16 h-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    <?php endif; ?>
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/50 to-transparent z-10"></div>
                    
                    <div class="absolute bottom-0 left-0 right-0 p-8 z-10">
                        <span class="inline-block px-4 py-1.5 bg-primary-600 text-white text-xs font-bold rounded-lg mb-4 uppercase tracking-wider shadow-sm">
                            <?= e($featured['category']) ?>
                        </span>
                        <h3 class="text-3xl font-bold text-white mb-3 leading-tight group-hover:text-primary-300 transition-colors drop-shadow-md">
                            <?= e($featured['title']) ?>
                        </h3>
                        <p class="text-slate-300 text-sm mb-5 line-clamp-2 leading-relaxed">
                            <?= e($featured['excerpt']) ?>
                        </p>
                        <div class="flex items-center text-xs text-slate-400 font-semibold tracking-wide">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span><?= date('d M Y', strtotime($featured['published_at'] ?? $featured['created_at'])) ?></span>
                            <span class="mx-3 text-slate-600">•</span>
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span><?= e($featured['author_name'] ?? 'Admin') ?></span>
                        </div>
                    </div>
                </div>

                <!-- Secondary News -->
                <div class="lg:col-span-5 flex flex-col gap-6">
                    <?php for ($i = 1; $i <= 2; $i++): ?>
                        <?php if (isset($publishedNews[$i])): $item = $publishedNews[$i]; ?>
                            <div class="group flex gap-5 bg-white rounded-[1.5rem] border border-slate-100 p-4 shadow-sm hover:shadow-xl transition-all duration-300 flex-1 relative min-h-[190px]">
                                <a href="/berita/<?= e($item['slug']) ?>" class="absolute inset-0 z-20"></a>
                                <div class="w-1/3 rounded-xl overflow-hidden relative shadow-sm">
                                    <?php if (!empty($item['image'])): ?>
                                        <img src="/storage/<?= e($item['image']) ?>" alt="<?= e($item['title']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    <?php else: ?>
                                        <div class="w-full h-full bg-slate-50 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="w-2/3 flex flex-col justify-center py-2 relative">
                                    <span class="text-[10px] font-bold text-primary-600 bg-primary-50 px-2.5 py-1 rounded-md mb-3 self-start uppercase tracking-widest">
                                        <?= e($item['category']) ?>
                                    </span>
                                    <h3 class="font-bold text-slate-800 line-clamp-3 leading-snug mb-3 group-hover:text-primary-600 transition-colors">
                                        <?= e($item['title']) ?>
                                    </h3>
                                    <div class="mt-auto flex items-center text-xs text-slate-500 font-medium">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span><?= date('d M Y', strtotime($item['published_at'] ?? $item['created_at'])) ?></span>
                                    </div>
                                    <!-- Arrow -->
                                    <div class="absolute bottom-2 right-2 w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-primary-600 group-hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if (count($publishedNews) < 2): ?>
                        <div class="bg-slate-50 rounded-[1.5rem] border border-slate-200 flex flex-col items-center justify-center text-slate-400 flex-1 text-sm border-dashed">
                            <span class="text-2xl mb-2 opacity-50">📰</span>
                            Ruang Berita
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-16 bg-white rounded-[2rem] border border-slate-100 shadow-sm">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-700">Belum Ada Informasi</h3>
                <p class="text-slate-500 text-sm mt-1">Berita baru akan segera muncul di sini.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
