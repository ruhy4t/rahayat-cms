<?php if (!empty($facilities) || !empty($ekskul)): ?>
<section class="next-section"><div class="next-shell"><div class="next-heading"><div><p class="next-kicker"><?= e($copy('home_extracurricular_eyebrow', 'Pengembangan Diri')) ?></p><h2><?= e($copy('home_extracurricular_title', 'Program & Fasilitas')) ?></h2></div></div><div class="next-program-grid">
<?php foreach (array_slice($facilities, 0, 4) as $program): ?><article class="next-program"><div><?php if (!empty($program['image'])): ?><img src="/storage/<?= e($program['image']) ?>" alt="" loading="lazy"><?php endif; ?></div><h3><a href="/profil"><?= e($program['name']) ?></a></h3><p><?= e($program['description'] ?? '') ?></p></article><?php endforeach; ?>
<?php foreach (array_slice($ekskul, 0, 4) as $program): ?><article class="next-program"><div><?php if (!empty($program['image'])): ?><img src="/storage/<?= e($program['image']) ?>" alt="" loading="lazy"><?php endif; ?></div><h3><a href="/ekstrakurikuler/<?= (int)$program['id'] ?>"><?= e($program['name']) ?></a></h3><p><?= e($program['description'] ?? '') ?></p></article><?php endforeach; ?>
</div></div></section>
<?php endif; ?>
