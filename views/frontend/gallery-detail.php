<?php
$isVideo = ($album['type'] ?? 'foto') === 'video';
?>

<section class="py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-12">
            <a href="/galeri"
                class="inline-flex items-center text-slate-500 hover:text-indigo-600 transition-colors mb-4">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Galeri
            </a>

            <div class="flex items-center gap-3">
                <h1 class="text-3xl lg:text-4xl font-bold text-slate-800">
                    <?= e($album['title'] ?? 'Album Galeri') ?>
                </h1>
                <?php if ($isVideo): ?>
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z" />
                        </svg>
                        Video
                    </span>
                <?php else: ?>
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14" />
                        </svg>
                        Foto
                    </span>
                <?php endif; ?>
            </div>
            <?php if (!empty($album['description'])): ?>
                <p class="text-slate-600 mt-3 max-w-3xl text-lg">
                    <?= e($album['description']) ?>
                </p>
            <?php endif; ?>
        </div>

        <?php if ($isVideo): ?>
            <!-- Video Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $item): ?>
                        <?php if (!empty($item['youtube_video_id'])): ?>
                            <div
                                class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-slate-200">
                                <!-- YouTube Embed -->
                                <div class="aspect-[16/10]">
                                    <iframe src="https://www.youtube.com/embed/<?= e($item['youtube_video_id']) ?>"
                                        class="w-full h-full" frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                                <?php if (!empty($item['title'])): ?>
                                    <div class="p-4">
                                        <h3 class="font-medium text-slate-800 text-sm line-clamp-2">
                                            <?= e($item['title']) ?>
                                        </h3>
                                        <?php if (!empty($item['description'])): ?>
                                            <p class="text-slate-500 text-xs mt-1 line-clamp-2">
                                                <?= e($item['description']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Empty State -->
                    <div class="col-span-full">
                        <div class="text-center py-24 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                            <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-slate-700 mb-2">Belum Ada Video</h3>
                            <p class="text-slate-500">Belum ada video yang ditambahkan ke album ini</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Photo Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $item): ?>
                        <div class="aspect-square bg-gradient-to-br from-primary-100 to-primary-200 rounded-xl overflow-hidden group cursor-pointer shadow-sm hover:shadow-lg transition-all"
                            onclick='openImageModal(<?= json_encode((string) ($item['file_path'] ?? ''), JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode((string) ($item['title'] ?? ''), JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                            <div class="w-full h-full relative overflow-hidden">
                                <?php if (!empty($item['file_path'])): ?>
                                    <img src="/storage/<?= e($item['file_path']) ?>" alt="<?= e($item['title'] ?? ($album['title'] ?? 'Album Galeri')) ?>"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                        onerror="this.src='https://placehold.co/400x400?text=Error'">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-primary-300 bg-slate-50">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                <?php endif; ?>

                                <!-- Overlay Title -->
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
                                    <h3 class="text-white font-medium text-sm line-clamp-2">
                                        <?= e($item['title'] ?? 'Tanpa Judul') ?>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Empty State -->
                    <div class="col-span-full">
                        <div class="text-center py-24 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                            <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-slate-700 mb-2">Album Kosong</h3>
                            <p class="text-slate-500">Belum ada foto yang ditambahkan ke album ini</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Image Modal (for photo albums) -->
<div id="imageModal"
    class="hidden fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4 backdrop-blur-sm transition-all duration-300">
    <div class="relative w-full max-w-5xl h-full max-h-[90vh] flex flex-col items-center justify-center">
        <img id="modalImage" src="" alt=""
            class="w-auto h-auto max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl">
        <p id="modalTitle" class="text-white mt-4 text-center font-medium text-lg"></p>

        <button onclick="closeImageModal()"
            class="absolute -top-10 right-0 lg:top-0 lg:-right-12 text-white/70 hover:text-white transition-colors p-2">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>

<script>
    function openImageModal(src, title) {
        const fullSrc = src.startsWith('/') ? src : '/storage/' + src;

        document.getElementById('modalImage').src = fullSrc;
        document.getElementById('modalTitle').textContent = title;

        const modal = document.getElementById('imageModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        document.body.style.overflow = 'hidden';
    }

    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    // Close modal when clicking outside
    document.getElementById('imageModal').addEventListener('click', function (e) {
        if (e.target === this) closeImageModal();
    });

    // Close modal with ESC key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeImageModal();
    });
</script>
