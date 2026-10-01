# Patch Keamanan Rahayat CMS — 1 Oktober 2026

Menindaklanjuti audit keamanan (temuan C1–C3, H1–H3, dan pengerasan Medium/Low).
Target: menutup jalur eskalasi/penulisan tanpa izin tanpa mengorbankan kecepatan.

**Backup sebelum patch:** `security-patch-20261001-095645/` (di root proyek, di luar
`public/`, berisi 20 file `*.bak` + `MANIFEST.txt` sha256 + `RESTORE.md` + `.htaccess`
`Require all denied`). Lihat `RESTORE.md` untuk perintah pemulihan.

## Perbaikan yang diterapkan

| # | Temuan | Perbaikan | File |
|---|---|---|---|
| C1 | Fallback router me-lowercase action sehingga daftar otorisasi case-sensitive terlewat (eskalasi ke admin lewat `/api/userupdate/<id>`) | Router me-resolve **nama method yang dideklarasikan** (`ReflectionMethod::getName`, di-cache per kelas+aksi) lalu memakai nama itu untuk `before()`, pemanggilan, dan `after()`. Biaya terukur 0,26 µs/request. | `app/Core/Router.php` |
| H1 | Gate Dashboard allow-by-default untuk role `gtk` | Aksi yang tidak ada di `$permissionMap` sekarang **ditolak**, bukan diloloskan. | `app/Controllers/DashboardController.php` |
| C1b | `ApiController::before` menyisakan aksi tak-terpetakan bagi pengguna non-admin | Struktur diubah menjadi deny-by-default: publik (`news`, `newsShow`), admin-only, layanan-diri (`csrfToken`, `changePassword`, `categories`), lalu peta izin; sisanya 403. | `app/Controllers/ApiController.php` |
| C2 | `PrestasiController` menentukan autentikasi dari `REQUEST_URI` di constructor → `/prestasi/save` dan `/prestasi/delete/{id}` bisa ditulis tanpa login | Cek URI dihapus; otorisasi dipindah ke `before()` (`requireAuth` + permission `prestasi`; `delete` khusus admin). `description` kini disanitasi dengan `Security::sanitizeHtml()`. | `app/Controllers/PrestasiController.php` |
| C3 | Field rich text profil (visi/misi/motto/sejarah/sambutan/kutipan) disimpan mentah lalu dicetak mentah → stored XSS oleh `gtk` | Disanitasi saat simpan (`sanitizeHtml`) **dan** dijaga saat baca lewat `Security::sanitizeRichTextIfNeeded()` (fast-path: konten bersih tidak di-parse). | `app/Controllers/DashboardController.php`, `app/Models/SchoolProfile.php`, `app/Core/Security.php` |
| H2 | Nilai ter-escape di dalam string JS pada `onclick` (entity di-decode parser HTML) | `json_encode(..., JSON_HEX_APOS\|QUOT\|AMP\|TAG)` menggantikan `e()` di dalam argumen JS. | `views/backend/categories/index.php`, `views/backend/menus/index.php` |
| H3 | `newsUpdate` tanpa cek kepemilikan (murid/ekskul bisa menulis ulang/men-draft berita penulis lain) | Cek `author_id` untuk role `murid`/`ekskul` di API, plus helper `denyForeignNewsForRestrictedRole()` untuk `newsEdit`, `newsUpdate`, `newsDelete` di dashboard. | `app/Controllers/ApiController.php`, `app/Controllers/DashboardController.php` |
| M1 | `spmb_link` & `google_maps_embed` bisa berisi `javascript:` | Validasi skema lewat `Security::safeLinkUrl()` / `Security::safeEmbedSrc()` (hanya http/https/relatif; iframe hanya host Google Maps). Link manifest pembaruan juga divalidasi. | `app/Controllers/Controller.php`, `views/frontend/contact.php`, `views/backend/system/update.php` |
| L | Redirect pasca-login memakai `REQUEST_URI` mentah | `Security::safeLocalPath()` hanya mengizinkan path lokal. | `app/Controllers/AuthController.php` |
| L | Token CSRF pra-login tetap valid setelah login | Token dirotasi di `AuthSession::establish()`. | `app/Core/AuthSession.php` |
| L | Unggahan CKEditor hanya butuh login | Butuh permission `berita` + batas 100 unggahan/10 menit per IP. | `app/Controllers/UploadController.php` |
| L | Cek status SPMB bisa cocok dengan kredensial kosong pada record lama | NISN dan tanggal lahir tersimpan wajib tidak kosong sebelum `hash_equals`. | `app/Controllers/SPMBController.php` |
| L | Git argument injection lewat `UPDATE_BRANCH` | Regex mewajibkan karakter pertama alfanumerik dan menolak `..`. | `app/Core/AppUpdater.php` |
| L | Migration PHP bisa dieksekusi via HTTP bila `.htaccess` diabaikan | Guard `PHP_SAPI !== 'cli'` pada tiga migration PHP. | `database/migrations/*.php` |
| L | Aturan deny `.svg` storage tidak pernah tercapai (tertutup rule rewrite ber-`[L]`) | Rule deny dipindah **di atas** rewrite storage dan diperluas ke ekstensi aktif (`php`, `phtml`, `phar`, `cgi`, `pl`, `py`, `sh`, `asp`, `aspx`, `jsp`). | `.htaccess` |
| L | `scripts/`, metadata root, dan folder backup bisa tersaji pada deployment docroot = root proyek | `scripts` masuk daftar deny; `CHANGELOG/CONTRIBUTING/SECURITY/DEPLOYMENT-*.md`, `package*.json`, `version.json` di-deny; semua `*.bak` di-deny. | `.htaccess` |

