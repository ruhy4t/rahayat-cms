<?php
/**
 * Backend - Detail Pendaftaran SPMB
 */
$title = $data['title'] ?? 'Detail Pendaftaran';
$registration = $data['registration'] ?? null;
$documents = $data['documents'] ?? [];
$flash = $data['flash'] ?? null;
$user = $data['user'] ?? [];

if (!$registration) {
    echo '<div class="p-4 bg-red-50 text-red-800 rounded-lg">Data pendaftaran tidak ditemukan.</div>';
    return;
}

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

// Document labels mapper
$docLabels = [
    'akta_kelahiran' => 'Akta Kelahiran',
    'kartu_keluarga' => 'Kartu Keluarga',
    'ktp_ortu' => 'KTP Orang Tua/Wali',
    'pas_foto' => 'Pas Foto 3x4',
    'ijazah' => 'Ijazah / SKL',
    'rapor' => 'Scan Rapor Terakhir',
];
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <a href="/admin/spmb"
                    class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-slate-800">
                    <?= e($title) ?>
                </h1>
            </div>
            <p class="text-slate-500 mt-1 ml-12">No. Registrasi: <span class="font-mono font-bold text-primary-600">
                    <?= e($registration['registration_number']) ?>
                </span></p>
        </div>

        <?php $color = $statusColors[$registration['status']] ?? 'gray'; ?>
        <div class="flex items-center gap-3">
            <span
                class="px-4 py-2 text-sm font-semibold rounded-full bg-<?= $color ?>-100 text-<?= $color ?>-800 border border-<?= $color ?>-200">
                Status:
                <?= $statusLabels[$registration['status']] ?? $registration['status'] ?>
            </span>
            <button onclick="openStatusModal()"
                class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                Ubah Status
            </button>
        </div>
    </div>

    <?php if ($flash): ?>
        <div
            class="p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200' ?>">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Data Murid & Ortu -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Data Diri Murid -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Data Diri Calon Murid
                    </h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-slate-500">Nama Lengkap</dt>
                            <dd class="mt-1 text-lg font-semibold text-slate-900">
                                <?= e($registration['student_name']) ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">NISN</dt>
                            <dd class="mt-1 text-base text-slate-900 font-mono">
                                <?= e($registration['nisn'] ?? '-') ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">NIK</dt>
                            <dd class="mt-1 text-base text-slate-900 font-mono">
                                <?= e($registration['nik'] ?? '-') ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Tempat, Tanggal Lahir</dt>
                            <dd class="mt-1 text-base text-slate-900">
                                <?= e($registration['birth_place']) ?>,
                                <?= date('d F Y', strtotime($registration['birth_date'])) ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Jenis Kelamin</dt>
                            <dd class="mt-1 text-base text-slate-900">
                                <?= $registration['gender'] === 'L' ? 'Laki-laki' : 'Perempuan' ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Agama</dt>
                            <dd class="mt-1 text-base text-slate-900">
                                <?= e($registration['religion']) ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">No. HP/WhatsApp</dt>
                            <dd class="mt-1 text-base text-slate-900">
                                <?= e($registration['phone']) ?>
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-slate-500">Email</dt>
                            <dd class="mt-1 text-base text-slate-900">
                                <?= e($registration['email'] ?: '-') ?>
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-slate-500">Alamat Lengkap</dt>
                            <dd class="mt-1 text-base text-slate-900">
                                <?= e($registration['address']) ?><br>
                                Ds/Kel.
                                <?= e($registration['address_village'] ?? '-') ?>,
                                Kec.
                                <?= e($registration['address_district'] ?? '-') ?><br>
                                <?= e($registration['address_city'] ?? '-') ?>,
                                Prov.
                                <?= e($registration['address_province'] ?? '-') ?>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Data Orang Tua -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Data Orang Tua / Wali
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Data Ayah -->
                        <div>
                            <h4 class="text-sm font-bold text-slate-700 border-b border-slate-200 pb-2 mb-4">Data Ayah
                            </h4>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-slate-500">Nama Ayah</dt>
                                    <dd class="mt-1 text-base text-slate-900">
                                        <?= e($registration['father_name']) ?>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-slate-500">Pekerjaan</dt>
                                    <dd class="mt-1 text-base text-slate-900">
                                        <?= e($registration['father_occupation'] ?: '-') ?>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-slate-500">No. HP</dt>
                                    <dd class="mt-1 text-base text-slate-900">
                                        <?= e($registration['father_phone'] ?: '-') ?>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        <!-- Data Ibu -->
                        <div>
                            <h4 class="text-sm font-bold text-slate-700 border-b border-slate-200 pb-2 mb-4">Data Ibu
                            </h4>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-slate-500">Nama Ibu</dt>
                                    <dd class="mt-1 text-base text-slate-900">
                                        <?= e($registration['mother_name']) ?>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-slate-500">Pekerjaan</dt>
                                    <dd class="mt-1 text-base text-slate-900">
                                        <?= e($registration['mother_occupation'] ?: '-') ?>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-slate-500">No. HP</dt>
                                    <dd class="mt-1 text-base text-slate-900">
                                        <?= e($registration['mother_phone'] ?: '-') ?>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sekolah Asal -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Sekolah Asal
                    </h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-slate-500">Nama Sekolah</dt>
                            <dd class="mt-1 text-base font-semibold text-slate-900">
                                <?= e($registration['previous_school'] ?: '-') ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">NPSN Sekolah</dt>
                            <dd class="mt-1 text-base text-slate-900 font-mono">
                                <?= e($registration['previous_school_npsn'] ?? '-') ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Tahun Lulus</dt>
                            <dd class="mt-1 text-base text-slate-900">
                                <?= e($registration['graduation_year']) ?>
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-slate-500">Alamat Sekolah</dt>
                            <dd class="mt-1 text-base text-slate-900">
                                <?= e($registration['previous_school_address'] ?: '-') ?>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

        </div>

        <!-- Right Column: Documents & Status Info -->
        <div class="space-y-6">

            <!-- Dokumen Lampiran -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Dokumen Lampiran
                    </h3>
                </div>
                <div class="p-6">
                    <?php if (empty($documents)): ?>
                        <div
                            class="text-center py-6 text-slate-500 bg-slate-50 rounded-lg border border-slate-100 border-dashed">
                            Belum ada dokumen yang diunggah
                        </div>
                    <?php else: ?>
                        <ul class="space-y-3">
                            <?php foreach ($documents as $type => $path): ?>
                                <li
                                    class="flex items-center justify-between p-3 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-8 h-8 text-primary-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-sm font-medium text-slate-700">
                                            <?= $docLabels[$type] ?? ucwords(str_replace('_', ' ', $type)) ?>
                                        </span>
                                    </div>
                                    <a href="/storage/<?= e($path) ?>" target="_blank"
                                        class="text-sm text-primary-600 hover:text-primary-800 font-medium px-3 py-1 bg-primary-50 rounded-md">
                                        Lihat
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Catatan Review -->
            <?php if (!empty($registration['notes'])): ?>
                <div class="bg-yellow-50 rounded-xl shadow-sm border border-yellow-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-yellow-200/50 bg-yellow-100/50">
                        <h3 class="font-semibold text-yellow-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Catatan Internal
                        </h3>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-yellow-900 whitespace-pre-wrap">
                            <?= e($registration['notes']) ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Informasi Pendaftaran (Waktu) -->
            <div class="bg-slate-50 rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
                <div>
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Tanggal Mendaftar
                    </div>
                    <div class="text-sm text-slate-800 font-medium">
                        <?= date('d F Y, H:i', strtotime($registration['created_at'])) ?>
                    </div>
                </div>
                <?php if ($registration['reviewed_at']): ?>
                    <div>
                        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Terakhir Diupdate
                        </div>
                        <div class="text-sm text-slate-800 font-medium">
                            <?= date('d F Y, H:i', strtotime($registration['reviewed_at'])) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

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
        <form action="/admin/spmb/status/<?= $registration['id'] ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Pilih Status Baru</label>
                    <div class="grid grid-cols-2 gap-3">
                        <?php foreach ($statusLabels as $val => $label): ?>
                            <label class="cursor-pointer">
                                <input type="radio" name="status" value="<?= $val ?>" class="hidden peer"
                                    <?= $registration['status'] === $val ? 'checked' : '' ?>>
                                <div
                                    class="p-3 rounded-lg border-2 border-slate-200 peer-checked:border-<?= $statusColors[$val] ?>-500 peer-checked:bg-<?= $statusColors[$val] ?>-50 text-center transition-all">
                                    <span class="text-<?= $statusColors[$val] ?>-600 font-medium">
                                        <?= $label ?>
                                    </span>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Catatan Internal (opsional)</label>
                    <textarea name="notes" rows="3"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none"
                        placeholder="Catatan untuk pendaftar..."><?= e($registration['notes'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 p-6 border-t border-slate-100 bg-slate-50 rounded-b-xl">
                <button type="button" onclick="closeStatusModal()"
                    class="px-4 py-2 text-slate-600 hover:text-slate-800 font-medium">Batal</button>
                <button type="submit"
                    class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg">Simpan
                    Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('statusModal');
    const form = document.querySelector('form'); // Select the form inside the modal

    function openStatusModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeStatusModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeStatusModal();
    });

    // Handle form submission via AJAX
    form.addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent standard form submission

        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Menyimpan...';

        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Reload page to show updated status
                } else {
                    alert(data.message || 'Gagal mengubah status');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Simpan Perubahan';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan jaringan.');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Simpan Perubahan';
            });
    });
</script>