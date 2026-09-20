-- Rahayat CMS 1.11.0. Pilih database aplikasi sebelum mengimpor.
-- Aman dijalankan ulang. Tidak menghapus atau menimpa data yang sudah ada.
-- Pengaturan pesan kepala sekolah memakai site_settings yang sudah tersedia.
CREATE TABLE IF NOT EXISTS academic_years (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    start_year SMALLINT UNSIGNED NOT NULL,
    is_published TINYINT(1) NOT NULL DEFAULT 0,
    pdf_path VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_academic_start_year (start_year)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS school_events (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    academic_year_id INT UNSIGNED NOT NULL,
    kind VARCHAR(20) NOT NULL DEFAULT 'agenda',
    title VARCHAR(180) NOT NULL,
    category VARCHAR(20) NOT NULL DEFAULT 'kegiatan',
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    event_time TIME NULL,
    location VARCHAR(200) NOT NULL DEFAULT '',
    description TEXT NULL,
    show_in_calendar TINYINT(1) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'draft',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_events_public (status, kind, end_date, start_date),
    INDEX idx_events_year (academic_year_id, status, start_date, end_date),
    CONSTRAINT fk_events_academic_year FOREIGN KEY (academic_year_id) REFERENCES academic_years(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
