<?php
/**
 * Backend - System Update
 */
$title = $data['title'] ?? 'Pembaruan Sistem';
$status = $data['updateStatus'] ?? [];
$manual = $status['manual_update'] ?? [];
$flash = $data['flash'] ?? null;
?>

<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800"><?= e($title) ?></h1>
        <p class="text-slate-500 mt-1">Cek pembaruan aplikasi dari GitHub dan pilih update otomatis atau manual.</p>
    </div>

    <?php if ($flash): ?>
        <div
            class="p-4 rounded-lg whitespace-pre-wrap <?= $flash['type'] === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200' ?>">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">Status Versi</h2>
                <p class="text-slate-500 text-sm mt-1">Server tanpa Git tetap bisa mengetahui versi terbaru lewat file versi publik.</p>
            </div>
            <?php if (!empty($status['update_available'])): ?>
                <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-sm font-medium">Update tersedia</span>
            <?php else: ?>
                <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm font-medium">Terbaru</span>
            <?php endif; ?>
        </div>

        <div class="p-6 grid md:grid-cols-2 gap-4">
            <div class="border border-slate-200 rounded-lg p-4">
                <div class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Versi Lokal</div>
                <div class="mt-2 text-slate-800 font-mono"><?= e($status['local_version'] ?? APP_VERSION) ?></div>
            </div>
            <div class="border border-slate-200 rounded-lg p-4">
                <div class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Versi GitHub</div>
                <div class="mt-2 text-slate-800 font-mono"><?= e($manual['remote_version'] ?? '-') ?></div>
            </div>
            <div class="border border-slate-200 rounded-lg p-4">
                <div class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Commit Lokal</div>
                <div class="mt-2 text-slate-800 font-mono"><?= e($status['current_short'] ?? '-') ?></div>
            </div>
            <div class="border border-slate-200 rounded-lg p-4">
                <div class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Commit GitHub</div>
                <div class="mt-2 text-slate-800 font-mono"><?= e($status['remote_short'] ?? '-') ?></div>
            </div>
            <div class="border border-slate-200 rounded-lg p-4">
                <div class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Branch</div>
                <div class="mt-2 text-slate-800"><?= e($status['branch'] ?? '-') ?> -> <?= e($status['target_branch'] ?? 'main') ?></div>
            </div>
            <div class="border border-slate-200 rounded-lg p-4">
                <div class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Repository</div>
                <div class="mt-2 text-slate-800 break-all"><?= e($status['remote_url'] ?? '-') ?></div>
            </div>
        </div>

        <div class="px-6 pb-6 space-y-3">
            <?php if (empty($status['enabled'])): ?>
                <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 text-sm text-slate-700">
                    Fitur eksekusi update belum aktif. Aktifkan dengan environment variable <code class="font-mono">UPDATE_ENABLED=true</code>.
                </div>
            <?php endif; ?>
            <?php if (empty($status['git_available'])): ?>
                <div class="p-4 rounded-lg bg-amber-50 border border-amber-200 text-sm text-amber-800">
                    Git tidak tersedia di server. Update otomatis tidak dapat dipakai, tetapi cek versi dan update manual tetap bisa dilakukan.
                </div>
            <?php endif; ?>
            <?php if (empty($manual['can_check'])): ?>
                <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
                    Tidak bisa mengecek versi terbaru dari GitHub: <?= e($manual['error'] ?? '-') ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($status['dirty'])): ?>
                <div class="p-4 rounded-lg bg-amber-50 border border-amber-200 text-sm text-amber-700">
                    Ada perubahan lokal pada file aplikasi. Update otomatis akan ditolak sampai perubahan itu dirapikan.
                </div>
            <?php endif; ?>
            <?php if (!empty($status['remote_error'])): ?>
                <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
                    Tidak bisa membaca remote: <?= e($status['remote_error']) ?>
                </div>
            <?php endif; ?>
            <?php if (($status['relation'] ?? '') === 'ahead'): ?>
                <div class="p-4 rounded-lg bg-blue-50 border border-blue-200 text-sm text-blue-700">
                    Commit lokal lebih baru dari GitHub. Push perubahan lokal terlebih dahulu agar server online punya sumber update yang sama.
                </div>
            <?php elseif (($status['relation'] ?? '') === 'diverged'): ?>
                <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
                    Riwayat lokal dan GitHub berbeda. Update otomatis ditolak; sinkronkan repository lewat Git manual.
                </div>
            <?php endif; ?>
        </div>

        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="/admin/pembaruan"
                class="px-4 py-2 border border-slate-200 text-slate-700 rounded-lg hover:bg-white transition-colors">Cek Ulang</a>
            <form action="/admin/pembaruan/run" method="POST"
                onsubmit="return confirm('Lanjutkan update aplikasi dari GitHub? Pastikan backup database sudah tersedia.');">
                <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">
                <button type="submit"
                    class="px-4 py-2 rounded-lg text-white transition-colors <?= !empty($status['can_update']) ? 'bg-primary-600 hover:bg-primary-700' : 'bg-slate-400 cursor-not-allowed' ?>"
                    <?= empty($status['can_update']) ? 'disabled' : '' ?>>
                    Jalankan Update
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-lg font-semibold text-slate-800">Update Manual Tanpa Git</h2>
            <p class="text-slate-500 text-sm mt-1">Gunakan langkah ini untuk shared hosting yang hanya menyediakan File Manager, FTP, atau SFTP.</p>
        </div>

        <div class="p-6 space-y-5 text-sm text-slate-700">
            <div class="grid md:grid-cols-3 gap-4">
                <a href="<?= e($manual['download_url'] ?? UPDATE_DOWNLOAD_URL) ?>" target="_blank"
                    class="block border border-slate-200 rounded-lg p-4 hover:bg-slate-50 transition-colors">
                    <div class="font-semibold text-slate-800">1. Download ZIP</div>
                    <div class="text-slate-500 mt-1">Ambil source terbaru dari GitHub.</div>
                </a>
                <a href="<?= e($manual['notes_url'] ?? 'https://github.com/ruhy4t/rahayat-cms/commits/main') ?>" target="_blank"
                    class="block border border-slate-200 rounded-lg p-4 hover:bg-slate-50 transition-colors">
                    <div class="font-semibold text-slate-800">2. Baca Perubahan</div>
                    <div class="text-slate-500 mt-1">Lihat commit terbaru sebelum update.</div>
                </a>
                <a href="<?= e($manual['repository_url'] ?? 'https://github.com/ruhy4t/rahayat-cms') ?>" target="_blank"
                    class="block border border-slate-200 rounded-lg p-4 hover:bg-slate-50 transition-colors">
                    <div class="font-semibold text-slate-800">3. Buka Repository</div>
                    <div class="text-slate-500 mt-1">Sumber resmi Rahayat CMS.</div>
                </a>
            </div>

            <div class="rounded-lg border border-slate-200 overflow-hidden">
                <div class="px-4 py-3 bg-slate-50 font-semibold text-slate-800">Langkah aman update manual</div>
                <ol class="list-decimal list-inside p-4 space-y-2">
                    <li>Backup database dari cPanel/phpMyAdmin.</li>
                    <li>Backup folder aplikasi lama atau compress folder domain menjadi ZIP.</li>
                    <li>Download ZIP terbaru dari tombol di atas.</li>
                    <li>Upload ZIP ke hosting lewat File Manager, FTP, atau SFTP.</li>
                    <li>Extract ke folder sementara, misalnya <code class="font-mono">rahayat-update</code>.</li>
                    <li>Pindahkan file dan folder aplikasi baru ke root domain.</li>
                    <li>Jangan timpa <code class="font-mono">config/local.php</code>, <code class="font-mono">storage/</code>, dan <code class="font-mono">public/uploads/</code>.</li>
                    <li>Buka halaman admin dan cek menu Dashboard, Pengaturan, Berita, serta halaman depan.</li>
                </ol>
            </div>

            <div class="p-4 rounded-lg bg-blue-50 border border-blue-200 text-blue-800">
                Jika ada file migrasi database baru di folder <code class="font-mono">database/migrations</code>, jalankan SQL tersebut lewat phpMyAdmin sebelum membuka fitur terkait.
            </div>
        </div>
    </div>
</div>
