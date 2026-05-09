<?php
/**
 * SPMB Registration Form View
 */
?>

<div class="bg-primary-600 pb-24 pt-12 sm:pb-32 sm:pt-16 lg:pb-32 lg:pt-20">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl">
                Formulir Pendaftaran
            </h1>
            <p class="mt-6 text-lg leading-8 text-primary-100">
                Silakan isi data-data di bawah ini dengan lengkap dan benar untuk mendaftar sebagai calon murid baru di
                <?= e($profile['name'] ?? SCHOOL_NAME) ?>.
            </p>
        </div>
    </div>
</div>

<div class="-mt-16 sm:-mt-24 mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 pb-20">
    <div class="bg-white rounded-2xl shadow-2xl ring-1 ring-slate-200 overflow-hidden">

        <!-- Registration Form -->
        <form id="spmbForm" action="/spmb/store" method="POST" enctype="multipart/form-data"
            class="divide-y divide-slate-200">
            <input type="hidden" name="csrf_token" value="<?= Security::csrf() ?>">

            <?php if ($quota > 0): ?>
                <div class="p-6 bg-blue-50 border-b border-blue-100 flex items-start gap-4">
                    <div
                        class="mt-0.5 w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-blue-900 font-bold text-lg mb-1">Informasi Kuota</h3>
                        <p class="text-blue-700">Daya tampung SPMB saat ini adalah <strong
                                class="text-blue-900"><?= number_format($quota) ?> murid</strong>. Jumlah pendaftar saat
                            ini: <strong class="text-blue-900"><?= number_format($totalRegistered) ?> pendaftar</strong>.
                            Segera lengkapi formulir sebelum kuota penuh!</p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 1. Data Diri Calon Murid -->
            <div class="p-8 sm:p-10">
                <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <span
                        class="flex items-center justify-center w-8 h-8 rounded-full bg-primary-100 text-primary-600 text-sm">1</span>
                    Data Diri Calon Murid
                </h3>

                <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8">
                    <div class="sm:col-span-2">
                        <label for="student_name" class="block text-sm font-medium text-slate-700">Nama Lengkap
                            *</label>
                        <div class="mt-1">
                            <input type="text" name="student_name" id="student_name" required
                                class="block w-full rounded-xl border-slate-300 py-3 px-4 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200">
                        </div>
                    </div>

                    <div>
                        <label for="nisn" class="block text-sm font-medium text-slate-700">NISN *</label>
                        <div class="mt-1">
                            <input type="text" name="nisn" id="nisn" maxlength="10" placeholder="10 digit NISN" required
                                class="block w-full rounded-xl border-slate-300 py-3 px-4 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200">
                        </div>
                    </div>

                    <div>
                        <label for="nik" class="block text-sm font-medium text-slate-700">NIK *</label>
                        <div class="mt-1">
                            <input type="text" name="nik" id="nik" maxlength="16" placeholder="16 digit NIK" required
                                class="block w-full rounded-xl border-slate-300 py-3 px-4 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200">
                        </div>
                    </div>

                    <div>
                        <label for="birth_place" class="block text-sm font-medium text-slate-700">Tempat Lahir *</label>
                        <div class="mt-1">
                            <input type="text" name="birth_place" id="birth_place" required
                                class="block w-full rounded-xl border-slate-300 py-3 px-4 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200">
                        </div>
                    </div>

                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-slate-700">Tanggal Lahir *</label>
                        <div class="mt-1">
                            <input type="date" name="birth_date" id="birth_date" required
                                class="block w-full rounded-xl border-slate-300 py-3 px-4 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200">
                        </div>
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-medium text-slate-700">Jenis Kelamin *</label>
                        <div class="mt-1">
                            <select id="gender" name="gender" required
                                class="block w-full rounded-xl border-slate-300 py-3 px-4 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200">
                                <option value="">- Pilih Jenis Kelamin -</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="religion" class="block text-sm font-medium text-slate-700">Agama *</label>
                        <div class="mt-1">
                            <select id="religion" name="religion" required
                                class="block w-full rounded-xl border-slate-300 py-3 px-4 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200">
                                <option value="">- Pilih Agama -</option>
                                <option value="Islam">Islam</option>
                                <option value="Kristen">Kristen</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Buddha">Buddha</option>
                                <option value="Konghucu">Konghucu</option>
                            </select>
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="address" class="block text-sm font-medium text-slate-700">Nama Jalan / Dusun
                            *</label>
                        <div class="mt-1">
                            <input type="text" id="address" name="address" required
                                class="block w-full rounded-xl border-slate-300 py-3 px-4 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200">
                        </div>
                    </div>

                    <div>
                        <label for="address_village" class="block text-sm font-medium text-slate-700">Kelurahan/Desa
                            *</label>
                        <div class="mt-1">
                            <input type="text" name="address_village" id="address_village" required
                                class="block w-full rounded-xl border-slate-300 py-3 px-4 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200">
                        </div>
                    </div>

                    <div>
                        <label for="address_district" class="block text-sm font-medium text-slate-700">Kecamatan
                            *</label>
                        <div class="mt-1">
                            <input type="text" name="address_district" id="address_district" required
                                class="block w-full rounded-xl border-slate-300 py-3 px-4 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200">
                        </div>
                    </div>

                    <div>
                        <label for="address_city" class="block text-sm font-medium text-slate-700">Kota/Kabupaten
                            *</label>
                        <div class="mt-1">
                            <input type="text" name="address_city" id="address_city" required
                                class="block w-full rounded-xl border-slate-300 py-3 px-4 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200">
                        </div>
                    </div>

                    <div>
                        <label for="address_province" class="block text-sm font-medium text-slate-700">Provinsi
                            *</label>
                        <div class="mt-1">
                            <input type="text" name="address_province" id="address_province" required
                                class="block w-full rounded-xl border-slate-300 py-3 px-4 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700">Email Akun Pendaftar</label>
                        <div class="mt-1">
                            <input type="email" name="email" id="email"
                                class="block w-full rounded-xl border-slate-300 py-3 px-4 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200">
                        </div>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700">No. HP/WhatsApp *</label>
                        <div class="mt-1">
                            <input type="text" name="phone" id="phone" required
                                class="block w-full rounded-xl border-slate-300 py-3 px-4 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Data Orang Tua -->
            <div class="p-8 sm:p-10 bg-slate-50/50">
                <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <span
                        class="flex items-center justify-center w-8 h-8 rounded-full bg-primary-100 text-primary-600 text-sm">2</span>
                    Data Orang Tua / Wali
                </h3>

                <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8">
                    <!-- Ayah -->
                    <div class="space-y-4">
                        <h4 class="font-semibold text-slate-700 border-b pb-2">Data Ayah</h4>

                        <div>
                            <label for="father_name" class="block text-sm font-medium text-slate-700">Nama Ayah
                                *</label>
                            <input type="text" name="father_name" id="father_name" required
                                class="mt-1 block w-full rounded-xl border-slate-300 py-2.5 px-3 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200">
                        </div>
                        <div>
                            <label for="father_occupation" class="block text-sm font-medium text-slate-700">Pekerjaan
                                Ayah</label>
                            <input type="text" name="father_occupation" id="father_occupation"
                                class="mt-1 block w-full rounded-xl border-slate-300 py-2.5 px-3 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200">
                        </div>
                        <div>
                            <label for="father_phone" class="block text-sm font-medium text-slate-700">No. HP
                                Ayah</label>
                            <input type="text" name="father_phone" id="father_phone"
                                class="mt-1 block w-full rounded-xl border-slate-300 py-2.5 px-3 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200">
                        </div>
                    </div>

                    <!-- Ibu -->
                    <div class="space-y-4">
                        <h4 class="font-semibold text-slate-700 border-b pb-2">Data Ibu</h4>

                        <div>
                            <label for="mother_name" class="block text-sm font-medium text-slate-700">Nama Ibu *</label>
                            <input type="text" name="mother_name" id="mother_name" required
                                class="mt-1 block w-full rounded-xl border-slate-300 py-2.5 px-3 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200">
                        </div>
                        <div>
                            <label for="mother_occupation" class="block text-sm font-medium text-slate-700">Pekerjaan
                                Ibu</label>
                            <input type="text" name="mother_occupation" id="mother_occupation"
                                class="mt-1 block w-full rounded-xl border-slate-300 py-2.5 px-3 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200">
                        </div>
                        <div>
                            <label for="mother_phone" class="block text-sm font-medium text-slate-700">No. HP
                                Ibu</label>
                            <input type="text" name="mother_phone" id="mother_phone"
                                class="mt-1 block w-full rounded-xl border-slate-300 py-2.5 px-3 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Sekolah Asal -->
            <div class="p-8 sm:p-10">
                <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <span
                        class="flex items-center justify-center w-8 h-8 rounded-full bg-primary-100 text-primary-600 text-sm">3</span>
                    Sekolah Asal
                </h3>

                <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8">
                    <div class="sm:col-span-1">
                        <label for="previous_school" class="block text-sm font-medium text-slate-700">Nama Sekolah Asal
                            *</label>
                        <div class="mt-1">
                            <input type="text" name="previous_school" id="previous_school" required
                                class="block w-full rounded-xl border-slate-300 py-3 px-4 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200">
                        </div>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="previous_school_npsn" class="block text-sm font-medium text-slate-700">NPSN Sekolah
                            Asal *</label>
                        <div class="mt-1">
                            <input type="text" name="previous_school_npsn" id="previous_school_npsn" required
                                class="block w-full rounded-xl border-slate-300 py-3 px-4 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200">
                        </div>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="graduation_year" class="block text-sm font-medium text-slate-700">Tahun Lulus
                            *</label>
                        <div class="mt-1">
                            <input type="number" name="graduation_year" id="graduation_year" value="<?= date('Y') ?>"
                                required min="2000" max="2099"
                                class="block w-full rounded-xl border-slate-300 py-3 px-4 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200">
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="previous_school_address" class="block text-sm font-medium text-slate-700">Alamat
                            Sekolah Asal</label>
                        <div class="mt-1">
                            <textarea id="previous_school_address" name="previous_school_address" rows="2"
                                class="block w-full rounded-xl border-slate-300 py-3 px-4 shadow-sm focus:border-primary-500 focus:ring-primary-500 outline-none ring-1 ring-slate-200"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Upload Dokumen -->
            <div class="p-8 sm:p-10 bg-slate-50/50">
                <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <span
                        class="flex items-center justify-center w-8 h-8 rounded-full bg-primary-100 text-primary-600 text-sm">4</span>
                    Unggah Dokumen (Opsional)
                </h3>

                <p class="text-sm text-slate-500 mb-6">
                    Anda dapat mengunggah dokumen sekarang atau menyusulkannya nanti saat pendaftaran ulang di sekolah.
                    (Format: JPG, PNG, PDF. Maks: 2MB per file)
                </p>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Dokumen Inputs -->
                    <?php
                    $allDocs = [
                        'akta_kelahiran' => 'Akta Kelahiran',
                        'kartu_keluarga' => 'Kartu Keluarga',
                        'ktp_ortu' => 'KTP Orang Tua/Wali',
                        'pas_foto' => 'Pas Foto 3x4',
                        'ijazah' => 'Ijazah / SKL',
                        'rapor' => 'Scan Rapor Terakhir',
                    ];

                    if (empty($selectedDocuments)) {
                        echo '<div class="col-span-1 sm:col-span-2 text-center text-slate-500 py-4">Tidak ada formulir dokumen yang diminta untuk diunggah saat ini.</div>';
                    }

                    foreach ($selectedDocuments as $key):
                        $label = $allDocs[$key] ?? ucwords(str_replace('_', ' ', $key));
                        ?>
                        <div>
                            <span class="block text-sm font-medium text-slate-700 mb-2">
                                <?= $label ?>
                            </span>
                            <label for="<?= $key ?>"
                                class="mt-1 flex justify-center rounded-xl border border-dashed border-slate-300 px-6 py-4 bg-white hover:bg-slate-50 hover:border-primary-400 transition-colors cursor-pointer">
                                <div class="text-center">
                                    <svg class="mx-auto h-8 w-8 text-slate-300" viewBox="0 0 24 24" fill="currentColor"
                                        aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <div class="mt-2 text-sm leading-6 text-slate-600">
                                        <span class="font-semibold text-primary-600 hover:text-primary-500">Pilih
                                            File</span>
                                        <span class="text-slate-500"> atau klik area ini</span>
                                        <input id="<?= $key ?>" name="<?= $key ?>" type="file" class="sr-only"
                                            accept=".jpg,.jpeg,.png,.pdf">
                                    </div>
                                    <p class="text-xs leading-5 text-slate-500" id="filename_<?= $key ?>">Belum ada file
                                        terpilih</p>
                                </div>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Submit -->
            <div class="p-8 sm:p-10 bg-slate-50">
                <div class="flex items-start mb-8">
                    <div class="flex h-6 items-center">
                        <input id="agreement" name="agreement" type="checkbox" required
                            class="h-5 w-5 rounded border-slate-300 text-primary-600 focus:ring-primary-600">
                    </div>
                    <div class="ml-3">
                        <label for="agreement" class="text-sm text-slate-700 font-medium">
                            Dengan ini saya menyatakan bahwa seluruh data yang saya isikan adalah benar dan dapat
                            dipertanggungjawabkan.
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="/spmb"
                        class="px-6 py-3 font-semibold text-slate-600 hover:text-slate-800 transition-colors">Batal</a>
                    <button type="submit" id="submitBtn"
                        class="flex items-center px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition-all shadow-md">
                        <span id="btnText">Kirim Pendaftaran</span>
                        <svg id="btnSpinner" class="animate-spin -mr-1 ml-3 h-5 w-5 text-white hidden"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>
        </form>

        <!-- Success Screen (Hidden initially) -->
        <div id="successScreen" class="hidden p-12 text-center">
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-slate-800 mb-2">Pendaftaran Berhasil!</h2>
            <p class="text-slate-600 mb-6">Terima kasih, data pendaftaran Anda telah kami terima.</p>

            <div class="bg-slate-50 border border-slate-200 rounded-xl p-6 max-w-sm mx-auto mb-8">
                <p class="text-sm text-slate-500 mb-1">Nomor Registrasi Anda:</p>
                <div id="regNumberDisplay" class="text-2xl font-mono font-bold text-primary-600 tracking-wider"></div>
                <p class="mt-4 text-xs text-slate-400">Harap simpan nomor registrasi ini untuk mengecek status
                    pendaftaran Anda.</p>
            </div>

            <div class="flex justify-center gap-4">
                <a href="/spmb/cek-status"
                    class="px-6 py-3 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-xl transition-all shadow-md">
                    Cek Status Pendaftaran
                </a>
                <a href="/"
                    class="px-6 py-3 border border-slate-300 text-slate-700 font-semibold rounded-xl hover:bg-slate-50 transition-colors">
                    Kembali ke Beranda
                </a>
            </div>
        </div>

    </div>
