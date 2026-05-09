<?php
/**
 * ============================================
 * User Model
 * ============================================
 */

declare(strict_types=1);

class User extends Model
{
    protected string $table = 'users';
    protected array $fillable = ['username', 'email', 'password', 'name', 'role', 'avatar', 'is_active', 'permissions'];

    /**
     * All available GTK permissions
     */
    public const GTK_PERMISSIONS = [
        'berita',
        'kategori',
        'galeri',
        'slider',
        'profil',
        'fasilitas',
        'staff',
        'spmb',
        'prestasi'
    ];

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(array $user, string $permission): bool
    {
        // Admin has all permissions
        if (($user['role'] ?? '') === 'admin') {
            return true;
        }

        // GTK checks configurable permissions
        if (($user['role'] ?? '') === 'gtk') {
            $perms = $this->getPermissions($user);
            return in_array($permission, $perms);
        }

        // Murid/Ekskul only have berita and galeri
        if (in_array($user['role'] ?? '', ['murid', 'ekskul'])) {
            return in_array($permission, ['berita', 'galeri']);
        }

        return false;
    }

    /**
     * Get parsed permissions array from user
     */
    public function getPermissions(array $user): array
    {
        if (empty($user['permissions'])) {
            // Default: all permissions for GTK
            if (($user['role'] ?? '') === 'gtk') {
                return self::GTK_PERMISSIONS;
            }
            return [];
        }

        $decoded = is_string($user['permissions'])
            ? json_decode($user['permissions'], true)
            : $user['permissions'];

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Find user by username
     */
    public function findByUsername(string $username): array|false
    {
        return $this->findBy('username', $username);
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): array|false
    {
        return $this->findBy('email', $email);
    }

    /**
     * Authenticate user
     */
    public function authenticate(string $username, string $password): array|false
    {
        $user = $this->findByUsername($username);

        if (!$user || !$user['is_active']) {
            return false;
        }

        if (!Security::verifyPassword($password, $user['password'])) {
            return false;
        }

        // Check if password needs rehash
        if (Security::needsRehash($user['password'])) {
            $newHash = Security::hashPassword($password);
            $this->update($user['id'], ['password' => $newHash]);
        }

        // Update last login
        $this->db->update($this->table, ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);

        // Remove password from return
        unset($user['password']);

        return $user;
    }

    /**
     * Create user with hashed password
     */
    public function createUser(array $data): int|string
    {
        if (isset($data['password'])) {
            $data['password'] = Security::hashPassword($data['password']);
        }

        // Encode permissions to JSON if array
        if (isset($data['permissions']) && is_array($data['permissions'])) {
            $data['permissions'] = json_encode(array_values($data['permissions']));
        }

        return $this->create($data);
    }

    /**
     * Update password
     */
    public function updatePassword(int $id, string $password): int
    {
        $hash = Security::hashPassword($password);
        return $this->db->update($this->table, ['password' => $hash, 'updated_at' => date('Y-m-d H:i:s')], 'id = ?', [$id]);
    }

    /**
     * Get active users
     */
    public function getActive(): array
    {
        return $this->findAllBy('is_active', 1);
    }

    /**
     * Get users by role
     */
    public function getByRole(string $role): array
    {
        return $this->findAllBy('role', $role);
    }
}

