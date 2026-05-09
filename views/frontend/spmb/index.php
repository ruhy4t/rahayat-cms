<?php
/**
 * SPMB Landing Page View
 */

// Formatting dates
$fmtStart = $startDate ? date('d F Y', strtotime($startDate)) : '-';
$fmtEnd = $endDate ? date('d F Y', strtotime($endDate)) : '-';

$isActive = false;
if ($startDate && $endDate) {
    $now = date('Y-m-d');
    if ($now >= $startDate && $now <= $endDate) {
        $isActive = true;
    }
}
?>

<div class="bg-primary-600 pb-24 pt-12 sm:pb-32 sm:pt-16 lg:pb-32 lg:pt-20">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl">
                Sistem Penerimaan Murid Baru
            </h1>
            <p class="mt-6 text-lg leading-8 text-primary-100">
                Pendaftaran murid baru tahun ajaran
                <?= date('Y') ?>/
                <?= date('Y') + 1 ?> di
                <?= e($profile['name'] ?? SCHOOL_NAME) ?> telah dibuka. Daftarkan putra/putri Anda secara online dengan
                mudah.
            </p>
        </div>
    </div>
</div>

<div class="-mt-16 sm:-mt-24 mx-auto max-w-7xl px-6 lg:px-8 pb-12">
    <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:gap-12">
        <!-- Informasi Pendaftaran -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden ring-1 ring-slate-200">
            <div class="p-8 sm:p-10">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center text-primary-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">Jadwal Pendaftaran</h3>
                </div>

                <div class="space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-slate-50 rounded-xl">
                        <span class="text-slate-600 font-medium">Tanggal Buka</span>
                        <span class="text-slate-900 font-semibold text-lg">
                            <?= $fmtStart ?>
                        </span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-slate-50 rounded-xl">
                        <span class="text-slate-600 font-medium">Tanggal Tutup</span>
                        <span class="text-slate-900 font-semibold text-lg">
                            <?= $fmtEnd ?>
                        </span>
                    </div>
                </div>

                <?php if ($quota > 0): ?>
                    <div class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-100 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-1">Daya Tampung
                            </div>
                            <div class="text-2xl font-bold text-blue-900"><?= number_format($quota) ?> <span
                                    class="text-base font-medium text-blue-700">Murid</span></div>
                        </div>
                        <div class="h-10 w-px bg-blue-200"></div>
                        <div class="text-right">
                            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Telah Mendaftar
                            </div>
                            <div class="text-2xl font-bold text-slate-800"><?= number_format($totalRegistered) ?> <span
                                    class="text-base font-medium text-slate-600">Pendaftar</span></div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mt-8">
                    <?php if ($isActive): ?>
                        <div
                            class="bg-green-50 text-green-800 p-4 rounded-xl border border-green-200 mb-6 flex items-start gap-3">
                            <svg class="w-6 h-6 text-green-600 shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="font-medium">Masa pendaftaran sedang berlangsung. Silakan isi formulir pendaftaran.
                            </p>
                        </div>
                        <a href="/spmb/daftar"
                            class="block w-full text-center px-6 py-4 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition-all shadow-md shadow-primary-500/30">
                            Isi Formulir Pendaftaran
                        </a>
                    <?php else: ?>
                        <div class="bg-red-50 text-red-800 p-4 rounded-xl border border-red-200 text-center">
                            <p class="font-medium">Mohon maaf, pendaftaran saat ini sedang ditutup.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Cek Status -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden ring-1 ring-slate-200">
            <div class="p-8 sm:p-10">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800">Cek Status Pendaftaran</h3>
                </div>

                <p class="text-slate-600 mb-8">
                    Sudah mendaftar? Silakan cek status pendaftaran Anda menggunakan nomor registrasi yang telah
                    diberikan saat pendaftaran.
                </p>

                <form action="/spmb/cek-status" method="GET" class="space-y-4">
                    <div>
                        <label for="nomor" class="block text-sm font-medium text-slate-700 mb-2">Nomor Registrasi
                            SPMB</label>
                        <input type="text" name="nomor" id="nomor" required placeholder="Contoh: SPMB2026030001"
                            class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-lg uppercase shadow-sm">
                    </div>
                    <button type="submit"
                        class="w-full px-6 py-4 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl transition-all shadow-md">
                        Cek Status
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>