<?php
// hero-emerald.php
$schoolType = $profile['school_type'] ?? 'negeri';
$spmbLink = $schoolType === 'swasta' ? '/spmb' : ($profile['spmb_link'] ?? '#');
$showSpmbButton = $schoolType === 'swasta' || !empty($profile['spmb_link']);
$spmbButtonText = $schoolType === 'swasta' ? 'Daftar SPMB' : 'SPMB Wilayah';
$spmbButtonTarget = $schoolType === 'swasta' ? '_self' : '_blank';
?>
<section
    class="relative overflow-hidden min-h-[550px] lg:min-h-[650px] flex items-center rounded-b-[2rem] lg:rounded-b-[4rem] shadow-xl xl:mx-8 xl:mt-4 xl:rounded-[3rem] border-b-4 xl:border-4 border-primary-500/20 mb-12">
    <?php if (!empty($slides)): ?>
        <div id="heroSlider" class="absolute inset-0 z-0">
            <?php foreach ($slides as $index => $slide): ?>
                <div class="hero-slide absolute inset-0 transition-opacity duration-1000 <?= $index === 0 ? 'opacity-100' : 'opacity-0' ?>"
                    data-slide="<?= $index ?>">
                    <img src="/storage/<?= e($slide['image']) ?>" alt="<?= e($slide['title'] ?? '') ?>"
                        class="w-full h-full object-cover" loading="<?= $index === 0 ? 'eager' : 'lazy' ?>">
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="absolute inset-0 z-[5] bg-slate-900/60 mix-blend-multiply"></div>
    <div class="absolute inset-0 z-[6] bg-gradient-to-t from-primary-900/90 via-primary-900/20 to-transparent"></div>

    <div class="relative z-10 w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white pt-20 pb-24">
        <?php if (!empty($profile['school_type']) && $profile['school_type'] === 'swasta'): ?>
            <div
                class="inline-flex items-center px-5 py-2 bg-green-500/20 text-green-300 rounded-full text-xs font-bold uppercase tracking-wider backdrop-blur-md border border-green-400/30 mb-8">
                <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                Pendaftaran Telah Dibuka
            </div>
        <?php endif; ?>

        <h1 class="text-4xl md:text-5xl lg:text-7xl font-bold leading-tight mb-6 drop-shadow-xl">
            <?= e($profile['name'] ?? SCHOOL_NAME) ?>
        </h1>

        <?php if (!empty($profile['tagline'])): ?>
            <p
                class="text-xl md:text-2xl text-primary-50 font-medium mb-10 max-w-3xl mx-auto drop-shadow-lg leading-relaxed">
                <?= e($profile['tagline']) ?>
            </p>
        <?php endif; ?>

        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mt-8">
            <a href="/profil"
                class="px-8 py-4 bg-white text-primary-800 font-bold rounded-full hover:bg-slate-50 shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 min-w-56 text-center">
                Tentang Kami
            </a>
            <?php if ($showSpmbButton): ?>
                <a href="<?= e($spmbLink) ?>" target="<?= $spmbButtonTarget ?>"
                    class="px-8 py-4 bg-primary-600 border border-primary-500 hover:bg-primary-500 text-white font-bold rounded-full shadow-xl hover:shadow-primary-600/40 hover:-translate-y-1 transition-all duration-300 min-w-56 text-center">
                    <?= e($spmbButtonText) ?>
                </a>
            <?php else: ?>
                <a href="/kontak"
                    class="px-8 py-4 bg-transparent border border-white text-white font-bold rounded-full hover:bg-white hover:text-slate-900 shadow-xl backdrop-blur-sm transition-all duration-300 min-w-56 text-center">
                    Hubungi Kami
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($slides) && count($slides) > 1): ?>
        <div
            class="absolute bottom-6 left-1/2 transform -translate-x-1/2 z-20 flex gap-2 bg-black/30 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/10">
            <?php foreach ($slides as $index => $slide): ?>
                <button
                    class="slider-dot h-1.5 rounded-full transition-all duration-300 <?= $index === 0 ? 'bg-white w-6 block' : 'bg-white/40 w-1.5 block' ?>"
                    data-slide="<?= $index ?>"></button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>