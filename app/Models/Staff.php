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
    protected array $fillable = ['name', 'nip', 'position', 'subject', 'photo', 'email', 'phone', 'is_teacher', 'is_principal', 'is_active', 'sort_order'];

    public function designatePrincipal(int $staffId): void
    {
        $this->db->query("UPDATE {$this->table} SET is_principal = 0 WHERE is_principal = 1");
        $this->update($staffId, ['is_principal' => 1]);
    }

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
        $groups = [
            'principal' => [
                'name' => 'Kepala Sekolah',
                'items' => []
            ],
            'teachers' => [
                'name' => 'Guru',
                'items' => []
            ],
            'staff' => [
                'name' => 'Tenaga Kependidikan',
                'items' => []
            ]
        ];

        foreach ($this->getActive() as $staff) {
            $position = trim((string) ($staff['position'] ?? ''));
            $normalizedPosition = function_exists('mb_strtolower')
                ? mb_strtolower($position, 'UTF-8')
                : strtolower($position);
            $isPrincipal = (int) ($staff['is_principal'] ?? 0) === 1
                || str_contains($normalizedPosition, 'kepala sekolah');

            if ($isPrincipal) {
                $staff['position'] = 'Kepala Sekolah';
                $groups['principal']['items'][] = $staff;
                continue;
            }

            $groupKey = (int) ($staff['is_teacher'] ?? 0) === 1 ? 'teachers' : 'staff';
            $groups[$groupKey]['items'][] = $staff;
        }

        return $groups;
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
