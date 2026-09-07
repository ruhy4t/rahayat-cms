# Migrasi sekali klik — 1.10.3

## Jika file versi 1.10.2 sudah diunggah

Unggah isi `rahayat-cms-1.10.3-from-1.10.2.zip` ke root proyek hosting.
File aplikasi yang berubah:

- `app/Controllers/SecurityMigrationController.php`
- `views/backend/system/migration.php`
- `public/js/security-migration.js` (file baru)
- `config/app.php`
- `version.json`

Paket juga menyertakan changelog dan panduan. Tidak perlu mengimpor SQL lagi.
Data yang sudah dimigrasikan tetap tersimpan dan akan dilewati.

Login admin, buka `/admin/pembaruan/migrasi`, lalu muat ulang halaman agar
kode terbaru termuat. Centang pernyataan backup, klik **Mulai migrasi otomatis**
atau **Lanjutkan migrasi otomatis** satu kali. Biarkan tab terbuka sampai
muncul **Migrasi selesai**. Semua tahap berikutnya berjalan otomatis.

Jika koneksi terputus, periksa koneksi lalu klik **Coba lagi**. Jika sesi
berakhir, muat ulang halaman dan login kembali. Jika waktu tunggu habis,
tunggu sebentar karena tahap terakhir mungkin masih diproses server sebelum
mencoba lagi. Proses melanjutkan data yang belum dienkripsi. Tidak perlu
mengulang impor SQL atau mengembalikan data yang sudah berhasil dimigrasikan.

Backup per tahap tetap dibuat dan diverifikasi. Setelah selesai, simpan semua
folder `storage/backups/security-*` melalui File Manager. Simpan kunci enkripsi
hosting secara aman dan terpisah dari backup data. Blokir salin/cetak pada
halaman data pribadi tetap dipertahankan.

JavaScript harus aktif untuk mode otomatis. Jika JavaScript dimatikan, form
tetap tersedia, tetapi hanya memproses satu tahap per klik.

## Jika belum mengunggah pembaruan keamanan

Gunakan paket lengkap `rahayat-cms-1.10.3-hosting-update.zip`, yang mencakup
perbaikan sejak 1.10.0. Urutannya:

1. Backup database, file, dan kunci enkripsi hosting. Batasi akses publik sementara.
2. Impor `database/migrations/security_1_10_2_idempotent.sql` melalui phpMyAdmin.
   SQL yang sama tetap berlaku dan aman diimpor ulang. Jangan impor `schema.sql`.
3. Unggah isi paket lengkap sesuai struktur folder ke root proyek hosting.
4. Jalankan migrasi otomatis sekali klik seperti petunjuk di atas.
5. Periksa fungsi CMS dan akses dokumen sebelum membuka kembali akses publik.

Jangan menimpa `config/local.php`, `.env`, kunci enkripsi hosting, atau isi
storage dengan data dari komputer lokal. Dari folder storage, paket lengkap
hanya memperbarui `.htaccess`. Tidak diperlukan CLI, SSH, npm, atau Composer.
