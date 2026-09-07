<?php

declare(strict_types=1);

final class PrivateDocument
{
    private const PREFIX = 'RAHAYAT-PRIVATE-V1:';

    public static function isEncrypted(string $path): bool
    {
        $handle = fopen($path, 'rb');
        if ($handle === false) { throw new RuntimeException('Dokumen tidak dapat dibaca.'); }
        try { return fread($handle, strlen(self::PREFIX)) === self::PREFIX; }
        finally { fclose($handle); }
    }

    public static function read(string $path): string
    {
        $data = file_get_contents($path);
        if ($data === false) { throw new RuntimeException('Dokumen tidak dapat dibaca.'); }
        if (!str_starts_with($data, self::PREFIX)) { return $data; }
        $plain = DataCipher::decrypt(substr($data, strlen(self::PREFIX)));
        $binary = base64_decode($plain, true);
        if ($plain === '' || $binary === false) { throw new RuntimeException('Dokumen gagal didekripsi.'); }
        return $binary;
    }

    public static function encryptFile(string $path): void
    {
        $plain = self::read($path);
        $payload = self::PREFIX . DataCipher::encrypt(base64_encode($plain));
        $temporary = $path . '.' . bin2hex(random_bytes(8)) . '.tmp';
        try {
            if (file_put_contents($temporary, $payload, LOCK_EX) !== strlen($payload)) {
                throw new RuntimeException('Dokumen gagal dienkripsi.');
            }
            @chmod($temporary, 0600);
            if (!hash_equals(hash('sha256', $plain), hash('sha256', self::read($temporary)))) {
                throw new RuntimeException('Verifikasi enkripsi dokumen gagal.');
            }
            if (!rename($temporary, $path)) { throw new RuntimeException('Dokumen gagal disimpan.'); }
        } finally {
            if (is_file($temporary)) { unlink($temporary); }
        }
    }
}
