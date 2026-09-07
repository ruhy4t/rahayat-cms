<?php

declare(strict_types=1);

/** Small, repeatable batches for shared hosting. Backups contain no encryption key. */
final class SecurityDataMigration
{
    public static function pendingDocuments(): array
    {
        $pending = [];
        foreach (glob(STORAGE_PATH . '/spmb/*') ?: [] as $path) {
            if (is_file($path) && !is_link($path) && !str_ends_with($path, '.tmp')
                && !PrivateDocument::isEncrypted($path)) { $pending[] = $path; }
        }
        return $pending;
    }

    private static function backup(string $path, string $plain): void
    {
        $cipher = DataCipher::encrypt($plain);
        if (file_put_contents($path, $cipher, LOCK_EX) !== strlen($cipher)) {
            throw new RuntimeException('Backup tidak dapat disimpan.');
        }
        @chmod($path, 0600);
        if (!hash_equals(hash('sha256', $plain), hash('sha256', DataCipher::decrypt((string) file_get_contents($path))))) {
            throw new RuntimeException('Verifikasi backup gagal.');
        }
    }

    public static function runBatch(int $newsCursor = 0): array
    {
        $lockDirectory = STORAGE_PATH . '/backups';
        if (!is_dir($lockDirectory) && !mkdir($lockDirectory, 0700, true)) {
            throw new RuntimeException('Folder backup tidak dapat dibuat.');
        }
        $lock = fopen($lockDirectory . '/.security-migration.lock', 'c');
        if ($lock === false) { throw new RuntimeException('Migrasi tidak dapat dikunci.'); }
        $pdo = Database::getInstance()->getConnection();
        try {
            if (!flock($lock, LOCK_EX | LOCK_NB)) { throw new RuntimeException('Migrasi lain sedang berjalan. Coba lagi.'); }
            $documents = self::pendingDocuments();
            $pdo->beginTransaction();
            $rows = $pdo->query("SELECT * FROM spmb_registrations WHERE private_payload IS NULL OR private_payload = '' ORDER BY id LIMIT 5 FOR UPDATE")->fetchAll(PDO::FETCH_ASSOC);
            $query = $pdo->prepare('SELECT id, content FROM news WHERE id > ? ORDER BY id LIMIT 10 FOR UPDATE');
            $query->execute([max(0, $newsCursor)]);
            $news = $query->fetchAll(PDO::FETCH_ASSOC);
            $changes = [];
            foreach ($news as $row) {
                $clean = Security::sanitizeHtml((string) $row['content']);
                if ($clean !== $row['content']) { $changes[] = ['id' => $row['id'], 'content' => $clean]; }
                $newsCursor = (int) $row['id'];
            }
            $directory = null;
            if ($rows || $changes || $documents) {
                $directory = $lockDirectory . '/security-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4));
                if (!mkdir($directory, 0700)) { throw new RuntimeException('Backup tidak dapat dibuat.'); }
                self::backup($directory . '/rows.enc', json_encode(['spmb_registrations' => $rows, 'news' => $news], JSON_THROW_ON_ERROR));
                if ($documents) {
                    self::backup($directory . '/' . basename($documents[0]) . '.enc', base64_encode(PrivateDocument::read($documents[0])));
                }
            }
            $model = new SPMBRegistration();
            foreach ($rows as $row) {
                $model->update((int) $row['id'], array_intersect_key($row, array_flip(SPMBRegistration::PRIVATE_FIELDS)));
                $restored = $model->find((int) $row['id']);
                foreach (SPMBRegistration::PRIVATE_FIELDS as $field) {
                    if (array_key_exists($field, $row) && $row[$field] !== $restored[$field]) {
                        throw new RuntimeException('Verifikasi data gagal.');
                    }
                }
            }
            $update = $pdo->prepare('UPDATE news SET content = ? WHERE id = ?');
            foreach ($changes as $row) { $update->execute([$row['content'], $row['id']]); }
            $pdo->commit();
            // A stopped request may finish only part of a batch. Retrying skips completed records/files.
            if ($documents) { PrivateDocument::encryptFile($documents[0]); }
            $remaining = (int) $pdo->query("SELECT COUNT(*) FROM spmb_registrations WHERE private_payload IS NULL OR private_payload = ''")->fetchColumn();
            $query = $pdo->prepare('SELECT COUNT(*) FROM news WHERE id > ?');
            $query->execute([$newsCursor]);
            return ['rows' => count($rows), 'documents' => $documents ? 1 : 0, 'news' => count($changes),
                'cursor' => $newsCursor, 'backup' => $directory ? 'storage/backups/' . basename($directory) : null,
                'complete' => $remaining === 0 && count($documents) <= 1 && (int) $query->fetchColumn() === 0];
        } finally {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            fclose($lock);
        }
    }
}
