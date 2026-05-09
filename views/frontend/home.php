<?php
/**
 * Frontend Homepage with Hero Slider
 */
$title = $data['title'] ?? 'Beranda';
$profile = $data['profile'] ?? [];
$news = $data['news'] ?? [];
$slides = $data['slides'] ?? [];
$theme = $data['theme'] ?? 'indigo-modern';
?>

<!-- Hero Section with Slider -->
<?php if ($theme === 'emerald-campus'): ?>
    <?php include __DIR__ . '/home/partials/hero-emerald.php'; ?>
<?php elseif ($theme === 'crimson-bold'): ?>
    <?php include __DIR__ . '/home/partials/hero-crimson.php'; ?>
<?php else: ?>
    <section class="relative overflow-hidden min-h-[600px] lg:min-h-[700px]">
        <!-- Background Slider -->
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

        <!-- Gradient Overlay -->
        <div class="absolute inset-0 z-[5] bg-gradient-to-br from-primary-600/90 via-primary-700/85 to-slate-900/90"></div>

        <!-- Background Pattern -->
        <div class="absolute inset-0 z-[6] opacity-10">
            <div class="absolute inset-0"
                style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;0.4&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
            </div>
        </div>

        <!-- Content -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32 text-white">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Column: Content & Buttons -->
                <div class="space-y-8">
                    <?php if (!empty($profile['school_type']) && $profile['school_type'] === 'swasta'): ?>
                        <div
                            class="inline-flex items-center px-4 py-2 bg-white/10 rounded-full text-sm font-medium backdrop-blur-sm">
                            <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                            Pendaftaran SPMB Dibuka
                        </div>
                    <?php endif; ?>

                    <h1 class="text-4xl lg:text-6xl font-bold leading-tight">
                        Selamat Datang di
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-200 to-white">
                            <?= e($profile['name'] ?? SCHOOL_NAME) ?>
                        </span>
                    </h1>

                    <?php if (!empty($profile['tagline'])): ?>
                        <div class="text-xl lg:text-2xl text-primary-100 font-medium italic">
                            <?= e($profile['tagline']) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($profile['vision'])): ?>
                        <div class="text-lg lg:text-xl text-primary-100 leading-relaxed max-w-xl">
                            <?= $profile['vision'] ?>
                        </div>
                    <?php endif; ?>

                    <!-- Buttons moved to Left Column -->
                    <div class="flex flex-wrap gap-4 pt-4">
                        <a href="/profil"
                            class="inline-flex items-center px-6 py-3 bg-white text-primary-700 font-semibold rounded-lg hover:bg-primary-50 transition-all duration-200 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                            Tentang Kami
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>

                        <?php
                        // SPMB Button Logic
                        $schoolType = $profile['school_type'] ?? 'negeri';
                        $spmbLink = $profile['spmb_link'] ?? '#';
                        $showSpmbButton = false;
                        $spmbButtonText = 'Daftar Sekarang';
                        $spmbButtonTarget = '_self'; // Default self
                    
                        if ($schoolType === 'swasta') {
                            // Swasta: Use internal SPMB feature
                            $spmbLink = '/spmb';
                            $showSpmbButton = true;
                            $spmbButtonText = 'Daftar SPMB';
                        } elseif ($schoolType === 'negeri' && !empty($profile['spmb_link'])) {
                            // Negeri: External Link
                            $spmbLink = $profile['spmb_link'];
                            $showSpmbButton = true;
                            $spmbButtonText = 'SPMB Wilayah';
                            $spmbButtonTarget = '_blank'; // External link usually blank
                        }
                        ?>

                        <?php if ($showSpmbButton): ?>
                            <a href="<?= e($spmbLink) ?>" target="<?= $spmbButtonTarget ?>"
                                class="inline-flex items-center px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                                <?= e($spmbButtonText) ?>
                            </a>
                        <?php else: ?>
                            <!-- Fallback button if no SPMB logic applies (e.g. Negeri with no link) -->
                            <a href="/kontak"
                                class="inline-flex items-center px-6 py-3 border-2 border-white/30 text-white font-semibold rounded-lg hover:bg-white/10 backdrop-blur-sm transition-all duration-200">
                                Hubungi Kami
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Column: Stats Cards -->
                <div class="grid grid-cols-2 gap-4">
                    <?php if (!empty($profile['total_students'])): ?>
                        <div
                            class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-colors duration-300">
                            <div class="text-4xl font-bold">
                                <?= e($profile['total_students']) ?>+
                            </div>
                            <div class="text-primary-200 mt-1">Murid Aktif</div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($profile['total_teachers'])): ?>
                        <div
                            class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-colors duration-300">
                            <div class="text-4xl font-bold">
                                <?= e($profile['total_teachers']) ?>+
                            </div>
                            <div class="text-primary-200 mt-1">Guru Profesional</div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($profile['accreditation'])): ?>
                        <div
                            class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-colors duration-300">
                            <div class="text-4xl font-bold">
                                <?= e($profile['accreditation']) ?>
                            </div>
                            <div class="text-primary-200 mt-1">Akreditasi</div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($profile['graduation_rate'])): ?>
                        <div
                            class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-colors duration-300">
                            <div class="text-4xl font-bold">
                                <?= e($profile['graduation_rate']) ?>%
                            </div>
                            <div class="text-primary-200 mt-1">Kelulusan</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Slider Indicators -->
        <?php if (!empty($slides) && count($slides) > 1): ?>
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20 flex gap-2">
                <?php foreach ($slides as $index => $slide): ?>
                    <button
                        class="slider-dot w-3 h-3 rounded-full transition-all duration-300 <?= $index === 0 ? 'bg-white w-8' : 'bg-white/50' ?>"
                        data-slide="<?= $index ?>"></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Wave Divider -->
        <div class="absolute bottom-0 left-0 right-0 z-20 pointer-events-none">
            <svg viewBox="0 0 1440 120" fill="none" class="w-full h-auto block" xmlns="http://www.w3.org/2000/svg"
                preserveAspectRatio="none">
                <path
                    d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z"
                    fill="#f8fafc" />
            </svg>
        </div>
    </section>
