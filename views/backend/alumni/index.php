<?php
$items = $data['alumniItems'] ?? [];
$statistics = $data['statistics'] ?? [];
$flash = $data['flash'] ?? null;
$labels = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
$continuationOptions = $data['continuationOptions'] ?? [];
$employmentStatuses = $data['employmentStatuses'] ?? [];
$chartData = [
    'continuation' => $statistics['continuation'] ?? [],
    'schoolStatus' => $statistics['school_status'] ?? [],
    'employment' => $statistics['employment'] ?? [],
];
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Kelola Alumni</h1>
            <p class="text-sm text-slate-500 mt-1">Verifikasi data, pantau tujuan setelah lulus, dan kelola publikasi alumni.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="/admin/alumni/export" class="inline-flex items-center gap-2 px-4 py-2.5 border border-emerald-200 bg-emerald-50 text-emerald-700 font-semibold rounded-lg hover:bg-emerald-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14"/></svg>
                Download Excel
            </a>
            <button type="button" onclick="openAlumniModal()" class="px-4 py-2.5 bg-primary-600 text-white font-semibold rounded-lg">Tambah Alumni</button>
        </div>
    </div>

    <?php if ($flash): ?>
        <div class="p-4 rounded-lg border <?= ($flash['type'] ?? '') === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800' ?>"><?= e($flash['message'] ?? '') ?></div>
    <?php endif; ?>

    <div class="grid lg:grid-cols-2 xl:grid-cols-3 gap-5">
        <section class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
            <h2 class="font-bold text-slate-800">Tujuan Setelah Lulus</h2>
            <p class="text-xs text-slate-500 mt-1">Jumlah alumni berdasarkan jenjang atau pilihan lanjutan.</p>
            <div class="h-72 mt-4"><canvas id="continuationChart" aria-label="Grafik tujuan setelah lulus"></canvas></div>
        </section>
        <section class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
            <h2 class="font-bold text-slate-800">Status Sekolah Tujuan</h2>
            <p class="text-xs text-slate-500 mt-1">Perbandingan sekolah Negeri, Swasta, dan data lainnya.</p>
            <div class="h-72 mt-4"><canvas id="schoolStatusChart" aria-label="Grafik status sekolah tujuan"></canvas></div>
        </section>
        <section class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm lg:col-span-2 xl:col-span-1">
            <h2 class="font-bold text-slate-800">Status Pekerjaan/Aktivitas</h2>
            <p class="text-xs text-slate-500 mt-1">Kondisi aktivitas alumni saat ini.</p>
            <div class="h-72 mt-4"><canvas id="employmentChart" aria-label="Grafik status pekerjaan alumni"></canvas></div>
        </section>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <?php if (!$items): ?>
            <div class="p-14 text-center text-slate-500">Belum ada data alumni.</div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3 text-left">Alumni</th>
                            <th class="px-5 py-3 text-left">Melanjutkan ke</th>
                            <th class="px-5 py-3 text-left">Status aktivitas</th>
                            <th class="px-5 py-3 text-left">Pekerjaan</th>
                            <th class="px-5 py-3 text-left">Kontak Privat</th>
                            <th class="px-5 py-3 text-left">Status Data</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($items as $item): ?>
                            <tr class="align-top hover:bg-slate-50">
                                <td class="px-5 py-4">
                                    <div class="flex gap-3 min-w-52">
                                        <?php if ($item['photo']): ?>
                                            <img src="/storage/<?= e($item['photo']) ?>" class="w-11 h-11 rounded-full object-cover object-top" alt="">
                                        <?php else: ?>
                                            <div class="w-11 h-11 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold shrink-0"><?= e(mb_strtoupper(mb_substr(trim($item['name']), 0, 1))) ?></div>
                                        <?php endif; ?>
                                        <div>
                                            <strong class="text-slate-800"><?= e($item['name']) ?></strong>
                                            <p class="text-xs text-primary-600">Angkatan <?= (int) $item['graduation_year'] ?></p>
                                            <?php if ($item['is_featured']): ?><p class="text-xs text-amber-600">★ Inspiratif</p><?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 min-w-48">
                                    <?php if (!empty($item['further_education'])): ?>
                                        <span class="inline-flex px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 font-semibold"><?= e($item['further_education']) ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-400">Belum diisi</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 min-w-40"><span class="text-slate-700"><?= e($item['employment_status'] ?: 'Belum diisi') ?></span></td>
                                <td class="px-5 py-4 text-slate-600 min-w-48"><?= e($item['occupation'] ?: '-') ?><br><span class="text-xs text-slate-400"><?= e($item['institution'] ?: $item['city']) ?></span></td>
                                <td class="px-5 py-4 text-slate-600 min-w-52">
                                    <div><span class="text-xs text-slate-400">WhatsApp:</span> <?= e($item['whatsapp_plain'] ?: '-') ?></div>
                                    <div class="mt-1"><span class="text-xs text-slate-400">Email:</span> <?= e($item['email_plain'] ?: '-') ?></div>
                                </td>
                                <td class="px-5 py-4"><span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $item['status'] === 'approved' ? 'bg-green-100 text-green-700' : ($item['status'] === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') ?>"><?= e($labels[$item['status']] ?? '') ?></span></td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2 min-w-48">
                                        <?php if ($item['status'] !== 'approved'): ?>
                                            <form method="POST" action="/admin/alumni/status/<?= (int) $item['id'] ?>"><input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>"><input type="hidden" name="status" value="approved"><button class="px-2 py-1.5 bg-green-50 text-green-700 rounded">Setujui</button></form>
                                        <?php endif; ?>
                                        <button type="button" onclick='editAlumni(<?= json_encode(Security::normalizeTextData($item), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) ?>)' class="px-2 py-1.5 bg-indigo-50 text-indigo-700 rounded">Edit</button>
                                        <form method="POST" action="/admin/alumni/delete/<?= (int) $item['id'] ?>" onsubmit="return confirm('Hapus data alumni ini?')"><input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>"><button class="px-2 py-1.5 bg-red-50 text-red-700 rounded">Hapus</button></form>
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

<div id="alumniModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/60">
    <div class="bg-white rounded-xl w-full max-w-4xl max-h-[92vh] overflow-y-auto">
        <form id="alumniForm" action="/admin/alumni/save" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">
            <input type="hidden" name="id" id="aId">
            <input type="hidden" name="cropped_photo" id="aCroppedPhoto">
            <div class="p-5 border-b flex justify-between">
                <h2 id="aTitle" class="font-bold text-lg">Tambah Alumni</h2>
                <button type="button" onclick="closeAlumniModal()" aria-label="Tutup">✕</button>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid sm:grid-cols-2 gap-4">
                    <label class="text-sm font-medium">Nama *<input id="aName" name="name" required maxlength="100" class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"></label>
                    <label class="text-sm font-medium">Tahun lulus *<input id="aYear" type="number" name="graduation_year" required min="1950" max="<?= date('Y') + 1 ?>" class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"></label>
                    <label class="text-sm font-medium">Kelas terakhir<input id="aClass" name="final_class" maxlength="60" class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"></label>
                    <label class="text-sm font-medium">Melanjutkan ke *<select id="aEducation" name="further_education" required class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"><option value="">Pilih tujuan setelah lulus</option><?php foreach ($continuationOptions as $option): ?><option value="<?= e($option) ?>"><?= e($option) ?></option><?php endforeach; ?></select></label>
                    <label class="text-sm font-medium">Status sekolah <span id="aEducationStatusRequired">*</span><select id="aEducationStatus" name="further_education_status" required class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"><option value="">Pilih negeri atau swasta</option><option value="Negeri">Negeri</option><option value="Swasta">Swasta</option></select></label>
                    <label class="text-sm font-medium">Nama sekolah tujuan <span id="aEducationDetailRequired">*</span><input id="aEducationDetail" name="further_education_detail" required maxlength="120" placeholder="Contoh: SMAN 1 Bogor" class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"></label>
                    <label class="text-sm font-medium">Status pekerjaan/aktivitas<select id="aEmploymentStatus" name="employment_status" class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"><option value="">Pilih status saat ini</option><?php foreach ($employmentStatuses as $option): ?><option value="<?= e($option) ?>"><?= e($option) ?></option><?php endforeach; ?></select></label>
                    <label class="text-sm font-medium">Pekerjaan/bidang<input id="aOccupation" name="occupation" maxlength="120" class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"></label>
                    <label class="text-sm font-medium">Instansi<input id="aInstitution" name="institution" maxlength="160" class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"></label>
                    <label class="text-sm font-medium">Kota<input id="aCity" name="city" maxlength="100" class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"></label>
                    <label class="text-sm font-medium">Nomor WhatsApp *<input id="aWhatsapp" type="tel" name="whatsapp" maxlength="30" class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"><small class="text-slate-500">Kosongkan saat edit untuk mempertahankan nomor.</small></label>
                    <label class="text-sm font-medium">Email *<input id="aEmail" type="email" name="email" maxlength="160" class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"><small class="text-slate-500">Kosongkan saat edit untuk mempertahankan email.</small></label>
                </div>

                <label class="block text-sm font-medium">Cerita<textarea id="aStory" name="story" maxlength="1500" rows="3" class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"></textarea></label>
                <label class="block text-sm font-medium">Prestasi<textarea id="aAchievement" name="achievement" maxlength="1000" rows="3" class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"></textarea></label>

                <section class="p-4 bg-slate-50 border border-slate-200 rounded-xl">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div id="aPhotoPreview" class="w-24 h-28 rounded-xl bg-slate-200 flex items-center justify-center overflow-hidden shrink-0 text-slate-400">Belum ada foto</div>
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-slate-700">Foto alumni</label>
                            <input id="aPhotoInput" type="file" name="photo" accept="image/jpeg,image/png,image/webp" class="mt-2 text-sm">
                            <p class="text-xs text-slate-500 mt-1">Maksimal 1 MB. Pilih foto lalu geser dan perbesar sampai wajah berada di dalam bingkai.</p>
                            <label id="aRemoveLabel" class="hidden mt-2 text-sm text-red-600"><input type="checkbox" name="remove_photo" value="1"> Hapus foto</label>
                        </div>
                    </div>
                    <div id="aCropEditor" class="hidden mt-4 pt-4 border-t border-slate-200">
                        <div class="grid sm:grid-cols-[200px_1fr] gap-5 items-start">
                            <div>
                                <canvas id="aCropCanvas" width="400" height="500" class="block w-full max-w-[200px] mx-auto bg-slate-900 rounded-lg shadow-inner cursor-move touch-none" aria-label="Area pengaturan foto alumni"></canvas>
                                <p class="mt-2 text-center text-xs text-slate-500">Geser foto agar wajah berada di tengah bagian atas.</p>
                            </div>
                            <div class="space-y-3">
                                <div><div class="flex justify-between mb-2"><label for="aCropZoom" class="text-sm font-medium">Perbesar wajah</label><span id="aCropZoomValue" class="text-xs font-semibold text-indigo-600">100%</span></div><input type="range" id="aCropZoom" min="1" max="3" step="0.01" value="1" class="w-full accent-indigo-600"></div>
                                <button type="button" id="aResetCrop" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm font-medium hover:bg-white">Atur Ulang Posisi</button>
                                <p class="rounded-lg bg-indigo-50 border border-indigo-100 p-3 text-xs leading-relaxed text-indigo-800">Foto disimpan dalam rasio portrait 4:5 berukuran 800×1000 piksel agar wajah konsisten pada kartu alumni.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="grid sm:grid-cols-3 gap-3 p-4 bg-slate-50 rounded-lg"><label><input id="aPublishPhoto" type="checkbox" name="publish_photo" value="1"> Publikasikan foto</label><label><input id="aPublishJob" type="checkbox" name="publish_occupation" value="1"> Publikasikan pekerjaan</label><label><input id="aPublishCity" type="checkbox" name="publish_city" value="1"> Publikasikan kota</label></div>
                <div class="grid sm:grid-cols-3 gap-4"><label>Status<select id="aStatus" name="status" class="block w-full mt-1 rounded-lg border-slate-300"><option value="pending">Menunggu</option><option value="approved">Disetujui</option><option value="rejected">Ditolak</option></select></label><label>Urutan<input id="aOrder" type="number" name="sort_order" min="0" class="block w-full mt-1 rounded-lg border-slate-300"></label><label class="mt-7"><input id="aFeatured" type="checkbox" name="is_featured" value="1"> Alumni inspiratif</label></div>
            </div>
            <div class="p-5 border-t bg-slate-50 flex justify-end gap-3"><button type="button" onclick="closeAlumniModal()" class="px-4 py-2 border rounded-lg">Batal</button><button class="px-4 py-2 bg-primary-600 text-white rounded-lg">Simpan</button></div>
        </form>
    </div>
</div>

<script src="/vendor/chart.js/chart.umd.js?v=<?= filemtime(ROOT_PATH . '/public/vendor/chart.js/chart.umd.js') ?>"></script>
<script>
const alumniChartData = <?= json_encode(Security::normalizeTextData($chartData), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
const barValueLabels = {
    id: 'barValueLabels',
    afterDatasetsDraw(chart) {
        const {ctx} = chart;
        ctx.save();
        ctx.fillStyle = '#334155';
        ctx.font = '600 11px Inter, sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'bottom';
        chart.data.datasets.forEach((dataset, datasetIndex) => {
            chart.getDatasetMeta(datasetIndex).data.forEach((bar, index) => ctx.fillText(String(dataset.data[index]), bar.x, bar.y - 5));
        });
        ctx.restore();
    }
};

function renderAlumniBarChart(id, rows, color) {
    const canvas = document.getElementById(id);
    if (!canvas || typeof Chart === 'undefined') return;
    const normalizedRows = rows.length ? rows : [{label: 'Belum ada data', total: 0}];
    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: normalizedRows.map(row => `${row.label} (${row.total})`),
            datasets: [{label: 'Jumlah alumni', data: normalizedRows.map(row => Number(row.total)), backgroundColor: color, borderColor: color.replace('0.72', '1'), borderWidth: 1, borderRadius: 7, maxBarThickness: 54}]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {padding: {top: 20}},
            plugins: {legend: {display: true, position: 'bottom'}, tooltip: {callbacks: {label: context => ` ${context.raw} alumni`}}},
            scales: {x: {grid: {display: false}, ticks: {autoSkip: false, maxRotation: 35, minRotation: 0}}, y: {beginAtZero: true, ticks: {precision: 0, stepSize: 1}}}
        },
        plugins: [barValueLabels]
    });
}

