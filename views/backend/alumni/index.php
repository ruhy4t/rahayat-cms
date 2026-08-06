<?php
$items = $data['alumniItems'] ?? [];
$flash = $data['flash'] ?? null;
$labels = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
$continuationOptions = ['SMA', 'SMK', 'MA', 'Pesantren', 'Paket C', 'Bekerja', 'Tidak/Belum Melanjutkan', 'Lainnya'];
?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div><h1 class="text-2xl font-bold text-slate-800">Kelola Alumni</h1><p class="text-sm text-slate-500 mt-1">Verifikasi pendataan alumni dan atur informasi publik.</p></div>
        <button onclick="openAlumniModal()" class="px-4 py-2.5 bg-primary-600 text-white font-semibold rounded-lg">Tambah Alumni</button>
    </div>
    <?php if ($flash): ?><div class="p-4 rounded-lg border <?= ($flash['type'] ?? '') === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800' ?>"><?= e($flash['message'] ?? '') ?></div><?php endif; ?>
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <?php if (!$items): ?><div class="p-14 text-center text-slate-500">Belum ada data alumni.</div><?php else: ?>
        <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-slate-50 border-b border-slate-200"><tr><th class="px-5 py-3 text-left">Alumni</th><th class="px-5 py-3 text-left">Melanjutkan ke</th><th class="px-5 py-3 text-left">Aktivitas</th><th class="px-5 py-3 text-left">Kontak Privat</th><th class="px-5 py-3 text-left">Status</th><th class="px-5 py-3 text-right">Aksi</th></tr></thead>
        <tbody class="divide-y divide-slate-100"><?php foreach ($items as $item): ?><tr class="align-top hover:bg-slate-50">
            <td class="px-5 py-4"><div class="flex gap-3 min-w-52"><?php if ($item['photo']): ?><img src="/storage/<?= e($item['photo']) ?>" class="w-11 h-11 rounded-full object-cover" alt=""><?php else: ?><div class="w-11 h-11 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold"><?= e(mb_strtoupper(mb_substr($item['name'], 0, 1))) ?></div><?php endif; ?><div><strong class="text-slate-800"><?= e($item['name']) ?></strong><p class="text-xs text-primary-600">Angkatan <?= (int) $item['graduation_year'] ?></p><?php if ($item['is_featured']): ?><p class="text-xs text-amber-600">★ Inspiratif</p><?php endif; ?></div></div></td>
            <td class="px-5 py-4 min-w-44"><?php if (!empty($item['further_education'])): ?><span class="inline-flex px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 font-semibold"><?= e($item['further_education']) ?></span><?php else: ?><span class="text-slate-400">Belum diisi</span><?php endif; ?></td>
            <td class="px-5 py-4 text-slate-600 min-w-48"><?= e($item['occupation'] ?: '-') ?><br><span class="text-xs text-slate-400"><?= e($item['institution'] ?: $item['city']) ?></span></td>
            <td class="px-5 py-4 text-slate-600 min-w-44"><?= e($item['contact_plain'] ?: '-') ?></td>
            <td class="px-5 py-4"><span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $item['status'] === 'approved' ? 'bg-green-100 text-green-700' : ($item['status'] === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') ?>"><?= e($labels[$item['status']] ?? '') ?></span></td>
            <td class="px-5 py-4"><div class="flex justify-end gap-2 min-w-48"><?php if ($item['status'] !== 'approved'): ?><form method="POST" action="/admin/alumni/status/<?= (int) $item['id'] ?>"><input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>"><input type="hidden" name="status" value="approved"><button class="px-2 py-1.5 bg-green-50 text-green-700 rounded">Setujui</button></form><?php endif; ?><button onclick='editAlumni(<?= e(json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT)) ?>)' class="px-2 py-1.5 bg-indigo-50 text-indigo-700 rounded">Edit</button><form method="POST" action="/admin/alumni/delete/<?= (int) $item['id'] ?>" onsubmit="return confirm('Hapus data alumni ini?')"><input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>"><button class="px-2 py-1.5 bg-red-50 text-red-700 rounded">Hapus</button></form></div></td>
        </tr><?php endforeach; ?></tbody></table></div><?php endif; ?>
    </div>
</div>

<div id="alumniModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/60">
<div class="bg-white rounded-xl w-full max-w-3xl max-h-[92vh] overflow-y-auto"><form id="alumniForm" action="/admin/alumni/save" method="POST" enctype="multipart/form-data">
<input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>"><input type="hidden" name="id" id="aId">
<div class="p-5 border-b flex justify-between"><h2 id="aTitle" class="font-bold text-lg">Tambah Alumni</h2><button type="button" onclick="closeAlumniModal()">✕</button></div>
<div class="p-6 space-y-4">
<div class="grid sm:grid-cols-2 gap-4">
<label class="text-sm font-medium">Nama *<input id="aName" name="name" required maxlength="100" class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"></label>
<label class="text-sm font-medium">Tahun lulus *<input id="aYear" type="number" name="graduation_year" required min="1950" max="<?= date('Y') + 1 ?>" class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"></label>
<label class="text-sm font-medium">Kelas/jurusan<input id="aClass" name="final_class" maxlength="60" class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"></label>
<label class="text-sm font-medium">Melanjutkan ke<select id="aEducation" name="further_education" class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"><option value="">Pilih tujuan setelah lulus</option><?php foreach ($continuationOptions as $option): ?><option value="<?= e($option) ?>"><?= e($option) ?></option><?php endforeach; ?></select></label>
<label class="text-sm font-medium">Status sekolah<select id="aEducationStatus" name="further_education_status" class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"><option value="">Pilih negeri atau swasta</option><option value="Negeri">Negeri</option><option value="Swasta">Swasta</option></select></label>
<label class="text-sm font-medium">Nama sekolah/tujuan<input id="aEducationDetail" name="further_education_detail" maxlength="120" placeholder="Contoh: SMAN 1 Bogor" class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"></label>
<label class="text-sm font-medium">Pekerjaan<input id="aOccupation" name="occupation" maxlength="120" class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"></label>
<label class="text-sm font-medium">Instansi<input id="aInstitution" name="institution" maxlength="160" class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"></label>
<label class="text-sm font-medium">Kota<input id="aCity" name="city" maxlength="100" class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"></label>
<label class="text-sm font-medium">Kontak privat *<input id="aContact" name="contact" maxlength="160" class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"><small class="text-slate-500">Kosongkan saat edit untuk mempertahankan kontak.</small></label>
</div>
<label class="block text-sm font-medium">Cerita<textarea id="aStory" name="story" maxlength="1500" rows="3" class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"></textarea></label>
<label class="block text-sm font-medium">Prestasi<textarea id="aAchievement" name="achievement" maxlength="1000" rows="3" class="block w-full mt-1 px-3 py-2 border-slate-300 rounded-lg"></textarea></label>
<div><input type="file" name="photo" accept="image/jpeg,image/png,image/webp"><p class="text-xs text-slate-500">Maksimal 2 MB.</p><label id="aRemoveLabel" class="hidden text-sm text-red-600"><input type="checkbox" name="remove_photo" value="1"> Hapus foto</label></div>
<div class="grid sm:grid-cols-3 gap-3 p-4 bg-slate-50 rounded-lg"><label><input id="aPublishPhoto" type="checkbox" name="publish_photo" value="1"> Publikasikan foto</label><label><input id="aPublishJob" type="checkbox" name="publish_occupation" value="1"> Publikasikan pekerjaan</label><label><input id="aPublishCity" type="checkbox" name="publish_city" value="1"> Publikasikan kota</label></div>
<div class="grid sm:grid-cols-3 gap-4"><label>Status<select id="aStatus" name="status" class="block w-full mt-1 rounded-lg border-slate-300"><option value="pending">Menunggu</option><option value="approved">Disetujui</option><option value="rejected">Ditolak</option></select></label><label>Urutan<input id="aOrder" type="number" name="sort_order" min="0" class="block w-full mt-1 rounded-lg border-slate-300"></label><label class="mt-7"><input id="aFeatured" type="checkbox" name="is_featured" value="1"> Alumni inspiratif</label></div>
</div><div class="p-5 border-t bg-slate-50 flex justify-end gap-3"><button type="button" onclick="closeAlumniModal()" class="px-4 py-2 border rounded-lg">Batal</button><button class="px-4 py-2 bg-primary-600 text-white rounded-lg">Simpan</button></div>
</form></div></div>
<script>
const alumniModal=document.getElementById('alumniModal'),alumniForm=document.getElementById('alumniForm');
const aId=document.getElementById('aId'),aTitle=document.getElementById('aTitle'),aName=document.getElementById('aName'),aYear=document.getElementById('aYear'),aClass=document.getElementById('aClass'),aEducation=document.getElementById('aEducation'),aEducationStatus=document.getElementById('aEducationStatus'),aEducationDetail=document.getElementById('aEducationDetail'),aOccupation=document.getElementById('aOccupation'),aInstitution=document.getElementById('aInstitution'),aCity=document.getElementById('aCity'),aContact=document.getElementById('aContact'),aStory=document.getElementById('aStory'),aAchievement=document.getElementById('aAchievement'),aStatus=document.getElementById('aStatus'),aOrder=document.getElementById('aOrder'),aFeatured=document.getElementById('aFeatured'),aPublishPhoto=document.getElementById('aPublishPhoto'),aPublishJob=document.getElementById('aPublishJob'),aPublishCity=document.getElementById('aPublishCity'),aRemoveLabel=document.getElementById('aRemoveLabel');
function openAlumniModal(){alumniForm.reset();aId.value='';aTitle.textContent='Tambah Alumni';aStatus.value='approved';aRemoveLabel.classList.add('hidden');alumniModal.classList.replace('hidden','flex')}
function setEducationValue(value){const parts=(value||'').split(' — '),type=parts.shift()||'',hasStatus=['Negeri','Swasta'].includes(parts[0]);if(type&&!Array.from(aEducation.options).some(option=>option.value===type)){aEducation.add(new Option(type,type))}aEducation.value=type;aEducationStatus.value=hasStatus?parts.shift():'';aEducationDetail.value=parts.join(' — ')}
function editAlumni(i){aId.value=i.id;aName.value=i.name||'';aYear.value=i.graduation_year||'';aClass.value=i.final_class||'';setEducationValue(i.further_education);aOccupation.value=i.occupation||'';aInstitution.value=i.institution||'';aCity.value=i.city||'';aContact.value='';aStory.value=i.story||'';aAchievement.value=i.achievement||'';aStatus.value=i.status;aOrder.value=i.sort_order||0;aFeatured.checked=i.is_featured==1;aPublishPhoto.checked=i.publish_photo==1;aPublishJob.checked=i.publish_occupation==1;aPublishCity.checked=i.publish_city==1;aTitle.textContent='Edit Alumni';aRemoveLabel.classList.toggle('hidden',!i.photo);alumniModal.classList.replace('hidden','flex')}
function closeAlumniModal(){alumniModal.classList.replace('flex','hidden')}
alumniModal.addEventListener('click',e=>{if(e.target===alumniModal)closeAlumniModal()});
</script>
