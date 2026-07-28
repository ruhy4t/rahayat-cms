-- Rahayat CMS 1.5.0 - WhatsApp, Testimoni, dan Direktori Alumni
-- Aman dijalankan berulang kali pada MySQL/MariaDB yang mendukung CREATE TABLE IF NOT EXISTS.

CREATE TABLE IF NOT EXISTS testimonials (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    relationship VARCHAR(40) NOT NULL,
    graduation_year SMALLINT UNSIGNED NULL,
    occupation VARCHAR(120) NULL,
    testimonial TEXT NOT NULL,
    photo VARCHAR(255) NULL,
    contact VARCHAR(120) NULL,
    consent TINYINT(1) NOT NULL DEFAULT 0,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    submitted_ip_hash CHAR(64) NULL,
    approved_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_testimonial_public (status, consent, is_featured, sort_order),
    INDEX idx_testimonial_ip_created (submitted_ip_hash, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS alumni (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    graduation_year SMALLINT UNSIGNED NOT NULL,
    final_class VARCHAR(60) NULL,
    further_education VARCHAR(160) NULL,
    occupation VARCHAR(120) NULL,
    institution VARCHAR(160) NULL,
    city VARCHAR(100) NULL,
    story TEXT NULL,
    achievement TEXT NULL,
    photo VARCHAR(255) NULL,
    contact_encrypted TEXT NULL,
    contact_hash CHAR(64) NULL,
    consent TINYINT(1) NOT NULL DEFAULT 0,
    publish_photo TINYINT(1) NOT NULL DEFAULT 0,
    publish_occupation TINYINT(1) NOT NULL DEFAULT 0,
    publish_city TINYINT(1) NOT NULL DEFAULT 0,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    submitted_ip_hash CHAR(64) NULL,
    approved_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_alumni_public (status, consent, graduation_year, is_featured, sort_order),
    INDEX idx_alumni_city (city),
    INDEX idx_alumni_occupation (occupation),
    INDEX idx_alumni_contact_hash (contact_hash),
    INDEX idx_alumni_ip_created (submitted_ip_hash, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO site_settings (setting_key, setting_value, setting_type, description)
VALUES
('whatsapp_enabled', '1', 'boolean', 'Show the floating WhatsApp feedback button'),
('whatsapp_number', NULL, 'text', 'School WhatsApp number; profile phone is used when empty'),
('whatsapp_message', 'Halo, saya ingin menyampaikan saran, masukan, atau aduan kepada pihak sekolah.', 'text', 'Prefilled WhatsApp feedback message')
ON DUPLICATE KEY UPDATE setting_key = VALUES(setting_key);

INSERT INTO site_settings (setting_key, setting_value, setting_type, description)
VALUES ('app_schema_version', '1.5.0', 'text', 'Last repaired schema version')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), setting_type = VALUES(setting_type), description = VALUES(description);
