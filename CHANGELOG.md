# Changelog

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
