<?php
// nav-crimson.php
?>
<nav class="bg-slate-900 border-b-4 border-primary-600 sticky top-0 z-50 shadow-xl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">
            <!-- Logo -->
            <a href="/" class="flex items-center space-x-4 group">
                <?php if (!empty($profile['logo'])): ?>
                    <div
                        class="h-12 w-12 bg-white/10 backdrop-blur-md rounded-xl p-1.5 flex items-center justify-center border border-white/20 group-hover:bg-white/20 transition-all">
                        <img src="/storage/<?= e($profile['logo']) ?>" alt="Logo" class="max-h-9 max-w-9 object-contain">
                    </div>
                <?php else: ?>
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center text-white font-black text-xl shadow-lg border border-primary-400/30">
                        <?= strtoupper(substr($profile['name'] ?? 'S', 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <div class="flex flex-col">
                    <span
                        class="font-black text-2xl text-white tracking-widest uppercase group-hover:text-primary-400 transition-colors leading-none">
                        <?= e($profile['name'] ?? SCHOOL_NAME) ?>
                    </span>
                    <span class="text-xs text-primary-300 font-bold uppercase tracking-widest mt-1">Website Resmi</span>
                </div>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-1 border-l border-slate-700/50 pl-6 ml-4">
                <?php if (!empty($headerMenus)): ?>
                    <?php foreach ($headerMenus as $menu): ?>
                        <?php if (empty($menu['children'])): ?>
                            <a href="<?= e($menu['url']) ?>" <?= $menu['target'] === '_blank' ? 'target="_blank"' : '' ?> class="px-4 py-2 bg-transparent text-slate-300 hover:text-white hover:bg-slate-800 rounded-lg
                    font-bold transition-all text-sm uppercase tracking-wide">
                                <?= e($menu['title']) ?>
                            </a>
                        <?php else: ?>
                            <div class="relative group">
                                <button
                                    class="flex items-center space-x-1 px-4 py-2 bg-transparent text-slate-300 hover:text-white hover:bg-slate-800 rounded-lg font-bold transition-all text-sm uppercase tracking-wide">
                                    <span>
                                        <?= e($menu['title']) ?>
                                    </span>
                                    <svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-180" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div
                                    class="absolute top-full right-0 mt-3 w-56 bg-white rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 overflow-hidden border border-slate-100 transform origin-top -translate-y-2 group-hover:translate-y-0">
                                    <div class="p-2 space-y-1">
                                        <?php foreach ($menu['children'] as $child): ?>
                                            <a href="<?= e($child['url']) ?>" <?= $child['target'] === '_blank' ? 'target="_blank"' : '' ?>
                                                class="block px-4 py-2 text-sm text-slate-700 hover:bg-primary-50 hover:text-primary-700
                                rounded-lg font-semibold transition-colors">
                                                <?= e($child['title']) ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="pl-4 ml-2">
                </div>
            </div>

            <!-- Mobile Toggle -->
            <button onclick="toggleMobileMenu()"
                class="md:hidden p-2 text-white hover:text-primary-400 transition-colors">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu"
        class="hidden md:hidden bg-slate-900 border-t border-slate-800 absolute w-full shadow-2xl z-50">
        <div class="px-4 py-4 space-y-2">
            <?php if (!empty($headerMenus)):
                foreach ($headerMenus as $menu): ?>
                    <?php if (empty($menu['children'])): ?>
                        <a href="<?= e($menu['url']) ?>" <?= $menu['target'] === '_blank' ? 'target="_blank"' : '' ?>
                            class="block px-4 py-3 text-slate-300 hover:text-white hover:bg-slate-800 rounded-xl font-bold uppercase tracking-wide text-sm">
                            <?= e($menu['title']) ?>
                        </a>
                    <?php else: ?>
                        <div class="bg-slate-800/50 rounded-xl p-2 space-y-1">
                            <div class="px-3 py-2 text-primary-400 font-bold uppercase text-xs tracking-wider">
                                <?= e($menu['title']) ?>
                            </div>
                            <div class="space-y-1">
                                <?php foreach ($menu['children'] as $child): ?>
                                    <a href="<?= e($child['url']) ?>" <?= $child['target'] === '_blank' ? 'target="_blank"' : '' ?>
                                        class="block px-4 py-2 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg text-sm font-semibold">
                                        <?= e($child['title']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; endif; ?>
            <div class="pt-4 mt-4 border-t border-slate-800">
            </div>
        </div>
    </div>
</nav>
