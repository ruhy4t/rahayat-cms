<?php
/**
 * Backend - Profile Management
 */
$title = $data['title'] ?? 'Profil Sekolah';
$user = $data['user'] ?? null;
$profile = $data['profile'] ?? [];
$flash = $data['flash'] ?? [];
?>

<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/super-build/ckeditor.js"></script>

<style>
    /* Restore list styles for CKEditor */
    .ck-content ul,
    .ck-content ol {
        padding-left: 2rem;
    }

    .ck-content ul {
        list-style-type: disc;
    }

    .ck-content ol {
        list-style-type: decimal;
    }
</style>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">
        <?= e($title) ?>
    </h1>
    <p class="text-slate-600 text-sm mt-1">Kelola informasi dan pengaturan sekolah</p>
</div>

<!-- Flash Messages -->
<?php if (!empty($flash) && isset($flash['type']) && isset($flash['message'])): ?>
    <div
        class="mb-4 p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' ?>">
        <?= e($flash['message']) ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-xl shadow-md border border-slate-100 overflow-hidden">
    <form action="/admin/profil/update" method="POST" enctype="multipart/form-data" id="profileForm">
        <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">

        <!-- Tabs -->
        <div class="border-b border-slate-100">
            <nav class="flex gap-4 px-6" aria-label="Tabs">
                <button type="button"
                    class="tab-btn active px-4 pb-4 pt-6 text-sm font-medium border-b-2 border-transparent hover:border-slate-300 focus:outline-none"
                    data-tab="info">
                    Informasi Umum
                </button>
                <button type="button"
                    class="tab-btn px-4 pb-4 pt-6 text-sm font-medium border-b-2 border-transparent hover:border-slate-300 focus:outline-none"
                    data-tab="visi">
                    Visi & Misi
                </button>
                <button type="button"
                    class="tab-btn px-4 pb-4 pt-6 text-sm font-medium border-b-2 border-transparent hover:border-slate-300 focus:outline-none"
                    data-tab="kontak">
                    Kontak
                </button>
                <button type="button"
                    class="tab-btn px-4 pb-4 pt-6 text-sm font-medium border-b-2 border-transparent hover:border-slate-300 focus:outline-none"
                    data-tab="jam">
                    Jam Operasional
                </button>
                <button type="button"
                    class="tab-btn px-4 pb-4 pt-6 text-sm font-medium border-b-2 border-transparent hover:border-slate-300 focus:outline-none"
                    data-tab="stats">
                    Statistik
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Tab: Informasi Umum -->
            <div id="tab-info" class="tab-content">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div
                        class="md:col-span-2 flex items-start gap-6 bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <div
                            class="w-32 h-32 bg-white rounded-lg border border-slate-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                            <?php if (!empty($profile['logo'])): ?>
                                <img src="/storage/<?= e($profile['logo']) ?>" alt="Logo"
                                    class="w-full h-full object-contain">
                            <?php else: ?>
                                <span class="text-slate-300 text-4xl font-bold">L</span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1">
                            <label for="logo" class="block text-sm font-medium text-slate-700 mb-1">Logo Sekolah</label>
                            <input type="file" id="logo" name="logo" accept="image/jpeg,image/png,image/gif,image/webp"
                                class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG, GIF, WebP. Maksimal 5MB. Logo ini akan
                                digunakan di seluruh aplikasi.</p>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Sekolah</label>
                        <input type="text" id="name" name="name" value="<?= e($profile['name'] ?? '') ?>"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                    <div class="md:col-span-2">
                        <label for="tagline" class="block text-sm font-medium text-slate-700 mb-1">Tagline
                            (Slogan)</label>
                        <input type="text" id="tagline" name="tagline" value="<?= e($profile['tagline'] ?? '') ?>"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            placeholder="Contoh: Berkarakter, Cerdas, dan Berdaya Saing Global">
                        <p class="text-xs text-slate-500 mt-1">Akan ditampilkan di halaman beranda di bawah ucapan
                            selamat datang.</p>
                    </div>
                    <div>
                        <label for="school_type" class="block text-sm font-medium text-slate-700 mb-1">Status
                            Sekolah</label>
                        <select id="school_type" name="school_type"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            onchange="toggleSpmbLink()">
                            <option value="negeri" <?= ($profile['school_type'] ?? 'negeri') === 'negeri' ? 'selected' : '' ?>>Negeri</option>
                            <option value="swasta" <?= ($profile['school_type'] ?? '') === 'swasta' ? 'selected' : '' ?>>
                                Swasta</option>
                        </select>
                    </div>
                    <div id="spmb_link_container"
                        class="<?= ($profile['school_type'] ?? 'negeri') === 'swasta' ? 'hidden' : '' ?>">
                        <label for="spmb_link" class="block text-sm font-medium text-slate-700 mb-1">Link SPMB
                            Wilayah</label>
                        <input type="url" id="spmb_link" name="spmb_link" value="<?= e($profile['spmb_link'] ?? '') ?>"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            placeholder="https://spmb.kotatangerang.go.id">
                        <p class="text-xs text-slate-500 mt-1">Khusus Sekolah Negeri. Link ini akan digunakan pada
                            tombol "Daftar SPMB".</p>
                    </div>
                    <div>
                        <label for="npsn" class="block text-sm font-medium text-slate-700 mb-1">NPSN</label>
                        <input type="text" id="npsn" name="npsn" value="<?= e($profile['npsn'] ?? '') ?>"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                    <div>
                        <label for="accreditation"
                            class="block text-sm font-medium text-slate-700 mb-1">Akreditasi</label>
                        <select id="accreditation" name="accreditation"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">Pilih Akreditasi</option>
                            <option value="A" <?= ($profile['accreditation'] ?? '') === 'A' ? 'selected' : '' ?>>A
                            </option>
                            <option value="B" <?= ($profile['accreditation'] ?? '') === 'B' ? 'selected' : '' ?>>B
                            </option>
                            <option value="C" <?= ($profile['accreditation'] ?? '') === 'C' ? 'selected' : '' ?>>C
                            </option>
                        </select>
                    </div>
                    <div>
                        <label for="established_year" class="block text-sm font-medium text-slate-700 mb-1">Tahun
                            Berdiri</label>
                        <input type="number" id="established_year" name="established_year"
                            value="<?= e($profile['established_year'] ?? '') ?>"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                    <div class="md:col-span-2">
                        <label for="principal_name" class="block text-sm font-medium text-slate-700 mb-1">Nama Kepala
                            Sekolah</label>
                        <input type="text" id="principal_name" name="principal_name"
                            value="<?= e($profile['principal_name'] ?? '') ?>"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                    <div class="md:col-span-2">
                        <label for="principal_nip" class="block text-sm font-medium text-slate-700 mb-1">NIP Kepala
                            Sekolah</label>
                        <input type="text" id="principal_nip" name="principal_nip"
                            value="<?= e($profile['principal_nip'] ?? '') ?>"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                    <div class="md:col-span-2">
                        <label for="principal_photo" class="block text-sm font-medium text-slate-700 mb-1">Foto Kepala
                            Sekolah</label>
                        <div class="flex items-center gap-4">
                            <div
                                class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center overflow-hidden border border-slate-200 flex-shrink-0">
                                <?php if (!empty($profile['principal_photo'])): ?>
                                    <img src="/storage/<?= e($profile['principal_photo']) ?>" alt="Kepala Sekolah"
                                        class="w-full h-full object-cover">
                                <?php else: ?>
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <input type="file" id="principal_photo" name="principal_photo" accept="image/jpeg,image/png,image/gif,image/webp"
                                class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                    </div>
                    <div class="md:col-span-2 mt-2">
                        <div class="flex items-start gap-3 bg-amber-50 p-4 rounded-lg border border-amber-200">
                            <input type="checkbox" id="watermark_enabled" name="watermark_enabled" value="1"
                                <?= ($profile['watermark_enabled'] ?? true) ? 'checked' : '' ?>
                                class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 mt-0.5">
                            <div>
                                <label for="watermark_enabled" class="block text-sm font-medium text-slate-800">Aktifkan
                                    Watermark pada Foto</label>
                                <p class="text-xs text-slate-600 mt-1">Jika diaktifkan, semua foto/gambar yang diunggah
                                    akan diberi watermark teks <strong>"Property of
                                        <?= e($profile['name'] ?? SCHOOL_NAME) ?>"</strong> secara otomatis.
                                    Watermark tidak akan diterapkan pada logo dan foto profil.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Visi, Misi & Sambutan -->
            <div id="tab-visi" class="tab-content hidden">
                <div class="space-y-6">
                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
                        <h3 class="font-bold text-indigo-800 mb-2">Sambutan & Quote Kepala Sekolah</h3>
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="welcome_message" class="block text-sm font-medium text-slate-700 mb-1">Kata
                                    Sambutan Kepala Sekolah</label>
                                <textarea id="welcome_message" name="welcome_message" rows="6"
                                    class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"><?= $profile['welcome_message'] ?? '' ?></textarea>
                            </div>
                            <div>
                                <label for="principal_quote"
                                    class="block text-sm font-medium text-slate-700 mb-1">Kutipan (Quote) Kepala
                                    Sekolah</label>
                                <textarea id="principal_quote" name="principal_quote" rows="2"
                                    class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"><?= $profile['principal_quote'] ?? '' ?></textarea>
                                <p class="text-xs text-slate-500 mt-1">Akan ditampilkan di halaman profil di bawah foto
                                    Kepala Sekolah.</p>
                            </div>
                        </div>
                    </div>

                    <h3 class="font-bold text-slate-800 border-b pb-2">Visi, Misi & Sejarah</h3>

                    <div>
                        <label for="vision" class="block text-sm font-medium text-slate-700 mb-1">Visi</label>
                        <textarea id="vision" name="vision" rows="4"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"><?= $profile['vision'] ?? '' ?></textarea>
                    </div>
                    <div>
                        <label for="mission" class="block text-sm font-medium text-slate-700 mb-1">Misi</label>
                        <textarea id="mission" name="mission" rows="6"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"><?= $profile['mission'] ?? '' ?></textarea>
                    </div>
                    <div>
                        <label for="motto" class="block text-sm font-medium text-slate-700 mb-1">Motto Sekolah</label>
                        <textarea id="motto" name="motto" rows="3"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"><?= $profile['motto'] ?? '' ?></textarea>
                    </div>
                    <div>
                        <label for="history" class="block text-sm font-medium text-slate-700 mb-1">Sejarah
                            Sekolah</label>
                        <textarea id="history" name="history" rows="6"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"><?= $profile['history'] ?? '' ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Tab: Kontak -->
            <div id="tab-kontak" class="tab-content hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
                        <textarea id="address" name="address" rows="3"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"><?= e($profile['address'] ?? '') ?></textarea>
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">Telepon</label>
                        <input type="text" id="phone" name="phone" value="<?= e($profile['phone'] ?? '') ?>"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" value="<?= e($profile['email'] ?? '') ?>"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                    <div>
                        <label for="website" class="block text-sm font-medium text-slate-700 mb-1">Website</label>
                        <input type="url" id="website" name="website" value="<?= e($profile['website'] ?? '') ?>"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            placeholder="https://">
                    </div>

                    <!-- Google Maps Embed -->
                    <div class="md:col-span-2">
                        <label for="google_maps_embed" class="block text-sm font-medium text-slate-700 mb-1">Google Maps Embed</label>
                        <textarea id="google_maps_embed" name="google_maps_embed" rows="4"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none font-mono text-sm"
                            placeholder='Tempel kode embed dari Google Maps, contoh: <iframe src="https://www.google.com/maps/embed?pb=..." ...></iframe>'><?= e($profile['google_maps_embed'] ?? '') ?></textarea>
                        <p class="text-xs text-slate-500 mt-1">
                            Buka <a href="https://www.google.com/maps" target="_blank" class="text-indigo-600 hover:underline">Google Maps</a>, 
                            cari lokasi sekolah → klik <strong>Bagikan</strong> → pilih tab <strong>Sematkan peta</strong> → salin kode HTML dan tempel di sini.
                        </p>
                        <!-- Map Preview -->
                        <?php
                        $mapSrc = '';
                        $embedVal = $profile['google_maps_embed'] ?? '';
                        if (!empty($embedVal)) {
                            // Extract src from iframe if full embed code is provided
                            if (preg_match('/src=["\']([^"\']+)["\']/i', $embedVal, $matches)) {
                                $mapSrc = $matches[1];
                            } elseif (filter_var($embedVal, FILTER_VALIDATE_URL)) {
                                $mapSrc = $embedVal;
                            }
                        }
                        ?>
                        <?php if (!empty($mapSrc)): ?>
                            <div class="mt-3 rounded-lg overflow-hidden border border-slate-200" id="maps-preview">
                                <iframe src="<?= e($mapSrc) ?>" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        <?php else: ?>
                            <div class="mt-3 rounded-lg border-2 border-dashed border-slate-200 p-6 text-center text-slate-400" id="maps-preview">
                                <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <p class="text-sm">Preview peta akan muncul setelah kode embed diisi</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Tab: Jam Operasional -->
            <div id="tab-jam" class="tab-content hidden">
                <div class="space-y-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-800">Atur jam buka dan tutup untuk setiap hari. Aktifkan/nonaktifkan
                            toggle untuk menandai hari buka atau tutup.</p>
                    </div>

                    <?php
                    $days = [
                        'monday' => 'Senin',
                        'tuesday' => 'Selasa',
                        'wednesday' => 'Rabu',
                        'thursday' => 'Kamis',
                        'friday' => 'Jumat',
                        'saturday' => 'Sabtu',
                        'sunday' => 'Minggu',
                    ];
                    $defaultOpen = [
                        'monday' => '07:00', 'tuesday' => '07:00', 'wednesday' => '07:00',
                        'thursday' => '07:00', 'friday' => '07:00', 'saturday' => '07:00', 'sunday' => '07:00',
                    ];
                    $defaultClose = [
                        'monday' => '15:00', 'tuesday' => '15:00', 'wednesday' => '15:00',
                        'thursday' => '15:00', 'friday' => '15:00', 'saturday' => '12:00', 'sunday' => '15:00',
                    ];
                    $defaultClosed = [
                        'monday' => 0, 'tuesday' => 0, 'wednesday' => 0,
                        'thursday' => 0, 'friday' => 0, 'saturday' => 0, 'sunday' => 1,
                    ];
                    ?>

                    <?php foreach ($days as $key => $label): ?>
                        <?php
                        $isClosed = (int)($profile["is_closed_{$key}"] ?? $defaultClosed[$key]);
                        $openVal = $profile["{$key}_open"] ?? $defaultOpen[$key];
                        $closeVal = $profile["{$key}_close"] ?? $defaultClose[$key];
                        ?>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-4 rounded-lg border <?= $isClosed ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200' ?>"
                            id="day-row-<?= $key ?>">
                            <!-- Day Name -->
                            <div class="w-24 flex-shrink-0">
                                <span class="font-semibold text-slate-800"><?= $label ?></span>
                            </div>

                            <!-- Toggle Buka/Tutup -->
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_closed_<?= $key ?>" value="1"
                                        <?= $isClosed ? 'checked' : '' ?>
                                        class="sr-only peer day-toggle" data-day="<?= $key ?>"
                                        onchange="toggleDay('<?= $key ?>')">
                                    <div
                                        class="w-11 h-6 bg-green-500 peer-checked:bg-red-400 rounded-full peer peer-focus:ring-2 peer-focus:ring-red-300 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full">
                                    </div>
                                </label>
                                <span class="text-sm font-medium day-status-label"
                                    id="status-<?= $key ?>"><?= $isClosed ? '<span class="text-red-600">Tutup</span>' : '<span class="text-green-700">Buka</span>' ?></span>
                            </div>

                            <!-- Time Inputs -->
                            <div class="flex items-center gap-2 flex-1" id="time-inputs-<?= $key ?>">
                                <div class="flex items-center gap-2">
                                    <label class="text-xs text-slate-500 w-10">Buka:</label>
                                    <input type="time" name="<?= $key ?>_open" value="<?= e($openVal) ?>"
                                        <?= $isClosed ? 'disabled' : '' ?>
                                        class="px-3 py-1.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm <?= $isClosed ? 'opacity-40 bg-slate-100' : '' ?>"
                                        id="input-<?= $key ?>-open">
                                </div>
                                <span class="text-slate-400">—</span>
                                <div class="flex items-center gap-2">
                                    <label class="text-xs text-slate-500 w-10">Tutup:</label>
                                    <input type="time" name="<?= $key ?>_close" value="<?= e($closeVal) ?>"
                                        <?= $isClosed ? 'disabled' : '' ?>
                                        class="px-3 py-1.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm <?= $isClosed ? 'opacity-40 bg-slate-100' : '' ?>"
                                        id="input-<?= $key ?>-close">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Tab: Statistik -->
            <div id="tab-stats" class="tab-content hidden">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="total_students" class="block text-sm font-medium text-slate-700 mb-1">Jumlah Murid
                            Aktif</label>
                        <input type="number" id="total_students" name="total_students"
                            value="<?= e($profile['total_students'] ?? '0') ?>"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            min="0">
                    </div>
                    <div>
                        <label for="total_teachers" class="block text-sm font-medium text-slate-700 mb-1">Jumlah
                            Guru</label>
                        <input type="number" id="total_teachers" name="total_teachers"
                            value="<?= e($profile['total_teachers'] ?? '0') ?>"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            min="0">
                    </div>
                    <div>
                        <label for="graduation_rate" class="block text-sm font-medium text-slate-700 mb-1">Tingkat
                            Kelulusan (%)</label>
                        <input type="number" id="graduation_rate" name="graduation_rate"
                            value="<?= e($profile['graduation_rate'] ?? '100') ?>"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                            min="0" max="100">
                    </div>
                </div>
                <div class="mt-4 bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <p class="text-sm text-amber-800">Data statistik ini akan ditampilkan di halaman beranda sekolah.
                    </p>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="px-6 py-4 border-t border-slate-100 flex justify-end">
            <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition-colors shadow-md hover:shadow-lg">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const targetTab = btn.dataset.tab;

                // Update buttons
                tabBtns.forEach(b => {
                    b.classList.remove('active', 'border-indigo-500', 'text-indigo-600');
                    b.classList.add('text-slate-500');
                });
                btn.classList.add('active', 'border-indigo-500', 'text-indigo-600');
                btn.classList.remove('text-slate-500');

                // Update content
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                document.getElementById(`tab-${targetTab}`).classList.remove('hidden');
            });
        });

        // Initialize first tab
        tabBtns[0]?.click();

        // --- CKEditor 5 Advanced Setup (Matches News Module) ---

        // Custom Upload Adapter
        class MyUploadAdapter {
            constructor(loader) {
                this.loader = loader;
            }

            upload() {
                return this.loader.file
                    .then(file => new Promise((resolve, reject) => {
                        this._initRequest();
                        this._initListeners(resolve, reject, file);
                        this._sendRequest(file);
                    }));
            }

            abort() {
                if (this.xhr) {
                    this.xhr.abort();
                }
            }

            _initRequest() {
                const xhr = this.xhr = new XMLHttpRequest();
                xhr.open('POST', '/admin/upload/image', true);
                xhr.responseType = 'json';

                // Add CSRF Token if available
                const csrfToken = document.querySelector('input[name="csrf_token"]')?.value;
                if (csrfToken) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                }
            }

            _initListeners(resolve, reject, file) {
                const xhr = this.xhr;
                const loader = this.loader;
                const genericErrorText = `Couldn't upload file: ${file.name}.`;

                xhr.addEventListener('error', () => reject(genericErrorText));
                xhr.addEventListener('abort', () => reject());
                xhr.addEventListener('load', () => {
                    const response = xhr.response;

                    if (!response || response.error) {
                        return reject(response && response.error ? response.error.message : genericErrorText);
                    }

                    resolve({
                        default: response.url
                    });
                });

                if (xhr.upload) {
                    xhr.upload.addEventListener('progress', evt => {
                        if (evt.lengthComputable) {
                            loader.uploadTotal = evt.total;
                            loader.uploaded = evt.loaded;
                        }
                    });
                }
            }

            _sendRequest(file) {
                const data = new FormData();
                data.append('upload', file);

                // Append CSRF token if needed for the backend
                const csrfToken = document.querySelector('input[name="csrf_token"]')?.value;
                if (csrfToken) {
                    data.append('csrf_token', csrfToken);
                }

                this.xhr.send(data);
            }
        }

        function MyCustomUploadAdapterPlugin(editor) {
            editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                return new MyUploadAdapter(loader);
            };
        }

        // Initialize CKEditor 5 for specific textareas
        const textareas = ['vision', 'mission', 'history', 'motto', 'welcome_message'];
        const editors = {};

        textareas.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                if (window.CKEDITOR && window.CKEDITOR.ClassicEditor) {
                    CKEDITOR.ClassicEditor.create(element, {
                        // Plugins configuration
                        extraPlugins: [MyCustomUploadAdapterPlugin],

                        // Toolbar configuration (Matches News Module)
                        toolbar: {
                            items: [
                                'heading', '|',
                                'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript', 'removeFormat', '|',
                                'bulletedList', 'numberedList', '|',
                                'outdent', 'indent', '|',
                                'imageUpload', 'blockQuote', 'insertTable', 'mediaEmbed', 'link', '|',
                                'undo', 'redo',
                                '-',
                                'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                                'alignment', '|',
                                'horizontalLine', 'pageBreak', '|',
                                'sourceEditing'
                            ],
                            shouldNotGroupWhenFull: true
                        },

                        // Language
                        language: 'id',

                        // Image configuration
                        image: {
                            toolbar: [
                                'imageTextAlternative', 'imageStyle:inline', 'imageStyle:block', 'imageStyle:side', '|',
                                'toggleImageCaption', 'imageResize'
                            ],
                            insert: {
                                type: 'auto'
                            }
                        },

                        // Table configuration
                        table: {
                            contentToolbar: [
                                'tableColumn', 'tableRow', 'mergeTableCells', 'tableCellProperties', 'tableProperties'
                            ]
                        },

                        // Remove premium/AI plugins
                        removePlugins: [
                            'CaseChange', 'ExportPdf', 'ExportWord', 'ImportWord', 'AIAssistant', 'CKBox', 'CKFinder',
                            'EasyImage', 'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
                            'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments', 'TrackChanges',
                            'TrackChangesData', 'RevisionHistory', 'Pagination', 'WProofreader', 'MathType',
                            'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter', 'TableOfContents',
                            'PasteFromOfficeEnhanced'
                        ],
                    })
                        .then(editor => {
                            editors[id] = editor;
                            console.log(`CKEditor initialized for #${id}`);

                            // Set min-height
                            editor.editing.view.change(writer => {
                                writer.setStyle('min-height', '200px', editor.editing.view.document.getRoot());
                            });
                        })
                        .catch(err => {
                            console.error(`CKEditor Init Error for #${id}:`, err);
                        });
                }
            }
        });

        // Handle form submission
        const form = document.getElementById('profileForm');
        form.addEventListener('submit', function (e) {
            // Sync data from editors to textareas
            Object.keys(editors).forEach(id => {
                if (editors[id]) {
                    const data = editors[id].getData();
                    document.getElementById(id).value = data;
                }
            });
        });

        // Toggle SPMB Link visibility
        window.toggleSpmbLink = function () {
            const type = document.getElementById('school_type').value;
            const container = document.getElementById('spmb_link_container');
            if (type === 'negeri') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        };

        // Toggle day open/closed
        window.toggleDay = function(day) {
            const checkbox = document.querySelector(`input[name="is_closed_${day}"]`);
            const isClosed = checkbox.checked;
            const row = document.getElementById(`day-row-${day}`);
            const statusLabel = document.getElementById(`status-${day}`);
            const openInput = document.getElementById(`input-${day}-open`);
            const closeInput = document.getElementById(`input-${day}-close`);

            // Update row styling
            if (isClosed) {
                row.classList.remove('bg-green-50', 'border-green-200');
                row.classList.add('bg-red-50', 'border-red-200');
                statusLabel.innerHTML = '<span class="text-red-600">Tutup</span>';
            } else {
                row.classList.remove('bg-red-50', 'border-red-200');
                row.classList.add('bg-green-50', 'border-green-200');
                statusLabel.innerHTML = '<span class="text-green-700">Buka</span>';
            }

            // Enable/disable time inputs
            [openInput, closeInput].forEach(input => {
                input.disabled = isClosed;
                if (isClosed) {
                    input.classList.add('opacity-40', 'bg-slate-100');
                } else {
                    input.classList.remove('opacity-40', 'bg-slate-100');
                }
            });
        };

        // Re-enable disabled inputs before form submit so values are sent
        const profileForm = document.getElementById('profileForm');
        const originalSubmitHandler = profileForm.onsubmit;
        profileForm.addEventListener('submit', function() {
            document.querySelectorAll('#tab-jam input[disabled]').forEach(input => {
                input.disabled = false;
            });
        });
    });
</script>
