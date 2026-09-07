<?php

declare(strict_types=1);
require __DIR__ . '/security-bootstrap.php';

// Back up and verify before rewriting existing data. No personal data is printed.
$pdo = Database::getInstance()->getConnection();
SecuritySchema::ensure();
$spmb = $pdo->query("SELECT * FROM spmb_registrations WHERE private_payload IS NULL OR private_payload = ''")->fetchAll();
$news = $pdo->query('SELECT id, content FROM news')->fetchAll();
$changes = [];
foreach ($news as $row) {
    $clean = Security::sanitizeHtml((string) $row['content']);
    if ($clean !== $row['content']) { $changes[] = ['id' => $row['id'], 'content' => $clean]; }
}
$documents = glob(STORAGE_PATH . '/spmb/*') ?: [];
$documents = array_values(array_filter($documents, static fn($path) => is_file($path) && !str_starts_with(basename($path), '.')));
if (!in_array('--apply', $argv, true)) {
    echo json_encode(['legacy_spmb' => count($spmb), 'news_to_sanitize' => count($changes), 'documents' => count($documents)]), PHP_EOL;
    exit;
}
$directory = STORAGE_PATH . '/backups/security-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4));
if (!mkdir($directory, 0700, true)) { throw new RuntimeException('Backup directory unavailable.'); }
$backup = json_encode(['spmb_registrations' => $spmb, 'news' => $news], JSON_THROW_ON_ERROR);
$encrypted = DataCipher::encrypt($backup);
$backupFile = $directory . '/rows.enc';
if (file_put_contents($backupFile, $encrypted, LOCK_EX) !== strlen($encrypted)
    || !hash_equals(hash('sha256', $backup), hash('sha256', DataCipher::decrypt(file_get_contents($backupFile))))) {
    throw new RuntimeException('Backup verification failed. No data changed.');
}
// Keys remain separate from data backups; preserve the existing key securely.
foreach ($documents as $path) {
    $plain = PrivateDocument::read($path);
    $copy = $directory . '/' . basename($path) . '.enc';
    $payload = DataCipher::encrypt(base64_encode($plain));
    if (file_put_contents($copy, $payload, LOCK_EX) !== strlen($payload)
        || !hash_equals(hash('sha256', $plain), hash('sha256', base64_decode(DataCipher::decrypt(file_get_contents($copy)), true)))) {
        throw new RuntimeException('Document backup verification failed.');
    }
}
$pdo->beginTransaction();
try {
    $model = new SPMBRegistration();
    foreach ($spmb as $row) {
        $model->update((int) $row['id'], array_intersect_key($row, array_flip(SPMBRegistration::PRIVATE_FIELDS)));
        $restored = $model->find((int) $row['id']);
        foreach (SPMBRegistration::PRIVATE_FIELDS as $field) {
            if (array_key_exists($field, $row) && $row[$field] !== $restored[$field]) { throw new RuntimeException('SPMB round-trip verification failed.'); }
        }
    }
    foreach ($changes as $row) {
        $stmt = $pdo->prepare('UPDATE news SET content = ? WHERE id = ?');
        $stmt->execute([$row['content'], $row['id']]);
    }
    foreach ($documents as $path) { PrivateDocument::encryptFile($path); }
    $pdo->commit();
} catch (Throwable $error) {
    $pdo->rollBack();
    throw $error;
}
echo json_encode(['encrypted_spmb' => count($spmb), 'sanitized_news' => count($changes),
    'encrypted_documents' => count($documents), 'verified_backup' => $directory]), PHP_EOL;
