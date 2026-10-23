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
- Pertahankan kompatibilitas PHP 8.3+ dan pola yang sudah digunakan di repository.
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

Jalankan pemeriksaan yang proporsional dengan perubahan. Minimal:

1. Untuk setiap file PHP yang diubah, jalankan `php -l <file>`.
2. Untuk perubahan frontend/aset, jalankan `npm run build:css` dan pastikan artefak build yang relevan ikut diperbarui.
3. Untuk perubahan keamanan, autentikasi, sesi, izin, SPMB, atau penanganan data pribadi, jalankan:
   - `php scripts/test-security-patch.php`
   - `php scripts/test-security.php` bila database development tersedia.
   - `php scripts/test-http-security.php` bila situs development `http://rahayat-cms.test` dan database uji tersedia.
4. Untuk area akademik, kalender, migrasi, atau statistik pengunjung, jalankan skrip `scripts/test-*` yang namanya paling sesuai dengan area yang diubah.
5. Jalankan pemeriksaan tambahan yang tersedia dan relevan dengan bug/fitur yang dikerjakan.

Jika sebuah tes tidak dapat dijalankan karena layanan, database, konfigurasi, atau dependency eksternal tidak tersedia, jangan mengaku tes tersebut lulus. Catat penyebabnya dengan jelas. Kegagalan verifikasi yang disebabkan perubahan harus diperbaiki sebelum publikasi.

## Commit dan push wajib

Setelah perubahan yang diminta selesai dan semua verifikasi relevan yang dapat dijalankan telah lulus:

1. Tinjau `git status` dan diff. Pastikan hanya file yang berkaitan dengan tugas yang akan dimasukkan.
2. Pastikan branch aktif adalah `main`. Jangan memindahkan atau menggabungkan branch secara paksa. Jika bukan `main`, laporkan keadaan tersebut dan jangan push ke `origin/main` tanpa instruksi eksplisit pemilik.
3. Buat commit non-interaktif dengan pesan singkat dan spesifik dalam bentuk imperatif. Jangan amend commit lama kecuali diminta.
4. Push commit langsung dengan `git push origin main`.
5. Jangan pernah memakai `--force`, `--force-with-lease`, mengubah riwayat, atau melewati proteksi branch.
6. Jika remote lebih maju, gunakan alur aman yang mempertahankan riwayat (fetch lalu rebase/merge sesuai keadaan), selesaikan konflik hanya bila jelas, jalankan ulang verifikasi, kemudian push. Jangan menimpa perubahan remote.
7. Jika push gagal karena autentikasi, jaringan, proteksi branch, konflik yang ambigu, atau izin, laporkan error dan commit lokal yang sudah dibuat; jangan menyatakan perubahan telah dipublikasikan.

Kewajiban push ini berlaku untuk tugas yang memang mengubah repository. Untuk permintaan analisis, review, penjelasan, atau pemeriksaan tanpa perubahan file, jangan membuat commit kosong dan jangan push.

## Laporan akhir

Sampaikan secara ringkas:

- perubahan utama yang dilakukan;
- verifikasi yang dijalankan beserta hasilnya dan tes yang tidak dapat dijalankan;
- hash commit dan status push ke `origin/main` jika ada perubahan;
- risiko atau tindak lanjut yang benar-benar masih diperlukan.
