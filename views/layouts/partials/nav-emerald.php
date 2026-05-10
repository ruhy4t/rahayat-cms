<?php
// nav-emerald.php
?>
<div class="bg-primary-700 text-white text-sm py-2 px-4 shadow-sm z-50 relative">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <div class="flex items-center gap-4">
            <span class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                <?= e($profile['phone'] ?? SCHOOL_PHONE) ?>
            </span>
            <span class="hidden sm:flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <?= e($profile['email'] ?? SCHOOL_EMAIL) ?>
            </span>
        </div>
        <div class="flex gap-4">
        </div>
    </div>
</div>
<nav class="bg-white shadow-md sticky top-0 z-40 border-b border-primary-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">
            <a href="/" class="flex items-center space-x-3">
                <?php if (!empty($profile['logo'])): ?>
                    <img src="/storage/<?= e($profile['logo']) ?>" alt="Logo" class="h-12 w-12 object-contain">
                <?php else: ?>
                    <div
                        class="w-12 h-12 bg-primary-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                        <?= strtoupper(substr($profile['name'] ?? 'S', 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <span class="font-bold text-2xl text-slate-800 tracking-tight">
                    <?= e($profile['name'] ?? SCHOOL_NAME) ?>
                </span>
            </a>

            <div class="hidden md:flex items-center space-x-2">
                <?php if (!empty($headerMenus)): ?>
                    <?php foreach ($headerMenus as $menu): ?>
                        <?php if (empty($menu['children'])): ?>
                            <a href="<?= e($menu['url']) ?>" <?= $menu['target'] === '_blank' ? 'target="_blank"' : '' ?>
                                class="px-3 py-2 text-slate-700 hover:text-primary-600 font-medium transition-colors">
                                <?= e($menu['title']) ?>
                            </a>
                        <?php else: ?>
                            <div class="relative group">
                                <button
                                    class="flex items-center space-x-1 px-3 py-2 text-slate-700 hover:text-primary-600 font-medium transition-colors">
                                    <span>
                                        <?= e($menu['title']) ?>
                                    </span>
                                    <svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-180" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div
                                    class="absolute top-full left-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                    <div class="py-2">
                                        <?php foreach ($menu['children'] as $child): ?>
                                            <a href="<?= e($child['url']) ?>" <?= $child['target'] === '_blank' ? 'target="_blank"' : '' ?>
                                                class="block px-4 py-2 text-sm text-slate-600 hover:bg-primary-50
                                hover:text-primary-600">
                                                <?= e($child['title']) ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <button onclick="toggleMobileMenu()" class="md:hidden p-2 text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <div id="mobileMenu" class="hidden md:hidden bg-white border-t border-slate-100 absolute w-full shadow-lg z-50">
        <div class="px-4 py-2 space-y-1">
            <?php if (!empty($headerMenus)):
                foreach ($headerMenus as $menu): ?>
                    <?php if (empty($menu['children'])): ?>
                        <a href="<?= e($menu['url']) ?>" <?= $menu['target'] === '_blank' ? 'target="_blank"' : '' ?>
                            class="block px-3 py-2 text-slate-700 hover:bg-primary-50 hover:text-primary-600 rounded-lg">
                            <?= e($menu['title']) ?>
                        </a>
                    <?php else: ?>
                        <div class="space-y-1">
                            <div class="px-3 py-2 text-slate-800 font-semibold">
                                <?= e($menu['title']) ?>
                            </div>
                            <div class="pl-4">
                                <?php foreach ($menu['children'] as $child): ?>
                                    <a href="<?= e($child['url']) ?>" <?= $child['target'] === '_blank' ? 'target="_blank"' : '' ?>
                                        class="block px-3 py-2 text-sm text-slate-600 hover:bg-primary-50 hover:text-primary-600 rounded-lg">
                                        <?= e($child['title']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; endif; ?>
        </div>
    </div>
</nav>
