<?php
/**
 * ============================================
 * Install Controller
 * ============================================
 */

declare(strict_types=1);

class InstallController extends Controller
{
    private string $localConfigPath;

    public function __construct()
    {
        $this->localConfigPath = CONFIG_PATH . '/local.php';
    }

    public function index(): void
    {
        if ($this->isInstalled()) {
            $this->view('install.locked', ['title' => 'Installer Terkunci']);
            return;
        }

        $errors = [];
        $success = false;

        if (Security::isPost()) {
            $this->requireCsrf();

            try {
                $this->install();
                $success = true;
            } catch (\Throwable $e) {
                $errors[] = APP_DEBUG ? $e->getMessage() : 'Instalasi gagal. Periksa data database dan izin tulis folder config.';
                error_log('Install failed: ' . $e->getMessage());
            }
        }

        $this->view('install.index', [
            'title' => 'Install Rahayat CMS',
            'errors' => $errors,
            'success' => $success,
            'defaults' => [
                'app_url' => APP_URL,
                'db_host' => 'localhost',
                'db_port' => '3306',
                'db_name' => 'schoolweb_db',
                'db_user' => 'schoolweb_user',
            ],
        ]);
    }

    private function isInstalled(): bool
    {
        if (file_exists($this->localConfigPath) || filter_var(getenv('APP_INSTALLED') ?: false, FILTER_VALIDATE_BOOLEAN)) {
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

            return $this->databaseLooksInstalled($pdo);
        } catch (\Throwable) {
            return false;
        }
    }

    private function install(): void
    {
        $appUrl = rtrim($this->postTrim('app_url'), '/');
        $requiredSuffix = $this->normalizeDomainSuffix($this->postTrim('required_domain_suffix', '.sch.id'));
        $dbHost = $this->postTrim('db_host', 'localhost');
        $dbPort = (int) $this->postTrim('db_port', '3306');
        $dbName = $this->postTrim('db_name');
        $dbUser = $this->postTrim('db_user');
        $dbPass = (string) $this->post('db_pass', '');
        $schoolName = $this->postTrim('school_name');
        $schoolEmail = $this->postTrim('school_email');
        $adminName = $this->postTrim('admin_name', 'Administrator');
        $adminUsername = $this->postTrim('admin_username');
        $adminEmail = $this->postTrim('admin_email');
        $adminPassword = (string) $this->post('admin_password', '');
        $adminConfirm = (string) $this->post('admin_password_confirm', '');

        if (
            $appUrl === '' || $dbName === '' || $dbUser === '' || $schoolName === '' || $schoolEmail === ''
            || $adminUsername === '' || $adminEmail === '' || $adminPassword === ''
        ) {
            throw new RuntimeException('Semua field wajib harus diisi.');
        }

        if (!filter_var($appUrl, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $appUrl)) {
            throw new RuntimeException('URL website tidak valid. Gunakan format http(s)://domain.');
        }

        if (!filter_var($schoolEmail, FILTER_VALIDATE_EMAIL) || !filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Email sekolah/admin tidak valid.');
        }

        if ($dbPort < 1 || $dbPort > 65535) {
            throw new RuntimeException('Port database tidak valid.');
        }

        $host = strtolower((string) (parse_url($appUrl, PHP_URL_HOST) ?: ''));
        $localHosts = ['localhost', '127.0.0.1', '::1'];
        $isLocal = in_array($host, $localHosts, true) || str_ends_with($host, '.test') || str_ends_with($host, '.local');
        if (!$isLocal && !str_ends_with($host, strtolower($requiredSuffix))) {
            throw new RuntimeException('Domain website harus berakhiran ' . $requiredSuffix . '.');
        }

        if ($adminPassword !== $adminConfirm) {
            throw new RuntimeException('Konfirmasi password admin tidak cocok.');
        }
        if (strlen($adminPassword) < 8) {
            throw new RuntimeException('Password admin minimal 8 karakter.');
        }

        $pdo = $this->connectInstallerDatabase($dbHost, $dbPort, $dbName, $dbUser, $dbPass);

        $this->runSchema($pdo);
        $this->createAdmin($pdo, $adminUsername, $adminEmail, $adminPassword, $adminName);
        $this->saveInitialSettings($pdo, $schoolName, $schoolEmail, $appUrl);

        if (!$this->databaseLooksInstalled($pdo)) {
            throw new RuntimeException('Instalasi belum lengkap: beberapa tabel/kolom inti tidak terbentuk.');
        }

        $this->writeLocalConfig([
            'APP_INSTALLED' => 'true',
            'APP_URL' => $appUrl,
            'APP_DEBUG' => 'false',
            'REQUIRED_DOMAIN_SUFFIX' => $requiredSuffix,
            'UPDATE_ENABLED' => 'false',
            'UPDATE_BRANCH' => 'main',
            'DB_HOST' => $dbHost,
            'DB_PORT' => (string) $dbPort,
            'DB_NAME' => $dbName,
            'DB_USER' => $dbUser,
            'DB_PASS' => $dbPass,
            'DB_CHARSET' => 'utf8mb4',
        ]);
    }

