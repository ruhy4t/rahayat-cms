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
        $sql = "SELECT * FROM {$this->table} WHERE album_id = ? AND is_active = 1 ORDER BY sort_order ASC";
        return $this->db->fetchAll($sql, [$albumId]);
    }

    /**
     * Get all images (type = image)
     */
    public function getImages(): array
    {
        $sql = "SELECT i.*, a.title as album_title 
                FROM {$this->table} i 
                LEFT JOIN gallery_albums a ON i.album_id = a.id
                WHERE i.type = 'image' AND i.is_active = 1 
                ORDER BY i.sort_order ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get all videos (type = video)
     */
    public function getVideos(): array
    {
        $sql = "SELECT i.*, a.title as album_title 
                FROM {$this->table} i 
                LEFT JOIN gallery_albums a ON i.album_id = a.id
                WHERE i.type = 'video' AND i.is_active = 1 
                ORDER BY i.sort_order ASC";
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
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY created_at DESC LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }
}