renderAlumniBarChart('continuationChart', alumniChartData.continuation, 'rgba(37, 99, 235, 0.72)');
renderAlumniBarChart('schoolStatusChart', alumniChartData.schoolStatus, 'rgba(13, 148, 136, 0.72)');
renderAlumniBarChart('employmentChart', alumniChartData.employment, 'rgba(124, 58, 237, 0.72)');

const alumniModal = document.getElementById('alumniModal');
const alumniForm = document.getElementById('alumniForm');
const aId = document.getElementById('aId'), aTitle = document.getElementById('aTitle'), aName = document.getElementById('aName'), aYear = document.getElementById('aYear'), aClass = document.getElementById('aClass');
const aEducation = document.getElementById('aEducation'), aEducationStatus = document.getElementById('aEducationStatus'), aEducationDetail = document.getElementById('aEducationDetail'), aEducationStatusRequired = document.getElementById('aEducationStatusRequired'), aEducationDetailRequired = document.getElementById('aEducationDetailRequired'), aEmploymentStatus = document.getElementById('aEmploymentStatus');
const aOccupation = document.getElementById('aOccupation'), aInstitution = document.getElementById('aInstitution'), aCity = document.getElementById('aCity'), aWhatsapp = document.getElementById('aWhatsapp'), aEmail = document.getElementById('aEmail'), aStory = document.getElementById('aStory'), aAchievement = document.getElementById('aAchievement');
const aStatus = document.getElementById('aStatus'), aOrder = document.getElementById('aOrder'), aFeatured = document.getElementById('aFeatured'), aPublishPhoto = document.getElementById('aPublishPhoto'), aPublishJob = document.getElementById('aPublishJob'), aPublishCity = document.getElementById('aPublishCity'), aRemoveLabel = document.getElementById('aRemoveLabel');
const aPhotoInput = document.getElementById('aPhotoInput'), aPhotoPreview = document.getElementById('aPhotoPreview'), aCroppedPhoto = document.getElementById('aCroppedPhoto'), aCropEditor = document.getElementById('aCropEditor'), aCropCanvas = document.getElementById('aCropCanvas'), aCropContext = aCropCanvas.getContext('2d'), aCropZoom = document.getElementById('aCropZoom'), aCropZoomValue = document.getElementById('aCropZoomValue');
let cropImage = null, cropBaseScale = 1, cropScale = 1, cropOffsetX = 0, cropOffsetY = 0, cropDragging = false, cropPointerX = 0, cropPointerY = 0, cropDirty = false;

