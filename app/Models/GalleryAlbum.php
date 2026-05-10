<?php
/**
 * ============================================
 * GalleryAlbum Model
 * ============================================
 */

declare(strict_types=1);

class GalleryAlbum extends Model
{
    protected string $table = 'gallery_albums';
    protected array $fillable = ['title', 'slug', 'description', 'type', 'cover_image', 'is_active', 'sort_order'];

    /**
     * Get all albums (including inactive) with item count
     */
    public function getAll(): array
    {
        $itemCountSql = $this->galleryItemsTableExists()
            ? "(SELECT COUNT(*) FROM gallery_items i WHERE i.album_id = a.id) AS item_count"
            : "0 AS item_count";
        $orderBy = $this->hasColumn('sort_order') ? 'a.sort_order ASC' : 'a.id ASC';

        $sql = "SELECT a.*, {$itemCountSql}
                FROM {$this->table} a
                ORDER BY {$orderBy}";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get all active albums
     */
    public function getActive(): array
    {
        $where = $this->hasColumn('is_active') ? 'WHERE is_active = 1' : '';
        $orderBy = $this->hasColumn('sort_order') ? 'sort_order ASC' : 'id ASC';
        $sql = "SELECT * FROM {$this->table} {$where} ORDER BY {$orderBy}";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get albums with item count
     */
    public function getWithItemCount(): array
    {
        $where = $this->hasColumn('is_active') ? 'WHERE a.is_active = 1' : '';
        $orderBy = $this->hasColumn('sort_order') ? 'a.sort_order ASC' : 'a.id ASC';
        $itemCountSql = '0 AS item_count';

        if ($this->galleryItemsTableExists()) {
            $itemActiveFilter = $this->galleryItemsHasColumn('is_active') ? ' AND i.is_active = 1' : '';
            $itemCountSql = "(SELECT COUNT(*) FROM gallery_items i WHERE i.album_id = a.id{$itemActiveFilter}) AS item_count";
        }

        $sql = "SELECT a.*, {$itemCountSql}
                FROM {$this->table} a
                {$where}
                ORDER BY {$orderBy}";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get album by slug
     */
    public function findBySlug(string $slug): array|false
    {
        $activeFilter = $this->hasColumn('is_active') ? ' AND is_active = 1' : '';
        $sql = "SELECT * FROM {$this->table} WHERE slug = ?{$activeFilter}";
        return $this->db->fetch($sql, [$slug]);
    }

    /**
     * Generate unique slug
     */
    public function generateSlug(string $title, ?int $excludeId = null): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
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

    private function galleryItemsTableExists(): bool
    {
        try {
            $sql = 'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?';
            return (int) $this->db->fetchColumn($sql, ['gallery_items']) > 0;
        } catch (\Throwable) {
            try {
                return (bool) $this->db->fetchColumn("SHOW TABLES LIKE 'gallery_items'");
            } catch (\Throwable) {
                return false;
            }
        }
    }

    private function galleryItemsHasColumn(string $column): bool
    {
        try {
            $sql = 'SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?';
            return (int) $this->db->fetchColumn($sql, ['gallery_items', $column]) > 0;
        } catch (\Throwable) {
            try {
                return (bool) $this->db->fetchColumn("SHOW COLUMNS FROM `gallery_items` LIKE " . $this->db->getConnection()->quote($column));
            } catch (\Throwable) {
                return false;
            }
        }
    }
}
