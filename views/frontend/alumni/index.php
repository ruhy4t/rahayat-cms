<?php
$items = $data['result']['data'] ?? [];
$result = $data['result'] ?? [];
$filters = $data['filters'] ?? [];
$query = $data['query'] ?? [];
$flash = $data['flash'] ?? null;
$queryString = $_GET;
$continuationOptions = $data['continuationOptions'] ?? [];
$employmentStatuses = $data['employmentStatuses'] ?? [];
?>

<section class="bg-gradient-to-br from-primary-900 via-primary-800 to-primary-900 text-white">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20 text-center">
        <span class="text-primary-200 font-semibold uppercase tracking-wider text-sm">Jejaring Lulusan</span>
        <h1 class="text-4xl lg:text-5xl font-bold mt-3">Direktori Alumni</h1>
        <p class="text-primary-100 text-lg mt-5 max-w-2xl mx-auto">Temukan alumni berdasarkan nama, angkatan, kota, atau bidang pekerjaan.</p>
    </div>
</section>

<?php if ($flash): ?>
    <div class="max-w-5xl mx-auto px-4 mt-7">
        <div class="p-4 rounded-xl border <?= ($flash['type'] ?? '') === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800' ?>">
            <?= e($flash['message'] ?? '') ?>
        </div>
    </div>
<?php endif; ?>

