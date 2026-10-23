# Audit keamanan dan performa tema — 1.12.0

## Cakupan

Empat tema tambahan, renderer/layout, pengaturan admin, helper escaping/URL/rich
text, pengiriman storage, Apache, serta kompatibilitas controller lama.
Pengujian hanya pada development lokal dengan data sintetis; tidak melakukan
pentest produksi, mengubah kredensial atau menjalankan migrasi data produksi.

## Temuan dan perbaikan

1. **Guard rich text berbasis regex dapat dilewati.** Contoh
   `<a href="java&#x73;cript:alert(1)">` tidak mengandung pola literal javascript,
   tetapi browser mendekode entitas. Semua markup kini melewati parser DOM dan
   whitelist atribut/URL. Plain text tetap tanpa parsing. Cache request-local
   dibatasi 16 entri, masing-masing input maksimal 64 KiB; konten lebih besar
   tetap disanitasi, hanya tidak di-cache. Tidak ada cache konten di disk.
2. **Validasi URL dan output berpotensi berbeda akibat encoding berlapis.**
   Tolak nilai yang belum stabil setelah batas decoding, dan kembalikan URL
   kanonik dari safeLinkUrl. Regresi mencakup dua belas lapisan encoding.
3. **Pembatasan canonical storage belum mencakup semua folder internal.**
   Daftar cache/rate_limits/backups/logs/academic_documents diperiksa sebelum
   serta setelah realpath; klasifikasi SPMB harus sama dan tetap berotorisasi.
   Risiko alias/symlink memerlukan alias filesystem yang sudah ada; bukan
   klaim bahwa pengunjung anonim dapat membuat symlink. Tes unit memeriksa
   daftar canonical, tes HTTP memeriksa penolakan direktori nyata. Pembuatan
   alias symlink Windows sendiri tidak termasuk pengujian HTTP otomatis ini.
4. **Metadata root belum ditolak menyeluruh.** Apache kini menolak komponen
   path tersembunyi selain `.well-known`, serta AGENTS.md dan manifest deploy.
   Tidak menyatakan ada credential yang ditemukan/terbuka pada instalasi ini.
5. **Pekerjaan transfer/render tidak perlu.** Hapus link CSS komposisi ganda;
   tambah validator ETag/Last-Modified untuk media publik dan penanganan HEAD.
   Revalidasi yang cocok mengirim 304 kosong. SPMB tidak mendapat validator,
   selalu no-store dan diperiksa hak akses sebelum pembacaan/dekripsi.

## Perlindungan yang dipertahankan

Prepared statements, autentikasi dan izin default-deny, CSRF/CAPTCHA formulir,
moderasi kiriman publik, escaping teks, validasi font/tema whitelist, validasi
upload MIME/ukuran/dimensi, dokumen privat terenkripsi, session httponly/samesite,
CSP dan aset lokal. Tidak menambah CDN, dependency, atau tracking pihak ketiga.
Gambar non-kritis lazy; gambar utama prioritas tinggi. Settings sudah memiliki
cache request-local sehingga tidak di-query ulang per label.

## Verifikasi

- Lint 182 file PHP terlacak.
- Security patch: 94; security database: 45; academic: 68;
  migration: 13; statistik pengunjung: 7 pemeriksaan lulus.
- HTTP security: 56; academic HTTP: 33; tema HTTP: 44 halaman lulus.
- JavaScript kalender, migration UI, dan navigasi tema lulus; build CSS lulus.
- 84 fixture halaman × desktop/mobile dan keadaan menu/fokus/hover/galeri:
  680 kondisi, 42.644 pemeriksaan teks, 0 gagal, 0 uncertain. Audit visual ini
  bukan audit otomatis seluruh WCAG atau seluruh kemungkinan konten admin.
- Tes HTTP tema mencetak median/maksimum TTFB lokal per tema, bukan estimasi
  Lighthouse/Core Web Vitals produksi. Kecepatan hosting harus diukur setelah
  deploy dengan konten dan konfigurasi produksi yang sebenarnya.

