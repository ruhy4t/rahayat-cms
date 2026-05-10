<?php
/**
 * ============================================
 * GalleryItem Model (Photos & Videos)
 * ============================================
 */

declare(strict_types=1);

class GalleryItem extends Model
{
    protected string $table = 'gallery_items';
    protected array $fillable = ['album_id', 'title', 'description', 'type', 'file_path', 'youtube_url', 'youtube_video_id', 'is_active', 'sort_order'];

    /**
     * Get items by album
     */
    public function getByAlbum(int $albumId): array
    {
        $activeFilter = $this->hasColumn('is_active') ? ' AND is_active = 1' : '';
        $orderBy = $this->hasColumn('sort_order') ? 'sort_order ASC' : 'id ASC';
        $sql = "SELECT * FROM {$this->table} WHERE album_id = ?{$activeFilter} ORDER BY {$orderBy}";
        return $this->db->fetchAll($sql, [$albumId]);
    }

    /**
     * Get all images (type = image)
     */
    public function getImages(): array
    {
        $typeFilter = $this->hasColumn('type') ? "i.type = 'image' AND " : '';
        $activeFilter = $this->hasColumn('is_active') ? 'i.is_active = 1' : '1=1';
        $orderBy = $this->hasColumn('sort_order') ? 'i.sort_order ASC' : 'i.id ASC';

        $sql = "SELECT i.*, a.title as album_title 
                FROM {$this->table} i 
                LEFT JOIN gallery_albums a ON i.album_id = a.id
                WHERE {$typeFilter}{$activeFilter}
                ORDER BY {$orderBy}";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get all videos (type = video)
     */
    public function getVideos(): array
    {
        $typeFilter = $this->hasColumn('type') ? "i.type = 'video' AND " : '';
        $activeFilter = $this->hasColumn('is_active') ? 'i.is_active = 1' : '1=1';
        $orderBy = $this->hasColumn('sort_order') ? 'i.sort_order ASC' : 'i.id ASC';

        $sql = "SELECT i.*, a.title as album_title 
                FROM {$this->table} i 
                LEFT JOIN gallery_albums a ON i.album_id = a.id
                WHERE {$typeFilter}{$activeFilter}
                ORDER BY {$orderBy}";
        return $this->db->fetchAll($sql);
    }

    /**
     * Extract YouTube video ID from URL
     */
    public static function extractYouTubeId(string $url): ?string
    {
        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
            '/youtu\.be\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/v\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/shorts\/([a-zA-Z0-9_-]+)/'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Generate YouTube embed URL
     */
    public static function getYouTubeEmbedUrl(string $videoId): string
    {
        return "https://www.youtube.com/embed/{$videoId}";
    }

    /**
     * Generate YouTube thumbnail URL
     */
    public static function getYouTubeThumbnail(string $videoId, string $quality = 'hqdefault'): string
    {
        return "https://img.youtube.com/vi/{$videoId}/{$quality}.jpg";
    }

    /**
     * Create video item with YouTube parsing
     */
    public function createVideo(array $data): int|string
    {
        if (!empty($data['youtube_url'])) {
            $videoId = self::extractYouTubeId($data['youtube_url']);
            $data['youtube_video_id'] = $videoId;
        }
        $data['type'] = 'video';
        return $this->create($data);
    }

    /**
     * Get recent items for homepage
     */
    public function getRecent(int $limit = 8): array
    {
        $activeFilter = $this->hasColumn('is_active') ? 'WHERE is_active = 1' : '';
        $orderBy = $this->hasColumn('created_at') ? 'created_at DESC' : 'id DESC';
        $sql = "SELECT * FROM {$this->table} {$activeFilter} ORDER BY {$orderBy} LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }
}