function toggleSchoolFields() {
    const disabled = ['Bekerja', 'Tidak Melanjutkan'].includes(aEducation.value);
    aEducationStatus.disabled = disabled;
    aEducationDetail.disabled = disabled;
    aEducationStatus.required = !disabled;
    aEducationDetail.required = !disabled;
    aEducationStatusRequired.classList.toggle('hidden', disabled);
    aEducationDetailRequired.classList.toggle('hidden', disabled);
    if (disabled) { aEducationStatus.value = ''; aEducationDetail.value = ''; }
}

function constrainCrop() {
    if (!cropImage) return;
    const width = cropImage.naturalWidth * cropScale, height = cropImage.naturalHeight * cropScale;
    cropOffsetX = Math.min(0, Math.max(aCropCanvas.width - width, cropOffsetX));
    cropOffsetY = Math.min(0, Math.max(aCropCanvas.height - height, cropOffsetY));
}

function drawCrop() {
    if (!cropImage) return;
    constrainCrop();
    aCropContext.fillStyle = '#0f172a';
    aCropContext.fillRect(0, 0, aCropCanvas.width, aCropCanvas.height);
    aCropContext.drawImage(cropImage, cropOffsetX, cropOffsetY, cropImage.naturalWidth * cropScale, cropImage.naturalHeight * cropScale);
    aPhotoPreview.innerHTML = `<img src="${aCropCanvas.toDataURL('image/jpeg', 0.82)}" class="w-full h-full object-cover" alt="Pratinjau foto alumni">`;
}

