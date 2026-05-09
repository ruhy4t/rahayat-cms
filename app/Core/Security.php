<?php
/**
 * ============================================
 * Security Class - CSRF & XSS Protection
 * ============================================
 */

declare(strict_types=1);

class Security
{
    /**
     * Generate CSRF token
     */
    public static function generateCsrfToken(): string
    {
        if (
            empty($_SESSION[CSRF_TOKEN_NAME]) ||
            (isset($_SESSION['_csrf_time']) && time() - $_SESSION['_csrf_time'] > CSRF_TOKEN_EXPIRY)
        ) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
            $_SESSION['_csrf_time'] = time();
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    /**
     * Validate CSRF token
     */
    public static function validateCsrfToken(?string $token): bool
    {
        if (empty($token) || empty($_SESSION[CSRF_TOKEN_NAME])) {
            return false;
        }

        // Check expiry
        if (isset($_SESSION['_csrf_time']) && time() - $_SESSION['_csrf_time'] > CSRF_TOKEN_EXPIRY) {
            unset($_SESSION[CSRF_TOKEN_NAME], $_SESSION['_csrf_time']);
            return false;
        }

        return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }

    /**
     * Get CSRF token input HTML
     */
    public static function csrfInput(): string
    {
        $token = self::generateCsrfToken();
        return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . $token . '">';
    }

    /**
     * Get CSRF token for AJAX
     */
    public static function csrfMeta(): string
    {
        $token = self::generateCsrfToken();
        return '<meta name="csrf-token" content="' . $token . '">';
    }

    /**
     * Get CSRF token value (alias for generateCsrfToken)
     */
    public static function csrf(): string
    {
        return self::generateCsrfToken();
    }

    /**
     * XSS filter - escape output
     */
    public static function escape(mixed $value): string
    {
        if ($value === null) {
            return '';
        }
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Alias for escape
     */
    public static function e(mixed $value): string
    {
        return self::escape($value);
    }

    /**
     * Sanitize input
     */
    public static function sanitize(mixed $value): string
    {
        if ($value === null) {
            return '';
        }
        $value = trim((string) $value);
        $value = stripslashes($value);
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Sanitize array of inputs
     */
    public static function sanitizeArray(array $data): array
    {
        return array_map(function ($value) {
            if (is_array($value)) {
                return self::sanitizeArray($value);
            }
            return self::sanitize($value);
        }, $data);
    }

    /**
     * Validate and sanitize email
     */
    public static function sanitizeEmail(string $email): string|false
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validate integer
     */
    public static function sanitizeInt(mixed $value): int
    {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Hash password using Argon2ID
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ALGO, PASSWORD_OPTIONS);
    }

    /**
     * Verify password
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Check if password needs rehash
     */
    public static function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_ALGO, PASSWORD_OPTIONS);
    }

    /**
     * Generate random string
     */
    public static function randomString(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Validate file upload
     */
    public static function validateUpload(array $file, ?array $allowedTypes = null, ?int $maxSize = null): array
    {
        $errors = [];
        $allowedTypes = $allowedTypes ?? UPLOAD_ALLOWED_TYPES;
        $maxSize = $maxSize ?? UPLOAD_MAX_SIZE;
        $uploadError = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);

        if ($uploadError !== UPLOAD_ERR_OK) {
            $errors[] = self::uploadErrorDescription($uploadError);
            return $errors;
        }

        if (($file['size'] ?? 0) > $maxSize) {
            $errors[] = 'Ukuran file melebihi batas ' . self::formatBytes($maxSize) . '.';
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name'] ?? '');

        if (!$mimeType) {
            $errors[] = 'Tipe file tidak dapat dibaca.';
        } elseif (!in_array($mimeType, $allowedTypes, true)) {
            $errors[] = 'Tipe file tidak didukung (' . $mimeType . '). Gunakan ' . self::allowedUploadLabel($allowedTypes) . '.';
        }

        return $errors;
    }

    public static function allowedUploadLabel(?array $allowedTypes = null): string
    {
        $allowedTypes = $allowedTypes ?? UPLOAD_ALLOWED_TYPES;
        $labels = [];

        foreach ($allowedTypes as $type) {
            $labels[] = match ($type) {
                'image/jpeg', 'image/pjpeg' => 'JPG/JPEG',
                'image/png', 'image/x-png' => 'PNG',
                'image/gif' => 'GIF',
                'image/webp' => 'WebP',
                'application/pdf' => 'PDF',
                default => $type,
            };
        }

        return implode(', ', array_values(array_unique($labels)));
    }

    /**
     * Return a safe file extension for an allowed MIME type.
     */
    public static function extensionForMime(string $mimeType): ?string
    {
        return match ($mimeType) {
            'image/jpeg', 'image/pjpeg' => 'jpg',
            'image/png', 'image/x-png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
            default => null,
        };
    }

    private static function uploadErrorDescription(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Ukuran file melebihi batas PHP di server (' . self::serverUploadLimitLabel() . '). Naikkan upload_max_filesize dan post_max_size di hosting, atau pastikan file .user.ini terbaru sudah ter-upload.',
            UPLOAD_ERR_PARTIAL => 'File hanya terunggah sebagian. Coba unggah ulang.',
            UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diunggah.',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder sementara upload tidak tersedia di server.',
            UPLOAD_ERR_CANT_WRITE => 'Server gagal menulis file upload.',
            UPLOAD_ERR_EXTENSION => 'Upload diblokir oleh ekstensi PHP.',
            default => 'Upload gagal dengan kode error: ' . $code,
        };
    }

    private static function serverUploadLimitLabel(): string
    {
        $uploadMax = ini_get('upload_max_filesize') ?: 'tidak diketahui';
        $postMax = ini_get('post_max_size') ?: 'tidak diketahui';

        return 'upload_max_filesize=' . $uploadMax . ', post_max_size=' . $postMax;
    }

    private static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = (float) $bytes;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return rtrim(rtrim(number_format($size, 2, '.', ''), '0'), '.') . ' ' . $units[$unit];
    }

    /**
     * Safe redirect
     */
    public static function redirect(string $url, int $statusCode = 302): never
    {
        // Prevent header injection
        $url = filter_var($url, FILTER_SANITIZE_URL);
        header('Location: ' . $url, true, $statusCode);
        exit;
    }

    /**
     * Check if request is AJAX
     */
    public static function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Check if request method is POST
     */
    public static function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Check if request method is GET
     */
    public static function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
}

/**
 * Helper function for escaping
 */
function e(mixed $value): string
{
    return Security::escape($value);
}
