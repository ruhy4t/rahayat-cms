<?php $pageHeading=PublicTheme::text($settings,'testimonials'); include __DIR__.'/banner.php'; ?>
<section class="next-shell next-community-layout"><div class="next-community-stories">
<?php foreach ($data['testimonials'] ?? [] as $testimonial): ?><figure class="next-quote"><blockquote><?= e($testimonial['testimonial']) ?></blockquote><figcaption><?php if (!empty($testimonial['photo'])): ?><img src="/storage/<?= e($testimonial['photo']) ?>" alt="" loading="lazy"><?php endif; ?><span><strong><?= e($testimonial['name']) ?></strong><small><?= e($testimonial['relationship']) ?></small></span></figcaption></figure><?php endforeach; ?>
<?php if (empty($data['testimonials'])): ?><p class="next-empty"><?= e(PublicTheme::text($settings,'empty')) ?></p><?php endif; ?></div>
<details class="next-submission" id="kirim-testimoni"><summary><?= e(PublicTheme::text($settings,'submit')) ?></summary>
<?php if (!empty($data['flash'])): ?><p role="status"><?= e($data['flash']['message']) ?></p><?php endif; ?>
<form action="/testimoni/kirim" method="post" enctype="multipart/form-data" class="next-form">
<?= Security::csrfInput() ?><div class="next-honeypot" aria-hidden="true"><label>Website<input name="website" tabindex="-1" autocomplete="off"></label></div>
<label><?= e(PublicTheme::text($settings,'name')) ?><input name="name" required maxlength="100" autocomplete="name"></label>
<label><?= e(PublicTheme::text($settings,'relationship')) ?><select name="relationship" required><option value="">—</option><?php foreach ($data['relationships'] ?? [] as $relationship): ?><option><?= e($relationship) ?></option><?php endforeach; ?></select></label>
<label><?= e(PublicTheme::text($settings,'graduation_year')) ?><input type="number" name="graduation_year" min="1950" max="<?= (int)date('Y')+1 ?>"></label>
<label><?= e(PublicTheme::text($settings,'occupation')) ?><input name="occupation" maxlength="120"></label>
<label><?= e(PublicTheme::text($settings,'testimonial')) ?><textarea name="testimonial" rows="6" minlength="20" maxlength="1200" required></textarea></label>
<label><?= e(PublicTheme::text($settings,'photo')) ?><input type="file" name="photo" accept="image/jpeg,image/png,image/webp"></label>
<label><?= e(PublicTheme::text($settings,'contact_private')) ?><input name="contact" maxlength="120"></label>
<label class="next-consent"><input type="checkbox" name="consent" value="1" required><span><?= e(PublicTheme::text($settings,'consent_testimonial')) ?></span></label>
<?= Security::publicCaptchaInput('testimonial') ?><button class="next-button next-button--primary" type="submit"><?= e(PublicTheme::text($settings,'submit')) ?></button>
</form></details></section>