<?php endif; ?>

<!-- News Section -->
<?php if ($theme === 'emerald-campus'): ?>
    <?php include __DIR__ . '/home/partials/news-emerald.php'; ?>
<?php elseif ($theme === 'crimson-bold'): ?>
    <?php include __DIR__ . '/home/partials/news-crimson.php'; ?>
<?php else: ?>
    <section class="py-16 lg:py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between mb-12">
                <div>
                    <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">Informasi Terkini</span>
                    <h2 class="text-3xl lg:text-4xl font-bold text-slate-800 mt-2">Berita & Pengumuman</h2>
                </div>
                <a href="/berita"
                    class="mt-4 md:mt-0 inline-flex items-center text-primary-600 hover:text-primary-700 font-medium">
                    Lihat Semua
                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

            <div id="newsContainer" class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if (!empty($news)): ?>
                    <?php foreach ($news as $item): ?>
                        <?php if ($item['status'] === 'published'): ?>
                            <article
                                class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-slate-200/50">
                                <div class="aspect-video bg-gradient-to-br from-primary-100 to-primary-200 relative overflow-hidden">
                                    <?php if (!empty($item['image'])): ?>
                                        <img src="/storage/<?= e($item['image']) ?>" alt="<?= e($item['title']) ?>"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                            loading="lazy">
                                    <?php else: ?>
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <svg class="w-16 h-16 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                    <div class="absolute top-4 left-4">
                                        <span class="px-3 py-1 bg-primary-600 text-white text-xs font-medium rounded-full">
                                            <?= e(ucfirst($item['category'])) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <div class="flex items-center text-sm text-slate-500 mb-3">
                                        <span>
                                            <?= date('d M Y', strtotime($item['published_at'] ?? $item['created_at'])) ?>
                                        </span>
                                        <span class="mx-2">•</span>
                                        <span>
                                            <?= e($item['author_name'] ?? 'Admin') ?>
                                        </span>
                                    </div>
                                    <h3
                                        class="text-lg font-semibold text-slate-800 mb-2 group-hover:text-primary-600 transition-colors line-clamp-2">
                                        <a href="/berita/<?= e($item['slug']) ?>">
                                            <?= e($item['title']) ?>
                                        </a>
                                    </h3>
                                    <p class="text-slate-600 text-sm line-clamp-2">
                                        <?= e($item['excerpt']) ?>
                                    </p>
                                    <a href="/berita/<?= e($item['slug']) ?>"
                                        class="inline-flex items-center mt-4 text-primary-600 hover:text-primary-700 font-medium text-sm">
                                        Baca Selengkapnya
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </article>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-3 text-center py-12">
                        <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                        <p class="text-slate-500">Belum ada berita</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Fasilitas Section (Default Theme) -->
