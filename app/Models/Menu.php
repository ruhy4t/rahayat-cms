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
        $sql = "SELECT * FROM {$this->table} 
                WHERE is_active = 1 AND (menu_location = ? OR menu_location = 'both')
                ORDER BY sort_order ASC";
        return $this->db->fetchAll($sql, [$location]);
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
            if ($item['parent_id'] == $parentId) {
                $children = $this->buildTree($items, $item['id']);
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
        $sql = "SELECT m.*, p.title as parent_title 
                FROM {$this->table} m 
                LEFT JOIN {$this->table} p ON m.parent_id = p.id 
                ORDER BY m.sort_order ASC";
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
     * Get only parent menus (for dropdown)
     */
    public function getParentMenus(): array
    {
        $sql = "SELECT id, title FROM {$this->table} WHERE parent_id IS NULL ORDER BY title ASC";
        return $this->db->fetchAll($sql);
    }
}
