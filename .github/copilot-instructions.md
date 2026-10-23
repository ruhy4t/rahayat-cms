# GitHub Copilot Instructions

Instruksi ini berlaku untuk seluruh repository Rahayat CMS. Baca juga `/AGENTS.md`; jika ada perbedaan, ikuti instruksi eksplisit pemilik repository pada tugas aktif, lalu `/AGENTS.md`, kemudian file ini.

## Konteks proyek

- Rahayat CMS adalah aplikasi PHP native MVC dengan MariaDB/MySQL.
- Pertahankan kompatibilitas PHP 8.4 dan 8.5.
- Frontend menggunakan Tailwind CSS dan aset lokal yang dibangun melalui `package.json`.
- Entry point publik berada di `public/index.php`; routing utama berada di `app/Core/App.php`.
- Ikuti struktur, pola, penamaan, dan bahasa antarmuka yang sudah digunakan. Jangan memasukkan framework baru tanpa permintaan eksplisit.

## Batas perubahan

- Ubah hanya file yang diperlukan untuk tugas aktif.
- Jangan melakukan refactor, perubahan format massal, migrasi, atau pembaruan dependency yang tidak diminta.
- Jangan menghapus atau menimpa perubahan lokal yang tidak berkaitan dengan tugas.
- Cari implementasi serupa sebelum menambahkan controller, model, view, route, atau helper baru.
- Jangan membuat duplikat kelas. Autoloader aplikasi mencari kelas langsung di `app/Core/`, `app/Controllers/`, dan `app/Models/`.
- Jangan mengedit file hasil build secara manual. Ubah sumbernya lalu jalankan build yang sesuai.

## Keamanan dan privasi

- Gunakan PDO prepared statements; jangan menyusun query dari input pengguna.
- Semua aksi yang mengubah data harus memeriksa metode HTTP, autentikasi/otorisasi, dan CSRF.
- Escape output teks dengan helper yang sesuai. Sanitasi rich text dan validasi URL sebelum disimpan atau ditampilkan.
- Terapkan otorisasi secara default-deny pada controller, API, file privat, dan data SPMB.
- Jangan mempercayai header IP klien tanpa konfigurasi trusted proxy.
- Jangan melemahkan CSP, session cookie, rate limiting, pembatasan domain `.sch.id`, atau validasi upload.
- Jangan commit credential, token, dump database, data pribadi, dokumen SPMB, isi runtime `storage/`, backup lokal, atau kunci `DATA_ENCRYPTION_KEY`.
- Jangan menampilkan data sensitif di log, pesan error, fixture, screenshot, atau output terminal.

## Database dan migrasi

- Pertahankan kompatibilitas MariaDB/MySQL dan gunakan `utf8mb4`.
- Migrasi harus idempoten bila memungkinkan dan aman untuk instalasi yang telah memiliki data.
- Jangan menjalankan migrasi `--apply` atau pengujian terhadap database produksi.
- Tes database hanya boleh memakai database development khusus; fixture harus di-rollback atau dibersihkan.

## Frontend, tema, dan aksesibilitas

- Pertahankan keempat tema publik: `indigo-modern`, `emerald-campus`, `crimson-bold`, dan `cendekia-smp`, kecuali tugas secara eksplisit mengubah cakupan tema.
- Periksa perubahan tampilan pada desktop dan mobile. Hindari overflow horizontal, teks terpotong, dan kontrol yang saling bertumpuk.
- Gunakan elemen semantik, label yang terlihat, focus state, `aria-*` yang tepat, kontras memadai, dan dukungan `prefers-reduced-motion`.
- Gunakan lazy loading untuk gambar non-kritis. Gambar hero pertama boleh eager dengan `fetchpriority="high"`; gambar berikutnya harus lazy.
- Pertahankan aset produksi lokal dan jangan menambahkan CDN tanpa persetujuan.
- Setelah perubahan class, CSS, JavaScript, font, atau vendor frontend, jalankan `npm run build:css` dan commit artefak build yang berubah.

## Verifikasi

Untuk setiap perubahan kode, jalankan baseline berikut:

```text
php -l <setiap file PHP yang diubah>
php scripts/test-security-patch.php
node scripts/test-academic-calendar.mjs
node scripts/test-migration-ui.mjs
php scripts/test-visitor-statistics.php
```

Tambahkan tes sesuai area perubahan:

- Model, schema, autentikasi, izin, SPMB, kalender, atau migrasi:
  - `php scripts/test-security.php`
  - `php scripts/test-academic.php`
  - `php scripts/test-web-migration.php` untuk schema/migrasi
- Route, controller, autentikasi HTTP, atau tampilan terkait:
  - `php scripts/test-http-security.php`
  - `php scripts/test-academic-http.php` untuk area akademik
- Frontend atau aset:
  - `npm run build:css`
  - pastikan build tidak meninggalkan diff yang tidak disengaja

Jangan menyatakan tes lulus jika tes tidak dijalankan. Jika tes wajib untuk area yang diubah tidak tersedia, laporkan alasannya dan jangan push tanpa persetujuan eksplisit pemilik repository.

## Git dan hasil akhir

- Tinjau diff dan `git status`; masukkan hanya perubahan yang berkaitan dengan tugas.
- Jangan force-push, mengubah riwayat, melewati proteksi branch, atau memasukkan file rahasia/runtime.
- Jangan membuat commit kosong untuk tugas analisis tanpa perubahan.
- Dalam laporan akhir, sebutkan perubahan utama, tes yang dijalankan, tes yang tidak dapat dijalankan, dan risiko yang masih tersisa.
