<?php
$schoolName = $profile['name'] ?? SCHOOL_NAME;
?>
<header class="cendekia-header sticky top-0 z-50">
    <div class="hidden lg:block bg-slate-950 text-slate-300">
        <div class="max-w-[1440px] mx-auto px-6 lg:px-8 h-9 flex items-center justify-between text-xs">
            <p class="truncate"><?= e($profile['tagline'] ?? 'Tumbuh, belajar, dan berprestasi bersama') ?></p>
            <div class="flex items-center gap-5">
                <?php if (!empty($profile['phone'])): ?>
                    <span>Telp. <?= e($profile['phone']) ?></span>
                <?php endif; ?>
                <?php if (!empty($profile['email'])): ?>
                    <span><?= e($profile['email']) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <nav class="bg-white/95 backdrop-blur-xl border-b border-slate-200/80" aria-label="Navigasi utama">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="h-[72px] flex items-center justify-between gap-6">
                <a href="/" class="flex items-center gap-3 min-w-0" aria-label="Beranda <?= e($schoolName) ?>">
                    <?php if (!empty($profile['logo'])): ?>
                        <span class="w-11 h-11 rounded-xl bg-white border border-slate-200 p-1.5 shadow-sm shrink-0">
                            <img src="/storage/<?= e($profile['logo']) ?>" alt="" class="w-full h-full object-contain">
                        </span>
                    <?php else: ?>
                        <span class="w-11 h-11 rounded-xl bg-gradient-to-br from-primary-600 via-purple-600 to-cyan-400 text-white flex items-center justify-center font-extrabold text-lg shadow-lg shadow-indigo-700/20 shrink-0">
                            <?= e(strtoupper(substr($schoolName, 0, 1))) ?>
                        </span>
                    <?php endif; ?>
                    <span class="min-w-0">
                        <strong class="block text-sm sm:text-base text-slate-900 leading-tight truncate"><?= e($schoolName) ?></strong>
                        <span class="hidden sm:block mt-1 text-[10px] font-bold tracking-[0.18em] uppercase text-primary-700">Sekolah Menengah Pertama</span>
                    </span>
                </a>

                <div class="hidden lg:flex items-center gap-1">
                    <?php foreach ($headerMenus ?? [] as $menu): ?>
                        <?php
                        $menuTarget = ($menu['target'] ?? '_self') === '_blank' ? '_blank' : '_self';
                        $menuRel = $menuTarget === '_blank' ? 'rel="noopener noreferrer"' : '';
                        ?>
                        <?php if (empty($menu['children'])): ?>
                            <a href="<?= e($menu['url']) ?>" target="<?= $menuTarget ?>" <?= $menuRel ?>
                                class="px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:text-primary-700 hover:bg-cyan-50 transition-colors">
                                <?= e($menu['title']) ?>
                            </a>
                        <?php else: ?>
                            <div class="relative group">
                                <button type="button"
                                    class="flex items-center gap-1 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 group-hover:text-primary-700 group-hover:bg-cyan-50 transition-colors"
                                    aria-haspopup="true">
                                    <?= e($menu['title']) ?>
                                    <svg class="w-4 h-4 group-hover:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div class="absolute right-0 top-full pt-2 w-56 opacity-0 invisible translate-y-1 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 group-focus-within:opacity-100 group-focus-within:visible group-focus-within:translate-y-0 transition-all">
                                    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-900/10 p-2">
                                        <?php foreach ($menu['children'] as $child): ?>
                                            <?php
                                            $childTarget = ($child['target'] ?? '_self') === '_blank' ? '_blank' : '_self';
                                            $childRel = $childTarget === '_blank' ? 'rel="noopener noreferrer"' : '';
                                            ?>
                                            <a href="<?= e($child['url']) ?>" target="<?= $childTarget ?>" <?= $childRel ?>
                                                class="block px-4 py-2.5 rounded-xl text-sm text-slate-600 hover:text-primary-700 hover:bg-cyan-50 transition-colors">
                                                <?= e($child['title']) ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <button type="button" onclick="toggleMobileMenu()"
                    class="lg:hidden w-11 h-11 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center hover:bg-cyan-50 hover:text-primary-700 transition-colors"
                    aria-label="Buka menu navigasi" aria-controls="mobileMenu" aria-expanded="false">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <div id="mobileMenu" class="hidden lg:hidden bg-white border-t border-slate-200 max-h-[70vh] overflow-y-auto">
            <div class="px-4 py-4 space-y-1">
                <?php foreach ($headerMenus ?? [] as $menu): ?>
                    <?php $mobileTarget = ($menu['target'] ?? '_self') === '_blank' ? '_blank' : '_self'; ?>
                    <?php if (empty($menu['children'])): ?>
                        <a href="<?= e($menu['url']) ?>" target="<?= $mobileTarget ?>"
                            <?= $mobileTarget === '_blank' ? 'rel="noopener noreferrer"' : '' ?>
                            class="block px-4 py-3 rounded-xl font-semibold text-slate-700 hover:bg-cyan-50 hover:text-primary-700">
                            <?= e($menu['title']) ?>
                        </a>
                    <?php else: ?>
                        <div class="px-4 pt-3 pb-1 text-xs font-bold tracking-wider uppercase text-slate-400"><?= e($menu['title']) ?></div>
                        <?php foreach ($menu['children'] as $child): ?>
                            <?php $mobileChildTarget = ($child['target'] ?? '_self') === '_blank' ? '_blank' : '_self'; ?>
                            <a href="<?= e($child['url']) ?>" target="<?= $mobileChildTarget ?>"
                                <?= $mobileChildTarget === '_blank' ? 'rel="noopener noreferrer"' : '' ?>
                                class="block ml-3 px-4 py-2.5 rounded-xl text-sm text-slate-600 hover:bg-cyan-50 hover:text-primary-700">
                                <?= e($child['title']) ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </nav>
</header>
