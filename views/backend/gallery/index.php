<?php
/**
 * Backend - Gallery Management
 */
$title = $data['title'] ?? 'Kelola Galeri';
$user = $data['user'] ?? null;
$flash = $data['flash'] ?? [];
?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">
            <?= e($title) ?>
        </h1>
        <p class="text-slate-600 text-sm mt-1">Upload dan kelola foto-foto galeri sekolah</p>
    </div>
    <button id="btnAddGallery"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-md hover:shadow-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Gambar
    </button>
</div>

<!-- Flash Messages -->
<?php if (!empty($flash) && isset($flash['type']) && isset($flash['message'])): ?>
    <div
        class="mb-4 p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' ?>">
        <?= e($flash['message']) ?>
    </div>
<?php endif; ?>

<!-- Gallery Grid -->
<div class="bg-white rounded-xl shadow-md overflow-hidden border border-slate-100">
    <div class="p-6">
        <div id="galleryGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php if (!empty($images)): ?>
                <?php foreach ($images as $image): ?>
                    <div class="group relative aspect-square rounded-lg overflow-hidden bg-slate-100 cursor-pointer"
                        data-id="<?= e($image['id']) ?>">
                        <img src="/storage/<?= e($image['file_path']) ?>" alt="<?= e($image['title'] ?? 'Gallery') ?>"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                            onerror="this.src='https://placehold.co/300x300?text=Gallery'">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                            <div class="flex gap-2">
                                <button onclick="deleteImage(<?= e($image['id']) ?>)"
                                    class="bg-red-500/90 hover:bg-red-500 p-1.5 rounded-md text-white transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <?php if (!empty($image['title'])): ?>
                            <div class="absolute bottom-0 left-0 right-0 p-2 bg-black/30 text-white text-xs truncate">
                                <?= e($image['title']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full py-12 text-center text-slate-500">
                    <p class="mb-3">Belum ada gambar di galeri</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Empty State -->
        <div id="emptyGallery" class="hidden py-12 text-center">
            <div class="mx-auto w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-slate-800 mb-1">Belum ada gambar</h3>
            <p class="text-slate-600 text-sm mb-4">Upload gambar pertama untuk galeri sekolah</p>
            <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                Upload Gambar
            </button>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="galleryModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 id="modalTitle" class="text-lg font-semibold text-slate-800">Tambah Gambar</h3>
            <button id="closeModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="galleryForm" action="/api/gallery/store" method="POST" enctype="multipart/form-data" class="p-6">
            <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">
            <input type="hidden" id="galleryId" name="id">
            <input type="hidden" id="galleryType" name="type" value="image">

            <div class="mb-4">
                <label for="galleryAlbum" class="block text-sm font-medium text-slate-700 mb-1">Album</label>
                <select id="galleryAlbum" name="album_id" required
                    class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    <option value="">-- Pilih Album --</option>
                    <?php if (!empty($albums)): ?>
                        <?php foreach ($albums as $album): ?>
                            <option value="<?= e($album['id']) ?>"><?= e($album['title']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="galleryTitle" class="block text-sm font-medium text-slate-700 mb-1">Judul</label>
                <input type="text" id="galleryTitle" name="title"
                    class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                    placeholder="Judul gambar">
            </div>

            <div class="mb-4">
                <label for="galleryImage" class="block text-sm font-medium text-slate-700 mb-1">Gambar</label>
                <div id="uploadArea"
                    class="border-2 border-dashed border-slate-300 rounded-lg p-4 text-center hover:border-indigo-500 transition-colors cursor-pointer">
                    <svg class="mx-auto w-10 h-10 text-slate-400 mb-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-sm text-slate-600">Klik atau drag & drop gambar</p>
                    <p class="text-xs text-slate-400 mt-1">JPG, PNG, GIF, WebP max 5MB</p>
                </div>
                <input type="file" id="galleryImage" name="file" class="hidden" accept="image/jpeg,image/png,image/gif,image/webp" required>
            </div>

            <div class="mb-4">
                <label for="galleryDescription" class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                <textarea id="galleryDescription" name="description" rows="3"
                    class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"
                    placeholder="Deskripsi gambar (opsional)"></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" id="cancelBtn"
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
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('galleryModal');
        const btnAdd = document.getElementById('btnAddGallery');
        const btnClose = document.getElementById('closeModal');
        const btnCancel = document.getElementById('cancelBtn');
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('galleryImage');

        // Open modal
        btnAdd?.addEventListener('click', () => {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });

        // Close modal
        const closeModal = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };

        btnClose?.addEventListener('click', closeModal);
        btnCancel?.addEventListener('click', closeModal);

        // Close on backdrop click
        modal?.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        // Upload area click
        uploadArea?.addEventListener('click', () => {
            fileInput?.click();
        });

        // File input change
        fileInput?.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const fileName = this.files[0].name;
                uploadArea.innerHTML = `
                <svg class="mx-auto w-10 h-10 text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <p class="text-sm text-slate-700 font-medium">${fileName}</p>
                <p class="text-xs text-slate-400 mt-1">Klik untuk ganti</p>
            `;
            }
        });

        // Form submit
        const galleryForm = document.getElementById('galleryForm');
        galleryForm?.addEventListener('submit', async (e) => {
            e.preventDefault();

            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            const formData = new FormData(form);

            try {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Menyimpan...';

                const csrfToken = document.querySelector('input[name="csrf_token"]')?.value
                    || document.querySelector('meta[name="csrf-token"]')?.content;

                const response = await fetch('/api/gallery/store', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    credentials: 'same-origin',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    closeModal();
                    // Reset form
                    form.reset();
                    uploadArea.innerHTML = `
                        <svg class="mx-auto w-10 h-10 text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-sm text-slate-600">Klik atau drag & drop gambar</p>
                        <p class="text-xs text-slate-400 mt-1">JPG, PNG, GIF, WebP max 5MB</p>
                    `;
                    // Reload page to show new image
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                } else {
                    alert(data.message || 'Gagal menyimpan gambar');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Simpan';
            }
        });
    });

    function deleteImage(id) {
        if (confirm('Yakin ingin menghapus gambar ini?')) {
            const csrfToken = document.querySelector('input[name="csrf_token"]')?.value;

            console.log('Deleting image:', id, 'Token:', csrfToken);

            fetch(`/api/gallery/delete/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'X-HTTP-Method-Override': 'DELETE'
                },
                body: JSON.stringify({
                    _method: 'DELETE',
                    csrf_token: csrfToken
                })
            })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        alert('Gambar berhasil dihapus');
                        location.reload();
                    } else {
                        alert(data.message || 'Gagal menghapus gambar');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan: ' + error.message);
                });
        }
    }
</script>