# Patch Keamanan Rahayat CMS 1.11.1

Rilis ini adalah **patch keamanan**. Tidak ada perubahan struktur database dan
tidak ada berkas SQL yang perlu diimpor. Isi situs, unggahan, pengaturan, dan
akun tidak tersentuh oleh pembaruan ini.

## Ringkasan yang diperbaiki

| Prioritas | Perbaikan |
| --- | --- |
| Kritis | Resolusi nama aksi pada router membuat daftar otorisasi peka huruf besar/kecil dapat dilewati (eskalasi ke administrator melalui API). |
| Kritis | Pengelolaan prestasi menentukan autentikasi dari URI, sehingga jalur simpan/hapus dapat dijalankan tanpa login. |
| Kritis | Rich text profil sekolah disimpan tanpa sanitasi lalu dicetak sebagai HTML (stored XSS dari akun GTK ke sesi administrator). |
| Tinggi | Otorisasi panel admin dan API kini menolak secara default; ace yang tidak dipetakan ke izin tidak dapat dijalankan akun non-admin. |
| Tinggi | Nilai ter-escape di dalam string JavaScript pada tombol admin kategori/menu. |
| Tinggi | Murid/ekskul dapat menyunting dan menurunkan berita penulis lain. |
| Sedang | Validasi skema URL (tautan SPMB, embed peta, tautan manifes), redirect pasca-login, rotasi CSRF saat login, izin dan batas laju unggahan editor, kredensial status SPMB, `UPDATE_BRANCH`. |
| Rendah | Guard CLI pada migrasi PHP dan pengerasan aturan Apache (`scripts`, metadata root, `*.bak`, berkas aktif di `storage`). |

## Berkas yang diunggah

Unggah dengan **mempertahankan struktur folder** (timpa berkas lama). Semua
berkas di bawah ini berada di luar `public/`, jadi wajib ikut diunggah.

**Kode yang diperbaiki (20 berkas)**

```text
.htaccess
app/Core/Router.php
app/Core/Security.php
app/Core/AuthSession.php
app/Core/AppUpdater.php
app/Models/SchoolProfile.php
app/Controllers/Controller.php
app/Controllers/ApiController.php
app/Controllers/AuthController.php
app/Controllers/DashboardController.php
app/Controllers/PrestasiController.php
app/Controllers/SPMBController.php
app/Controllers/UploadController.php
views/backend/categories/index.php
views/backend/menus/index.php
views/backend/system/update.php
views/frontend/contact.php
database/migrations/create_ekstrakurikuler_table.php
database/migrations/add_spmb_npsn.php
database/migrations/add_spmb_address_fields.php
```

**Metadata versi (4 berkas)**

```text
config/app.php
version.json
CHANGELOG.md
DEPLOYMENT-1.11.1.md
```

**Opsional (tidak wajib untuk produksi)**

```text
scripts/test-security-patch.php          # uji regresi CLI
app/audit-reports/security-patch-20261001.md
.gitignore
README.md
```

## Langkah

1. Cadangkan database dan folder aplikasi (minimal `storage/` dan
   `config/local.php`) sebelum menimpa berkas.
2. Unggah berkas di atas ke root aplikasi, timpa versi lama.
   Jangan menimpa `config/local.php`, `storage/`, dan `public/uploads/`.
3. Tidak ada SQL yang perlu dijalankan.
4. Muat ulang beranda dan panel admin dengan Ctrl+F5.
   Karena `APP_VERSION` berubah, aplikasi otomatis memverifikasi skema sekali
   dan menulis ulang penanda di `storage/cache/schema-version`. Tidak perlu
   menghapus penanda tersebut secara manual.

## Verifikasi pasca-unggah

- Beranda, berita, profil, galeri, kontak, dan login tampil normal.
- `POST /prestasi/save` atau `/prestasi/delete/1` **tanpa login** harus
  dialihkan ke halaman login, bukan mengubah data.
- `/api/news` tetap dapat diakses publik; `/api/csrf-token` menjawab 401 tanpa
  sesi.
- Jika tersedia akses CLI: `php scripts/test-security-patch.php` harus
  melaporkan `PASS: 60 security-patch checks`.

## Setelah pembaruan

- Ganti password administrator bila instalasi masih memakai akun bawaan
  `database/schema.sql`, dan hapus atau blokir `/install` setelah selesai.
- Simpan kunci enkripsi (`DATA_ENCRYPTION_KEY` atau
  `storage/.data-encryption-key`) terpisah dari cadangan data.

## Rollback

Simpan salinan berkas lama sebelum menimpa. Untuk mengembalikan rilis
sebelumnya, unggah ulang berkas versi 1.11.0 dengan daftar yang sama
(tidak ada perubahan database yang perlu dibalik).
