<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('VIEW_PATH', ROOT_PATH . '/views');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('PUBLIC_PATH', ROOT_PATH . '/public');
require CONFIG_PATH . '/app.php';
require CONFIG_PATH . '/database.php';
spl_autoload_register(static function (string $class): void {
    foreach (['Core', 'Models', 'Controllers'] as $folder) {
        $path = APP_PATH . '/' . $folder . '/' . $class . '.php';
        if (is_file($path)) { require_once $path; return; }
    }
});
