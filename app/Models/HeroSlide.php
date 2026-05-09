<?php
/**
 * ============================================
 * HeroSlide Model
 * ============================================
 */

declare(strict_types=1);

class HeroSlide extends Model
{
    protected string $table = 'hero_slides';
    protected array $fillable = ['title', 'subtitle', 'image', 'button_text', 'button_url', 'sort_order', 'is_active'];

    /**
     * Get all active slides for frontend
     */
    public function getActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY sort_order ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get all slides for admin
     */
    public function getAllForAdmin(): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY sort_order ASC";
        return $this->db->fetchAll($sql);
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

    /**
     * Toggle active status
     */
    public function toggleActive(int $id): bool
    {
        $slide = $this->find($id);
        if (!$slide) {
            return false;
        }

        return (bool) $this->update($id, ['is_active' => !$slide['is_active']]);
    }
}
