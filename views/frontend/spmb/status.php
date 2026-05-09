<?php
/**
 * SPMB Status Check View
 */
?>

<div class="bg-slate-900 pb-24 pt-12 sm:pb-32 sm:pt-16 lg:pb-32 lg:pt-20">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl">
                Cek Status Pendaftaran
            </h1>
            <p class="mt-6 text-lg leading-8 text-slate-300">
                Masukkan Nomor Registrasi SPMB Anda untuk mengetahui status proses pendaftaran calon murid.
            </p>
        </div>
    </div>
</div>

<div class="-mt-16 sm:-mt-24 mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 pb-20">
    <div class="bg-white rounded-2xl shadow-xl ring-1 ring-slate-200 overflow-hidden">

        <!-- Search Form -->
        <div class="p-8 sm:p-10 border-b border-slate-100">
            <form action="/spmb/cek-status" method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="nomor" id="nomor" required value="<?= e($_GET['nomor'] ?? '') ?>"
                        placeholder="Masukkan Nomor Registrasi (Contoh: SPMB2026030001)"
                        class="block w-full pl-11 rounded-xl border-slate-300 py-3.5 px-4 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200 text-lg uppercase">
                </div>
                <button type="submit"
                    class="px-8 py-3.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition-all shadow-md shrink-0">
                    Cek Status
                </button>
            </form>
        </div>

        <!-- Result -->
        <?php if ($searched): ?>
            <div class="p-8 sm:p-10 bg-slate-50/50">
                <?php if ($registration): ?>

                    <?php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                        'review' => 'bg-blue-100 text-blue-800 border-blue-200',
                        'accepted' => 'bg-green-100 text-green-800 border-green-200',
                        'rejected' => 'bg-red-100 text-red-800 border-red-200'
                    ];
                    $statusLabels = [
                        'pending' => 'Menunggu Verifikasi',
                        'review' => 'Sedang Direview',
                        'accepted' => 'Diterima',
                        'rejected' => 'Ditolak'
                    ];

                    $statusColor = $statusColors[$registration['status']] ?? 'bg-slate-100 text-slate-800';
                    $statusLabel = $statusLabels[$registration['status']] ?? 'Unknown';
                    ?>

                    <div class="text-center mb-8">
                        <h2 class="text-xl font-bold text-slate-800 mb-2">Hasil Pencarian</h2>
                        <span
                            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold border <?= $statusColor ?>">
                            Status:
                            <?= $statusLabel ?>
                        </span>
                    </div>

                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <dl class="divide-y divide-slate-100">
                            <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-slate-500">Nomor Registrasi</dt>
                                <dd
                                    class="mt-1 text-sm font-bold text-slate-900 sm:col-span-2 sm:mt-0 font-mono tracking-wider">
                                    <?= e($registration['registration_number']) ?>
                                </dd>
                            </div>
                            <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-slate-500">Nama Calon Murid</dt>
                                <dd class="mt-1 text-sm text-slate-900 sm:col-span-2 sm:mt-0 font-semibold">
                                    <?= e($registration['student_name']) ?>
                                </dd>
                            </div>
                            <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-slate-500">Tanggal Mendaftar</dt>
                                <dd class="mt-1 text-sm text-slate-900 sm:col-span-2 sm:mt-0">
                                    <?= date('d F Y', strtotime($registration['created_at'])) ?>
                                </dd>
                            </div>
                            <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-slate-500">Asal Sekolah</dt>
                                <dd class="mt-1 text-sm text-slate-900 sm:col-span-2 sm:mt-0">
                                    <?= e($registration['previous_school']) ?>
                                </dd>
                            </div>

                            <?php if (!empty($registration['notes'])): ?>
                                <div class="px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-slate-50">
                                    <dt class="text-sm font-medium text-slate-700">Catatan dari Panitia</dt>
                                    <dd class="mt-1 text-sm text-slate-900 sm:col-span-2 sm:mt-0">
                                        <div class="prose prose-sm text-slate-700">
                                            <?= nl2br(e($registration['notes'])) ?>
                                        </div>
                                    </dd>
                                </div>
                            <?php endif; ?>
                        </dl>
                    </div>

                    <?php if ($registration['status'] === 'accepted'): ?>
                        <div class="mt-6 flex flex-col sm:flex-row gap-4 justify-center">
                            <button onclick="window.print()"
                                class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Cetak Bukti Pendaftaran
                            </button>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="text-center py-12">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 text-red-600 mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 mb-2">Data Tidak Ditemukan</h3>
                        <p class="text-slate-600">
                            Kami tidak dapat menemukan data pendaftaran dengan nomor registrasi <span class="font-bold">"
                                <?= e($_GET['nomor']) ?>"
                            </span>.<br>
                            Pastikan nomor yang Anda masukkan sudah benar.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="p-8 sm:p-12 text-center bg-slate-50/50">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-100 text-slate-400 mb-4">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-slate-800 mb-1">Cek Status Pendaftaran</h3>
                <p class="text-slate-500">Silakan masukkan nomor registrasi Anda pada kolom pencarian di atas.</p>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
    // Automatically fill from localstorage if available and not searched yet
    const input = document.getElementById('nomor');
    if (!input.value && localStorage.getItem('last_spmb_reg_number')) {
        input.value = localStorage.getItem('last_spmb_reg_number');
    }
</script>