<?php
$publishedNews = array_values(array_filter(
    $news,
    static fn(array $item): bool => ($item['status'] ?? '') === 'published'
));
$featuredNews = $publishedNews[0] ?? null;
?>
<section class="cendekia-news py-16 lg:py-24 bg-[#f4f8fb]">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-5 mb-10">
            <div>
                <span class="cendekia-kicker">Kabar sekolah</span>
                <h2 class="mt-4 text-4xl sm:text-5xl font-extrabold tracking-[-0.04em] text-slate-950">Berita dan kegiatan terbaru.</h2>
            </div>
            <a href="/berita" class="inline-flex items-center gap-3 text-sm font-extrabold text-slate-800 hover:text-primary-700">
                Lihat semua berita <span class="w-9 h-9 rounded-full border border-slate-300 flex items-center justify-center" aria-hidden="true">&#8594;</span>
            </a>
        </div>

        <?php if ($featuredNews): ?>
            <div id="newsContainer" class="grid lg:grid-cols-12 gap-5">
                <article class="group lg:col-span-7 bg-slate-950 text-white overflow-hidden">
                    <a href="/berita/<?= e($featuredNews['slug']) ?>" class="grid sm:grid-cols-2 min-h-[480px]">
                        <div class="relative min-h-[280px] overflow-hidden">
                            <?php if (!empty($featuredNews['image'])): ?>
                                <img src="/storage/<?= e($featuredNews['image']) ?>" alt="<?= e($featuredNews['title']) ?>"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" loading="lazy">
                            <?php else: ?>
                                <div class="absolute inset-0 cendekia-photo-fallback"></div>
                            <?php endif; ?>
                            <span class="absolute top-5 left-5 px-3 py-1.5 bg-cyan-400 text-slate-950 text-[10px] font-black uppercase tracking-widest">
                                <?= e($featuredNews['category'] ?? 'Berita') ?>
                            </span>
                        </div>
                        <div class="p-7 sm:p-8 flex flex-col justify-between">
                            <div>
                                <time class="text-xs font-bold text-cyan-300" datetime="<?= e(date('Y-m-d', strtotime($featuredNews['published_at'] ?? $featuredNews['created_at']))) ?>">
                                    <?= e(date('d M Y', strtotime($featuredNews['published_at'] ?? $featuredNews['created_at']))) ?>
                                </time>
                                <h3 class="mt-5 text-2xl sm:text-3xl font-extrabold leading-tight"><?= e($featuredNews['title']) ?></h3>
                                <p class="mt-4 text-sm leading-7 text-slate-400 line-clamp-3"><?= e($featuredNews['excerpt'] ?? '') ?></p>
                            </div>
                            <span class="mt-8 inline-flex items-center gap-3 text-sm font-extrabold text-cyan-300">Baca selengkapnya <span aria-hidden="true">&#8599;</span></span>
                        </div>
                    </a>
                </article>

                <div class="lg:col-span-5 border-t-2 border-slate-950">
                    <?php foreach (array_slice($publishedNews, 1, 4) as $index => $item): ?>
                        <article class="group border-b border-slate-300 py-5">
                            <a href="/berita/<?= e($item['slug']) ?>" class="grid grid-cols-[1fr_6rem] sm:grid-cols-[1fr_9rem] gap-5 items-center">
                                <div>
                                    <div class="flex items-center gap-3 text-[10px] font-black uppercase tracking-widest text-primary-700">
                                        <span><?= e($item['category'] ?? 'Berita') ?></span>
                                        <span class="w-1 h-1 rounded-full bg-cyan-500"></span>
                                        <time><?= e(date('d M Y', strtotime($item['published_at'] ?? $item['created_at']))) ?></time>
                                    </div>
                                    <h3 class="mt-3 text-base sm:text-lg font-extrabold leading-snug text-slate-900 group-hover:text-primary-700 transition-colors line-clamp-2"><?= e($item['title']) ?></h3>
                                </div>
                                <div class="aspect-[4/3] bg-slate-200 overflow-hidden">
                                    <?php if (!empty($item['image'])): ?>
                                        <img src="/storage/<?= e($item['image']) ?>" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                    <?php else: ?>
                                        <div class="w-full h-full cendekia-photo-fallback"></div>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="border-y-2 border-slate-900 py-14 text-center">
                <p class="font-extrabold text-slate-800">Belum ada berita terbaru.</p>
                <p class="mt-2 text-sm text-slate-500">Berita sekolah akan ditampilkan di bagian ini.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
