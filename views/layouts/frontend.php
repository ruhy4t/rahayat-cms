<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="<?= e($news['meta_description'] ?? $settings['meta_description'] ?? (($profile['name'] ?? SCHOOL_NAME) . ' - Website Resmi')) ?>">
    <?php if (!empty($news['meta_keywords'])): ?>
        <meta name="keywords" content="<?= e($news['meta_keywords']) ?>">
    <?php endif; ?>
    <?= Security::csrfMeta() ?>

    <title>
        <?= e($title ?? 'Beranda') ?> |
        <?= e($profile['name'] ?? SCHOOL_NAME) ?>
    </title>

    <?php if (!empty($enableContentProtection)): ?>
    <meta name="content-protection" content="true">
    <?php endif; ?>

    <!-- Compiled production assets -->
    <link rel="stylesheet" href="/css/tailwind.min.css?v=<?= filemtime(ROOT_PATH . '/public/css/tailwind.min.css') ?>">
    <link rel="stylesheet" href="/css/fonts.css?v=<?= filemtime(ROOT_PATH . '/public/css/fonts.css') ?>">
    <style>
        :root {
            <?php
            // Default Theme configuration if not set
            if (!isset($themeConfig)) {
                $themeConfig = ['primary' => '#4f46e5'];
                $themeName = 'indigo-modern';
            }
            $baseColor = $themeConfig['primary'] ?? '#4f46e5';

            // Base Border Radius by Theme
            $baseRadius = '0.5rem'; // Default: indigo-modern
            if ($themeName === 'emerald-campus') {
                $baseRadius = '1.5rem'; // Highly rounded
            } else if ($themeName === 'crimson-bold') {
                $baseRadius = '0rem'; // Sharp edges
            } else if ($themeName === 'cendekia-smp') {
                $baseRadius = '1rem';
            }

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

            echo "--primary-rgb-50: " . hex2rgb($p50) . ";\n";
            echo "--primary-rgb-100: " . hex2rgb($p100) . ";\n";
            echo "--primary-rgb-200: " . hex2rgb($p200) . ";\n";
            echo "--primary-rgb-300: " . hex2rgb($p300) . ";\n";
            echo "--primary-rgb-400: " . hex2rgb($p400) . ";\n";
            echo "--primary-rgb-500: " . hex2rgb($p500) . ";\n";
            echo "--primary-rgb-600: " . hex2rgb($p600) . ";\n";
            echo "--primary-rgb-700: " . hex2rgb($p700) . ";\n";
            echo "--primary-rgb-800: " . hex2rgb($p800) . ";\n";
            echo "--primary-rgb-900: " . hex2rgb($p900) . ";\n";
            ?>
        }
    </style>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/app.css?v=<?= filemtime(ROOT_PATH . '/public/css/app.css') ?>">
    <?php if ($themeName === 'cendekia-smp'): ?>
        <link rel="stylesheet"
            href="/css/themes/cendekia-smp.css?v=<?= filemtime(ROOT_PATH . '/public/css/themes/cendekia-smp.css') ?>">
    <?php endif; ?>

    <style>
        body {
            <?php if ($themeName === 'emerald-campus'): ?>
            font-family: 'Poppins', sans-serif;
            <?php elseif ($themeName === 'crimson-bold'): ?>
            font-family: 'Plus Jakarta Sans', sans-serif;
            <?php elseif ($themeName === 'cendekia-smp'): ?>
            font-family: 'Manrope', sans-serif;
            <?php else: ?>
            font-family: 'Inter', sans-serif;
            <?php endif; ?>
            <?php if ($themeName === 'emerald-campus'): ?>
                --base-radius-sm: 0.5rem;
                --base-radius: 0.75rem;
                --base-radius-md: 1rem;
                --base-radius-lg: 1.25rem;
                --base-radius-xl: 1.5rem;
                --base-radius-2xl: 2rem;
                --base-radius-3xl: 2.5rem;
            <?php elseif ($themeName === 'crimson-bold'): ?>
                --base-radius-sm: 0;
                --base-radius: 0;
                --base-radius-md: 0;
                --base-radius-lg: 0;
                --base-radius-xl: 0;
                --base-radius-2xl: 0;
                --base-radius-3xl: 0;
            <?php elseif ($themeName === 'cendekia-smp'): ?>
                --base-radius-sm: 0.375rem;
                --base-radius: 0.625rem;
                --base-radius-md: 0.75rem;
                --base-radius-lg: 0.875rem;
                --base-radius-xl: 1rem;
                --base-radius-2xl: 1.25rem;
                --base-radius-3xl: 1.75rem;
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
        
        <?php if ($themeName === 'crimson-bold'): ?>
        .shadow-lg, .shadow-xl, .shadow-2xl {
            box-shadow: 4px 4px 0px rgba(var(--primary-rgb-900) / 0.2) !important;
            border: 1px solid rgb(var(--primary-rgb-200));
        }
        .shadow-sm, .shadow {
            box-shadow: 2px 2px 0px rgba(var(--primary-rgb-900) / 0.1) !important;
        }
        <?php endif; ?>

        .public-text-slider__track {
            display: flex;
            width: max-content;
            animation: publicTextSlider var(--public-text-slider-duration, 32s) linear infinite;
            animation-duration: var(--public-text-slider-duration, 32s) !important;
            animation-iteration-count: infinite !important;
            will-change: transform;
        }

        .public-text-slider__group {
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        .public-text-slider:hover .public-text-slider__track {
            animation-play-state: paused;
        }

        @keyframes publicTextSlider {
            from { transform: translateX(0); }
            to { transform: translateX(-50%); }
        }

    </style>
</head>

<body class="bg-slate-50 min-h-screen flex flex-col theme-<?= e($themeName) ?>">
    <!-- Navigation -->
    <?php if ($themeName === 'emerald-campus'): ?>
        <?php include __DIR__ . '/partials/nav-emerald.php'; ?>
    <?php elseif ($themeName === 'crimson-bold'): ?>
        <?php include __DIR__ . '/partials/nav-crimson.php'; ?>
    <?php elseif ($themeName === 'cendekia-smp'): ?>
        <?php include __DIR__ . '/partials/nav-cendekia.php'; ?>
    <?php else: ?>
    <nav class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-slate-200">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="/" class="flex items-center space-x-3">
                        <?php if (!empty($profile['logo'])): ?>
                            <div class="h-10 w-10 flex-shrink-0">
                                <img src="/storage/<?= e($profile['logo']) ?>" alt="Logo" class="h-10 w-10 object-contain">
                            </div>
                        <?php else: ?>
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl flex items-center justify-center shadow-lg shadow-primary-500/30">
                                <span
                                    class="text-white font-bold text-lg"><?= strtoupper(substr($profile['name'] ?? 'S', 0, 1)) ?></span>
                            </div>
                        <?php endif; ?>
                        <span class="font-bold text-xl text-slate-800 hidden sm:block">
                            <?= e($profile['name'] ?? SCHOOL_NAME) ?>
                        </span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-1">
                    <?php if (!empty($headerMenus)): ?>
                        <?php foreach ($headerMenus as $menu): ?>
                            <?php if (empty($menu['children'])): ?>
                                <a href="<?= e($menu['url']) ?>" <?= $menu['target'] === '_blank' ? 'target="_blank"' : '' ?>
                                    class="px-4 py-2 rounded-lg text-slate-600 hover:text-primary-600 hover:bg-primary-50 transition-all duration-200">
                                    <?= e($menu['title']) ?>
                                </a>
                            <?php else: ?>
                                <div class="relative group">
                                    <button class="flex items-center space-x-1 px-4 py-2 rounded-lg text-slate-600 hover:text-primary-600 hover:bg-primary-50 transition-all duration-200">
                                        <span><?= e($menu['title']) ?></span>
                                        <svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div class="absolute top-full left-0 mt-1 w-48 bg-white rounded-xl shadow-lg border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top -translate-y-2 group-hover:translate-y-0 z-50">
                                        <div class="py-2">
                                            <?php foreach ($menu['children'] as $child): ?>
                                                <a href="<?= e($child['url']) ?>" <?= $child['target'] === '_blank' ? 'target="_blank"' : '' ?>
                                                    class="block px-4 py-2 text-sm text-slate-600 hover:bg-primary-50 hover:text-primary-600">
                                                    <?= e($child['title']) ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['user_id'])): ?>
                    <?php else: ?>
                    <?php endif; ?>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button type="button" onclick="toggleMobileMenu()"
                        class="text-slate-500 hover:text-primary-600 p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-white border-t border-slate-200">
            <div class="px-4 py-3 space-y-1">
                <?php if (!empty($headerMenus)): ?>
                    <?php foreach ($headerMenus as $menu): ?>
                        <?php if (empty($menu['children'])): ?>
                            <a href="<?= e($menu['url']) ?>" <?= $menu['target'] === '_blank' ? 'target="_blank"' : '' ?>
                                class="block px-4 py-2 rounded-lg text-slate-600 hover:bg-primary-50 hover:text-primary-600">
                                <?= e($menu['title']) ?>
                            </a>
                        <?php else: ?>
                            <div class="space-y-1">
                                <div class="px-4 py-2 text-slate-800 font-medium">
                                    <?= e($menu['title']) ?>
                                </div>
                                <div class="pl-4 space-y-1">
                                    <?php foreach ($menu['children'] as $child): ?>
                                        <a href="<?= e($child['url']) ?>" <?= $child['target'] === '_blank' ? 'target="_blank"' : '' ?>
                                            class="block px-4 py-2 rounded-lg text-slate-600 hover:bg-primary-50 hover:text-primary-600 text-sm">
                                            <?= e($child['title']) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <?php if (!empty($activeTextSlides)): ?>
        <section class="public-text-slider bg-primary-700 text-white border-b border-primary-800 overflow-hidden"
            aria-label="Informasi terbaru">
            <div class="public-text-slider__track py-2.5">
                <?php for ($repeat = 0; $repeat < 2; $repeat++): ?>
                    <div class="public-text-slider__group" <?= $repeat === 1 ? 'aria-hidden="true"' : '' ?>>
                        <?php foreach ($activeTextSlides as $textSlide): ?>
                            <span class="inline-flex items-center gap-2 px-7 sm:px-10 whitespace-nowrap text-sm font-medium">
                                <span class="w-2 h-2 rounded-full bg-amber-300 shrink-0"></span>
                                <?php if (!empty($textSlide['title'])): ?>
                                    <strong><?= e($textSlide['title']) ?>:</strong>
                                <?php endif; ?>
                                <?= e($textSlide['content']) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endfor; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php
    $themePublicPath = trim((string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'), '/');
    $themePublicPage = $themePublicPath === '' ? 'home' : $themePublicPath;
    ?>
    <!-- Main Content -->
    <main class="flex-1" data-public-page="<?= e($themePublicPage) ?>">
        <?php if ($themeName === 'cendekia-smp'): ?>
            <div class="cendekia-page-surface">
                <?= $content ?>
            </div>
        <?php else: ?>
            <?= $content ?>
        <?php endif; ?>
    </main>

    <?php
    $currentPublicPath = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
    $showPopupOnHomepage = rtrim($currentPublicPath, '/') === '';
    ?>
    <?php if (!empty($activePopup) && $showPopupOnHomepage): ?>
        <div id="publicAnnouncementPopup"
            data-announcement-id="<?= (int) $activePopup['id'] ?>"
            class="fixed inset-0 z-[100] hidden items-center justify-center p-3 sm:p-6"
            role="dialog" aria-modal="true" aria-labelledby="publicAnnouncementTitle">
            <button type="button" data-popup-close aria-label="Tutup popup"
                class="absolute inset-0 bg-slate-950/65 backdrop-blur-sm"></button>
            <article class="relative w-full max-w-3xl max-h-[92vh] overflow-y-auto bg-white rounded-2xl sm:rounded-3xl shadow-2xl">
                <button type="button" data-popup-close aria-label="Tutup"
                    class="absolute top-3 right-3 z-10 w-10 h-10 rounded-full bg-white/95 text-slate-600 shadow flex items-center justify-center hover:text-slate-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <?php if (!empty($activePopup['image'])): ?>
                    <div class="w-full h-[36vh] sm:h-[45vh] bg-slate-100 flex items-center justify-center overflow-hidden">
                        <img src="/storage/<?= e($activePopup['image']) ?>"
                            alt="<?= e($activePopup['title'] ?? 'Informasi') ?>"
                            class="w-full h-full object-contain">
                    </div>
                <?php endif; ?>
                <div class="p-5 sm:p-8">
                    <div class="inline-flex items-center gap-2 px-3 py-1 mb-4 rounded-full bg-primary-50 text-primary-700 text-xs font-semibold uppercase tracking-wide">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary-500"></span>
                        Informasi
                    </div>
                    <?php if (!empty($activePopup['title'])): ?>
                        <h2 id="publicAnnouncementTitle" class="text-2xl sm:text-3xl font-bold text-slate-900">
                            <?= e($activePopup['title']) ?>
                        </h2>
                    <?php else: ?>
                        <h2 id="publicAnnouncementTitle" class="sr-only">Informasi</h2>
                    <?php endif; ?>
                    <div class="mt-4 text-sm sm:text-base text-slate-600 leading-relaxed">
                        <?= nl2br(e($activePopup['content'])) ?>
                    </div>
                </div>
            </article>
        </div>
    <?php endif; ?>

    <!-- Footer -->
    <?php if ($themeName === 'emerald-campus'): ?>
        <?php include __DIR__ . '/partials/footer-emerald.php'; ?>
    <?php elseif ($themeName === 'crimson-bold'): ?>
        <?php include __DIR__ . '/partials/footer-crimson.php'; ?>
    <?php elseif ($themeName === 'cendekia-smp'): ?>
        <?php include __DIR__ . '/partials/footer-cendekia.php'; ?>
    <?php else: ?>
    <footer class="bg-slate-900 text-slate-400">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                <!-- About -->
                <div class="md:col-span-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <?php if (!empty($profile['logo'])): ?>
                            <div class="h-10 w-10 flex-shrink-0">
                                <img src="/storage/<?= e($profile['logo']) ?>" alt="Logo" class="h-10 w-10 object-contain">
                            </div>
                        <?php else: ?>
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center">
                                <span
                                    class="text-white font-bold text-lg"><?= strtoupper(substr($profile['name'] ?? 'S', 0, 1)) ?></span>
                            </div>
                        <?php endif; ?>
                        <span class="font-bold text-xl text-white">
                            <?= e($profile['name'] ?? SCHOOL_NAME) ?>
                        </span>
                    </div>
                    <p class="text-sm text-slate-400 mb-6 leading-relaxed max-w-sm">
                        <?= e($profile['tagline'] ?? 'Dedicated to excellence in education.') ?>
                    </p>
                    <p class="text-sm leading-relaxed">
                        <?= e($profile['address'] ?? '') ?>
                    </p>
                    <p class="text-sm mt-2">Telp:
                        <?= e($profile['phone'] ?? '') ?>
                    </p>
                    <p class="text-sm">Email:
                        <?= e($profile['email'] ?? '') ?>
                    </p>
                </div>

                <!-- Quick Links -->
                <div class="md:col-span-3">
                    <h4 class="text-white font-semibold mb-4">Tautan Penting</h4>
                    <div class="grid grid-cols-1 gap-4">
                        <ul class="space-y-2 text-sm">
                            <?php if (!empty($footerMenus)): ?>
                                <?php 
                                $half = ceil(count($footerMenus) / 2);
                                $col1 = array_slice($footerMenus, 0, $half);
                                $col2 = array_slice($footerMenus, $half);
                                ?>
                                <?php foreach ($col1 as $menu): ?>
                                    <li><a href="<?= e($menu['url']) ?>" <?= $menu['target'] === '_blank' ? 'target="_blank"' : '' ?> class="hover:text-primary-400 transition-colors"><?= e($menu['title']) ?></a></li>
                                <?php endforeach; ?>
                        </ul>
                        <ul class="space-y-2 text-sm">
                                <?php foreach ($col2 as $menu): ?>
                                    <li><a href="<?= e($menu['url']) ?>" <?= $menu['target'] === '_blank' ? 'target="_blank"' : '' ?> class="hover:text-primary-400 transition-colors"><?= e($menu['title']) ?></a></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- Social Media Links -->
                <div class="md:col-span-3 md:text-right">
                    <h4 class="text-white font-semibold mb-4">Ikuti Kami</h4>
                    <div class="flex items-center md:justify-end space-x-4">
                        <?php if (!empty($settings['social_facebook'])): ?>
                            <a href="<?= e($settings['social_facebook']) ?>" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-primary-600 hover:text-white transition-all hover:-translate-y-1 shadow-md">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($settings['social_instagram'])): ?>
                            <a href="<?= e($settings['social_instagram']) ?>" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-pink-600 hover:text-white transition-all hover:-translate-y-1 shadow-md">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" /></svg>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($settings['social_twitter'])): ?>
                            <a href="<?= e($settings['social_twitter']) ?>" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-slate-700 hover:text-white transition-all hover:-translate-y-1 shadow-md">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" /></svg>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($settings['social_youtube'])): ?>
                            <a href="<?= e($settings['social_youtube']) ?>" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-red-600 hover:text-white transition-all hover:-translate-y-1 shadow-md">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M19.812 5.418c.861.23 1.538.907 1.768 1.768C21.998 8.746 22 12 22 12s0 3.255-.418 4.814a2.504 2.504 0 0 1-1.768 1.768c-1.56.419-7.814.419-7.814.419s-6.255 0-7.814-.419a2.505 2.505 0 0 1-1.768-1.768C2 15.255 2 12 2 12s0-3.255.417-4.814a2.507 2.507 0 0 1 1.768-1.768C5.744 5 11.998 5 11.998 5s6.255 0 7.814.418ZM15.194 12 10 15V9l5.194 3Z" clip-rule="evenodd" /></svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-800 mt-8 pt-8 text-center text-sm">
                <p>
                    <?php
                    $footerText = $settings['footer_text'] ?? '';
                    // Default if empty
                    if (empty($footerText)) {
                        $footerText = '&copy; {year} {school}. All rights reserved.';
                    }

                    // Replace placeholders
                    $replacements = [
                        '{year}' => date('Y'),
                        '{school}' => e($profile['name'] ?? SCHOOL_NAME)
                    ];

                    $output = e($footerText);
                    $output = str_replace(array_keys($replacements), array_values($replacements), $output);

                    if (empty($settings['footer_text'])) {
                        // We can construct HTML directly for default
                        echo '&copy; ' . date('Y') . ' ' . e($profile['name'] ?? SCHOOL_NAME) . '. All rights reserved.';
                    } else {
                        // User provided text.
                        echo $output;
                    }
                    ?>
                </p>
            </div>
        </div>
    </footer>
    <?php endif; ?>

    <?php
    $whatsappEnabled = !array_key_exists('whatsapp_enabled', $settings ?? [])
        || filter_var($settings['whatsapp_enabled'], FILTER_VALIDATE_BOOLEAN);
    $whatsappRawNumber = trim((string) ($settings['whatsapp_number'] ?? ($profile['phone'] ?? '')));
    $whatsappNumber = preg_replace('/\D+/', '', $whatsappRawNumber);
    if (str_starts_with($whatsappNumber, '0')) {
        $whatsappNumber = '62' . substr($whatsappNumber, 1);
    }
    $whatsappMessage = trim((string) ($settings['whatsapp_message'] ?? ''));
    if ($whatsappMessage === '') {
        $whatsappMessage = 'Halo, saya ingin menyampaikan saran, masukan, atau aduan kepada pihak sekolah.';
    }
    ?>
    <?php if ($whatsappEnabled && $whatsappNumber !== ''): ?>
        <a href="https://wa.me/<?= e($whatsappNumber) ?>?text=<?= urlencode($whatsappMessage) ?>"
            target="_blank" rel="noopener noreferrer"
            class="fixed right-4 bottom-5 sm:right-6 sm:bottom-6 z-40 group flex items-center gap-3 no-print"
            aria-label="Kirim saran, masukan, atau aduan melalui WhatsApp">
            <span class="hidden sm:block px-4 py-2.5 rounded-xl bg-white text-slate-700 text-sm font-semibold shadow-lg border border-slate-200 opacity-0 translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 group-focus:opacity-100 group-focus:translate-x-0 transition-all">
                Saran, Masukan & Aduan
            </span>
            <span class="relative w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-green-500 hover:bg-green-600 text-white flex items-center justify-center shadow-xl shadow-green-500/30 transition-all hover:-translate-y-1">
                <span class="absolute inset-0 rounded-full bg-green-400 animate-ping opacity-20"></span>
                <svg class="relative w-8 h-8" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884M12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893C23.943 5.347 18.606 0 12.05 0Z" />
                </svg>
            </span>
        </a>
    <?php endif; ?>

    <!-- JavaScript -->
    <script src="/js/form-enhancements.js?v=<?= filemtime(ROOT_PATH . '/public/js/form-enhancements.js') ?>"></script>
    <script src="/js/app.js?v=<?= filemtime(ROOT_PATH . '/public/js/app.js') ?>"></script>
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            if (!menu) return;
            menu.classList.toggle('hidden');
            const trigger = document.querySelector('[aria-controls="mobileMenu"]');
            trigger?.setAttribute('aria-expanded', String(!menu.classList.contains('hidden')));
        }

        (() => {
            document.querySelectorAll('.public-text-slider').forEach(slider => {
                const track = slider.querySelector('.public-text-slider__track');
                const groups = track?.querySelectorAll('.public-text-slider__group');
                if (!track || !groups || groups.length < 2) return;

                const primaryGroup = groups[0];
                const duplicateGroup = groups[1];
                const sourceItems = Array.from(primaryGroup.children, item => item.cloneNode(true));
                if (sourceItems.length === 0) return;

                const appendSourceItems = (group, hidden = false) => {
                    sourceItems.forEach(item => {
                        const clone = item.cloneNode(true);
                        if (hidden) clone.setAttribute('aria-hidden', 'true');
                        group.appendChild(clone);
                    });
                };

                const rebuildSlider = () => {
                    primaryGroup.replaceChildren();
                    appendSourceItems(primaryGroup);

                    // Repeat short announcements until one animation cycle covers the viewport.
                    let repeatGuard = 0;
                    while (primaryGroup.scrollWidth < slider.clientWidth * 1.15 && repeatGuard < 30) {
                        appendSourceItems(primaryGroup, true);
                        repeatGuard++;
                    }

                    duplicateGroup.replaceChildren(
                        ...Array.from(primaryGroup.children, item => item.cloneNode(true))
                    );

                    // Keep the marquee speed consistent regardless of content length.
                    const duration = Math.max(18, primaryGroup.scrollWidth / 45);
                    track.style.setProperty('--public-text-slider-duration', `${duration}s`);
                };

                let resizeTimer;
                rebuildSlider();
                window.addEventListener('resize', () => {
                    window.clearTimeout(resizeTimer);
                    resizeTimer = window.setTimeout(rebuildSlider, 150);
                }, { passive: true });
            });
        })();

        (() => {
            const popup = document.getElementById('publicAnnouncementPopup');
            if (!popup) return;

            const closePopup = () => {
                popup.classList.add('hidden');
                popup.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            };

            popup.querySelectorAll('[data-popup-close]').forEach(button => {
                button.addEventListener('click', closePopup);
            });

            document.addEventListener('keydown', event => {
                if (event.key === 'Escape' && !popup.classList.contains('hidden')) {
                    closePopup();
                }
            });

            window.setTimeout(() => {
                popup.classList.remove('hidden');
                popup.classList.add('flex');
                document.body.classList.add('overflow-hidden');
                popup.querySelector('[data-popup-close]')?.focus();
            }, 450);
        })();
    </script>

    <!-- ======================= CONTENT PROTECTION ======================= -->
    <style>
        /* Anti-copy & Anti-select */
        body.content-protected {
            -webkit-user-select: none !important;
            -khtml-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
            -webkit-touch-callout: none !important;
            -webkit-user-drag: none !important;
        }
        body.content-protected img {
            pointer-events: none;
            -webkit-user-drag: none !important;
        }
        .content-protection-watermark {
            position: fixed;
            inset: 0;
            z-index: 35;
            pointer-events: none;
            overflow: hidden;
            opacity: 0.055;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            align-content: space-around;
            transform: rotate(-18deg) scale(1.25);
            font-size: clamp(0.75rem, 1.5vw, 1.1rem);
            font-weight: 800;
            color: #0f172a;
            text-align: center;
        }
        /* Hide content when printing */
        @media print {
            body.content-protected {
                display: none !important;
            }
        }
        /* Blur overlay for screenshot attempt */
        #contentProtectionOverlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            z-index: 999999;
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #1e293b;
            opacity: 0;
            transition: opacity 0.3s;
        }
        #contentProtectionOverlay.active {
            display: flex;
            opacity: 1;
        }
    </style>

    <!-- Protection Overlay -->
    <?php if (!empty($enableContentProtection)): ?>
        <div class="content-protection-watermark" aria-hidden="true">
            <?php for ($watermarkIndex = 0; $watermarkIndex < 18; $watermarkIndex++): ?>
                <span><?= e($profile['name'] ?? SCHOOL_NAME) ?> · KONTEN DILINDUNGI</span>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
    <div id="contentProtectionOverlay">
        <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                </path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold mb-2">Akses Dibatasi</h2>
        <p class="text-slate-600 text-center px-6">Salin, cetak, dan tangkap layar melalui browser dibatasi pada halaman ini.</p>
    </div>

</body>

</html>
