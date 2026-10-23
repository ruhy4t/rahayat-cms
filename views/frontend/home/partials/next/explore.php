<div class="home-explore" data-home-layout="explore">
    <section class="explore-introduction next-shell"><div class="explore-welcome"><?php $renderHome('identity'); ?></div><div class="explore-visual"><?php $renderHome('photo'); ?><div class="explore-caption"><?= e($profile['tagline'] ?? '') ?></div></div></section>
    <div class="explore-statistics"><?php $renderHome('stats'); ?></div>
    <div class="explore-programs"><?php $renderHome('programs'); ?></div>
    <?php $renderHome('principal'); $renderHome('featured'); $renderHome('news'); $renderHome('calendar'); $renderHome('gallery'); $renderHome('testimonials'); $renderHome('cta'); ?>
</div>
