<aside class="composition-agenda">
    <div class="composition-panel-heading"><h2><?= e($copy('home_agenda_title',PublicTheme::text($settings,'agenda'))) ?></h2><a href="/agenda"><?= e(PublicTheme::text($settings,'all')) ?></a></div>
    <?php foreach ($upcomingEvents ?? [] as $event): ?><a class="composition-agenda-item" href="/agenda/<?= (int)$event['id'] ?>"><time datetime="<?= e($event['start_date']) ?>"><strong><?= e(date('d',strtotime($event['start_date']))) ?></strong><span><?= e(substr(AcademicCalendar::MONTHS[(int)date('n',strtotime($event['start_date']))],0,3)) ?></span></time><span><strong><?= e($event['title']) ?></strong><small><?= e($event['location'] ?? '') ?></small></span></a><?php endforeach; ?>
    <?php if (empty($upcomingEvents)): ?><p><?= e(PublicTheme::text($settings,'empty')) ?></p><?php endif; ?>
</aside>
