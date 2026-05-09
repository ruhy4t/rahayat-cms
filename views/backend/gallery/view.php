<?php
/**
 * Backend - Gallery Album Items (Photos & Videos)
 */
$title = $data['title'] ?? 'Isi Album';
$album = $data['album'] ?? [];
$items = $data['items'] ?? [];
$flash = $data['flash'] ?? [];
$albumType = $album['type'] ?? 'foto';
$isVideo = $albumType === 'video';
?>

<!-- Header -->
<div class="mb-6 flex items-center justify-between">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="/admin/galeri" class="text-slate-500 hover:text-indigo-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-slate-800">
                <?= e($title) ?>
            </h1>
            <?php if ($isVideo): ?>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    Video
                </span>
            <?php else: ?>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Foto
                </span>
            <?php endif; ?>
        </div>
        <p class="text-slate-600 text-sm ml-7">Kelola <?= $isVideo ? 'video' : 'foto' ?> untuk album: <strong>
                <?= e($album['title']) ?>
            </strong></p>
    </div>
    <button onclick="openGalleryModal()"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-md hover:shadow-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah <?= $isVideo ? 'Video' : 'Foto' ?>
    </button>
</div>

<!-- Flash Messages -->
<?php if (!empty($flash) && isset($flash['type']) && isset($flash['message'])): ?>
    <div
        class="mb-4 p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' ?>">
        <?= e($flash['message']) ?>
    </div>
<?php endif; ?>

