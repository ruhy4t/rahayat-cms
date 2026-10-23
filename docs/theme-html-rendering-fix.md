# Patch teks HTML pada empat tema baru

Patch lanjutan setelah tag v1.12.0; tidak mengubah versi rilis/tag yang sudah
diterbitkan. Berlaku untuk Nawala, Arunika, Wibawa, dan Samudra. Tema lama,
data sekolah, editor admin, dan struktur database tidak diubah.

## Penyebab dan perbaikan

Admin memakai editor HTML untuk `school_profile.motto`. Tema baru mencetak
moto melalui `e()` langsung sehingga `<p>`, `<strong>`, dan tag lain terlihat
sebagai tulisan di hero, header/footer, banner halaman, profil, dan kalender.
Pada ringkasan kartu, `strip_tags()` sebelum decoding juga tidak membersihkan
tag yang telah tersimpan sebagai entitas HTML.

`PublicTheme::summary()` mendekode entitas sebelum menghapus tag, memberi
spasi pada batas paragraf/baris, dan merapikan whitespace. Semua pemanggil
tetap menggunakan `e()` untuk output teks. Helper bukan sanitizer HTML atau
validator URL. Artikel/profil lengkap tetap melewati sanitizer HTML yang ada.
Tidak ada pencetakan HTML mentah atau perubahan sanitizer global.

## Unggah produksi

Untuk instalasi yang sudah lengkap pada 1.12.0, unggah **13 file** pada
`production-upload-theme-html-fix.txt` dari commit patch terbaru di main.
Path relatif terhadap root aplikasi; pertahankan struktur folder. Unggah
helper dan view sebagai satu patch, bukan hanya view. Cadangkan kode lama,
gunakan maintenance/pergantian direktori atomik agar file tidak tercampur,
kemudian revalidate OPcache dan purge cache HTML/CDN jika digunakan.

Tidak perlu SQL, impor schema, perubahan konfigurasi, atau mengunggah storage.
Untuk instalasi sebelum 1.12.0, ikuti deployment 1.12.0 terlebih dahulu lalu
patch ini. Jangan mengambil file patch dari tag lama v1.12.0.

## Verifikasi

- `php scripts/test-public-theme-text.php`: 34 pemeriksaan teks mentah/encoded,
  entitas, paragraf, whitespace, escaping, payload aktif, dan format artikel.
- `php scripts/render-public-theme-fixtures.php`: 84 halaman empat tema; moto
  HTML/encoded, ringkasan berita/fasilitas encoded, serta pemeriksaan text node
  untuk mendeteksi tag HTML yang tercetak sebagai teks (di luar script/style/textarea).
- Baseline keamanan, kalender, migration UI, statistik, lint PHP, tes database,
  HTTP keamanan/akademik/tema, navigasi, serta build CSS dijalankan terpisah.
- Koneksi Browser bawaan tidak tersedia pada sesi patch; pemeriksaan DOM dan
  HTTP bukan klaim pemeriksaan visual browser interaktif atau produksi.

Setelah unggah, periksa moto di beranda/header/footer dan halaman lain,
ringkasan kartu, serta format visi/misi dan artikel lengkap. Teks yang memang
berupa contoh kode di isi artikel tidak diubah menjadi ringkasan.
