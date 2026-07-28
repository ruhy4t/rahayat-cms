<?php

declare(strict_types=1);

class Testimonial extends Model
{
    protected string $table = 'testimonials';
    protected array $fillable = [
        'name',
        'relationship',
        'graduation_year',
        'occupation',
        'testimonial',
        'photo',
        'contact',
        'consent',
        'status',
        'is_featured',
        'sort_order',
        'submitted_ip_hash',
        'approved_at',
    ];

    public function getApproved(int $limit = 24): array
    {
        $limit = min(100, max(1, $limit));
        $sql = "SELECT id, name, relationship, graduation_year, occupation, testimonial, photo, is_featured
                FROM {$this->table}
                WHERE status = 'approved' AND consent = 1
                ORDER BY is_featured DESC, sort_order ASC, approved_at DESC, id DESC
                LIMIT :limit";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getForHomepage(int $limit = 6): array
    {
        $limit = min(12, max(1, $limit));
        $sql = "SELECT id, name, relationship, graduation_year, occupation, testimonial, photo
                FROM {$this->table}
                WHERE status = 'approved' AND consent = 1
                ORDER BY is_featured DESC, sort_order ASC, approved_at DESC, id DESC
                LIMIT :limit";
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllForAdmin(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table}
             ORDER BY FIELD(status, 'pending', 'approved', 'rejected'), created_at DESC, id DESC"
        );
    }

    public function countRecentByIpHash(string $ipHash, int $minutes = 60): int
    {
        $minutes = min(1440, max(1, $minutes));
        $cutoff = date('Y-m-d H:i:s', time() - ($minutes * 60));
        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->table} WHERE submitted_ip_hash = ? AND created_at >= ?",
            [$ipHash, $cutoff]
        );
    }
}
