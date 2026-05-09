<?php
/**
 * Backend - Users Management
 */
$title = $data['title'] ?? 'Kelola Pengguna';
$currentUser = $data['user'] ?? null;
$users = $data['users'] ?? [];
$flash = $data['flash'] ?? [];
?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">
            <?= e($title) ?>
        </h1>
        <p class="text-slate-600 text-sm mt-1">Kelola akun pengguna sistem</p>
    </div>
    <button id="btnAddUser"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-md hover:shadow-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Pengguna
    </button>
</div>

<!-- Flash Messages -->
<?php if (!empty($flash) && isset($flash['type']) && isset($flash['message'])): ?>
    <div
        class="mb-4 p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' ?>">
        <?= e($flash['message']) ?>
    </div>
<?php endif; ?>

<!-- Users Table -->
<div class="bg-white rounded-xl shadow-md overflow-hidden border border-slate-100">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                        Pengguna</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                        Username</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Role
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Status
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Login
                        Terakhir</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                            Belum ada pengguna terdaftar
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full flex items-center justify-center text-white font-semibold">
                                        <?= strtoupper(substr($u['name'] ?? 'U', 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="font-medium text-slate-800">
                                            <?= e($u['name'] ?? '-') ?>
                                        </div>
                                        <div class="text-sm text-slate-500">
                                            <?= e($u['email'] ?? '-') ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                <?= e($u['username'] ?? '-') ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                $roleBadge = match ($u['role'] ?? '') {
                                    'admin' => 'bg-purple-100 text-purple-800',
                                    'gtk' => 'bg-blue-100 text-blue-800',
                                    'murid' => 'bg-green-100 text-green-800',
                                    'ekskul' => 'bg-amber-100 text-amber-800',
                                    default => 'bg-slate-100 text-slate-800',
                                };
                                ?>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $roleBadge ?>">
                                    <?= strtoupper(e($u['role'] ?? 'user')) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= ($u['is_active'] ?? false) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= ($u['is_active'] ?? false) ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-sm">
                                <?= $u['last_login'] ? date('d M Y H:i', strtotime($u['last_login'])) : 'Belum pernah' ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-2">
                                    <button
                                        class="btn-edit p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded transition-colors"
                                        title="Edit" data-id="<?= $u['id'] ?>" data-name="<?= e($u['name'] ?? '') ?>"
                                        data-username="<?= e($u['username'] ?? '') ?>" data-email="<?= e($u['email'] ?? '') ?>"
                                        data-role="<?= e($u['role'] ?? '') ?>" data-active="<?= $u['is_active'] ? '1' : '0' ?>"
                                        data-permissions='<?= e($u['permissions'] ?? '[]') ?>'>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <?php if (($u['id'] ?? 0) != ($currentUser['id'] ?? 0)): ?>
                                        <button
                                            class="btn-delete p-1.5 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded transition-colors"
                                            title="Hapus" data-id="<?= $u['id'] ?>" data-name="<?= e($u['name'] ?? '') ?>">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div id="userModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 id="modalTitle" class="text-lg font-semibold text-slate-800">Tambah Pengguna</h3>
            <button id="closeModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="userForm" action="/api/users/store" method="POST" class="p-6">
            <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">
            <input type="hidden" id="userId" name="id">

            <div class="space-y-4">
                <div>
                    <label for="userName" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" id="userName" name="name"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                        required>
                </div>
                <div>
                    <label for="userUsername" class="block text-sm font-medium text-slate-700 mb-1">Username</label>
                    <input type="text" id="userUsername" name="username"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                        required>
                </div>
                <div>
                    <label for="userEmail" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" id="userEmail" name="email"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                        required>
                </div>
                <div>
                    <label for="userPassword" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <div class="relative">
                        <input type="password" id="userPassword" name="password"
                            minlength="8"
                            class="w-full px-3 py-2 pr-10 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <button type="button" class="toggle-password absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600" data-target="userPassword">
                            <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Minimal 8 karakter. Kosongkan jika tidak ingin mengubah password</p>
                </div>
                <div>
                    <label for="userRole" class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                    <select id="userRole" name="role"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <option value="admin">Admin</option>
                        <option value="gtk">GTK (Guru/Tenaga Kependidikan)</option>
                        <option value="murid">Murid</option>
                        <option value="ekskul">Ekskul</option>
                    </select>
                </div>
                <!-- GTK Permission Checkboxes -->
                <div id="permissionSection" class="hidden">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Akses Fitur</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="permissions[]" value="berita"
                                class="perm-check w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                checked> Berita
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="permissions[]" value="kategori"
                                class="perm-check w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                checked> Kategori
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="permissions[]" value="galeri"
                                class="perm-check w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                checked> Galeri
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="permissions[]" value="slider"
                                class="perm-check w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                checked> Slider Hero
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="permissions[]" value="profil"
                                class="perm-check w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                checked> Profil
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="permissions[]" value="fasilitas"
                                class="perm-check w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                checked> Fasilitas
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="permissions[]" value="staff"
                                class="perm-check w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                checked> GTK/Staff
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="permissions[]" value="spmb"
                                class="perm-check w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                checked> SPMB
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="permissions[]" value="prestasi"
                                class="perm-check w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                checked> Prestasi
                        </label>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">Centang fitur yang boleh diakses oleh user GTK ini</p>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="userActive" name="is_active" value="1" checked
                        class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="userActive" class="text-sm text-slate-700">Aktif</label>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
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
        const modal = document.getElementById('userModal');
        const form = document.getElementById('userForm');
        const btnAdd = document.getElementById('btnAddUser');
        const btnClose = document.getElementById('closeModal');
        const btnCancel = document.getElementById('cancelBtn');
        const modalTitle = document.getElementById('modalTitle');
        const roleSelect = document.getElementById('userRole');
        const permSection = document.getElementById('permissionSection');
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : document.querySelector('input[name="csrf_token"]')?.value;
        const showAlert = (title, message, icon = 'info') => {
            if (window.Swal) {
                return Swal.fire(title, message, icon);
            }

            alert([title, message].filter(Boolean).join('\n'));
            return Promise.resolve();
        };
        const confirmAction = (options) => {
            if (window.Swal) {
                return Swal.fire(options).then(result => result.isConfirmed);
            }

            return Promise.resolve(confirm(options.text || options.title || 'Lanjutkan?'));
        };
        const reloadAfterAlert = (title, message, icon = 'success') => {
            if (window.Swal) {
                return Swal.fire(title, message, icon).then(() => location.reload());
            }

            alert([title, message].filter(Boolean).join('\n'));
            location.reload();
            return Promise.resolve();
        };

        // Toggle permission section based on role
        function togglePermissions() {
            if (roleSelect.value === 'gtk') {
                permSection.classList.remove('hidden');
            } else {
                permSection.classList.add('hidden');
            }
        }
        roleSelect?.addEventListener('change', togglePermissions);

        // Reset form
        function resetForm() {
            form.reset();
            document.getElementById('userId').value = '';
            document.getElementById('userPassword').required = true;
            document.getElementById('userUsername').readOnly = false;
            form.action = '/api/users/store';
            modalTitle.textContent = 'Tambah Pengguna';
            // Reset all permission checkboxes to checked
            document.querySelectorAll('.perm-check').forEach(cb => cb.checked = true);
            // Reset password visibility
            const pwInput = document.getElementById('userPassword');
            if (pwInput) pwInput.type = 'password';
            const pwToggle = document.querySelector('.toggle-password[data-target="userPassword"]');
            if (pwToggle) {
                pwToggle.querySelector('.eye-open').classList.remove('hidden');
                pwToggle.querySelector('.eye-closed').classList.add('hidden');
            }
            togglePermissions();
        }

        // Open modal for adding
        btnAdd?.addEventListener('click', () => {
            resetForm();
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
        modal?.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        // Edit buttons
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                resetForm();
                const id = btn.dataset.id;
                document.getElementById('userId').value = id;
                document.getElementById('userName').value = btn.dataset.name;
                document.getElementById('userUsername').value = btn.dataset.username;
                document.getElementById('userUsername').readOnly = true;
                document.getElementById('userEmail').value = btn.dataset.email;
                roleSelect.value = btn.dataset.role;
                document.getElementById('userActive').checked = btn.dataset.active === '1';
                document.getElementById('userPassword').required = false;
                form.action = '/api/users/update/' + id;
                modalTitle.textContent = 'Edit Pengguna';

                // Set permissions for GTK
                if (btn.dataset.role === 'gtk') {
                    let perms = [];
                    try { perms = JSON.parse(btn.dataset.permissions || '[]'); } catch (e) { }
                    document.querySelectorAll('.perm-check').forEach(cb => {
                        cb.checked = perms.length === 0 || perms.includes(cb.value);
                    });
                }

                togglePermissions();
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            });
        });

        // Delete buttons
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                const name = btn.dataset.name;
                confirmAction({
                    title: 'Hapus Pengguna?',
                    text: 'Apakah Anda yakin ingin menghapus "' + name + '"?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then(isConfirmed => {
                    if (isConfirmed) {
                        fetch('/api/users/delete/' + id, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ csrf_token: csrfToken })
                        })
                            .then(r => r.json())
                            .then(data => {
                                if (data.success) {
                                    reloadAfterAlert('Terhapus!', data.message || 'Pengguna berhasil dihapus', 'success');
                                } else {
                                    showAlert('Gagal', data.message || 'Gagal menghapus', 'error');
                                }
                            })
                            .catch(() => showAlert('Error', 'Terjadi kesalahan', 'error'));
                    }
                });
            });
        });

        // Form submit
        form?.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const endpoint = this.action;

            // Explicitly send is_active=0 when checkbox is unchecked
            if (!document.getElementById('userActive').checked) {
                formData.set('is_active', '0');
            }

            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        reloadAfterAlert('Berhasil!', data.message || 'Data tersimpan', 'success');
                    } else {
                        showAlert('Gagal', data.message || 'Gagal menyimpan', 'error');
                    }
                })
                .catch(() => showAlert('Error', 'Terjadi kesalahan', 'error'));
        });
    });
</script>
