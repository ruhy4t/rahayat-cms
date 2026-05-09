<?php
/**
 * ============================================
 * SPMBRegistration Model
 * Sistem Penerimaan Murid Baru
 * ============================================
 */

declare(strict_types=1);

class SPMBRegistration extends Model
{
    protected string $table = 'spmb_registrations';
    protected array $fillable = [
        'registration_number',
        'student_name',
        'nisn',
        'nik',
        'birth_date',
        'birth_place',
        'gender',
        'religion',
        'address',
        'address_village',
        'address_district',
        'address_city',
        'address_province',
        'father_name',
        'father_occupation',
        'father_phone',
        'mother_name',
        'mother_occupation',
        'mother_phone',
        'email',
        'phone',
        'previous_school',
        'previous_school_npsn',
        'previous_school_address',
        'graduation_year',
        'documents',
        'status',
        'notes',
        'reviewed_by',
        'reviewed_at'
    ];

    /**
     * Status labels
     */
    public const STATUS_LABELS = [
        'pending' => 'Menunggu',
        'review' => 'Dalam Review',
        'accepted' => 'Diterima',
        'rejected' => 'Ditolak'
    ];

    /**
     * Status colors for UI
     */
    public const STATUS_COLORS = [
        'pending' => 'yellow',
        'review' => 'blue',
        'accepted' => 'green',
        'rejected' => 'red'
    ];

    /**
     * Generate unique registration number
     */
    public function generateRegistrationNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "SPMB{$year}{$month}";

        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE registration_number LIKE ?";
        $result = $this->db->fetch($sql, ["{$prefix}%"]);
        $count = ($result['count'] ?? 0) + 1;

        return $prefix . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get registrations by status
     */
    public function getByStatus(string $status): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = ? ORDER BY created_at DESC";
        return $this->db->fetchAll($sql, [$status]);
    }

    /**
     * Get all registrations with pagination
     */
    public function paginateByStatus(int $page = 1, int $perPage = 20, ?string $status = null): array
    {
        $page = max(1, $page);
        $perPage = min(100, max(1, $perPage));
        $offset = ($page - 1) * $perPage;

        $status = $status && array_key_exists($status, self::STATUS_LABELS) ? $status : null;
        $where = $status ? "WHERE status = ?" : "";
        $params = $status ? [$status] : [];

        $countSql = "SELECT COUNT(*) as total FROM {$this->table} {$where}";
        $countResult = $this->db->fetch($countSql, $params);
        $total = $countResult['total'] ?? 0;

        $sql = "SELECT r.*, u.name as reviewer_name 
                FROM {$this->table} r 
                LEFT JOIN users u ON r.reviewed_by = u.id
                {$where}
                ORDER BY r.created_at DESC 
                LIMIT {$perPage} OFFSET {$offset}";

        return [
            'data' => $this->db->fetchAll($sql, $params),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage)
        ];
    }

    /**
     * Update status
     */
    public function updateStatus(int $id, string $status, int $reviewerId, ?string $notes = null): bool
    {
        return (bool) $this->update($id, [
            'status' => $status,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => date('Y-m-d H:i:s'),
            'notes' => $notes
        ]);
    }

    /**
     * Get registration by registration number (for public status check)
     */
    public function findByRegistrationNumber(string $number): array|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE registration_number = ?";
        return $this->db->fetch($sql, [$number]);
    }

    /**
     * Get statistics
     */
    public function getStats(): array
    {
        $sql = "SELECT status, COUNT(*) as count FROM {$this->table} GROUP BY status";
        $results = $this->db->fetchAll($sql);

        $stats = [
            'pending' => 0,
            'review' => 0,
            'accepted' => 0,
            'rejected' => 0,
            'total' => 0
        ];

        foreach ($results as $row) {
            $stats[$row['status']] = (int) $row['count'];
            $stats['total'] += (int) $row['count'];
        }

        return $stats;
    }

    /**
     * Parse documents JSON
     */
    public function getDocuments(int $id): array
    {
        $registration = $this->find($id);
        if (!$registration || empty($registration['documents'])) {
            return [];
        }

        return json_decode($registration['documents'], true) ?? [];
    }
}
