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
    protected array $fillable = [
        'name', 'description', 'image', 'schedule', 'supervisor',
        'supervisors_json', 'schedules_json', 'achievements_json',
        'is_active', 'sort_order',
    ];

    public function all(string $orderBy = 'id', string $direction = 'DESC'): array
    {
        return array_map($this->expandStructuredData(...), parent::all($orderBy, $direction));
    }

    public function find(int $id): array|false
    {
        $item = parent::find($id);
        return $item === false ? false : $this->expandStructuredData($item);
    }

    /**
     * Get all active ekstrakurikuler
     */
    public function getActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY sort_order ASC";
        return array_map($this->expandStructuredData(...), $this->db->fetchAll($sql));
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
        return array_map($this->expandStructuredData(...), $stmt->fetchAll(PDO::FETCH_ASSOC));
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

    private function expandStructuredData(array $item): array
    {
        foreach (['name', 'description', 'schedule', 'supervisor'] as $field) {
            if (array_key_exists($field, $item)) {
                $item[$field] = Security::plainText($item[$field]);
            }
        }

        $item['supervisors'] = $this->decodeList($item['supervisors_json'] ?? null);
        if ($item['supervisors'] === [] && trim((string) ($item['supervisor'] ?? '')) !== '') {
            $item['supervisors'] = [[
                'name' => trim((string) $item['supervisor']),
                'role' => 'Pembina',
            ]];
        }

        $item['schedules'] = $this->decodeList($item['schedules_json'] ?? null);
        if ($item['schedules'] === [] && trim((string) ($item['schedule'] ?? '')) !== '') {
            $item['schedules'] = [[
                'day' => '',
                'time' => trim((string) $item['schedule']),
                'note' => '',
            ]];
        }

        $item['supervisors'] = $this->normalizeList($item['supervisors']);
        $item['schedules'] = $this->normalizeList($item['schedules']);
        $item['achievements'] = $this->normalizeList($this->decodeList($item['achievements_json'] ?? null));
        return $item;
    }

    private function normalizeList(array $items): array
    {
        foreach ($items as &$item) {
            foreach ($item as $key => $value) {
                if (is_scalar($value) || $value === null) {
                    $item[$key] = Security::plainText($value);
                }
            }
        }
        unset($item);

        return $items;
    }

    private function decodeList(mixed $value): array
    {
        if (!is_string($value) || trim($value) === '') {
            return [];
        }

        try {
            $decoded = json_decode($value, true, 32, JSON_THROW_ON_ERROR);
            return is_array($decoded) ? array_values(array_filter($decoded, 'is_array')) : [];
        } catch (JsonException) {
            return [];
        }
    }
}
