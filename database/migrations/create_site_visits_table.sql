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
