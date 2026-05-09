<?php
/**
 * ============================================
 * Menu Model
 * ============================================
 */

declare(strict_types=1);

class Menu extends Model
{
    protected string $table = 'menus';
    protected array $fillable = ['title', 'url', 'icon', 'parent_id', 'sort_order', 'is_active', 'target', 'menu_location'];

    /**
     * Get all menus for a location (header/footer)
     */
    public function getByLocation(string $location = 'header'): array
    {
        $filters = [];
        $params = [];

        if ($this->hasColumn('is_active')) {
            $filters[] = 'is_active = 1';
        }

        if ($this->hasColumn('menu_location')) {
            $filters[] = "(menu_location = ? OR menu_location = 'both')";
            $params[] = $location;
        }

        $where = empty($filters) ? '1=1' : implode(' AND ', $filters);
        $orderBy = $this->hasColumn('sort_order') ? 'sort_order ASC' : 'id ASC';

        $sql = "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$orderBy}";
        $rows = $this->db->fetchAll($sql, $params);

        return array_map([$this, 'normalizeMenuRow'], $rows);
    }

    /**
     * Get hierarchical menu structure
     */
    public function getHierarchical(string $location = 'header'): array
    {
        $menus = $this->getByLocation($location);
        return $this->buildTree($menus);
    }

    /**
     * Build menu tree from flat array
     */
    private function buildTree(array $items, ?int $parentId = null): array
    {
        $tree = [];
        foreach ($items as $item) {
            if ($this->normalizeParentId($item['parent_id'] ?? null) === $parentId) {
                $children = $this->buildTree($items, (int) $item['id']);
                if ($children) {
                    $item['children'] = $children;
                }
                $tree[] = $item;
            }
        }
        return $tree;
    }

    /**
     * Get all menus for admin (flat list with parent info)
     */
    public function getAllForAdmin(): array
    {
        if (!$this->hasColumn('parent_id')) {
            $orderBy = $this->hasColumn('sort_order') ? 'sort_order ASC' : 'id ASC';
            $rows = $this->db->fetchAll("SELECT * FROM {$this->table} ORDER BY {$orderBy}");
            return array_map(function (array $row): array {
                $row = $this->normalizeMenuRow($row);
                $row['parent_title'] = null;
                return $row;
            }, $rows);
        }

        $orderBy = $this->hasColumn('sort_order') ? 'm.sort_order ASC' : 'm.id ASC';
        $sql = "SELECT m.*, p.title as parent_title 
                FROM {$this->table} m 
                LEFT JOIN {$this->table} p ON m.parent_id = p.id 
                ORDER BY {$orderBy}";
        $rows = $this->db->fetchAll($sql);
        $rows = array_map(function (array $row): array {
            return $this->normalizeMenuRow($row);
        }, $rows);

        return $this->sortForAdminTree($rows);
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

    public function delete(int $id): int
    {
        if ($this->hasColumn('parent_id')) {
            $children = $this->db->fetchAll("SELECT id FROM {$this->table} WHERE parent_id = ?", [$id]);
            foreach ($children as $child) {
                $this->delete((int) $child['id']);
            }
        }

        return parent::delete($id);
    }

    public function hasChildren(int $id): bool
    {
        if (!$this->hasColumn('parent_id')) {
            return false;
        }

        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE parent_id = ?";
        return (int) $this->db->fetchColumn($sql, [$id]) > 0;
    }

    /**
     * Get only parent menus (for dropdown)
     */
    public function getParentMenus(): array
    {
        if (!$this->hasColumn('parent_id')) {
            $sql = "SELECT id, title FROM {$this->table} ORDER BY title ASC";
            return $this->db->fetchAll($sql);
        }

        $sql = "SELECT id, title FROM {$this->table} WHERE parent_id IS NULL ORDER BY title ASC";
        return $this->db->fetchAll($sql);
    }

    private function normalizeMenuRow(array $row): array
    {
        $defaults = [
            'icon' => null,
            'parent_id' => null,
            'sort_order' => 0,
            'is_active' => 1,
            'target' => '_self',
            'menu_location' => 'header',
        ];

        return $row + $defaults;
    }

    private function normalizeParentId(mixed $parentId): ?int
    {
        if ($parentId === null || $parentId === '' || (string) $parentId === '0') {
            return null;
        }

        return (int) $parentId;
    }

    private function sortForAdminTree(array $rows): array
    {
        $parents = [];
        $children = [];

        foreach ($rows as $row) {
            $parentId = $this->normalizeParentId($row['parent_id'] ?? null);
            if ($parentId === null) {
                $parents[] = $row;
                continue;
            }

            $children[$parentId][] = $row;
        }

        $sorted = [];
        foreach ($parents as $parent) {
            $sorted[] = $parent;
            $parentId = (int) $parent['id'];
            foreach ($children[$parentId] ?? [] as $child) {
                $sorted[] = $child;
            }
            unset($children[$parentId]);
        }

        foreach ($children as $orphanGroup) {
            foreach ($orphanGroup as $orphan) {
                $sorted[] = $orphan;
            }
        }

        return $sorted;
    }
}
