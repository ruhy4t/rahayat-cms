<?php
/**
 * Backend - Facilities Management
 */
$title = $data['title'] ?? 'Kelola Fasilitas';
$user = $data['user'] ?? null;
$facilities = $data['facilities'] ?? [];
$flash = $data['flash'] ?? [];
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">
            <?= e($title) ?>
        </h1>
        <p class="text-slate-600 text-sm mt-1">Kelola daftar fasilitas sekolah</p>
    </div>
    <button onclick="openModal()"
        class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Fasilitas
    </button>
</div>

<!-- Flash Messages -->
<?php if (!empty($flash) && isset($flash['type']) && isset($flash['message'])): ?>
    <div
        class="mb-6 p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' ?>">
        <?= e($flash['message']) ?>
    </div>
<?php endif; ?>

<!-- Facilities List -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (empty($facilities)): ?>
        <div class="col-span-full py-12 text-center bg-white rounded-xl border border-slate-200 border-dashed">
            <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <p class="text-slate-500 font-medium">Belum ada fasilitas</p>
            <p class="text-slate-400 text-sm mt-1">Silakan tambahkan fasilitas baru</p>
        </div>
    <?php else: ?>
        <?php foreach ($facilities as $item): ?>
            <div
                class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden group hover:shadow-md transition-all">
                <div class="h-48 bg-slate-100 relative overflow-hidden">
                    <?php if (!empty($item['image'])): ?>
                        <img src="/storage/<?= e($item['image']) ?>" alt="<?= e($item['name']) ?>"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <?php else: ?>
                        <div class="absolute inset-0 flex items-center justify-center text-slate-300">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    <?php endif; ?>
                    <div class="absolute top-2 right-2 flex gap-1">
                        <span class="px-2 py-1 text-xs font-semibold rounded bg-white/90 text-slate-700 shadow-sm">
                            <?= ucfirst($item['type']) ?>
                        </span>
                        <?php if (!$item['is_active']): ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-700 shadow-sm">
                                Nonaktif
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-slate-800 text-lg mb-1 line-clamp-1">
                        <?= e($item['name']) ?>
                    </h3>
                    <?php if (!empty($item['capacity'])): ?>
                        <p class="text-xs text-slate-500 mb-2">
                            <span class="font-medium">Kapasitas:</span>
                            <?= e($item['capacity']) ?> orang
                        </p>
                    <?php endif; ?>
                    <p class="text-sm text-slate-600 line-clamp-2 mb-4 h-10">
                        <?= e($item['description'] ?? '-') ?>
                    </p>
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button onclick='editItem(<?= json_encode($item) ?>)'
                            class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <form action="/admin/fasilitas/delete/<?= $item['id'] ?>" method="POST"
                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus fasilitas ini?');" class="inline">
                            <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">
                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal Form -->
<div id="modalOverlay" class="fixed inset-0 bg-black/50 z-50 hidden transition-opacity opacity-0"></div>
<div id="modal"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden pointer-events-none transition-all transform scale-95 opacity-0">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg pointer-events-auto max-h-[90vh] overflow-y-auto">
        <form id="facilityForm" action="/admin/fasilitas/store" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800" id="modalTitle">Tambah Fasilitas</h3>
                <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Fasilitas</label>
                    <input type="text" name="name" id="name" required
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                    <select name="type" id="type"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <option value="lainnya">Lainnya</option>
                        <option value="perpustakaan">Perpustakaan</option>
                        <option value="laboratorium">Laboratorium</option>
                        <option value="olahraga">Olahraga</option>
                        <option value="seni">Seni</option>
                        <option value="ibadah">Ibadah</option>
                        <option value="kantin">Kantin</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Kapasitas (Orang)</label>
                        <input type="number" name="capacity" id="capacity" value="0"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Urutan</label>
                        <input type="number" name="sort_order" id="sort_order" value="0"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Gambar</label>
                    <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/gif,image/webp"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-xs text-slate-500 mt-1">Biarkan kosong jika tidak ingin mengubah gambar (saat edit).
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked
                        class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500">
                    <label for="is_active" class="text-sm font-medium text-slate-700">Tampilkan di Website</label>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 flex justify-end gap-3 bg-slate-50 rounded-b-xl">
                <button type="button" onclick="closeModal()"
                    class="px-4 py-2 bg-white text-slate-700 font-medium rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('modal');
    const overlay = document.getElementById('modalOverlay');
    const form = document.getElementById('facilityForm');
    const modalTitle = document.getElementById('modalTitle');

    function openModal() {
        form.reset();
        form.action = '/admin/fasilitas/store';
        modalTitle.textContent = 'Tambah Fasilitas';
        document.getElementById('is_active').checked = true;

        showModal();
    }

    function editItem(item) {
        form.action = `/admin/fasilitas/update/${item.id}`;
        modalTitle.textContent = 'Edit Fasilitas';

        document.getElementById('name').value = item.name;
        document.getElementById('type').value = item.type;
        document.getElementById('capacity').value = item.capacity;
        document.getElementById('sort_order').value = item.sort_order;
        document.getElementById('description').value = item.description;
        document.getElementById('is_active').checked = item.is_active == 1;

        showModal();
    }

    function showModal() {
        overlay.classList.remove('hidden');
        modal.classList.remove('hidden');

        // Animation
        setTimeout(() => {
            overlay.classList.remove('opacity-0');
            modal.classList.remove('opacity-0', 'scale-95');
        }, 10);
    }

    function closeModal() {
        overlay.classList.add('opacity-0');
        modal.classList.add('opacity-0', 'scale-95');

        setTimeout(() => {
            overlay.classList.add('hidden');
            modal.classList.add('hidden');
        }, 300);
    }

    // Close on overlay click
    overlay.addEventListener('click', closeModal);
</script>