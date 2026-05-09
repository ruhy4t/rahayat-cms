<?php
/**
 * ============================================
 * Facility Model
 * ============================================
 */

declare(strict_types=1);

class Facility extends Model
{
    protected string $table = 'facilities';
    protected array $fillable = ['name', 'description', 'image', 'type', 'capacity', 'is_active', 'sort_order'];

    /**
     * Facility types
     */
    public const TYPES = [
        'perpustakaan' => 'Perpustakaan',
        'laboratorium' => 'Laboratorium',
        'olahraga' => 'Olahraga',
        'seni' => 'Seni',
        'ibadah' => 'Ibadah',
        'kantin' => 'Kantin',
        'lainnya' => 'Lainnya'
    ];

    /**
     * Get all active facilities
     */
    public function getActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY sort_order ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get facilities by type
     */
    public function getByType(string $type): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE type = ? AND is_active = 1 ORDER BY sort_order ASC";
        return $this->db->fetchAll($sql, [$type]);
    }

    /**
     * Get facilities grouped by type
     */
    public function getGroupedByType(): array
    {
        $facilities = $this->getActive();
        $grouped = [];

        foreach ($facilities as $facility) {
            $type = $facility['type'];
            if (!isset($grouped[$type])) {
                $grouped[$type] = [
                    'name' => self::TYPES[$type] ?? ucfirst($type),
                    'items' => []
                ];
            }
            $grouped[$type]['items'][] = $facility;
        }

        return $grouped;
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
