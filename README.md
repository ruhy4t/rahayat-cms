# Rahayat CMS

PHP 8.5 Native MVC dengan MariaDB/MySQL, kompatibel dengan PHP 8.4.

## Disclaimer Aplikasi

Aplikasi ini dibangun dengan cara VIBE CODING, full dengan bantuan AI.

Repository publik yang disarankan:

```text
https://github.com/ruhy4t/rahayat-cms
```

## Fitur Keamanan
- PDO Prepared Statements
- CSRF Token Protection
- XSS Filtering (htmlspecialchars)
- Password Argon2ID Hashing
- Secure Session Management

## Instalasi / Deployment

### 1. Setup Database

Untuk development lokal:

```sql
CREATE DATABASE schoolweb_db;
CREATE USER 'schoolweb_user'@'localhost' IDENTIFIED BY 'password-kuat-anda';
GRANT ALL PRIVILEGES ON schoolweb_db.* TO 'schoolweb_user'@'localhost';
FLUSH PRIVILEGES;
```

Import schema:
```bash
mysql -u root -p schoolweb_db < database/schema.sql
```

Untuk shared hosting/cPanel:

1. Buat database dan pengguna database melalui panel hosting.
2. Hubungkan pengguna tersebut ke database dengan hak akses yang diperlukan.
3. Buka phpMyAdmin dan pilih nama database yang diberikan hosting.
4. Import `database/schema.sql` ke database yang sudah dipilih.

`schema.sql` tidak membuat atau memilih nama database tertentu, sehingga aman
digunakan pada nama seperti `akunhosting_namadatabase`.

### 2. Konfigurasi Produksi
Set environment variable di virtual host / hosting panel:

```apache
SetEnv APP_DEBUG false
SetEnv APP_URL https://domain-sekolah.sch.id
SetEnv REQUIRED_DOMAIN_SUFFIX .sch.id
SetEnv UPDATE_ENABLED false
SetEnv UPDATE_BRANCH main
SetEnv SCHEMA_CHECK_ALWAYS false
SetEnv DB_HOST localhost
SetEnv DB_NAME schoolweb_db
SetEnv DB_USER schoolweb_user
SetEnv DB_PASS password-kuat-anda
SetEnv DATA_ENCRYPTION_KEY ganti-dengan-kunci-acak-minimal-32-karakter
```

Schema database diverifikasi sekali untuk setiap versi aplikasi lalu hasilnya
disimpan di `storage/cache/schema-version`. Hapus file tersebut atau set
`SCHEMA_CHECK_ALWAYS=true` hanya saat ingin memaksa pemeriksaan ulang.

`DATA_ENCRYPTION_KEY` digunakan untuk mengenkripsi kontak pribadi alumni.
Gunakan nilai acak yang kuat dan simpan di password manager/backup terenkripsi.
Jika variabel tersebut tidak disediakan, aplikasi membuat kunci lokal di
`storage/.data-encryption-key`. Jangan menghapus atau menimpa kunci tersebut
karena data yang telah dienkripsi tidak dapat dipulihkan tanpanya.

Rahayat CMS hanya dapat dijalankan online pada domain resmi sekolah Indonesia
dengan akhiran `.sch.id`. Domain lokal seperti `localhost`, `127.0.0.1`,
`.test`, dan `.local` tetap diizinkan untuk development.

### 3. Document Root
Untuk produksi, arahkan document root hosting ke folder `public`.

### 4. Installer Web
Jika belum ada konfigurasi lokal dan database belum terdeteksi, aplikasi akan
mengarahkan ke:

```text
/install
```

Installer akan:

- menguji koneksi database,
- membuat/import tabel awal,
- membuat akun admin awal,
- menulis `config/local.php`,
- mengunci halaman install setelah selesai.

Halaman install tetap mengikuti pembatasan domain `.sch.id` untuk deployment
online. Domain lokal seperti `localhost`, `.test`, dan `.local` hanya untuk
development.

### 5. Build Aset Frontend

Tailwind CSS, font, dan library antarmuka disajikan dari `public/` tanpa CDN.
File hasil build sudah disertakan dalam paket rilis, sehingga server production
tidak memerlukan Node.js. Pengembang yang mengubah class atau aset frontend
harus menjalankan:

```bash
npm install
npm run build:css
```

Commit `package-lock.json`, `public/css/tailwind.min.css`, `public/css/fonts.css`,
`public/fonts/`, dan `public/vendor/` bersama perubahan frontend.

## Login Admin
Segera ganti password admin setelah import database pertama kali.

### Pembaruan keamanan September 2026

Dokumen SPMB hanya dapat dibuka oleh admin atau GTK panitia yang memiliki izin
SPMB. Data pribadi pendaftaran disimpan dalam `private_payload` terenkripsi,
dan dokumen baru dienkripsi sebelum pendaftaran disimpan. Kunci yang digunakan
sama dengan `DATA_ENCRYPTION_KEY` atau `storage/.data-encryption-key`; jangan
mengganti/menghapus kunci yang masih digunakan. Simpan cadangan kunci secara
terpisah dari cadangan data, dengan akses terbatas.

