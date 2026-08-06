<?php

declare(strict_types=1);

class Alumni extends Model
{
    protected string $table = 'alumni';
    protected array $fillable = [
        'name', 'graduation_year', 'final_class', 'further_education', 'continuation_type',
        'continuation_status', 'continuation_institution', 'employment_status', 'occupation',
        'institution', 'city', 'story', 'achievement', 'photo', 'contact_encrypted',
        'contact_hash', 'consent', 'publish_photo', 'publish_occupation', 'publish_city',
        'status', 'is_featured', 'sort_order', 'submitted_ip_hash', 'approved_at',
    ];

    public function searchPublic(string $term, ?int $year, string $city, string $occupation, int $page = 1): array
    {
        $page = max(1, $page);
        $perPage = 12;
        $where = ["status = 'approved'", 'consent = 1'];
        $params = [];

        if ($term !== '') {
            $where[] = '(name LIKE ? OR institution LIKE ? OR further_education LIKE ?)';
            $like = '%' . $term . '%';
            array_push($params, $like, $like, $like);
        }
        if ($year !== null) {
            $where[] = 'graduation_year = ?';
            $params[] = $year;
        }
        if ($city !== '') {
            $where[] = 'publish_city = 1 AND city = ?';
            $params[] = $city;
        }
        if ($occupation !== '') {
            $where[] = 'publish_occupation = 1 AND occupation = ?';
            $params[] = $occupation;
        }

        $whereSql = implode(' AND ', $where);
        $total = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->table} WHERE {$whereSql}", $params);
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT id, name, graduation_year, final_class, further_education,
                       CASE WHEN publish_occupation = 1 THEN occupation ELSE NULL END AS occupation,
                       CASE WHEN publish_occupation = 1 THEN institution ELSE NULL END AS institution,
                       CASE WHEN publish_city = 1 THEN city ELSE NULL END AS city,
                       CASE WHEN publish_photo = 1 THEN photo ELSE NULL END AS photo,
                       story, achievement, is_featured
                FROM {$this->table}
                WHERE {$whereSql}
                ORDER BY is_featured DESC, sort_order ASC, graduation_year DESC, name ASC
                LIMIT {$perPage} OFFSET {$offset}";

        return [
            'data' => $this->db->fetchAll($sql, $params),
            'current_page' => $page,
            'last_page' => max(1, (int) ceil($total / $perPage)),
            'total' => $total,
        ];
    }

    public function findPublic(int $id): array|false
    {
        return $this->db->fetch(
            "SELECT id, name, graduation_year, final_class, further_education,
                    CASE WHEN publish_occupation = 1 THEN occupation ELSE NULL END AS occupation,
                    CASE WHEN publish_occupation = 1 THEN institution ELSE NULL END AS institution,
                    CASE WHEN publish_city = 1 THEN city ELSE NULL END AS city,
                    CASE WHEN publish_photo = 1 THEN photo ELSE NULL END AS photo,
                    story, achievement, is_featured
             FROM {$this->table}
             WHERE id = ? AND status = 'approved' AND consent = 1",
            [$id]
        );
    }

    public function getAllForAdmin(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table}
             ORDER BY FIELD(status, 'pending', 'approved', 'rejected'), created_at DESC, id DESC"
        );
    }

    public function adminStatistics(): array
    {
        return [
            'continuation' => $this->db->fetchAll(
                "SELECT COALESCE(NULLIF(continuation_type, ''), 'Belum Diisi') AS label, COUNT(*) AS total
                 FROM {$this->table}
                 GROUP BY label ORDER BY total DESC, label ASC"
            ),
            'school_status' => $this->db->fetchAll(
                "SELECT CASE
                    WHEN continuation_type IN ('Bekerja', 'Tidak Melanjutkan') THEN 'Tidak Berlaku'
                    ELSE COALESCE(NULLIF(continuation_status, ''), 'Belum Diisi')
                 END AS label, COUNT(*) AS total
                 FROM {$this->table}
                 GROUP BY label ORDER BY total DESC, label ASC"
            ),
            'employment' => $this->db->fetchAll(
                "SELECT COALESCE(NULLIF(employment_status, ''), 'Belum Diisi') AS label, COUNT(*) AS total
                 FROM {$this->table}
                 GROUP BY label ORDER BY total DESC, label ASC"
            ),
        ];
    }

    public function countRecentByIpHash(string $hash, int $minutes = 60): int
    {
        $cutoff = date('Y-m-d H:i:s', time() - (max(1, $minutes) * 60));
        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->table} WHERE submitted_ip_hash = ? AND created_at >= ?",
            [$hash, $cutoff]
        );
    }

    public function publicFilters(): array
    {
        return [
            'years' => $this->db->fetchAll(
                "SELECT DISTINCT graduation_year value FROM {$this->table}
                 WHERE status = 'approved' AND consent = 1 ORDER BY graduation_year DESC"
            ),
            'cities' => $this->db->fetchAll(
                "SELECT DISTINCT city value FROM {$this->table}
                 WHERE status = 'approved' AND consent = 1 AND publish_city = 1 AND city <> '' ORDER BY city"
            ),
            'occupations' => $this->db->fetchAll(
                "SELECT DISTINCT occupation value FROM {$this->table}
                 WHERE status = 'approved' AND consent = 1 AND publish_occupation = 1 AND occupation <> '' ORDER BY occupation"
            ),
        ];
    }
}
