-- Scheduled public popup and text-slider information.
CREATE TABLE IF NOT EXISTS announcements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type ENUM('popup', 'text_slider') NOT NULL DEFAULT 'popup',
    title VARCHAR(180) NULL,
    content TEXT NOT NULL,
    image VARCHAR(255) NULL,
    start_at DATETIME NULL,
    end_at DATETIME NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_announcement_active_schedule (type, is_active, start_at, end_at),
    INDEX idx_announcement_sort (type, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
