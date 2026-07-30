<?php
$schoolName = $profile['name'] ?? SCHOOL_NAME;
$footerColumns = array_chunk($footerMenus ?? [], max(1, (int) ceil(count($footerMenus ?? []) / 2)));
$socialLinks = [
    ['key' => 'social_facebook', 'label' => 'Facebook', 'mark' => 'f'],
    ['key' => 'social_instagram', 'label' => 'Instagram', 'mark' => 'IG'],
    ['key' => 'social_twitter', 'label' => 'X', 'mark' => 'X'],
    ['key' => 'social_youtube', 'label' => 'YouTube', 'mark' => 'YT'],
];
?>
<footer class="cendekia-footer bg-slate-950 text-slate-400">
    <div class="h-1 bg-gradient-to-r from-primary-600 via-purple-600 to-cyan-400"></div>
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-8">
        <div class="grid md:grid-cols-12 gap-10 lg:gap-14">
            <div class="md:col-span-5">
                <div class="flex items-center gap-3">
                    <?php if (!empty($profile['logo'])): ?>
                        <span class="w-12 h-12 rounded-xl bg-white p-1.5">
                            <img src="/storage/<?= e($profile['logo']) ?>" alt="" class="w-full h-full object-contain">
                        </span>
                    <?php else: ?>
                        <span class="w-12 h-12 rounded-xl bg-cyan-400 text-slate-950 flex items-center justify-center font-extrabold text-xl">
                            <?= e(strtoupper(substr($schoolName, 0, 1))) ?>
                        </span>
                    <?php endif; ?>
                    <div>
                        <strong class="block text-lg text-white"><?= e($schoolName) ?></strong>
                        <span class="text-[10px] uppercase tracking-[0.18em] font-bold text-cyan-300">Sekolah Menengah Pertama</span>
                    </div>
                </div>
                <p class="mt-5 max-w-md text-sm leading-7"><?= e($profile['tagline'] ?? 'Bersama membentuk generasi yang berkarakter, cakap, dan siap menghadapi masa depan.') ?></p>
                <div class="mt-6 space-y-2 text-sm">
                    <?php if (!empty($profile['address'])): ?><p><?= e($profile['address']) ?></p><?php endif; ?>
                    <?php if (!empty($profile['phone'])): ?><p>Telp. <?= e($profile['phone']) ?></p><?php endif; ?>
                    <?php if (!empty($profile['email'])): ?><p><?= e($profile['email']) ?></p><?php endif; ?>
                </div>
            </div>

            <div class="md:col-span-4 grid grid-cols-2 gap-8">
                <?php foreach ($footerColumns as $columnIndex => $column): ?>
                    <div>
                        <h2 class="text-xs font-extrabold uppercase tracking-[0.18em] text-white"><?= $columnIndex === 0 ? 'Navigasi' : 'Tautan' ?></h2>
                        <ul class="mt-5 space-y-3 text-sm">
                            <?php foreach ($column as $menu): ?>
                                <?php $target = ($menu['target'] ?? '_self') === '_blank' ? '_blank' : '_self'; ?>
                                <li>
                                    <a href="<?= e($menu['url']) ?>" target="<?= $target ?>"
                                        <?= $target === '_blank' ? 'rel="noopener noreferrer"' : '' ?>
                                        class="hover:text-cyan-300 transition-colors"><?= e($menu['title']) ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="md:col-span-3">
                <h2 class="text-xs font-extrabold uppercase tracking-[0.18em] text-white">Media sosial</h2>
                <p class="mt-5 text-sm leading-6">Ikuti akun resmi sekolah untuk mendapatkan informasi terbaru.</p>
                <div class="mt-5 flex flex-wrap gap-2">
                    <?php foreach ($socialLinks as $social): ?>
                        <?php if (!empty($settings[$social['key']])): ?>
                            <a href="<?= e($settings[$social['key']]) ?>" target="_blank" rel="noopener noreferrer"
                                aria-label="<?= $social['label'] ?>"
                                class="w-10 h-10 rounded-xl border border-white/10 bg-white/5 text-slate-300 flex items-center justify-center font-bold hover:bg-cyan-400 hover:text-slate-950 hover:border-cyan-400 transition-colors">
                                <?= $social['mark'] ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="mt-12 pt-7 border-t border-white/10 flex flex-col sm:flex-row gap-3 justify-between text-xs">
            <p>
                <?php
                if (empty($settings['footer_text'])) {
                    echo '&copy; ' . date('Y') . ' ' . e($schoolName) . '. Hak Cipta Dilindungi.';
                } else {
                    $footerText = e($settings['footer_text']);
                    echo str_replace(['{year}', '{school}'], [date('Y'), e($schoolName)], $footerText);
                }
                ?>
            </p>
            <p class="font-bold text-cyan-300">Cendekia SMP</p>
        </div>
    </div>
</footer>