</div>

<script>
    // File input filename display
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function (e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Belum ada file terpilih';
            document.getElementById('filename_' + this.id).textContent = fileName;
        });
    });

    // Form Submisison
    document.getElementById('spmbForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const btn = document.getElementById('submitBtn');
        const spinner = document.getElementById('btnSpinner');
        const btnText = document.getElementById('btnText');

        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        spinner.classList.remove('hidden');
        btnText.textContent = 'Memproses...';

        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
            .then(response => {
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Server response:', text);
                        throw new Error('Server error: ' + (text.substring(0, 200) || 'Unknown error'));
                    }
                });
            })
            .then(data => {
                if (data.success) {
                    // Show success screen
                    document.getElementById('spmbForm').classList.add('hidden');
                    document.getElementById('successScreen').classList.remove('hidden');
                    document.getElementById('regNumberDisplay').textContent = data.registration_number;

                    // Set the reg number to localstorage so user can easily check status
                    localStorage.setItem('last_spmb_reg_number', data.registration_number);

                    // Scroll to top of card
                    document.getElementById('successScreen').scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    alert(data.message || 'Terjadi kesalahan saat menyimpan data.');
                    // Reset button
                    btn.disabled = false;
                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                    spinner.classList.add('hidden');
                    btnText.textContent = 'Kirim Pendaftaran';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Terjadi kesalahan jaringan.');

                // Reset button
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
                spinner.classList.add('hidden');
                btnText.textContent = 'Kirim Pendaftaran';
            });
    });
</script>