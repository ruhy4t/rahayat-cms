<?php
// footer-emerald.php
?>
<footer class="bg-white border-t-4 border-primary-600 pt-16 pb-8 text-slate-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-12 mb-12">
            <!-- Brand -->
            <div class="md:col-span-4">
                <div class="flex items-center space-x-3 mb-6">
                    <?php if (!empty($profile['logo'])): ?>
                        <img src="/storage/<?= e($profile['logo']) ?>" alt="Logo" class="h-14 w-auto object-contain">
                    <?php else: ?>
                        <div
                            class="w-12 h-12 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center font-bold text-xl">
                            <?= strtoupper(substr($profile['name'] ?? 'S', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <span class="font-bold text-2xl text-slate-800">
                        <?= e($profile['name'] ?? SCHOOL_NAME) ?>
                    </span>
                </div>
                <p class="text-sm leading-relaxed mb-6">
                    <?= e($profile['address'] ?? '') ?>
                </p>
                <div class="space-y-2 text-sm">
                    <p class="flex items-center"><svg class="w-4 h-4 mr-2 text-primary-600" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <?= e($profile['phone'] ?? '') ?>
                    </p>
                    <p class="flex items-center"><svg class="w-4 h-4 mr-2 text-primary-600" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <?= e($profile['email'] ?? '') ?>
                    </p>
                </div>
            </div>

            <!-- Links -->
            <div class="md:col-span-8 grid grid-cols-2 md:grid-cols-3 gap-8">
                <div>
                    <h4 class="text-slate-800 font-bold mb-6 uppercase tracking-wider text-sm flex items-center">
                        <span class="w-2 h-2 bg-primary-500 rounded-full mr-2"></span> Navigasi
                    </h4>
                    <ul class="space-y-3">
                        <?php if (!empty($footerMenus)): ?>
                            <?php foreach (array_slice($footerMenus, 0, ceil(count($footerMenus) / 2)) as $menu): ?>
                                <li><a href="<?= e($menu['url']) ?>" <?= $menu['target'] === '_blank' ? 'target="_blank"' : '' ?>
                                        class="hover:text-primary-600 transition-colors inline-block transform
                                hover:translate-x-1 duration-200">
                                        <?= e($menu['title']) ?>
                                    </a></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
                <div>
                    <h4 class="text-slate-800 font-bold mb-6 uppercase tracking-wider text-sm flex items-center">
                        <span class="w-2 h-2 bg-primary-500 rounded-full mr-2"></span> Tautan Lain
                    </h4>
                    <ul class="space-y-3">
                        <?php if (!empty($footerMenus)): ?>
                            <?php foreach (array_slice($footerMenus, ceil(count($footerMenus) / 2)) as $menu): ?>
                                <li><a href="<?= e($menu['url']) ?>" <?= $menu['target'] === '_blank' ? 'target="_blank"' : '' ?>
                                        class="hover:text-primary-600 transition-colors inline-block transform
                                hover:translate-x-1 duration-200">
                                        <?= e($menu['title']) ?>
                                    </a></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="md:text-right">
                    <h4 class="text-slate-800 font-bold mb-6 uppercase tracking-wider text-sm flex items-center md:justify-end">
                        <span class="w-2 h-2 bg-primary-500 rounded-full mr-2"></span> Ikuti Kami
                    </h4>
                    <div class="flex items-center md:justify-end space-x-3">
                        <?php if (!empty($settings['social_facebook'])): ?>
                            <a href="<?= e($settings['social_facebook']) ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-primary-600 hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($settings['social_instagram'])): ?>
                            <a href="<?= e($settings['social_instagram']) ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-pink-600 hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" /></svg>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($settings['social_twitter'])): ?>
                            <a href="<?= e($settings['social_twitter']) ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-800 hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" /></svg>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($settings['social_youtube'])): ?>
                            <a href="<?= e($settings['social_youtube']) ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-red-600 hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M19.812 5.418c.861.23 1.538.907 1.768 1.768C21.998 8.746 22 12 22 12s0 3.255-.418 4.814a2.504 2.504 0 0 1-1.768 1.768c-1.56.419-7.814.419-7.814.419s-6.255 0-7.814-.419a2.505 2.505 0 0 1-1.768-1.768C2 15.255 2 12 2 12s0-3.255.417-4.814a2.507 2.507 0 0 1 1.768-1.768C5.744 5 11.998 5 11.998 5s6.255 0 7.814.418ZM15.194 12 10 15V9l5.194 3Z" clip-rule="evenodd" /></svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="border-t border-slate-200 pt-8 mt-8 flex flex-col md:flex-row justify-between items-center text-sm text-slate-500">
            <p>
                <?php
                if (empty($settings['footer_text'])) {
                    echo '&copy; ' . date('Y') . ' ' . e($profile['name'] ?? SCHOOL_NAME) . '. Hak Cipta Dilindungi.';
                } else {
                    $text = e($settings['footer_text']);
                    echo str_replace(['{year}', '{school}'], [date('Y'), e($profile['name'] ?? SCHOOL_NAME)], $text);
                }
                ?>
            </p>
            <p class="mt-4 md:mt-0 font-medium text-primary-600 flex items-center">
                Tema Emerald Campus
            </p>
        </div>
    </div>
</footer>
