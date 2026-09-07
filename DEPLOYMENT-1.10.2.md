# Pembaruan hosting 1.10.2 tanpa CLI

Paket `rahayat-cms-1.10.2-hosting-update.zip` mencakup perbaikan 1.10.1 dan
migrasi tanpa Terminal pada 1.10.2, untuk hosting yang memakai versi 1.10.0 atau
1.10.1. PHP dan ekstensi mengikuti persyaratan proyek. CSS sudah dibangun;
tidak perlu menjalankan Composer, npm, SSH, atau Terminal di hosting.

## Langkah melalui File Manager dan phpMyAdmin

1. Melalui panel hosting, buat backup lengkap database dan file aplikasi,
   termasuk storage dan konfigurasi produksi. Cadangkan kunci enkripsi
   `storage/.data-encryption-key` atau nilai `DATA_ENCRYPTION_KEY` hosting secara
   aman dan terpisah dari arsip data. Jangan menggantinya dengan kunci komputer lokal.
2. Batasi akses publik sementara melalui panel hosting, tetapi izinkan akses
   Anda ke panel admin. Jangan menerima pendaftaran/perubahan data saat upload
   dan migrasi. Mode maintenance CMS saja tidak menutup semua API.
3. Buka phpMyAdmin, pilih **database CMS yang benar**, lalu tab **Import**.
   Pilih `database/migrations/security_1_10_2_idempotent.sql` dari paket dan
   jalankan. SQL menambahkan kolom keamanan/alamat yang belum ada dan membuat
   tabel kunjungan bila belum ada. Aman diimpor ulang; data yang sudah ada tidak
   ditimpa/dihapus. Tidak membutuhkan stored procedure. Akun database perlu
   izin CREATE dan ALTER. Jangan impor seluruh `database/schema.sql`.
4. Unggah dan ekstrak **isi paket** ke root proyek hosting, sejajar dengan
   `app`, `config`, `public`, dan `views`. Pertahankan struktur folder; jangan
   menaruh seluruh paket dalam `public` bila root aplikasi ada di atasnya.
   Tampilkan file tersembunyi agar `.htaccess` ikut terunggah.
5. Login sebagai **admin**, buka langsung
   `https://DOMAIN-ANDA/admin/pembaruan/migrasi`, atau gunakan tautan Migrasi
   Keamanan pada menu Pembaruan. Fitur ini tidak memerlukan Git atau UPDATE_ENABLED.
6. Centang bahwa backup sudah disimpan, lalu klik **Jalankan pemeriksaan dan
   migrasi**. Klik **Lanjutkan migrasi** pada tahap berikutnya sampai muncul
   **Migrasi selesai**. Setiap tahap maksimal 5 pendaftar, 1 dokumen, dan 10 berita.
   Jika permintaan terputus, buka halaman kembali dan ulangi. Record/dokumen
   yang sudah dienkripsi dilewati; HTML yang sudah bersih tidak ditulis ulang.
7. Setiap perubahan memiliki backup terenkripsi di
   `storage/backups/security-*`. Halaman menampilkan folder tahap terakhir;
   salin **semua folder tahap migrasi** melalui File Manager ke penyimpanan
   cadangan yang aman. Isi backup diperiksa otomatis sebelum perubahan; tidak
   perlu menjalankan skrip verifikasi melalui CLI. Backup ini hanya data yang
   diproses per tahap, bukan pengganti backup lengkap langkah 1.
8. Periksa login, berita, cek status SPMB, dan dokumen sebagai admin/panitia
   berizin. Pastikan pengunjung anonim tidak bisa membuka dokumen pribadi.
   Blokir salin/cetak pada halaman data pribadi tetap aktif. Buka akses publik
   setelah seluruh tahap selesai dan pemeriksaan berhasil.

SQL hanya mengubah **struktur database**. Enkripsi record dan file lama harus
dijalankan melalui panel admin dengan kunci aplikasi hosting. Impor SQL saja
belum menyelesaikan perlindungan data lama. Persetujuan privasi lama dibiarkan
NULL; migrasi tidak membuat riwayat persetujuan yang tidak pernah diberikan.

Jika terjadi kesalahan, periksa hasil impor SQL, ruang disk, izin tulis storage,
dan kunci enkripsi hosting. Jangan mengganti kunci untuk mengatasi kesalahan.
Satu dokumen besar tetap bergantung pada batas memori/waktu PHP hosting;
naikkan batas melalui panel hosting bila diperlukan sebelum mencoba ulang.

## File/folder yang diunggah

Gunakan seluruh isi paket. Daftar file persis tersedia dalam
`UPLOAD-MANIFEST-1.10.2.txt` di dalam ZIP. Cakupannya:

- `app/Controllers/`, `app/Core/`, `app/Models/`: file PHP pembaruan yang disertakan.
- `views/`: halaman dan layout pembaruan yang disertakan.
- `public/index.php` dan `public/css/tailwind.min.css`.
- `config/app.php` saja dari folder konfigurasi.
- `database/schema.sql` (dependensi aplikasi) dan SQL migrasi yang disertakan.
- `storage/.htaccess` saja dari folder storage.
- `.htaccess`, `version.json`, dokumentasi, dan skrip pendukung yang disertakan.

Jangan menimpa `config/local.php`, `.env`, konfigurasi DB/secret produksi,
kunci enkripsi, dokumen `storage/spmb`, media, cache, log, atau backup hosting
dengan file lokal. File tersebut tidak ada dalam paket. Tidak perlu mengunggah
`.git`, `node_modules`, laporan audit, atau skrip pengujian.

Panduan ini menggantikan langkah CLI pada `DEPLOYMENT-1.10.1.md`. Untuk versi
sebelum 1.10.0 atau source hosting yang dimodifikasi sendiri, cocokkan perubahan
versi dan source terlebih dahulu. Untuk rollback, pulihkan backup lengkap
pra-update beserta kunci asli; kode lama saja tidak bisa membaca data terenkripsi.
