-- ============================================
-- SchoolWeb CMS Database Schema
-- MariaDB/MySQL
-- Updated: 2026-01-15
-- ============================================

-- Create database
CREATE DATABASE IF NOT EXISTS schoolweb_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE schoolweb_db;

-- ============================================
-- Users Table (Updated with new roles)
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'gtk', 'murid', 'ekskul') DEFAULT 'murid',
    editor_id INT UNSIGNED NULL COMMENT 'GTK assigned as editor for murid contributors',
    is_spmb_committee TINYINT(1) DEFAULT 0 COMMENT 'GTK designated as SPMB committee by admin',
    permissions JSON NULL COMMENT 'GTK configurable permissions (JSON array)',
    avatar VARCHAR(255) NULL,
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_is_active (is_active),
    INDEX idx_is_spmb_committee (is_spmb_committee),
    
    FOREIGN KEY (editor_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- News Categories Table
-- ============================================
CREATE TABLE IF NOT EXISTS news_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    color VARCHAR(20) DEFAULT '#4F46E5',
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_slug (slug),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- News Table (Updated with category_id)
-- ============================================
CREATE TABLE IF NOT EXISTS news (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT NULL,
    content LONGTEXT NOT NULL,
    image VARCHAR(255) NULL,
    category VARCHAR(50) DEFAULT 'umum',
    category_id INT UNSIGNED NULL,
    author_id INT UNSIGNED NOT NULL,
    status ENUM('draft', 'pending', 'published', 'archived') DEFAULT 'draft',
    views INT UNSIGNED DEFAULT 0,
    published_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_slug (slug),
    INDEX idx_category (category),
    INDEX idx_category_id (category_id),
    INDEX idx_status (status),
    INDEX idx_published_at (published_at),
    INDEX idx_author_id (author_id),
    
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES news_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Site Visits Table
-- ============================================
CREATE TABLE IF NOT EXISTS site_visits (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    visitor_key CHAR(64) NOT NULL,
    path VARCHAR(255) NOT NULL,
    title VARCHAR(255) NULL,
    content_type VARCHAR(50) NOT NULL DEFAULT 'page',
    content_id INT UNSIGNED NULL,
    visited_on DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_visited_on (visited_on),
    INDEX idx_visitor_day (visitor_key, visited_on),
    INDEX idx_content (content_type, content_id),
    INDEX idx_path (path)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- School Profile Table (Updated)
-- ============================================
CREATE TABLE IF NOT EXISTS school_profile (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    tagline VARCHAR(255) NULL,
    npsn VARCHAR(20) NULL,
    address TEXT NULL,
    phone VARCHAR(50) NULL,
    email VARCHAR(100) NULL,
    website VARCHAR(255) NULL,
    principal_name VARCHAR(100) NULL,
    principal_nip VARCHAR(50) NULL,
    principal_photo VARCHAR(255) NULL,
    logo VARCHAR(255) NULL,
    vision TEXT NULL,
    mission TEXT NULL,
    motto VARCHAR(255) NULL,
    history TEXT NULL,
    welcome_message LONGTEXT NULL,
    principal_quote TEXT NULL,
    organizational_structure TEXT NULL COMMENT 'JSON or description of org structure',
    accreditation VARCHAR(10) NULL,
    school_type ENUM('negeri', 'swasta') DEFAULT 'negeri',
    spmb_link VARCHAR(255) NULL,
    established_year YEAR NULL,
    google_maps_embed TEXT NULL,
    
    -- Operating Hours (per day with open/closed flags)
    monday_open VARCHAR(5) DEFAULT '07:00',
    monday_close VARCHAR(5) DEFAULT '15:00',
    is_closed_monday TINYINT(1) DEFAULT 0,
    tuesday_open VARCHAR(5) DEFAULT '07:00',
    tuesday_close VARCHAR(5) DEFAULT '15:00',
    is_closed_tuesday TINYINT(1) DEFAULT 0,
    wednesday_open VARCHAR(5) DEFAULT '07:00',
    wednesday_close VARCHAR(5) DEFAULT '15:00',
    is_closed_wednesday TINYINT(1) DEFAULT 0,
    thursday_open VARCHAR(5) DEFAULT '07:00',
    thursday_close VARCHAR(5) DEFAULT '15:00',
    is_closed_thursday TINYINT(1) DEFAULT 0,
    friday_open VARCHAR(5) DEFAULT '07:00',
    friday_close VARCHAR(5) DEFAULT '15:00',
    is_closed_friday TINYINT(1) DEFAULT 0,
    saturday_open VARCHAR(5) DEFAULT '07:00',
    saturday_close VARCHAR(5) DEFAULT '12:00',
    is_closed_saturday TINYINT(1) DEFAULT 0,
    sunday_open VARCHAR(5) DEFAULT '07:00',
    sunday_close VARCHAR(5) DEFAULT '15:00',
    is_closed_sunday TINYINT(1) DEFAULT 1,
    
    -- Statistics
    total_students INT DEFAULT 0,
    total_teachers INT DEFAULT 0,
    graduation_rate INT DEFAULT 0,
    
    -- Watermark
    watermark_enabled TINYINT(1) DEFAULT 1 COMMENT 'Enable watermark on uploaded images',
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Facilities Table
-- ============================================
CREATE TABLE IF NOT EXISTS facilities (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    image VARCHAR(255) NULL,
    type ENUM('perpustakaan', 'laboratorium', 'olahraga', 'seni', 'ibadah', 'kantin', 'lainnya') DEFAULT 'lainnya',
    capacity INT UNSIGNED NULL,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_type (type),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Staff Table (Guru & Tenaga Kependidikan)
-- ============================================
CREATE TABLE IF NOT EXISTS staff (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    nip VARCHAR(50) NULL,
    position VARCHAR(100) NULL,
    subject VARCHAR(100) NULL COMMENT 'Mata pelajaran untuk guru',
    photo VARCHAR(255) NULL,
    email VARCHAR(100) NULL,
    phone VARCHAR(20) NULL,
    is_teacher TINYINT(1) DEFAULT 1,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_is_teacher (is_teacher),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Gallery Albums Table
-- ============================================
CREATE TABLE IF NOT EXISTS gallery_albums (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    cover_image VARCHAR(255) NULL,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_slug (slug),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Gallery Items Table (Photos & Videos)
-- ============================================
CREATE TABLE IF NOT EXISTS gallery_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    album_id INT UNSIGNED NULL,
    title VARCHAR(255) NULL,
    description TEXT NULL,
    type ENUM('image', 'video') DEFAULT 'image',
    file_path VARCHAR(255) NULL,
    youtube_url VARCHAR(255) NULL,
    youtube_video_id VARCHAR(50) NULL COMMENT 'Extracted YouTube video ID',
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_album_id (album_id),
    INDEX idx_type (type),
    INDEX idx_is_active (is_active),
    
    FOREIGN KEY (album_id) REFERENCES gallery_albums(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Hero Slides Table
-- ============================================
CREATE TABLE IF NOT EXISTS hero_slides (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NULL,
    subtitle TEXT NULL,
    image VARCHAR(255) NOT NULL,
    button_text VARCHAR(100) NULL,
    button_url VARCHAR(255) NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_is_active (is_active),
    INDEX idx_sort_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Menu Management Table
-- ============================================
CREATE TABLE IF NOT EXISTS menus (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    icon VARCHAR(50) NULL,
    parent_id INT UNSIGNED NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    target ENUM('_self', '_blank') DEFAULT '_self',
    menu_location ENUM('header', 'footer', 'both') DEFAULT 'header',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_parent_id (parent_id),
    INDEX idx_is_active (is_active),
    INDEX idx_menu_location (menu_location),
    
    FOREIGN KEY (parent_id) REFERENCES menus(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Site Settings Table (Theme, etc)
-- ============================================
CREATE TABLE IF NOT EXISTS site_settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    setting_type ENUM('text', 'number', 'boolean', 'json', 'select') DEFAULT 'text',
    description VARCHAR(255) NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Contact Messages Table
-- ============================================
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    replied TINYINT(1) DEFAULT 0,
    reply_message TEXT NULL,
    replied_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SPMB Registrations Table
-- ============================================
CREATE TABLE IF NOT EXISTS spmb_registrations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    registration_number VARCHAR(50) UNIQUE,
    
    -- Student Data
    student_name VARCHAR(100) NOT NULL,
    nisn VARCHAR(10) NULL COMMENT 'Nomor Induk Siswa Nasional',
    nik VARCHAR(16) NULL COMMENT 'Nomor Induk Kependudukan',
    birth_date DATE NOT NULL,
    birth_place VARCHAR(100) NULL,
    gender ENUM('L', 'P') NOT NULL,
    religion VARCHAR(50) NULL,
    address TEXT NULL,
    
    -- Parent/Guardian Data
    father_name VARCHAR(100) NULL,
    father_occupation VARCHAR(100) NULL,
    father_phone VARCHAR(20) NULL,
    mother_name VARCHAR(100) NULL,
    mother_occupation VARCHAR(100) NULL,
    mother_phone VARCHAR(20) NULL,
    
    -- Contact
    email VARCHAR(100) NULL,
    phone VARCHAR(20) NULL,
    
    -- Previous Education
    previous_school VARCHAR(255) NULL,
    previous_school_address TEXT NULL,
    graduation_year YEAR NULL,
    
    -- Documents (JSON array of uploaded files)
    documents JSON NULL,
    
    -- Status
    status ENUM('pending', 'review', 'accepted', 'rejected') DEFAULT 'pending',
    notes TEXT NULL,
    reviewed_by INT UNSIGNED NULL,
    reviewed_at DATETIME NULL,
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_registration_number (registration_number),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Sample Data
-- ============================================

-- Insert initial users. Change these passwords immediately after first login.
INSERT INTO users (username, email, password, name, role, is_active) VALUES
('admin', 'admin@rahayat.sch.id', '$argon2id$v=19$m=65536,t=4,p=1$SUFJYUEyWXM0alQzdVNDeA$4yt9Ba8+YRViFpSOOQ+r0G79jQrwIdGyyEiiCKnd3Mg', 'Administrator', 'admin', 1),
('gtk1', 'gtk1@rahayat.sch.id', '$argon2id$v=19$m=65536,t=4,p=1$SUFJYUEyWXM0alQzdVNDeA$4yt9Ba8+YRViFpSOOQ+r0G79jQrwIdGyyEiiCKnd3Mg', 'Guru Contoh', 'gtk', 1);

-- Insert default news categories
INSERT INTO news_categories (name, slug, color) VALUES
('Pengumuman', 'pengumuman', '#EF4444'),
('Prestasi', 'prestasi', '#10B981'),
('Kegiatan', 'kegiatan', '#3B82F6'),
('Akademik', 'akademik', '#8B5CF6'),
('Ekstrakurikuler', 'ekstrakurikuler', '#F59E0B');

-- Insert school profile
INSERT INTO school_profile (name, npsn, address, phone, email, website, principal_name, vision, mission, motto, accreditation, school_type, established_year) VALUES
('Sekolah Rahayat', '12345678', 'Jl. Pendidikan No. 1, Kota Bandung', '(022) 123-4567', 'info@rahayat.sch.id', 'https://rahayat.sch.id', 'Dr. H. Ahmad Suryadi, M.Pd', 
'Menjadi sekolah unggul yang menghasilkan generasi berkarakter, cerdas, dan berdaya saing global.',
'1. Menyelenggarakan pendidikan berkualitas berbasis teknologi\n2. Mengembangkan potensi peserta didik secara optimal\n3. Membangun karakter religius dan nasionalis\n4. Menjalin kerjasama dengan berbagai pihak',
'Cerdas, Berkarakter, Berprestasi',
'A', 'negeri', 2010);

-- Insert default menus
INSERT INTO menus (title, url, sort_order, menu_location) VALUES
('Beranda', '/', 1, 'header'),
('Profil', '/profil', 2, 'header'),
('Berita', '/berita', 3, 'header'),
('Galeri', '/galeri', 4, 'header'),
('Kontak', '/kontak', 5, 'header');

-- Insert default site settings
INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES
('theme', 'indigo-modern', 'select', 'Website theme: indigo-modern, green-nature, blue-ocean'),
('site_title', 'Sekolah Rahayat', 'text', 'Website title'),
('meta_description', 'Website resmi Sekolah Rahayat', 'text', 'SEO meta description'),
('footer_text', '© 2026 Sekolah Rahayat. All rights reserved.', 'text', 'Footer copyright text'),
('spmb_enabled', '0', 'boolean', 'Enable SPMB registration'),
('spmb_start_date', NULL, 'text', 'SPMB registration start date'),
('spmb_end_date', NULL, 'text', 'SPMB registration end date');

-- Insert sample facilities
INSERT INTO facilities (name, description, type, is_active, sort_order) VALUES
('Perpustakaan', 'Perpustakaan dengan koleksi 10.000+ buku', 'perpustakaan', 1, 1),
('Laboratorium Komputer', 'Lab dengan 40 unit komputer terbaru', 'laboratorium', 1, 2),
('Laboratorium IPA', 'Lab IPA lengkap untuk praktikum', 'laboratorium', 1, 3),
('Lapangan Olahraga', 'Lapangan basket, voli, dan futsal', 'olahraga', 1, 4),
('Musholla', 'Musholla dengan kapasitas 200 jamaah', 'ibadah', 1, 5);

-- Insert sample news
INSERT INTO news (title, slug, excerpt, content, category, category_id, author_id, status, published_at) VALUES
('Selamat Datang di Website Resmi Sekolah Rahayat', 'selamat-datang-di-website-resmi-sekolah-rahayat', 
'Website resmi Sekolah Rahayat kini hadir dengan tampilan baru yang lebih modern dan informatif.',
'<p>Dengan bangga kami mempersembahkan website resmi Sekolah Rahayat dengan tampilan yang lebih modern, responsif, dan informatif.</p><p>Website ini dirancang untuk memudahkan akses informasi bagi siswa, orang tua, dan masyarakat umum tentang kegiatan dan perkembangan sekolah kami.</p><p>Fitur-fitur yang tersedia meliputi:</p><ul><li>Informasi profil sekolah</li><li>Berita dan agenda kegiatan</li><li>Galeri foto dan video</li><li>Direktori guru dan siswa</li></ul>',
'pengumuman', 1, 1, 'published', NOW()),

('Sistem Penerimaan Murid Baru Tahun Ajaran 2026/2027', 'spmb-tahun-ajaran-2026-2027',
'Pendaftaran SPMB untuk tahun ajaran 2026/2027 telah dibuka. Segera daftarkan putra-putri Anda.',
'<p>Sekolah Rahayat membuka pendaftaran Sistem Penerimaan Murid Baru (SPMB) untuk tahun ajaran 2026/2027.</p><h3>Jadwal Pendaftaran:</h3><ul><li>Gelombang 1: 1 Januari - 28 Februari 2026</li><li>Gelombang 2: 1 Maret - 30 April 2026</li></ul><h3>Persyaratan:</h3><ul><li>Fotokopi Akta Kelahiran</li><li>Fotokopi Kartu Keluarga</li><li>Pas foto 3x4 (4 lembar)</li><li>Rapor terakhir</li></ul>',
'pengumuman', 1, 1, 'published', NOW()),

('Prestasi Membanggakan Siswa dalam Olimpiade Sains', 'prestasi-olimpiade-sains-2026',
'Tim Olimpiade Sains Sekolah Rahayat berhasil meraih medali emas di tingkat provinsi.',
'<p>Dengan penuh kebanggaan, kami mengumumkan bahwa Tim Olimpiade Sains Sekolah Rahayat berhasil meraih prestasi gemilang di ajang Olimpiade Sains Nasional tingkat Provinsi.</p><p>Berikut daftar peraih medali:</p><ul><li><strong>Medali Emas</strong> - Ahmad Fauzan (Matematika)</li><li><strong>Medali Perak</strong> - Siti Nurhaliza (Fisika)</li><li><strong>Medali Perunggu</strong> - Budi Santoso (Biologi)</li></ul><p>Selamat kepada para siswa berprestasi dan guru pembimbing!</p>',
'prestasi', 2, 1, 'published', NOW());

-- Insert sample gallery album
INSERT INTO gallery_albums (title, slug, description, is_active, sort_order) VALUES
('Kegiatan Sekolah 2026', 'kegiatan-sekolah-2026', 'Dokumentasi kegiatan sekolah tahun 2026', 1, 1),
('Upacara dan Peringatan', 'upacara-peringatan', 'Upacara bendera dan peringatan hari besar', 1, 2);

-- ============================================
-- Ekstrakurikuler Table
-- ============================================
CREATE TABLE IF NOT EXISTS ekstrakurikuler (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    image VARCHAR(255) NULL,
    schedule VARCHAR(100) NULL,
    supervisor VARCHAR(100) NULL,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_ekskul_active (is_active),
    INDEX idx_ekskul_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Prestasi (Achievements) Table
-- ============================================
CREATE TABLE IF NOT EXISTS prestasi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    category ENUM('Sekolah', 'Guru', 'Murid') NOT NULL DEFAULT 'Sekolah',
    image VARCHAR(255) NULL,
    date DATE NULL,
    created_by INT UNSIGNED NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_prestasi_category (category),
    INDEX idx_prestasi_date (date),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