function fitCrop() {
    if (!cropImage) return;
    cropBaseScale = Math.max(aCropCanvas.width / cropImage.naturalWidth, aCropCanvas.height / cropImage.naturalHeight);
    cropScale = cropBaseScale;
    cropOffsetX = (aCropCanvas.width - cropImage.naturalWidth * cropScale) / 2;
    cropOffsetY = (aCropCanvas.height - cropImage.naturalHeight * cropScale) / 2;
    aCropZoom.value = '1';
    aCropZoomValue.textContent = '100%';
    drawCrop();
}

function clearCrop() {
    cropImage = null;
    cropDirty = false;
    aCroppedPhoto.value = '';
    aCropEditor.classList.add('hidden');
    aCropContext.clearRect(0, 0, aCropCanvas.width, aCropCanvas.height);
}

function loadCropImage(src, markDirty = false) {
    const image = new Image();
    image.onload = () => { cropImage = image; cropDirty = markDirty; aCropEditor.classList.remove('hidden'); fitCrop(); };
    image.onerror = () => { if (markDirty) alert('Foto tidak dapat dibaca. Gunakan JPG, PNG, atau WebP.'); };
    image.src = src;
}

function openAlumniModal() {
    alumniForm.reset();
    clearCrop();
    aPhotoPreview.textContent = 'Belum ada foto';
    aId.value = '';
    aTitle.textContent = 'Tambah Alumni';
    aStatus.value = 'approved';
    aOrder.value = '0';
    aRemoveLabel.classList.add('hidden');
    toggleSchoolFields();
    alumniModal.classList.replace('hidden', 'flex');
}

