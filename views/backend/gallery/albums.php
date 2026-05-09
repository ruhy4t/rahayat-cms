<?php
/**
 * Backend - Gallery Albums Management
 */
$title = $data['title'] ?? 'Album Galeri';
$albums = $data['albums'] ?? [];
?>

<!-- Header -->
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800"><?= e($title) ?></h1>
        <p class="text-slate-600 text-sm mt-1">Kelola album dan kategori galeri</p>
    </div>
    <button onclick="openAlbumModal()"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-md hover:shadow-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Album
    </button>
</div>

<!-- Albums Table -->
<div class="bg-white rounded-xl shadow-md overflow-hidden border border-slate-100">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider w-20">
                        Sampul</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">Album
                    </th>
                    <th class="text-center px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">Tipe
                    </th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                        Deskripsi</th>
                    <th class="text-center px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">Isi
                    </th>
                    <th class="text-center px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                        Status</th>
                    <th class="text-right px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (!empty($albums)): ?>
                    <?php foreach ($albums as $album): ?>
                        <tr class="hover:bg-slate-50 transition-colors" data-id="<?= e($album['id']) ?>"
                            data-cover="<?= e($album['cover_image'] ?? '') ?>" data-type="<?= e($album['type'] ?? 'foto') ?>">
                            <td class="px-6 py-4">
                                <div class="w-12 h-12 rounded-lg bg-slate-100 border border-slate-200 overflow-hidden">
                                    <?php if (!empty($album['cover_image'])): ?>
                                        <img src="/storage/<?= e($album['cover_image']) ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                                            <?php if (($album['type'] ?? 'foto') === 'video'): ?>
                                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8 5v14l11-7z" />
                                                </svg>
                                            <?php else: ?>
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900"><?= e($album['title']) ?></div>
                                <div class="text-xs text-slate-500 mt-0.5">ID: <?= e($album['id']) ?></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if (($album['type'] ?? 'foto') === 'video'): ?>
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
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Foto
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                <?= e(substr($album['description'] ?? '', 0, 50)) ?>
                                <?= strlen($album['description'] ?? '') > 50 ? '...' : '' ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 text-sm font-medium bg-indigo-100 text-indigo-700 rounded-full">
                                    <?= e($album['item_count'] ?? 0) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="px-3 py-1 text-xs font-medium rounded-full <?= $album['is_active'] ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-700' ?>">
                                    <?= $album['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="/admin/galeri/view/<?= e($album['id']) ?>"
                                        class="p-2 text-slate-600 hover:text-green-600 hover:bg-slate-100 rounded-lg transition-colors"
                                        title="Lihat <?= ($album['type'] ?? 'foto') === 'video' ? 'Video' : 'Foto' ?>">
                                        <?php if (($album['type'] ?? 'foto') === 'video'): ?>
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z" />
                                            </svg>
                                        <?php else: ?>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        <?php endif; ?>
                                    </a>
                                    <button onclick="editAlbum(<?= e($album['id']) ?>)"
                                        class="p-2 text-slate-600 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition-colors"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button onclick="deleteAlbum(<?= e($album['id']) ?>)"
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
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                            <p class="mb-3">Belum ada album</p>
                            <button onclick="openAlbumModal()"
                                class="text-indigo-600 hover:text-indigo-700 font-medium text-sm">
                                Buat album pertama
                            </button>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Album Modal -->
<div id="albumModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAlbumModal()"></div>
    <div
        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-2xl shadow-2xl">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <h3 id="modalTitle" class="text-lg font-semibold text-slate-800">Tambah Album</h3>
            <button onclick="closeAlbumModal()"
                class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="albumForm" class="p-6 space-y-4">
            <input type="hidden" id="albumId" name="id">
            <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">

            <div>
                <label for="albumTitle" class="block text-sm font-medium text-slate-700 mb-2">
                    Judul Album <span class="text-red-500">*</span>
                </label>
                <input type="text" id="albumTitle" name="title" required
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                    placeholder="Contoh: Wisata Alam">
            </div>

            <!-- Album Type Selector -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Tipe Album <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label id="typeFotoLabel" class="relative cursor-pointer">
                        <input type="radio" name="type" value="foto" checked class="sr-only peer">
                        <div
                            class="flex items-center gap-3 p-3 border-2 border-slate-200 rounded-xl 
                            peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-slate-300 transition-all">
                            <div
                                class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-slate-800 text-sm">Foto</div>
                                <div class="text-xs text-slate-500">Upload gambar</div>
                            </div>
                        </div>
                    </label>
                    <label id="typeVideoLabel" class="relative cursor-pointer">
                        <input type="radio" name="type" value="video" class="sr-only peer">
                        <div class="flex items-center gap-3 p-3 border-2 border-slate-200 rounded-xl 
                            peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-slate-300 transition-all">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center text-red-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-slate-800 text-sm">Video</div>
                                <div class="text-xs text-slate-500">Link YouTube</div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Sampul Album
                </label>
                <div class="flex items-start gap-4">
                    <div id="coverPreviewContainer"
                        class="hidden w-24 h-24 bg-slate-100 rounded-lg overflow-hidden border border-slate-200">
                        <img id="coverPreview" src="" alt="Preview" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <input type="file" id="albumCover" name="file" accept="image/*" class="block w-full text-sm text-slate-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-indigo-50 file:text-indigo-700
                            hover:file:bg-indigo-100">
                        <p class="mt-1 text-xs text-slate-500">Format: JPG, PNG. Maks: 2MB.</p>
                    </div>
                </div>
            </div>

            <div>
                <label for="albumDescription" class="block text-sm font-medium text-slate-700 mb-2">
                    Deskripsi
                </label>
                <textarea id="albumDescription" name="description" rows="3"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"
                    placeholder="Deskripsi album (opsional)"></textarea>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="albumActive" name="is_active" value="1" checked
                    class="w-4 h-4 text-indigo-600 rounded">
                <label for="albumActive" class="text-sm text-slate-700">Album Aktif</label>
            </div>
        </form>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-200 bg-slate-50 rounded-b-2xl">
            <button type="button" onclick="closeAlbumModal()"
                class="px-4 py-2 text-slate-700 hover:bg-slate-200 rounded-lg transition-colors font-medium">
                Batal
            </button>
            <button type="button" onclick="saveAlbum()"
                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                Simpan
            </button>
        </div>
    </div>
</div>

<script>
    let currentAlbumId = null;

    function openAlbumModal() {
        currentAlbumId = null;
        document.getElementById('albumId').value = '';
        document.getElementById('albumTitle').value = '';
        document.getElementById('albumDescription').value = '';
        document.getElementById('albumActive').checked = true;

        // Reset type to foto
        document.querySelector('input[name="type"][value="foto"]').checked = true;

        // Reset file input and preview
        document.getElementById('albumCover').value = '';
        document.getElementById('coverPreviewContainer').classList.add('hidden');
        document.getElementById('coverPreview').src = '';

        document.getElementById('modalTitle').textContent = 'Tambah Album';
        document.getElementById('albumModal').classList.remove('hidden');
    }

    function closeAlbumModal() {
        document.getElementById('albumModal').classList.add('hidden');
    }

    function editAlbum(id) {
        // Fetch album data
        const row = document.querySelector(`tr[data-id="${id}"]`);
        if (!row) return;

        const title = row.querySelector('td:nth-child(2) .font-medium').textContent;
        const description = row.querySelector('td:nth-child(4)').textContent.trim();
        const coverImage = row.dataset.cover;
        const albumType = row.dataset.type || 'foto';

        currentAlbumId = id;
        document.getElementById('albumId').value = id;
        document.getElementById('albumTitle').value = title;
        document.getElementById('albumDescription').value = description;
        document.getElementById('albumActive').checked = row.querySelector('td:nth-child(6)').textContent.includes('Aktif');

        // Set album type
        const typeRadio = document.querySelector(`input[name="type"][value="${albumType}"]`);
        if (typeRadio) typeRadio.checked = true;

        // Handle Preview
        const previewContainer = document.getElementById('coverPreviewContainer');
        const previewImg = document.getElementById('coverPreview');
        document.getElementById('albumCover').value = ''; // Reset file input

        if (coverImage) {
            previewImg.src = '/storage/' + coverImage;
            previewContainer.classList.remove('hidden');
        } else {
            previewContainer.classList.add('hidden');
        }

        document.getElementById('modalTitle').textContent = 'Edit Album';
        document.getElementById('albumModal').classList.remove('hidden');
    }

    function saveAlbum() {
        const form = document.getElementById('albumForm');
        const id = document.getElementById('albumId').value;
        const endpoint = id ? `/api/gallery/album/update/${id}` : '/api/gallery/album/store';

        const formData = new FormData(form);
        const csrfToken = document.querySelector('input[name="csrf_token"]')?.value
            || document.querySelector('meta[name="csrf-token"]')?.content;

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'same-origin',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeAlbumModal();
                    location.reload();
                } else {
                    alert(data.message || 'Terjadi kesalahan');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan');
            });
    }

    // File Input Preview Listener
    document.getElementById('albumCover')?.addEventListener('change', function (e) {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('coverPreview').src = e.target.result;
                document.getElementById('coverPreviewContainer').classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });


    function deleteAlbum(id) {
        Swal.fire({
            title: 'Hapus album ini?',
            text: "Semua foto/video di dalamnya akan ikut terhapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/api/gallery/album/delete/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="csrf_token"]').value
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Terhapus!',
                                text: 'Album berhasil dihapus.',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', data.message || 'Terjadi kesalahan', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error!', 'Terjadi kesalahan saat menghapus', 'error');
                    });
            }
        });
    }

    // Close modal when clicking outside
    document.getElementById('albumModal')?.addEventListener('click', (e) => {
        if (e.target.id === 'albumModal') {
            closeAlbumModal();
        }
    });
</script>