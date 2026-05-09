<?php
/**
 * ============================================
 * Site Visit Analytics Model
 * ============================================
 */

declare(strict_types=1);

class SiteVisit extends Model
{
    protected string $table = 'site_visits';
    protected array $fillable = [
        'visitor_key',
        'path',
        'title',
        'content_type',
        'content_id',
        'visited_on',
        'created_at',
    ];
    private static bool $tableChecked = false;

    public function __construct()
    {
        parent::__construct();

        if (!self::$tableChecked) {
            $this->ensureTable();
            self::$tableChecked = true;
        }
    }

    private function ensureTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->db->query($sql);
    }

    public function record(array $data): void
    {
        $data['visited_on'] = date('Y-m-d');
        $data['created_at'] = date('Y-m-d H:i:s');

        $this->db->insert($this->table, array_intersect_key($data, array_flip($this->fillable)));
    }

    public function countVisitorsToday(): int
    {
        $sql = "SELECT COUNT(DISTINCT visitor_key) FROM {$this->table} WHERE visited_on = CURDATE()";
        return (int) $this->db->fetchColumn($sql);
    }

    public function countPageViewsToday(): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE visited_on = CURDATE()";
        return (int) $this->db->fetchColumn($sql);
    }

    public function getTopContent(int $limit = 5, int $days = 30): array
    {
        $limit = min(20, max(1, $limit));
        $days = min(365, max(1, $days));

        $sql = "SELECT
                    COALESCE(NULLIF(title, ''), path) AS title,
                    content_type,
                    content_id,
                    path,
                    COUNT(*) AS views,
                    COUNT(DISTINCT visitor_key) AS visitors
                FROM {$this->table}
                WHERE visited_on >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                  AND content_type NOT IN ('home', 'search')
                GROUP BY content_type, content_id, path, title
                ORDER BY views DESC, visitors DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$days, $limit]);
    }
}
