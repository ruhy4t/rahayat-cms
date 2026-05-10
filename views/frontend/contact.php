<section class="py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Contact Info -->
        <div class="mb-12">
            <h1 class="text-3xl lg:text-4xl font-bold text-slate-800 mb-6">Hubungi Kami</h1>
            <p class="text-slate-600 mb-8">Silakan hubungi kami untuk informasi lebih lanjut mengenai pendaftaran,
                kegiatan sekolah, atau pertanyaan lainnya.</p>

            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <?php if (!empty($profile['address'])): ?>
                    <div class="flex items-start gap-4 bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                        <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-800">Alamat</h3>
                            <p class="text-slate-600">
                                <?= e($profile['address']) ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($profile['phone'])): ?>
                    <div class="flex items-start gap-4 bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-800">Telepon</h3>
                            <p class="text-slate-600">
                                <?= e($profile['phone']) ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($profile['email'])): ?>
                    <div class="flex items-start gap-4 bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-800">Email</h3>
                            <p class="text-slate-600">
                                <?= e($profile['email']) ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php
            $mapEmbedSrc = '';
            $embedCode = $profile['google_maps_embed'] ?? '';
            if (!empty($embedCode)) {
                if (preg_match('/src=["\']([^"\']+)["\']/i', $embedCode, $mMatches)) {
                    $mapEmbedSrc = $mMatches[1];
                } elseif (filter_var($embedCode, FILTER_VALIDATE_URL)) {
                    $mapEmbedSrc = $embedCode;
                }
            }

            $days = [
                'monday' => 'Senin',
                'tuesday' => 'Selasa',
                'wednesday' => 'Rabu',
                'thursday' => 'Kamis',
                'friday' => 'Jumat',
                'saturday' => 'Sabtu',
                'sunday' => 'Minggu',
            ];
            $defaultClosed = [
                'monday' => 0,
                'tuesday' => 0,
                'wednesday' => 0,
                'thursday' => 0,
                'friday' => 0,
                'saturday' => 0,
                'sunday' => 1,
            ];
            ?>

            <div class="<?= !empty($mapEmbedSrc) ? 'grid lg:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)] gap-6 items-stretch' : '' ?>">
                <div class="p-6 bg-white rounded-xl shadow-sm border border-slate-200 h-full">
                    <h3 class="font-semibold text-slate-800 mb-4">Jam Operasional</h3>
                    <div class="space-y-3 text-slate-600">
                        <?php foreach ($days as $key => $label):
                            $isClosed = (int) ($profile["is_closed_{$key}"] ?? $defaultClosed[$key]);
                            ?>
                            <div class="flex items-center justify-between gap-4 p-3 rounded-lg <?= $isClosed ? 'bg-red-50' : 'bg-green-50' ?>">
                                <span class="font-medium"><?= $label ?></span>
                                <?php if ($isClosed): ?>
                                    <span class="font-medium text-red-500">Tutup</span>
                                <?php else: ?>
                                    <span class="font-medium text-green-700 text-right">
                                        <?= e($profile["{$key}_open"] ?? '07:00') ?> -
                                        <?= e($profile["{$key}_close"] ?? '15:00') ?> WIB
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if (!empty($mapEmbedSrc)): ?>
                    <div class="p-6 bg-white rounded-xl shadow-sm border border-slate-200 h-full flex flex-col">
                        <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-3">
                            <span class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </span>
                            Peta Lokasi
                        </h3>
                        <div class="rounded-xl overflow-hidden border border-slate-200 flex-1 min-h-[360px]">
                            <iframe src="<?= e($mapEmbedSrc) ?>" width="100%" height="100%" style="border:0;" allowfullscreen=""
                                loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="w-full h-full"></iframe>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
