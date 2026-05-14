<?php
/**
 * ============================================
 * SchoolWeb CMS - Single Entry Point
 * ============================================
 * PHP 8.3 Native MVC Framework
 */

declare(strict_types=1);

// Define base paths
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('VIEW_PATH', ROOT_PATH . '/views');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('PUBLIC_PATH', __DIR__);

// Load application configuration before bootstrapping runtime behavior.
require_once CONFIG_PATH . '/app.php';
require_once CONFIG_PATH . '/database.php';

function isInstallRequest(): bool
{
    $url = trim((string) ($_GET['url'] ?? ''), '/');
    return $url === 'install' || str_starts_with($url, 'install/');
}

function isInstalled(): bool
{
    if (file_exists(CONFIG_PATH . '/local.php') || filter_var(getenv('APP_INSTALLED') ?: false, FILTER_VALIDATE_BOOLEAN)) {
        return true;
    }

    try {
        $pdo = new PDO(
            sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', DB_HOST, DB_PORT, DB_NAME, DB_CHARSET),
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        $requiredTables = ['users', 'school_profile', 'site_settings', 'menus'];
        foreach ($requiredTables as $table) {
            if (!tableExists($pdo, $table)) {
                return false;
            }
        }

        $requiredColumns = [
            ['menus', 'menu_location'],
            ['menus', 'target'],
            ['menus', 'is_active'],
            ['school_profile', 'welcome_message'],
            ['school_profile', 'spmb_link'],
            ['school_profile', 'monday_open'],
            ['users', 'is_spmb_committee'],
            ['users', 'permissions'],
            ['site_settings', 'setting_key'],
            ['site_settings', 'setting_value'],
        ];

        foreach ($requiredColumns as [$table, $column]) {
            if (!columnExists($pdo, $table, $column)) {
                return false;
            }
        }

        $hasAdmin = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn() > 0;
        $hasProfile = (int) $pdo->query('SELECT COUNT(*) FROM school_profile')->fetchColumn() > 0;
        $hasMenu = (int) $pdo->query('SELECT COUNT(*) FROM menus')->fetchColumn() > 0;

        return $hasAdmin && $hasProfile && $hasMenu;
    } catch (\Throwable) {
        return false;
    }
}

function tableExists(PDO $pdo, string $table): bool
{
    try {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?');
        $stmt->execute([$table]);
        return (int) $stmt->fetchColumn() > 0;
    } catch (\Throwable) {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            return false;
        }
        $stmt = $pdo->query('SHOW TABLES LIKE ' . $pdo->quote($table));
        return (bool) $stmt->fetchColumn();
    }
}

function columnExists(PDO $pdo, string $table, string $column): bool
{
    try {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?');
        $stmt->execute([$table, $column]);
        return (int) $stmt->fetchColumn() > 0;
    } catch (\Throwable) {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            return false;
        }
        $stmt = $pdo->query("SHOW COLUMNS FROM `{$table}` LIKE " . $pdo->quote($column));
        return (bool) $stmt->fetchColumn();
    }
}

function isAllowedApplicationHost(): bool
{
    if (PHP_SAPI === 'cli') {
        return true;
    }

    $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
    $host = preg_replace('/:\d+$/', '', $host);
    $host = trim($host, '[]');

    if ($host === '') {
        return false;
    }

    $localHosts = ['localhost', '127.0.0.1', '::1'];
    if (in_array($host, $localHosts, true) || str_ends_with($host, '.test') || str_ends_with($host, '.local')) {
        return true;
    }

    $requiredSuffix = strtolower((string) REQUIRED_DOMAIN_SUFFIX);
    $requiredSuffix = str_starts_with($requiredSuffix, '.') ? $requiredSuffix : '.' . $requiredSuffix;

    return str_ends_with($host, $requiredSuffix);
}

if (!isAllowedApplicationHost()) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Rahayat CMS hanya dapat digunakan pada domain resmi sekolah Indonesia dengan akhiran .sch.id.');
}

if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    ini_set('display_errors', '0');
}

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
if ($isHttps) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

ini_set('session.use_strict_mode', '1');

session_start([
    'cookie_secure' => $isHttps,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
]);

// Regenerate session ID periodically for security
if (!isset($_SESSION['_created'])) {
    $_SESSION['_created'] = time();
} elseif (time() - $_SESSION['_created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['_created'] = time();
}

if (!isInstalled() && !isInstallRequest()) {
    header('Location: /install');
    exit;
}

// Serve storage files through the app so private folders can be protected.
if (strpos($_GET['url'] ?? '', 'storage/') === 0) {
    $file = substr($_GET['url'], 8); // Remove 'storage/' prefix

    // Security: prevent directory traversal
    if (strpos($file, '..') === false && strpos($file, '~') === false && !str_starts_with($file, '/')) {
        $normalizedFile = str_replace('\\', '/', $file);
        if (str_starts_with($normalizedFile, 'spmb/') && empty($_SESSION['user_id'])) {
            http_response_code(403);
            exit('Forbidden');
        }

        $storagePath = dirname(__DIR__) . '/storage/' . $file;
        $realPath = realpath($storagePath);
        $storageDirReal = realpath(dirname(__DIR__) . '/storage');

        if ($realPath && strpos($realPath, $storageDirReal) === 0 && file_exists($realPath)) {
            // Determine content type
            $ext = strtolower(pathinfo($realPath, PATHINFO_EXTENSION));
            $types = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'svg' => 'image/svg+xml',
                'pdf' => 'application/pdf',
                'mp4' => 'video/mp4',
                'webm' => 'video/webm',
            ];
            if (!isset($types[$ext])) {
                http_response_code(404);
                exit('File not found');
            }
            $contentType = $types[$ext];

            // Set headers
            header('Cache-Control: public, max-age=2592000');
            if (str_starts_with($normalizedFile, 'spmb/')) {
                header('Cache-Control: private, no-store');
            }
            header('Content-Type: ' . $contentType);
            header('Content-Length: ' . filesize($realPath));

            // Stream file
            readfile($realPath);
            exit;
        }
    }

    http_response_code(404);
    exit('File not found');
}

// Setup autoloader
spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . '/Core/',
        APP_PATH . '/Controllers/',
        APP_PATH . '/Models/',
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});


// Load Security class for helper functions (e.g., e() for XSS filtering)
require_once APP_PATH . '/Core/Security.php';

if (isInstalled() && !isInstallRequest()) {
    try {
        SchemaRepairer::repair();
    } catch (\Throwable $e) {
        error_log('Schema repair failed: ' . $e->getMessage());
    }
}

// Initialize and run application
try {
    $app = new App();
    $app->run();
} catch (\Throwable $e) {
    // Log error
    error_log($e->getMessage());

    // Show error page in development, generic message in production
    if (defined('APP_DEBUG') && APP_DEBUG) {
        echo '<h1>Error</h1>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        http_response_code(500);
        echo '<h1>500 - Internal Server Error</h1>';
    }
}
