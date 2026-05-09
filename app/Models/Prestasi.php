<?php
/**
 * ============================================
 * Prestasi Model
 * ============================================
 */

declare(strict_types=1);

class Prestasi extends Model
{
    protected string $table = 'prestasi';
    protected array $fillable = [
        'title',
        'description',
        'category',
        'image',
        'date',
        'created_by'
    ];

    /**
     * Alias for find()
     */
    public function findById(int $id): array|false
    {
        return $this->find($id);
    }

    /**
     * Get all prestasi with author name
     */
    public function getAllWithAuthor(): array
    {
        $sql = "SELECT p.*, u.name as author_name 
                FROM {$this->table} p 
                LEFT JOIN users u ON p.created_by = u.id 
                ORDER BY p.date DESC, p.created_at DESC";

        return $this->db->fetchAll($sql);
    }

    /**
     * Get latest prestasi by category
     */
    public function getLatestByCategory(string $category, int $limit = 6): array
    {
        $sql = "SELECT p.*, u.name as author_name 
                FROM {$this->table} p 
                LEFT JOIN users u ON p.created_by = u.id 
                WHERE p.category = ?
                ORDER BY p.date DESC, p.created_at DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$category, $limit]);
    }

    /**
     * Get a single prestasi with author details
     */
    public function getWithAuthor(int $id): array|false
    {
        $sql = "SELECT p.*, u.name as author_name 
                FROM {$this->table} p 
                LEFT JOIN users u ON p.created_by = u.id 
                WHERE p.id = ?";

        return $this->db->fetch($sql, [$id]);
    }
}