Untuk deployment yang sudah memiliki data, sesudah mengunggah kode jalankan:

```bash
php scripts/migrate-security-data.php
php scripts/migrate-security-data.php --apply
php scripts/verify-security-backup.php storage/backups/security-YYYYMMDD-HHMMSS-xxxxxxxx
```

Perintah pertama memeriksa/melengkapi struktur database dan menampilkan jumlah
data yang perlu dimigrasikan. `--apply` membuat cadangan terenkripsi yang
diverifikasi sebelum mengenkripsi data lama dan membersihkan HTML berita.
Nama direktori cadangan sebenarnya dicetak oleh perintah migrasi. Verifikasi
cadangan tidak menulis ke database atau membuka data pribadi ke terminal.
Backup ini khusus data yang disentuh migrasi; tetap buat backup database,
storage, dan kunci secara lengkap serta simpan salinannya di lokasi terpisah.

Pembaruan ini meminta sesi lama login kembali. Perubahan password, role, izin,
status aktif, atau penghapusan akun mencabut sesi lama pada request berikutnya.
Timeout bawaan adalah 30 menit tanpa aktivitas dan maksimum 8 jam; gunakan
`SESSION_IDLE_SECONDS` dan `SESSION_MAX_SECONDS` untuk mengaturnya.

Throttle menggunakan alamat peer `REMOTE_ADDR` dan penyimpanan server. Jika
memakai reverse proxy/Cloudflare, konfigurasikan trusted proxy di web server
(misalnya `mod_remoteip` dengan daftar proxy tepercaya), serta batasi akses
langsung origin. Aplikasi tidak mempercayai header IP kiriman klien.

Cek status SPMB sekarang menggunakan POST, CSRF, nomor registrasi, NISN, dan
tanggal lahir. Nomor lama tetap dapat digunakan dengan verifikasi tersebut;
nomor baru acak. Nomor tidak lagi disimpan di localStorage. Blokir salin/cetak
untuk halaman dengan data pribadi tetap aktif.

Uji regresi pada lingkungan development yang memakai database lokal:

```bash
php scripts/test-security.php
php scripts/test-http-security.php
npm run build:css
```

Uji pertama memakai transaksi yang di-rollback; uji HTTP menggunakan akun,
berita, dokumen, dan pendaftaran sintetis yang dihapus setelah pengujian, dengan
target tetap `http://rahayat-cms.test`. Jalankan pada development, bukan sebagai
load test produksi. Pada PowerShell yang membatasi skrip, gunakan `npm.cmd`.

Audit akses dokumen/detail dan perubahan status SPMB disimpan di
`storage/logs/security-YYYY-MM-DD.jsonl` tanpa isi dokumen, NIK, atau nama murid.
Tentukan kebijakan retensi pendaftaran, log, dan backup sesuai kebutuhan sekolah;
pembaruan ini tidak menghapus data pribadi lama secara otomatis.

## Kontribusi

Kontribusi publik diterima melalui pull request. Baca [CONTRIBUTING.md](CONTRIBUTING.md)
sebelum mengirim perubahan.

## Pembaruan Sistem

Admin dapat mengecek pembaruan dari menu **Pembaruan**. Tombol eksekusi update
default nonaktif dan hanya aktif jika server diberi environment variable:

```apache
SetEnv UPDATE_ENABLED true
```

Update memakai `git fetch` dan `git pull --ff-only origin main`, sehingga akan
ditolak jika ada perubahan lokal yang belum dirapikan. Pastikan backup database
tersedia sebelum menjalankan update di hosting.

### Hosting Tanpa Git

Hosting tanpa Git tetap dapat mengetahui pembaruan dari menu **Pembaruan**.
Aplikasi membaca `version.json` terbaru dari GitHub dan membandingkannya dengan
`APP_VERSION` lokal.

Langkah update manual:

1. Backup database dari cPanel/phpMyAdmin.
2. Backup folder aplikasi lama atau compress folder domain menjadi ZIP.
3. Download ZIP terbaru dari GitHub:
   `https://github.com/ruhy4t/rahayat-cms/archive/refs/heads/main.zip`
4. Upload ZIP ke hosting lewat File Manager, FTP, atau SFTP.
5. Extract ZIP ke folder sementara, misalnya `rahayat-update`.
6. Pindahkan file dan folder aplikasi baru ke root domain.
7. Jangan timpa `config/local.php`, `storage/`, dan `public/uploads/`.
8. Jika ada file baru di `database/migrations`, jalankan SQL tersebut lewat
   phpMyAdmin.
9. Buka halaman depan dan admin untuk memastikan semuanya normal.

## Lisensi

Project ini memakai `Rahayat CMS Source Available License 1.0`.
Source code boleh dilihat, difork, dimodifikasi, dan dikontribusikan, tetapi
deployment hanya diizinkan pada domain `.sch.id`. Lisensi ini bersifat
source-available dan bukan lisensi open-source OSI seperti MIT/GPL.

## Struktur
```
/app
  /Controllers
  /Models
  /Core
/views
  /frontend
  /backend
  /layouts
/public (entry point)
/config
/storage/uploads
/database
```

