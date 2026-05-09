<div class="max-w-4xl">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
        <a href="/admin/prestasi"
            class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                <?= htmlspecialchars($title) ?>
            </h1>
            <p class="text-slate-500 mt-1">Isi formulir di bawah ini dengan lengkap dan benar.</p>
        </div>
    </div>

    <?php if (isset($_SESSION['flash'])): ?>
        <div
            class="mb-6 p-4 rounded-lg <?= $_SESSION['flash']['type'] === 'success' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800' ?>">
            <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <form action="/admin/prestasi/save" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            <?= Security::csrfInput() ?>

            <?php if ($prestasi): ?>
                <input type="hidden" name="id" value="<?= $prestasi['id'] ?>">
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="md:col-span-2 space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-slate-700 mb-1">Judul Prestasi <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="title" name="title" required
                            value="<?= $prestasi ? htmlspecialchars($prestasi['title']) : '' ?>"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all"
                            placeholder="Contoh: Juara 1 Lomba OSN Tingkat Provinsi">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                        <textarea id="description" name="description" rows="4"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all"
                            placeholder="Tuliskan keterangan lebih lanjut tentang prestasi ini..."><?= $prestasi ? htmlspecialchars($prestasi['description']) : '' ?></textarea>
                    </div>
                </div>

                <!-- Sidebar Content -->
                <div class="space-y-6">
                    <div>
                        <label for="category" class="block text-sm font-medium text-slate-700 mb-1">Kategori <span
                                class="text-red-500">*</span></label>
                        <select id="category" name="category" required
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all">
                            <option value="">Pilih Kategori</option>
                            <option value="Sekolah" <?= ($prestasi && $prestasi['category'] === 'Sekolah') ? 'selected' : '' ?>>Sekolah</option>
                            <option value="Guru" <?= ($prestasi && $prestasi['category'] === 'Guru') ? 'selected' : '' ?>
                                >Guru</option>
                            <option value="Murid" <?= ($prestasi && $prestasi['category'] === 'Murid') ? 'selected' : '' ?>>Murid</option>
                        </select>
                    </div>

                    <div>
                        <label for="date" class="block text-sm font-medium text-slate-700 mb-1">Tanggal Prestasi <span
                                class="text-red-500">*</span></label>
                        <input type="date" id="date" name="date" required
                            value="<?= $prestasi ? htmlspecialchars($prestasi['date']) : date('Y-m-d') ?>"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all">
                    </div>

                    <div>
                        <label for="image"
                            class="block text-sm font-medium text-slate-700 mb-1">Gambar/Sertifikat</label>
                        <?php if ($prestasi && !empty($prestasi['image'])): ?>
                            <div class="mb-3 relative group rounded-lg overflow-hidden border border-slate-200">
                                <img src="/storage/<?= htmlspecialchars($prestasi['image']) ?>" alt="Preview"
                                    class="w-full h-auto">
                            </div>
                        <?php endif; ?>
                        <div class="relative">
                            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp" class="block w-full text-sm text-slate-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-lg file:border-0
                                file:text-sm file:font-semibold
                                file:bg-primary-50 file:text-primary-700
                                hover:file:bg-primary-100 transition-all cursor-pointer">
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Format: JPG, PNG, GIF, WebP. Maks 5MB.</p>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-200 flex justify-end gap-3">
                <a href="/admin/prestasi"
                    class="px-6 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors font-medium">Batal</a>
                <button type="submit"
                    class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium">
                    Simpan Prestasi
                </button>
            </div>
        </form>
    </div>
</div>