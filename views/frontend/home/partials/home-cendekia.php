<?php
include __DIR__ . '/hero-cendekia.php';

$cendekiaFacilities = array_values($facilities ?? []);
$cendekiaEkskul = array_values($ekskul ?? []);
$cendekiaTestimonials = array_values($testimonials ?? []);
$quickLinks = [
    ['number' => '01', 'label' => 'Profil Sekolah', 'caption' => 'Kenali sekolah kami', 'url' => '/profil'],
    ['number' => '02', 'label' => 'Guru dan Tenaga Kependidikan', 'caption' => 'Kenali para pendamping murid', 'url' => '/profil/gtk'],
    ['number' => '03', 'label' => 'Prestasi', 'caption' => 'Lihat pencapaian sekolah', 'url' => '/prestasi'],
    ['number' => '04', 'label' => 'Galeri', 'caption' => 'Lihat kegiatan sekolah', 'url' => '/galeri'],
];
?>

<section class="relative z-20 -mt-10 pb-16 lg:pb-24" aria-label="Akses cepat">
    <div class="max-w-[1320px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 bg-white shadow-2xl shadow-slate-950/10 border border-slate-200">
            <?php foreach ($quickLinks as $index => $link): ?>
                <a href="<?= $link['url'] ?>"
                    class="cendekia-quick-link group flex items-start gap-4 p-5 sm:p-6 <?= $index < 3 ? 'lg:border-r border-slate-200' : '' ?> <?= $index < 2 ? 'sm:border-b lg:border-b-0 border-slate-200' : '' ?>">
                    <span class="text-xs font-black text-cyan-600 mt-1"><?= $link['number'] ?></span>
                    <span>
                        <strong class="block text-base font-extrabold text-slate-900 group-hover:text-primary-700 transition-colors"><?= $link['label'] ?></strong>
                        <span class="block mt-1 text-xs text-slate-500"><?= $link['caption'] ?></span>
                    </span>
                    <span class="ml-auto text-slate-300 group-hover:text-cyan-500 group-hover:translate-x-1 transition-all" aria-hidden="true">&#8594;</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if (!empty($cendekiaFacilities)): ?>
    <section class="py-16 lg:py-24 bg-white overflow-hidden">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-12 gap-8 lg:gap-12 items-end mb-10">
                <div class="lg:col-span-7">
                    <span class="cendekia-kicker">Fasilitas sekolah</span>
                    <h2 class="mt-4 text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-[-0.045em] leading-[1.03] text-slate-950">
                        Ruang yang nyaman untuk belajar.
                    </h2>
                </div>
                <div class="lg:col-span-5 lg:pb-2">
                    <p class="text-slate-600 leading-7">Setiap fasilitas kami siapkan agar murid dapat belajar,
                        berkegiatan, dan bekerja sama dengan nyaman.</p>
                </div>
            </div>

            <?php
            $featuredFacility = $cendekiaFacilities[0];
            $highlightFacilities = array_slice($cendekiaFacilities, 1, 4);
            $additionalFacilities = array_slice($cendekiaFacilities, 5);
            ?>
            <div class="grid lg:grid-cols-12 gap-4 lg:h-[560px]">
                <article class="group lg:col-span-7 relative min-h-[390px] lg:min-h-0 overflow-hidden bg-slate-900">
                    <?php if (!empty($featuredFacility['image'])): ?>
                        <img src="/storage/<?= e($featuredFacility['image']) ?>" alt="<?= e($featuredFacility['name']) ?>"
                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" loading="lazy">
                    <?php else: ?>
                        <div class="absolute inset-0 cendekia-photo-fallback"></div>
                    <?php endif; ?>
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-0 p-6 sm:p-8 text-white">
                        <span class="text-xs font-extrabold uppercase tracking-[0.2em] text-cyan-300"><?= e($featuredFacility['type'] ?? 'Fasilitas') ?></span>
                        <h3 class="mt-3 text-3xl sm:text-4xl font-extrabold"><?= e($featuredFacility['name']) ?></h3>
                        <p class="mt-3 max-w-xl text-sm sm:text-base text-slate-300 line-clamp-2"><?= e($featuredFacility['description'] ?? 'Fasilitas yang mendukung kegiatan belajar murid.') ?></p>
                    </div>
                </article>

                <div class="lg:col-span-5 grid sm:grid-cols-2 gap-4">
                    <?php foreach ($highlightFacilities as $facilityIndex => $facility): ?>
                        <article class="group relative min-h-[230px] overflow-hidden bg-slate-100">
                            <?php if (!empty($facility['image'])): ?>
                                <img src="/storage/<?= e($facility['image']) ?>" alt="<?= e($facility['name']) ?>"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" loading="lazy">
                            <?php else: ?>
                                <div class="absolute inset-0 cendekia-photo-fallback"></div>
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/10 to-transparent"></div>
                            <div class="absolute inset-x-0 bottom-0 p-5 text-white">
                                <span class="text-[10px] font-black text-cyan-300"><?= str_pad((string) ($facilityIndex + 2), 2, '0', STR_PAD_LEFT) ?></span>
                                <h3 class="mt-1 text-lg font-extrabold"><?= e($facility['name']) ?></h3>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if (!empty($additionalFacilities)): ?>
                <div class="mt-4 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <?php foreach ($additionalFacilities as $facilityIndex => $facility): ?>
                        <article class="group relative min-h-[260px] overflow-hidden bg-slate-100">
                            <?php if (!empty($facility['image'])): ?>
                                <img src="/storage/<?= e($facility['image']) ?>" alt="<?= e($facility['name']) ?>"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" loading="lazy" decoding="async">
                            <?php else: ?>
                                <div class="absolute inset-0 cendekia-photo-fallback"></div>
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/10 to-transparent"></div>
                            <div class="absolute inset-x-0 bottom-0 p-5 text-white">
                                <span class="text-[10px] font-black text-cyan-300"><?= str_pad((string) ($facilityIndex + 6), 2, '0', STR_PAD_LEFT) ?></span>
                                <span class="block mt-2 text-[10px] font-extrabold uppercase tracking-[0.16em] text-cyan-200"><?= e($facility['type'] ?? 'Fasilitas') ?></span>
                                <h3 class="mt-1 text-lg font-extrabold"><?= e($facility['name']) ?></h3>
                                <?php if (!empty($facility['description'])): ?>
                                    <p class="mt-2 text-sm text-slate-300 line-clamp-2"><?= e($facility['description']) ?></p>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<?php if (!empty($cendekiaEkskul)): ?>
    <section id="ekstrakurikuler" class="py-16 lg:py-24 bg-[#071a2e] text-white">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-12 gap-10 lg:gap-16">
                <div class="lg:col-span-4">
                    <span class="text-xs font-extrabold uppercase tracking-[0.22em] text-cyan-300">Kegiatan murid</span>
                    <h2 class="mt-5 text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight">Kembangkan minat<br>dan bakat.</h2>
                    <p class="mt-5 text-slate-400 leading-7">Melalui kegiatan ekstrakurikuler, murid dapat mencoba
                        hal baru, bertemu teman, dan melatih rasa percaya diri.</p>
                    <div class="mt-8 inline-flex items-center gap-3 text-cyan-300 font-extrabold">
                        <span class="w-12 h-px bg-cyan-400"></span>
                        <?= count($cendekiaEkskul) ?> pilihan kegiatan
                    </div>
                </div>

                <div class="lg:col-span-8 border-t border-white/15">
                    <?php foreach ($cendekiaEkskul as $index => $item): ?>
                        <?php
                        $firstSchedule = $item['schedules'][0] ?? null;
                        $scheduleLabel = $firstSchedule
                            ? trim(($firstSchedule['day'] ?? '') . ' ' . ($firstSchedule['time'] ?? ''))
                            : (string) ($item['schedule'] ?? '');
                        ?>
                        <a href="/ekstrakurikuler/<?= (int) $item['id'] ?>"
                            class="cendekia-activity group grid grid-cols-[3rem_1fr_auto] sm:grid-cols-[4rem_1fr_12rem_auto] items-center gap-3 sm:gap-6 py-5 border-b border-white/15">
                            <span class="text-xs font-black text-cyan-400"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                            <span class="text-lg sm:text-xl font-extrabold group-hover:text-cyan-300 transition-colors"><?= e($item['name']) ?></span>
                            <span class="hidden sm:block text-sm text-slate-400"><?= e($scheduleLabel !== '' ? $scheduleLabel : 'Jadwal akan diumumkan') ?></span>
                            <span class="w-9 h-9 rounded-full border border-white/20 flex items-center justify-center group-hover:bg-cyan-400 group-hover:text-slate-950 group-hover:border-cyan-400 transition-colors" aria-hidden="true">&#8599;</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php include __DIR__ . '/news-cendekia.php'; ?>

