<?php
/**
 * Backend - SPMB Management View
 */
$title = $data['title'] ?? 'Kelola SPMB';
$registrations = $data['registrations'] ?? [];
$stats = $data['stats'] ?? [];
$flash = $data['flash'] ?? null;
$currentStatus = $data['currentStatus'] ?? null;

$statusLabels = [
    'pending' => 'Menunggu',
    'review' => 'Dalam Review',
    'accepted' => 'Diterima',
    'rejected' => 'Ditolak'
];

$statusColors = [
    'pending' => 'yellow',
    'review' => 'blue',
    'accepted' => 'green',
    'rejected' => 'red'
];
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                <?= e($title) ?>
            </h1>
            <p class="text-slate-500 mt-1">Sistem Penerimaan Murid Baru</p>
        </div>
        <?php if (($user['role'] ?? '') === 'admin' || !empty($user['is_spmb_committee'])): ?>
            <a href="/admin/spmb/pengaturan"
                class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium rounded-lg transition-colors flex items-center gap-2 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Pengaturan
            </a>
        <?php endif; ?>
    </div>

    <?php if ($flash): ?>
        <div
            class="p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200' ?>">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm">
            <div class="text-2xl font-bold text-slate-800">
                <?= (int) ($stats['total'] ?? 0) ?>
            </div>
            <div class="text-sm text-slate-500">Total Pendaftar</div>
        </div>
        <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-200">
            <div class="text-2xl font-bold text-yellow-700">
                <?= (int) ($stats['pending'] ?? 0) ?>
            </div>
            <div class="text-sm text-yellow-600">Menunggu</div>
        </div>
        <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
            <div class="text-2xl font-bold text-blue-700">
                <?= (int) ($stats['review'] ?? 0) ?>
            </div>
            <div class="text-sm text-blue-600">Dalam Review</div>
        </div>
        <div class="bg-green-50 rounded-xl p-4 border border-green-200">
            <div class="text-2xl font-bold text-green-700">
                <?= (int) ($stats['accepted'] ?? 0) ?>
            </div>
            <div class="text-sm text-green-600">Diterima</div>
        </div>
        <div class="bg-red-50 rounded-xl p-4 border border-red-200">
            <div class="text-2xl font-bold text-red-700">
                <?= (int) ($stats['rejected'] ?? 0) ?>
            </div>
            <div class="text-sm text-red-600">Ditolak</div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex gap-2 overflow-x-auto pb-2">
        <a href="/admin/spmb"
            class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap <?= !$currentStatus ? 'bg-primary-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
            Semua
        </a>
        <?php foreach ($statusLabels as $key => $label): ?>
            <a href="/admin/spmb?status=<?= $key ?>"
                class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap <?= $currentStatus === $key ? 'bg-primary-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                <?= $label ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Registrations Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">No. Registrasi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Nama Murid</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Asal Sekolah</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-600 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Tanggal Daftar
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-slate-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($registrations['data'] ?? $registrations)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Belum ada pendaftaran
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach (($registrations['data'] ?? $registrations) as $reg): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-mono text-sm font-medium text-primary-600">
                                        <?= e($reg['registration_number']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-800">
                                        <?= e($reg['student_name']) ?>
                                    </div>
                                    <div class="text-sm text-slate-500">
                                        <?= e($reg['email'] ?? '-') ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    <?= e($reg['previous_school'] ?? '-') ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php $color = $statusColors[$reg['status']] ?? 'gray'; ?>
                                    <span
                                        class="px-3 py-1 text-xs font-medium rounded-full bg-<?= $color ?>-100 text-<?= $color ?>-700">
                                        <?= $statusLabels[$reg['status']] ?? $reg['status'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    <?= date('d M Y', strtotime($reg['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center gap-2">
                                        <button onclick="viewDetail(<?= $reg['id'] ?>)"
                                            class="p-1.5 text-slate-500 hover:text-primary-600 hover:bg-primary-50 rounded transition-colors"
                                            title="Lihat Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <button onclick="openStatusModal(<?= e(json_encode($reg)) ?>)"
                                            class="p-1.5 text-slate-500 hover:text-green-600 hover:bg-green-50 rounded transition-colors"
                                            title="Ubah Status">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
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
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <h3 class="text-lg font-semibold text-slate-800">Ubah Status Pendaftaran</h3>
            <button onclick="closeStatusModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="statusForm" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">
            <input type="hidden" id="regId" name="id" value="">
            <div class="p-6 space-y-4">
                <div>
                    <p class="text-sm text-slate-500 mb-2">Pendaftaran:</p>
                    <p id="regInfo" class="font-medium text-slate-800"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Status Baru</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="status" value="pending" class="hidden peer">
                            <div
                                class="p-3 rounded-lg border-2 border-slate-200 peer-checked:border-yellow-500 peer-checked:bg-yellow-50 text-center transition-all">
                                <span class="text-yellow-600 font-medium">Menunggu</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="status" value="review" class="hidden peer">
                            <div
                                class="p-3 rounded-lg border-2 border-slate-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 text-center transition-all">
                                <span class="text-blue-600 font-medium">Review</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="status" value="accepted" class="hidden peer">
                            <div
                                class="p-3 rounded-lg border-2 border-slate-200 peer-checked:border-green-500 peer-checked:bg-green-50 text-center transition-all">
                                <span class="text-green-600 font-medium">Diterima</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="status" value="rejected" class="hidden peer">
                            <div
                                class="p-3 rounded-lg border-2 border-slate-200 peer-checked:border-red-500 peer-checked:bg-red-50 text-center transition-all">
                                <span class="text-red-600 font-medium">Ditolak</span>
                            </div>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Catatan (opsional)</label>
                    <textarea name="notes" rows="3"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Catatan untuk pendaftar..."></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 p-6 border-t border-slate-100 bg-slate-50 rounded-b-xl">
                <button type="button" onclick="closeStatusModal()"
                    class="px-4 py-2 text-slate-600 hover:text-slate-800 font-medium">Batal</button>
                <button type="submit"
                    class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('statusModal');
    const form = document.getElementById('statusForm');

    function viewDetail(id) {
        window.location.href = '/admin/spmb/' + id;
    }

    function openStatusModal(reg) {
        document.getElementById('regId').value = reg.id;
        document.getElementById('regInfo').textContent = reg.registration_number + ' - ' + reg.student_name;
        form.action = '/admin/spmb/status/' + reg.id;

        // Check current status
        const statusRadio = document.querySelector('input[name="status"][value="' + reg.status + '"]');
        if (statusRadio) statusRadio.checked = true;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeStatusModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
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
            else alert(data.message || 'Gagal mengubah status');
        });
    });

    modal.addEventListener('click', (e) => { if (e.target === modal) closeStatusModal(); });
</script>