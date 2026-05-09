<?php
require_once __DIR__ . '/config/database.php';

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        DB_OPTIONS
    );

    // Add columns if they don't exist
    $columns = ['address_village', 'address_district', 'address_city', 'address_province'];

    $stmt = $pdo->query("SHOW COLUMNS FROM spmb_registrations LIKE 'address_village'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE spmb_registrations 
            ADD COLUMN address_village VARCHAR(100) NULL AFTER address,
            ADD COLUMN address_district VARCHAR(100) NULL AFTER address_village,
            ADD COLUMN address_city VARCHAR(100) NULL AFTER address_district,
            ADD COLUMN address_province VARCHAR(100) NULL AFTER address_city");
        echo "Columns added to spmb_registrations successfully.\n";
    } else {
        echo "Columns already exist.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