## Verifikasi yang dijalankan

- `php -l` pada **141 file PHP** → bersih.
- `php scripts/test-security-patch.php` → **60 pemeriksaan lulus** (regresi baru untuk C1/C2/C3/H1–H3).
- `php scripts/test-security.php` (suite lama) → **45 pemeriksaan lulus**, fixture DB di-rollback.
- Uji HTTP nyata di `http://rahayat-cms.test`:
  - `POST /prestasi/delete/999999` dengan CSRF valid sebagai tamu → **302 ke `/login`** (sebelumnya menjalankan aksi).
  - `GET /prestasi/save` sebagai tamu → 302 ke `/login`; `GET /api/userupdate/1` → 401.
  - 16 halaman publik (beranda, berita, profil, galeri, kontak, prestasi, testimoni, alumni, agenda, kalender, login) → **200**; `/api/news` → 200.
- Backup: 20 salinan pra-patch + `MANIFEST.txt` sha256; `Controller.php.bak` direkonstruksi byte-exact (hanya hunk patch yang berbeda).

## Dampak performa (diukur di PHP 8.5.8)

| Jalur | Biaya |
|---|---|
| Guard rich-text saat baca (konten bersih, fast-path) | 0,45 µs per field (~0,003 ms untuk 6 field) |
| Resolusi nama aksi Router | 0,26 µs per request (~0,0003 ms) |
| Validasi URL | 0,46 µs per render |

Referensi TTFB beranda dari audit repo: ~63 ms. Tambahan patch < 0,01 ms, dan guard
hanya menjalankan DOM parser bila konten memang mengandung penanda aktif.

## Catatan yang sengaja tidak diubah

- **CSP `unsafe-inline`/`unsafe-eval`**: menggantinya butuh refaktor seluruh handler
  inline + pengujian editor; sanitasi sisi server sudah menutup jalur injeksi.
- **Default `gtk` tanpa `permissions` = semua izin**: dipertahankan agar akun GTK lama
  tidak terkunci; jalur eskalasi ke data SPMB (self-grant panitia) sudah ditutup dan
  pembuatan user baru selalu menyimpan daftar izin eksplisit.
- **`newsEdit` menulis normalisasi konten pada GET**: tidak berbahaya, fungsi lama.
- **Oracle "kontak alumni sudah terdaftar"**: masih ada (dibatasi CAPTCHA + 3/jam per IP);
  menghilangkannya berarti mengubah perilaku deduplikasi.
- Instalasi `/install` tanpa token: disarankan hapus/blokir setelah instalasi selesai.

## Urutan pemulihan

```powershell
# dari root proyek
Get-Content ".\security-patch-20261001-095645\MANIFEST.txt" | ForEach-Object {
  $p = $_ -split '\|'
  Copy-Item -LiteralPath ".\security-patch-20261001-095645\$($p[1]).bak" -Destination (Join-Path '.' $p[1]) -Force
}
```

Setelah patch dinyatakan stabil, hapus `security-patch-20261001-095645/` dan
`D:\laragon\www\rahayat-cms-acl-recovery\` (artefak perbaikan izin Windows).