## Batas yang harus diketahui

- Tidak menjamin semua celah tertutup. PHP/MySQL/web server/OS, credential,
  backup, dan konfigurasi CDN tetap perlu dikelola serta diperbarui pemilik.
- CSP kompatibilitas masih memakai unsafe-inline dan unsafe-eval. Migrasi ke
  nonce CSP harus memindahkan inline handler/skrip seluruh tema dan admin serta
  memverifikasi editor; bukan perubahan aman satu baris. Tidak dinonaktifkan
  atau diperlonggar dalam rilis ini. HTML sanitizer bukan pengganti pemeliharaan
  library/parser dan pengujian XSS berkelanjutan.
- Header proxy harus ditimpa proxy terpercaya. HTTPS, APP_DEBUG=false, OPcache,
  kompresi/cache hosting dan aturan Nginx perlu diverifikasi pada server tujuan.
- Cache ETag berbasis metadata path/mtime/ukuran, bukan pembacaan/hash isi setiap
  request. Media yang diganti harus memiliki nama/timestamp baru. Jangan cache
  HTML bertoken atau dokumen privat lewat CDN.

Referensi keamanan: [OWASP XSS Prevention](https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet/)
menempatkan encoding kontekstual dan sanitasi HTML sebagai kontrol utama;
CSP adalah lapisan tambahan, bukan pengganti keduanya.

## Audit lanjutan setelah tag v1.12.0

- `FILTER_VALIDATE_URL` menerima `javascript://example.invalid/%0Aalert(1)`.
  Input video API kini memakai whitelist HTTP/HTTPS; renderer admin, galeri
  lama, dan galeri baru juga menyaring URL historis sebelum menjadi href/src.
  Media root-relative lama tetap didukung secara eksplisit pada renderer,
  tetapi URL protocol-relative dan skema aktif ditolak. Ini menutup jalur URL
  berbahaya; tidak diklaim sebagai pembuktian eksekusi XSS pada semua browser.
- 23 penanganan exception API tidak lagi mengirim detail SQL/error internal.
  Log hanya berisi metode dan kelas exception, bukan query, payload, atau PII.
- Statistik footer menyimpan hanya angka today/week/month/total pada satu
  cache privat server, maksimal 30 detik. Ini bukan cache HTML, dokumen,
  autentikasi, atau token CSRF. Pencatatan kunjungan tidak dihentikan; angka
  publik dapat tertunda sampai 30 detik. Laporan dengan tanggal eksplisit
  selalu live. Cache rusak, tanggal berbeda, TTL tidak valid, dan cache yang
  tidak dapat ditulis memakai perhitungan database sebagai fallback.
- Regresi: security-patch 108, HTTP security 68, statistik SQLite 19.
  Tes cache menonaktifkan tabel sementara di database SQLite in-memory untuk
  membuktikan cache hit tidak menyentuh database, lalu menguji kedaluwarsa,
  kerusakan, validasi nilai, dan kegagalan direktori cache.
- `php scripts/test-public-theme-http.php --benchmark` mengukur 55 request
  per tema (220 total). Sampel lokal: median 35,8–42,2 ms dan p95 63,0–78,8 ms.
  Ini TTFB HTML lokal, bukan waktu muat aset, load test, atau angka produksi.
  Cache hit menghindari dua query agregat; tidak diklaim sebagai persentase
  peningkatan end-to-end karena sampel sebelum/sesudah tidak identik.
- Apache lokal belum memuat mod_deflate/mod_brotli. Konfigurasi hosting untuk
  kompresi, OPcache, HTTPS dan proteksi proxy/CDN perlu diperiksa pemilik.
  Tidak mengubah konfigurasi layanan di luar repository atau server produksi.
- Tidak ada perubahan struktur database atau SQL yang perlu diimpor. Patch
  tersedia pada main setelah tag awal v1.12.0, bukan arsip tag lama tersebut.
