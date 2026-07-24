# Changelog

Semua perubahan penting Rahayat CMS dicatat di file ini. Penomoran versi
mengikuti [Semantic Versioning](https://semver.org/):

- `MAJOR`: perubahan besar yang tidak kompatibel dengan versi sebelumnya.
- `MINOR`: fitur baru yang tetap kompatibel.
- `PATCH`: perbaikan bug atau keamanan tanpa fitur besar.

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
