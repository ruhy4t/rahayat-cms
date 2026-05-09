<?php
/**
 * Backend - Menu Management View
 */
$title = $data['title'] ?? 'Kelola Menu';
$menus = $data['menus'] ?? [];
$parentMenus = $data['parentMenus'] ?? [];
$flash = $data['flash'] ?? null;
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                <?= e($title) ?>
            </h1>
            <p class="text-slate-500 mt-1">Kelola menu navigasi website</p>
        </div>
        <button onclick="openModal()"
            class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Menu
        </button>
    </div>

    <?php if ($flash): ?>
        <div
            class="p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200' ?>">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <!-- Menu List -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200">
            <p class="text-sm text-slate-500">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Drag & drop untuk mengubah urutan menu
            </p>
        </div>
        <ul id="menuList" class="divide-y divide-slate-100">
            <?php if (empty($menus)): ?>
                <li class="px-6 py-12 text-center text-slate-500">
                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    Belum ada menu
                </li>
            <?php else: ?>
                <?php foreach ($menus as $menu): ?>
                    <?php $isChildMenu = !empty($menu['parent_id']); ?>
                    <li class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 cursor-move <?= $isChildMenu ? 'bg-slate-50/60' : '' ?>"
                        data-id="<?= $menu['id'] ?>">
                        <div class="text-slate-400 cursor-grab <?= $isChildMenu ? 'ml-8 sm:ml-12' : '' ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                            </svg>
                        </div>
                        <div class="flex-1 <?= $isChildMenu ? 'relative border-l-2 border-primary-200 pl-4' : '' ?>">
                            <?php if ($isChildMenu): ?>
                                <span class="absolute -left-2 top-3 w-3 border-t-2 border-primary-200"></span>
                            <?php endif; ?>
                            <div class="flex flex-wrap items-center gap-2 font-medium <?= $isChildMenu ? 'text-slate-700 text-sm' : 'text-slate-800' ?>">
                                <span><?= e($menu['title']) ?></span>
                                <?php if ($isChildMenu): ?>
                                    <span class="px-2 py-0.5 text-[11px] rounded-full bg-primary-50 text-primary-700 border border-primary-100">
                                        Sub menu dari <?= e($menu['parent_title'] ?? 'menu utama') ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="text-sm text-slate-500">
                                <?= e($menu['url']) ?>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <?php if ($menu['target'] === '_blank'): ?>
                                <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">Buka tab baru</span>
                            <?php endif; ?>
                            <span
                                class="px-2 py-1 text-xs rounded <?= $menu['is_active'] ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' ?>">
                                <?= $menu['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                            <span class="px-2 py-1 text-xs rounded bg-slate-100 text-slate-600 capitalize">
                                <?= e($menu['menu_location']) ?>
                            </span>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="editMenu(<?= e(json_encode($menu)) ?>)"
                                class="p-1.5 text-slate-500 hover:text-primary-600 hover:bg-primary-50 rounded transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button onclick="deleteMenu(<?= $menu['id'] ?>, '<?= e($menu['title']) ?>')"
                                class="p-1.5 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>

<!-- Menu Modal -->
<div id="menuModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <h3 id="modalTitle" class="text-lg font-semibold text-slate-800">Tambah Menu</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="menuForm" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">
            <input type="hidden" id="menuId" name="id" value="">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Judul Menu *</label>
                    <input type="text" id="menuTitle" name="title" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">URL *</label>
                    <input type="text" id="menuUrl" name="url" required placeholder="/halaman atau https://..."
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Parent Menu</label>
                    <select id="menuParent" name="parent_id"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">- Tidak ada (Menu utama) -</option>
                        <?php foreach ($parentMenus as $pm): ?>
                            <option value="<?= $pm['id'] ?>">
                                <?= e($pm['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Lokasi</label>
                        <select id="menuLocation" name="menu_location"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="header">Header</option>
                            <option value="footer">Footer</option>
                            <option value="both">Keduanya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Target</label>
                        <select id="menuTarget" name="target"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="_self">Tab yang sama</option>
                            <option value="_blank">Tab baru</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="menuActive" name="is_active" value="1" checked
                        class="w-4 h-4 text-primary-600 border-slate-300 rounded focus:ring-primary-500">
                    <label for="menuActive" class="ml-2 text-sm text-slate-700">Aktif</label>
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

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    const modal = document.getElementById('menuModal');
    const form = document.getElementById('menuForm');

    // Initialize Sortable
    new Sortable(document.getElementById('menuList'), {
        animation: 150,
        ghostClass: 'bg-primary-50',
        onEnd: function () {
            const order = [...document.querySelectorAll('#menuList li')].map(li => li.dataset.id).filter(id => id);
            if (!order.length) return;
            fetch('/admin/menu/reorder', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= Security::csrf() ?>'
                },
                body: JSON.stringify({ order })
            }).then(r => r.json()).then(data => {
                if (!data.success) {
                    alert(data.message || 'Gagal menyimpan urutan menu');
                    location.reload();
                }
            }).catch(() => {
                alert('Gagal menyimpan urutan menu');
                location.reload();
            });
        }
    });

    function openModal() {
        document.getElementById('modalTitle').textContent = 'Tambah Menu';
        form.action = '/admin/menu/store';
        form.reset();
        document.getElementById('menuId').value = '';
        resetParentOptions();
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function editMenu(menu) {
        document.getElementById('modalTitle').textContent = 'Edit Menu';
        form.action = '/admin/menu/update/' + menu.id;
        document.getElementById('menuId').value = menu.id;
        resetParentOptions(menu.id);
        document.getElementById('menuTitle').value = menu.title;
        document.getElementById('menuUrl').value = menu.url;
        document.getElementById('menuParent').value = menu.parent_id || '';
        document.getElementById('menuLocation').value = menu.menu_location;
        document.getElementById('menuTarget').value = menu.target;
        document.getElementById('menuActive').checked = menu.is_active == 1;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function resetParentOptions(currentId = null) {
        document.querySelectorAll('#menuParent option').forEach(option => {
            option.disabled = currentId !== null && option.value === String(currentId);
        });
    }

    function deleteMenu(id, title) {
        if (confirm('Hapus menu "' + title + '"?')) {
            fetch('/admin/menu/delete/' + id, {
                method: 'POST',
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest', 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?= Security::csrf() ?>'
                },
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
