# Changelog

## Unreleased

- Tailwind CSS 4, font tema, CKEditor, SweetAlert, Chart.js, dan SortableJS kini dibangun atau disajikan lokal tanpa ketergantungan CDN production.
- CSP response header diperketat ke sumber lokal dan cache aset statis diperpanjang dengan cache busting.
- Hero seluruh tema memperoleh prioritas LCP yang konsisten; gambar berita dan ekstrakurikuler di bawah fold memakai lazy loading.
- Pemeriksaan schema dicache per versi aplikasi agar request normal tidak menjalankan query versi berulang.
- Runtime utama ditetapkan ke PHP 8.5 dengan kompatibilitas minimum PHP 8.4.

## 1.7.1 - 2026-08-09

- Kolom kontak verifikasi alumni dipisahkan menjadi nomor WhatsApp dan email, termasuk pada pengelolaan admin dan ekspor Excel.
- Pilihan informasi alumni yang boleh ditampilkan dirapikan menjadi checkbox kotak yang sejajar dan mendukung banyak pilihan.
- Keterangan tambahan pada verifikasi keamanan dihapus.
- Seluruh unggahan gambar diperkuat dengan batas keras maksimal 2 MB, termasuk pemeriksaan ulang setelah optimasi, watermark, atau crop.
- Foto alumni dan testimoni dibatasi maksimal 1 MB pada formulir publik, panel admin, dan validasi server.

## 1.7.0 - 2026-08-06

- Editor foto alumni 4:5 memungkinkan admin menggeser dan memperbesar foto agar wajah tampil jelas.
- Tujuan setelah lulus menjadi wajib dan menyediakan pilihan `Tidak Melanjutkan`; status serta nama sekolah tujuan juga wajib untuk jalur pendidikan.
- Status pekerjaan/aktivitas alumni disimpan sebagai data terstruktur.
- Dashboard admin alumni menampilkan tiga grafik batang: tujuan setelah lulus, status sekolah tujuan, serta status pekerjaan/aktivitas.
- Data alumni dapat diunduh sebagai berkas Excel lengkap dengan header, filter, dan baris judul beku.
- Migrasi terstruktur idempoten tersedia di `database/migrations/add_alumni_analytics_1_7_0.sql`, aman dijalankan ulang melalui phpMyAdmin, dan juga dijalankan otomatis oleh perbaikan skema.

## 1.6.0 - 2026-08-06

- Kartu direktori alumni dibuat lebih ringkas agar foto tidak mendominasi tampilan.
- Alumni tanpa foto kini menggunakan inisial yang tampil utuh di dalam avatar lingkaran.
- Formulir publik dan admin menyediakan pilihan tujuan setelah lulus: SMA, SMK, MA, pesantren, Paket C, bekerja, tidak/belum melanjutkan, atau lainnya.
- Status sekolah negeri/swasta dan nama sekolah tujuan dapat dicatat tanpa mengubah skema database.
- Panel admin menampilkan tujuan lanjutan alumni secara langsung pada tabel pengelolaan.

## 1.5.0 - 2026-07-28

- Direktori dan pencarian alumni berdasarkan nama, angkatan, kota, serta pekerjaan.
- Pendaftaran alumni mandiri dengan verifikasi administrator dan pilihan data yang boleh dipublikasikan.
- Kontak pribadi alumni dienkripsi menggunakan AES-256-GCM dan tidak dikirim ke halaman publik.
- Profil alumni inspiratif, pagination, avatar ringan, dan panel pengelolaan admin.
- Perlindungan konten alumni diperkuat dengan watermark, anti-klik kanan, anti-seleksi, anti-cetak, serta penghalang shortcut tangkap layar.
- Migrasi gabungan tersedia di `database/migrations/add_engagement_features_1_5_0.sql`.

## 1.4.0 - 2026-07-28

- Formulir testimoni publik untuk orang tua, alumni, siswa, mitra, dan pihak terkait.
- Moderasi testimoni oleh admin, termasuk persetujuan, penolakan, edit, unggulan, dan input manual.
- Foto/avatar testimoni opsional dengan fallback inisial dan lazy loading.
- Semua upload gambar dibatasi maksimal 2 MB serta diperiksa tipe MIME dan dimensinya.
- Halaman detail prestasi dan ekstrakurikuler serta tombol WhatsApp saran/aduan.

