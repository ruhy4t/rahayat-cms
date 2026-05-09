<?php
/**
 * Backend - News Management
 */
$categories = $data['categories'] ?? [];
?>

<!-- Header Actions -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <p class="text-slate-500 text-sm">Kelola berita dan pengumuman sekolah</p>
    </div>
    <a href="/admin/berita/create"
        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-medium rounded-lg hover:shadow-lg hover:shadow-primary-500/30 transition-all duration-200">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Berita
    </a>
</div>

<!-- News Table -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200/50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">Berita
                    </th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                        Kategori</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">Status
                    </th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                        Tanggal</th>
                    <th class="text-right px-6 py-4 text-xs font-semibold text-slate-600 uppercase tracking-wider">Aksi
                    </th>
                </tr>
            </thead>
            <tbody id="newsTableBody" class="divide-y divide-slate-100">
                <?php if (!empty($news)): ?>
                    <?php foreach ($news as $item): ?>
                        <tr class="hover:bg-slate-50 transition-colors" data-id="<?= e($item['id']) ?>">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-primary-100 to-primary-200 rounded-lg flex-shrink-0 overflow-hidden">
                                        <?php if (!empty($item['image'])): ?>
                                            <img src="/storage/<?= e($item['image']) ?>" alt="" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-primary-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-sm font-medium text-slate-800 truncate max-w-xs">
                                            <?= e($item['title']) ?>
                                        </h4>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            <?= e($item['author_name'] ?? 'Admin') ?>
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-medium bg-slate-100 text-slate-700 rounded-full">
                                    <?= e(ucfirst($item['category'])) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2.5 py-1 text-xs font-medium rounded-full <?= $item['status'] === 'published' ? 'bg-green-100 text-green-700' : ($item['status'] === 'draft' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700') ?>">
                                    <?= ucfirst($item['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                <?= date('d M Y', strtotime($item['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="/admin/berita/edit/<?= $item['id'] ?>"
                                        class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors"
                                        title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="/admin/berita/delete/<?= $item['id'] ?>" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus berita ini?');"
                                        class="inline-block">
                                        <?= Security::csrfInput() ?>
                                        <button type="submit"
                                            class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                            <p>Belum ada berita</p>
                            <a href="/admin/berita/create"
                                class="mt-3 text-primary-600 hover:text-primary-700 font-medium text-sm inline-block">Tambah
                                berita
                                pertama</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if (!empty($pagination) && $pagination['total_pages'] > 1): ?>
        <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between">
            <p class="text-sm text-slate-500">
                Menampilkan
                <?= (($pagination['current_page'] - 1) * $pagination['per_page']) + 1 ?> -
                <?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total']) ?> dari
                <?= $pagination['total'] ?> berita
            </p>
            <div class="flex items-center gap-2">
                <?php if ($pagination['current_page'] > 1): ?>
                    <a href="?page=<?= $pagination['current_page'] - 1 ?>"
                        class="px-3 py-1.5 text-sm text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">Prev</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <a href="?page=<?= $i ?>"
                        class="px-3 py-1.5 text-sm rounded-lg transition-colors <?= $i === $pagination['current_page'] ? 'bg-primary-600 text-white' : 'text-slate-600 hover:bg-slate-100' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                    <a href="?page=<?= $pagination['current_page'] + 1 ?>"
                        class="px-3 py-1.5 text-sm text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">Next</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>