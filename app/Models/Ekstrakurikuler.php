<?php
/**
 * ============================================
 * Ekstrakurikuler Model
 * ============================================
 */

declare(strict_types=1);

class Ekstrakurikuler extends Model
{
    protected string $table = 'ekstrakurikuler';
    protected array $fillable = ['name', 'description', 'image', 'schedule', 'supervisor', 'is_active', 'sort_order'];

    /**
     * Get all active ekstrakurikuler
     */
    public function getActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY sort_order ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get recent ekstrakurikuler with a limit
     */
    public function getRecent(int $limit = 6): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY sort_order ASC LIMIT :limit";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update sort order
     */
    public function updateOrder(array $order): bool
    {
        foreach ($order as $index => $id) {
            $this->update((int) $id, ['sort_order' => $index]);
        }
        return true;
    }
}
