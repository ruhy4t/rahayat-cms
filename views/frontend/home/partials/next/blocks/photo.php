<figure class="composition-photo<?= $heroImage === '' ? ' composition-photo--empty' : '' ?>">
    <?php if ($heroImage !== ''): ?><img src="<?= e($heroImage) ?>" alt="<?= e($heroTitle) ?>" loading="eager" fetchpriority="high" decoding="async"><?php else: ?><span aria-hidden="true" class="composition-emblem"><?= e(mb_substr($heroTitle,0,1)) ?></span><?php endif; ?>
</figure>
