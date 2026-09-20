# Pembaruan Rahayat CMS 1.11.0

Pembaruan ini menambahkan Pesan Kepala Sekolah, Agenda Kegiatan, Kalender Pendidikan,
dan statistik pengunjung footer. Paket pembaruan ditujukan untuk instalasi 1.10.3
yang sudah menerapkan migrasi keamanan sebelumnya. Konfigurasi lokal, akun,
unggahan, isi profil, dan kegiatan yang sudah tersimpan tidak ditimpa oleh SQL ini.

## Langkah produksi melalui phpMyAdmin dan File Manager

1. Cadangkan database dan file aplikasi yang akan ditimpa.
2. Pilih database aplikasi di phpMyAdmin. Impor
   `database/migrations/academic_features_1_11_0_idempotent.sql`.
   SQL menambahkan `academic_years` dan `school_events` menggunakan
   `CREATE TABLE IF NOT EXISTS`. Aman diimpor ulang pada struktur standar versi ini,
   tanpa menghapus atau mengisi ulang data. Tidak diperlukan ALTER tabel profil.
3. Jika tabel `site_visits` belum tersedia, impor
   `database/migrations/create_site_visits_table.sql` untuk statistik footer.
4. Unggah isi paket pembaruan ke root aplikasi dengan mempertahankan struktur
   folder. Folder yang memuat `app`, `config`, `views`, dan `public` adalah root aplikasi.
   Pastikan `storage/.htaccess` ikut diunggah, termasuk bila File Manager menyembunyikan
   dotfiles. Jangan menimpa `config/local.php` atau unggahan lainnya.
5. Pastikan proses PHP dapat menulis ke `storage`. Folder `storage/academic_documents`
   dibuat ketika PDF pertama diunggah. PDF dibatasi 10 MB; batas `upload_max_filesize`
   dan `post_max_size` hosting juga harus memadai.
6. Buka beranda dan admin, lalu muat ulang dengan Ctrl+F5. Jika hosting menonaktifkan
   validasi timestamp OPcache, reset OPcache/restart PHP dari panel hosting.

Jangan mengimpor `database/schema.sql` ke instalasi produksi yang sudah berjalan:
file tersebut untuk instalasi baru. Pemeriksaan skema aplikasi juga mencoba menambahkan
dua tabel baru saat versi berubah, tetapi impor SQL sebelum unggah lebih mudah diverifikasi
di hosting dengan izin DDL terbatas. File migrasi harus tetap ikut diunggah.

## Pengaturan admin

### Pesan Kepala Sekolah

Lokasi: **Admin → Profil Sekolah → tab Pesan Kepala Sekolah**.

- Atur judul, pesan singkat (disarankan 50–80 kata), pesan lengkap, dan teks tombol.
- Centang **Tampilkan pesan di beranda**. Bagian ini belum tampil sebelum diaktifkan
  dan ringkasan diisi, sehingga tidak menerbitkan pesan baru tanpa isian admin.
- Foto dan nama memakai data profil yang sudah tersedia. Jika ada GTK yang ditandai
  sebagai kepala sekolah, data GTK tersebut menjadi sumber sesuai perilaku profil lama.
- Tombol ke `/pesan-kepala-sekolah` muncul jika pesan lengkap terisi.
- Pesan lengkap yang sudah ada tetap dipertahankan saat pembaruan.

### Tahun pelajaran dan Kalender Pendidikan

Lokasi: **Admin → Kalender Pendidikan** (khusus akun admin).

1. Buka **Tambah tahun pelajaran**. Isian `2026` berarti Juli 2026–Juni 2027.
2. Opsional: unggah PDF. Centang publikasi saat kalender siap ditampilkan.
3. Tambahkan kegiatan, pilih tahun pelajaran, kategori, rentang tanggal, waktu/lokasi
   jika diperlukan, keterangan, dan status publikasi.
4. Kegiatan hanya tampil ketika **kegiatan dan tahun pelajarannya sama-sama publik**.
5. Penggantian/lepas PDF berlaku hanya untuk tahun pelajaran yang diedit. Data tahun
   lain tidak dihapus. Dokumen diakses melalui route unduh; direktori dokumen diblokir
   dari akses langsung, termasuk untuk tahun pelajaran yang masih draft.