<section class="py-16 lg:py-24 bg-white">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-12 gap-8 lg:gap-14">
            <div class="lg:col-span-4">
                <span class="cendekia-kicker">Cerita dari sekolah</span>
                <h2 class="mt-4 text-4xl lg:text-5xl font-extrabold tracking-tight text-slate-950">Pengalaman orang tua dan alumni.</h2>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="/testimoni#kirim-testimoni" class="px-5 py-3 rounded-xl bg-slate-950 text-white text-sm font-extrabold hover:bg-primary-800 transition-colors">Bagikan pengalaman</a>
                    <a href="/alumni" class="px-5 py-3 rounded-xl border border-slate-300 text-slate-700 text-sm font-extrabold hover:border-primary-500 transition-colors">Lihat alumni</a>
                </div>
            </div>
            <div class="lg:col-span-8">
                <?php if (!empty($cendekiaTestimonials)): ?>
                    <div class="grid md:grid-cols-2 gap-4">
                        <?php foreach (array_slice($cendekiaTestimonials, 0, 4) as $index => $testimonial): ?>
                            <article class="cendekia-quote p-6 sm:p-7 <?= $index === 0 ? 'md:row-span-2 bg-primary-800 text-white' : 'bg-slate-50 text-slate-800 border border-slate-200' ?>">
                                <span class="text-4xl font-black <?= $index === 0 ? 'text-cyan-300' : 'text-cyan-500' ?>" aria-hidden="true">&#8220;</span>
                                <p class="mt-3 leading-7 <?= $index === 0 ? 'text-lg sm:text-xl' : 'text-sm' ?> line-clamp-6"><?= e($testimonial['testimonial']) ?></p>
                                <div class="mt-6 pt-5 <?= $index === 0 ? 'border-t border-white/15' : 'border-t border-slate-200' ?>">
                                    <strong class="block text-sm"><?= e($testimonial['name']) ?></strong>
                                    <span class="block mt-1 text-xs <?= $index === 0 ? 'text-cyan-200' : 'text-slate-500' ?>"><?= e($testimonial['relationship']) ?></span>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="p-10 bg-slate-50 border border-slate-200 text-center text-slate-600">Belum ada
                        testimoni yang ditampilkan. Anda dapat menjadi orang pertama yang membagikan pengalaman.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php
