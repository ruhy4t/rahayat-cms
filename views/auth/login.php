<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= Security::csrfMeta() ?>

    <title>Login |
        <?= e(SCHOOL_NAME) ?>
    </title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
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

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            <?php if (isset($themeName) && $themeName === 'green-nature'): ?>
                --base-radius-sm: 0.5rem;
                --base-radius: 0.75rem;
                --base-radius-md: 1rem;
                --base-radius-lg: 1.25rem;
                --base-radius-xl: 1.5rem;
                --base-radius-2xl: 2rem;
                --base-radius-3xl: 2.5rem;
            <?php elseif (isset($themeName) && $themeName === 'blue-ocean'): ?>
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

        <?php if (isset($themeName) && $themeName === 'blue-ocean'): ?>
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
</head>

<body
    class="min-h-screen bg-gradient-to-br from-primary-600 via-primary-700 to-slate-900 flex items-center justify-center p-4 theme-<?= e($themeName ?? 'indigo-modern') ?>">
    <!-- Background Pattern -->
    <div class="fixed inset-0 opacity-10 pointer-events-none">
        <div class="absolute inset-0"
            style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;0.4&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
        </div>
    </div>

    <div class="w-full max-w-md relative">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center justify-center space-x-3">
                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-xl">
                    <span class="text-primary-600 font-bold text-2xl">R</span>
                </div>
            </a>
            <h1 class="text-white text-2xl font-bold mt-4">
                <?= e(SCHOOL_NAME) ?>
            </h1>
            <p class="text-primary-200 mt-1">Admin Panel</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-slate-800">Selamat Datang</h2>
                <p class="text-slate-500 mt-1">Masuk ke akun Anda</p>
            </div>

            <!-- Flash Message -->
            <?php if (isset($flash) && $flash): ?>
                <div
                    class="mb-6 px-4 py-3 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200' ?>">
                    <?= e($flash['message']) ?>
                </div>
            <?php endif; ?>

            <form action="/login" method="POST" class="space-y-5">
                <?= Security::csrfInput() ?>

                <div>
                    <label for="username" class="block text-sm font-medium text-slate-700 mb-2">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input type="text" id="username" name="username" required autofocus
                            class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                            placeholder="Masukkan username">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" id="password" name="password" required
                            class="w-full pl-10 pr-12 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                            placeholder="Masukkan password">
                        <button type="button" onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg id="eyeIcon" class="w-5 h-5 text-slate-400 hover:text-slate-600" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember"
                            class="w-4 h-4 text-primary-600 border-slate-300 rounded focus:ring-primary-500">
                        <span class="ml-2 text-sm text-slate-600">Ingat saya</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-semibold rounded-lg hover:shadow-lg hover:shadow-primary-500/30 transition-all duration-200 flex items-center justify-center">
                    <span>Masuk</span>
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-primary-200 text-sm mt-6">
            <a href="/" class="hover:text-white transition-colors">← Kembali ke Website</a>
        </p>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
            }
        }
    </script>
</body>

</html>