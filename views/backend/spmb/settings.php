<?php
/**
 * Backend - SPMB Settings View
 */
$title = $data['title'] ?? 'Pengaturan SPMB';
$settings = $data['settings'] ?? [];
$availableDocuments = $data['availableDocuments'] ?? [];
$flash = $data['flash'] ?? null;

// Parse the saved documents JSON array
$savedDocumentsRaw = $settings['spmb_documents'] ?? '[]';
$selectedDocuments = json_decode($savedDocumentsRaw, true);
if (!is_array($selectedDocuments)) {
    $selectedDocuments = [];
}
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3">
                <a href="/admin/spmb" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-slate-800">
                    <?= e($title) ?>
                </h1>
            </div>
            <p class="text-slate-500 mt-1 ml-9">Konfigurasi Sistem Penerimaan Murid Baru</p>
        </div>
    </div>

    <!-- SPMB Settings -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-lg font-semibold text-slate-800">Pengaturan Utama</h2>
        </div>
        <form id="spmbSettingsForm" class="p-6 space-y-6">
            <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">

            <!-- Toggle Activation -->
            <div class="flex flex-col gap-2">
                <label class="block text-sm font-medium text-slate-700">Status Pendaftaran</label>
                <div class="flex items-center gap-3">
                    <input type="checkbox" id="spmbEnabled" name="spmb_enabled" value="1"
                        <?= !empty($settings['spmb_enabled']) ? 'checked' : '' ?>
                        class="w-5 h-5 text-primary-600 border-slate-300 rounded focus:ring-primary-500 cursor-pointer">
                    <label for="spmbEnabled" class="text-slate-700 cursor-pointer">Aktifkan formulir pendaftaran SPMB
                        untuk publik</label>
                </div>
            </div>

            <!-- Date Configuration -->
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="spmb_start_date" value="<?= e($settings['spmb_start_date'] ?? '') ?>"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="spmb_end_date" value="<?= e($settings['spmb_end_date'] ?? '') ?>"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>

            <!-- Quota Configuration -->
            <div class="pt-4 border-t border-slate-100">
                <div class="max-w-xs">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Daya Tampung / Kuota Murid</label>
                    <input type="number" name="spmb_quota" value="<?= e($settings['spmb_quota'] ?? '0') ?>" min="0"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="Misal: 100">
                    <p class="text-xs text-slate-500 mt-1">Biarkan 0 jika tidak ada batasan kuota.</p>
                </div>
            </div>

            <!-- Documents Configuration -->
            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-sm font-medium text-slate-700 mb-3">Dokumen Wajib/Persyaratan Pendaftaran</h3>
                <p class="text-slate-500 text-sm mb-4">Pilih file apa saja yang diwajibkan untuk diunggah oleh pendaftar
                    pada saat pengisian formulir.</p>

                <div class="grid sm:grid-cols-2 gap-3">
                    <?php foreach ($availableDocuments as $key => $label): ?>
                        <div class="flex items-center gap-3">
                            <input type="checkbox" id="doc_<?= e($key) ?>" name="spmb_documents[]" value="<?= e($key) ?>"
                                <?= in_array($key, $selectedDocuments) ? 'checked' : '' ?>
                                class="w-4 h-4 text-primary-600 border-slate-300 rounded focus:ring-primary-500 cursor-pointer">
                            <label for="doc_<?= e($key) ?>" class="text-slate-700 cursor-pointer">
                                <?= e($label) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Submission Setup -->
            <div class="pt-6 border-t border-slate-100 flex justify-end">
                <button type="submit"
                    class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('spmbSettingsForm')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        // Ensure spmb_enabled is sent as 0 if unchecked
        if (!formData.has('spmb_enabled')) {
            formData.append('spmb_enabled', '0');
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        fetch('/admin/spmb/pengaturan/update', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        }).then(r => r.json()).then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message || 'Pengaturan SPMB berhasil disimpan',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message || 'Gagal menyimpan pengaturan SPMB'
                });
            }
        }).catch(err => {
            console.error(err);
            Swal.fire('Error', 'Terjadi kesalahan pada server', 'error');
        });
    });
</script>