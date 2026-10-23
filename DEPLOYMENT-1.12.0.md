# Deployment Rahayat CMS 1.12.0

Rilis empat tema baru dan penguatan keamanan. Empat tema lama dipertahankan.
Daftar ini untuk **upgrade dari rilis lengkap v1.11.1** (commit `411d002`),
termasuk seluruh perubahan tema setelah rilis tersebut, bukan hanya patch terakhir.
Jika produksi lebih lama dari 1.11.1, gunakan paket kode rilis lengkap dan ikuti
panduan upgrade versi antara; daftar delta ini tidak mencukupi.

## Daftar file persis

Lihat [`production-upload-1.12.0.txt`](production-upload-1.12.0.txt): **64 path**,
satu path per baris, relatif terhadap **root aplikasi**, bukan root `public/`.
Unggah semuanya dari tag `v1.12.0` dengan struktur folder yang sama.
Termasuk `.htaccess` tersembunyi, PHP di luar `public/`, CSS hasil build,
seluruh partial tema, konfigurasi aplikasi umum, dan metadata versi.

`database/schema.sql` dan `database/migrations/add_four_public_themes_1_12_0.sql`
boleh diunggah sebagai kode aplikasi, **jangan mengimpor ulang schema.sql**.
Tidak ada perubahan struktur tabel dari 1.11.1. SQL tema hanya menambahkan
pengaturan default secara idempoten dan **tidak wajib dieksekusi**: aplikasi
memakai fallback dan admin dapat menyimpan pengaturan yang belum ada.

## Langkah aman

1. Cadangkan database, kode lama, `config/local.php`, `storage/`, dan seluruh
   unggahan. Aktifkan maintenance selama unggah manual agar kode lama/baru
   tidak tercampur saat request masuk; bila memungkinkan gunakan pergantian
   direktori rilis secara atomik.
2. Unggah 64 file pada manifest. Jangan mengganti `config/local.php`, `.env`,
   `storage/`, `public/uploads/`, kunci enkripsi, atau database dengan isi lokal.
   `config/app.php` **harus** diperbarui; berbeda dengan `config/local.php`.
3. Pastikan document root mengarah ke `public/` bila hosting mendukungnya.
   Jika root mengarah ke root proyek, `.htaccess` root wajib aktif. Nginx
   mengabaikan `.htaccess`: terapkan penolakan folder internal/hidden dan
   routing storage melalui PHP pada konfigurasi server sebelum membuka situs.
4. PHP harus 8.4/8.5, `APP_DEBUG=false`, `APP_URL` berisi URL HTTPS produksi.
   Aktifkan HTTPS dan OPcache; `SCHEMA_CHECK_ALWAYS=false`. Bila ada reverse
   proxy, proxy wajib menimpa header forwarded dari klien, bukan meneruskannya.
5. Restart/revalidate OPcache sesuai panel hosting. Jika memakai CDN, purge
   HTML dan CSS/JS yang diperbarui. URL aset memakai `filemtime`, jadi unggahan
   harus memiliki waktu modifikasi baru; jangan mempertahankan timestamp lama
   pada aset yang kontennya berubah. Jangan cache HTML formulir/token CSRF.
6. Buka kembali situs. Perubahan versi memicu pengecekan schema satu kali;
   jangan menghapus penanda/kunci runtime atau menjalankan migrasi `--apply`.

File lama `app/Controllers/Backend/EkstrakurikulerController.php` telah dihapus
dari repository pada pembersihan sebelumnya. Ini bukan controller aktif;
setelah backup boleh hapus salinan lama tersebut dari produksi, jangan menghapus
`app/Controllers/EkstrakurikulerController.php` yang masih digunakan.

Dokumentasi, tes, `.github/`, `AGENTS.md`, dan manifest tidak perlu diunggah
ke web hosting. Jangan mengunggah `.git/`, `node_modules/`, fixture, atau laporan
audit sementara. Manifest hanyalah daftar kode, bukan backup data.

## Pemeriksaan setelah unggah

- Versi admin menunjukkan 1.12.0; tema lama dan empat tema baru tetap dapat dipilih.
- Periksa beranda, profil, berita/detail, galeri/detail, GTK, kontak, prestasi,
  agenda/kalender, alumni, testimoni dan SPMB pada desktop/mobile. Pastikan
  identitas, menu, label dan font berasal dari pengaturan sekolah yang benar.
- Uji login dan formulir publik dengan CSRF/CAPTCHA yang valid, serta penolakan
  token salah. Jangan menjalankan skrip fixture/test database pada produksi.
- `/config/local.php`, `/.git/config`, `/.github/copilot-instructions.md`,
  `/AGENTS.md`, dan storage cache/log/backups harus menjawab 403/404, tanpa isi file.
- Berkas SPMB tidak dapat dibaca tanpa akun berizin. Respons berizin harus
  `Cache-Control: private, no-store`, tanpa ETag; CDN tidak boleh menimpanya.
- Media publik mengirim ETag/Last-Modified; request `If-None-Match` yang cocok
  menjawab 304 kosong. CSS/JS dikompresi/cache bila modul Apache tersedia.

## Batas audit dan rollback

Audit dan tes memakai development lokal, bukan server produksi. Tidak ada
jaminan bebas semua kerentanan atau skor kecepatan produksi: konfigurasi server,
ukuran gambar admin, database, CDN, dan jaringan tetap menentukan hasilnya.
CSP kompatibilitas aplikasi masih mengizinkan inline script dan unsafe-eval;
tidak diklaim sebagai strict nonce CSP. Sanitasi/escaping tetap pertahanan utama.
Penjelasan audit dan verifikasi tersedia di `docs/security-theme-1.12.0.md`.

Jika perlu rollback, pulihkan kode versi sebelumnya secara konsisten, termasuk
`config/app.php`, CSS/JS, dan `version.json`; revalidate OPcache/CDN. Rilis ini
tidak mengubah struktur tabel atau menghapus data sekolah.
