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

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
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
    <?php if ($themeName === 'emerald-campus'): ?>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php elseif ($themeName === 'crimson-bold'): ?>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php else: ?>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php endif; ?>

    <style>
        body {
            <?php if ($themeName === 'emerald-campus'): ?>
            font-family: 'Poppins', sans-serif;
            <?php elseif ($themeName === 'crimson-bold'): ?>
            font-family: 'Plus Jakarta Sans', sans-serif;
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
            box-shadow: 4px 4px 0px rgba(var(--color-primary-900) / 0.2) !important;
            border: 1px solid rgb(var(--color-primary-200));
        }
        .shadow-sm, .shadow {
            box-shadow: 2px 2px 0px rgba(var(--color-primary-900) / 0.1) !important;
        }
        <?php endif; ?>
    </style>
</head>

<body class="bg-slate-50 min-h-screen flex flex-col theme-<?= e($themeName) ?>">
    <!-- Navigation -->
    <?php if ($themeName === 'emerald-campus'): ?>
        <?php include __DIR__ . '/partials/nav-emerald.php'; ?>
    <?php elseif ($themeName === 'crimson-bold'): ?>
        <?php include __DIR__ . '/partials/nav-crimson.php'; ?>
    <?php else: ?>
    <nav class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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

    <!-- Main Content -->
    <main class="flex-1">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <?php if ($themeName === 'emerald-campus'): ?>
        <?php include __DIR__ . '/partials/footer-emerald.php'; ?>
    <?php elseif ($themeName === 'crimson-bold'): ?>
        <?php include __DIR__ . '/partials/footer-crimson.php'; ?>
    <?php else: ?>
    <footer class="bg-slate-900 text-slate-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
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
                <?php
                $appDisclaimerClass = 'mt-3 text-xs text-slate-500';
                $appDisclaimerLabelClass = 'font-semibold text-slate-300';
                $appDisclaimerHighlightClass = 'font-semibold text-slate-300';
                include __DIR__ . '/partials/app-disclaimer.php';
                ?>
            </div>
        </div>
    </footer>
    <?php endif; ?>

    <!-- JavaScript -->
    <script src="/js/app.js?v=<?= filemtime(ROOT_PATH . '/public/js/app.js') ?>"></script>
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }
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
    <div id="contentProtectionOverlay">
        <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                </path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold mb-2">Akses Dibatasi</h2>
        <p class="text-slate-600">Fitur tangkap layar (screenshot) dan salin teks dinonaktifkan di halaman ini.</p>
    </div>

</body>

</html>
