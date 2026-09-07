<?php

declare(strict_types=1);

final class SecurityAudit
{
    public static function record(string $event, ?int $actorId = null, ?string $resource = null): void
    {
        $directory = STORAGE_PATH . '/logs';
        if (!is_dir($directory) && !@mkdir($directory, 0750, true) && !is_dir($directory)) {
            error_log('Security audit storage unavailable.');
            return;
        }
        // Never log names, identifiers, credentials, document contents, or IPs.
        $entry = ['at' => gmdate('c'), 'event' => $event, 'actor_id' => $actorId,
            'resource_hash' => $resource !== null ? hash('sha256', $resource) : null];
        if (@file_put_contents($directory . '/security-' . gmdate('Y-m-d') . '.jsonl',
            json_encode($entry, JSON_THROW_ON_ERROR) . "\n", FILE_APPEND | LOCK_EX) === false) {
            error_log('Security audit write failed.');
        }
    }
}
