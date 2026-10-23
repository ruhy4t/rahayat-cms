# AGENTS.md

Instruksi ini berlaku untuk seluruh repository Rahayat CMS.

## Prioritas kerja

- Ikuti instruksi eksplisit pemilik repository pada percakapan aktif. Jika instruksi tersebut bertentangan dengan dokumen proyek, jelaskan konflik secara singkat lalu utamakan permintaan terbaru pemilik selama aman dan memungkinkan.
- Kerjakan permintaan sampai tuntas: inspeksi konteks, implementasikan perubahan, jalankan verifikasi yang relevan, perbaiki kegagalan yang disebabkan perubahan, lalu publikasikan hasil sesuai bagian **Commit dan push wajib**.
- Jangan berhenti hanya pada rencana atau analisis apabila perubahan dapat dikerjakan langsung.
- Buat asumsi yang wajar untuk detail kecil. Tanyakan hanya jika keputusan yang hilang dapat mengubah hasil secara material, membutuhkan kredensial/izin baru, atau berisiko merusak data.
- Jangan mengubah, menghapus, atau memasukkan ke commit pekerjaan lokal milik pengguna yang tidak terkait dengan tugas.

## Gambaran proyek

- Aplikasi menggunakan PHP native MVC dengan MariaDB/MySQL.
- Pertahankan kompatibilitas PHP 8.4 dan 8.5 serta pola yang sudah digunakan di repository.
- Aset frontend dibangun dengan Node.js, Tailwind CSS, dan skrip pada `package.json`.
- Jangan commit rahasia, kredensial, dump database produksi, data pribadi, dokumen SPMB, isi runtime `storage/`, atau artefak audit sementara.
- Gunakan prepared statements untuk query database, proteksi CSRF untuk aksi yang mengubah data, escaping output yang sesuai, dan validasi otorisasi pada endpoint sensitif.

## Cara mengubah kode

- Baca file terkait dan cari implementasi serupa sebelum mengedit.
- Buat perubahan sekecil dan seterfokus mungkin; jangan melakukan refactor yang tidak diminta.
- Pertahankan gaya, struktur, penamaan, dan bahasa antarmuka yang sudah ada.
- Jika mengubah dependency atau pipeline aset, perbarui lockfile dan artefak build yang memang dilacak repository.
- Jangan menjalankan migrasi yang mengubah data nyata atau pengujian HTTP terhadap produksi. Gunakan lingkungan development/lokal saja.

## Verifikasi wajib

Jalankan pemeriksaan yang proporsional dengan perubahan. Baseline yang tidak membutuhkan database wajib dijalankan untuk setiap perubahan kode:

1. Jalankan `php -l <file>` untuk setiap file PHP yang diubah. Untuk perubahan lintas-komponen, lint seluruh file PHP yang dilacak Git.
2. Jalankan `php scripts/test-security-patch.php`.
3. Jalankan `node scripts/test-academic-calendar.mjs`.
4. Jalankan `node scripts/test-migration-ui.mjs`.
5. Jalankan `php scripts/test-visitor-statistics.php` bila ekstensi SQLite tersedia.
6. Untuk perubahan frontend/aset, jalankan `npm run build:css` dan pastikan artefak build yang relevan ikut diperbarui.

Tes database berikut wajib untuk perubahan pada model, schema, keamanan, autentikasi, sesi, izin, SPMB, kalender akademik, atau migrasi:

- `php scripts/test-security.php`
- `php scripts/test-academic.php`
- `php scripts/test-web-migration.php` untuk perubahan migrasi atau schema

Tes HTTP berikut wajib untuk perubahan routing, controller, autentikasi/otorisasi HTTP, atau tampilan yang diuji olehnya:

- `php scripts/test-http-security.php`
- `php scripts/test-academic-http.php` untuk area akademik

Tes database dan HTTP hanya boleh dijalankan pada database development khusus dan host lokal `localhost`, `127.0.0.1`, `.test`, atau `.local`. Jangan menjalankannya terhadap produksi. Jangan menjalankan migrasi dengan `--apply` kecuali diminta secara eksplisit dan target telah dipastikan sebagai data development yang dapat dipulihkan.

Jika sebuah tes tidak dapat dijalankan karena layanan, database, konfigurasi, atau dependency eksternal tidak tersedia, jangan mengaku tes tersebut lulus. Untuk perubahan pada area yang mewajibkan tes tersebut, jangan push tanpa persetujuan eksplisit pemilik repository. Kegagalan verifikasi yang disebabkan perubahan harus diperbaiki sebelum publikasi.

## Commit dan push wajib

Setelah perubahan yang diminta selesai dan semua verifikasi relevan yang dapat dijalankan telah lulus:

1. Tinjau `git status` dan diff. Pastikan hanya file yang berkaitan dengan tugas yang akan dimasukkan.
2. Jalankan `git fetch origin main` dan pastikan perubahan lokal berbasis pada `origin/main`. Jangan menimpa perubahan remote.
3. Pastikan branch aktif adalah `main`. Jangan memindahkan atau menggabungkan branch secara paksa. Jika bukan `main`, laporkan keadaan tersebut dan jangan push ke `origin/main` tanpa instruksi eksplisit pemilik.
4. Buat commit non-interaktif dengan pesan singkat dan spesifik dalam bentuk imperatif. Jangan amend commit lama kecuali diminta.
5. Push commit langsung dengan `git push origin main`.
6. Jangan pernah memakai `--force`, `--force-with-lease`, mengubah riwayat, atau melewati proteksi branch.
7. Jika remote lebih maju, gunakan alur aman yang mempertahankan riwayat (fetch lalu rebase/merge sesuai keadaan), selesaikan konflik hanya bila jelas, jalankan ulang verifikasi, kemudian push. Jangan menimpa perubahan remote.
8. Jika push gagal karena autentikasi, jaringan, proteksi branch, konflik yang ambigu, atau izin, laporkan error dan commit lokal yang sudah dibuat; jangan menyatakan perubahan telah dipublikasikan.

## Rilis

Untuk perubahan versi, sinkronkan `APP_VERSION` di `config/app.php`, `version.json`, `CHANGELOG.md`, dokumen deployment, Git tag, dan GitHub Release. Jangan membuat atau memindahkan tag versi tanpa memeriksa commit rilis yang benar.

Kewajiban push ini berlaku untuk tugas yang memang mengubah repository. Untuk permintaan analisis, review, penjelasan, atau pemeriksaan tanpa perubahan file, jangan membuat commit kosong dan jangan push.

## Laporan akhir

Sampaikan secara ringkas:

- perubahan utama yang dilakukan;
- verifikasi yang dijalankan beserta hasilnya dan tes yang tidak dapat dijalankan;
- hash commit dan status push ke `origin/main` jika ada perubahan;
- risiko atau tindak lanjut yang benar-benar masih diperlukan.