<section class="py-12 lg:py-16 bg-slate-50">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
        <form method="GET" action="/alumni" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm grid md:grid-cols-5 gap-3 mb-10">
            <input type="search" name="q" value="<?= e($query['term'] ?? '') ?>" maxlength="80" placeholder="Cari nama/instansi..."
                class="md:col-span-2 px-4 py-2.5 border-slate-300 rounded-xl">
            <select name="tahun" class="px-3 py-2.5 border-slate-300 rounded-xl">
                <option value="">Semua angkatan</option>
                <?php foreach (($filters['years'] ?? []) as $option): ?>
                    <option value="<?= (int) $option['value'] ?>" <?= (string) ($query['yearRaw'] ?? '') === (string) $option['value'] ? 'selected' : '' ?>><?= (int) $option['value'] ?></option>
                <?php endforeach; ?>
            </select>
            <select name="kota" class="px-3 py-2.5 border-slate-300 rounded-xl">
                <option value="">Semua kota</option>
                <?php foreach (($filters['cities'] ?? []) as $option): ?>
                    <option value="<?= e($option['value']) ?>" <?= ($query['city'] ?? '') === $option['value'] ? 'selected' : '' ?>><?= e($option['value']) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl">Telusuri</button>
        </form>

        <div class="flex items-center justify-between gap-4 mb-6">
            <p class="text-slate-600"><strong class="text-slate-900"><?= (int) ($result['total'] ?? 0) ?></strong> alumni ditemukan</p>
            <a href="#daftar-alumni" class="text-primary-600 font-semibold hover:text-primary-700">Daftarkan Data Alumni</a>
        </div>

        <?php if (empty($items)): ?>
            <div class="p-12 text-center bg-white rounded-2xl border border-slate-200 text-slate-500">Belum ada alumni yang sesuai dengan pencarian.</div>
        <?php else: ?>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($items as $item): ?>
                    <a href="/alumni/<?= (int) $item['id'] ?>" class="alumni-card group bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-lg transition-all">
                        <div class="alumni-card__media">
                            <?php if (!empty($item['photo'])): ?>
                                <img src="/storage/<?= e($item['photo']) ?>" alt="Foto <?= e($item['name']) ?>" loading="lazy" decoding="async" class="alumni-card__photo">
                            <?php else: ?>
                                <div class="alumni-card__initial" aria-hidden="true"><?= e(mb_strtoupper(mb_substr(trim($item['name']), 0, 1))) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($item['is_featured'])): ?><span class="absolute top-3 right-3 px-2.5 py-1 bg-amber-400 text-amber-950 rounded-full text-xs font-bold">Inspiratif</span><?php endif; ?>
                        </div>
                        <div class="alumni-card__body p-5">
                            <h2 class="font-bold text-lg text-slate-900 group-hover:text-primary-600"><?= e($item['name']) ?></h2>
                            <p class="text-sm text-primary-600 font-semibold mt-1">Angkatan <?= (int) $item['graduation_year'] ?></p>
                            <?php if (!empty($item['occupation'])): ?><p class="text-sm text-slate-500 mt-2 line-clamp-1"><?= e($item['occupation']) ?><?= !empty($item['institution']) ? ' · ' . e($item['institution']) : '' ?></p><?php endif; ?>
                            <?php if (!empty($item['city'])): ?><p class="text-xs text-slate-400 mt-1"><?= e($item['city']) ?></p><?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if (($result['last_page'] ?? 1) > 1): ?>
                <nav class="flex justify-center gap-2 mt-10" aria-label="Pagination">
                    <?php for ($page = 1; $page <= $result['last_page']; $page++): $queryString['page'] = $page; ?>
                        <a href="/alumni?<?= e(http_build_query($queryString)) ?>" class="w-10 h-10 flex items-center justify-center rounded-lg <?= $page === $result['current_page'] ? 'bg-primary-600 text-white' : 'bg-white border border-slate-200 text-slate-600' ?>"><?= $page ?></a>
                    <?php endfor; ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<section class="py-14 lg:py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <details id="daftar-alumni" class="group scroll-mt-24">
            <summary
                class="list-none cursor-pointer rounded-2xl border border-primary-200 bg-primary-50 p-5 sm:p-6 transition-colors hover:bg-primary-100 focus:outline-none focus-visible:ring-4 focus-visible:ring-primary-200 [&::-webkit-details-marker]:hidden">
                <span class="flex items-center justify-between gap-4">
                    <span>
                        <span class="block text-primary-600 font-semibold text-sm uppercase tracking-wider">Jejaring Lulusan</span>
                        <span class="block text-xl sm:text-2xl font-bold text-slate-900 mt-1">Isi Data Alumni</span>
                        <span class="block text-sm sm:text-base text-slate-600 mt-1">Klik untuk membuka formulir pendataan alumni.</span>
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
                <p class="text-slate-600 text-center mb-6">Data akan diverifikasi admin. Kontak dienkripsi dan tidak pernah ditampilkan kepada publik.</p>
                <form action="/alumni/kirim" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 bg-slate-50 border border-slate-200 rounded-2xl space-y-5">
            <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">
            <div class="absolute -left-[10000px]" aria-hidden="true"><label>Website<input name="website" tabindex="-1" autocomplete="off"></label></div>
            <div class="grid sm:grid-cols-2 gap-4">
                <label class="text-sm font-semibold text-slate-700">Nama lengkap *<input name="name" required maxlength="100" autocomplete="name" class="block w-full mt-1.5 px-3 py-2.5 border-slate-300 rounded-xl"></label>
                <label class="text-sm font-semibold text-slate-700">Tahun lulus *<input type="number" name="graduation_year" required min="1950" max="<?= (int) date('Y') + 1 ?>" class="block w-full mt-1.5 px-3 py-2.5 border-slate-300 rounded-xl"></label>
                <label class="text-sm font-semibold text-slate-700">Kelas terakhir<input name="final_class" maxlength="60" class="block w-full mt-1.5 px-3 py-2.5 border-slate-300 rounded-xl"></label>
                <label class="text-sm font-semibold text-slate-700">Melanjutkan ke *
                    <select id="publicContinuationType" name="further_education" required class="block w-full mt-1.5 px-3 py-2.5 border-slate-300 rounded-xl">
                        <option value="">Pilih tujuan setelah lulus</option>
                        <?php foreach ($continuationOptions as $option): ?>
                            <option value="<?= e($option) ?>"><?= e($option) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="text-sm font-semibold text-slate-700">Status sekolah <span id="publicContinuationStatusRequired">*</span>
                    <select id="publicContinuationStatus" name="further_education_status" required class="block w-full mt-1.5 px-3 py-2.5 border-slate-300 rounded-xl">
                        <option value="">Pilih negeri atau swasta</option>
                        <option value="Negeri">Negeri</option>
                        <option value="Swasta">Swasta</option>
                    </select>
                </label>
                <label class="text-sm font-semibold text-slate-700">Nama sekolah tujuan <span id="publicContinuationDetailRequired">*</span><input id="publicContinuationDetail" name="further_education_detail" required maxlength="120" placeholder="Contoh: SMAN 1 Bogor" class="block w-full mt-1.5 px-3 py-2.5 border-slate-300 rounded-xl"></label>
                <label class="text-sm font-semibold text-slate-700">Status pekerjaan/aktivitas
                    <select name="employment_status" class="block w-full mt-1.5 px-3 py-2.5 border-slate-300 rounded-xl">
                        <option value="">Pilih status saat ini</option>
                        <?php foreach ($employmentStatuses as $option): ?>
                            <option value="<?= e($option) ?>"><?= e($option) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="text-sm font-semibold text-slate-700">Pekerjaan/bidang<input name="occupation" maxlength="120" class="block w-full mt-1.5 px-3 py-2.5 border-slate-300 rounded-xl"></label>
                <label class="text-sm font-semibold text-slate-700">Instansi<input name="institution" maxlength="160" class="block w-full mt-1.5 px-3 py-2.5 border-slate-300 rounded-xl"></label>
                <label class="text-sm font-semibold text-slate-700">Kota domisili<input name="city" maxlength="100" class="block w-full mt-1.5 px-3 py-2.5 border-slate-300 rounded-xl"></label>
                <label class="text-sm font-semibold text-slate-700">WhatsApp/email untuk verifikasi *<input name="contact" required maxlength="160" autocomplete="email" class="block w-full mt-1.5 px-3 py-2.5 border-slate-300 rounded-xl"></label>
            </div>
            <label class="block text-sm font-semibold text-slate-700">Cerita setelah lulus<textarea name="story" maxlength="1500" rows="4" class="block w-full mt-1.5 px-3 py-2.5 border-slate-300 rounded-xl"></textarea></label>
            <label class="block text-sm font-semibold text-slate-700">Prestasi/pencapaian<textarea name="achievement" maxlength="1000" rows="3" class="block w-full mt-1.5 px-3 py-2.5 border-slate-300 rounded-xl"></textarea></label>
            <div><label class="block text-sm font-semibold text-slate-700">Foto opsional</label><input type="file" name="photo" accept="image/jpeg,image/png,image/webp" class="mt-2 text-sm"><p class="text-xs text-slate-500 mt-1">JPG, PNG, atau WebP; maksimal 2 MB dan 16 megapiksel.</p></div>
            <div class="p-4 bg-white border border-slate-200 rounded-xl space-y-3">
                <p class="text-sm font-bold text-slate-700">Informasi yang boleh ditampilkan:</p>
                <label class="flex gap-2 text-sm text-slate-600"><input type="checkbox" name="publish_photo" value="1" class="rounded text-primary-600"> Foto</label>
                <label class="flex gap-2 text-sm text-slate-600"><input type="checkbox" name="publish_occupation" value="1" class="rounded text-primary-600"> Pekerjaan dan instansi</label>
                <label class="flex gap-2 text-sm text-slate-600"><input type="checkbox" name="publish_city" value="1" class="rounded text-primary-600"> Kota domisili</label>
                <label class="flex items-start gap-2 text-sm text-slate-600"><input type="checkbox" name="consent" value="1" required class="mt-1 rounded text-primary-600"> Saya menyetujui publikasi nama, tahun lulus, serta informasi yang saya pilih setelah diverifikasi sekolah.</label>
            </div>
                    <?= Security::publicCaptchaInput('alumni') ?>
                    <button class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl">Kirim untuk Diverifikasi</button>
                </form>
            </div>
        </details>
    </div>
</section>

<script>
(() => {
    const type = document.getElementById('publicContinuationType');
    const status = document.getElementById('publicContinuationStatus');
    const detail = document.getElementById('publicContinuationDetail');
    const statusRequired = document.getElementById('publicContinuationStatusRequired');
    const detailRequired = document.getElementById('publicContinuationDetailRequired');
    const syncSchoolFields = () => {
        const disabled = ['Bekerja', 'Tidak Melanjutkan'].includes(type?.value || '');
        if (status) { status.disabled = disabled; status.required = !disabled; }
        if (detail) { detail.disabled = disabled; detail.required = !disabled; }
        if (statusRequired) statusRequired.classList.toggle('hidden', disabled);
        if (detailRequired) detailRequired.classList.toggle('hidden', disabled);
        if (disabled) { status.value = ''; detail.value = ''; }
    };
    type?.addEventListener('change', syncSchoolFields);
    syncSchoolFields();
})();
</script>
