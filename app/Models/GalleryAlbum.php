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
        $sql = "SELECT a.*, COUNT(i.id) as item_count 
                FROM {$this->table} a 
                LEFT JOIN gallery_items i ON i.album_id = a.id
                GROUP BY a.id 
                ORDER BY a.sort_order ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get all active albums
     */
    public function getActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY sort_order ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get albums with item count
     */
    public function getWithItemCount(): array
    {
        $sql = "SELECT a.*, COUNT(i.id) as item_count 
                FROM {$this->table} a 
                LEFT JOIN gallery_items i ON i.album_id = a.id AND i.is_active = 1
                WHERE a.is_active = 1
                GROUP BY a.id 
                ORDER BY a.sort_order ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get album by slug
     */
    public function findBySlug(string $slug): array|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE slug = ? AND is_active = 1";
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
}
