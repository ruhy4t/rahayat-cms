<?php
/**
 * ============================================
 * Staff Model (Guru & Tenaga Kependidikan)
 * ============================================
 */

declare(strict_types=1);

class Staff extends Model
{
    protected string $table = 'staff';
    protected array $fillable = ['name', 'nip', 'position', 'subject', 'photo', 'email', 'phone', 'is_teacher', 'is_active', 'sort_order'];

    /**
     * Get all active staff
     */
    public function getActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY sort_order ASC, name ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get teachers only
     */
    public function getTeachers(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_teacher = 1 AND is_active = 1 ORDER BY sort_order ASC, name ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get non-teaching staff
     */
    public function getNonTeachers(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_teacher = 0 AND is_active = 1 ORDER BY sort_order ASC, name ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get staff grouped by category
     */
    public function getGrouped(): array
    {
        return [
            'teachers' => [
                'name' => 'Guru',
                'items' => $this->getTeachers()
            ],
            'staff' => [
                'name' => 'Tenaga Kependidikan',
                'items' => $this->getNonTeachers()
            ]
        ];
    }

    /**
     * Search staff by name
     */
    public function searchByName(string $query): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE name LIKE ? AND is_active = 1 ORDER BY name ASC";
        return $this->db->fetchAll($sql, ["%{$query}%"]);
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
