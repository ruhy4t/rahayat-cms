<div class="space-y-6">
    <h1 class="text-2xl font-bold text-slate-800">Migrasi Keamanan</h1>
    <p>Enkripsi data dan dokumen SPMB lama serta pembersihan HTML berita. Proses dapat dilanjutkan jika terputus, tanpa Terminal hosting.</p>
    <?php if ($flash): ?>
        <div role="status" class="p-4 rounded-lg border <?= $flash['type'] === 'success' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800' ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>
    <?php if ($result): ?>
        <div class="bg-white rounded-xl p-6 border space-y-2">
            <p>Tahap terakhir: <?= (int) $result['rows'] ?> pendaftar, <?= (int) $result['documents'] ?> dokumen, <?= (int) $result['news'] ?> berita diperbarui.</p>
            <?php if ($result['backup']): ?><p>Backup terverifikasi: <code><?= e($result['backup']) ?></code></p><?php endif; ?>
            <p><?= $result['complete'] ? 'Seluruh tahap selesai. Simpan semua folder backup migrasi melalui File Manager.' : 'Masih ada tahap berikutnya. Lanjutkan sampai muncul pesan selesai.' ?></p>
        </div>
    <?php endif; ?>
    <form method="post" action="/admin/pembaruan/migrasi/run" class="bg-white rounded-xl p-6 border space-y-4">
        <?= Security::csrfInput() ?>
        <p>Setiap tahap memproses maksimal 5 pendaftar, 1 dokumen, dan 10 berita. Backup perubahan dibuat dan diperiksa sebelum data ditulis.</p>
        <label class="flex items-start gap-2"><input type="checkbox" name="backup_ready" value="1" required><span>Saya telah menyimpan backup lengkap database dan storage, serta kunci enkripsi hosting secara terpisah. File pembaruan selesai diunggah.</span></label>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg"><?= $result && !$result['complete'] ? 'Lanjutkan migrasi' : 'Jalankan pemeriksaan dan migrasi' ?></button>
    </form>
    <a href="/admin/pembaruan" class="text-indigo-600 underline">Kembali ke Pembaruan Sistem</a>
</div>
