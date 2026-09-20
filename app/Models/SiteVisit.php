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

    /** Unique session visitors per calendar period; total counts all recorded page views. */
    public function getPublicStatistics(?DateTimeImmutable $now = null): array
    {
        $now ??= new DateTimeImmutable('today');
        $today = $now->format('Y-m-d');
        $weekStart = $now->modify('monday this week')->format('Y-m-d');
        $monthStart = $now->format('Y-m-01');
        $period = $this->db->fetch(
            "SELECT COUNT(DISTINCT CASE WHEN visited_on = ? THEN visitor_key END) AS today,
                    COUNT(DISTINCT CASE WHEN visited_on >= ? THEN visitor_key END) AS week,
                    COUNT(DISTINCT CASE WHEN visited_on >= ? THEN visitor_key END) AS month
             FROM {$this->table} WHERE visited_on >= ? AND visited_on <= ?",
            [$today, $weekStart, $monthStart, min($weekStart, $monthStart), $today]
        );

        return [
            'today' => (int) ($period['today'] ?? 0),
            'week' => (int) ($period['week'] ?? 0),
            'month' => (int) ($period['month'] ?? 0),
            'total' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->table}"),
        ];
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
