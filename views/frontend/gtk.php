<?php
/**
 * Frontend - Guru & Tenaga Kependidikan (GTK)
 */
$title = $data['title'] ?? 'Guru & Tenaga Kependidikan';
$profile = $data['profile'] ?? null;
$groupedStaff = $data['groupedStaff'] ?? [];
?>

<style>
    /* =========================================
   ARTISTIC & GLASSMORPHISM DESIGN
   ========================================= */
    .glass-header {
        background: linear-gradient(135deg, rgba(30, 58, 138, 0.9), rgba(17, 24, 39, 0.9)), url('<?= isset($profile['logo']) ? "/storage/" . e($profile['logo']) : "" ?>') center/cover;
        background-blend-mode: overlay;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.08);
        border-radius: 1.5rem;
        /* 24px */
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .glass-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px -15px rgba(79, 70, 229, 0.2);
        border-color: rgba(79, 70, 229, 0.3);
    }

    .img-container {
        overflow: hidden;
        border-radius: 1.5rem;
        position: relative;
        padding-top: 100%;
        /* 1:1 Aspect Ratio */
    }

    /* Abstract blob background in image container */
    .img-container::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 80%;
        height: 80%;
        transform: translate(-50%, -50%);
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
        z-index: 0;
        animation: morph 8s ease-in-out infinite both alternate;
    }

    .glass-card:hover .img-container::before {
        background: linear-gradient(135deg, #818cf8 0%, #6366f1 100%);
    }

    @keyframes morph {

        0%,
        100% {
            border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
        }

        34% {
            border-radius: 70% 30% 50% 50% / 30% 30% 70% 70%;
        }

        67% {
            border-radius: 100% 60% 60% 100% / 100% 100% 60% 60%;
        }
    }

    .photo-el {
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        height: 95%;
        width: auto;
        max-width: 95%;
        object-fit: cover;
        object-position: bottom;
        z-index: 1;
        transition: transform 0.5s ease;
        filter: drop-shadow(0px 10px 10px rgba(0, 0, 0, 0.15));
    }

    .glass-card:hover .photo-el {
        transform: translateX(-50%) scale(1.08);
    }

</style>

<!-- ======================= HEADER ======================= -->
<section class="glass-header relative pt-32 pb-24 lg:pt-40 lg:pb-32 overflow-hidden protected-content">
    <div class="absolute inset-0 bg-indigo-900/50"></div>
    <!-- Decorative Elements -->
    <div
        class="absolute top-0 right-0 w-64 h-64 bg-primary-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 transform translate-x-1/2 -translate-y-1/2">
    </div>
    <div
        class="absolute bottom-0 left-0 w-80 h-80 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 transform -translate-x-1/2 translate-y-1/2">
    </div>

    <div class="container mx-auto px-4 relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 tracking-tight">
            Guru & Tenaga Kependidikan
        </h1>
        <p class="text-lg md:text-xl text-indigo-100 max-w-2xl mx-auto font-light">
            Mengenal lebih dekat para pendidik dan tenaga kependidikan berdedikasi di
            <?= e($profile['name'] ?? 'Sekolah Kami') ?>.
        </p>
    </div>
</section>

<!-- ======================= GTK CONTENT ======================= -->
<section class="py-16 lg:py-24 bg-pattern relative protected-content min-h-screen">
    <div class="container mx-auto px-4">

        <?php foreach ($groupedStaff as $groupKey => $groupData): ?>
            <?php if (!empty($groupData['items'])): ?>

                <div class="mb-20">
                    <div class="text-center mb-12">
                        <span class="text-indigo-600 font-semibold tracking-wider uppercase text-sm mb-2 block">Kategori</span>
                        <h2 class="text-3xl md:text-4xl font-bold text-slate-800">
                            <?= e($groupData['name']) ?>
                        </h2>
                        <div class="w-16 h-1 bg-indigo-500 mx-auto mt-4 rounded-full"></div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 lg:gap-10">
                        <?php foreach ($groupData['items'] as $staff): ?>
                            <div class="glass-card flex flex-col p-4 relative group">
                                <div class="img-container mb-6">
                                    <?php
                                    $photoSrc = (!empty($staff['photo']))
                                        ? '/storage/' . e($staff['photo'])
                                        : '/img/default-avatar.png'; // Make sure you have a default OR handle it via CSS empty state
                                    ?>
                                    <img src="<?= $photoSrc ?>" alt="Profile" class="photo-el protected-img" draggable="false" />
                                </div>
                                <div class="text-center pb-4 px-2">
                                    <h3
                                        class="text-xl font-bold text-slate-800 mb-1 leading-tight group-hover:text-indigo-700 transition-colors">
                                        <?= e($staff['name']) ?>
                                    </h3>
                                    <p
                                        class="text-sm font-medium text-slate-500 uppercase tracking-widest text-[10px] mt-2 border border-slate-200 inline-block px-3 py-1 rounded-full bg-white/50">
                                        <?= e($staff['position'] ?: 'Staff') ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            <?php endif; ?>
        <?php endforeach; ?>

        <?php if (empty($groupedStaff['teachers']['items']) && empty($groupedStaff['staff']['items'])): ?>
            <div class="text-center py-20">
                <div
                    class="w-24 h-24 bg-slate-200 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-400">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-2xl font-semibold text-slate-700 mb-2">Data GTK Belum Tersedia</h3>
                <p class="text-slate-500">Administrator belum menambahkan data Guru dan Tenaga Kependidikan.</p>
            </div>
        <?php endif; ?>

    </div>
</section>