<?php
/**
 * ============================================
 * Base Model Class
 * ============================================
 */

declare(strict_types=1);

abstract class Model
{
    protected Database $db;
    protected string $table = '';
    protected string $primaryKey = 'id';
    protected array $fillable = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all records
     */
    public function all(string $orderBy = 'id', string $direction = 'DESC'): array
    {
        $orderBy = $this->safeIdentifier($orderBy, $this->primaryKey);
        $direction = $this->safeDirection($direction);
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy} {$direction}";
        return $this->db->fetchAll($sql);
    }

    /**
     * Find record by ID
     */
    public function find(int $id): array|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->fetch($sql, [$id]);
    }

    /**
     * Find record by column
     */
    public function findBy(string $column, mixed $value): array|false
    {
        $column = $this->safeIdentifier($column, $this->primaryKey);
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ?";
        return $this->db->fetch($sql, [$value]);
    }

    /**
     * Find all by column
     */
    public function findAllBy(string $column, mixed $value, string $orderBy = 'id', string $direction = 'DESC'): array
    {
        $column = $this->safeIdentifier($column, $this->primaryKey);
        $orderBy = $this->safeIdentifier($orderBy, $this->primaryKey);
        $direction = $this->safeDirection($direction);
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ? ORDER BY {$orderBy} {$direction}";
        return $this->db->fetchAll($sql, [$value]);
    }

    /**
     * Create new record
     */
    public function create(array $data): int|string
    {
        // Filter only fillable fields
        $data = array_intersect_key($data, array_flip($this->fillable));
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->insert($this->table, $data);
    }

    /**
     * Update record
     */
    public function update(int $id, array $data): int
    {
        // Filter only fillable fields
        $data = array_intersect_key($data, array_flip($this->fillable));
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->update($this->table, $data, "{$this->primaryKey} = ?", [$id]);
    }

    /**
     * Delete record
     */
    public function delete(int $id): int
    {
        return $this->db->delete($this->table, "{$this->primaryKey} = ?", [$id]);
    }

    /**
     * Count all records
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        return (int) $this->db->fetchColumn($sql);
    }

    /**
     * Count by condition
     */
    public function countWhere(string $column, mixed $value): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE {$column} = ?";
        return (int) $this->db->fetchColumn($sql, [$value]);
    }

    /**
     * Paginate records
     */
    public function paginate(int $page = 1, int $perPage = ITEMS_PER_PAGE, string $orderBy = 'id', string $direction = 'DESC'): array
    {
        $page = max(1, $page);
        $perPage = min(100, max(1, $perPage));
        $orderBy = $this->safeIdentifier($orderBy, $this->primaryKey);
        $direction = $this->safeDirection($direction);
        $offset = ($page - 1) * $perPage;
        $total = $this->count();
        $totalPages = (int) ceil($total / $perPage);

        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy} {$direction} LIMIT ? OFFSET ?";
        $data = $this->db->fetchAll($sql, [$perPage, $offset]);

        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'has_more' => $page < $totalPages
        ];
    }

    /**
     * Search records
     */
    public function search(string $column, string $term, int $limit = 10): array
    {
        $column = $this->safeIdentifier($column, $this->primaryKey);
        $limit = min(100, max(1, $limit));
        $sql = "SELECT * FROM {$this->table} WHERE {$column} LIKE ? LIMIT ?";
        return $this->db->fetchAll($sql, ['%' . $term . '%', $limit]);
    }

    protected function safeIdentifier(string $identifier, string $fallback = 'id'): string
    {
        return preg_match('/^[a-zA-Z0-9_\.]+$/', $identifier) ? $identifier : $fallback;
    }

    protected function safeDirection(string $direction): string
    {
        $direction = strtoupper($direction);
        return in_array($direction, ['ASC', 'DESC'], true) ? $direction : 'DESC';
    }
}
