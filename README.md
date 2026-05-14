# Rahayat CMS

PHP 8.3/8.4 Native MVC dengan MariaDB/MySQL.

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

### 2. Konfigurasi Produksi
Set environment variable di virtual host / hosting panel:

```apache
SetEnv APP_DEBUG false
SetEnv APP_URL https://domain-sekolah.sch.id
SetEnv REQUIRED_DOMAIN_SUFFIX .sch.id
SetEnv UPDATE_ENABLED false
SetEnv UPDATE_BRANCH main
SetEnv DB_HOST localhost
SetEnv DB_NAME schoolweb_db
SetEnv DB_USER schoolweb_user
SetEnv DB_PASS password-kuat-anda
```

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

## Login Admin
Segera ganti password admin setelah import database pertama kali.

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

