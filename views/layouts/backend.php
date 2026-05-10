<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="
        default-src 'self';
        script-src 'self' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://cdn.ckeditor.com 'unsafe-inline';
        style-src 'self' https://fonts.googleapis.com 'unsafe-inline';
        img-src 'self' data: https://*;
        connect-src 'self' https://cdn.ckeditor.com https://*.ckeditor.com;
        frame-src 'self' https://www.google.com https://maps.google.com https://www.youtube.com https://www.youtube-nocookie.com;
        font-src 'self' https://fonts.gstatic.com;
        object-src 'none';
        base-uri 'self';
        form-action 'self';
    ">
    <?= Security::csrfMeta() ?>

    <title>
        <?= e($title ?? 'Dashboard') ?> | Admin -
        <?= e(SCHOOL_NAME) ?>
    </title>

    <!-- Tailwind CSS via CDN -->
    <script>
        // Suppress Tailwind CDN warning
        // Suppress Tailwind CDN warning
        const originalWarn = console.warn;
        console.warn = (...args) => {
            if (args[0] && args[0].includes && args[0].includes('cdn.tailwindcss.com should not be used in production')) return;
            originalWarn.apply(console, args);
        };
    </script>
    <style>
        :root {
            <?php
            // Default Theme configuration if not set
            if (!isset($themeConfig)) {
                $themeConfig = ['primary' => '#4f46e5'];
                $themeName = 'indigo-modern';
            }
            $baseColor = $themeConfig['primary'] ?? '#4f46e5';

            // Helpers
            if (!function_exists('adjustBrightness')) {
                function adjustBrightness($hex, $percent)
                {
                    $hex = ltrim($hex, '#');
                    if (strlen($hex) == 3) {
                        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
                        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
                        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
                    } else {
                        $r = hexdec(substr($hex, 0, 2));
                        $g = hexdec(substr($hex, 2, 2));
                        $b = hexdec(substr($hex, 4, 2));
                    }
                    if ($percent > 0) {
                        $r = $r + (255 - $r) * $percent;
                        $g = $g + (255 - $g) * $percent;
                        $b = $b + (255 - $b) * $percent;
                    } else {
                        $r = $r * (1 + $percent);
                        $g = $g * (1 + $percent);
                        $b = $b * (1 + $percent);
                    }
                    return sprintf("#%02x%02x%02x", $r, $g, $b);
                }
            }
            if (!function_exists('hex2rgb')) {
                function hex2rgb($hex)
                {
                    $hex = ltrim($hex, '#');
                    if (strlen($hex) == 3) {
                        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
                        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
                        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
                    } else {
                        $r = hexdec(substr($hex, 0, 2));
                        $g = hexdec(substr($hex, 2, 2));
                        $b = hexdec(substr($hex, 4, 2));
                    }
                    return "$r $g $b";
                }
            }

            // Generate palette
            $p50 = adjustBrightness($baseColor, 0.95);
            $p100 = adjustBrightness($baseColor, 0.9);
            $p200 = adjustBrightness($baseColor, 0.75);
            $p300 = adjustBrightness($baseColor, 0.6);
            $p400 = adjustBrightness($baseColor, 0.3);
            $p500 = $baseColor;
            $p600 = adjustBrightness($baseColor, -0.1);
            $p700 = adjustBrightness($baseColor, -0.25);
            $p800 = adjustBrightness($baseColor, -0.4);
            $p900 = adjustBrightness($baseColor, -0.6);

            echo "--color-primary-50: " . hex2rgb($p50) . ";\n";
            echo "--color-primary-100: " . hex2rgb($p100) . ";\n";
            echo "--color-primary-200: " . hex2rgb($p200) . ";\n";
            echo "--color-primary-300: " . hex2rgb($p300) . ";\n";
            echo "--color-primary-400: " . hex2rgb($p400) . ";\n";
            echo "--color-primary-500: " . hex2rgb($p500) . ";\n";
            echo "--color-primary-600: " . hex2rgb($p600) . ";\n";
            echo "--color-primary-700: " . hex2rgb($p700) . ";\n";
            echo "--color-primary-800: " . hex2rgb($p800) . ";\n";
            echo "--color-primary-900: " . hex2rgb($p900) . ";\n";
            ?>
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: 'rgb(var(--color-primary-50) / <alpha-value>)',
                            100: 'rgb(var(--color-primary-100) / <alpha-value>)',
                            200: 'rgb(var(--color-primary-200) / <alpha-value>)',
                            300: 'rgb(var(--color-primary-300) / <alpha-value>)',
                            400: 'rgb(var(--color-primary-400) / <alpha-value>)',
                            500: 'rgb(var(--color-primary-500) / <alpha-value>)',
                            600: 'rgb(var(--color-primary-600) / <alpha-value>)',
                            700: 'rgb(var(--color-primary-700) / <alpha-value>)',
                            800: 'rgb(var(--color-primary-800) / <alpha-value>)',
                            900: 'rgb(var(--color-primary-900) / <alpha-value>)',
                            DEFAULT: 'rgb(var(--color-primary-500) / <alpha-value>)',
                        }
                    },
                    borderRadius: {
                        'none': '0px',
                        'sm': 'var(--base-radius-sm, 0.125rem)',
                        DEFAULT: 'var(--base-radius, 0.25rem)',
                        'md': 'var(--base-radius-md, 0.375rem)',
                        'lg': 'var(--base-radius-lg, 0.5rem)',
                        'xl': 'var(--base-radius-xl, 0.75rem)',
                        '2xl': 'var(--base-radius-2xl, 1rem)',
                        '3xl': 'var(--base-radius-3xl, 1.5rem)',
                        'full': '9999px',
                    }
                }
            }
        }
    </script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/app.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            <?php if ($themeName === 'green-nature'): ?>
                --base-radius-sm: 0.5rem;
                --base-radius: 0.75rem;
                --base-radius-md: 1rem;
                --base-radius-lg: 1.25rem;
                --base-radius-xl: 1.5rem;
                --base-radius-2xl: 2rem;
                --base-radius-3xl: 2.5rem;
            <?php elseif ($themeName === 'blue-ocean'): ?>
                --base-radius-sm: 0;
                --base-radius: 0;
                --base-radius-md: 0;
                --base-radius-lg: 0;
                --base-radius-xl: 0;
                --base-radius-2xl: 0;
                --base-radius-3xl: 0;
            <?php else: ?>
                --base-radius-sm: 0.125rem;
                --base-radius: 0.25rem;
                --base-radius-md: 0.375rem;
                --base-radius-lg: 0.5rem;
                --base-radius-xl: 0.75rem;
                --base-radius-2xl: 1rem;
                --base-radius-3xl: 1.5rem;
            <?php endif; ?>
        }

        <?php if ($themeName === 'blue-ocean'): ?>
            .shadow-lg,
            .shadow-xl,
            .shadow-2xl {
                box-shadow: 4px 4px 0px rgba(var(--color-primary-900) / 0.2) !important;
                border: 1px solid rgb(var(--color-primary-200));
            }

            .shadow-sm,
            .shadow {
                box-shadow: 2px 2px 0px rgba(var(--color-primary-900) / 0.1) !important;
            }

        <?php endif; ?>
    </style>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-slate-100 min-h-screen theme-<?= e($themeName) ?>">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 transform -translate-x-full lg:translate-x-0 lg:static transition-transform duration-300 ease-in-out">
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-6 bg-slate-800/50">
                <a href="/admin" class="flex items-center space-x-3">
                    <div
                        class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold">R</span>
                    </div>
                    <span class="font-bold text-white">Admin Panel</span>
                </a>
                <button onclick="toggleSidebar()" class="lg:hidden text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="mt-6 px-4">
                <?php
                // Parse user permissions for sidebar visibility
                $userRole = $user['role'] ?? '';
                $userPerms = [];
                if ($userRole === 'admin') {
                    $userPerms = ['berita', 'kategori', 'galeri', 'slider', 'profil', 'fasilitas', 'staff', 'spmb', 'prestasi'];
                } elseif ($userRole === 'gtk') {
                    $raw = $user['permissions'] ?? null;
                    if ($raw) {
                        $userPerms = is_string($raw) ? json_decode($raw, true) : $raw;
                        if (!is_array($userPerms))
                            $userPerms = ['berita', 'kategori', 'galeri', 'slider', 'profil', 'fasilitas', 'staff', 'spmb', 'prestasi'];
                    } else {
                        $userPerms = ['berita', 'kategori', 'galeri', 'slider', 'profil', 'fasilitas', 'staff', 'spmb', 'prestasi'];
                    }
                } elseif (in_array($userRole, ['murid', 'ekskul'])) {
                    $userPerms = ['berita', 'galeri'];
                }
                ?>
                <div class="space-y-1">
                    <div class="px-4 mt-4 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Utama
                    </div>
                    <a href="/admin"
                        class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors group <?= ($_SERVER['REQUEST_URI'] === '/admin') ? 'bg-slate-800 text-white' : '' ?>">
                        <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-primary-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    <?php if (in_array('berita', $userPerms)): ?>
                        <div class="px-4 mt-6 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Konten
                        </div>
                        <a href="/admin/berita"
                            class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors group <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/berita') ? 'bg-slate-800 text-white' : '' ?>">
                            <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-primary-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                            Berita
                        </a>
                    <?php endif; ?>

                    <?php if (in_array('kategori', $userPerms)): ?>
                        <a href="/admin/kategori"
                            class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors group <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/kategori') ? 'bg-slate-800 text-white' : '' ?>">
                            <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-primary-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Kategori
                        </a>
                    <?php endif; ?>

                    <?php if (in_array('galeri', $userPerms)): ?>
                        <a href="/admin/galeri"
                            class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors group <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/galeri') ? 'bg-slate-800 text-white' : '' ?>">
                            <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-primary-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Galeri
                        </a>
                    <?php endif; ?>

                    <?php if (in_array('prestasi', $userPerms)): ?>
                        <a href="/admin/prestasi"
                            class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors group <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/prestasi') ? 'bg-slate-800 text-white' : '' ?>">
                            <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-primary-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                            Prestasi
                        </a>
                    <?php endif; ?>

                    <?php if (in_array('slider', $userPerms)): ?>
                        <a href="/admin/slides"
                            class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors group <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/slides') ? 'bg-slate-800 text-white' : '' ?>">
                            <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-primary-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Slider Hero
                        </a>
                    <?php endif; ?>

                    <?php if (in_array('profil', $userPerms) || in_array('fasilitas', $userPerms) || in_array('staff', $userPerms)): ?>
                        <div class="px-4 mt-6 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Sekolah
                        </div>
                    <?php endif; ?>

                    <?php if (in_array('profil', $userPerms)): ?>
                        <a href="/admin/profil"
                            class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors group <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/profil') ? 'bg-slate-800 text-white' : '' ?>">
                            <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-primary-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Profil Sekolah
                        </a>
                    <?php endif; ?>

                    <?php if (in_array('fasilitas', $userPerms)): ?>
                        <a href="/admin/fasilitas"
                            class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors group <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/fasilitas') ? 'bg-slate-800 text-white' : '' ?>">
                            <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-primary-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Fasilitas
                        </a>

                        <a href="/admin/ekstrakurikuler"
                            class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors group <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/ekstrakurikuler') ? 'bg-slate-800 text-white' : '' ?>">
                            <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-primary-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            Ekstrakurikuler
                        </a>
                    <?php endif; ?>

                    <?php if (in_array('staff', $userPerms)): ?>
                        <a href="/admin/gtk"
                            class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors group <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/gtk') ? 'bg-slate-800 text-white' : '' ?>">
                            <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-primary-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Kelola GTK
                        </a>
                    <?php endif; ?>

                    <?php
                    $schoolProfile = (new SchoolProfile())->getProfile();
                    $isSwasta = !empty($schoolProfile['school_type']) && $schoolProfile['school_type'] === 'swasta';
                    if ($isSwasta && in_array('spmb', $userPerms) && (($user['role'] ?? '') === 'admin' || !empty($user['is_spmb_committee']))):
                        ?>
                        <a href="/admin/spmb"
                            class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors group <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/spmb') ? 'bg-slate-800 text-white' : '' ?>">
                            <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-green-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            SPMB
                        </a>
                    <?php endif; ?>

                    <?php if ($userRole === 'admin'): ?>
                        <div class="px-4 mt-6 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Sistem
                        </div>
                        <a href="/admin/menu"
                            class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors group <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/menu') ? 'bg-slate-800 text-white' : '' ?>">
                            <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-primary-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            Kelola Menu
                        </a>

                        <a href="/admin/pengguna"
                            class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors group <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/pengguna') ? 'bg-slate-800 text-white' : '' ?>">
                            <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-primary-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Pengguna
                        </a>

                        <a href="/admin/pengaturan"
                            class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors group <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/pengaturan') ? 'bg-slate-800 text-white' : '' ?>">
                            <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-primary-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Pengaturan
                        </a>

                        <a href="/admin/pembaruan"
                            class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors group <?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/pembaruan') ? 'bg-slate-800 text-white' : '' ?>">
                            <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-primary-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Pembaruan
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Divider -->
                <div class="my-6 border-t border-slate-700"></div>

                <!-- Secondary Links -->
                <div class="space-y-1">
                    <a href="/" target="_blank"
                        class="flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors group">
                        <svg class="w-5 h-5 mr-3 text-slate-400 group-hover:text-primary-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        Lihat Website
                    </a>

                    <a href="/logout"
                        class="flex items-center px-4 py-3 text-red-400 hover:bg-red-900/20 hover:text-red-300 rounded-lg transition-colors group">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-0">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm z-40">
                <div class="flex items-center justify-between h-16 px-6">
                    <!-- Mobile menu button -->
                    <button onclick="toggleSidebar()" class="lg:hidden text-slate-500 hover:text-slate-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Page Title -->
                    <h1 class="text-xl font-semibold text-slate-800">
                        <?= e($title ?? 'Dashboard') ?>
                    </h1>

                    <!-- User Menu -->
                    <div class="flex items-center space-x-4 relative">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-medium text-slate-700">
                                <?= e($user['name'] ?? 'User') ?>
                            </p>
                            <p class="text-xs text-slate-500">
                                <?= e(ucfirst($user['role'] ?? 'Admin')) ?>
                            </p>
                        </div>
                        <button onclick="toggleUserMenu()" class="focus:outline-none">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-white font-semibold shadow-sm border-2 border-transparent hover:border-primary-300 transition-colors">
                                <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                            </div>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="userMenuDropdown"
                            class="absolute right-0 top-12 mt-2 w-48 bg-white rounded-lg shadow-xl border border-slate-100 hidden z-50">
                            <div class="py-1">
                                <button onclick="openPasswordModal()"
                                    class="w-full text-left block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-primary-600">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                        </svg>
                                        Ganti Password
                                    </div>
                                </button>
                                <div class="border-t border-slate-100 my-1"></div>
                                <a href="/logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Logout
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Flash Messages -->
                <!-- Flash Messages -->
                <?php if (isset($flash) && $flash): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            Swal.fire({
                                icon: '<?= $flash['type'] ?>',
                                title: '<?= $flash['type'] === 'success' ? 'Berhasil!' : 'Gagal!' ?>',
                                text: '<?= e($flash['message']) ?>',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            });
                        });
                    </script>
                <?php endif; ?>

                <?= $content ?>
            </main>
        </div>
    </div>

    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden"></div>

    <!-- Change Password Modal -->
    <div id="changePasswordModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-800">Ganti Password</h3>
                <button onclick="closePasswordModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="changePasswordForm" class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Password Lama</label>
                        <div class="relative">
                            <input type="password" id="old_password" name="old_password"
                                class="w-full px-3 py-2 pr-10 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                            <button type="button" class="toggle-password absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600" data-target="old_password">
                                <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Password Baru</label>
                        <div class="relative">
                            <input type="password" id="new_password" name="new_password"
                                class="w-full px-3 py-2 pr-10 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                            <button type="button" class="toggle-password absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600" data-target="new_password">
                                <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input type="password" id="confirm_password" name="confirm_password"
                                class="w-full px-3 py-2 pr-10 border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                            <button type="button" class="toggle-password absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600" data-target="confirm_password">
                                <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closePasswordModal()"
                        class="px-4 py-2 border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="button" onclick="submitChangePassword()" id="btnSavePassword"
                        class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="/js/admin.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        function toggleUserMenu() {
            const dropdown = document.getElementById('userMenuDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        window.addEventListener('click', function (e) {
            const dropdown = document.getElementById('userMenuDropdown');
            const userMenuBtn = document.querySelector('button[onclick="toggleUserMenu()"]');

            if (!dropdown.classList.contains('hidden') && !dropdown.contains(e.target) && !userMenuBtn.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Change Password Modal Logic
        function openPasswordModal() {
            document.getElementById('changePasswordModal').classList.remove('hidden');
            document.getElementById('changePasswordModal').classList.add('flex');
            document.getElementById('userMenuDropdown').classList.add('hidden');
        }

        function closePasswordModal() {
            document.getElementById('changePasswordModal').classList.add('hidden');
            document.getElementById('changePasswordModal').classList.remove('flex');
            document.getElementById('changePasswordForm').reset();
            // Reset all password fields to hidden
            document.querySelectorAll('#changePasswordModal .toggle-password').forEach(btn => {
                const input = document.getElementById(btn.dataset.target);
                if (input) input.type = 'password';
                btn.querySelector('.eye-open').classList.remove('hidden');
                btn.querySelector('.eye-closed').classList.add('hidden');
            });
        }

        function submitChangePassword() {
            const form = document.getElementById('changePasswordForm');
            const formData = new FormData(form);
            const btn = document.getElementById('btnSavePassword');
            
            if (!formData.get('old_password')) {
                Swal.fire('Peringatan', 'Password lama harus diisi', 'warning');
                return;
            }
            if (formData.get('new_password').length < 8) {
                Swal.fire('Peringatan', 'Password baru minimal 8 karakter', 'warning');
                return;
            }
            if(formData.get('new_password') !== formData.get('confirm_password')) {
                Swal.fire('Peringatan', 'Konfirmasi password baru tidak cocok', 'warning');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = 'Menyimpan...';

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            
            fetch('/api/users/change-password', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            }).then(r => r.json()).then(data => {
                btn.disabled = false;
                btn.innerHTML = 'Simpan';
                
                if(data.success) {
                    closePasswordModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message || 'Password berhasil diubah',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                } else {
                    Swal.fire('Gagal', data.message || 'Gagal mengubah password', 'error');
                }
            }).catch(() => {
                btn.disabled = false;
                btn.innerHTML = 'Simpan';
                Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
            });
        }

        // Toggle password visibility
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.toggle-password').forEach(btn => {
                btn.addEventListener('click', function() {
                    const input = document.getElementById(this.dataset.target);
                    if (!input) return;
                    const isPassword = input.type === 'password';
                    input.type = isPassword ? 'text' : 'password';
                    this.querySelector('.eye-open').classList.toggle('hidden', isPassword);
                    this.querySelector('.eye-closed').classList.toggle('hidden', !isPassword);
                });
            });
        });
    </script>
</body>

</html>