Tanggal mulai dan selesai harus berada dalam Juli–Juni tahun yang dipilih.
Kegiatan lintas bulan cukup satu entri dan tampil pada seluruh tanggal terkait.
Kalender otomatis membuka tahun pelajaran/bulan saat ini. Arsip tetap bisa dipilih;
kalender lama tidak ditampilkan sebagai kalender tahun pelajaran aktif.

### Agenda Kegiatan

Lokasi: **Admin → Agenda Kegiatan** (khusus akun admin).

- Buat tahun pelajaran melalui Kalender Pendidikan terlebih dahulu.
- Atur judul, kategori, tanggal, waktu/lokasi opsional, keterangan, dan draft/publikasi.
- Centang **Tampilkan juga di Kalender Pendidikan** bila agenda perlu muncul di kalender.
  Edit agenda tetap dilakukan di menu Agenda Kegiatan; tidak ada salinan data kedua.
- Agenda yang tanggal selesainya sebelum hari ini otomatis masuk daftar arsip.
- Daftar admin dapat difilter menurut tahun pelajaran. Data lama tidak dihapus otomatis.

## Frontend

- Footer semua tema memiliki tiga bagian seperti Indigo Modern: identitas sekolah,
  Tautan Penting, dan Ikuti Kami beserta statistik pengunjung.
- Semua tema: pesan setelah banner/akses cepat, kemudian berita, lalu agenda dan kalender.
  Pada Cendekia, blok berita ditempatkan sebelum fasilitas untuk mengikuti urutan ini.
- Agenda dan kalender berdampingan di desktop, bertumpuk di layar kecil.
- Beranda menampilkan tiga agenda terdekat. Kalender menandai tanggal berdasarkan kategori;
  klik tanggal untuk melihat keterangannya. Tombol semua tanggal mengembalikan daftar bulanan.
- Navigasi bulan dan tahun memakai tautan/form server sehingga tetap bekerja tanpa JavaScript.
  Tanpa JavaScript, daftar kegiatan bulan tersebut tetap terlihat.
- Halaman lengkap: `/agenda`, `/agenda?arsip=1`, dan `/kalender-pendidikan`.
  Admin dapat menambahkan URL tersebut ke navigasi melalui pengelolaan menu yang sudah ada.
- Bagian kosong disembunyikan dari beranda. Arsip kalender yang sudah dipublikasikan tetap
  dapat diakses dari pilihan tahun meskipun kalender tahun berjalan belum tersedia.
- Perhitungan tanggal mengikuti `APP_TIMEZONE`, default Asia/Jakarta.
- Statistik footer menggunakan pengunjung unik per sesi dan total akses halaman yang
  sudah tercatat, bukan identitas orang lintas perangkat.

## Verifikasi

- SQL baru sudah diimpor dua kali pada database pengembangan tanpa error.
- `php scripts/test-academic.php`: tanggal, batas tahun, leap year, rentang lintas bulan,
  draft, XSS, CRUD model, serta rendering empat tema; fixture database di-rollback.
- `node scripts/test-academic-calendar.mjs`: pemilihan tanggal, kegiatan berimpitan,
  keadaan kosong, reset, dan atribut aksesibilitas.
- `php scripts/test-academic-http.php`: akun sintetis lokal, POST/CSRF, izin, CRUD,
  upload/unduh PDF, blokir dokumen langsung/draft, dan simpan/tampil/sembunyikan pesan.
  Data uji dibersihkan dan profil sebelumnya dipulihkan.
- Pengujian statistik pengunjung, HTTP keamanan lama, dan UI migrasi lama tetap lulus.
- Browser pengujian tidak terhubung saat pengerjaan: verifikasi visual screenshot pada
  desktop/ponsel belum dilakukan. Struktur HTML semua tema dan perilaku JavaScript diuji.

Script pengujian tidak perlu diunggah ke produksi. Paket pembaruan tidak menyertakannya.
