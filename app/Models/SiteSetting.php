<?php
/**
 * ============================================
 * SiteSetting Model
 * ============================================
 */

declare(strict_types=1);

class SiteSetting extends Model
{
    protected string $table = 'site_settings';
    protected array $fillable = ['setting_key', 'setting_value', 'setting_type', 'description'];

    /**
     * Get setting by key
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $sql = "SELECT setting_value, setting_type FROM {$this->table} WHERE setting_key = ?";
        $result = $this->db->fetch($sql, [$key]);

        if (!$result) {
            return $default;
        }

        return $this->castValue($result['setting_value'], $result['setting_type']);
    }

    /**
     * Set setting value
     */
    public function set(string $key, mixed $value): bool
    {
        $existing = $this->db->fetch("SELECT id FROM {$this->table} WHERE setting_key = ?", [$key]);

        if ($existing) {
            return (bool) $this->db->update(
                $this->table,
                ['setting_value' => $value, 'updated_at' => date('Y-m-d H:i:s')],
                'id = ?',
                [$existing['id']]
            );
        }

        return (bool) $this->db->insert($this->table, [
            'setting_key' => $key,
            'setting_value' => $value,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get all settings as associative array
     */
    public function getAll(): array
    {
        $sql = "SELECT setting_key, setting_value, setting_type FROM {$this->table}";
        $results = $this->db->fetchAll($sql);

        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $this->castValue($row['setting_value'], $row['setting_type']);
        }

        return $settings;
    }

    /**
     * Get all settings for admin (with metadata)
     */
    public function getAllForAdmin(): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY setting_key ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Cast value based on type
     */
    private function castValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => (bool) $value,
            'number' => (float) $value,
            'json' => json_decode($value, true) ?? [],
            default => $value
        };
    }

    /**
     * Get current theme
     */
    public function getTheme(): string
    {
        return $this->get('theme', 'indigo-modern');
    }

    /**
     * Get available themes
     */
    public function getAvailableThemes(): array
    {
        return [
            'indigo-modern' => [
                'name' => 'Indigo Modern',
                'primary' => '#4F46E5',
                'description' => 'Clean, professional, gradient accents (Default Layout)'
            ],
            'emerald-campus' => [
                'name' => 'Emerald Campus',
                'primary' => '#059669',
                'description' => 'Fresh, 2-tier navbar, centered hero, magazine news layout'
            ],
            'crimson-bold' => [
                'name' => 'Crimson Bold',
                'primary' => '#BE123C',
                'description' => 'Bold, solid dark navbar, diagonal hero, horizontal news cards'
            ]
        ];
    }

    /**
     * Check if SPMB is enabled
     */
    public function isSPMBEnabled(): bool
    {
        return $this->get('spmb_enabled', false);
    }
}
