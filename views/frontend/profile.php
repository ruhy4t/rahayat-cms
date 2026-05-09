<section class="py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-12 text-center">
            <h1 class="text-3xl lg:text-4xl font-bold text-slate-800">Profil Sekolah</h1>
            <p class="text-slate-600 mt-2">Mengenal lebih dekat
                <?= e($profile['name'] ?? SCHOOL_NAME) ?>
            </p>
            <?php if (!empty($profile['motto'])): ?>
                <div class="mt-6 flex justify-center">
                    <span
                        class="inline-block px-6 py-2 rounded-full bg-primary-50 text-primary-700 text-sm font-medium italic border border-primary-100">
                        <?= $profile['motto'] ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Visi & Misi -->
        <?php if (!empty($profile['vision']) || !empty($profile['mission'])): ?>
            <div class="grid lg:grid-cols-2 gap-8 mb-16">
                <?php if (!empty($profile['vision'])): ?>
                    <div class="bg-gradient-to-br from-primary-600 to-primary-700 text-white p-8 rounded-2xl">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Visi
                        </h2>
                        <div class="text-primary-100 leading-relaxed prose prose-invert max-w-none">
                            <?= $profile['vision'] ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($profile['mission'])): ?>
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
                        <h2 class="text-2xl font-bold text-slate-800 mb-4 flex items-center">
                            <svg class="w-8 h-8 mr-3 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Misi
                        </h2>
                        <div class="text-slate-600 leading-relaxed prose max-w-none">
                            <?= $profile['mission'] ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Sejarah Singkat -->
        <?php if (!empty($profile['history'])): ?>
            <div class="mb-16">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-800">Sejarah Singkat</h2>
                    </div>
                    <div class="prose prose-slate max-w-none text-slate-600">
                        <?= $profile['history'] ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Info Cards -->
        <div class="grid md:grid-cols-3 gap-6 mb-16">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 text-center">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-800 mb-1">Didirikan</h3>
                <p class="text-2xl font-bold text-primary-600">
                    <?= e($profile['established_year'] ?? '2010') ?>
                </p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-800 mb-1">Akreditasi</h3>
                <p class="text-2xl font-bold text-green-600">
                    <?= e($profile['accreditation'] ?? 'A') ?>
                </p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 text-center">
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-800 mb-1">NPSN</h3>
                <p class="text-2xl font-bold text-amber-600">
                    <?= e($profile['npsn'] ?? '12345678') ?>
                </p>
            </div>
        </div>

        <!-- Kepala Sekolah -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 mb-16">
            <div class="flex flex-col md:flex-row items-start gap-8">
                <div class="flex-shrink-0 text-center md:text-left">
                    <?php if (!empty($profile['principal_photo'])): ?>
                        <img src="/storage/<?= e($profile['principal_photo']) ?>"
                            alt="<?= e($profile['principal_name'] ?? 'Kepala Sekolah') ?>"
                            class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg mx-auto md:mx-0">
                    <?php else: ?>
                        <div
                            class="w-32 h-32 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-white text-4xl font-bold flex-shrink-0 border-4 border-white shadow-lg mx-auto md:mx-0">
                            <?= strtoupper(substr($profile['principal_name'] ?? 'K', 0, 1)) ?>
                        </div>
                    <?php endif; ?>

                    <h3 class="text-xl font-bold text-slate-800 mt-4">
                        <?= e($profile['principal_name'] ?? 'Kepala Sekolah') ?>
                    </h3>
                    <p class="text-slate-500">Kepala Sekolah</p>
                </div>

                <div class="flex-1">
                    <?php if (!empty($profile['welcome_message'])): ?>
                        <div class="prose prose-slate max-w-none text-slate-600 mb-6">
                            <?= $profile['welcome_message'] ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($profile['principal_quote'])): ?>
                        <blockquote class="border-l-4 border-primary-500 pl-4 italic text-slate-600">
                            "<?= $profile['principal_quote'] ?>"
                        </blockquote>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Fasilitas -->
        <?php if (!empty($facilities)): ?>
            <div class="mb-16">
                <h2 class="text-2xl lg:text-3xl font-bold text-slate-800 mb-8">Fasilitas Sekolah</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($facilities as $facility): ?>
                        <div
                            class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
                            <div
                                class="h-48 bg-gradient-to-br from-primary-100 to-primary-200 flex items-center justify-center">
                                <?php if (!empty($facility['image'])): ?>
                                    <img src="/storage/<?= e($facility['image']) ?>" alt="<?= e($facility['name']) ?>"
                                        class="w-full h-full object-cover">
                                <?php else: ?>
                                    <svg class="w-16 h-16 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-slate-800 text-lg">
                                    <?= e($facility['name']) ?>
                                </h3>
                                <?php if (!empty($facility['description'])): ?>
                                    <p class="text-slate-600 text-sm mt-2">
                                        <?= e($facility['description']) ?>
                                    </p>
                                <?php endif; ?>
                                <?php if (!empty($facility['capacity'])): ?>
                                    <p class="text-slate-500 text-xs mt-2">
                                        <span class="font-medium">Kapasitas:</span> <?= e($facility['capacity']) ?> orang
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Kontak -->
        <div class="bg-gradient-to-r from-slate-800 to-slate-900 text-white rounded-2xl p-8">
            <h2 class="text-2xl font-bold mb-6 text-center">Informasi Kontak</h2>
            <div class="grid md:grid-cols-3 gap-6 text-center">
                <div>
                    <svg class="w-8 h-8 text-primary-400 mx-auto mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <p class="text-slate-300">
                        <?= e($profile['address'] ?? SCHOOL_ADDRESS) ?>
                    </p>
                </div>
                <div>
                    <svg class="w-8 h-8 text-primary-400 mx-auto mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <p class="text-slate-300">
                        <?= e($profile['phone'] ?? SCHOOL_PHONE) ?>
                    </p>
                </div>
                <div>
                    <svg class="w-8 h-8 text-primary-400 mx-auto mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <p class="text-slate-300">
                        <?= e($profile['email'] ?? SCHOOL_EMAIL) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>