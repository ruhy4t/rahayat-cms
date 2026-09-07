<?php

declare(strict_types=1);

final class SecuritySchema
{
    public static function ensure(): void
    {
        $pdo = Database::getInstance()->getConnection();
        $schema = file_get_contents(ROOT_PATH . '/database/schema.sql');
        if (!preg_match('/CREATE TABLE IF NOT EXISTS spmb_registrations\s*\([\s\S]*?ENGINE=InnoDB[^;]*;/i', $schema, $match)) {
            throw new RuntimeException('Schema SPMB tidak ditemukan.');
        }
        $pdo->exec($match[0]);
        $columns = $pdo->query('SHOW COLUMNS FROM spmb_registrations')->fetchAll(PDO::FETCH_COLUMN);
        $required = ['private_payload' => 'LONGTEXT NULL', 'privacy_accepted_at' => 'DATETIME NULL',
            'address_village' => 'VARCHAR(100) NULL', 'address_district' => 'VARCHAR(100) NULL',
            'address_city' => 'VARCHAR(100) NULL', 'address_province' => 'VARCHAR(100) NULL',
            'previous_school_npsn' => 'VARCHAR(8) NULL'];
        foreach ($required as $name => $definition) {
            if (!in_array($name, $columns, true)) {
                $pdo->exec("ALTER TABLE spmb_registrations ADD COLUMN {$name} {$definition}");
            }
        }
        $pdo->exec(file_get_contents(ROOT_PATH . '/database/migrations/create_site_visits_table.sql'));
    }
}
