<?php
/**
 * ============================================
 * Database Schema Repairer
 * ============================================
 */

declare(strict_types=1);

class SchemaRepairer
{
    private static bool $checked = false;
    private PDO $pdo;

    private function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public static function repair(): void
    {
        if (self::$checked) {
            return;
        }

        self::$checked = true;
        (new self())->run();
    }

    private function run(): void
    {
        $this->ensureCoreTables();

        // Only skip if version matches AND the database actually has all
        // required columns and default data.  A partially-imported hosting
        // database may have the version flag set while columns are missing.
        if ($this->schemaVersion() === APP_VERSION && $this->hasRequiredSchema()) {
            return;
        }

        $this->ensureMenusColumns();
        $this->ensureSiteSettingsColumns();
        $this->ensureUsersColumns();
        $this->ensureSchoolProfileColumns();
        $this->ensureNewsColumns();
        $this->ensureGalleryTablesAndColumns();
        $this->ensureDefaultProfile();
        $this->ensureDefaultMenus();
        $this->ensureDefaultSettings();

        if ($this->hasRequiredSchema()) {
            $this->setSchemaVersion(APP_VERSION);
        }
    }

    private function ensureCoreTables(): void
    {
        $this->safeExec("CREATE TABLE IF NOT EXISTS school_profile (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            tagline VARCHAR(255) NULL,
            npsn VARCHAR(20) NULL,
            address TEXT NULL,
            phone VARCHAR(50) NULL,
            email VARCHAR(100) NULL,
            website VARCHAR(255) NULL,
            principal_name VARCHAR(100) NULL,
            principal_nip VARCHAR(50) NULL,
            principal_photo VARCHAR(255) NULL,
            logo VARCHAR(255) NULL,
            vision TEXT NULL,
            mission TEXT NULL,
            motto VARCHAR(255) NULL,
            history TEXT NULL,
            welcome_message LONGTEXT NULL,
            principal_quote TEXT NULL,
            accreditation VARCHAR(10) NULL,
            school_type ENUM('negeri', 'swasta') DEFAULT 'negeri',
            spmb_link VARCHAR(255) NULL,
            established_year YEAR NULL,
            google_maps_embed TEXT NULL,
            monday_open VARCHAR(5) DEFAULT '07:00',
            monday_close VARCHAR(5) DEFAULT '15:00',
            is_closed_monday TINYINT(1) DEFAULT 0,
            tuesday_open VARCHAR(5) DEFAULT '07:00',
            tuesday_close VARCHAR(5) DEFAULT '15:00',
            is_closed_tuesday TINYINT(1) DEFAULT 0,
            wednesday_open VARCHAR(5) DEFAULT '07:00',
            wednesday_close VARCHAR(5) DEFAULT '15:00',
            is_closed_wednesday TINYINT(1) DEFAULT 0,
            thursday_open VARCHAR(5) DEFAULT '07:00',
            thursday_close VARCHAR(5) DEFAULT '15:00',
            is_closed_thursday TINYINT(1) DEFAULT 0,
            friday_open VARCHAR(5) DEFAULT '07:00',
            friday_close VARCHAR(5) DEFAULT '15:00',
            is_closed_friday TINYINT(1) DEFAULT 0,
            saturday_open VARCHAR(5) DEFAULT '07:00',
            saturday_close VARCHAR(5) DEFAULT '12:00',
            is_closed_saturday TINYINT(1) DEFAULT 0,
            sunday_open VARCHAR(5) DEFAULT '07:00',
            sunday_close VARCHAR(5) DEFAULT '15:00',
            is_closed_sunday TINYINT(1) DEFAULT 1,
            total_students INT DEFAULT 0,
            total_teachers INT DEFAULT 0,
            graduation_rate INT DEFAULT 0,
            watermark_enabled TINYINT(1) DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->safeExec("CREATE TABLE IF NOT EXISTS menus (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(100) NOT NULL,
            url VARCHAR(255) NOT NULL,
            icon VARCHAR(50) NULL,
            parent_id INT UNSIGNED NULL,
            sort_order INT DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            target ENUM('_self', '_blank') DEFAULT '_self',
            menu_location ENUM('header', 'footer', 'both') DEFAULT 'header',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_parent_id (parent_id),
            INDEX idx_is_active (is_active),
            INDEX idx_menu_location (menu_location)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->safeExec("CREATE TABLE IF NOT EXISTS site_settings (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) NOT NULL UNIQUE,
            setting_value TEXT NULL,
            setting_type ENUM('text', 'number', 'boolean', 'json', 'select') DEFAULT 'text',
            description VARCHAR(255) NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_setting_key (setting_key)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->safeExec("CREATE TABLE IF NOT EXISTS site_visits (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            visitor_key CHAR(64) NOT NULL,
            path VARCHAR(255) NOT NULL,
            title VARCHAR(255) NULL,
            content_type VARCHAR(50) NOT NULL DEFAULT 'page',
            content_id INT UNSIGNED NULL,
            visited_on DATE NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_visited_on (visited_on),
            INDEX idx_visitor_day (visitor_key, visited_on),
            INDEX idx_content (content_type, content_id),
            INDEX idx_path (path)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    private function ensureUsersColumns(): void
    {
        if (!$this->tableExists('users')) {
            return;
        }

        $this->addColumn('users', 'editor_id', 'INT UNSIGNED NULL');
        $this->addColumn('users', 'is_spmb_committee', 'TINYINT(1) DEFAULT 0');
        $this->addColumn('users', 'permissions', 'LONGTEXT NULL');
    }

    private function ensureMenusColumns(): void
    {
        if (!$this->tableExists('menus')) {
            return;
        }

        $this->addColumn('menus', 'icon', 'VARCHAR(50) NULL');
        $this->addColumn('menus', 'parent_id', 'INT UNSIGNED NULL');
        $this->addColumn('menus', 'sort_order', 'INT DEFAULT 0');
        $this->addColumn('menus', 'is_active', 'TINYINT(1) DEFAULT 1');
        $this->addColumn('menus', 'target', "ENUM('_self', '_blank') DEFAULT '_self'");
        $this->addColumn('menus', 'menu_location', "ENUM('header', 'footer', 'both') DEFAULT 'header'");
        $this->addColumn('menus', 'created_at', 'DATETIME DEFAULT CURRENT_TIMESTAMP');
        $this->addColumn('menus', 'updated_at', 'DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
    }

    private function ensureSiteSettingsColumns(): void
    {
        if (!$this->tableExists('site_settings')) {
            return;
        }

        $this->addColumn('site_settings', 'setting_value', 'TEXT NULL');
        $this->addColumn('site_settings', 'setting_type', "ENUM('text', 'number', 'boolean', 'json', 'select') DEFAULT 'text'");
        $this->addColumn('site_settings', 'description', 'VARCHAR(255) NULL');
        $this->addColumn('site_settings', 'updated_at', 'DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
    }

    private function ensureSchoolProfileColumns(): void
    {
        if (!$this->tableExists('school_profile')) {
            return;
        }

        $columns = [
            'tagline' => 'VARCHAR(255) NULL',
            'principal_nip' => 'VARCHAR(50) NULL',
            'principal_photo' => 'VARCHAR(255) NULL',
            'google_maps_embed' => 'TEXT NULL',
            'welcome_message' => 'LONGTEXT NULL',
            'principal_quote' => 'TEXT NULL',
            'spmb_link' => 'VARCHAR(255) NULL',
            'monday_open' => "VARCHAR(5) DEFAULT '07:00'",
            'monday_close' => "VARCHAR(5) DEFAULT '15:00'",
            'is_closed_monday' => 'TINYINT(1) DEFAULT 0',
            'tuesday_open' => "VARCHAR(5) DEFAULT '07:00'",
            'tuesday_close' => "VARCHAR(5) DEFAULT '15:00'",
            'is_closed_tuesday' => 'TINYINT(1) DEFAULT 0',
            'wednesday_open' => "VARCHAR(5) DEFAULT '07:00'",
            'wednesday_close' => "VARCHAR(5) DEFAULT '15:00'",
            'is_closed_wednesday' => 'TINYINT(1) DEFAULT 0',
            'thursday_open' => "VARCHAR(5) DEFAULT '07:00'",
            'thursday_close' => "VARCHAR(5) DEFAULT '15:00'",
            'is_closed_thursday' => 'TINYINT(1) DEFAULT 0',
            'friday_open' => "VARCHAR(5) DEFAULT '07:00'",
            'friday_close' => "VARCHAR(5) DEFAULT '15:00'",
            'is_closed_friday' => 'TINYINT(1) DEFAULT 0',
            'saturday_open' => "VARCHAR(5) DEFAULT '07:00'",
            'saturday_close' => "VARCHAR(5) DEFAULT '12:00'",
            'is_closed_saturday' => 'TINYINT(1) DEFAULT 0',
            'sunday_open' => "VARCHAR(5) DEFAULT '07:00'",
            'sunday_close' => "VARCHAR(5) DEFAULT '15:00'",
            'is_closed_sunday' => 'TINYINT(1) DEFAULT 1',
            'total_students' => 'INT DEFAULT 0',
            'total_teachers' => 'INT DEFAULT 0',
            'graduation_rate' => 'INT DEFAULT 0',
            'watermark_enabled' => 'TINYINT(1) DEFAULT 1',
        ];

        foreach ($columns as $column => $definition) {
            $this->addColumn('school_profile', $column, $definition);
        }
    }

    private function ensureNewsColumns(): void
    {
        if (!$this->tableExists('news')) {
            return;
        }

        $columns = [
            'slug' => 'VARCHAR(255) NULL',
            'excerpt' => 'TEXT NULL',
            'category' => "VARCHAR(50) DEFAULT 'umum'",
            'category_id' => 'INT UNSIGNED NULL',
            'status' => "ENUM('draft', 'pending', 'published', 'archived') DEFAULT 'draft'",
            'views' => 'INT UNSIGNED DEFAULT 0',
            'published_at' => 'DATETIME NULL',
            'meta_description' => 'TEXT NULL',
            'meta_keywords' => 'VARCHAR(255) NULL',
        ];

        foreach ($columns as $column => $definition) {
            $this->addColumn('news', $column, $definition);
        }
    }

    private function ensureGalleryTablesAndColumns(): void
    {
        $this->safeExec("CREATE TABLE IF NOT EXISTS gallery_albums (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            description TEXT NULL,
            type ENUM('foto', 'video') DEFAULT 'foto',
            cover_image VARCHAR(255) NULL,
            is_active TINYINT(1) DEFAULT 1,
            sort_order INT DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_slug (slug),
            INDEX idx_is_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $this->safeExec("CREATE TABLE IF NOT EXISTS gallery_items (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            album_id INT UNSIGNED NULL,
            title VARCHAR(255) NULL,
            description TEXT NULL,
            type ENUM('image', 'video') DEFAULT 'image',
            file_path VARCHAR(255) NULL,
            youtube_url VARCHAR(255) NULL,
            youtube_video_id VARCHAR(50) NULL,
            is_active TINYINT(1) DEFAULT 1,
            sort_order INT DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_album_id (album_id),
            INDEX idx_is_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        if ($this->tableExists('gallery_albums')) {
            $columns = [
                'slug' => 'VARCHAR(255) NULL',
                'description' => 'TEXT NULL',
                'type' => "ENUM('foto', 'video') DEFAULT 'foto'",
                'cover_image' => 'VARCHAR(255) NULL',
                'is_active' => 'TINYINT(1) DEFAULT 1',
                'sort_order' => 'INT DEFAULT 0',
                'created_at' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
                'updated_at' => 'DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            ];

            foreach ($columns as $column => $definition) {
                $this->addColumn('gallery_albums', $column, $definition);
            }

            $this->ensureGalleryAlbumSlugs();
        }

        if ($this->tableExists('gallery_items')) {
            $columns = [
                'album_id' => 'INT UNSIGNED NULL',
                'title' => 'VARCHAR(255) NULL',
                'description' => 'TEXT NULL',
                'type' => "ENUM('image', 'video') DEFAULT 'image'",
                'file_path' => 'VARCHAR(255) NULL',
                'youtube_url' => 'VARCHAR(255) NULL',
                'youtube_video_id' => 'VARCHAR(50) NULL',
                'is_active' => 'TINYINT(1) DEFAULT 1',
                'sort_order' => 'INT DEFAULT 0',
                'created_at' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
                'updated_at' => 'DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            ];

            foreach ($columns as $column => $definition) {
                $this->addColumn('gallery_items', $column, $definition);
            }
        }
    }

    private function ensureGalleryAlbumSlugs(): void
    {
        if (!$this->tableExists('gallery_albums') || !$this->columnExists('gallery_albums', 'slug')) {
            return;
        }

        try {
            $stmt = $this->pdo->query("SELECT id, title, slug FROM gallery_albums WHERE slug IS NULL OR slug = ''");
            $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($albums)) {
                return;
            }

            $update = $this->pdo->prepare('UPDATE gallery_albums SET slug = ? WHERE id = ?');
            foreach ($albums as $album) {
                $slug = $this->uniqueGallerySlug((string) ($album['title'] ?? 'album'), (int) $album['id']);
                $update->execute([$slug, (int) $album['id']]);
            }
        } catch (\Throwable $e) {
            error_log('Gallery slug repair failed: ' . $e->getMessage());
        }
    }

    private function uniqueGallerySlug(string $title, int $id): string
    {
        $slug = strtolower(trim((string) preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
        if ($slug === '') {
            $slug = 'album';
        }

        $base = $slug;
        $counter = 1;

        while ($this->gallerySlugExists($slug, $id)) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function gallerySlugExists(string $slug, int $excludeId): bool
    {
        try {
            $stmt = $this->pdo->prepare('SELECT id FROM gallery_albums WHERE slug = ? AND id != ? LIMIT 1');
            $stmt->execute([$slug, $excludeId]);
            return (bool) $stmt->fetchColumn();
        } catch (\Throwable) {
            return false;
        }
    }

    private function ensureDefaultProfile(): void
    {
        if (!$this->tableExists('school_profile')) {
            return;
        }

        $count = (int) $this->pdo->query('SELECT COUNT(*) FROM school_profile')->fetchColumn();
        if ($count > 0) {
            return;
        }

        $stmt = $this->pdo->prepare("INSERT INTO school_profile
            (name, npsn, address, phone, email, website, principal_name, vision, mission, motto, accreditation, school_type, established_year)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            'Sekolah Rahayat',
            '12345678',
            'Jl. Pendidikan No. 1',
            '(022) 123-4567',
            'info@rahayat.sch.id',
            APP_URL ?: 'https://rahayat.sch.id',
            'Kepala Sekolah',
            'Menjadi sekolah unggul yang menghasilkan generasi berkarakter.',
            'Menyelenggarakan pendidikan berkualitas dan membangun karakter peserta didik.',
            'Cerdas, Berkarakter, Berprestasi',
            'A',
            'negeri',
            '2010',
        ]);
    }

    private function ensureDefaultMenus(): void
    {
        if (!$this->tableExists('menus')) {
            return;
        }

        $count = (int) $this->pdo->query('SELECT COUNT(*) FROM menus')->fetchColumn();
        if ($count > 0) {
            return;
        }

        $menus = [
            ['Beranda', '/', 1],
            ['Profil', '/profil', 2],
            ['Berita', '/berita', 3],
            ['Galeri', '/galeri', 4],
            ['Kontak', '/kontak', 5],
        ];
        $stmt = $this->pdo->prepare("INSERT INTO menus (title, url, sort_order, menu_location, is_active, target) VALUES (?, ?, ?, 'header', 1, '_self')");
        foreach ($menus as $menu) {
            $stmt->execute($menu);
        }
    }

    private function ensureDefaultSettings(): void
    {
        if (!$this->tableExists('site_settings')) {
            return;
        }

        $settings = [
            ['theme', 'indigo-modern', 'select', 'Website theme'],
            ['site_title', 'Sekolah Rahayat', 'text', 'Website title'],
            ['meta_description', 'Website resmi Sekolah Rahayat', 'text', 'SEO meta description'],
            ['footer_text', '(c) {year} {school}. All rights reserved.', 'text', 'Footer copyright text'],
            ['spmb_enabled', '0', 'boolean', 'Enable SPMB registration'],
            ['spmb_start_date', null, 'text', 'SPMB registration start date'],
            ['spmb_end_date', null, 'text', 'SPMB registration end date'],
        ];

        foreach ($settings as $setting) {
            $this->insertSettingIfMissing(...$setting);
        }
    }

    private function schemaVersion(): string
    {
        if (!$this->tableExists('site_settings')) {
            return '';
        }

        $stmt = $this->pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = 'app_schema_version' LIMIT 1");
        $stmt->execute();
        return (string) ($stmt->fetchColumn() ?: '');
    }

    private function setSchemaVersion(string $version): void
    {
        $this->insertSettingIfMissing('app_schema_version', $version, 'text', 'Last repaired schema version');
        $stmt = $this->pdo->prepare("UPDATE site_settings SET setting_value = ?, setting_type = 'text', description = 'Last repaired schema version' WHERE setting_key = 'app_schema_version'");
        $stmt->execute([$version]);
    }

    private function hasRequiredSchema(): bool
    {
        $requiredColumns = [
            'menus' => ['menu_location', 'target', 'is_active', 'sort_order', 'parent_id', 'icon'],
            'school_profile' => [
                'tagline', 'spmb_link', 'welcome_message', 'principal_quote',
                'watermark_enabled', 'principal_nip', 'principal_photo',
                'google_maps_embed',
                // Operating hours (a representative subset is enough)
                'monday_open', 'monday_close', 'is_closed_monday',
                'friday_open', 'sunday_close', 'is_closed_sunday',
                // Statistics
                'total_students', 'total_teachers', 'graduation_rate',
            ],
            'site_settings' => ['setting_key', 'setting_value', 'setting_type'],
            'users' => ['permissions', 'is_spmb_committee', 'editor_id'],
            'news' => ['slug', 'category', 'status', 'views', 'published_at', 'meta_description', 'meta_keywords'],
            'gallery_albums' => ['slug', 'type', 'cover_image', 'is_active', 'sort_order'],
            'gallery_items' => ['album_id', 'type', 'file_path', 'youtube_url', 'youtube_video_id', 'is_active', 'sort_order'],
        ];

        foreach ($requiredColumns as $table => $columns) {
            if (!$this->tableExists($table)) {
                return false;
            }
            foreach ($columns as $column) {
                if (!$this->columnExists($table, $column)) {
                    return false;
                }
            }
        }

        // Ensure default data exists
        try {
            if ($this->tableExists('menus')) {
                $count = (int) $this->pdo->query('SELECT COUNT(*) FROM menus')->fetchColumn();
                if ($count === 0) {
                    return false;
                }
            }
            if ($this->tableExists('school_profile')) {
                $count = (int) $this->pdo->query('SELECT COUNT(*) FROM school_profile')->fetchColumn();
                if ($count === 0) {
                    return false;
                }
            }
        } catch (\Throwable) {
            return false;
        }

        return true;
    }

    private function insertSettingIfMissing(string $key, ?string $value, string $type, string $description): void
    {
        $stmt = $this->pdo->prepare('SELECT id FROM site_settings WHERE setting_key = ? LIMIT 1');
        $stmt->execute([$key]);
        if ($stmt->fetchColumn()) {
            return;
        }

        $stmt = $this->pdo->prepare('INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, ?, ?)');
        $stmt->execute([$key, $value, $type, $description]);
    }

    private function tableExists(string $table): bool
    {
        try {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?');
            $stmt->execute([$table]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (\Throwable) {
            if (!$this->isSafeIdentifier($table)) {
                return false;
            }

            try {
                $stmt = $this->pdo->query("SHOW TABLES LIKE " . $this->pdo->quote($table));
                return (bool) $stmt->fetchColumn();
            } catch (\Throwable) {
                return false;
            }
        }
    }

    private function columnExists(string $table, string $column): bool
    {
        try {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?');
            $stmt->execute([$table, $column]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (\Throwable) {
            if (!$this->isSafeIdentifier($table)) {
                return false;
            }

            try {
                $stmt = $this->pdo->query("SHOW COLUMNS FROM `{$table}` LIKE " . $this->pdo->quote($column));
                return (bool) $stmt->fetchColumn();
            } catch (\Throwable) {
                return false;
            }
        }
    }

    private function addColumn(string $table, string $column, string $definition): void
    {
        if ($this->columnExists($table, $column)) {
            return;
        }

        $this->safeExec(sprintf('ALTER TABLE `%s` ADD COLUMN `%s` %s', $table, $column, $definition));
    }

    private function safeExec(string $sql): void
    {
        try {
            $this->pdo->exec($sql);
        } catch (\Throwable $e) {
            error_log('Schema repair SQL failed: ' . $e->getMessage());
        }
    }

    private function isSafeIdentifier(string $value): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9_]+$/', $value);
    }
}