function setEducationFields(item) {
    let type = item.continuation_type || '', status = item.continuation_status || '', detail = item.continuation_institution || '';
    if (!type && item.further_education) {
        const parts = item.further_education.split(' — ');
        type = parts.shift() || '';
        if (['Negeri', 'Swasta'].includes(parts[0])) status = parts.shift();
        detail = parts.join(' — ');
    }
    if (type && !Array.from(aEducation.options).some(option => option.value === type)) aEducation.add(new Option(type, type));
    aEducation.value = type;
    aEducationStatus.value = status;
    aEducationDetail.value = detail;
    toggleSchoolFields();
}

function editAlumni(item) {
    alumniForm.reset();
    clearCrop();
    aId.value = item.id;
    aName.value = item.name || '';
    aYear.value = item.graduation_year || '';
    aClass.value = item.final_class || '';
    setEducationFields(item);
    aEmploymentStatus.value = item.employment_status || '';
    aOccupation.value = item.occupation || '';
    aInstitution.value = item.institution || '';
    aCity.value = item.city || '';
    aWhatsapp.value = '';
    aEmail.value = '';
    aStory.value = item.story || '';
    aAchievement.value = item.achievement || '';
    aStatus.value = item.status;
    aOrder.value = item.sort_order || 0;
    aFeatured.checked = item.is_featured == 1;
    aPublishPhoto.checked = item.publish_photo == 1;
    aPublishJob.checked = item.publish_occupation == 1;
    aPublishCity.checked = item.publish_city == 1;
    aTitle.textContent = 'Edit Alumni';
    aRemoveLabel.classList.toggle('hidden', !item.photo);
    if (item.photo) loadCropImage('/storage/' + item.photo, false); else aPhotoPreview.textContent = 'Belum ada foto';
    alumniModal.classList.replace('hidden', 'flex');
}

