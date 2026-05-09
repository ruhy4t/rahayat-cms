-- Rahayat CMS 1.0.2 hosting repair.
-- Jalankan lewat phpMyAdmin jika auto repair tidak berjalan karena pembatasan hosting.

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

ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS tagline VARCHAR(255) NULL;
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS welcome_message LONGTEXT NULL;
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS principal_quote TEXT NULL;
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS spmb_link VARCHAR(255) NULL;
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS principal_nip VARCHAR(50) NULL;
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS principal_photo VARCHAR(255) NULL;
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS google_maps_embed TEXT NULL;
ALTER TABLE school_profile ADD COLUMN IF NOT EXISTS watermark_enabled TINYINT(1) DEFAULT 1;

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
