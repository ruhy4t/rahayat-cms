# Rahayat CMS

PHP 8.3 Native MVC dengan MariaDB/MySQL.

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

## Login Admin
Segera ganti password admin setelah import database pertama kali.

## Kontribusi

Kontribusi publik diterima melalui pull request. Baca [CONTRIBUTING.md](CONTRIBUTING.md)
sebelum mengirim perubahan.

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