<?php if ($theme === 'indigo-modern'): ?>
    <?php if (!empty($facilities)): ?>
        <section class="py-16 lg:py-24 bg-white relative overflow-hidden">
            <!-- Background elements -->
            <div class="absolute top-0 right-0 -translate-y-12 translate-x-1/3 opacity-5 pointer-events-none text-primary-600">
                <svg width="404" height="404" fill="none" viewBox="0 0 404 404">
                    <defs>
                        <pattern id="85737c0e-0916-41d7-917f-596dc7edfa27" x="0" y="0" width="20" height="20"
                            patternUnits="userSpaceOnUse">
                            <rect x="0" y="0" width="4" height="4" fill="currentColor"></rect>
                        </pattern>
                    </defs>
                    <rect width="404" height="404" fill="url(#85737c0e-0916-41d7-917f-596dc7edfa27)"></rect>
                </svg>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">Infrastruktur</span>
                    <h2 class="text-3xl lg:text-4xl font-bold text-slate-800 mt-2 mb-4">Fasilitas Sekolah</h2>
                    <p class="text-slate-600">Kami menyediakan berbagai fasilitas pendukung untuk menunjang kegiatan belajar
                        mengajar secara optimal.</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php $i = 0;
                    foreach ($facilities as $fasilitas):
                        if ($i++ >= 4)
                            break; ?>
                        <div
                            class="group relative rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300">
                            <div class="aspect-[4/3] bg-slate-100 relative">
                                <?php if (!empty($fasilitas['image'])): ?>
                                    <img src="/storage/<?= e($fasilitas['image']) ?>" alt="<?= e($fasilitas['name']) ?>"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                        loading="lazy">
                                <?php else: ?>
                                    <div
                                        class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100 text-primary-300">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent">
                                </div>
                            </div>
                            <div
                                class="absolute bottom-0 left-0 right-0 p-6 translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                <span class="inline-block px-2 py-1 bg-primary-600 text-white text-xs font-medium rounded mb-2">
                                    <?= e(ucfirst($fasilitas['type'])) ?>
                                </span>
                                <h3 class="text-xl font-bold text-white mb-1">
                                    <?= e($fasilitas['name']) ?>
                                </h3>
                                <p
                                    class="text-slate-200 text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 line-clamp-2">
                                    <?= e($fasilitas['description'] ?? '-') ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="text-center mt-12">
                    <a href="/profil"
                        class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium group">
                        Lihat Semua Fasilitas
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Ekstrakurikuler Section (Default Theme) -->
    <?php if (!empty($ekskul)): ?>
        <section class="py-16 lg:py-24 bg-slate-50 relative border-t border-slate-200/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-end md:justify-between mb-12">
                    <div>
                        <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">Pengembangan Diri</span>
                        <h2 class="text-3xl lg:text-4xl font-bold text-slate-800 mt-2">Ekstrakurikuler</h2>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php $i = 0;
                    foreach ($ekskul as $item):
                        if ($i++ >= 6)
                            break; ?>
                        <div
                            class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-xl hover:border-primary-100 transition-all duration-300 group">
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-16 h-16 rounded-xl shrink-0 overflow-hidden bg-gradient-to-br from-primary-50 to-primary-100 flex items-center justify-center text-primary-500 relative">
                                    <?php if (!empty($item['image'])): ?>
                                        <img src="/storage/<?= e($item['image']) ?>" alt="<?= e($item['name']) ?>"
                                            class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h3
                                        class="text-lg font-bold text-slate-800 mb-1 group-hover:text-primary-600 transition-colors">
                                        <?= e($item['name']) ?>
                                    </h3>

                                    <?php if (!empty($item['schedule'])): ?>
                                        <div class="text-xs text-slate-500 flex items-center mb-2">
                                            <svg class="w-3.5 h-3.5 mr-1 text-slate-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <?= e($item['schedule']) ?>
                                        </div>
                                    <?php endif; ?>

                                    <p class="text-slate-600 text-sm line-clamp-2">
                                        <?= e($item['description'] ?? 'Kegiatan ekstrakurikuler ' . $item['name']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
<?php endif; ?>

<!-- CTA Section -->
<section class="py-16 lg:py-24 bg-gradient-to-r from-primary-600 to-primary-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">Bergabunglah Bersama Kami</h2>
        <p class="text-xl text-primary-100 mb-8 max-w-2xl mx-auto">Daftarkan putra-putri Anda untuk mendapatkan
            pendidikan berkualitas dan masa depan yang cerah.</p>
        <a href="<?= (!empty($profile['school_type']) && $profile['school_type'] === 'swasta') ? '/spmb' : '/kontak' ?>"
            class="inline-flex items-center px-8 py-4 bg-white text-primary-700 font-semibold rounded-lg hover:bg-primary-50 transition-all duration-200 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
            <?= (!empty($profile['school_type']) && $profile['school_type'] === 'swasta') ? 'Daftar SPMB' : 'Hubungi Kami' ?>
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
        </a>
    </div>
</section>

<!-- Hero Slider Script -->
<?php if (!empty($slides) && count($slides) > 1): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const slides = document.querySelectorAll('.hero-slide');
            const dots = document.querySelectorAll('.slider-dot');
            let currentSlide = 0;
            const totalSlides = slides.length;

            function showSlide(index) {
                slides.forEach((slide, i) => {
                    slide.classList.toggle('opacity-100', i === index);
                    slide.classList.toggle('opacity-0', i !== index);
                });
                dots.forEach((dot, i) => {
                    dot.classList.toggle('bg-white', i === index);
                    dot.classList.toggle('w-8', i === index);
                    dot.classList.toggle('bg-white/50', i !== index);
                    dot.classList.toggle('w-3', i !== index);
                });
                currentSlide = index;
            }

            // Auto slide
            setInterval(() => {
                showSlide((currentSlide + 1) % totalSlides);
            }, 5000);

            // Dot click
            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => showSlide(index));
            });
        });
    </script>
<?php endif; ?>