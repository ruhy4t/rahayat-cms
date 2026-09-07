<?php
/**
 * SPMB Status Check View
 */
?>

<div class="bg-slate-900 pb-24 pt-12 sm:pb-32 sm:pt-16 lg:pb-32 lg:pt-20">
    <div class="mx-auto max-w-[1440px] px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl">
                Cek Status Pendaftaran
            </h1>
            <p class="mt-6 text-lg leading-8 text-slate-300">
                Masukkan nomor registrasi, NISN, dan tanggal lahir calon murid untuk melihat status pendaftaran.
            </p>
        </div>
    </div>
</div>

<div class="relative z-10 -mt-16 sm:-mt-24 mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 pb-20">
    <div class="bg-white rounded-2xl shadow-xl ring-1 ring-slate-200 overflow-hidden">

        <!-- Search Form -->
        <div class="p-8 sm:p-10 border-b border-slate-100">
            <?php if (!empty($statusError)): ?>
                <p role="alert" class="mb-4 text-red-600"><?= e($statusError) ?></p>
            <?php endif; ?>
            <form action="/spmb/cek-status" method="POST" class="space-y-4">
                <?= Security::csrfInput() ?>
                <div>
                    <label for="registration_number" class="block text-sm font-medium mb-2">Nomor registrasi</label>
                    <input id="registration_number" name="registration_number" required maxlength="50" autocomplete="off"
                        value="<?= e($registrationNumber ?? '') ?>" class="block w-full rounded-xl border-slate-300 p-3">
                </div>
                <div>
                    <label for="nisn" class="block text-sm font-medium mb-2">NISN calon murid</label>
                    <input id="nisn" name="nisn" required pattern="[0-9]{10}" maxlength="10" inputmode="numeric" autocomplete="off"
                        class="block w-full rounded-xl border-slate-300 p-3">
                </div>
                <div>
                    <label for="birth_date" class="block text-sm font-medium mb-2">Tanggal lahir calon murid</label>
                    <input id="birth_date" name="birth_date" type="date" required autocomplete="off"
                        class="block w-full rounded-xl border-slate-300 p-3">
                </div>
                <button type="submit" class="px-8 py-3 bg-primary-600 text-white font-bold rounded-xl">Cek Status</button>
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
                                    class="mt-1 text-sm font-bold text-slate-900 sm:col-span-2 sm:mt-0 font-mono break-all">
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
                        </dl>
                    </div>



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
                                <?= e($registrationNumber ?? '') ?>"
                            </span>.<br>
                            Pastikan nomor registrasi, NISN, dan tanggal lahir yang Anda masukkan sudah benar.
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
                <p class="text-slate-500">Silakan masukkan nomor registrasi beserta data verifikasi pada formulir di atas.</p>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
    // Remove the credential left by older releases on shared devices.
    try { localStorage.removeItem('last_spmb_reg_number'); } catch (error) {}
</script>
