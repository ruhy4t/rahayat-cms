<?php

declare(strict_types=1);

final class AuthSession
{
    private static bool $checked = false;
    private static ?array $user = null;

    public static function establish(array $user): void
    {
        $fresh = (new User())->find((int) $user['id']);
        if (!$fresh || empty($fresh['is_active'])) {
            throw new RuntimeException('Akun tidak aktif.');
        }
        session_regenerate_id(true);
        $_SESSION['_auth_fingerprint'] = self::fingerprint($fresh);
        $_SESSION['_auth_started'] = $_SESSION['_auth_seen'] = time();
        unset($fresh['password']);
        $_SESSION['user_id'] = $fresh['id'];
        $_SESSION['user'] = $fresh;
        self::$user = $fresh;
        self::$checked = true;
    }

    public static function fingerprint(array $user): string
    {
        return hash('sha256', json_encode([
            $user['id'], $user['password'], $user['role'],
            $user['permissions'] ?? null, (int) ($user['is_spmb_committee'] ?? 0),
            (int) $user['is_active'],
        ], JSON_THROW_ON_ERROR));
    }

    public static function current(): ?array
    {
        if (self::$checked) {
            return self::$user;
        }
        self::$checked = true;
        if (empty($_SESSION['user_id'])) {
            return null;
        }
        $now = time();
        $idle = max(60, (int) (getenv('SESSION_IDLE_SECONDS') ?: 1800));
        $absolute = max($idle, (int) (getenv('SESSION_MAX_SECONDS') ?: 28800));
        $user = (new User())->find((int) $_SESSION['user_id']);
        if (!$user || empty($user['is_active'])
            || $now - (int) ($_SESSION['_auth_seen'] ?? 0) > $idle
            || $now - (int) ($_SESSION['_auth_started'] ?? 0) > $absolute
            || !hash_equals(self::fingerprint($user), (string) ($_SESSION['_auth_fingerprint'] ?? ''))) {
            unset($_SESSION['user_id'], $_SESSION['user'], $_SESSION['_auth_fingerprint'],
                $_SESSION['_auth_started'], $_SESSION['_auth_seen']);
            session_regenerate_id(true);
            return null;
        }
        $_SESSION['_auth_seen'] = $now;
        unset($user['password']);
        $_SESSION['user'] = $user;
        return self::$user = $user;
    }

    public static function canReadSpmb(?array $user): bool
    {
        return $user !== null && !empty($user['is_active']) && (
            ($user['role'] ?? '') === 'admin'
            || (($user['role'] ?? '') === 'gtk' && !empty($user['is_spmb_committee'])
                && (new User())->hasPermission($user, 'spmb'))
        );
    }
}
