<?php
/**
 * ============================================
 * News Model
 * ============================================
 */

declare(strict_types=1);

class News extends Model
{
    protected string $table = 'news';
    protected array $fillable = ['title', 'slug', 'excerpt', 'content', 'image', 'category', 'category_id', 'author_id', 'status', 'published_at'];

    /**
     * Get published news
     */
    public function getPublished(int $limit = 10): array
    {
        $sql = "SELECT n.*, u.name as author_name 
                FROM {$this->table} n 
                LEFT JOIN users u ON n.author_id = u.id 
                WHERE n.status = 'published' AND n.published_at <= NOW()
                ORDER BY n.published_at DESC 
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Find by slug
     */
    public function findBySlug(string $slug): array|false
    {
        $sql = "SELECT n.*, u.name as author_name 
                FROM {$this->table} n 
                LEFT JOIN users u ON n.author_id = u.id 
                WHERE n.slug = ?";
        return $this->db->fetch($sql, [$slug]);
    }

    /**
     * Get news with author info
     */
    public function getAllWithAuthor(string $orderBy = 'created_at', string $direction = 'DESC'): array
    {
        $allowedSorts = ['id', 'title', 'created_at', 'updated_at', 'published_at', 'status', 'views'];
        $orderBy = in_array($orderBy, $allowedSorts, true) ? $orderBy : 'created_at';
        $direction = $this->safeDirection($direction);
        $sql = "SELECT n.*, u.name as author_name 
                FROM {$this->table} n 
                LEFT JOIN users u ON n.author_id = u.id 
                ORDER BY n.{$orderBy} {$direction}";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get by category
     */
    public function getByCategory(string $category, int $limit = 10): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE category = ? AND status = 'published' 
                ORDER BY published_at DESC 
                LIMIT ?";
        return $this->db->fetchAll($sql, [$category, $limit]);
    }

    /**
     * Generate unique slug
     */
    public function generateSlug(string $title, ?int $excludeId = null): string
    {
        // Convert to lowercase and replace spaces
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        // Check for uniqueness
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
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE slug = ?";
        $params = [$slug];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        return (int) $this->db->fetchColumn($sql, $params) > 0;
    }

    /**
     * Search news
     */
    public function searchNews(string $term, int $limit = 10): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE (title LIKE ? OR content LIKE ?) AND status = 'published'
                ORDER BY published_at DESC 
                LIMIT ?";
        $term = '%' . $term . '%';
        return $this->db->fetchAll($sql, [$term, $term, $limit]);
    }

    /**
     * Increment view count
     */
    public function incrementViews(int $id): void
    {
        $sql = "UPDATE {$this->table} SET views = views + 1 WHERE id = ?";
        $this->db->query($sql, [$id]);
    }

    /**
     * Get popular news
     */
    public function getPopular(int $limit = 5): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 'published' 
                ORDER BY views DESC 
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Get recent news
     */
    public function getRecent(int $limit = 5): array
    {
        return $this->getPublished($limit);
    }

    /**
     * Count published news
     */
    public function countPublished(): int
    {
        return $this->countWhere('status', 'published');
    }

    /**
     * Paginate with author
     */
    public function paginateWithAuthor(int $page = 1, int $perPage = ITEMS_PER_PAGE): array
    {
        $page = max(1, $page);
        $perPage = min(100, max(1, $perPage));
        $offset = ($page - 1) * $perPage;
        $total = $this->count();
        $totalPages = (int) ceil($total / $perPage);

        $sql = "SELECT n.*, u.name as author_name 
                FROM {$this->table} n 
                LEFT JOIN users u ON n.author_id = u.id 
                ORDER BY n.created_at DESC 
                LIMIT ? OFFSET ?";
        $data = $this->db->fetchAll($sql, [$perPage, $offset]);

        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'has_more' => $page < $totalPages
        ];
    }
}
