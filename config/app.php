<?php
/**
 * ============================================
 * Application Configuration
 * ============================================
 */

declare(strict_types=1);

// Application Settings
define('APP_NAME', getenv('APP_NAME') ?: 'SchoolWeb CMS');
define('APP_VERSION', '1.0.0');
define('APP_DEBUG', filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOLEAN));
$appUrl = getenv('APP_URL') ?: '';
if (!$appUrl && !empty($_SERVER['HTTP_HOST'])) {
    $scheme = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')) ? 'https' : 'http';
    $appUrl = $scheme . '://' . $_SERVER['HTTP_HOST'];
}
define('APP_URL', rtrim($appUrl, '/'));

// Online deployments are restricted to Indonesian school domains.
define('REQUIRED_DOMAIN_SUFFIX', getenv('REQUIRED_DOMAIN_SUFFIX') ?: '.sch.id');

// Default Controller and Action
define('DEFAULT_CONTROLLER', 'Home');
define('DEFAULT_ACTION', 'index');

// Security Settings
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_EXPIRY', 3600); // 1 hour

// Password Hashing (PHP 8.3+ Argon2ID)
define('PASSWORD_ALGO', PASSWORD_ARGON2ID);
define('PASSWORD_OPTIONS', [
    'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
    'time_cost' => PASSWORD_ARGON2_DEFAULT_TIME_COST,
    'threads' => PASSWORD_ARGON2_DEFAULT_THREADS,
]);

// Upload Settings
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// Watermark Settings
define('WATERMARK_ENABLED', true);
define('WATERMARK_OPACITY', 12);       // 0 = transparent, 100 = opaque
define('WATERMARK_MIN_SIZE', 200);     // Minimum image dimension (px) to apply watermark

// Pagination
define('ITEMS_PER_PAGE', 10);

// Date & Time
define('APP_TIMEZONE', 'Asia/Jakarta');
date_default_timezone_set(APP_TIMEZONE);

// School Profile Defaults
define('SCHOOL_NAME', 'Sekolah Rahayat');
define('SCHOOL_ADDRESS', 'Jl. Pendidikan No. 1');
define('SCHOOL_PHONE', '(021) 123-4567');
define('SCHOOL_EMAIL', 'info@rahayat.sch.id');
