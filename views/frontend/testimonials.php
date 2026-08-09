<?php
$testimonials = $data['testimonials'] ?? [];
$relationships = $data['relationships'] ?? [];
$flash = $data['flash'] ?? null;
?>

<section class="bg-gradient-to-br from-primary-900 via-primary-800 to-primary-900 text-white">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20 text-center">
        <span class="inline-flex px-3 py-1 rounded-full bg-white/10 text-primary-100 text-sm font-semibold mb-4">
            Suara Keluarga Besar Sekolah
        </span>
        <h1 class="text-4xl lg:text-5xl font-bold">Testimoni</h1>
        <p class="mt-5 max-w-2xl mx-auto text-lg text-primary-100">
            Pengalaman orang tua, alumni, siswa, mitra, dan masyarakat bersama sekolah kami.
        </p>
    </div>
</section>

<?php if ($flash): ?>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
        <div class="p-4 rounded-xl border <?= ($flash['type'] ?? '') === 'success'
            ? 'bg-green-50 border-green-200 text-green-800'
            : 'bg-red-50 border-red-200 text-red-800' ?>" role="alert">
            <?= e($flash['message'] ?? '') ?>
        </div>
    </div>
<?php endif; ?>

<section class="py-14 lg:py-20 bg-slate-50">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
            <div>
                <span class="text-primary-600 font-semibold text-sm uppercase tracking-wider">Cerita Mereka</span>
                <h2 class="text-3xl font-bold text-slate-900 mt-2">Apa kata mereka?</h2>
            </div>
            <a href="#kirim-testimoni"
                class="inline-flex items-center justify-center px-5 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition-colors">
                Kirim Testimoni
            </a>
        </div>

        <?php if (empty($testimonials)): ?>
            <div class="py-16 text-center bg-white rounded-2xl border border-slate-200">
                <p class="text-slate-500">Belum ada testimoni yang dipublikasikan.</p>
            </div>
        <?php else: ?>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($testimonials as $item): ?>
                    <article class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <svg class="w-9 h-9 text-primary-200 mb-5" fill="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path d="M7.17 6A5.17 5.17 0 0 0 2 11.17V18h7v-7H5.07A2.17 2.17 0 0 1 7.17 9H9V6H7.17Zm10 0A5.17 5.17 0 0 0 12 11.17V18h7v-7h-3.93A2.17 2.17 0 0 1 17.17 9H19V6h-1.83Z" />
                        </svg>
                        <p class="text-slate-700 leading-relaxed whitespace-pre-line"><?= e($item['testimonial']) ?></p>
                        <footer class="flex items-center gap-3 mt-6 pt-5 border-t border-slate-100">
                            <?php if (!empty($item['photo'])): ?>
                                <img src="/storage/<?= e($item['photo']) ?>" alt="Foto <?= e($item['name']) ?>"
                                    class="w-12 h-12 rounded-full object-cover bg-slate-100" loading="lazy"
                                    decoding="async">
                            <?php else: ?>
                                <div class="w-12 h-12 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold"
                                    aria-hidden="true">
                                    <?= e(mb_strtoupper(mb_substr($item['name'], 0, 1))) ?>
                                </div>
                            <?php endif; ?>
                            <div class="min-w-0">
                                <h3 class="font-bold text-slate-900 truncate"><?= e($item['name']) ?></h3>
                                <p class="text-sm text-slate-500 truncate">
                                    <?= e($item['relationship']) ?>
                                    <?php if (!empty($item['graduation_year'])): ?>
                                        · Angkatan <?= (int) $item['graduation_year'] ?>
                                    <?php elseif (!empty($item['occupation'])): ?>
                                        · <?= e($item['occupation']) ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </footer>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="py-14 lg:py-20 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <details id="kirim-testimoni" class="group scroll-mt-24">
            <summary
                class="list-none cursor-pointer rounded-2xl border border-primary-200 bg-primary-50 p-5 sm:p-6 transition-colors hover:bg-primary-100 focus:outline-none focus-visible:ring-4 focus-visible:ring-primary-200 [&::-webkit-details-marker]:hidden">
                <span class="flex items-center justify-between gap-4">
                    <span>
                        <span class="block text-primary-600 font-semibold text-sm uppercase tracking-wider">Bagikan Pengalaman</span>
                        <span class="block text-xl sm:text-2xl font-bold text-slate-900 mt-1">Isi Testimoni</span>
                        <span class="block text-sm sm:text-base text-slate-600 mt-1">Klik untuk membuka formulir testimoni.</span>
                    </span>
                    <span
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-primary-600 text-white transition-transform group-open:rotate-180"
                        aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m6 9 6 6 6-6" />
                        </svg>
                    </span>
                </span>
            </summary>

            <div class="pt-6">
                <p class="text-slate-600 text-center mb-6">Testimoni akan ditinjau admin sebelum ditampilkan di website.</p>
                <form action="/testimoni/kirim" method="POST" enctype="multipart/form-data"
                    class="bg-slate-50 p-6 sm:p-8 rounded-2xl border border-slate-200 space-y-5">
            <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">
            <div class="absolute -left-[10000px]" aria-hidden="true">
                <label for="website">Website</label>
                <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label for="testimonial-name" class="block text-sm font-semibold text-slate-700 mb-1.5">Nama *</label>
                    <input id="testimonial-name" type="text" name="name" maxlength="100" required autocomplete="name"
                        class="w-full px-4 py-2.5 border-slate-300 rounded-xl focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="testimonial-relationship"
                        class="block text-sm font-semibold text-slate-700 mb-1.5">Hubungan dengan sekolah *</label>
                    <select id="testimonial-relationship" name="relationship" required
                        class="w-full px-4 py-2.5 border-slate-300 rounded-xl focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Pilih kategori</option>
                        <?php foreach ($relationships as $relationship): ?>
                            <option value="<?= e($relationship) ?>"><?= e($relationship) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label for="testimonial-year" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Tahun lulus <span class="font-normal text-slate-400">(khusus alumni)</span>
                    </label>
                    <input id="testimonial-year" type="number" name="graduation_year" min="1950"
                        max="<?= (int) date('Y') + 1 ?>"
                        class="w-full px-4 py-2.5 border-slate-300 rounded-xl focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="testimonial-occupation" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Pekerjaan/instansi <span class="font-normal text-slate-400">(opsional)</span>
                    </label>
                    <input id="testimonial-occupation" type="text" name="occupation" maxlength="120"
                        class="w-full px-4 py-2.5 border-slate-300 rounded-xl focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>

            <div>
                <label for="testimonial-content" class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Testimoni * <span class="font-normal text-slate-400">(20–1.200 karakter)</span>
                </label>
                <textarea id="testimonial-content" name="testimonial" rows="6" minlength="20" maxlength="1200" required
                    class="w-full px-4 py-2.5 border-slate-300 rounded-xl focus:ring-primary-500 focus:border-primary-500 resize-y"></textarea>
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label for="testimonial-photo" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Foto/avatar <span class="font-normal text-slate-400">(opsional)</span>
                    </label>
                    <input id="testimonial-photo" type="file" name="photo"
                        accept="image/jpeg,image/png,image/webp"
                        class="w-full text-sm text-slate-600 file:mr-3 file:px-3 file:py-2 file:border-0 file:rounded-lg file:bg-primary-100 file:text-primary-700 file:font-semibold">
                    <p class="text-xs text-slate-500 mt-1.5">JPG, PNG, atau WebP. Maksimal 1 MB.</p>
                </div>
                <div>
                    <label for="testimonial-contact" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        WhatsApp/email <span class="font-normal text-slate-400">(opsional)</span>
                    </label>
                    <input id="testimonial-contact" type="text" name="contact" maxlength="120"
                        class="w-full px-4 py-2.5 border-slate-300 rounded-xl focus:ring-primary-500 focus:border-primary-500">
                    <p class="text-xs text-slate-500 mt-1.5">Hanya untuk verifikasi dan tidak ditampilkan.</p>
                </div>
            </div>

            <label class="flex items-start gap-3 p-4 bg-white rounded-xl border border-slate-200">
                <input type="checkbox" name="consent" value="1" required
                    class="mt-1 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                <span class="text-sm text-slate-600">
                    Saya menyetujui nama, kategori, foto (jika ada), dan isi testimoni dipublikasikan setelah ditinjau oleh sekolah.
                </span>
            </label>

                    <?= Security::publicCaptchaInput('testimonial') ?>

                    <button type="submit"
                        class="w-full sm:w-auto px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition-colors">
                        Kirim untuk Ditinjau
                    </button>
                </form>
            </div>
        </details>
    </div>
</section>

<script>
(() => {
    const photo = document.getElementById('testimonial-photo');
    photo?.addEventListener('change', () => {
        if (photo.files?.[0] && photo.files[0].size > 1048576) {
            alert('Ukuran foto testimoni maksimal 1 MB.');
            photo.value = '';
        }
    });
})();
</script>
