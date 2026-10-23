# Tema publik tambahan

Tema lama tetap memakai view dan aset sebelumnya. Nawala Indigo, Arunika Emerald,
Wibawa Crimson, dan Samudra Cendekia memakai navigasi, footer, serta komponen
`next-*`. Pemilihan view khusus dilakukan di `Controller::view` hanya untuk empat
ID tema baru.

## Komposisi

| Tema | Navigasi dan isi |
| --- | --- |
| Nawala Indigo | Masthead sebaris, grid berita, direktori dan panel isi |
| Arunika Emerald | Masthead terpusat, navigasi baris kedua, komposisi majalah |
| Wibawa Crimson | Masthead institusional, navigasi gelap, kartu horizontal |
| Samudra Cendekia | Hero asimetris, statistik mengambang, berita unggulan lebar |

View khusus mencakup profil, arsip/detail berita, galeri/album, GTK, kontak,
prestasi/detail, agenda/detail, kalender, program ekstrakurikuler, sambutan,
testimoni, dan direktori/detail alumni. Formulir transaksi SPMB tetap memakai
kontrak input dan proteksi modul SPMB, dengan komponen presentasi tema baru.
Widget kalender memakai logika kalender yang sama agar pemilihan tanggal,
arsip, dan unduhan PDF tetap konsisten.

## Admin

Identitas, moto hero, gambar, isi artikel, personel, jadwal, album, program, dan
menu menggunakan data CMS. Label presentasi dapat diedit di Pengaturan melalui
bagian label halaman tema baru; font isi/judul menggunakan whitelist font lokal.
Teks default pada katalog label adalah nilai awal, bukan data sekolah contoh.

## Verifikasi yang dapat diulang

1. `php scripts/render-public-theme-fixtures.php`
2. `node scripts/audit-public-themes.mjs` (memerlukan Microsoft Edge lokal)
3. `php scripts/test-public-theme-http.php` (host development `.test`)
4. Baseline lint/build, security, akademik, migrasi, statistik, dan HTTP sesuai AGENTS.md.

Generator menghasilkan 84 halaman sintetis untuk 21 jenis halaman pada empat
tema. Audit Edge memeriksa desktop 1440px dan mobile 390px, keadaan normal,
menu/formulir terbuka, hover, dan fokus. Rasio teks memakai WCAG AA 4,5:1 atau
3:1 untuk teks besar. Warna OKLCH dikonversi ke sRGB oleh Canvas browser.
Laporan juga memisahkan latar yang tidak dapat dihitung serta overflow.

HTML, screenshot, dan laporan audit berada di `storage/cache/theme-preview/`
dan tidak boleh masuk commit. Data fixture bukan data pribadi pengguna.
Hasil fixture tidak menjamin kontras gambar eksternal, dokumen PDF, maupun
konten pihak ketiga. Konten rich text dapat mengandung gambar/video yang harus
tetap diperiksa saat dipublikasikan.

Smoke test HTTP mengganti pengaturan tema hanya selama pengujian lokal dan
mengembalikan nilai awal di blok `finally`. Jangan jalankan tes ini ketika
database lokal diarahkan ke produksi.
