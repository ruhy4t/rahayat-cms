<?php
/**
 * Backend - Settings View (Theme Selection)
 */
$title = $data['title'] ?? 'Pengaturan';
$settings = $data['settings'] ?? [];
$themes = $data['themes'] ?? [];
$currentTheme = $data['currentTheme'] ?? 'indigo-modern';
$profile = $data['profile'] ?? [];
$flash = $data['flash'] ?? null;
?>

<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-slate-800">
            <?= e($title) ?>
        </h1>
        <p class="text-slate-500 mt-1">Konfigurasi website dan pilih tema tampilan</p>
    </div>

    <?php if ($flash): ?>
        <div
            class="p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200' ?>">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <!-- Theme Selection -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-lg font-semibold text-slate-800">Pilih Tema</h2>
            <p class="text-slate-500 text-sm mt-1">Ubah tampilan website dengan memilih salah satu tema</p>
        </div>
        <div class="p-6">
            <div class="grid md:grid-cols-3 gap-6">
                <?php foreach ($themes as $key => $theme): ?>
                    <label class="cursor-pointer group">
                        <input type="radio" name="theme" value="<?= e($key) ?>" <?= $currentTheme === $key ? 'checked' : '' ?>
                            class="hidden peer">
                        <div
                            class="border-2 rounded-xl overflow-hidden transition-all peer-checked:border-primary-500 peer-checked:ring-2 peer-checked:ring-primary-200 border-slate-200 group-hover:border-slate-300">
                            <!-- Theme Preview -->
                            <div class="h-36 relative flex flex-col items-center justify-center p-4 transition-all"
                                style="background: linear-gradient(135deg, <?= e($theme['primary']) ?> 0%, <?= e($theme['primary']) ?>dd 100%)">

                                <div
                                    class="bg-white/20 rounded-lg px-4 py-2 backdrop-blur-sm mb-3 z-10 shadow-sm border border-white/10">
                                    <span class="text-white font-bold tracking-wide">
                                        <?= e($theme['name']) ?>
                                    </span>
                                </div>

                                <!-- Mini Wireframe -->
                                <?php if ($key === 'indigo-modern'): ?>
                                    <div class="flex flex-col gap-1.5 w-full max-w-[120px] opacity-60">
                                        <div class="w-full h-2 bg-white rounded-full flex justify-between">
                                            <div class="w-1/3 h-full bg-white/50 rounded-l-full"></div>
                                        </div>
                                        <div class="flex gap-1">
                                            <div class="w-1/2 h-10 bg-white rounded-md"></div>
                                            <div class="w-1/2 h-10 bg-white/50 rounded-md"></div>
                                        </div>
                                    </div>
                                <?php elseif ($key === 'emerald-campus'): ?>
                                    <div class="flex flex-col gap-1 w-full max-w-[120px] opacity-60 items-center">
                                        <div class="w-full h-1 bg-white/60"></div>
                                        <div class="w-full h-2 bg-white rounded-sm mb-1"></div>
                                        <div class="w-full h-8 bg-white/80 rounded-b-xl flex items-center justify-center">
                                            <div class="w-1/2 h-1.5 bg-slate-900/20 rounded-full"></div>
                                        </div>
                                    </div>
                                <?php elseif ($key === 'crimson-bold'): ?>
                                    <div
                                        class="flex flex-col w-full max-w-[120px] opacity-60 relative h-[50px] bg-white/10 rounded overflow-hidden">
                                        <div class="absolute top-0 right-0 w-2/3 h-full bg-white/40"
                                            style="clip-path: polygon(20% 0, 100% 0, 100% 100%, 0% 100%);"></div>
                                        <div class="absolute inset-y-0 left-0 w-1 bg-white/80"></div>
                                        <div class="absolute left-2 top-2 w-1/3 h-2 bg-white rounded-sm"></div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($currentTheme === $key): ?>
                                    <div class="absolute top-3 right-3 bg-white shadow-lg rounded-full p-1.5 z-20">
                                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-5 h-5 rounded-full" style="background-color: <?= e($theme['primary']) ?>">
                                    </div>
                                    <span class="font-medium text-slate-800">
                                        <?= e($theme['name']) ?>
                                    </span>
                                </div>
                                <p class="text-sm text-slate-500">
                                    <?= e($theme['description']) ?>
                                </p>
                            </div>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
            <div class="mt-6">
                <button onclick="saveTheme()"
                    class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                    Simpan Tema
                </button>
            </div>
        </div>
    </div>

    <!-- General Settings -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-lg font-semibold text-slate-800">Pengaturan Umum</h2>
        </div>
        <form id="settingsForm" class="p-6 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Meta Description</label>
                <input type="text" name="meta_description" value="<?= e($settings['meta_description'] ?? '') ?>"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Teks Footer</label>
                <input type="text" name="footer_text" value="<?= e($settings['footer_text'] ?? '') ?>"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <p class="text-xs text-slate-500 mt-1">
                    Gunakan <code>{year}</code> untuk tahun otomatis dan <code>{school}</code> untuk nama sekolah.
                    <br>Contoh: <i>&copy; {year} {school}. All rights reserved.</i>
                </p>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <button type="submit"
                    class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

    <!-- Social Media Settings -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center gap-2">
            <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
            </svg>
            <h2 class="text-lg font-semibold text-slate-800">Media Sosial</h2>
        </div>
        <form id="socialForm" class="p-6 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Facebook URL</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-slate-500 font-bold text-lg">f</span>
                        </div>
                        <input type="text" name="social_facebook" value="<?= e($settings['social_facebook'] ?? '') ?>"
                            placeholder="https://facebook.com/namasekolah"
                            class="w-full pl-8 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Instagram URL</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input type="text" name="social_instagram" value="<?= e($settings['social_instagram'] ?? '') ?>"
                            placeholder="https://instagram.com/namasekolah"
                            class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Twitter / X URL</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-slate-500 font-bold">X</span>
                        </div>
                        <input type="text" name="social_twitter" value="<?= e($settings['social_twitter'] ?? '') ?>"
                            placeholder="https://twitter.com/namasekolah"
                            class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">YouTube URL</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <input type="text" name="social_youtube" value="<?= e($settings['social_youtube'] ?? '') ?>"
                            placeholder="https://youtube.com/@namasekolah"
                            class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <button type="submit"
                    class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                    Simpan Sosial Media
                </button>
            </div>
        </form>
    </div>

    <!-- Maintenance Mode Settings -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-orange-50/50">
            <h2 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Maintenance Mode
            </h2>
            <p class="text-slate-500 text-sm mt-1">Aktifkan mode perbaikan untuk menutup akses publik sementara</p>
        </div>
        <form id="maintenanceForm" class="p-6 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">

            <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-lg border border-slate-200">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="maintenance_mode" value="1" class="sr-only peer"
                        <?= ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' ?>>
                    <div
                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500">
                    </div>
                    <span class="ml-3 text-sm font-medium text-slate-700">Aktifkan Maintenance Mode</span>
                </label>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Pesa Maintenance</label>
                <textarea name="maintenance_message" rows="3"
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                    placeholder="Mohon maaf, website sedang dalam perbaikan..."><?= e($settings['maintenance_message'] ?? '') ?></textarea>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <button type="submit"
                    class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-medium rounded-lg transition-colors">
                    Simpan Pengaturan Maintenance
                </button>
            </div>
        </form>
    </div>



    <script>
        function saveTheme() {
            const theme = document.querySelector('input[name="theme"]:checked')?.value;
            if (!theme) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            fetch('/admin/pengaturan/theme', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ theme })
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Gagal menyimpan tema'
                    });
                }
            });
        }

        document.getElementById('settingsForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            fetch('/admin/pengaturan/update', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Gagal menyimpan pengaturan'
                    });
                }
            });
        });

        document.getElementById('socialForm')?.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            fetch('/admin/pengaturan/update', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Pengaturan Media Sosial berhasil disimpan',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Gagal menyimpan media sosial'
                    });
                }
            });
        });


        document.getElementById('maintenanceForm')?.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            // Handle checkbox manual addition if unchecked (optional but good practice)
            if (!formData.has('maintenance_mode')) {
                formData.append('maintenance_mode', '0');
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            fetch('/admin/pengaturan/update', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Pengaturan Maintenance Mode berhasil disimpan',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Gagal menyimpan pengaturan'
                    });
                }
            });
        });
    </script>