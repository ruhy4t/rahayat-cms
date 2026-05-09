<?php
/**
 * Backend - Hero Slides Management
 */
$title = $data['title'] ?? 'Kelola Slider';
$slides = $data['slides'] ?? [];
$flash = $data['flash'] ?? null;
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                <?= e($title) ?>
            </h1>
            <p class="text-slate-500 mt-1">Kelola gambar slider di halaman utama</p>
        </div>
        <button onclick="openSlideModal()"
            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-medium rounded-lg hover:shadow-lg hover:shadow-primary-500/30 transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Slide
        </button>
    </div>

    <?php if ($flash): ?>
        <div
            class="p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200' ?>">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <!-- Info Box -->
    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h4 class="font-medium text-blue-800">Tips Hero Slider</h4>
                <p class="text-sm text-blue-600 mt-1">Gunakan gambar dengan resolusi tinggi (min. 1920x1080) untuk hasil
                    terbaik. Slide akan tampil di halaman utama website secara bergantian.</p>
            </div>
        </div>
    </div>

    <!-- Slides Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="slidesGrid">
        <?php if (empty($slides)): ?>
            <div class="col-span-full">
                <div
                    class="flex flex-col items-center justify-center py-16 bg-white rounded-xl border-2 border-dashed border-slate-200">
                    <svg class="w-16 h-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <p class="mt-4 text-slate-500">Belum ada slide</p>
                    <button onclick="openSlideModal()" class="mt-4 text-primary-600 hover:text-primary-700 font-medium">
                        Tambah slide pertama
                    </button>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($slides as $slide): ?>
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden group"
                    data-id="<?= $slide['id'] ?>">
                    <div class="aspect-video relative overflow-hidden">
                        <img src="/storage/<?= e($slide['image']) ?>" alt="<?= e($slide['title'] ?? 'Slide') ?>"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <?php if (!$slide['is_active']): ?>
                            <div class="absolute inset-0 bg-slate-900/50 flex items-center justify-center">
                                <span class="px-3 py-1 bg-slate-700 text-white text-sm rounded-full">Tidak Aktif</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <h3 class="font-medium text-slate-800">
                            <?= e($slide['title'] ?? 'Tanpa Judul') ?>
                        </h3>
                        <?php if (!empty($slide['subtitle'])): ?>
                            <p class="text-sm text-slate-500 mt-1">
                                <?= e($slide['subtitle']) ?>
                            </p>
                        <?php endif; ?>
                        <div class="flex items-center justify-between mt-4">
                            <span class="text-xs text-slate-400">Urutan:
                                <?= $slide['sort_order'] ?? 0 ?>
                            </span>
                            <div class="flex gap-2">
                                <button onclick="toggleSlide(<?= $slide['id'] ?>)"
                                    class="p-1.5 rounded-lg <?= $slide['is_active'] ? 'text-green-600 hover:bg-green-50' : 'text-slate-400 hover:bg-slate-50' ?>"
                                    title="<?= $slide['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                                <button onclick="editSlide(<?= e(json_encode($slide)) ?>)"
                                    class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg"
                                    title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button onclick="deleteSlide(<?= $slide['id'] ?>)"
                                    class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Slide Modal -->
<div id="slideModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <h3 id="modalTitle" class="text-lg font-semibold text-slate-800">Tambah Slide</h3>
            <button onclick="closeSlideModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="slideForm" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">
            <input type="hidden" id="slideId" name="id" value="">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Judul Slide</label>
                    <input type="text" id="slideTitle" name="title"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Judul opsional">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Subtitle</label>
                    <input type="text" id="slideSubtitle" name="subtitle"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Keterangan opsional">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Gambar <span
                            class="text-red-500">*</span></label>
                    <input type="file" id="slideImage" name="image" accept="image/jpeg,image/png,image/gif,image/webp"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary-50 file:text-primary-700">
                    <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG, GIF, WebP. Maks: 5MB. Ukuran optimal: 1920x1080 piksel.</p>
                    <div id="imagePreview" class="mt-2 hidden">
                        <img id="previewImg" src="" alt="Preview" class="w-full h-40 object-cover rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Urutan</label>
                    <input type="number" id="slideOrder" name="sort_order" value="0" min="0"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="slideActive" name="is_active" value="1" checked
                        class="w-4 h-4 text-primary-600 rounded border-slate-300 focus:ring-primary-500">
                    <label for="slideActive" class="text-sm text-slate-700">Aktif</label>
                </div>
            </div>
            <div class="flex justify-end gap-3 p-6 border-t border-slate-100 bg-slate-50 rounded-b-xl">
                <button type="button" onclick="closeSlideModal()"
                    class="px-4 py-2 text-slate-600 hover:text-slate-800 font-medium">Batal</button>
                <button type="submit"
                    class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('slideModal');
    const form = document.getElementById('slideForm');
    let editMode = false;

    function openSlideModal() {
        editMode = false;
        document.getElementById('modalTitle').textContent = 'Tambah Slide';
        form.reset();
        document.getElementById('slideId').value = '';
        document.getElementById('imagePreview').classList.add('hidden');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function editSlide(slide) {
        editMode = true;
        document.getElementById('modalTitle').textContent = 'Edit Slide';
        document.getElementById('slideId').value = slide.id;
        document.getElementById('slideTitle').value = slide.title || '';
        document.getElementById('slideSubtitle').value = slide.subtitle || '';
        document.getElementById('slideOrder').value = slide.sort_order || 0;
        document.getElementById('slideActive').checked = slide.is_active == 1;

        if (slide.image) {
            document.getElementById('previewImg').src = '/storage/' + slide.image;
            document.getElementById('imagePreview').classList.remove('hidden');
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeSlideModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.getElementById('slideImage').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('imagePreview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(form);
        const url = editMode ? '/api/slides/update' : '/api/slides/create';

        fetch(url, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        }).then(r => r.json()).then(data => {
            if (data.success) location.reload();
            else alert(data.message || 'Gagal menyimpan');
        });
    });

    function toggleSlide(id) {
        fetch('/api/slides/toggle/' + id, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ csrf_token: '<?= Security::csrf() ?>' })
        }).then(r => r.json()).then(data => {
            if (data.success) location.reload();
        });
    }

    function deleteSlide(id) {
        if (!confirm('Hapus slide ini?')) return;
        fetch('/api/slides/delete/' + id, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ csrf_token: '<?= Security::csrf() ?>' })
        }).then(r => r.json()).then(data => {
            if (data.success) location.reload();
            else alert(data.message || 'Gagal menghapus');
        });
    }

    modal.addEventListener('click', (e) => { if (e.target === modal) closeSlideModal(); });
</script>
