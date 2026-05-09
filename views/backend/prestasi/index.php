<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Kelola Prestasi</h1>
            <p class="text-slate-500 mt-1">Daftar semua prestasi sekolah, guru, dan murid</p>
        </div>
        <a href="/admin/prestasi/tambah"
            class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Prestasi
        </a>
    </div>

    <?php if (isset($flash)): ?>
        <div
            class="p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800' ?>">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
    <?php endif; ?>

    <!-- Prestasi List -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 text-sm font-semibold text-slate-900">Judul & Gambar</th>
                        <th class="px-6 py-4 text-sm font-semibold text-slate-900">Kategori</th>
                        <th class="px-6 py-4 text-sm font-semibold text-slate-900">Tanggal</th>
                        <th class="px-6 py-4 text-sm font-semibold text-slate-900">Penulis</th>
                        <th class="px-6 py-4 text-sm font-semibold text-slate-900 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php if (empty($prestasi)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                Belum ada data prestasi.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($prestasi as $item): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <?php if (!empty($item['image'])): ?>
                                            <img src="/storage/<?= htmlspecialchars($item['image']) ?>"
                                                alt="<?= htmlspecialchars($item['title']) ?>"
                                                class="w-16 h-16 object-cover rounded-lg">
                                        <?php else: ?>
                                            <div
                                                class="w-16 h-16 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <h3 class="font-medium text-slate-900 line-clamp-1">
                                                <?= htmlspecialchars($item['title']) ?>
                                            </h3>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $item['category'] === 'Sekolah' ? 'bg-blue-100 text-blue-800' : ($item['category'] === 'Guru' ? 'bg-purple-100 text-purple-800' : 'bg-amber-100 text-amber-800') ?>">
                                        <?= htmlspecialchars($item['category']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    <?= date('d M Y', strtotime($item['date'])) ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    <?= htmlspecialchars($item['author_name'] ?? 'Sistem') ?>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="/admin/prestasi/edit/<?= $item['id'] ?>"
                                        class="inline-flex items-center p-2 text-slate-400 hover:text-primary-600 transition-colors"
                                        title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <button type="button" onclick="deletePrestasi(<?= $item['id'] ?>)"
                                        class="inline-flex items-center p-2 text-slate-400 hover:text-red-600 transition-colors"
                                        title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function deletePrestasi(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus data prestasi ini?')) return;

        fetch(`/admin/prestasi/delete/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `csrf_token=${document.querySelector('meta[name="csrf-token"]').content}`
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Gagal menghapus data');
                }
            })
            .catch(() => alert('Terjadi kesalahan sistem'));
    }
</script>