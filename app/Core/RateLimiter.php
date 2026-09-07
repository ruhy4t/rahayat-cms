<?php

declare(strict_types=1);

final class RateLimiter
{
    // Trusted reverse proxies must configure mod_remoteip or its equivalent.
    // Never accept an IP identity supplied directly in client headers.
    public static function clientIp(): string
    {
        return (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    }

    public static function hit(string $key, int $limit, int $window): bool
    {
        $directory = STORAGE_PATH . '/rate_limits';
        if (!is_dir($directory) && !@mkdir($directory, 0750, true) && !is_dir($directory)) {
            return true;
        }
        $handle = @fopen($directory . '/request_' . hash('sha256', $key) . '.json', 'c+');
        if (!$handle) {
            return true;
        }
        try {
            if (!flock($handle, LOCK_EX)) {
                return true;
            }
            $now = time();
            $state = json_decode(stream_get_contents($handle) ?: '', true);
            if (!is_array($state) || (int) ($state['until'] ?? 0) <= $now) {
                $state = ['until' => $now + $window, 'count' => 0];
            }
            $limited = (int) $state['count'] >= $limit;
            if (!$limited) {
                $state['count']++;
            }
            rewind($handle);
            ftruncate($handle, 0);
            if (fwrite($handle, json_encode($state, JSON_THROW_ON_ERROR)) === false) {
                return true;
            }
            return $limited;
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
}
