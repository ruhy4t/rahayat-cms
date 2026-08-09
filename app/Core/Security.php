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
     * Render a lightweight arithmetic CAPTCHA for a public submission form.
     * Multiple tokens are retained briefly so separate browser tabs do not
     * invalidate one another.
     */
    public static function publicCaptchaInput(string $scope): string
    {
        $scope = preg_replace('/[^a-z0-9_-]/i', '', $scope) ?: 'public';
        $token = bin2hex(random_bytes(16));
        $left = random_int(2, 9);
        $right = random_int(1, 9);
        $answer = $left + $right;
        $secret = bin2hex(random_bytes(16));

        self::prunePublicCaptchas($scope);
        $_SESSION['_public_captchas'][$scope][$token] = [
            'hash' => hash_hmac('sha256', (string) $answer, $secret),
            'secret' => $secret,
            'expires_at' => time() + 900,
            'attempts' => 0,
        ];

        // Keep session storage bounded when a page is repeatedly refreshed.
        if (count($_SESSION['_public_captchas'][$scope]) > 5) {
            $_SESSION['_public_captchas'][$scope] = array_slice(
                $_SESSION['_public_captchas'][$scope],
                -5,
                null,
                true
            );
        }

        $id = 'captcha-' . $scope;
        return '<div class="public-captcha rounded-xl border border-slate-200 bg-white p-4">'
            . '<label for="' . self::escape($id) . '" class="block text-sm font-semibold text-slate-700">'
            . 'Verifikasi keamanan <span class="text-red-600" aria-hidden="true">*</span>'
            . '</label>'
            . '<p class="mt-1 text-sm text-slate-600">Berapa hasil dari <strong>'
            . $left . ' + ' . $right . '</strong>?</p>'
            . '<input type="hidden" name="captcha_token" value="' . self::escape($token) . '">'
            . '<input id="' . self::escape($id) . '" type="number" name="captcha_answer" required '
            . 'inputmode="numeric" autocomplete="off" min="0" max="99" '
            . 'class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-2.5" '
            . 'placeholder="Masukkan hasil perhitungan">'
            . '</div>';
    }

    /**
     * Validate a public CAPTCHA. Challenges that reach five failed attempts
     * are removed. Controllers consume a valid challenge immediately before
     * persisting data, so ordinary validation errors do not force users to
     * refill a long form.
     */
    public static function validatePublicCaptcha(string $scope, ?string $token, mixed $answer): bool
    {
        $scope = preg_replace('/[^a-z0-9_-]/i', '', $scope) ?: 'public';
        self::prunePublicCaptchas($scope);

        if (
            !is_string($token)
            || !preg_match('/^[a-f0-9]{32}$/', $token)
            || !preg_match('/^\s*\d{1,2}\s*$/', (string) $answer)
        ) {
            return false;
        }

        $challenge = $_SESSION['_public_captchas'][$scope][$token] ?? null;
        if (!is_array($challenge) || empty($challenge['hash']) || empty($challenge['secret'])) {
            return false;
        }

        $answerHash = hash_hmac('sha256', trim((string) $answer), (string) $challenge['secret']);
        $valid = hash_equals((string) $challenge['hash'], $answerHash);

        if (!$valid) {
            $_SESSION['_public_captchas'][$scope][$token]['attempts'] =
                (int) ($challenge['attempts'] ?? 0) + 1;
        }

        if (!$valid && $_SESSION['_public_captchas'][$scope][$token]['attempts'] >= 5) {
            unset($_SESSION['_public_captchas'][$scope][$token]);
        }

        return $valid;
    }

    public static function consumePublicCaptcha(string $scope, ?string $token): void
    {
        $scope = preg_replace('/[^a-z0-9_-]/i', '', $scope) ?: 'public';
        if (is_string($token) && preg_match('/^[a-f0-9]{32}$/', $token)) {
            unset($_SESSION['_public_captchas'][$scope][$token]);
        }
    }

    private static function prunePublicCaptchas(string $scope): void
    {
        if (!isset($_SESSION['_public_captchas'][$scope])
            || !is_array($_SESSION['_public_captchas'][$scope])) {
            $_SESSION['_public_captchas'][$scope] = [];
            return;
        }

        $now = time();
        foreach ($_SESSION['_public_captchas'][$scope] as $token => $challenge) {
            if (!is_array($challenge) || (int) ($challenge['expires_at'] ?? 0) < $now) {
                unset($_SESSION['_public_captchas'][$scope][$token]);
            }
        }
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
     * Sanitize trusted-format rich text before storing or rendering it.
     */
    public static function sanitizeHtml(string $html): string
    {
        if ($html === '') {
            return '';
        }

        $allowedTags = [
            'a', 'abbr', 'b', 'blockquote', 'br', 'caption', 'code', 'col', 'colgroup',
            'div', 'em', 'figcaption', 'figure', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'hr', 'i', 'iframe', 'img', 'li', 'ol', 'p', 'pre', 's', 'span', 'strong',
            'sub', 'sup', 'table', 'tbody', 'td', 'tfoot', 'th', 'thead', 'tr', 'u', 'ul',
        ];
        $allowedAttributes = [
            'alt', 'class', 'colspan', 'height', 'href', 'loading', 'rel', 'rowspan',
            'src', 'target', 'title', 'width',
        ];

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="UTF-8"><div id="__sanitize_root__">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementById('__sanitize_root__');
        if (!$root) {
            return '';
        }

        self::sanitizeHtmlNode($root, $allowedTags, $allowedAttributes);

        $output = '';
        foreach ($root->childNodes as $child) {
            $output .= $document->saveHTML($child);
        }

        return trim($output);
    }

    private static function sanitizeHtmlNode(DOMNode $node, array $allowedTags, array $allowedAttributes): void
    {
        for ($child = $node->firstChild; $child !== null; ) {
            $next = $child->nextSibling;

            if ($child instanceof DOMElement) {
                $tag = strtolower($child->tagName);
                if (!in_array($tag, $allowedTags, true)) {
                    self::removeElementKeepText($child);
                    $child = $next;
                    continue;
                }

                self::sanitizeHtmlAttributes($child, $allowedAttributes);
                self::sanitizeHtmlNode($child, $allowedTags, $allowedAttributes);
            }

            $child = $next;
        }
    }

    private static function sanitizeHtmlAttributes(DOMElement $element, array $allowedAttributes): void
    {
        $tag = strtolower($element->tagName);
        $remove = [];

        foreach ($element->attributes as $attribute) {
            $name = strtolower($attribute->name);
            $value = trim($attribute->value);

            if (str_starts_with($name, 'on') || !in_array($name, $allowedAttributes, true)) {
                $remove[] = $attribute->name;
                continue;
            }

            if (in_array($name, ['href', 'src'], true) && !self::isSafeHtmlUrl($value)) {
                $remove[] = $attribute->name;
            }
        }

        foreach ($remove as $name) {
            $element->removeAttribute($name);
        }

        if ($tag === 'iframe' && !self::isAllowedIframeSrc($element->getAttribute('src'))) {
            $element->parentNode?->removeChild($element);
            return;
        }

        if ($tag === 'a' && $element->getAttribute('target') === '_blank') {
            $element->setAttribute('rel', 'noopener noreferrer');
        }

        if ($tag === 'img' && !$element->hasAttribute('alt')) {
            $element->setAttribute('alt', '');
        }
    }

    private static function removeElementKeepText(DOMElement $element): void
    {
        $parent = $element->parentNode;
        if (!$parent) {
            return;
        }

        while ($element->firstChild) {
            $parent->insertBefore($element->firstChild, $element);
        }
        $parent->removeChild($element);
    }

    private static function isSafeHtmlUrl(string $url): bool
    {
        if ($url === '' || str_starts_with($url, '#') || str_starts_with($url, '/')) {
            return true;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        return in_array($scheme, ['http', 'https', 'mailto', 'tel'], true);
    }

    private static function isAllowedIframeSrc(string $url): bool
    {
        $path = (string) (parse_url($url, PHP_URL_PATH) ?? '');
        return str_starts_with($path, '/storage/uploads/news/')
            && strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'pdf';
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

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name'] ?? '');

        // Batas gambar adalah invariant global. Parameter yang lebih besar hanya
        // boleh digunakan untuk tipe non-gambar seperti PDF editor.
        $effectiveMaxSize = is_string($mimeType) && str_starts_with($mimeType, 'image/')
            ? min($maxSize, UPLOAD_MAX_SIZE)
            : $maxSize;
        if (($file['size'] ?? 0) > $effectiveMaxSize) {
            $errors[] = 'Ukuran file melebihi batas ' . self::formatBytes($effectiveMaxSize) . '.';
        }

        if (!$mimeType) {
            $errors[] = 'Tipe file tidak dapat dibaca.';
        } elseif (!in_array($mimeType, $allowedTypes, true)) {
            $errors[] = 'Tipe file tidak didukung (' . $mimeType . '). Gunakan ' . self::allowedUploadLabel($allowedTypes) . '.';
        } elseif (str_starts_with($mimeType, 'image/')) {
            $dimensions = @getimagesize($file['tmp_name'] ?? '');
            if ($dimensions === false) {
                $errors[] = 'Berkas gambar tidak valid atau rusak.';
            } else {
                $width = (int) ($dimensions[0] ?? 0);
                $height = (int) ($dimensions[1] ?? 0);
                if ($width < 1 || $height < 1 || ($width * $height) > 16000000) {
                    $errors[] = 'Dimensi gambar terlalu besar. Maksimal 16 megapiksel.';
                }
            }
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

    public static function formatBytes(int $bytes): string
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
