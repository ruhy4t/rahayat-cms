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
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = 'users'");
            $stmt->execute([DB_NAME]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (\Throwable) {
            return false;
        }
    }

    private function install(): void
    {
        $appUrl = rtrim((string) $this->postSafe('app_url'), '/');
        $requiredSuffix = $this->postSafe('required_domain_suffix') ?: '.sch.id';
        $dbHost = $this->postSafe('db_host') ?: 'localhost';
        $dbPort = (int) $this->post('db_port', 3306);
        $dbName = $this->postSafe('db_name');
        $dbUser = $this->postSafe('db_user');
        $dbPass = (string) $this->post('db_pass', '');
        $schoolName = $this->postSafe('school_name');
        $schoolEmail = $this->postSafe('school_email');
        $adminName = $this->postSafe('admin_name') ?: 'Administrator';
        $adminUsername = $this->postSafe('admin_username');
        $adminEmail = $this->postSafe('admin_email');
        $adminPassword = (string) $this->post('admin_password', '');
        $adminConfirm = (string) $this->post('admin_password_confirm', '');

        if (!$appUrl || !$dbName || !$dbUser || !$schoolName || !$schoolEmail || !$adminUsername || !$adminEmail || !$adminPassword) {
            throw new RuntimeException('Semua field wajib harus diisi.');
        }
        $host = strtolower((string) (parse_url($appUrl, PHP_URL_HOST) ?: ''));
        $normalizedSuffix = str_starts_with($requiredSuffix, '.') ? strtolower($requiredSuffix) : '.' . strtolower($requiredSuffix);
        $localHosts = ['localhost', '127.0.0.1', '::1'];
        $isLocal = in_array($host, $localHosts, true) || str_ends_with($host, '.test') || str_ends_with($host, '.local');
        if (!$isLocal && !str_ends_with($host, $normalizedSuffix)) {
            throw new RuntimeException('Domain website harus berakhiran ' . $requiredSuffix . '.');
        }
        if ($adminPassword !== $adminConfirm) {
            throw new RuntimeException('Konfirmasi password admin tidak cocok.');
        }
        if (strlen($adminPassword) < 8) {
            throw new RuntimeException('Password admin minimal 8 karakter.');
        }

        $pdo = $this->connectInstallerDatabase($dbHost, $dbPort, $dbName, $dbUser, $dbPass);

        $this->runSchema($pdo, $dbName);
        $this->createAdmin($pdo, $adminUsername, $adminEmail, $adminPassword, $adminName);
        $this->saveInitialSettings($pdo, $schoolName, $schoolEmail, $appUrl);
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

    private function runSchema(PDO $pdo, string $dbName): void
    {
        $schema = file_get_contents(ROOT_PATH . '/database/schema.sql');
        if ($schema === false) {
            throw new RuntimeException('File schema database tidak ditemukan.');
        }

        $schema = preg_replace('/CREATE DATABASE IF NOT EXISTS.*?;/is', '', $schema) ?? $schema;
        $schema = preg_replace('/USE\s+`?[\w-]+`?\s*;/i', 'USE `' . str_replace('`', '``', $dbName) . '`;', $schema) ?? $schema;
        $pdo->exec($schema);
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
        $stmt = $pdo->prepare("UPDATE school_profile SET name = ?, email = ?, website = ? WHERE id = 1");
        $stmt->execute([$schoolName, $schoolEmail, $appUrl]);

        $stmt = $pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = 'site_title'");
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
}
