<?php

declare(strict_types=1);
require __DIR__ . '/security-bootstrap.php';

$root = realpath(STORAGE_PATH . '/backups');
$directory = realpath($argv[1] ?? '');
if (!$root || !$directory || !str_starts_with($directory, $root . DIRECTORY_SEPARATOR)) {
    throw new RuntimeException('Specify a backup directory inside storage/backups.');
}
$rows = json_decode(DataCipher::decrypt(file_get_contents($directory . '/rows.enc')), true, 512, JSON_THROW_ON_ERROR);
if (!isset($rows['spmb_registrations'], $rows['news'])) { throw new RuntimeException('Invalid row backup.'); }
$verified = 0;
foreach (glob($directory . '/*.enc') ?: [] as $file) {
    if (basename($file) === 'rows.enc') { continue; }
    $plain = DataCipher::decrypt(file_get_contents($file));
    if ($plain === '' || base64_decode($plain, true) === false) { throw new RuntimeException('Invalid document backup.'); }
    $verified++;
}
echo json_encode(['verified_spmb_rows' => count($rows['spmb_registrations']), 'verified_news_rows' => count($rows['news']),
    'verified_documents' => $verified, 'writes' => 0]), PHP_EOL;