function closeAlumniModal() { alumniModal.classList.replace('flex', 'hidden'); }

aEducation.addEventListener('change', toggleSchoolFields);
aPhotoInput.addEventListener('change', function () {
    if (!this.files?.[0]) return;
    if (this.files[0].size > 1048576) { alert('Ukuran foto alumni maksimal 1 MB.'); this.value = ''; return; }
    const url = URL.createObjectURL(this.files[0]);
    const image = new Image();
    image.onload = () => { URL.revokeObjectURL(url); cropImage = image; cropDirty = true; aCropEditor.classList.remove('hidden'); fitCrop(); };
    image.onerror = () => { URL.revokeObjectURL(url); alert('Foto tidak dapat dibaca.'); this.value = ''; };
    image.src = url;
});
aCropZoom.addEventListener('input', () => {
    if (!cropImage) return;
    const oldScale = cropScale, centerX = (aCropCanvas.width / 2 - cropOffsetX) / oldScale, centerY = (aCropCanvas.height / 2 - cropOffsetY) / oldScale;
    cropScale = cropBaseScale * Number(aCropZoom.value);
    cropOffsetX = aCropCanvas.width / 2 - centerX * cropScale;
    cropOffsetY = aCropCanvas.height / 2 - centerY * cropScale;
    aCropZoomValue.textContent = `${Math.round(Number(aCropZoom.value) * 100)}%`;
    cropDirty = true;
    drawCrop();
});
document.getElementById('aResetCrop').addEventListener('click', () => { fitCrop(); cropDirty = true; });
aCropCanvas.addEventListener('pointerdown', event => { if (!cropImage) return; cropDragging = true; cropPointerX = event.clientX; cropPointerY = event.clientY; aCropCanvas.setPointerCapture(event.pointerId); });
aCropCanvas.addEventListener('pointermove', event => { if (!cropDragging || !cropImage) return; const rect = aCropCanvas.getBoundingClientRect(); cropOffsetX += (event.clientX - cropPointerX) * (aCropCanvas.width / rect.width); cropOffsetY += (event.clientY - cropPointerY) * (aCropCanvas.height / rect.height); cropPointerX = event.clientX; cropPointerY = event.clientY; cropDirty = true; drawCrop(); });
const stopCrop = event => { cropDragging = false; if (event.pointerId !== undefined && aCropCanvas.hasPointerCapture(event.pointerId)) aCropCanvas.releasePointerCapture(event.pointerId); };
aCropCanvas.addEventListener('pointerup', stopCrop);
aCropCanvas.addEventListener('pointercancel', stopCrop);
alumniModal.addEventListener('click', event => { if (event.target === alumniModal) closeAlumniModal(); });
alumniForm.addEventListener('submit', () => {
    if (!cropImage || !cropDirty) return;
    const output = document.createElement('canvas'); output.width = 800; output.height = 1000;
    const context = output.getContext('2d'); context.fillStyle = '#fff'; context.fillRect(0, 0, output.width, output.height);
    context.drawImage(cropImage, cropOffsetX * 2, cropOffsetY * 2, cropImage.naturalWidth * cropScale * 2, cropImage.naturalHeight * cropScale * 2);
    aCroppedPhoto.value = output.toDataURL('image/jpeg', 0.9);
});
</script>
