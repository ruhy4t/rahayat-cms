<?php

declare(strict_types=1);

/**
 * Authenticated encryption for private application data.
 *
 * Set DATA_ENCRYPTION_KEY in the hosting environment for portable deployments.
 * When it is absent, a random key is generated in storage/.data-encryption-key.
 * That file must be included in encrypted backups.
 */
final class DataCipher
{
    private const CIPHER = 'aes-256-gcm';
    private const PREFIX = 'v1:';
    private static ?string $key = null;

    public static function encrypt(?string $plaintext): ?string
    {
        $plaintext = trim((string) $plaintext);
        if ($plaintext === '') {
            return null;
        }

        $iv = random_bytes(12);
        $tag = '';
        $ciphertext = openssl_encrypt(
            $plaintext,
            self::CIPHER,
            self::key(),
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            16
        );
        if ($ciphertext === false) {
            throw new RuntimeException('Data pribadi gagal dienkripsi.');
        }

        return self::PREFIX . base64_encode($iv . $tag . $ciphertext);
    }

    public static function decrypt(?string $payload): string
    {
        $payload = (string) $payload;
        if ($payload === '') {
            return '';
        }
        if (!str_starts_with($payload, self::PREFIX)) {
            return '';
        }

        $binary = base64_decode(substr($payload, strlen(self::PREFIX)), true);
        if ($binary === false || strlen($binary) < 29) {
            return '';
        }

        $plaintext = openssl_decrypt(
            substr($binary, 28),
            self::CIPHER,
            self::key(),
            OPENSSL_RAW_DATA,
            substr($binary, 0, 12),
            substr($binary, 12, 16)
        );

        return $plaintext === false ? '' : $plaintext;
    }

    public static function blindIndex(?string $value): ?string
    {
        $normalized = strtolower(preg_replace('/\s+/', '', trim((string) $value)) ?? '');
        return $normalized === '' ? null : hash_hmac('sha256', $normalized, self::key());
    }

    private static function key(): string
    {
        if (self::$key !== null) {
            return self::$key;
        }
        if (!extension_loaded('openssl')) {
            throw new RuntimeException('Ekstensi OpenSSL diperlukan untuk melindungi data pribadi alumni.');
        }

        $environmentKey = trim((string) getenv('DATA_ENCRYPTION_KEY'));
        if ($environmentKey !== '') {
            if (strlen($environmentKey) < 32) { throw new RuntimeException('Kunci enkripsi harus minimal 32 karakter.'); }
            self::$key = hash('sha256', $environmentKey, true);
            return self::$key;
        }

        $keyFile = STORAGE_PATH . '/.data-encryption-key';
        $existed = is_file($keyFile);
        $handle = fopen($keyFile, 'c+');
        if (!$handle) { throw new RuntimeException('Kunci enkripsi tidak dapat dibuka.'); }
        try {
            if (!flock($handle, LOCK_EX)) { throw new RuntimeException('Kunci enkripsi tidak dapat dikunci.'); }
            @chmod($keyFile, 0600);
            $encoded = trim(stream_get_contents($handle));
            if ($encoded !== '') {
                $key = base64_decode($encoded, true);
                if ($key === false || strlen($key) !== 32) { throw new RuntimeException('Kunci enkripsi lokal tidak valid.'); }
            } else {
                if ($existed) { throw new RuntimeException('Kunci enkripsi lokal kosong. Pulihkan kunci dari cadangan.'); }
                $key = random_bytes(32);
                $value = base64_encode($key);
                rewind($handle);
                if (fwrite($handle, $value) !== strlen($value) || !fflush($handle)) {
                    throw new RuntimeException('Kunci enkripsi tidak dapat disimpan.');
                }
            }
            return self::$key = $key;
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
}
