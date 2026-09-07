-- Rahayat CMS 1.10.2: impor melalui phpMyAdmin setelah memilih database CMS.
-- Idempoten: tidak menghapus/mengubah record, aman diimpor ulang.
-- Enkripsi data lama: lanjutkan di /admin/pembaruan/migrasi setelah upload kode.

CREATE TABLE IF NOT EXISTS spmb_registrations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    registration_number VARCHAR(50) UNIQUE,
    private_payload LONGTEXT NULL,
    privacy_accepted_at DATETIME NULL,

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

SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'spmb_registrations' AND column_name = 'private_payload');
SET @migration_sql = IF(@column_exists = 0, 'ALTER TABLE spmb_registrations ADD COLUMN private_payload LONGTEXT NULL', 'DO 0');
PREPARE migration_statement FROM @migration_sql;
EXECUTE migration_statement;
DEALLOCATE PREPARE migration_statement;

SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'spmb_registrations' AND column_name = 'privacy_accepted_at');
SET @migration_sql = IF(@column_exists = 0, 'ALTER TABLE spmb_registrations ADD COLUMN privacy_accepted_at DATETIME NULL', 'DO 0');
PREPARE migration_statement FROM @migration_sql;
EXECUTE migration_statement;
DEALLOCATE PREPARE migration_statement;

SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'spmb_registrations' AND column_name = 'address_village');
SET @migration_sql = IF(@column_exists = 0, 'ALTER TABLE spmb_registrations ADD COLUMN address_village VARCHAR(100) NULL', 'DO 0');
PREPARE migration_statement FROM @migration_sql;
EXECUTE migration_statement;
DEALLOCATE PREPARE migration_statement;

SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'spmb_registrations' AND column_name = 'address_district');
SET @migration_sql = IF(@column_exists = 0, 'ALTER TABLE spmb_registrations ADD COLUMN address_district VARCHAR(100) NULL', 'DO 0');
PREPARE migration_statement FROM @migration_sql;
EXECUTE migration_statement;
DEALLOCATE PREPARE migration_statement;

SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'spmb_registrations' AND column_name = 'address_city');
SET @migration_sql = IF(@column_exists = 0, 'ALTER TABLE spmb_registrations ADD COLUMN address_city VARCHAR(100) NULL', 'DO 0');
PREPARE migration_statement FROM @migration_sql;
EXECUTE migration_statement;
DEALLOCATE PREPARE migration_statement;

SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'spmb_registrations' AND column_name = 'address_province');
SET @migration_sql = IF(@column_exists = 0, 'ALTER TABLE spmb_registrations ADD COLUMN address_province VARCHAR(100) NULL', 'DO 0');
PREPARE migration_statement FROM @migration_sql;
EXECUTE migration_statement;
DEALLOCATE PREPARE migration_statement;

SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'spmb_registrations' AND column_name = 'previous_school_npsn');
SET @migration_sql = IF(@column_exists = 0, 'ALTER TABLE spmb_registrations ADD COLUMN previous_school_npsn VARCHAR(8) NULL', 'DO 0');
PREPARE migration_statement FROM @migration_sql;
EXECUTE migration_statement;
DEALLOCATE PREPARE migration_statement;

-- Migration: Create site_visits analytics table

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
