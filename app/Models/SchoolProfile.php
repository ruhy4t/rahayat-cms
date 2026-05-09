<?php
/**
 * ============================================
 * SchoolProfile Model
 * ============================================
 */

declare(strict_types=1);

class SchoolProfile extends Model
{
    protected string $table = 'school_profile';
    protected array $fillable = [
        'name',
        'npsn',
        'address',
        'phone',
        'email',
        'website',
        'principal_name',
        'principal_nip',
        'logo',
        'vision',
        'mission',
        'motto',
        'history',
        'accreditation',
        'established_year',
        'school_type',
        'monday_open',
        'monday_close',
        'is_closed_monday',
        'tuesday_open',
        'tuesday_close',
        'is_closed_tuesday',
        'wednesday_open',
        'wednesday_close',
        'is_closed_wednesday',
        'thursday_open',
        'thursday_close',
        'is_closed_thursday',
        'friday_open',
        'friday_close',
        'is_closed_friday',
        'saturday_open',
        'saturday_close',
        'is_closed_saturday',
        'sunday_open',
        'sunday_close',
        'is_closed_sunday',
        'total_students',
        'total_teachers',
        'graduation_rate',
        'google_maps_embed',
        'principal_photo',
        'welcome_message',
        'principal_quote',
        'spmb_link',
        'tagline',
        'watermark_enabled'
    ];

    /**
     * Get school profile (singleton pattern - only one record)
     */
    public function getProfile(): array|false
    {
        $sql = "SELECT * FROM {$this->table} LIMIT 1";
        return $this->db->fetch($sql);
    }

    /**
     * Update or create profile
     */
    public function saveProfile(array $data): bool
    {
        $existing = $this->getProfile();

        if ($existing) {
            $this->update((int) $existing['id'], $data);
        } else {
            $this->create($data);
        }

        return true;
    }

    /**
     * Get setting value
     */
    public function getSetting(string $key): mixed
    {
        $profile = $this->getProfile();
        return $profile[$key] ?? null;
    }
}
