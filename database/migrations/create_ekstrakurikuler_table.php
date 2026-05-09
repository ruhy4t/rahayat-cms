<?php
/**
 * Migration to add ekstrakurikuler table
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/Core/Database.php';

try {
    $db = Database::getInstance()->getConnection();

    // Create ekstrakurikuler table
    $sql = "
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
        
        INDEX idx_is_active (is_active),
        INDEX idx_sort_order (sort_order)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $db->exec($sql);
    echo "Successfully created ekstrakurikuler table.\n";

    // Insert sample data
    $checkSql = "SELECT COUNT(*) FROM ekstrakurikuler";
    $count = $db->query($checkSql)->fetchColumn();

    if ($count == 0) {
        $insertSql = "INSERT INTO ekstrakurikuler (name, description, schedule, supervisor, is_active, sort_order) VALUES 
        ('Pramuka', 'Kegiatan kepramukaan yang melatih kemandirian, kedisiplinan, dan jiwa korsa.', 'Jumat, 15:30 - 17:00', 'Bpk. Ahmad Soleh, S.Pd', 1, 1),
        ('PMR (Palang Merah Remaja)', 'Ekstrakurikuler yang bergerak di bidang kepalangmerahan dan kesehatan remaja.', 'Sabtu, 10:00 - 12:00', 'Ibu Rahmawati, S.Kep', 1, 2),
        ('Paskibra', 'Pasukan Pengibar Bendera Pusaka untuk melatih kedisiplinan dan jiwa nasionalisme.', 'Senin & Kamis, 15:30 - 17:00', 'Bpk. Budi Santoso, S.Pd', 1, 3),
        ('Futsal', 'Kegiatan olahraga futsal untuk mengembangkan bakat dan minat di bidang olahraga bola besar.', 'Selasa & Jumat, 15:30 - 17:30', 'Bpk. Dwi Prasetyo, S.Or', 1, 4),
        ('Seni Tari', 'Kegiatan melestarikan budaya bangsa melalui seni tari tradisional dan kontemporer.', 'Rabu, 15:00 - 17:00', 'Ibu Lestari, S.Sn', 1, 5)
        ";

        $db->exec($insertSql);
        echo "Successfully inserted sample data for ekstrakurikuler.\n";
    }

} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
