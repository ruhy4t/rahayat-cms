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
    private static array $tableColumnsCache = [];

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
        $data = $this->filterExistingColumns($data);

        if ($this->hasColumn('created_at')) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        if ($this->hasColumn('updated_at')) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        if (empty($data)) {
            return 0;
        }

        return $this->db->insert($this->table, $data);
    }

    /**
     * Update record
     */
    public function update(int $id, array $data): int
    {
        // Filter only fillable fields
        $data = array_intersect_key($data, array_flip($this->fillable));
        $data = $this->filterExistingColumns($data);

        if ($this->hasColumn('updated_at')) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        if (empty($data)) {
            return 0;
        }

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

    protected function hasColumn(string $column): bool
    {
        return in_array($column, $this->getTableColumns(), true);
    }

    protected function filterExistingColumns(array $data): array
    {
        $columns = $this->getTableColumns();
        if (empty($columns)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($columns));
    }

    protected function getTableColumns(): array
    {
        $cacheKey = DB_NAME . '.' . $this->table;
        if (isset(self::$tableColumnsCache[$cacheKey])) {
            return self::$tableColumnsCache[$cacheKey];
        }

        $columns = [];

        try {
            $sql = 'SELECT column_name FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ?';
            $rows = $this->db->fetchAll($sql, [$this->table]);
            foreach ($rows as $row) {
                if (!empty($row['column_name'])) {
                    $columns[] = (string) $row['column_name'];
                } elseif (!empty($row['COLUMN_NAME'])) {
                    $columns[] = (string) $row['COLUMN_NAME'];
                }
            }
        } catch (\Throwable) {
            $columns = [];
        }

        if (empty($columns)) {
            try {
                $safeTable = preg_replace('/[^a-zA-Z0-9_]/', '', $this->table);
                $rows = $this->db->fetchAll("SHOW COLUMNS FROM `{$safeTable}`");
                foreach ($rows as $row) {
                    if (!empty($row['Field'])) {
                        $columns[] = (string) $row['Field'];
                    }
                }
            } catch (\Throwable) {
                $columns = [];
            }
        }

        $columns = array_values(array_unique($columns));
        self::$tableColumnsCache[$cacheKey] = $columns;

        return $columns;
    }
}
