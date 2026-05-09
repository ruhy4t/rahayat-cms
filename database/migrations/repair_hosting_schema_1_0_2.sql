-- Rahayat CMS 1.0.2 hosting repair.
-- Jalankan lewat phpMyAdmin jika auto repair tidak berjalan karena pembatasan hosting.
-- File ini AMAN dijalankan berulang kali (idempotent).

-- ============================================
-- SCHOOL PROFILE
-- ============================================

CREATE TABLE IF NOT EXISTS school_profile (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    npsn VARCHAR(20) NULL,
    address TEXT NULL,
    phone VARCHAR(50) NULL,
    email VARCHAR(100) NULL,
    website VARCHAR(255) NULL,
    principal_name VARCHAR(100) NULL,
    logo VARCHAR(255) NULL,
    vision TEXT NULL,
    mission TEXT NULL,
    motto VARCHAR(255) NULL,
    history TEXT NULL,
    accreditation VARCHAR(10) NULL,
    school_type ENUM('negeri', 'swasta') DEFAULT 'negeri',
    established_year YEAR NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kolom tambahan (aman jika sudah ada)
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS tagline VARCHAR(255) NULL;
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS welcome_message LONGTEXT NULL;
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS principal_quote TEXT NULL;
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS spmb_link VARCHAR(255) NULL;
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS principal_nip VARCHAR(50) NULL;
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS principal_photo VARCHAR(255) NULL;
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS google_maps_embed TEXT NULL;
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS watermark_enabled TINYINT(1) DEFAULT 1;

-- Operating Hours (sering hilang di hosting!)
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS monday_open VARCHAR(5) DEFAULT '07:00';
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS monday_close VARCHAR(5) DEFAULT '15:00';
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS is_closed_monday TINYINT(1) DEFAULT 0;
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS tuesday_open VARCHAR(5) DEFAULT '07:00';
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS tuesday_close VARCHAR(5) DEFAULT '15:00';
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS is_closed_tuesday TINYINT(1) DEFAULT 0;
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS wednesday_open VARCHAR(5) DEFAULT '07:00';
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS wednesday_close VARCHAR(5) DEFAULT '15:00';
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS is_closed_wednesday TINYINT(1) DEFAULT 0;
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS thursday_open VARCHAR(5) DEFAULT '07:00';
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS thursday_close VARCHAR(5) DEFAULT '15:00';
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS is_closed_thursday TINYINT(1) DEFAULT 0;
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS friday_open VARCHAR(5) DEFAULT '07:00';
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS friday_close VARCHAR(5) DEFAULT '15:00';
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS is_closed_friday TINYINT(1) DEFAULT 0;
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS saturday_open VARCHAR(5) DEFAULT '07:00';
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS saturday_close VARCHAR(5) DEFAULT '12:00';
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS is_closed_saturday TINYINT(1) DEFAULT 0;
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS sunday_open VARCHAR(5) DEFAULT '07:00';
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS sunday_close VARCHAR(5) DEFAULT '15:00';
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS is_closed_sunday TINYINT(1) DEFAULT 1;

-- Statistics
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS total_students INT DEFAULT 0;
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS total_teachers INT DEFAULT 0;
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS graduation_rate INT DEFAULT 0;

-- ============================================
-- MENUS
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
    INDEX idx_menu_location (menu_location)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE menus ADD COLUMN IF NOT EXISTS icon VARCHAR(50) NULL;
ALTER TABLE menus ADD COLUMN IF NOT EXISTS parent_id INT UNSIGNED NULL;
ALTER TABLE menus ADD COLUMN IF NOT EXISTS sort_order INT DEFAULT 0;
ALTER TABLE menus ADD COLUMN IF NOT EXISTS is_active TINYINT(1) DEFAULT 1;
ALTER TABLE menus ADD COLUMN IF NOT EXISTS target ENUM('_self', '_blank') DEFAULT '_self';
ALTER TABLE menus ADD COLUMN IF NOT EXISTS menu_location ENUM('header', 'footer', 'both') DEFAULT 'header';
ALTER TABLE menus ADD COLUMN IF NOT EXISTS created_at DATETIME DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE menus ADD COLUMN IF NOT EXISTS updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Default menus (hanya insert jika tabel kosong)
INSERT INTO menus (title, url, sort_order, menu_location, is_active, target)
SELECT 'Beranda', '/', 1, 'header', 1, '_self'
WHERE NOT EXISTS (SELECT 1 FROM menus LIMIT 1);

INSERT INTO menus (title, url, sort_order, menu_location, is_active, target)
SELECT 'Profil', '/profil', 2, 'header', 1, '_self'
WHERE (SELECT COUNT(*) FROM menus) = 1;

INSERT INTO menus (title, url, sort_order, menu_location, is_active, target)
SELECT 'Berita', '/berita', 3, 'header', 1, '_self'
WHERE (SELECT COUNT(*) FROM menus) = 2;

INSERT INTO menus (title, url, sort_order, menu_location, is_active, target)
SELECT 'Galeri', '/galeri', 4, 'header', 1, '_self'
WHERE (SELECT COUNT(*) FROM menus) = 3;

INSERT INTO menus (title, url, sort_order, menu_location, is_active, target)
SELECT 'Kontak', '/kontak', 5, 'header', 1, '_self'
WHERE (SELECT COUNT(*) FROM menus) = 4;

-- ============================================
-- SITE SETTINGS
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

ALTER TABLE site_settings ADD COLUMN IF NOT EXISTS setting_value TEXT NULL;
ALTER TABLE site_settings ADD COLUMN IF NOT EXISTS setting_type ENUM('text', 'number', 'boolean', 'json', 'select') DEFAULT 'text';
ALTER TABLE site_settings ADD COLUMN IF NOT EXISTS description VARCHAR(255) NULL;
ALTER TABLE site_settings ADD COLUMN IF NOT EXISTS updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- ============================================
-- SITE VISITS
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
-- USERS - kolom tambahan
-- ============================================

ALTER TABLE users ADD COLUMN IF NOT EXISTS editor_id INT UNSIGNED NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS is_spmb_committee TINYINT(1) DEFAULT 0;
ALTER TABLE users ADD COLUMN IF NOT EXISTS permissions LONGTEXT NULL;

-- ============================================
-- NEWS - kolom yang sering hilang di hosting
-- ============================================

ALTER TABLE news ADD COLUMN IF NOT EXISTS slug VARCHAR(255) NULL;
ALTER TABLE news ADD COLUMN IF NOT EXISTS excerpt TEXT NULL;
ALTER TABLE news ADD COLUMN IF NOT EXISTS category VARCHAR(50) DEFAULT 'umum';
ALTER TABLE news ADD COLUMN IF NOT EXISTS category_id INT UNSIGNED NULL;
ALTER TABLE news ADD COLUMN IF NOT EXISTS status ENUM('draft', 'pending', 'published', 'archived') DEFAULT 'draft';
ALTER TABLE news ADD COLUMN IF NOT EXISTS views INT UNSIGNED DEFAULT 0;
ALTER TABLE news ADD COLUMN IF NOT EXISTS published_at DATETIME NULL;

-- ============================================
-- Reset schema version agar auto-repair jalan lagi
-- ============================================

UPDATE site_settings SET setting_value = '' WHERE setting_key = 'app_schema_version';
