<?php
$schoolName = $profile['name'] ?? SCHOOL_NAME;
$heroImages = array_values(array_filter(
    array_slice($slides, 0, 3),
    static fn(array $slide): bool => !empty($slide['image'])
));
$spmbTarget = ($spmbPublic['target'] ?? '_self') === '_blank' ? '_blank' : '_self';
?>
<section class="cendekia-hero relative overflow-hidden bg-[#f4f8fb]">
    <div class="absolute inset-y-0 right-0 w-[38%] bg-slate-950 hidden lg:block"></div>
    <div class="cendekia-grid absolute inset-0 opacity-50 pointer-events-none"></div>

    <div class="relative max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-24 sm:pt-16 lg:pt-20 lg:pb-28">
        <div class="grid lg:grid-cols-12 gap-10 lg:gap-12 items-center">
            <div class="lg:col-span-6 xl:col-span-7 relative z-10">
                <div class="inline-flex items-center gap-3 text-xs font-extrabold uppercase tracking-[0.22em] text-primary-700">
                    <span class="w-10 h-1 rounded-full bg-cyan-400"></span>
                    Selamat datang di sekolah kami
                </div>
                <h1 class="mt-6 text-[2.65rem] sm:text-6xl xl:text-[5.2rem] font-extrabold tracking-[-0.055em] leading-[0.98] text-slate-950">
                    Belajar dengan gembira.
                    <span class="block mt-2 text-primary-700">Tumbuh menjadi diri sendiri.</span>
                </h1>
                <p class="mt-7 max-w-xl text-base sm:text-lg leading-8 text-slate-600">
                    Di <strong class="text-slate-900"><?= e($schoolName) ?></strong>, setiap murid didampingi untuk
                    belajar, berteman, dan mengembangkan bakatnya dalam lingkungan yang aman dan menyenangkan.
                </p>

                <div class="mt-9 flex flex-col sm:flex-row gap-3">
                    <?php if (!empty($spmbPublic['active'])): ?>
                        <a href="<?= e($spmbPublic['url'] ?? '/spmb') ?>" target="<?= $spmbTarget ?>"
                            <?= $spmbTarget === '_blank' ? 'rel="noopener noreferrer"' : '' ?>
                            class="cendekia-button-primary inline-flex items-center justify-center gap-3 px-6 py-4 rounded-2xl bg-slate-950 text-white font-extrabold">
                            <?= e($spmbPublic['label'] ?? 'Informasi SPMB') ?>
                            <span aria-hidden="true">&#8599;</span>
                        </a>
                    <?php endif; ?>
                    <a href="/profil"
                        class="inline-flex items-center justify-center gap-3 px-6 py-4 rounded-2xl bg-white border border-slate-300 text-slate-800 font-extrabold hover:border-primary-500 hover:text-primary-700 transition-colors">
                        Lihat profil sekolah
                        <span aria-hidden="true">&#8594;</span>
                    </a>
                </div>

                <div class="mt-12 grid grid-cols-3 max-w-xl border-y border-slate-300/80 py-5">
                    <?php
                    $heroStats = [
                        ['value' => $profile['total_students'] ?? '-', 'suffix' => !empty($profile['total_students']) ? '+' : '', 'label' => 'Murid aktif'],
                        ['value' => $profile['total_teachers'] ?? '-', 'suffix' => !empty($profile['total_teachers']) ? '+' : '', 'label' => 'Guru & tenaga'],
                        ['value' => $profile['accreditation'] ?? '-', 'suffix' => '', 'label' => 'Akreditasi'],
                    ];
                    ?>
                    <?php foreach ($heroStats as $index => $stat): ?>
                        <div class="<?= $index > 0 ? 'border-l border-slate-300 pl-5 sm:pl-7' : '' ?>">
                            <strong class="block text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-950">
                                <?= e($stat['value']) ?><?= $stat['suffix'] ?>
                            </strong>
                            <span class="block mt-1 text-xs font-bold uppercase tracking-wider text-slate-500"><?= $stat['label'] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="lg:col-span-6 xl:col-span-5 relative min-h-[420px] sm:min-h-[540px]">
                <div class="cendekia-photo-main absolute left-0 sm:left-8 lg:left-0 top-0 w-[84%] h-[82%] overflow-hidden bg-slate-200">
                    <?php if (!empty($heroImages[0])): ?>
                        <img src="/storage/<?= e($heroImages[0]['image']) ?>"
                            alt="<?= e($heroImages[0]['title'] ?? 'Kegiatan sekolah') ?>"
                            class="w-full h-full object-cover" loading="eager" fetchpriority="high">
                    <?php else: ?>
                        <div class="w-full h-full cendekia-photo-fallback flex items-center justify-center">
                            <span class="text-8xl font-black text-primary-200"><?= e(strtoupper(substr($schoolName, 0, 1))) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="absolute top-7 right-0 w-16 sm:w-20 py-5 bg-cyan-400 text-slate-950 text-center z-10">
                    <span class="block text-[10px] font-extrabold tracking-[0.25em] uppercase">Jenjang</span>
                    <strong class="block mt-2 text-xl sm:text-2xl font-black [writing-mode:vertical-rl] mx-auto tracking-[0.18em]">SMP</strong>
                </div>

                <div class="absolute right-0 bottom-0 w-[58%] h-[43%] border-[10px] border-[#f4f8fb] bg-slate-800 overflow-hidden shadow-2xl">
                    <?php if (!empty($heroImages[1])): ?>
                        <img src="/storage/<?= e($heroImages[1]['image']) ?>"
                            alt="<?= e($heroImages[1]['title'] ?? 'Aktivitas murid') ?>"
                            class="w-full h-full object-cover" loading="lazy">
                    <?php else: ?>
                        <div class="w-full h-full bg-primary-700 p-6 flex items-end">
                            <p class="text-white text-lg sm:text-2xl font-extrabold leading-tight"><?= e($profile['tagline'] ?? 'Belajar, berteman, dan berprestasi.') ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="absolute left-0 sm:left-8 lg:left-0 bottom-4 w-[40%] bg-white p-4 sm:p-5 shadow-xl border-l-4 border-cyan-400">
                    <span class="block text-[10px] font-extrabold uppercase tracking-[0.2em] text-primary-700">Yang kami jaga</span>
                    <p class="mt-2 text-xs sm:text-sm font-bold leading-snug text-slate-800">Belajar nyaman, tumbuh percaya diri.</p>
                </div>
            </div>
        </div>
    </div>
</section>
