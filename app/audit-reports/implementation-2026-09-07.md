# Hasil penerapan perbaikan — 7 September 2026

Perbaikan diterapkan pada workspace lokal. Blokir salin/cetak dan watermark pada
halaman data pribadi tetap dipertahankan sesuai instruksi pengguna. Tidak ada
deployment ke hosting eksternal atau commit Git pada pekerjaan ini.

| Temuan audit | Hasil penerapan |
| --- | --- |
| Akses dokumen tanpa login | Path ambigu ditolak, path kanonis diperiksa, folder privat dibandingkan tanpa bergantung kapitalisasi. Admin atau GTK panitia berizin SPMB saja yang dapat membuka dokumen. Cache privat `no-store`. |
| Sanitizer XSS | Descendant disanitasi sebelum tag pembungkus dilepas; URL dengan karakter kontrol, backslash, URL protocol-relative, dan iframe PDF eksternal ditolak. Konten lama diperiksa oleh migrasi. |
| Draft/future news | API publik dan daftar frontend hanya menampilkan berita yang sudah terbit dan waktunya telah tiba. Preview draft tetap tersedia bagi editor berizin, dengan pembatasan kepemilikan bagi murid/ekskul. GET detail tidak lagi menulis ulang konten database. |
| Sesi usang | Identitas dan fingerprint kredensial/izin diverifikasi dengan akun terkini; akun nonaktif/dihapus dan sesi kedaluwarsa ditolak. Sesi lama wajib login ulang. |
| Throttle | Request publik menggunakan counter server dengan file lock. Header IP klien tidak dipercaya. Login memiliki batas per IP dan per akun selain lock kegagalan sebelumnya. |
| Cek status SPMB | POST+CSRF, verifikasi NISN dan tanggal lahir, nomor baru acak 128 bit. Catatan internal dan asal sekolah tidak dikirim ke tampilan status publik. Nomor lama tetap bisa digunakan. |
| Data tersimpan | Pendaftaran pribadi disimpan dengan AES-256-GCM dalam payload terpisah; kolom pribadi lama dikosongkan/diganti placeholder setelah verifikasi round-trip. Dokumen dienkripsi dan diverifikasi byte-for-byte. |
| Integritas pendaftaran | Validasi server format/panjang/tanggal/enum dilakukan sebelum unggahan; file yang gagal disimpan dibersihkan. Token pengiriman per sesi mencegah duplikasi akibat retry. Nomor tidak memakai COUNT+1. Persetujuan penggunaan data divalidasi dan waktu persetujuan dicatat untuk pendaftaran baru. |
| Kinerja | Pemeriksaan instalasi memakai marker, hasil setting dicache per request, DDL analitik dipindahkan ke migrasi, lock sesi ditutup sebelum transfer file. URL admin.js memiliki versi sesuai filemtime. |
| UI/UX | Label tombol password aksesibel, opsi Ingat saya yang tidak berfungsi dihapus, error formulir tampil pada ringkasan/field, judul iframe dan heading kontak dibenahi, jam menjadi HH:mm, kartu SPMB tidak lagi tertutup header. |

**Migrasi lokal selesai:** 1 pendaftaran lama dan 28 dokumen terenkripsi. Lima
berita diperiksa dan tidak membutuhkan perubahan sanitizer. Cadangan terenkripsi:
`storage/backups/security-20260907-141643-325dc722`. Verifikasi independen berhasil
membaca kembali 1 record SPMB, 5 record berita, dan 28 dokumen dari cadangan tanpa
mencetak isinya. Kunci lama dipertahankan dan tidak dimasukkan ke arsip data.

**Validasi otomatis:** 45 pemeriksaan pada `scripts/test-security.php` dan 26
pemeriksaan HTTP pada `scripts/test-http-security.php` lulus. Uji mencakup nested
XSS, URL berbahaya, throttle setelah reset sesi, IP palsu, revokasi akun,
otorisasi admin/panitia/murid/nonpanitia, enkripsi record dan byte dokumen,
pembaruan status tanpa kehilangan field, draft/jadwal terbit, verifikasi status,
token pengiriman, serta sintaks JavaScript hasil render form. Fixture database
dan dokumen sintetis dibersihkan; permintaan HTTP dapat tercatat di analitik/log.
Build aset Tailwind berhasil dan `git diff --check` bersih.

Browser desktop/ponsel memverifikasi tombol password, form status berlabel,
metode POST, penanda content protection tetap aktif, tidak ada error console
pada pemuatan baru halaman status, judul iframe peta, format jam, serta tidak
ada overflow horizontal pada sampel kontak 390px.

| Halaman | Median TTFB sebelum | Median TTFB sesudah |
| --- | ---: | ---: |
| Beranda | 161 ms | 63 ms |
| Berita | 139 ms | 68 ms |
| Galeri | 97 ms | 52 ms |
| Kontak | 105 ms | 53 ms |
| Login | 106 ms | 39 ms |

Masing-masing tiga request HTML berurutan pada mesin lokal, tanpa throttling
jaringan dan setelah bootstrap migrasi. Semua respons HTTP 200. Ini bukan
load test atau pengukuran Core Web Vitals produksi; variasi lingkungan/cache
dapat memengaruhi hasil.

**Batas penerapan:** kebijakan durasi retensi dan salinan backup di luar server
harus ditentukan operator sekolah; tidak ada penghapusan data pribadi otomatis.
TLS/WAF/trusted proxy hosting belum diubah. CSP masih mengizinkan inline script
dan eval untuk kompatibilitas library/editor lama; penggantian menyeluruh dengan
nonce/hash membutuhkan refaktor seluruh handler inline dan pengujian editor.
Sanitizer dan otorisasi tidak bergantung pada kelonggaran CSP itu untuk
melindungi data. Konten contoh sekolah, CAPTCHA alternatif aksesibel, dan
pengukuran Lighthouse/konkurensi tetap merupakan pekerjaan operasional atau
penyempurnaan terpisah. Jangan mengartikan hasil ini sebagai sertifikasi bebas
kerentanan atau bukti keamanan konfigurasi produksi.
