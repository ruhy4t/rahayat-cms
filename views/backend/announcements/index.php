<?php
$items = $announcements ?? [];
$popupItems = array_values(array_filter($items, fn($item) => $item['type'] === 'popup'));
$textItems = array_values(array_filter($items, fn($item) => $item['type'] === 'text_slider'));

$dateInput = static function (?string $value): string {
    return $value ? date('Y-m-d\TH:i', strtotime($value)) : '';
};

$statusLabel = static function (array $item): array {
    if (empty($item['is_active'])) {
        return ['Nonaktif', 'bg-slate-100 text-slate-600'];
    }
    $now = time();
    if (!empty($item['start_at']) && strtotime($item['start_at']) > $now) {
        return ['Terjadwal', 'bg-amber-100 text-amber-700'];
    }
    if (!empty($item['end_at']) && strtotime($item['end_at']) < $now) {
        return ['Berakhir', 'bg-red-100 text-red-700'];
    }
    return ['Tayang', 'bg-emerald-100 text-emerald-700'];
};
?>

<div class="space-y-7">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Informasi Publik</h1>
        <p class="mt-1 text-slate-500">Kelola popup terjadwal dan slider teks yang tampil di website.</p>
    </div>

    <?php if (!empty($flash)): ?>
        <div class="p-4 rounded-xl border <?= $flash['type'] === 'success'
            ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
            : 'bg-red-50 border-red-200 text-red-800' ?>">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <details class="group bg-white border border-slate-200 rounded-2xl shadow-sm" open>
        <summary class="cursor-pointer list-none flex items-center justify-between gap-4 px-5 sm:px-6 py-5">
            <div>
                <h2 class="font-semibold text-slate-800">Tambah Informasi</h2>
                <p class="text-sm text-slate-500">Rentang tanggal boleh dikosongkan agar selalu berlaku.</p>
            </div>
            <span class="text-primary-600 text-sm font-medium group-open:hidden">Buka formulir</span>
        </summary>
        <form action="/admin/informasi/store" method="POST" enctype="multipart/form-data"
            class="border-t border-slate-100 p-5 sm:p-6">
            <?= Security::csrfInput() ?>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jenis</label>
                    <select name="type" data-information-type
                        class="w-full rounded-xl border-slate-300 focus:border-primary-500 focus:ring-primary-500">
                        <option value="popup">Popup informasi</option>
                        <option value="text_slider">Slider teks</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Judul</label>
                    <input name="title" maxlength="180"
                        class="w-full rounded-xl border-slate-300 focus:border-primary-500 focus:ring-primary-500"
                        placeholder="Contoh: Pengumuman Penting">
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Isi informasi <span class="text-red-500">*</span></label>
                    <textarea name="content" rows="4" required
                        class="w-full rounded-xl border-slate-300 focus:border-primary-500 focus:ring-primary-500"
                        placeholder="Tuliskan informasi yang akan ditampilkan"></textarea>
                </div>
                <div data-popup-image>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Foto popup</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp"
                        class="w-full rounded-xl border border-slate-300 p-2 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-3 file:py-2 file:text-primary-700">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Urutan</label>
                    <input type="number" name="sort_order" value="0" min="0"
                        class="w-full rounded-xl border-slate-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Mulai tayang</label>
                    <input type="datetime-local" name="start_at"
                        class="w-full rounded-xl border-slate-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Selesai tayang</label>
                    <input type="datetime-local" name="end_at"
                        class="w-full rounded-xl border-slate-300 focus:border-primary-500 focus:ring-primary-500">
                </div>
            </div>
            <div class="mt-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" checked
                        class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                    Aktifkan informasi
                </label>
                <button class="px-5 py-2.5 rounded-xl bg-primary-600 text-white font-medium hover:bg-primary-700">
                    Simpan Informasi
                </button>
            </div>
        </form>
    </details>

    <?php foreach ([
        ['Popup Informasi', 'Popup dapat berisi teks dan foto serta muncul setiap kali beranda dibuka atau direfresh.', $popupItems],
        ['Slider Teks', 'Semua slider teks aktif akan bergerak bergantian di bawah navigasi.', $textItems],
    ] as [$sectionTitle, $sectionDescription, $sectionItems]): ?>
        <section class="space-y-4">
            <div>
                <h2 class="text-lg font-semibold text-slate-800"><?= e($sectionTitle) ?></h2>
                <p class="text-sm text-slate-500"><?= e($sectionDescription) ?></p>
            </div>

            <?php if (empty($sectionItems)): ?>
                <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-white p-8 text-center text-slate-500">
                    Belum ada <?= strtolower(e($sectionTitle)) ?>.
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                <?php foreach ($sectionItems as $item): ?>
                    <?php [$label, $labelClass] = $statusLabel($item); ?>
                    <details class="group bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        <summary class="cursor-pointer list-none p-5">
                            <div class="flex gap-4">
                                <?php if (!empty($item['image'])): ?>
                                    <img src="/storage/<?= e($item['image']) ?>" alt=""
                                        class="w-20 h-20 rounded-xl object-cover shrink-0">
                                <?php endif; ?>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="font-semibold text-slate-800 truncate">
                                            <?= e($item['title'] ?: ($item['type'] === 'popup' ? 'Popup tanpa judul' : 'Slider teks')) ?>
                                        </h3>
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $labelClass ?>"><?= e($label) ?></span>
                                    </div>
                                    <p class="mt-1 text-sm text-slate-600 line-clamp-2"><?= e($item['content']) ?></p>
                                    <p class="mt-2 text-xs text-slate-400">
                                        <?= $item['start_at'] ? date('d/m/Y H:i', strtotime($item['start_at'])) : 'Tanpa tanggal mulai' ?>
                                        —
                                        <?= $item['end_at'] ? date('d/m/Y H:i', strtotime($item['end_at'])) : 'Tanpa tanggal selesai' ?>
                                    </p>
                                </div>
                            </div>
                            <p class="mt-3 text-xs font-medium text-primary-600 group-open:hidden">Klik untuk mengedit</p>
                        </summary>

                        <form action="/admin/informasi/update/<?= (int) $item['id'] ?>" method="POST"
                            enctype="multipart/form-data" class="border-t border-slate-100 p-5 space-y-4">
                            <?= Security::csrfInput() ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Jenis</label>
                                    <select name="type" data-information-type class="w-full rounded-xl border-slate-300">
                                        <option value="popup" <?= $item['type'] === 'popup' ? 'selected' : '' ?>>Popup informasi</option>
                                        <option value="text_slider" <?= $item['type'] === 'text_slider' ? 'selected' : '' ?>>Slider teks</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Judul</label>
                                    <input name="title" maxlength="180" value="<?= e($item['title'] ?? '') ?>"
                                        class="w-full rounded-xl border-slate-300">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Isi informasi</label>
                                    <textarea name="content" rows="3" required
                                        class="w-full rounded-xl border-slate-300"><?= e($item['content']) ?></textarea>
                                </div>
                                <div data-popup-image class="<?= $item['type'] === 'text_slider' ? 'hidden' : '' ?>">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Ganti foto</label>
                                    <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp"
                                        class="w-full rounded-xl border border-slate-300 p-2 text-sm">
                                    <?php if (!empty($item['image'])): ?>
                                        <label class="mt-2 inline-flex items-center gap-2 text-xs text-slate-600">
                                            <input type="checkbox" name="remove_image" value="1" class="rounded border-slate-300">
                                            Hapus foto saat ini
                                        </label>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Urutan</label>
                                    <input type="number" name="sort_order" min="0" value="<?= (int) $item['sort_order'] ?>"
                                        class="w-full rounded-xl border-slate-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Mulai tayang</label>
                                    <input type="datetime-local" name="start_at" value="<?= e($dateInput($item['start_at'])) ?>"
                                        class="w-full rounded-xl border-slate-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Selesai tayang</label>
                                    <input type="datetime-local" name="end_at" value="<?= e($dateInput($item['end_at'])) ?>"
                                        class="w-full rounded-xl border-slate-300">
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                    <input type="checkbox" name="is_active" value="1" <?= $item['is_active'] ? 'checked' : '' ?>
                                        class="rounded border-slate-300 text-primary-600">
                                    Aktif
                                </label>
                                <div class="flex gap-2">
                                    <button type="submit" formaction="/admin/informasi/delete/<?= (int) $item['id'] ?>"
                                        onclick="return confirm('Hapus informasi ini?')"
                                        class="px-4 py-2 rounded-xl text-red-600 bg-red-50 hover:bg-red-100">
                                        Hapus
                                    </button>
                                    <button class="px-4 py-2 rounded-xl text-white bg-primary-600 hover:bg-primary-700">
                                        Simpan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </details>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
</div>

<script>
    function syncInformationType(select) {
        const form = select.closest('form');
        const imageField = form?.querySelector('[data-popup-image]');
        imageField?.classList.toggle('hidden', select.value !== 'popup');
    }

    document.querySelectorAll('[data-information-type]').forEach(select => {
        syncInformationType(select);
        select.addEventListener('change', () => syncInformationType(select));
    });
</script>
