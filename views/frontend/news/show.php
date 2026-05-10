<article class="py-12 lg:py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex items-center text-sm text-slate-500 mb-8">
            <a href="/" class="hover:text-primary-600">Beranda</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <a href="/berita" class="hover:text-primary-600">Berita</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-slate-800 truncate max-w-xs">
                <?= e($news['title']) ?>
            </span>
        </nav>

        <!-- Article Header -->
        <header class="mb-8">
            <div class="flex items-center gap-3 mb-4">
                <span class="px-3 py-1 bg-primary-100 text-primary-700 text-sm font-medium rounded-full">
                    <?= e(ucfirst($news['category'] ?? 'Umum')) ?>
                </span>
                <span class="text-slate-500 text-sm">
                    <?= e($news['views'] ?? 0) ?> views
                </span>
            </div>
            <h1 class="text-3xl lg:text-4xl font-bold text-slate-800 leading-tight">
                <?= e($news['title']) ?>
            </h1>
            <div class="flex items-center mt-6 text-slate-500">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-white font-semibold">
                    <?= strtoupper(substr($news['author_name'] ?? 'A', 0, 1)) ?>
                </div>
                <div class="ml-3">
                    <p class="text-slate-800 font-medium">
                        <?= e($news['author_name'] ?? 'Admin') ?>
                    </p>
                    <p class="text-sm">
                        <?= date('d F Y', strtotime($news['published_at'] ?? $news['created_at'])) ?>
                    </p>
                </div>
            </div>
        </header>

        <!-- Featured Image -->
        <?php if (!empty($news['image'])): ?>
            <div class="aspect-video rounded-2xl overflow-hidden mb-8 bg-slate-100">
                <img src="/storage/<?= e($news['image']) ?>" alt="<?= e($news['title']) ?>"
                    class="w-full h-full object-cover">
            </div>
        <?php endif; ?>

        <!-- Article Content -->
        <div class="prose prose-lg prose-slate max-w-none">
            <?= $news['content'] ?>
        </div>

        <!-- Share -->
        <div class="mt-12 pt-8 border-t border-slate-200">
            <p class="text-slate-600 font-medium mb-4">Bagikan artikel ini:</p>
            <div class="flex items-center gap-3">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(APP_URL . '/berita/' . $news['slug']) ?>"
                    target="_blank"
                    class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                    </svg>
                </a>
                <a href="https://twitter.com/intent/tweet?url=<?= urlencode(APP_URL . '/berita/' . $news['slug']) ?>&text=<?= urlencode($news['title']) ?>"
                    target="_blank"
                    class="w-10 h-10 bg-sky-500 text-white rounded-full flex items-center justify-center hover:bg-sky-600 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                    </svg>
                </a>
                <a href="https://wa.me/?text=<?= urlencode($news['title'] . ' ' . APP_URL . '/berita/' . $news['slug']) ?>"
                    target="_blank"
                    class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center hover:bg-green-600 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</article>

<!-- Related News -->
<?php if (!empty($related)): ?>
    <section class="py-12 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-slate-800 mb-8">Berita Terkait</h2>
            <div class="grid md:grid-cols-3 gap-6">
                <?php foreach ($related as $item): ?>
                    <article
                        class="group bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-slate-200/50">
                        <div class="aspect-video bg-gradient-to-br from-primary-100 to-primary-200 relative overflow-hidden">
                            <?php if (!empty($item['image'])): ?>
                                <img src="/storage/<?= e($item['image']) ?>" alt="<?= e($item['title']) ?>"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    loading="lazy">
                            <?php endif; ?>
                        </div>
                        <div class="p-5">
                            <p class="text-sm text-slate-500 mb-2">
                                <?= date('d M Y', strtotime($item['published_at'] ?? $item['created_at'])) ?>
                            </p>
                            <h3
                                class="font-semibold text-slate-800 group-hover:text-primary-600 transition-colors line-clamp-2">
                                <a href="/berita/<?= e($item['slug']) ?>">
                                    <?= e($item['title']) ?>
                                </a>
                            </h3>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>