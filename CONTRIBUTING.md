# Contributing to Rahayat CMS

Terima kasih ingin berkontribusi.

## Cara Berkontribusi

1. Fork repository.
2. Buat branch dari `main`.
3. Jalankan pengecekan syntax PHP dan tes baseline:

   ```bash
   php -l path/to/file.php
   php scripts/test-security-patch.php
   node scripts/test-academic-calendar.mjs
   node scripts/test-migration-ui.mjs
   ```

4. Buat pull request dengan ringkasan perubahan dan cara mengetesnya.

## Aturan Teknis

- Jangan commit file rahasia, credential, dump database produksi, atau dokumen SPMB.
- Jangan commit isi folder `storage`, kecuali file konfigurasi seperti `.htaccess` dan `.gitkeep`.
- Pertahankan kompatibilitas PHP 8.4 dan 8.5.
- Gunakan prepared statements untuk query database.
- Semua fitur admin yang mengubah data harus memakai CSRF.
- Deployment tetap dibatasi untuk domain `.sch.id` sesuai `LICENSE`.

## Lisensi Kontribusi

Dengan mengirim pull request, Anda setuju kontribusi Anda dirilis di bawah
`Rahayat CMS Source Available License 1.0`.
