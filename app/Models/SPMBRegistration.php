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
        'reviewed_at',
        'private_payload',
        'privacy_accepted_at'
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
        return 'SPMB' . date('Ym') . strtoupper(bin2hex(random_bytes(16)));
    }

    public const PRIVATE_FIELDS = [
        'student_name', 'nisn', 'nik', 'birth_date', 'birth_place', 'gender', 'religion',
        'address', 'address_village', 'address_district', 'address_city', 'address_province',
        'father_name', 'father_occupation', 'father_phone', 'mother_name', 'mother_occupation',
        'mother_phone', 'email', 'phone', 'previous_school', 'previous_school_npsn',
        'previous_school_address', 'graduation_year', 'documents', 'notes',
    ];

    public static function protect(array $data): array
    {
        $private = array_intersect_key($data, array_flip(self::PRIVATE_FIELDS));
        $data['private_payload'] = DataCipher::encrypt(json_encode($private, JSON_THROW_ON_ERROR));
        foreach ($private as $field => $value) {
            $data[$field] = match ($field) {
                'student_name' => '[Terlindungi]', 'birth_date' => '1900-01-01', 'gender' => 'L',
                default => null,
            };
        }
        return $data;
    }

    public static function reveal(array|false $row): array|false
    {
        if (!$row || empty($row['private_payload'])) {
            return $row;
        }
        $plain = DataCipher::decrypt($row['private_payload']);
        if ($plain === '') {
            throw new RuntimeException('Data SPMB tidak dapat dibuka. Periksa kunci enkripsi.');
        }
        $private = json_decode($plain, true, 512, JSON_THROW_ON_ERROR);
        return array_replace($row, array_intersect_key($private, array_flip(self::PRIVATE_FIELDS)));
    }

    public function create(array $data): int|string
    {
        if (!$this->hasColumn('private_payload')) {
            throw new RuntimeException('Migrasi keamanan SPMB belum diterapkan.');
        }
        return parent::create(self::protect($data));
    }

    public function find(int $id): array|false
    {
        return self::reveal(parent::find($id));
    }

    public function update(int $id, array $data): int
    {
        if (array_intersect(array_keys($data), self::PRIVATE_FIELDS)) {
            if (!$this->hasColumn('private_payload')) {
                throw new RuntimeException('Migrasi keamanan SPMB belum diterapkan.');
            }
            $existing = $this->find($id);
            if (!$existing) { return 0; }
            $private = array_intersect_key($existing, array_flip(self::PRIVATE_FIELDS));
            $data = self::protect(array_replace($private, $data));
        }
        return parent::update($id, $data);
    }

    /**
     * Get registrations by status
     */
    public function getByStatus(string $status): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = ? ORDER BY created_at DESC";
        return array_map([self::class, 'reveal'], $this->db->fetchAll($sql, [$status]));
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
            'data' => array_map([self::class, 'reveal'], $this->db->fetchAll($sql, $params)),
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
        return self::reveal($this->db->fetch($sql, [$number]));
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
