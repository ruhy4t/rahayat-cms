-- Rahayat CMS: editable presentation settings for the four additive public themes.
-- Idempotent and safe for existing installations.
INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES
('theme_font_body', '', 'select', 'Public website body font'),
('theme_font_heading', '', 'select', 'Public website heading font'),
('home_hero_eyebrow', 'Info SPMB Tersedia', 'text', 'Homepage hero eyebrow'),
('home_hero_title_prefix', 'Selamat Datang di', 'text', 'Homepage hero title prefix'),
('home_primary_button_label', 'Tentang Kami', 'text', 'Homepage primary button label'),
('home_contact_button_label', 'Hubungi Kami', 'text', 'Homepage contact button label'),
('home_students_label', 'Murid Aktif', 'text', 'Homepage student statistic label'),
('home_teachers_label', 'Guru Profesional', 'text', 'Homepage teacher statistic label'),
('home_accreditation_label', 'Akreditasi', 'text', 'Homepage accreditation statistic label'),
('home_graduation_label', 'Kelulusan', 'text', 'Homepage graduation statistic label'),
('home_news_eyebrow', 'Informasi Terkini', 'text', 'Homepage news eyebrow'),
('home_news_title', 'Berita & Pengumuman', 'text', 'Homepage news title'),
('home_agenda_title', 'Agenda', 'text', 'Homepage agenda title'),
('home_gallery_eyebrow', 'Dokumentasi', 'text', 'Homepage gallery eyebrow'),
('home_gallery_title', 'Galeri Kegiatan', 'text', 'Homepage gallery title'),
('home_facilities_eyebrow', 'Infrastruktur', 'text', 'Homepage facilities eyebrow'),
('home_facilities_title', 'Fasilitas Sekolah', 'text', 'Homepage facilities title'),
('home_facilities_description', 'Fasilitas pendukung kegiatan belajar dan pengembangan murid.', 'text', 'Homepage facilities description'),
('home_extracurricular_eyebrow', 'Pengembangan Diri', 'text', 'Homepage extracurricular eyebrow'),
('home_extracurricular_title', 'Ekstrakurikuler', 'text', 'Homepage extracurricular title'),
('home_testimonials_eyebrow', 'Cerita Mereka', 'text', 'Homepage testimonials eyebrow'),
('home_testimonials_title', 'Testimoni', 'text', 'Homepage testimonials title'),
('home_testimonials_description', 'Pengalaman keluarga besar dan mitra sekolah bersama kami.', 'text', 'Homepage testimonials description'),
('home_cta_title', 'Bergabunglah Bersama Kami', 'text', 'Homepage call-to-action title'),
('home_cta_description', 'Hubungi sekolah untuk memperoleh informasi selengkapnya.', 'text', 'Homepage call-to-action description')
ON DUPLICATE KEY UPDATE setting_key = VALUES(setting_key);
