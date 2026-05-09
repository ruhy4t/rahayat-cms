<section class="py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-12 text-center">
            <h1 class="text-3xl lg:text-4xl font-bold text-slate-800">Galeri Sekolah</h1>
            <p class="text-slate-600 mt-2">Dokumentasi kegiatan dan fasilitas sekolah</p>
        </div>

        <!-- Albums Grid -->
        <?php if (!empty($albums)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($albums as $album): ?>
                    <?php $isVideo = ($album['type'] ?? 'foto') === 'video'; ?>
                    <a href="/galeri/<?= e($album['slug']) ?>"
                        class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-slate-200">
                        <div class="aspect-video bg-gradient-to-br from-primary-100 to-primary-200 overflow-hidden relative">
                            <?php if (!empty($album['cover_image'])): ?>
                                <img src="/storage/<?= e($album['cover_image']) ?>" alt="<?= e($album['title']) ?>"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    onerror="this.src='https://placehold.co/600x400?text=No+Cover'">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-primary-300">
                                    <?php if ($isVideo): ?>
                                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z" />
                                        </svg>
                                    <?php else: ?>
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Type Badge -->
                            <div class="absolute top-3 left-3">
                                <?php if ($isVideo): ?>
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-md bg-red-600/90 text-white backdrop-blur-sm">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z" />
                                        </svg>
                                        Video
                                    </span>
                                <?php else: ?>
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-md bg-blue-600/90 text-white backdrop-blur-sm">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14" />
                                        </svg>
                                        Foto
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Overlay Item Count -->
                            <div
                                class="absolute bottom-3 right-3 bg-black/60 text-white text-xs px-2 py-1 rounded-md backdrop-blur-sm">
                                <?= $album['item_count'] ?? 0 ?>         <?= $isVideo ? 'Video' : 'Foto' ?>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-slate-800 group-hover:text-indigo-600 transition-colors mb-2">
                                <?= e($album['title']) ?>
                            </h3>
                            <p class="text-slate-600 text-sm line-clamp-2">
                                <?= e($album['description'] ?? 'Tidak ada deskripsi') ?>
                            </p>
                            <div
                                class="mt-4 flex items-center text-indigo-600 text-sm font-medium group-hover:translate-x-1 transition-transform">
                                Lihat Album
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-16">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-slate-800 mb-2">Galeri Kosong</h3>
                <p class="text-slate-500">Belum ada album galeri yang ditambahkan</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Image Modal -->
<div id="imageModal" class="hidden fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-[90vh] w-full">
        <img id="modalImage" src="" alt="" class="w-full h-full object-contain rounded-lg">
        <button onclick="closeImageModal()"
            class="absolute top-4 right-4 bg-white/20 hover:bg-white/30 text-white rounded-full p-2 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>

<script>
    function openImageModal(src, title) {
        document.getElementById('modalImage').src = src;
        document.getElementById('modalImage').alt = title;
        document.getElementById('imageModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Close modal when clicking outside
    document.getElementById('imageModal')?.addEventListener('click', function (e) {
        if (e.target === this) closeImageModal();
    });

    // Close modal with ESC key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeImageModal();
    });
</script>