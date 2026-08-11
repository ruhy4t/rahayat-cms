<?php
/**
 * Backend - Ekstrakurikuler Management
 */
$title = $data['title'] ?? 'Kelola Ekstrakurikuler';
$user = $data['user'] ?? null;
$ekskul = $data['ekskul'] ?? [];
$flash = $data['flash'] ?? [];
?>

<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">
            <?= e($title) ?>
        </h1>
        <p class="text-slate-600 text-sm mt-1">Kelola daftar Ekstrakurikuler sekolah</p>
    </div>
    <button onclick="openModal()"
        class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Ekstrakurikuler
    </button>
</div>

<!-- Flash Messages -->
<?php if (!empty($flash) && isset($flash['type']) && isset($flash['message'])): ?>
    <div
        class="mb-6 p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' ?>">
        <?= e($flash['message']) ?>
    </div>
<?php endif; ?>

<!-- Ekstrakurikuler List -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (empty($ekskul)): ?>
        <div class="col-span-full py-12 text-center bg-white rounded-xl border border-slate-200 border-dashed">
            <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <p class="text-slate-500 font-medium">Belum ada ekstrakurikuler</p>
            <p class="text-slate-400 text-sm mt-1">Silakan tambahkan ekstrakurikuler baru</p>
        </div>
    <?php else: ?>
        <?php foreach ($ekskul as $item): ?>
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

                    <div class="space-y-1 my-3">
                        <?php if (!empty($item['supervisors'])): ?>
                            <p class="text-xs text-slate-600 flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Pembina/Pelatih:
                                <?= e(implode(', ', array_map(static fn (array $person): string => ($person['name'] ?? '') . ' (' . ($person['role'] ?? 'Pembina') . ')', $item['supervisors']))) ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($item['schedules'])): ?>
                            <p class="text-xs text-slate-600 flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Jadwal:
                                <?= e(implode(', ', array_map(static fn (array $schedule): string => trim(($schedule['day'] ?? '') . ' ' . ($schedule['time'] ?? '')), $item['schedules']))) ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($item['achievements'])): ?>
                            <p class="text-xs font-medium text-amber-700"><?= count($item['achievements']) ?> prestasi tercatat</p>
                        <?php endif; ?>
                    </div>

                    <p class="text-sm text-slate-600 line-clamp-2 mb-4 h-10">
                        <?= e($item['description'] ?? '-') ?>
                    </p>
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button onclick='editItem(<?= json_encode(Security::normalizeTextData($item), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) ?>)'
                            class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <form action="/admin/ekstrakurikuler/delete/<?= $item['id'] ?>" method="POST"
                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus ekstrakurikuler ini?');" class="inline">
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
        <form id="ekskulForm" action="/admin/ekstrakurikuler/store" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800" id="modalTitle">Tambah Ekstrakurikuler</h3>
                <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Ekstrakurikuler</label>
                    <input type="text" name="name" id="name" required
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>

                <div>
                    <div class="flex items-center justify-between gap-3 mb-2">
                        <label class="block text-sm font-medium text-slate-700">Pembina dan Pelatih</label>
                        <button type="button" onclick="addSupervisor()" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">+ Tambah orang</button>
                    </div>
                    <div id="supervisorsList" class="space-y-2"></div>
                    <p class="text-xs text-slate-500 mt-1">Tambahkan semua pembina atau pelatih yang terlibat.</p>
                </div>

                <div>
                    <div class="flex items-center justify-between gap-3 mb-2">
                        <label class="block text-sm font-medium text-slate-700">Jadwal Kegiatan</label>
                        <button type="button" onclick="addSchedule()" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">+ Tambah hari</button>
                    </div>
                    <div id="schedulesList" class="space-y-2"></div>
                    <p class="text-xs text-slate-500 mt-1">Satu ekstrakurikuler dapat memiliki kegiatan pada beberapa hari.</p>
                </div>

                <div>
                    <div class="flex items-center justify-between gap-3 mb-2">
                        <label class="block text-sm font-medium text-slate-700">Prestasi Ekstrakurikuler</label>
                        <button type="button" onclick="addAchievement()" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">+ Tambah prestasi</button>
                    </div>
                    <div id="achievementsList" class="space-y-2"></div>
                    <p class="text-xs text-slate-500 mt-1">Contoh: Juara 1 Futsal tingkat kabupaten.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Peringkat / Urutan Tampil</label>
                    <input type="number" name="sort_order" id="sort_order" value="0"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Gambar Banner / Kegiatan</label>
                    <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/gif,image/webp"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-xs text-slate-500 mt-1">Biarkan kosong jika tidak ingin mengubah gambar (saat edit).
                    </p>
                </div>

                <div class="flex items-center gap-2 mt-4">
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
    const form = document.getElementById('ekskulForm');
    const modalTitle = document.getElementById('modalTitle');
    const supervisorsList = document.getElementById('supervisorsList');
    const schedulesList = document.getElementById('schedulesList');
    const achievementsList = document.getElementById('achievementsList');
    const inputClass = 'w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors';

    function removeRow(button) {
        button.closest('[data-repeat-row]').remove();
    }

    function addSupervisor(item = {}) {
        const row = document.createElement('div');
        row.dataset.repeatRow = 'supervisor';
        row.className = 'grid grid-cols-[1fr_8rem_auto] gap-2';
        row.innerHTML = `<input type="text" name="supervisor_names[]" maxlength="100" placeholder="Nama lengkap" class="${inputClass}">
            <select name="supervisor_roles[]" class="${inputClass}"><option value="Pembina">Pembina</option><option value="Pelatih">Pelatih</option></select>
            <button type="button" onclick="removeRow(this)" class="px-3 text-red-600 hover:bg-red-50 rounded-lg" aria-label="Hapus pembina atau pelatih">&times;</button>`;
        row.querySelector('input').value = item.name || '';
        row.querySelector('select').value = item.role === 'Pelatih' ? 'Pelatih' : 'Pembina';
        supervisorsList.appendChild(row);
    }

    function addSchedule(item = {}) {
        const row = document.createElement('div');
        row.dataset.repeatRow = 'schedule';
        row.className = 'grid sm:grid-cols-[7rem_1fr_1fr_auto] gap-2';
        row.innerHTML = `<select name="schedule_days[]" class="${inputClass}">${['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'].map(day => `<option value="${day}">${day}</option>`).join('')}</select>
            <input type="text" name="schedule_times[]" maxlength="60" placeholder="15.00-17.00" class="${inputClass}">
            <input type="text" name="schedule_notes[]" maxlength="120" placeholder="Tempat/keterangan" class="${inputClass}">
            <button type="button" onclick="removeRow(this)" class="px-3 text-red-600 hover:bg-red-50 rounded-lg" aria-label="Hapus jadwal">&times;</button>`;
        row.querySelector('select').value = item.day || 'Senin';
        row.querySelector('[name="schedule_times[]"]').value = item.time || '';
        row.querySelector('[name="schedule_notes[]"]').value = item.note || '';
        schedulesList.appendChild(row);
    }

    function addAchievement(item = {}) {
        const row = document.createElement('div');
        row.dataset.repeatRow = 'achievement';
        row.className = 'grid grid-cols-[1fr_6rem_auto] gap-2';
        row.innerHTML = `<input type="text" name="achievement_titles[]" maxlength="180" placeholder="Nama prestasi" class="${inputClass}">
            <input type="text" name="achievement_years[]" inputmode="numeric" maxlength="4" placeholder="Tahun" class="${inputClass}">
            <button type="button" onclick="removeRow(this)" class="px-3 text-red-600 hover:bg-red-50 rounded-lg" aria-label="Hapus prestasi">&times;</button>`;
        row.querySelector('[name="achievement_titles[]"]').value = item.title || '';
        row.querySelector('[name="achievement_years[]"]').value = item.year || '';
        achievementsList.appendChild(row);
    }

    function resetRepeaters() {
        supervisorsList.replaceChildren();
        schedulesList.replaceChildren();
        achievementsList.replaceChildren();
        addSupervisor();
        addSchedule();
        addAchievement();
    }

    function openModal() {
        form.reset();
        form.action = '/admin/ekstrakurikuler/store';
        modalTitle.textContent = 'Tambah Ekstrakurikuler';
        document.getElementById('is_active').checked = true;
        resetRepeaters();

        showModal();
    }

    function editItem(item) {
        form.action = `/admin/ekstrakurikuler/update/${item.id}`;
        modalTitle.textContent = 'Edit Ekstrakurikuler';

        document.getElementById('name').value = item.name;
        document.getElementById('sort_order').value = item.sort_order;
        document.getElementById('description').value = item.description || '';
        document.getElementById('is_active').checked = item.is_active == 1;

        supervisorsList.replaceChildren();
        schedulesList.replaceChildren();
        achievementsList.replaceChildren();
        (item.supervisors?.length ? item.supervisors : [{}]).forEach(addSupervisor);
        (item.schedules?.length ? item.schedules : [{}]).forEach(addSchedule);
        (item.achievements?.length ? item.achievements : [{}]).forEach(addAchievement);

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
