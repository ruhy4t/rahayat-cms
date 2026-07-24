<?php
/**
 * Public announcement model for scheduled popups and text sliders.
 */

declare(strict_types=1);

class Announcement extends Model
{
    protected string $table = 'announcements';
    protected array $fillable = [
        'type',
        'title',
        'content',
        'image',
        'start_at',
        'end_at',
        'sort_order',
        'is_active',
    ];

    public function getAllForAdmin(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table}
             ORDER BY type ASC, sort_order ASC, created_at DESC"
        );
    }

    public function getActiveByType(string $type): array
    {
        if (!in_array($type, ['popup', 'text_slider'], true)) {
            return [];
        }

        $now = date('Y-m-d H:i:s');

        return $this->db->fetchAll(
            "SELECT * FROM {$this->table}
             WHERE type = ?
               AND is_active = 1
               AND (start_at IS NULL OR start_at <= ?)
               AND (end_at IS NULL OR end_at >= ?)
             ORDER BY sort_order ASC, created_at DESC",
            [$type, $now, $now]
        );
    }
}