<!-- Items Table -->
<div class="bg-white rounded-xl shadow-md overflow-hidden border border-slate-100">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider w-24">
                        Preview</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">Judul
                        & Deskripsi</th>
                    <th class="text-center px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                        Status</th>
                    <th class="text-right px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $item): ?>
                        <tr class="hover:bg-slate-50 transition-colors" data-id="<?= e($item['id']) ?>">
                            <td class="px-6 py-4">
                                <div class="w-16 h-16 rounded-lg overflow-hidden bg-slate-100 border border-slate-200">
                                    <?php if ($isVideo && !empty($item['youtube_video_id'])): ?>
                                        <!-- YouTube Thumbnail -->
                                        <img src="https://img.youtube.com/vi/<?= e($item['youtube_video_id']) ?>/hqdefault.jpg" 
                                            alt="<?= e($item['title']) ?>"
                                            class="w-full h-full object-cover">
                                    <?php elseif (!empty($item['file_path'])): ?>
                                        <img src="/storage/<?= e($item['file_path']) ?>" alt="<?= e($item['title']) ?>"
                                            class="w-full h-full object-cover"
                                            onerror="this.src='https://placehold.co/100x100?text=Error'">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                                            <?php if ($isVideo): ?>
                                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                                            <?php else: ?>
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16" />
                                                </svg>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900">
                                    <?= e($item['title'] ?? 'Tanpa Judul') ?>
                                </div>
                                <div class="text-sm text-slate-500 mt-1 line-clamp-2">
                                    <?= e($item['description'] ?? '-') ?>
                                </div>
                                <?php if ($isVideo && !empty($item['youtube_url'])): ?>
                                    <a href="<?= e($item['youtube_url']) ?>" target="_blank" 
                                        class="inline-flex items-center gap-1 text-xs text-red-600 hover:text-red-700 mt-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        YouTube
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="px-3 py-1 text-xs font-medium rounded-full <?= $item['is_active'] ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-700' ?>">
                                    <?= $item['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick='editItem(<?= json_encode($item) ?>)'
                                        class="p-2 text-slate-600 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition-colors"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button onclick="deleteItem(<?= e($item['id']) ?>)"
                                        class="p-2 text-slate-600 hover:text-red-600 hover:bg-slate-100 rounded-lg transition-colors"
                                        title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                            <div class="mx-auto w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mb-3">
                                <?php if ($isVideo): ?>
                                    <svg class="w-6 h-6 text-slate-400" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                                <?php else: ?>
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <p class="mb-3">Belum ada <?= $isVideo ? 'video' : 'foto' ?> di album ini</p>
                            <button onclick="openGalleryModal()"
                                class="text-indigo-600 hover:text-indigo-700 font-medium text-sm">
                                Upload <?= $isVideo ? 'video' : 'foto' ?> pertama
                            </button>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Upload/Edit Modal -->
<div id="galleryModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <!-- Overlay -->
    <div class="absolute inset-0" onclick="closeGalleryModal()"></div>

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden relative z-10">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 id="modalTitle" class="text-lg font-semibold text-slate-800">Tambah <?= $isVideo ? 'Video' : 'Foto' ?></h3>
            <button onclick="closeGalleryModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="galleryForm" action="/api/gallery/store" method="POST" enctype="multipart/form-data" class="p-6">
            <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">
            <input type="hidden" id="galleryId" name="id">
            <input type="hidden" name="album_id" value="<?= e($album['id']) ?>">
            <input type="hidden" name="type" value="<?= $isVideo ? 'video' : 'image' ?>">

            <div class="mb-4">
                <label for="galleryTitle" class="block text-sm font-medium text-slate-700 mb-1">Judul</label>
                <input type="text" id="galleryTitle" name="title"
                    class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                    placeholder="Judul <?= $isVideo ? 'video' : 'gambar' ?>">
            </div>

            <?php if ($isVideo): ?>
                <!-- YouTube URL Input -->
                <div class="mb-4">
                    <label for="youtubeUrl" class="block text-sm font-medium text-slate-700 mb-1">Link YouTube</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-red-500" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                        </div>
                        <input type="url" id="youtubeUrl" name="youtube_url"
                            class="w-full pl-10 pr-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                            placeholder="https://www.youtube.com/watch?v=..."
                            required>
                    </div>
                    <p class="text-xs text-slate-500 mt-1.5">Paste link YouTube biasa. Contoh: https://www.youtube.com/watch?v=xxx atau https://youtu.be/xxx</p>
                    
                    <!-- YouTube Preview -->
                    <div id="youtubePreview" class="mt-3 hidden rounded-lg overflow-hidden border border-slate-200">
                        <div class="aspect-video bg-black">
                            <iframe id="youtubeIframe" src="" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Image Upload -->
                <div class="mb-4">
                    <label for="galleryImage" class="block text-sm font-medium text-slate-700 mb-1">Gambar</label>
                    <div id="uploadArea"
                        class="border-2 border-dashed border-slate-300 rounded-lg p-4 text-center hover:border-indigo-500 transition-colors cursor-pointer"
                        onclick="document.getElementById('galleryImage').click()">
                        <svg class="mx-auto w-10 h-10 text-slate-400 mb-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p id="uploadText" class="text-sm text-slate-600">Klik untuk upload gambar</p>
                        <p class="text-xs text-slate-400 mt-1">JPG, PNG, GIF, WebP max 5MB</p>
                    </div>
                    <input type="file" id="galleryImage" name="file" class="hidden" accept="image/jpeg,image/png,image/gif,image/webp">
                </div>
            <?php endif; ?>

            <div class="mb-4">
                <label for="galleryDescription" class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                <textarea id="galleryDescription" name="description" rows="3"
                    class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"
                    placeholder="Deskripsi <?= $isVideo ? 'video' : 'gambar' ?> (opsional)"></textarea>
            </div>

            <div class="mb-4">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="galleryActive" name="is_active" value="1" checked
                        class="w-4 h-4 text-indigo-600 rounded">
                    <label for="galleryActive" class="text-sm text-slate-700">Tampilkan <?= $isVideo ? 'Video' : 'Foto' ?></label>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeGalleryModal()"
                    class="px-4 py-2 border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const isVideoAlbum = <?= $isVideo ? 'true' : 'false' ?>;
    const modal = document.getElementById('galleryModal');
    const form = document.getElementById('galleryForm');

    <?php if (!$isVideo): ?>
    const fileInput = document.getElementById('galleryImage');
    const uploadArea = document.getElementById('uploadArea');
    const uploadText = document.getElementById('uploadText');
    <?php endif; ?>

    function openGalleryModal() {
        // Reset for new entry
        document.getElementById('galleryId').value = '';
        document.getElementById('galleryTitle').value = '';
        document.getElementById('galleryDescription').value = '';
        document.getElementById('galleryActive').checked = true;
        document.getElementById('modalTitle').textContent = 'Tambah <?= $isVideo ? 'Video' : 'Foto' ?>';

        <?php if ($isVideo): ?>
        document.getElementById('youtubeUrl').value = '';
        document.getElementById('youtubePreview').classList.add('hidden');
        <?php else: ?>
        fileInput.value = '';
        uploadText.textContent = 'Klik untuk upload gambar';
        <?php endif; ?>

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function editItem(item) {
        document.getElementById('galleryId').value = item.id;
        document.getElementById('galleryTitle').value = item.title || '';
        document.getElementById('galleryDescription').value = item.description || '';
        document.getElementById('galleryActive').checked = (item.is_active == 1);
        document.getElementById('modalTitle').textContent = 'Edit <?= $isVideo ? 'Video' : 'Foto' ?>';

        <?php if ($isVideo): ?>
        document.getElementById('youtubeUrl').value = item.youtube_url || '';
        if (item.youtube_video_id) {
            showYouTubePreview(item.youtube_video_id);
        }
        <?php else: ?>
        uploadText.textContent = 'Klik untuk ganti gambar (Opsional)';
        <?php endif; ?>

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeGalleryModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    <?php if (!$isVideo): ?>
    // File Input Change
    fileInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            uploadText.textContent = this.files[0].name;
        }
    });
    <?php endif; ?>

    <?php if ($isVideo): ?>
    // YouTube URL Preview
    const youtubeInput = document.getElementById('youtubeUrl');
    let debounceTimer;

    youtubeInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const videoId = extractYouTubeId(this.value);
            if (videoId) {
                showYouTubePreview(videoId);
            } else {
                document.getElementById('youtubePreview').classList.add('hidden');
            }
        }, 500);
    });

    function extractYouTubeId(url) {
        const patterns = [
            /youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/,
            /youtube\.com\/embed\/([a-zA-Z0-9_-]+)/,
            /youtu\.be\/([a-zA-Z0-9_-]+)/,
            /youtube\.com\/v\/([a-zA-Z0-9_-]+)/,
            /youtube\.com\/shorts\/([a-zA-Z0-9_-]+)/
        ];

        for (const pattern of patterns) {
            const match = url.match(pattern);
            if (match) return match[1];
        }
        return null;
    }

    function showYouTubePreview(videoId) {
        const preview = document.getElementById('youtubePreview');
        const iframe = document.getElementById('youtubeIframe');
        iframe.src = `https://www.youtube.com/embed/${videoId}`;
        preview.classList.remove('hidden');
    }
    <?php endif; ?>

    // Delete item
    function deleteItem(id) {
        Swal.fire({
            title: 'Hapus <?= $isVideo ? 'video' : 'foto' ?> ini?',
            text: "<?= $isVideo ? 'Video' : 'Foto' ?> tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const csrfToken = document.querySelector('input[name="csrf_token"]').value;

                fetch(`/api/gallery/delete/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        _method: 'DELETE',
                        csrf_token: csrfToken
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Terhapus!',
                                text: '<?= $isVideo ? 'Video' : 'Foto' ?> berhasil dihapus.',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', data.message || 'Gagal menghapus', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error!', 'Terjadi kesalahan: ' + error.message, 'error');
                    });
            }
        });
    }

    // Handle Form Submit
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = form.querySelector('button[type="submit"]');
        const formData = new FormData(form);

        const id = document.getElementById('galleryId').value;
        const endpoint = id ? `/api/gallery/update/${id}` : '/api/gallery/store';

        try {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menyimpan...';

            const csrfToken = document.querySelector('input[name="csrf_token"]')?.value
                || document.querySelector('meta[name="csrf-token"]')?.content;

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                credentials: 'same-origin',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                closeGalleryModal();
                location.reload();
            } else {
                alert(data.message || 'Gagal menyimpan');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Simpan';
        }
    });
</script>