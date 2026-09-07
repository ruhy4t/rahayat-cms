# Deployment Rahayat CMS 1.10.1

Pembaruan keamanan dari kode 1.10.0 yang diaudit. Gunakan PHP 8.4 atau lebih baru
(direkomendasikan 8.5) dengan PDO MySQL, OpenSSL, DOM, mbstring, fileinfo, dan GD.

## File yang diunggah

Paket `rahayat-cms-1.10.1-hosting-update.zip` berisi file pembaruan dengan struktur
relatif terhadap root proyek. Ekstrak/unggah isinya ke root proyek hosting,
sejajar dengan folder `app`, `config`, dan `public`. Jangan menaruh seluruh paket
di dalam `public` jika root aplikasi berada satu tingkat di atasnya.

| Lokasi | Yang diperbarui |
| --- | --- |
| `app/Controllers/` | ApiController, AuthController, Controller, DashboardController, InstallController, NewsController, SPMBController |
| `app/Core/` | App, AuthSession, DataCipher, PrivateDocument, RateLimiter, SPMBValidation, SchemaRepairer, Security, SecurityAudit, SecuritySchema |
| `app/Models/` | News, SPMBRegistration, SiteSetting, SiteVisit |
| `config/` | **Hanya `app.php`** |
| `database/` | `schema.sql` dan `migrations/create_site_visits_table.sql` |
| `public/` | `index.php` dan `css/tailwind.min.css` |
| `views/auth/` | `login.php` |
| `views/frontend/` | `contact.php`, `spmb/register.php`, `spmb/status.php` |
| `views/layouts/` | `backend.php` |
| `storage/` | **Hanya `.htaccess`** |
| `scripts/` | `security-bootstrap.php`, `migrate-security-data.php`, `verify-security-backup.php` |
| Root proyek | `.htaccess`, `version.json`, `CHANGELOG.md`, `README.md`, dan panduan ini |

Paket ini tidak menyertakan dependency/font/vendor yang tidak berubah. Bila
hosting masih memakai rilis sebelum 1.10.0 atau ada perubahan kode khusus di
hosting, cocokkan juga seluruh source/aset rilis dan perubahan khusus tersebut.
Dokumentasi audit dan skrip pengujian development tidak perlu diunggah.

## Urutan penerapan

1. Buat backup lengkap database, source, isi storage, dan konfigurasi hosting.
   Simpan cadangan kunci enkripsi secara terpisah dari arsip data dan pastikan
   dapat dipulihkan. Backup migrasi di langkah berikut bukan backup seluruh CMS.
2. Batasi akses situs sementara dari panel/web server selama upload dan migrasi.
   Mode maintenance CMS saja tidak memblokir semua endpoint API. Jangan menerima
   pendaftaran atau perubahan admin selama berkas masih diunggah sebagian.
3. Unggah file paket dengan struktur folder tetap. Aktifkan opsi menampilkan file
   tersembunyi agar kedua file `.htaccess` ikut terunggah.
4. Jalankan perintah berikut melalui SSH/Terminal cPanel dari root proyek:

   ```bash
   php scripts/migrate-security-data.php
   php scripts/migrate-security-data.php --apply
   ```

   CLI harus memakai database dan `DATA_ENCRYPTION_KEY` yang **sama** dengan
   website. Environment dari `SetEnv` Apache tidak otomatis tersedia di Terminal.
   `config/local.php` dimuat oleh skrip bila tersedia; jika konfigurasi melalui
   environment hosting, sediakan environment yang sama untuk proses CLI.

   Perintah pertama melengkapi struktur database dan menampilkan jumlah data.
   Perintah kedua membuat backup terenkripsi, memverifikasinya, mengenkripsi
   record/dokumen lama, dan membersihkan HTML yang diperlukan. Pastikan akun DB
   bisa `CREATE`/`ALTER` tabel saat migrasi serta storage bisa ditulis.

5. Verifikasi direktori backup yang **dicetak oleh migrasi pada hosting**:

   ```bash
   php scripts/verify-security-backup.php storage/backups/security-NAMA-DIREKTORI-DARI-OUTPUT
   ```

   Ganti placeholder dengan nama sebenarnya. Kunci enkripsi tidak disertakan di
   direktori ini; simpan kunci secara aman dan terpisah. Jika tidak ada akses CLI,
   minta operator hosting menjalankan perintah tersebut dengan environment yang
   sesuai. Skrip sengaja menolak eksekusi lewat browser.

6. Akses situs sekali untuk menyelesaikan pemeriksaan schema versi 1.10.1.
   Pastikan login, berita, dan cek status berjalan; admin/panitia berizin dapat
   membuka dokumen SPMB dan pengunjung anonim tidak dapat membukanya. Sesi lama
   perlu login ulang. Nomor registrasi lama tetap berlaku dengan NISN/tanggal lahir.
7. Setelah pemeriksaan berhasil, buka akses situs kembali. Salin backup lengkap
   dan backup migrasi ke penyimpanan cadangan terpisah dengan akses terbatas.

## File yang harus dipertahankan

- Jangan timpa `config/local.php`, `.env`, konfigurasi DB produksi, atau secret hosting.
- Jangan unggah `storage/.data-encryption-key` dari komputer lokal. Pertahankan
  kunci milik hosting atau `DATA_ENCRYPTION_KEY` produksi yang sudah digunakan.
- Jangan timpa isi `storage/spmb/`, `storage/uploads/`, folder media lainnya,
  `storage/cache/`, `storage/rate_limits/`, `storage/logs/`, atau backup hosting
  dengan data dari komputer lokal. Dari folder storage, paket hanya mengubah `.htaccess`.
- Jangan impor ulang seluruh `database/schema.sql` ke database produksi.
  File tersebut adalah dependensi migrasi/instalasi; jalankan skrip migrasi di atas.
- Tidak perlu mengunggah `.git`, `node_modules`, laporan audit, atau menjalankan
  build Node di hosting. CSS hasil build sudah termasuk paket.

Untuk rollback, gunakan backup lengkap pra-update beserta kunci aslinya.
Jangan hanya mengembalikan kode lama setelah data SPMB telah dienkripsi; kode
lama tidak mengenali format penyimpanan baru. Migrasi lokal pada komputer
pengembang tidak menggantikan migrasi terpisah pada database hosting.