    private function connectInstallerDatabase(string $dbHost, int $dbPort, string $dbName, string $dbUser, string $dbPass): PDO
    {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        if (defined('PDO::MYSQL_ATTR_MULTI_STATEMENTS')) {
            $options[PDO::MYSQL_ATTR_MULTI_STATEMENTS] = true;
        }

        $quotedDb = '`' . str_replace('`', '``', $dbName) . '`';

        try {
            $pdo = new PDO(
                sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $dbHost, $dbPort),
                $dbUser,
                $dbPass,
                $options
            );

            try {
                $pdo->exec("CREATE DATABASE IF NOT EXISTS {$quotedDb} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            } catch (\Throwable $e) {
                error_log('Installer skipped CREATE DATABASE: ' . $e->getMessage());
            }

            $pdo->exec("USE {$quotedDb}");
            return $pdo;
        } catch (\Throwable $e) {
            error_log('Installer server-level DB connection failed: ' . $e->getMessage());
        }

        return new PDO(
            sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $dbHost, $dbPort, $dbName),
            $dbUser,
            $dbPass,
            $options
        );
    }

    private function runSchema(PDO $pdo): void
    {
        $schema = file_get_contents(ROOT_PATH . '/database/schema.sql');
        if ($schema === false) {
            throw new RuntimeException('File schema database tidak ditemukan.');
        }

        if (!$this->supportsNativeJson($pdo)) {
            $schema = preg_replace('/\bdocuments\s+JSON\b/i', 'documents LONGTEXT', $schema) ?? $schema;
        }

        $statements = $this->splitSqlStatements($schema);
        foreach ($statements as $index => $statement) {
            $sql = trim($statement);
            if ($sql === '') {
                continue;
            }

            if (preg_match('/^\s*CREATE\s+DATABASE\b/i', $sql) || preg_match('/^\s*USE\b/i', $sql)) {
                continue;
            }

            if (preg_match('/^\s*INSERT\s+INTO\b/i', $sql)) {
                $sql = preg_replace('/^\s*INSERT\s+INTO\b/i', 'INSERT IGNORE INTO', $sql, 1) ?? $sql;
            }

            try {
                $pdo->exec($sql);
            } catch (\Throwable $e) {
                throw new RuntimeException(
                    'Schema gagal dieksekusi pada statement #' . ($index + 1) . ': ' . $e->getMessage()
                );
            }
        }
    }

    private function createAdmin(PDO $pdo, string $username, string $email, string $password, string $name): void
    {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE role = 'admin' ORDER BY id ASC LIMIT 1");
        $stmt->execute();
        $adminId = $stmt->fetchColumn();

        if ($adminId) {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ?, name = ?, role = 'admin', is_active = 1, updated_at = NOW() WHERE id = ?");
            $stmt->execute([
                $username,
                $email,
                Security::hashPassword($password),
                $name,
                $adminId,
            ]);
            return;
        }

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, name, role, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, 'admin', 1, NOW(), NOW())");
        $stmt->execute([$username, $email, Security::hashPassword($password), $name]);
    }

    private function saveInitialSettings(PDO $pdo, string $schoolName, string $schoolEmail, string $appUrl): void
    {
        $pdo->exec("INSERT INTO school_profile (name, email, website) SELECT 'Sekolah Rahayat', 'info@rahayat.sch.id', '' WHERE NOT EXISTS (SELECT 1 FROM school_profile LIMIT 1)");

        $stmt = $pdo->prepare('UPDATE school_profile SET name = ?, email = ?, website = ? ORDER BY id ASC LIMIT 1');
        $stmt->execute([$schoolName, $schoolEmail, $appUrl]);

        $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES ('site_title', ?, 'text', 'Website title') ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        $stmt->execute([$schoolName]);
    }

    private function writeLocalConfig(array $values): void
    {
        if (!is_writable(CONFIG_PATH)) {
            throw new RuntimeException('Folder config tidak dapat ditulis.');
        }

        $export = "<?php\n\ndeclare(strict_types=1);\n\nreturn " . var_export($values, true) . ";\n";
        if (file_put_contents($this->localConfigPath, $export, LOCK_EX) === false) {
            throw new RuntimeException('Gagal menulis config/local.php.');
        }
    }

    private function databaseLooksInstalled(PDO $pdo): bool
    {
        $requiredTables = ['users', 'school_profile', 'site_settings', 'menus'];
        foreach ($requiredTables as $table) {
            if (!$this->tableExists($pdo, $table)) {
                return false;
            }
        }

        $requiredColumns = [
            'menus' => ['menu_location', 'target', 'is_active', 'sort_order', 'parent_id', 'icon'],
            'school_profile' => ['tagline', 'spmb_link', 'welcome_message', 'principal_quote', 'monday_open', 'monday_close', 'is_closed_monday', 'watermark_enabled'],
            'users' => ['role', 'is_spmb_committee', 'permissions'],
            'site_settings' => ['setting_key', 'setting_value', 'setting_type'],
        ];

        foreach ($requiredColumns as $table => $columns) {
            foreach ($columns as $column) {
                if (!$this->columnExists($pdo, $table, $column)) {
                    return false;
                }
            }
        }

        try {
            $hasAdmin = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn() > 0;
            $hasProfile = (int) $pdo->query('SELECT COUNT(*) FROM school_profile')->fetchColumn() > 0;
            $hasMenu = (int) $pdo->query('SELECT COUNT(*) FROM menus')->fetchColumn() > 0;

            return $hasAdmin && $hasProfile && $hasMenu;
        } catch (\Throwable) {
            return false;
        }
    }

    private function tableExists(PDO $pdo, string $table): bool
    {
        try {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?');
            $stmt->execute([$table]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (\Throwable) {
            if (!$this->isSafeIdentifier($table)) {
                return false;
            }
            $stmt = $pdo->query('SHOW TABLES LIKE ' . $pdo->quote($table));
            return (bool) $stmt->fetchColumn();
        }
    }

    private function columnExists(PDO $pdo, string $table, string $column): bool
    {
        try {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?');
            $stmt->execute([$table, $column]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (\Throwable) {
            if (!$this->isSafeIdentifier($table)) {
                return false;
            }
            $stmt = $pdo->query("SHOW COLUMNS FROM `{$table}` LIKE " . $pdo->quote($column));
            return (bool) $stmt->fetchColumn();
        }
    }

    private function supportsNativeJson(PDO $pdo): bool
    {
        try {
            $pdo->exec('CREATE TEMPORARY TABLE __json_capability (payload JSON NULL)');
            $pdo->exec('DROP TEMPORARY TABLE IF EXISTS __json_capability');
            return true;
        } catch (\Throwable) {
            try {
                $pdo->exec('DROP TEMPORARY TABLE IF EXISTS __json_capability');
            } catch (\Throwable) {
            }
            return false;
        }
    }

    private function splitSqlStatements(string $sql): array
    {
        $statements = [];
        $buffer = '';
        $inSingle = false;
        $inDouble = false;
        $inBacktick = false;
        $inLineComment = false;
        $inBlockComment = false;
        $length = strlen($sql);

        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];
            $next = $i + 1 < $length ? $sql[$i + 1] : '';

            if ($inLineComment) {
                if ($char === "\n") {
                    $inLineComment = false;
                    $buffer .= $char;
                }
                continue;
            }

            if ($inBlockComment) {
                if ($char === '*' && $next === '/') {
                    $inBlockComment = false;
                    $i++;
                }
                continue;
            }

            if (!$inSingle && !$inDouble && !$inBacktick) {
                if ($char === '-' && $next === '-') {
                    $inLineComment = true;
                    $i++;
                    continue;
                }
                if ($char === '#') {
                    $inLineComment = true;
                    continue;
                }
                if ($char === '/' && $next === '*') {
                    $inBlockComment = true;
                    $i++;
                    continue;
                }
            }

            if ($char === "'" && !$inDouble && !$inBacktick) {
                $prev = $i > 0 ? $sql[$i - 1] : '';
                if ($prev !== '\\') {
                    $inSingle = !$inSingle;
                }
            } elseif ($char === '"' && !$inSingle && !$inBacktick) {
                $prev = $i > 0 ? $sql[$i - 1] : '';
                if ($prev !== '\\') {
                    $inDouble = !$inDouble;
                }
            } elseif ($char === '`' && !$inSingle && !$inDouble) {
                $inBacktick = !$inBacktick;
            }

            if ($char === ';' && !$inSingle && !$inDouble && !$inBacktick) {
                $statements[] = $buffer;
                $buffer = '';
                continue;
            }

            $buffer .= $char;
        }

        if (trim($buffer) !== '') {
            $statements[] = $buffer;
        }

        return $statements;
    }

    private function postTrim(string $key, string $default = ''): string
    {
        $value = $this->post($key, $default);
        if ($value === null) {
            return $default;
        }
        return trim((string) $value);
    }

    private function normalizeDomainSuffix(string $suffix): string
    {
        $suffix = trim(strtolower($suffix));
        if ($suffix === '') {
            return '.sch.id';
        }
        return str_starts_with($suffix, '.') ? $suffix : '.' . $suffix;
    }

    private function isSafeIdentifier(string $value): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9_]+$/', $value);
    }
}
