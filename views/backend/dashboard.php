<?php
$topContent = $topContent ?? [];
?>

<!-- Dashboard Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total News -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/50 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-slate-500 text-sm font-medium">Total Berita</p>
                <p class="text-3xl font-bold text-slate-800 mt-1">
                    <?= e($stats['news_count'] ?? 0) ?>
                </p>
            </div>
            <div
                class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
            <span class="text-green-500 font-medium flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <?= e($stats['news_published'] ?? 0) ?> published
            </span>
        </div>
    </div>

    <!-- Total Users -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/50 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-slate-500 text-sm font-medium">Total Pengguna</p>
                <p class="text-3xl font-bold text-slate-800 mt-1">
                    <?= e($stats['user_count'] ?? 0) ?>
                </p>
            </div>
            <div
                class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
            <span class="text-slate-500">Aktif semua</span>
        </div>
    </div>

    <!-- Visitors -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/50 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-slate-500 text-sm font-medium">Pengunjung Hari Ini</p>
                <p class="text-3xl font-bold text-slate-800 mt-1">
                    <?= e($stats['visitors_today'] ?? 0) ?>
                </p>
            </div>
            <div
                class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/30">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
            <span class="text-slate-500">
                <?= e($stats['page_views_today'] ?? 0) ?> halaman dilihat
            </span>
        </div>
    </div>

    <!-- Storage -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/50 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-slate-500 text-sm font-medium">Penyimpanan</p>
                <p class="text-3xl font-bold text-slate-800 mt-1">
                    <?= e($stats['storage_size'] ?? '0 B') ?>
                </p>
            </div>
            <div
                class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/30">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                </svg>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
            <span class="text-slate-500">Total file storage</span>
        </div>
    </div>
</div>

<!-- Dashboard Content -->
<div class="grid lg:grid-cols-3 gap-6">
    <!-- Top Content -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/50 lg:col-span-3">
        <div class="flex items-center justify-between p-6 border-b border-slate-200">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">Konten Paling Banyak Dikunjungi</h2>
                <p class="text-sm text-slate-500 mt-1">Berdasarkan kunjungan 30 hari terakhir</p>
            </div>
        </div>
        <div class="divide-y divide-slate-100">
            <?php if (!empty($topContent)): ?>
                <?php foreach ($topContent as $index => $item): ?>
                    <?php
                    $contentLabel = match ($item['content_type'] ?? '') {
                        'news' => 'Berita',
                        'news_index' => 'Daftar Berita',
                        'gallery' => 'Galeri',
                        'gallery_album' => 'Album Galeri',
                        'profile' => 'Profil',
                        'gtk' => 'GTK',
                        'prestasi' => 'Prestasi',
                        'contact' => 'Kontak',
                        'spmb' => 'SPMB',
                        'spmb_register' => 'Form SPMB',
                        'spmb_status' => 'Cek Status SPMB',
                        default => 'Halaman',
                    };
                    ?>
                    <a href="<?= e($item['path'] ?? '#') ?>" target="_blank"
                        class="p-4 flex items-center gap-4 hover:bg-slate-50 transition-colors">
                        <div
                            class="w-10 h-10 rounded-xl bg-primary-50 text-primary-700 font-bold flex items-center justify-center">
                            <?= $index + 1 ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-slate-800 truncate">
                                <?= e($item['title'] ?? '-') ?>
                            </div>
                            <div class="text-xs text-slate-500 mt-1">
                                <?= e($contentLabel) ?> · <?= e($item['path'] ?? '-') ?>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-slate-800">
                                <?= e($item['views'] ?? 0) ?> views
                            </div>
                            <div class="text-xs text-slate-500">
                                <?= e($item['visitors'] ?? 0) ?> pengunjung
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="p-8 text-center text-slate-500">
                    Belum ada data kunjungan. Data akan muncul setelah halaman frontend dikunjungi.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent News Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/50 lg:col-span-2">
        <div class="flex items-center justify-between p-6 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-800">Berita Terbaru</h2>
            <a href="/admin/berita" class="text-primary-600 hover:text-primary-700 text-sm font-medium">Lihat Semua</a>
        </div>
        <div class="divide-y divide-slate-100">
            <?php if (!empty($recentNews)): ?>
                <?php foreach ($recentNews as $item): ?>
                    <div class="p-4 hover:bg-slate-50 transition-colors">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-primary-100 to-primary-200 rounded-lg flex-shrink-0 flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-slate-800 truncate">
                                    <?= e($item['title']) ?>
                                </h4>
                                <p class="text-xs text-slate-500 mt-1">
                                    <?= date('d M Y H:i', strtotime($item['created_at'])) ?>
                                </p>
                            </div>
                            <span
                                class="px-2 py-1 text-xs font-medium rounded-full <?= $item['status'] === 'published' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' ?>">
                                <?= ucfirst($item['status']) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="p-8 text-center text-slate-500">
                    Belum ada berita
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/50">
        <div class="p-6 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-800">Aksi Cepat</h2>
        </div>
        <div class="p-6 grid grid-cols-2 gap-4">
            <a href="/admin/berita/create"
                class="flex flex-col items-center p-6 rounded-xl border-2 border-dashed border-slate-200 hover:border-primary-300 hover:bg-primary-50 transition-all group">
                <div
                    class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center group-hover:bg-primary-200 transition-colors">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <span class="mt-3 text-sm font-medium text-slate-700 group-hover:text-primary-700">Tambah Berita</span>
            </a>

            <a href="/admin/galeri"
                class="flex flex-col items-center p-6 rounded-xl border-2 border-dashed border-slate-200 hover:border-emerald-300 hover:bg-emerald-50 transition-all group">
                <div
                    class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center group-hover:bg-emerald-200 transition-colors">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <span class="mt-3 text-sm font-medium text-slate-700 group-hover:text-emerald-700">Upload Galeri</span>
            </a>

            <a href="/admin/profil"
                class="flex flex-col items-center p-6 rounded-xl border-2 border-dashed border-slate-200 hover:border-amber-300 hover:bg-amber-50 transition-all group">
                <div
                    class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center group-hover:bg-amber-200 transition-colors">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <span class="mt-3 text-sm font-medium text-slate-700 group-hover:text-amber-700">Edit Profil</span>
            </a>

            <a href="/" target="_blank"
                class="flex flex-col items-center p-6 rounded-xl border-2 border-dashed border-slate-200 hover:border-purple-300 hover:bg-purple-50 transition-all group">
                <div
                    class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </div>
                <span class="mt-3 text-sm font-medium text-slate-700 group-hover:text-purple-700">Lihat Website</span>
            </a>
        </div>
    </div>
</div>
