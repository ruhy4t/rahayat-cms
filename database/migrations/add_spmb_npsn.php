<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config/database.php';

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Add columns if they don't exist
    $stmt = $pdo->query("SHOW COLUMNS FROM spmb_registrations LIKE 'previous_school_npsn'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE spmb_registrations 
            ADD COLUMN previous_school_npsn VARCHAR(20) NULL AFTER previous_school");
        echo "Column previous_school_npsn added to spmb_registrations successfully.\n";
    } else {
        echo "Column already exists.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
