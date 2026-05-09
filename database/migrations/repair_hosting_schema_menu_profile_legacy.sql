-- Hotfix schema untuk hosting MySQL/MariaDB lawas
-- Fokus: masalah navbar hilang + gagal simpan profil
-- Aman dijalankan berulang (idempotent)

-- 1) Cek versi DB dulu (opsional)
SELECT VERSION() AS db_version;

-- 2) Pastikan tabel inti ada
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

CREATE TABLE IF NOT EXISTS menus (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3) Tambah kolom school_profile jika belum ada (kompatibel MySQL 5.7)
SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'school_profile' AND column_name = 'tagline');
SET @sql := IF(@exists = 0, 'ALTER TABLE school_profile ADD COLUMN tagline VARCHAR(255) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'school_profile' AND column_name = 'principal_nip');
SET @sql := IF(@exists = 0, 'ALTER TABLE school_profile ADD COLUMN principal_nip VARCHAR(50) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'school_profile' AND column_name = 'principal_photo');
SET @sql := IF(@exists = 0, 'ALTER TABLE school_profile ADD COLUMN principal_photo VARCHAR(255) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'school_profile' AND column_name = 'welcome_message');
SET @sql := IF(@exists = 0, 'ALTER TABLE school_profile ADD COLUMN welcome_message LONGTEXT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'school_profile' AND column_name = 'principal_quote');
SET @sql := IF(@exists = 0, 'ALTER TABLE school_profile ADD COLUMN principal_quote TEXT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'school_profile' AND column_name = 'spmb_link');
SET @sql := IF(@exists = 0, 'ALTER TABLE school_profile ADD COLUMN spmb_link VARCHAR(255) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'school_profile' AND column_name = 'google_maps_embed');
SET @sql := IF(@exists = 0, 'ALTER TABLE school_profile ADD COLUMN google_maps_embed TEXT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'school_profile' AND column_name = 'watermark_enabled');
SET @sql := IF(@exists = 0, 'ALTER TABLE school_profile ADD COLUMN watermark_enabled TINYINT(1) DEFAULT 1', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'school_profile' AND column_name = 'total_students');
SET @sql := IF(@exists = 0, 'ALTER TABLE school_profile ADD COLUMN total_students INT DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'school_profile' AND column_name = 'total_teachers');
SET @sql := IF(@exists = 0, 'ALTER TABLE school_profile ADD COLUMN total_teachers INT DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'school_profile' AND column_name = 'graduation_rate');
SET @sql := IF(@exists = 0, 'ALTER TABLE school_profile ADD COLUMN graduation_rate INT DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Jam operasional (yang sering bikin gagal save profil)
SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'school_profile' AND column_name = 'monday_open');
SET @sql := IF(@exists = 0, "ALTER TABLE school_profile ADD COLUMN monday_open VARCHAR(5) DEFAULT '07:00'", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'school_profile' AND column_name = 'monday_close');
SET @sql := IF(@exists = 0, "ALTER TABLE school_profile ADD COLUMN monday_close VARCHAR(5) DEFAULT '15:00'", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'school_profile' AND column_name = 'is_closed_monday');
SET @sql := IF(@exists = 0, 'ALTER TABLE school_profile ADD COLUMN is_closed_monday TINYINT(1) DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'school_profile' AND column_name = 'friday_open');
SET @sql := IF(@exists = 0, "ALTER TABLE school_profile ADD COLUMN friday_open VARCHAR(5) DEFAULT '07:00'", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'school_profile' AND column_name = 'friday_close');
SET @sql := IF(@exists = 0, "ALTER TABLE school_profile ADD COLUMN friday_close VARCHAR(5) DEFAULT '15:00'", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'school_profile' AND column_name = 'is_closed_friday');
SET @sql := IF(@exists = 0, 'ALTER TABLE school_profile ADD COLUMN is_closed_friday TINYINT(1) DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'school_profile' AND column_name = 'sunday_open');
SET @sql := IF(@exists = 0, "ALTER TABLE school_profile ADD COLUMN sunday_open VARCHAR(5) DEFAULT '07:00'", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'school_profile' AND column_name = 'sunday_close');
SET @sql := IF(@exists = 0, "ALTER TABLE school_profile ADD COLUMN sunday_close VARCHAR(5) DEFAULT '15:00'", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'school_profile' AND column_name = 'is_closed_sunday');
SET @sql := IF(@exists = 0, 'ALTER TABLE school_profile ADD COLUMN is_closed_sunday TINYINT(1) DEFAULT 1', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 4) Tambah kolom menus jika belum ada
SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'menus' AND column_name = 'icon');
SET @sql := IF(@exists = 0, 'ALTER TABLE menus ADD COLUMN icon VARCHAR(50) NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'menus' AND column_name = 'parent_id');
SET @sql := IF(@exists = 0, 'ALTER TABLE menus ADD COLUMN parent_id INT UNSIGNED NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'menus' AND column_name = 'sort_order');
SET @sql := IF(@exists = 0, 'ALTER TABLE menus ADD COLUMN sort_order INT DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'menus' AND column_name = 'is_active');
SET @sql := IF(@exists = 0, 'ALTER TABLE menus ADD COLUMN is_active TINYINT(1) DEFAULT 1', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'menus' AND column_name = 'target');
SET @sql := IF(@exists = 0, "ALTER TABLE menus ADD COLUMN target ENUM('_self','_blank') DEFAULT '_self'", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @exists := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'menus' AND column_name = 'menu_location');
SET @sql := IF(@exists = 0, "ALTER TABLE menus ADD COLUMN menu_location ENUM('header','footer','both') DEFAULT 'header'", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 5) Seed menu default jika kosong
INSERT INTO menus (title, url, sort_order, menu_location, is_active, target)
SELECT 'Beranda', '/', 1, 'header', 1, '_self'
WHERE NOT EXISTS (SELECT 1 FROM menus);

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

-- 6) Pastikan 1 baris profil minimal ada
INSERT INTO school_profile (name, npsn, address, phone, email, website, principal_name, vision, mission, motto, accreditation, school_type, established_year)
SELECT 'Sekolah Rahayat', '12345678', 'Jl. Pendidikan No. 1', '(022) 123-4567', 'info@rahayat.sch.id', 'https://rahayat.sch.id', 'Kepala Sekolah', 'Menjadi sekolah unggul.', 'Menyelenggarakan pendidikan berkualitas.', 'Cerdas, Berkarakter, Berprestasi', 'A', 'negeri', '2010'
WHERE NOT EXISTS (SELECT 1 FROM school_profile);

-- 7) Diagnostik akhir
SELECT table_name, column_name
FROM information_schema.columns
WHERE table_schema = DATABASE()
  AND (
      (table_name = 'menus' AND column_name IN ('icon','parent_id','sort_order','is_active','target','menu_location'))
      OR
      (table_name = 'school_profile' AND column_name IN ('tagline','spmb_link','welcome_message','principal_quote','monday_open','monday_close','is_closed_monday','friday_open','sunday_close','is_closed_sunday'))
  )
ORDER BY table_name, column_name;
