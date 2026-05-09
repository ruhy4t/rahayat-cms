<?php
// hero-crimson.php
$schoolType = $profile['school_type'] ?? 'negeri';
$spmbLink = $schoolType === 'swasta' ? '/spmb' : ($profile['spmb_link'] ?? '#');
$showSpmbButton = $schoolType === 'swasta' || !empty($profile['spmb_link']);
$spmbButtonText = $schoolType === 'swasta' ? 'Daftar SPMB' : 'SPMB Wilayah';
$spmbButtonTarget = $schoolType === 'swasta' ? '_self' : '_blank';
?>
<!-- Main Hero -->
<section class="relative bg-slate-900 overflow-hidden min-h-[600px] lg:min-h-[700px] flex items-center">
    <!-- Diagonal background element -->
    <div class="absolute right-0 top-0 bottom-0 w-full md:w-2/3 lg:w-[65%] z-0"
        style="clip-path: polygon(15% 0, 100% 0, 100% 100%, 0% 100%);">
        <?php if (!empty($slides)): ?>
            <div id="heroSlider" class="absolute inset-0 z-0 bg-slate-800">
                <?php foreach ($slides as $index => $slide): ?>
                    <div class="hero-slide absolute inset-0 transition-opacity duration-1000 <?= $index === 0 ? 'opacity-100' : 'opacity-0' ?>"
                        data-slide="<?= $index ?>">
                        <img src="/storage/<?= e($slide['image']) ?>" alt="<?= e($slide['title'] ?? '') ?>"
                            class="w-full h-full object-cover" loading="<?= $index === 0 ? 'eager' : 'lazy' ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="absolute inset-0 bg-slate-800 border-l border-primary-500/30 flex items-center justify-center">
                <!-- Fallback pattern if no slides -->
                <div class="absolute inset-0 opacity-20"
                    style="background-image: radial-gradient(#F43F5E 2px, transparent 2px); background-size: 30px 30px;">
                </div>
            </div>
        <?php endif; ?>
        <div class="absolute inset-0 bg-primary-900/60 mix-blend-multiply"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-slate-900 via-transparent to-transparent"></div>
    </div>

    <!-- Accent line -->
    <div
        class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-primary-400 via-primary-600 to-primary-800 z-10">
    </div>

    <div
        class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 flex flex-col md:flex-row items-center">
        <!-- Content Left -->
        <div class="w-full md:w-[55%] lg:w-[45%] text-white pr-4 md:pr-12">
            <?php if (!empty($profile['school_type']) && $profile['school_type'] === 'swasta'): ?>
                <div
                    class="inline-flex items-center px-4 py-1.5 bg-primary-900/50 text-red-200 border border-primary-500/30 font-bold uppercase tracking-widest text-[10px] mb-8 pb-1 border-b-2 border-b-primary-500">
                    <span class="w-2 h-2 bg-red-400 mr-2 rounded-sm animate-pulse"></span> Pendaftaran Dibuka
                </div>
            <?php endif; ?>

            <h1
                class="text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-black leading-[1.1] mb-6 uppercase tracking-tight break-words">
                <?= e($profile['name'] ?? SCHOOL_NAME) ?>
            </h1>

            <div class="h-1.5 w-24 bg-primary-600 mb-8 border-b-2 border-primary-800"></div>

            <?php if (!empty($profile['tagline'])): ?>
                <p
                    class="text-lg text-slate-300 font-medium mb-10 leading-relaxed border-l-4 border-primary-800 pl-4 py-1 bg-slate-800/30 pr-4">
                    <?= e($profile['tagline']) ?>
                </p>
            <?php endif; ?>

            <div class="flex flex-col sm:flex-row gap-5">
                <a href="/profil"
                    class="px-8 py-4 bg-white hover:bg-slate-200 text-slate-900 font-bold uppercase tracking-wider text-sm text-center transition-all border border-transparent hover:border-white shadow-[4px_4px_0_theme(colors.primary.600)] hover:shadow-[2px_2px_0_theme(colors.primary.600)] hover:translate-x-[2px] hover:translate-y-[2px]">
                    Profil Sekolah
                </a>
                <?php if ($showSpmbButton): ?>
                    <a href="<?= e($spmbLink) ?>" target="<?= $spmbButtonTarget ?>"
                        class="px-8 py-4 bg-primary-600 hover:bg-primary-500 text-white font-bold uppercase tracking-wider text-sm text-center transition-all shadow-[4px_4px_0_theme(colors.slate.800)] border border-primary-400 hover:shadow-[2px_2px_0_theme(colors.slate.800)] hover:translate-x-[2px] hover:translate-y-[2px]">
                        <?= e($spmbButtonText) ?>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Slider Controls Left -->
            <?php if (!empty($slides) && count($slides) > 1): ?>
                <div class="mt-16 flex items-center space-x-3">
                    <span class="text-slate-500 text-xs font-bold tracking-widest uppercase mr-3">Galeri Foto</span>
                    <?php foreach ($slides as $index => $slide): ?>
                        <button
                            class="slider-dot h-1.5 transition-all duration-300 <?= $index === 0 ? 'bg-primary-500 w-10' : 'bg-slate-700 w-3 hover:bg-slate-500' ?>"
                            data-slide="<?= $index ?>"></button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Stats Strip -->
<div
    class="bg-primary-900 border-t-2 border-primary-800 border-b-4 border-b-primary-600 py-10 relative z-20 shadow-2xl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center divide-x-0 lg:divide-x divide-primary-800/50">
            <?php if (!empty($profile['total_students'])): ?>
                <div class="px-4 group">
                    <div
                        class="text-4xl md:text-5xl font-black text-white tracking-tighter group-hover:text-primary-300 transition-colors drop-shadow-sm mb-2">
                        <?= e($profile['total_students']) ?>+
                    </div>
                    <div class="text-primary-300 text-xs font-bold uppercase tracking-widest">Siswa Aktif</div>
                </div>
            <?php endif; ?>
            <?php if (!empty($profile['total_teachers'])): ?>
                <div class="px-4 group">
                    <div
                        class="text-4xl md:text-5xl font-black text-white tracking-tighter group-hover:text-primary-300 transition-colors drop-shadow-sm mb-2">
                        <?= e($profile['total_teachers']) ?>+
                    </div>
                    <div class="text-primary-300 text-xs font-bold uppercase tracking-widest">Tenaga Pendidik</div>
                </div>
            <?php endif; ?>
            <?php if (!empty($profile['accreditation'])): ?>
                <div class="px-4 group">
                    <div
                        class="text-4xl md:text-5xl font-black text-white tracking-tighter group-hover:text-primary-300 transition-colors drop-shadow-sm mb-2">
                        <?= e($profile['accreditation']) ?>
                    </div>
                    <div class="text-primary-300 text-xs font-bold uppercase tracking-widest">Akreditasi</div>
                </div>
            <?php endif; ?>
            <?php if (!empty($profile['graduation_rate'])): ?>
                <div class="px-4 group">
                    <div
                        class="text-4xl md:text-5xl font-black text-white tracking-tighter group-hover:text-primary-300 transition-colors drop-shadow-sm mb-2">
                        <?= e($profile['graduation_rate']) ?>%
                    </div>
                    <div class="text-primary-300 text-xs font-bold uppercase tracking-widest">Kelulusan</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>