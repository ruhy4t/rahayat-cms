<?php
declare(strict_types=1);

final class PublicTheme
{
    public const NAMES = ['nawala-indigo', 'arunika-emerald', 'wibawa-crimson', 'samudra-cendekia'];

    public static function isModern(string $name): bool
    {
        return in_array($name, self::NAMES, true);
    }

    /** Plain display text for rich-text fields used in banners/cards.
     * Decode legacy entities BEFORE removing markup; callers must still e().
     * Do not use for URLs, identifiers or full rich-text article bodies.
     */
    public static function summary(?string $value): string
    {
        $text = Security::plainText($value);
        // Block boundaries and line breaks must not join adjacent words.
        $text = preg_replace('/<\/?(?:p|div|h[1-6]|li|ul|ol|blockquote|tr|td|th|br|hr)\b[^>]*>/i', ' ', $text) ?? '';
        $text = strip_tags($text);
        return trim(preg_replace('/[\s\x{00a0}]+/u', ' ', $text) ?? '');
    }

    public static function labels(): array
    {
        return [
            'all' => 'Lihat semua', 'empty' => 'Informasi belum tersedia.',
            'profile' => 'Profil Sekolah', 'vision' => 'Visi', 'mission' => 'Misi',
            'history' => 'Sejarah', 'welcome_message' => 'Sambutan',
            'gallery' => 'Galeri Kegiatan', 'gtk' => 'Guru & Tenaga Kependidikan',
            'contact' => 'Hubungi Kami', 'address' => 'Alamat', 'phone' => 'Telepon',
            'email' => 'Email', 'hours' => 'Jam Operasional', 'closed' => 'Tutup',
            'agenda' => 'Agenda Kegiatan', 'calendar' => 'Kalender Pendidikan',
            'upcoming' => 'Mendatang', 'archive' => 'Arsip', 'news' => 'Berita',
            'category' => 'Kategori', 'filter' => 'Tampilkan', 'all_categories' => 'Semua kategori',
            'npsn' => 'NPSN', 'accreditation' => 'Akreditasi', 'established_year' => 'Tahun berdiri',
            'prestasi' => 'Prestasi & Penghargaan', 'back' => 'Kembali',
            'explore'=>'Jelajahi sekolah', 'connect'=>'Terhubung dengan kami',
            'close'=>'Tutup', 'previous'=>'Sebelumnya', 'next'=>'Berikutnya',
            'testimonials'=>'Testimoni', 'alumni'=>'Direktori Alumni', 'submit'=>'Kirim untuk ditinjau',
            'name'=>'Nama lengkap', 'relationship'=>'Hubungan dengan sekolah', 'graduation_year'=>'Tahun lulus',
            'occupation'=>'Pekerjaan/instansi', 'testimonial'=>'Testimoni', 'photo'=>'Foto',
            'contact_private'=>'Kontak untuk verifikasi (tidak dipublikasikan)',
            'consent_testimonial'=>'Saya menyetujui publikasi nama, kategori, foto, dan testimoni setelah ditinjau sekolah.',
            'search'=>'Cari nama atau instansi', 'city'=>'Kota', 'final_class'=>'Kelas terakhir',
            'further_education'=>'Pendidikan lanjutan', 'further_education_status'=>'Status sekolah tujuan',
            'further_education_detail'=>'Nama sekolah tujuan', 'employment_status'=>'Status pekerjaan',
            'institution'=>'Instansi', 'whatsapp'=>'WhatsApp untuk verifikasi', 'story'=>'Cerita setelah lulus',
            'achievement'=>'Prestasi dan pencapaian', 'publish_photo'=>'Publikasikan foto',
            'publish_occupation'=>'Publikasikan pekerjaan dan instansi', 'publish_city'=>'Publikasikan kota',
            'consent_alumni'=>'Saya menyetujui publikasi nama, tahun lulus, serta informasi yang saya pilih setelah diverifikasi sekolah.',
        ];
    }

    public static function text(array $settings, string $key): string
    {
        return (string) ($settings['public_label_' . $key] ?? self::labels()[$key] ?? $key);
    }
}
