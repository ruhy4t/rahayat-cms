<?php
// footer-crimson.php
?>
<footer class="bg-gradient-to-br from-slate-900 via-slate-900 to-slate-800 text-slate-300 relative overflow-hidden">
    <!-- Decorative background element -->
    <div
        class="absolute top-0 right-0 w-[500px] h-[500px] rounded-full bg-primary-600/10 blur-[100px] pointer-events-none -mr-48 -mt-48">
    </div>
    <div
        class="absolute bottom-0 left-0 w-[500px] h-[500px] rounded-full bg-primary-900/40 blur-[100px] pointer-events-none -ml-48 -mb-48">
    </div>

    <!-- Top accent stripe -->
    <div class="h-1.5 bg-gradient-to-r from-primary-400 via-primary-600 to-primary-800"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-12">

            <div class="md:col-span-5 text-center md:text-left">
                <a href="/" class="inline-flex items-center space-x-3 group mb-8">
                    <?php if (!empty($profile['logo'])): ?>
                        <div
                            class="h-12 w-12 bg-white/10 backdrop-blur-md rounded-xl p-1.5 flex items-center justify-center border border-white/20">
                            <img src="/storage/<?= e($profile['logo']) ?>" alt="Logo"
                                class="max-h-9 max-w-9 object-contain">
                        </div>
                    <?php endif; ?>
                    <h3 class="font-black text-2xl md:text-3xl text-white tracking-widest uppercase drop-shadow-sm">
                        <?= e($profile['name'] ?? SCHOOL_NAME) ?>
                    </h3>
                </a>

                <p class="text-slate-400 leading-relaxed text-sm max-w-md mx-auto md:mx-0 mb-8 font-medium">
                    <?= e($profile['address'] ?? '') ?>
                </p>
                <div class="flex flex-col space-y-4 max-w-xs mx-auto md:mx-0">
                    <div class="flex items-center space-x-4 bg-slate-800/50 p-3 rounded-xl border border-slate-700/50">
                        <div
                            class="w-10 h-10 rounded-lg bg-primary-600/20 flex items-center justify-center text-primary-400 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                            </svg>
                        </div>
                        <span class="text-white font-semibold tracking-wide">
                            <?= e($profile['phone'] ?? '') ?>
                        </span>
                    </div>
                    <div class="flex items-center space-x-4 bg-slate-800/50 p-3 rounded-xl border border-slate-700/50">
                        <div
                            class="w-10 h-10 rounded-lg bg-primary-600/20 flex items-center justify-center text-primary-400 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="text-white font-semibold tracking-wide text-sm">
                            <?= e($profile['email'] ?? '') ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="md:col-span-7">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div>
                        <h4
                            class="text-white font-black mb-6 text-sm tracking-widest uppercase border-b-2 border-primary-600 inline-block pb-2">
                            Informasi Utama</h4>
                        <ul class="space-y-4">
                            <?php if (!empty($footerMenus)): ?>
                                <?php foreach (array_slice($footerMenus, 0, ceil(count($footerMenus) / 2)) as $menu): ?>
                                    <li><a href="<?= e($menu['url']) ?>" <?= $menu['target'] === '_blank' ? 'target="_blank"' : '' ?> class="flex items-center text-slate-400 hover:text-white transition-colors group
                                    text-sm font-medium">
                                            <svg class="w-4 h-4 mr-3 text-primary-500 group-hover:translate-x-1 transition-transform"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                            <?= e($menu['title']) ?>
                                        </a></li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div>
                        <h4
                            class="text-white font-black mb-6 text-sm tracking-widest uppercase border-b-2 border-primary-600 inline-block pb-2">
                            Tautan Lainnya</h4>
                        <ul class="space-y-4">
                            <?php if (!empty($footerMenus)): ?>
                                <?php foreach (array_slice($footerMenus, ceil(count($footerMenus) / 2)) as $menu): ?>
                                    <li><a href="<?= e($menu['url']) ?>" <?= $menu['target'] === '_blank' ? 'target="_blank"' : '' ?> class="flex items-center text-slate-400 hover:text-white transition-colors group
                                    text-sm font-medium">
                                            <svg class="w-4 h-4 mr-3 text-primary-500 group-hover:translate-x-1 transition-transform"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                            <?= e($menu['title']) ?>
                                        </a></li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="p-6 bg-slate-800/50 rounded-2xl border border-slate-700 backdrop-blur-sm text-center lg:text-right">
                        <h4
                            class="text-white font-black mb-6 text-sm tracking-widest uppercase border-b-2 border-primary-600 inline-block pb-2">
                            Ikuti Kami</h4>
                        <div class="flex items-center justify-center lg:justify-end gap-3">
                            <?php if (!empty($settings['social_facebook'])): ?>
                                <a href="<?= e($settings['social_facebook']) ?>" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-xl border border-slate-700/50 bg-slate-900/60 flex items-center justify-center text-slate-400 hover:bg-primary-600 hover:text-white hover:border-primary-500 transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($settings['social_instagram'])): ?>
                                <a href="<?= e($settings['social_instagram']) ?>" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-xl border border-slate-700/50 bg-slate-900/60 flex items-center justify-center text-slate-400 hover:bg-pink-600 hover:text-white hover:border-pink-500 transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" /></svg>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($settings['social_twitter'])): ?>
                                <a href="<?= e($settings['social_twitter']) ?>" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-xl border border-slate-700/50 bg-slate-900/60 flex items-center justify-center text-slate-400 hover:bg-slate-700 hover:text-white hover:border-slate-600 transition-all shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" /></svg>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($settings['social_youtube'])): ?>
                                <a href="<?= e($settings['social_youtube']) ?>" target="_blank" rel="noopener noreferrer" class="w-10 h-10 rounded-xl border border-slate-700/50 bg-slate-900/60 flex items-center justify-center text-slate-400 hover:bg-red-600 hover:text-white hover:border-red-500 transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M19.812 5.418c.861.23 1.538.907 1.768 1.768C21.998 8.746 22 12 22 12s0 3.255-.418 4.814a2.504 2.504 0 0 1-1.768 1.768c-1.56.419-7.814.419-7.814.419s-6.255 0-7.814-.419a2.505 2.505 0 0 1-1.768-1.768C2 15.255 2 12 2 12s0-3.255.417-4.814a2.507 2.507 0 0 1 1.768-1.768C5.744 5 11.998 5 11.998 5s6.255 0 7.814.418ZM15.194 12 10 15V9l5.194 3Z" clip-rule="evenodd" /></svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="mt-16 pt-8 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center text-xs font-semibold tracking-wider text-slate-500 uppercase">
            <p class="mb-4 md:mb-0">
                <?php
                if (empty($settings['footer_text'])) {
                    echo '&copy; ' . date('Y') . ' <span class="text-slate-300">' . e($profile['name'] ?? SCHOOL_NAME) . '</span>. ALL RIGHTS RESERVED.';
                } else {
                    $text = e($settings['footer_text']);
                    echo str_replace(['{year}', '{school}'], [date('Y'), '<span class="text-slate-300">' . e($profile['name'] ?? SCHOOL_NAME) . '</span>'], $text);
                }
                ?>
            </p>
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-primary-600"></span>
                <span class="text-primary-500">Theme: Crimson Bold</span>
            </div>
        </div>
        <div class="mt-4 text-center md:text-left">
            <?php
            $appDisclaimerClass = 'text-xs normal-case tracking-normal font-normal text-slate-500';
            $appDisclaimerLabelClass = 'font-semibold text-slate-300';
            $appDisclaimerHighlightClass = 'font-semibold text-primary-400';
            include __DIR__ . '/app-disclaimer.php';
            ?>
        </div>
    </div>
</footer>