$finalCtaHref = !empty($spmbPublic['active']) ? ($spmbPublic['url'] ?? '/spmb') : '/kontak';
$finalCtaText = !empty($spmbPublic['active']) ? ($spmbPublic['label'] ?? 'Informasi SPMB') : 'Hubungi Sekolah';
$finalCtaTarget = !empty($spmbPublic['active']) && ($spmbPublic['target'] ?? '_self') === '_blank' ? '_blank' : '_self';
?>
<section class="cendekia-final-cta bg-cyan-400 text-slate-950">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-20">
        <div class="grid lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-8">
                <span class="text-xs font-black uppercase tracking-[0.22em]">Kenal lebih dekat</span>
                <h2 class="mt-3 text-3xl sm:text-5xl font-extrabold tracking-tight">Mari berkunjung dan mengenal sekolah kami lebih dekat.</h2>
            </div>
            <div class="lg:col-span-4 lg:text-right">
                <a href="<?= e($finalCtaHref) ?>" target="<?= $finalCtaTarget ?>"
                    <?= $finalCtaTarget === '_blank' ? 'rel="noopener noreferrer"' : '' ?>
                    class="inline-flex items-center justify-center gap-3 px-7 py-4 rounded-2xl bg-slate-950 text-white font-extrabold hover:bg-primary-900 transition-colors">
                    <?= e($finalCtaText) ?> <span aria-hidden="true">&#8594;</span>
                </a>
            </div>
        </div>
    </div>
</section>
