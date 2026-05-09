<?php
/**
 * Backend - Category Management View
 */
$title = $data['title'] ?? 'Kelola Kategori';
$categories = $data['categories'] ?? [];
$flash = $data['flash'] ?? null;
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                <?= e($title) ?>
            </h1>
            <p class="text-slate-500 mt-1">Kelola kategori untuk berita dan pengumuman</p>
        </div>
        <button onclick="openModal()"
            class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Kategori
        </button>
    </div>

    <!-- Flash Message -->
    <?php if ($flash): ?>
        <div
            class="p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200' ?>">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <!-- Categories Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Nama
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Slug
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Warna
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">
                        Jumlah Berita</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">
                        Status</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                            <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Belum ada kategori
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-medium text-slate-800">
                                    <?= e($cat['name']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                <?= e($cat['slug']) ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full shadow-sm"
                                        style="background-color: <?= e($cat['color']) ?>"></span>
                                    <span class="text-slate-500 text-sm">
                                        <?= e($cat['color']) ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-slate-600">
                                    <?= (int) ($cat['news_count'] ?? 0) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if ($cat['is_active']): ?>
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Aktif</span>
                                <?php else: ?>
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full bg-slate-100 text-slate-600">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-2">
                                    <button onclick="editCategory(<?= e(json_encode($cat)) ?>)"
                                        class="p-1.5 text-slate-500 hover:text-primary-600 hover:bg-primary-50 rounded transition-colors"
                                        title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button onclick="deleteCategory(<?= $cat['id'] ?>, '<?= e($cat['name']) ?>')"
                                        class="p-1.5 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded transition-colors"
                                        title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Category Modal -->
<div id="categoryModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <h3 id="modalTitle" class="text-lg font-semibold text-slate-800">Tambah Kategori</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="categoryForm" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">
            <input type="hidden" id="categoryId" name="id" value="">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Kategori *</label>
                    <input type="text" id="categoryName" name="name" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Warna</label>
                    <div class="flex gap-3">
                        <input type="color" id="categoryColor" name="color" value="#4F46E5"
                            class="w-12 h-10 border border-slate-300 rounded-lg cursor-pointer">
                        <input type="text" id="categoryColorText" value="#4F46E5"
                            class="flex-1 px-4 py-2 border border-slate-300 rounded-lg" readonly>
                    </div>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="categoryActive" name="is_active" value="1" checked
                        class="w-4 h-4 text-primary-600 border-slate-300 rounded focus:ring-primary-500">
                    <label for="categoryActive" class="ml-2 text-sm text-slate-700">Aktif</label>
                </div>
            </div>
            <div class="flex justify-end gap-3 p-6 border-t border-slate-100 bg-slate-50 rounded-b-xl">
                <button type="button" onclick="closeModal()"
                    class="px-4 py-2 text-slate-600 hover:text-slate-800 font-medium">Batal</button>
                <button type="submit"
                    class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('categoryModal');
    const form = document.getElementById('categoryForm');
    const colorInput = document.getElementById('categoryColor');
    const colorText = document.getElementById('categoryColorText');

    colorInput.addEventListener('input', () => colorText.value = colorInput.value);

    function openModal() {
        document.getElementById('modalTitle').textContent = 'Tambah Kategori';
        form.action = '/api/categories/store';
        form.reset();
        document.getElementById('categoryId').value = '';
        colorInput.value = '#4F46E5';
        colorText.value = '#4F46E5';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function editCategory(cat) {
        document.getElementById('modalTitle').textContent = 'Edit Kategori';
        form.action = '/api/categories/update/' + cat.id;
        document.getElementById('categoryId').value = cat.id;
        document.getElementById('categoryName').value = cat.name;
        colorInput.value = cat.color;
        colorText.value = cat.color;
        document.getElementById('categoryActive').checked = cat.is_active == 1;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function deleteCategory(id, name) {
        if (confirm('Hapus kategori "' + name + '"?')) {
            fetch('/api/categories/delete/' + id, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json' },
                body: JSON.stringify({ csrf_token: '<?= Security::csrf() ?>' })
            }).then(r => r.json()).then(data => {
                if (data.success) location.reload();
                else alert(data.message || 'Gagal menghapus');
            });
        }
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        }).then(r => r.json()).then(data => {
            if (data.success) location.reload();
            else alert(data.message || 'Gagal menyimpan');
        });
    });

    modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
</script>