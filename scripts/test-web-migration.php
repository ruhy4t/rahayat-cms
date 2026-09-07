<?php

declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('STORAGE_PATH', ROOT_PATH . '/storage/cache/migration-test-' . bin2hex(random_bytes(8)));
require CONFIG_PATH . '/app.php';
require CONFIG_PATH . '/database.php';
spl_autoload_register(static function ($class): void {
    foreach (['Core', 'Models'] as $folder) {
        $path = APP_PATH . '/' . $folder . '/' . $class . '.php';
        if (is_file($path)) { require_once $path; return; }
    }
});
$pdo = Database::getInstance()->getConnection();
$schema = 'cms_migration_test_' . bin2hex(random_bytes(8));
$checks = 0;
function verifyMigration(bool $ok, string $message): void {
    global $checks;
    if (!$ok) { throw new RuntimeException($message); }
    $checks++;
}
mkdir(STORAGE_PATH . '/spmb', 0700, true);
try {
    $pdo->exec('CREATE TEMPORARY TABLE news (id INT PRIMARY KEY AUTO_INCREMENT, content LONGTEXT) ENGINE=InnoDB');
    $sql = preg_replace('/^--[^\n]*$/m', '', file_get_contents(ROOT_PATH . '/database/migrations/security_1_10_2_idempotent.sql'));
    $sql = str_replace(['spmb_registrations', 'site_visits'], [$schema . '_spmb', $schema . '_visits'], $sql);
    $apply = static function () use ($pdo, $sql): void {
        foreach (explode(';', $sql) as $statement) { if (trim($statement) !== '') { $pdo->exec($statement); } }
    };
    $apply();
    $pdo->exec("INSERT INTO {$schema}_spmb (registration_number, student_name, birth_date, gender, nisn) VALUES ('SYNTHETIC','Test migration','2013-01-01','P','0000000001')");
    $before = $pdo->query("SELECT * FROM {$schema}_spmb")->fetchAll(PDO::FETCH_ASSOC);
    foreach (['private_payload', 'privacy_accepted_at', 'address_village', 'address_district', 'address_city', 'address_province', 'previous_school_npsn'] as $column) {
        $pdo->exec("ALTER TABLE {$schema}_spmb DROP COLUMN " . $column);
    }
    $apply(); // Existing legacy schema.
    verifyMigration($before == $pdo->query("SELECT * FROM {$schema}_spmb")->fetchAll(PDO::FETCH_ASSOC), 'Legacy migration changed data');
    $definition = $pdo->query("SHOW CREATE TABLE {$schema}_spmb")->fetch(PDO::FETCH_NUM)[1];
    $apply(); // Reimport must be a no-op.
    verifyMigration($definition === $pdo->query("SHOW CREATE TABLE {$schema}_spmb")->fetch(PDO::FETCH_NUM)[1], 'SQL is not idempotent');
    $pdo->exec("CREATE TEMPORARY TABLE spmb_registrations LIKE {$schema}_spmb");
    $pdo->exec("INSERT INTO spmb_registrations SELECT * FROM {$schema}_spmb");
    $pdo->exec("INSERT INTO news (content) VALUES ('<section><img src=x onerror=alert(1)></section>')");
    file_put_contents(STORAGE_PATH . '/spmb/test.pdf', '%PDF synthetic bytes');
    $first = SecurityDataMigration::runBatch();
    verifyMigration($first['complete'] && $first['rows'] === 1 && $first['documents'] === 1 && $first['news'] === 1, 'Batch counts incorrect');
    $raw = $pdo->query('SELECT * FROM spmb_registrations')->fetch();
    verifyMigration($raw['student_name'] === '[Terlindungi]' && $raw['nisn'] === null, 'Plain fields retained');
    verifyMigration((new SPMBRegistration())->find((int) $raw['id'])['student_name'] === 'Test migration', 'Data round trip failed');
    verifyMigration(PrivateDocument::read(STORAGE_PATH . '/spmb/test.pdf') === '%PDF synthetic bytes', 'Document changed');
    $backups = glob(STORAGE_PATH . '/backups/security-*');
    $backup = json_decode(DataCipher::decrypt(file_get_contents($backups[0] . '/rows.enc')), true, 512, JSON_THROW_ON_ERROR);
    verifyMigration($backup['spmb_registrations'][0]['student_name'] === 'Test migration', 'Row backup invalid');
    verifyMigration(base64_decode(DataCipher::decrypt(file_get_contents($backups[0] . '/test.pdf.enc')), true) === '%PDF synthetic bytes', 'Document backup invalid');
    $bytes = file_get_contents(STORAGE_PATH . '/spmb/test.pdf');
    $second = SecurityDataMigration::runBatch();
    verifyMigration($second['complete'] && $second['backup'] === null && $second['rows'] === 0, 'Retry rewrote data');
    verifyMigration($bytes === file_get_contents(STORAGE_PATH . '/spmb/test.pdf'), 'Retry reencrypted document');
    // More than one batch, and resumability after a request boundary.
    for ($i = 0; $i < 6; $i++) {
        $pdo->exec("INSERT INTO spmb_registrations (registration_number, student_name, birth_date, gender) VALUES ('BATCH-$i','Synthetic','2013-01-01','L')");
    }
    $part = SecurityDataMigration::runBatch();
    verifyMigration(!$part['complete'] && $part['rows'] === 5, 'Batch limit not respected');
    $part = SecurityDataMigration::runBatch($part['cursor']);
    verifyMigration($part['complete'] && $part['rows'] === 1, 'Resume failed');
    $lock = fopen(STORAGE_PATH . '/backups/.security-migration.lock', 'c');
    flock($lock, LOCK_EX);
    try {
        $failed = false;
        try { SecurityDataMigration::runBatch(); } catch (RuntimeException $e) { $failed = true; }
        verifyMigration($failed, 'Concurrent batch not rejected');
    } finally { fclose($lock); }
    echo "$checks migration checks passed.\n";
} finally {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    $pdo->exec('DROP TEMPORARY TABLE IF EXISTS spmb_registrations, news');
    $pdo->exec("DROP TABLE IF EXISTS {$schema}_spmb, {$schema}_visits");
    // Only this test's randomly generated workspace is removed.
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(STORAGE_PATH, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($iterator as $file) { $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname()); }
    rmdir(STORAGE_PATH);
}
