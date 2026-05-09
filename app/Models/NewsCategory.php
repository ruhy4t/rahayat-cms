<?php
/**
 * ============================================
 * NewsCategory Model
 * ============================================
 */

declare(strict_types=1);

class NewsCategory extends Model
{
    protected string $table = 'news_categories';
    protected array $fillable = ['name', 'slug', 'color', 'is_active'];

    /**
     * Get all active categories
     */
    public function getActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY name ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Generate unique slug
     */
    public function generateSlug(string $name, ?int $excludeId = null): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if slug exists
     */
    private function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM {$this->table} WHERE slug = ?";
        $params = [$slug];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        return (bool) $this->db->fetch($sql, $params);
    }

    /**
     * Get category with news count
     */
    public function getAllWithCount(): array
    {
        $sql = "SELECT c.*, COUNT(n.id) as news_count 
                FROM {$this->table} c 
                LEFT JOIN news n ON n.category_id = c.id 
                GROUP BY c.id 
                ORDER BY c.name ASC";
        return $this->db->fetchAll($sql);
    }
}
