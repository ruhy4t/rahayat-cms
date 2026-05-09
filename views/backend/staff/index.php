<?php
/**
 * Backend - Staff (GTK) Management
 */
$title = $data['title'] ?? 'Kelola GTK';
$staffList = $data['staff'] ?? [];
$flash = $data['flash'] ?? [];
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">
            <?= e($title) ?>
        </h1>
        <p class="text-slate-600 text-sm mt-1">Kelola data Guru dan Tenaga Kependidikan</p>
    </div>
    <button id="btnAddStaff"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-md hover:shadow-lg w-full sm:w-auto justify-center">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah GTK
    </button>
</div>

<!-- Flash Messages -->
<?php if (!empty($flash) && isset($flash['type']) && isset($flash['message'])): ?>
    <div
        class="mb-4 p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' ?>">
        <?= e($flash['message']) ?>
    </div>
<?php endif; ?>

<!-- Staff Table -->
<div class="bg-white rounded-xl shadow-md overflow-hidden border border-slate-100">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Profil
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                        Jabatan & Mengajar</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Kontak
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Urutan
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Status
                    </th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100" id="staffTableBody">
                <?php if (empty($staffList)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                            Belum ada data GTK terdaftar
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($staffList as $s): ?>
                        <tr class="hover:bg-slate-50 transition-colors" data-id="<?= $s['id'] ?>">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <?php if (!empty($s['photo'])): ?>
                                        <img src="/storage/<?= e($s['photo']) ?>" alt="<?= e($s['name']) ?>"
                                            class="w-12 h-12 rounded-full object-cover border border-slate-200">
                                    <?php else: ?>
                                        <div
                                            class="w-12 h-12 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full flex items-center justify-center text-white font-semibold text-lg">
                                            <?= strtoupper(substr($s['name'] ?? 'G', 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>

                                    <div>
                                        <div class="font-medium text-slate-800">
                                            <?= e($s['name']) ?>
                                            <?php if ($s['is_teacher']): ?>
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800 ml-2">Guru</span>
                                            <?php else: ?>
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-amber-100 text-amber-800 ml-2">Tendik</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-xs text-slate-500 font-mono mt-0.5">
                                            NIP:
                                            <?= e($s['nip'] ?: '-') ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-slate-700">
                                    <?= e($s['position'] ?: '-') ?>
                                </div>
                                <div class="text-xs text-slate-500 mt-1">
                                    <?= e($s['subject'] ?: '-') ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                <?php if ($s['email']): ?>
                                    <div class="flex items-center gap-2 mb-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <?= e($s['email']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($s['phone']): ?>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                            </path>
                                        </svg>
                                        <?= e($s['phone']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                <?= (int) $s['sort_order'] ?>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $s['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= $s['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <button
                                        class="btn-edit p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded transition-colors"
                                        title="Edit" data-id="<?= $s['id'] ?>" data-name="<?= e($s['name']) ?>"
                                        data-nip="<?= e($s['nip']) ?>" data-position="<?= e($s['position']) ?>"
                                        data-subject="<?= e($s['subject']) ?>" data-email="<?= e($s['email']) ?>"
                                        data-phone="<?= e($s['phone']) ?>" data-isteacher="<?= $s['is_teacher'] ? '1' : '0' ?>"
                                        data-active="<?= $s['is_active'] ? '1' : '0' ?>" data-order="<?= $s['sort_order'] ?>"
                                        data-photo="<?= $s['photo'] ? '/storage/' . e($s['photo']) : '' ?>">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button
                                        class="btn-delete p-1.5 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded transition-colors"
                                        title="Hapus" data-id="<?= $s['id'] ?>" data-name="<?= e($s['name']) ?>">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

<!-- Add/Edit Staff Modal -->
<div id="staffModal"
    class="fixed inset-0 bg-black/50 z-50 hidden items-start pt-10 pb-10 sm:pt-20 sm:items-center justify-center overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 my-auto relative shrink-0">
        <div
            class="px-6 py-4 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10 rounded-t-xl">
            <h3 id="modalTitle" class="text-lg font-semibold text-slate-800">Tambah GTK</h3>
            <button id="closeModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="staffForm" action="/admin/gtk/store" method="POST" enctype="multipart/form-data" class="p-6">
            <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">
            <input type="hidden" id="staffId" name="id">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kolom Kiri -->
                <div class="space-y-4">
                    <div>
                        <label for="staffName" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap &
                            Gelar</label>
                        <input type="text" id="staffName" name="name"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            required>
                    </div>
                    <div>
                        <label for="staffNip" class="block text-sm font-medium text-slate-700 mb-1">NIP / NUPTK</label>
                        <input type="text" id="staffNip" name="nip"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="staffEmail" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                            <input type="email" id="staffEmail" name="email"
                                class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        </div>
                        <div>
                            <label for="staffPhone" class="block text-sm font-medium text-slate-700 mb-1">No. HP /
                                WA</label>
                            <input type="text" id="staffPhone" name="phone"
                                class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Tugas / Kategori</label>
                        <div class="flex gap-4">
                            <label
                                class="flex items-center gap-2 cursor-pointer bg-slate-50 border border-slate-200 px-4 py-2 rounded-lg flex-1 hover:bg-indigo-50 hover:border-indigo-200 transition-colors"
                                id="teacherLabel">
                                <input type="radio" name="is_teacher" value="1" checked
                                    class="text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                <span class="text-sm font-medium text-slate-700">Tenaga Pendidik (Guru)</span>
                            </label>
                            <label
                                class="flex items-center gap-2 cursor-pointer bg-slate-50 border border-slate-200 px-4 py-2 rounded-lg flex-1 hover:bg-amber-50 hover:border-amber-200 transition-colors"
                                id="staffLabel">
                                <input type="radio" name="is_teacher" value="0"
                                    class="text-amber-600 focus:ring-amber-500 cursor-pointer">
                                <span class="text-sm font-medium text-slate-700">Tenaga Kependidikan</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="space-y-4">
                    <div>
                        <label for="staffPosition" class="block text-sm font-medium text-slate-700 mb-1">Jabatan /
                            Posisi</label>
                        <input type="text" id="staffPosition" name="position"
                            placeholder="Contoh: Wali Kelas 1A / Kepala TU"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                    <div id="subjectGroup">
                        <label for="staffSubject" class="block text-sm font-medium text-slate-700 mb-1">Mata Pelajaran
                            (Khusus Guru)</label>
                        <input type="text" id="staffSubject" name="subject" placeholder="Contoh: Matematika"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Urutan Tampil</label>
                        <input type="number" id="staffOrder" name="sort_order" value="0"
                            class="w-1/3 px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        <p class="text-[10px] text-slate-500 mt-1">Angka lebih kecil tampil lebih awal</p>
                    </div>

                    <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl mt-2">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-slate-700">Foto Profil</label>
                        </div>
                        <div class="flex items-center gap-4">
                            <div id="photoPreview"
                                class="w-16 h-16 rounded-xl bg-slate-200 flex items-center justify-center overflow-hidden shrink-0">
                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <label
                                    class="border border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-3 py-1.5 rounded-lg text-sm font-medium cursor-pointer transition-colors inline-block text-center mr-2">
                                    <input type="file" name="photo" id="photoInput" class="hidden"
                                        accept="image/jpeg,image/png,image/webp">
                                    Pilih Foto
                                </label>
                                <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG, WEBP. Maks: 2MB</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2 border-t border-slate-100 mt-4">
                        <input type="checkbox" id="staffActive" name="is_active" value="1" checked
                            class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                        <label for="staffActive" class="text-sm font-medium text-slate-700">Status Aktif</label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-slate-100">
                <button type="button" id="cancelBtn"
                    class="px-5 py-2 border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 font-medium transition-colors">Batal</button>
                <button type="submit"
                    class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition-colors shadow-md">Simpan
                    Data</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('staffModal');
        const form = document.getElementById('staffForm');
        const btnAdd = document.getElementById('btnAddStaff');
        const photoInput = document.getElementById('photoInput');
        const photoPreview = document.getElementById('photoPreview');
        const defaultPhotoHtml = '<svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>';

        // Toggle subject input based on teacher/staff radio
        const radios = document.querySelectorAll('input[name="is_teacher"]');
        radios.forEach(r => {
            r.addEventListener('change', (e) => {
                const subjectDiv = document.getElementById('subjectGroup');
                if (e.target.value === '1') {
                    subjectDiv.style.opacity = '1';
                    subjectDiv.querySelectorAll('input').forEach(i => i.disabled = false);
                } else {
                    subjectDiv.style.opacity = '0.5';
                    subjectDiv.querySelectorAll('input').forEach(i => {
                        i.disabled = true;
                        i.value = '';
                    });
                }
            });
        });

        const resetForm = () => {
            form.reset();
            form.action = '/admin/gtk/store';
            document.getElementById('modalTitle').textContent = 'Tambah GTK';
            document.getElementById('staffId').value = '';
            photoPreview.innerHTML = defaultPhotoHtml;
            document.getElementById('staffOrder').value = '0';
            document.getElementById('subjectGroup').style.opacity = '1';
            document.getElementById('subjectGroup').querySelectorAll('input').forEach(i => i.disabled = false);
        };

        const openModal = () => {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        };

        const closeModal = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };

        btnAdd?.addEventListener('click', () => {
            resetForm();
            openModal();
        });

        document.getElementById('closeModal')?.addEventListener('click', closeModal);
        document.getElementById('cancelBtn')?.addEventListener('click', closeModal);

        modal?.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        // Handle Edit
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                resetForm();
                const d = btn.dataset;
                document.getElementById('staffId').value = d.id;
                document.getElementById('staffName').value = d.name;
                document.getElementById('staffNip').value = d.nip;
                document.getElementById('staffPosition').value = d.position;
                document.getElementById('staffSubject').value = d.subject;
                document.getElementById('staffEmail').value = d.email;
                document.getElementById('staffPhone').value = d.phone;
                document.getElementById('staffOrder').value = d.order;
                document.getElementById('staffActive').checked = d.active === '1';

                const radioNode = document.querySelector(`input[name="is_teacher"][value="${d.isteacher}"]`);
                if (radioNode) {
                    radioNode.checked = true;
                    radioNode.dispatchEvent(new Event('change'));
                }

                if (d.photo) {
                    photoPreview.innerHTML = '<img src="' + d.photo + '" class="w-full h-full object-cover">';
                }

                form.action = '/admin/gtk/update/' + d.id;
                document.getElementById('modalTitle').textContent = 'Edit Data GTK';
                openModal();
            });
        });

        // Handle Delete via Form Submission
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', () => {
                Swal.fire({
                    title: 'Hapus Data GTK?',
                    text: 'Yakin menghapus "' + btn.dataset.name + '"?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '/admin/gtk/delete/' + btn.dataset.id;

                        const csrf = document.createElement('input');
                        csrf.type = 'hidden';
                        csrf.name = 'csrf_token';
                        csrf.value = document.querySelector('input[name="csrf_token"]').value;

                        form.appendChild(csrf);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });

        // Photo Preview
        photoInput?.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    photoPreview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>