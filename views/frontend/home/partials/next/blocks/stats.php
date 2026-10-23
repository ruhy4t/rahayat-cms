<section class="next-stats" aria-label="Statistik sekolah">
    <div class="next-shell next-stats__grid">
        <?php foreach ([
            [$profile['total_students'] ?? null, $copy('home_students_label', 'Murid Aktif'), '+'],
            [$profile['total_teachers'] ?? null, $copy('home_teachers_label', 'Guru Profesional'), '+'],
            [$profile['accreditation'] ?? null, $copy('home_accreditation_label', 'Akreditasi'), ''],
            [$profile['graduation_rate'] ?? null, $copy('home_graduation_label', 'Kelulusan'), '%'],
        ] as [$statValue, $statLabel, $statUnit]): ?>
            <?php if ($statValue !== null && $statValue !== ''): ?><div class="next-stat"><strong><?= e($statUnit !== '' ? rtrim((string)$statValue, "+% \t\n\r") : $statValue) ?><?php if ($statUnit !== ''): ?><small class="next-stat-unit"><?= e($statUnit) ?></small><?php endif; ?></strong><span><?= e($statLabel) ?></span></div><?php endif; ?>
        <?php endforeach; ?>
    </div>
</section>
