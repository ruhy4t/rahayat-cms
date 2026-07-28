<?php
$testimonials = $data['testimonials'] ?? [];
$relationships = $data['relationships'] ?? [];
$flash = $data['flash'] ?? null;
$statusLabels = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Kelola Testimoni</h1>
            <p class="text-sm text-slate-500 mt-1">Moderasi kiriman publik atau tambahkan testimoni secara manual.</p>
        </div>
        <button type="button" onclick="openTestimonialModal()"
            class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition-colors">
            Tambah Testimoni
        </button>
    </div>

    <?php if ($flash): ?>
        <div class="p-4 rounded-lg border <?= ($flash['type'] ?? '') === 'success'
            ? 'bg-green-50 border-green-200 text-green-800'
            : 'bg-red-50 border-red-200 text-red-800' ?>">
            <?= e($flash['message'] ?? '') ?>
        </div>
    <?php endif; ?>

    <div class="grid sm:grid-cols-3 gap-4">
        <?php foreach (['pending', 'approved', 'rejected'] as $summaryStatus): ?>
            <?php $count = count(array_filter($testimonials, fn($item) => $item['status'] === $summaryStatus)); ?>
            <div class="bg-white border border-slate-200 rounded-xl p-5">
                <p class="text-sm text-slate-500"><?= e($statusLabels[$summaryStatus]) ?></p>
                <p class="text-3xl font-bold text-slate-800 mt-1"><?= $count ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <?php if (empty($testimonials)): ?>
            <div class="py-14 text-center text-slate-500">Belum ada testimoni.</div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                        <tr>
                            <th class="text-left px-5 py-3 font-semibold">Pemberi Testimoni</th>
                            <th class="text-left px-5 py-3 font-semibold">Testimoni</th>
                            <th class="text-left px-5 py-3 font-semibold">Status</th>
                            <th class="text-right px-5 py-3 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($testimonials as $item): ?>
                            <tr class="hover:bg-slate-50 align-top">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3 min-w-[210px]">
                                        <?php if (!empty($item['photo'])): ?>
                                            <img src="/storage/<?= e($item['photo']) ?>" alt=""
                                                class="w-11 h-11 rounded-full object-cover bg-slate-100" loading="lazy">
                                        <?php else: ?>
                                            <div class="w-11 h-11 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold">
                                                <?= e(mb_strtoupper(mb_substr($item['name'], 0, 1))) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <p class="font-semibold text-slate-800"><?= e($item['name']) ?></p>
                                            <p class="text-xs text-slate-500"><?= e($item['relationship']) ?></p>
                                            <?php if (!empty($item['contact'])): ?>
                                                <p class="text-xs text-slate-400 mt-1">Kontak: <?= e($item['contact']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 max-w-md">
                                    <p class="text-slate-600 line-clamp-3"><?= e($item['testimonial']) ?></p>
                                    <div class="flex gap-2 mt-2 text-xs text-slate-400">
                                        <?php if (!empty($item['is_featured'])): ?><span>★ Unggulan</span><?php endif; ?>
                                        <span><?= date('d M Y', strtotime($item['created_at'])) ?></span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <?php
                                    $statusClass = $item['status'] === 'approved'
                                        ? 'bg-green-100 text-green-700'
                                        : ($item['status'] === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700');
                                    ?>
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold <?= $statusClass ?>">
                                        <?= e($statusLabels[$item['status']] ?? $item['status']) ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2 min-w-[190px]">
                                        <?php if ($item['status'] !== 'approved'): ?>
                                            <form action="/admin/testimoni/status/<?= (int) $item['id'] ?>" method="POST">
                                                <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">
                                                <input type="hidden" name="status" value="approved">
                                                <button class="px-2.5 py-1.5 bg-green-50 text-green-700 hover:bg-green-100 rounded-lg font-medium">Setujui</button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($item['status'] === 'pending'): ?>
                                            <form action="/admin/testimoni/status/<?= (int) $item['id'] ?>" method="POST">
                                                <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">
                                                <input type="hidden" name="status" value="rejected">
                                                <button class="px-2.5 py-1.5 bg-red-50 text-red-700 hover:bg-red-100 rounded-lg font-medium">Tolak</button>
                                            </form>
                                        <?php endif; ?>
                                        <button type="button" onclick='editTestimonial(<?= e(json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT)) ?>)'
                                            class="px-2.5 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-lg font-medium">
                                            Edit
                                        </button>
                                        <form action="/admin/testimoni/delete/<?= (int) $item['id'] ?>" method="POST"
                                            onsubmit="return confirm('Hapus testimoni ini secara permanen?')">
                                            <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">
                                            <button class="px-2.5 py-1.5 bg-slate-100 text-slate-600 hover:bg-red-100 hover:text-red-700 rounded-lg font-medium">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div id="testimonialModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/60">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[92vh] overflow-y-auto">
        <form id="testimonialForm" action="/admin/testimoni/save" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">
            <input type="hidden" name="id" id="testimonialId" value="">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h2 id="testimonialModalTitle" class="text-lg font-bold text-slate-800">Tambah Testimoni</h2>
                <button type="button" onclick="closeTestimonialModal()" class="text-slate-400 hover:text-slate-700">✕</button>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama *</label>
                        <input id="testimonialName" type="text" name="name" maxlength="100" required
                            class="w-full px-3 py-2 border-slate-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Kategori *</label>
                        <select id="testimonialRelationship" name="relationship" required
                            class="w-full px-3 py-2 border-slate-300 rounded-lg">
                            <?php foreach ($relationships as $relationship): ?>
                                <option value="<?= e($relationship) ?>"><?= e($relationship) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tahun Lulus</label>
                        <input id="testimonialYear" type="number" name="graduation_year" min="1950"
                            max="<?= (int) date('Y') + 1 ?>" class="w-full px-3 py-2 border-slate-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Pekerjaan/Instansi</label>
                        <input id="testimonialOccupation" type="text" name="occupation" maxlength="120"
                            class="w-full px-3 py-2 border-slate-300 rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Testimoni *</label>
                    <textarea id="testimonialContent" name="testimonial" rows="5" minlength="20" maxlength="1200" required
                        class="w-full px-3 py-2 border-slate-300 rounded-lg"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kontak Privat</label>
                    <input id="testimonialContact" type="text" name="contact" maxlength="120"
                        class="w-full px-3 py-2 border-slate-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Foto/Avatar</label>
                    <input type="file" name="photo" accept="image/jpeg,image/png,image/webp"
                        class="w-full text-sm border border-slate-300 rounded-lg p-2">
                    <p class="text-xs text-slate-500 mt-1">JPG, PNG, atau WebP. Maksimal 2 MB.</p>
                    <label id="removePhotoLabel" class="hidden items-center gap-2 mt-2 text-sm text-red-600">
                        <input type="checkbox" name="remove_photo" value="1"> Hapus foto saat ini
                    </label>
                </div>
                <div class="grid sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                        <select id="testimonialStatus" name="status" class="w-full px-3 py-2 border-slate-300 rounded-lg">
                            <option value="pending">Menunggu</option>
                            <option value="approved" selected>Disetujui</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Urutan</label>
                        <input id="testimonialOrder" type="number" name="sort_order" min="0" value="0"
                            class="w-full px-3 py-2 border-slate-300 rounded-lg">
                    </div>
                    <label class="flex items-center gap-2 sm:mt-7">
                        <input id="testimonialFeatured" type="checkbox" name="is_featured" value="1"
                            class="rounded text-primary-600">
                        <span class="text-sm font-medium text-slate-700">Testimoni unggulan</span>
                    </label>
                </div>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                <button type="button" onclick="closeTestimonialModal()"
                    class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg font-semibold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    const testimonialModal = document.getElementById('testimonialModal');
    const testimonialForm = document.getElementById('testimonialForm');

    function openTestimonialModal() {
        testimonialForm.reset();
        document.getElementById('testimonialId').value = '';
        document.getElementById('testimonialModalTitle').textContent = 'Tambah Testimoni';
        document.getElementById('testimonialStatus').value = 'approved';
        document.getElementById('removePhotoLabel').classList.add('hidden');
        document.getElementById('removePhotoLabel').classList.remove('flex');
        testimonialModal.classList.remove('hidden');
        testimonialModal.classList.add('flex');
    }

    function editTestimonial(item) {
        document.getElementById('testimonialId').value = item.id;
        document.getElementById('testimonialName').value = item.name || '';
        document.getElementById('testimonialRelationship').value = item.relationship || '';
        document.getElementById('testimonialYear').value = item.graduation_year || '';
        document.getElementById('testimonialOccupation').value = item.occupation || '';
        document.getElementById('testimonialContent').value = item.testimonial || '';
        document.getElementById('testimonialContact').value = item.contact || '';
        document.getElementById('testimonialStatus').value = item.status || 'pending';
        document.getElementById('testimonialOrder').value = item.sort_order || 0;
        document.getElementById('testimonialFeatured').checked = item.is_featured == 1;
        document.getElementById('testimonialModalTitle').textContent = 'Edit Testimoni';
        const removeLabel = document.getElementById('removePhotoLabel');
        removeLabel.classList.toggle('hidden', !item.photo);
        removeLabel.classList.toggle('flex', !!item.photo);
        testimonialModal.classList.remove('hidden');
        testimonialModal.classList.add('flex');
    }

    function closeTestimonialModal() {
        testimonialModal.classList.add('hidden');
        testimonialModal.classList.remove('flex');
    }

    testimonialModal.addEventListener('click', event => {
        if (event.target === testimonialModal) closeTestimonialModal();
    });
</script>