## 1.3.2 - 2026-07-25

- Galeri video mendukung Instagram Reel/Video, TikTok, Facebook Video, dan tautan media sosial lainnya.
- Instagram, TikTok, dan Facebook ditampilkan sebagai embed ketika URL dapat dikenali.
- Sumber lain ditampilkan sebagai kartu tautan aman menuju platform asal.

## 1.3.1 - 2026-07-25

- Penetapan Kepala Sekolah langsung dari data GTK dan sinkronisasi nama, NIP, serta foto ke laman Profil.
- Galeri video mendukung YouTube, Vimeo, dan tautan video langsung (MP4/WebM).
- Sumber setiap video ditampilkan pada halaman pengelolaan admin.
- Upload gambar web tetap dioptimalkan otomatis ke WebP dengan dimensi dan kualitas yang menjaga kejelasan.

Semua perubahan penting Rahayat CMS dicatat di file ini. Penomoran versi
mengikuti [Semantic Versioning](https://semver.org/):

- `MAJOR`: perubahan besar yang tidak kompatibel dengan versi sebelumnya.
- `MINOR`: fitur baru yang tetap kompatibel.
- `PATCH`: perbaikan bug atau keamanan tanpa fitur besar.

## [1.3.0] - 2026-07-25

### Ditambahkan

- Editor crop foto kepala sekolah dengan drag, zoom, reset posisi, dan preview.
- Normalisasi foto kepala sekolah menjadi portrait rasio 4:5 berukuran 800 x 1000 piksel.

### Diperbaiki

- Daftar menu sidebar admin kini dapat di-scroll pada layar pendek.
- Preview foto lokal pada editor crop tidak lagi diblokir oleh Content Security Policy.

## [1.2.0] - 2026-07-24

### Ditambahkan

- Editor crop foto GTK dengan drag, zoom, reset posisi, dan preview.
- Normalisasi foto GTK menjadi portrait rasio 4:5 berukuran 800×1000 piksel.
- Validasi hasil crop kembali di server sebelum gambar disimpan.

### Diubah

- Kartu GTK menampilkan foto close-up secara konsisten dengan fokus area wajah.
- Foto GTK dikecualikan dari watermark agar wajah tetap terlihat jelas.
- Foto lama GTK dibersihkan setelah pembaruan atau penghapusan data berhasil.

## [1.1.1] - 2026-07-24

### Diperbaiki

- `database/schema.sql` tidak lagi memaksa membuat database `schoolweb_db`.
- Perintah `USE schoolweb_db` dihapus agar schema dapat diimpor ke nama
  database apa pun yang diberikan oleh shared hosting.
- Petunjuk instalasi cPanel/phpMyAdmin diperjelas.

## [1.1.0] - 2026-07-24

### Ditambahkan

- Modul Informasi Publik di panel admin.
- Popup informasi dengan teks, foto, status aktif, urutan, dan jadwal tayang.
- Popup muncul setiap kali halaman beranda dibuka atau direfresh.
- Slider teks aktif dan terjadwal di bawah navigasi.
- CAPTCHA gambar adaptif setelah tiga kegagalan login.
- Honeypot sederhana untuk membantu menyaring bot login.

### Diubah

- Batas layout publik diperlebar menjadi 1440 piksel pada layar besar.
- Login dikunci selama dua menit setelah lima kegagalan.
- Pesan login menampilkan sisa percobaan dan waktu tunggu.
- Pembatasan login disimpan di server berdasarkan username dan alamat IP.
- Konstanta koneksi MySQL diperbarui untuk kompatibilitas PHP 8.5.

### Keamanan

- CAPTCHA berlaku dua menit dan hanya dapat digunakan satu kali.
- Percobaan gagal tidak dapat direset hanya dengan menghapus cookie browser.
- Hitungan kegagalan dibersihkan setelah login berhasil.

## [1.0.2]

- Versi stabil sebelumnya dengan perbaikan skema hosting, profil, menu, dan
  pengaturan dasar CMS.
