<?php
/**
 * ============================================
 * Contact Message Model
 * ============================================
 */

declare(strict_types=1);

class ContactMessage extends Model
{
    protected string $table = 'contact_messages';
    protected array $fillable = [
        'name',
        'email',
        'subject',
        'message'
    ];

    /**
     * Get unread messages count
     */
    public function getUnreadCount(): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE is_read = 0";
        $result = $this->db->fetch($sql);
        return (int) ($result['count'] ?? 0);
    }

    /**
     * Mark as read
     */
    public function markAsRead(int $id): void
    {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE id = ?";
        $this->db->query($sql, [$id]);
    }

    /**
     * Add reply
     */
    public function addReply(int $id, string $reply): void
    {
        $sql = "UPDATE {$this->table} SET reply_message = ?, replied = 1, replied_at = NOW() WHERE id = ?";
        $this->db->query($sql, [$reply, $id]);
    }

    /**
     * Get recent messages
     */
    public function getRecent(int $limit = 10): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }
}
