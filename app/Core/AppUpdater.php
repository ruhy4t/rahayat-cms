<?php
/**
 * ============================================
 * Application Updater
 * ============================================
 */

declare(strict_types=1);

class AppUpdater
{
    private string $branch;

    public function __construct()
    {
        $branch = (string) UPDATE_BRANCH;
        $this->branch = preg_match('/^[A-Za-z0-9._\/-]+$/', $branch) ? $branch : 'main';
    }

    public function getStatus(): array
    {
        $manualStatus = $this->getManualStatus();
        $gitVersion = $this->run(['git', '--version']);
        $isRepo = is_dir(ROOT_PATH . '/.git');
        $currentCommit = $gitVersion['success'] && $isRepo ? trim($this->git(['rev-parse', 'HEAD'])['output']) : '';
        $currentBranch = $gitVersion['success'] && $isRepo ? trim($this->git(['rev-parse', '--abbrev-ref', 'HEAD'])['output']) : '';
        $remoteUrl = $gitVersion['success'] && $isRepo ? trim($this->git(['config', '--get', 'remote.origin.url'])['output']) : '';
        $fetch = $gitVersion['success'] && $isRepo ? $this->git(['fetch', '--quiet', 'origin', $this->branch]) : ['success' => false, 'error' => ''];
        $remoteCommit = '';
        if ($fetch['success']) {
            $remoteCommit = trim($this->git(['rev-parse', 'origin/' . $this->branch])['output']);
        }
        $relation = $this->compareCommits($currentCommit, $remoteCommit);
        $status = $gitVersion['success'] && $isRepo ? $this->git(['status', '--porcelain', '--untracked-files=no']) : ['success' => false, 'output' => ''];
        $dirty = trim($status['output'] ?? '') !== '';
        $updateAvailable = $relation === 'behind';
        $manualUpdateAvailable = (bool) ($manualStatus['update_available'] ?? false);

        return [
            'enabled' => UPDATE_ENABLED,
            'local_version' => APP_VERSION,
            'git_available' => $gitVersion['success'],
            'git_version' => trim($gitVersion['output'] ?: $gitVersion['error']),
            'is_repository' => $isRepo,
            'branch' => $currentBranch ?: $this->branch,
            'target_branch' => $this->branch,
            'remote_url' => $this->sanitizeRemoteUrl($remoteUrl),
            'current_commit' => $currentCommit,
            'current_short' => $currentCommit ? substr($currentCommit, 0, 12) : '-',
            'remote_commit' => $remoteCommit,
            'remote_short' => $remoteCommit ? substr($remoteCommit, 0, 12) : '-',
            'remote_error' => $fetch['success'] ? '' : trim($fetch['error']),
            'relation' => $relation,
            'dirty' => $dirty,
            'update_available' => $updateAvailable || $manualUpdateAvailable,
            'git_update_available' => $updateAvailable,
            'manual_update' => $manualStatus,
            'can_update' => UPDATE_ENABLED && $gitVersion['success'] && $isRepo && !$dirty && $updateAvailable,
        ];
    }

    public function update(): array
    {
        $status = $this->getStatus();

        if (!$status['enabled']) {
            return $this->result(false, 'Fitur update belum diaktifkan. Set UPDATE_ENABLED=true di environment hosting.');
        }
        if (!$status['git_available']) {
            return $this->result(false, 'Git tidak tersedia di hosting.');
        }
        if (!$status['is_repository']) {
            return $this->result(false, 'Folder aplikasi bukan Git repository.');
        }
        if ($status['dirty']) {
            return $this->result(false, 'Update dibatalkan karena ada perubahan lokal pada file aplikasi. Commit atau rapikan dulu perubahan tersebut.');
        }
        if (!$status['remote_commit']) {
            return $this->result(false, 'Tidak bisa membaca commit terbaru dari remote. Pastikan server bisa mengakses GitHub.');
        }
        if (!$status['update_available']) {
            return $this->result(true, 'Aplikasi sudah menggunakan versi terbaru.');
        }

        $fetch = $this->git(['fetch', '--prune', 'origin', $this->branch]);
        if (!$fetch['success']) {
            return $this->result(false, 'Gagal mengambil update dari GitHub.', $fetch);
        }

        $pull = $this->git(['pull', '--ff-only', 'origin', $this->branch]);
        if (!$pull['success']) {
            return $this->result(false, 'Gagal menerapkan update. Pastikan tidak ada commit lokal yang berbeda dari remote.', $pull);
        }

        return $this->result(true, 'Update berhasil diterapkan.', $pull);
    }

    private function git(array $args): array
    {
        return $this->run(array_merge(['git'], $args));
    }

    private function getManualStatus(): array
    {
        $manifest = $this->fetchRemoteManifest();
        $downloadUrl = defined('UPDATE_DOWNLOAD_URL') ? UPDATE_DOWNLOAD_URL : '';

        if (!$manifest['success']) {
            return [
                'can_check' => false,
                'local_version' => APP_VERSION,
                'remote_version' => '',
                'update_available' => false,
                'download_url' => $downloadUrl,
                'repository_url' => 'https://github.com/ruhy4t/rahayat-cms',
                'notes_url' => 'https://github.com/ruhy4t/rahayat-cms/commits/main',
                'error' => $manifest['error'],
            ];
        }

        $data = $manifest['data'];
        $remoteVersion = (string) ($data['version'] ?? '');
        return [
            'can_check' => true,
            'local_version' => APP_VERSION,
            'remote_version' => $remoteVersion,
            'update_available' => $remoteVersion !== '' && version_compare($remoteVersion, APP_VERSION, '>'),
            'download_url' => (string) ($data['download_url'] ?? $downloadUrl),
            'repository_url' => (string) ($data['repository_url'] ?? 'https://github.com/ruhy4t/rahayat-cms'),
            'notes_url' => (string) ($data['notes_url'] ?? 'https://github.com/ruhy4t/rahayat-cms/commits/main'),
            'error' => '',
        ];
    }

    private function fetchRemoteManifest(): array
    {
        $url = defined('UPDATE_CHECK_URL') ? (string) UPDATE_CHECK_URL : '';
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return ['success' => false, 'data' => [], 'error' => 'URL cek pembaruan tidak valid.'];
        }

        $body = '';
        if (function_exists('curl_init')) {
            $curl = curl_init($url);
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_USERAGENT => 'RahayatCMS/' . APP_VERSION,
            ]);
            $body = (string) curl_exec($curl);
            $error = curl_error($curl);
            $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
            curl_close($curl);

            if ($body === '' || $status >= 400) {
                return ['success' => false, 'data' => [], 'error' => $error ?: 'GitHub memberi respons HTTP ' . $status . '.'];
            }
        } else {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'header' => "User-Agent: RahayatCMS/" . APP_VERSION . "\r\n",
                ],
            ]);
            $body = (string) @file_get_contents($url, false, $context);
            if ($body === '') {
                return ['success' => false, 'data' => [], 'error' => 'Server tidak bisa membaca file versi dari GitHub.'];
            }
        }

        $data = json_decode($body, true);
        if (!is_array($data)) {
            return ['success' => false, 'data' => [], 'error' => 'Format file versi dari GitHub tidak valid.'];
        }

        return ['success' => true, 'data' => $data, 'error' => ''];
    }

    private function run(array $command): array
    {
        if (!function_exists('proc_open')) {
            return [
                'success' => false,
                'code' => 127,
                'output' => '',
                'error' => 'proc_open tidak tersedia di server.',
            ];
        }

        $previousPrompt = getenv('GIT_TERMINAL_PROMPT');
        putenv('GIT_TERMINAL_PROMPT=0');

        $pipes = [];
        $process = @proc_open(
            $command,
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            ROOT_PATH,
            null,
            ['bypass_shell' => true]
        );

        if (!is_resource($process)) {
            $this->restoreGitPrompt($previousPrompt);
            return [
                'success' => false,
                'code' => 127,
                'output' => '',
                'error' => 'Gagal menjalankan perintah Git.',
            ];
        }

        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]) ?: '';
        $error = stream_get_contents($pipes[2]) ?: '';
        fclose($pipes[1]);
        fclose($pipes[2]);

        $code = proc_close($process);
        $this->restoreGitPrompt($previousPrompt);

        return [
            'success' => $code === 0,
            'code' => $code,
            'output' => trim($output),
            'error' => trim($error),
        ];
    }

    private function restoreGitPrompt(string|false $previousPrompt): void
    {
        if ($previousPrompt === false) {
            putenv('GIT_TERMINAL_PROMPT');
            return;
        }

        putenv('GIT_TERMINAL_PROMPT=' . $previousPrompt);
    }

    private function compareCommits(string $currentCommit, string $remoteCommit): string
    {
        if ($currentCommit === '' || $remoteCommit === '') {
            return 'unknown';
        }
        if ($currentCommit === $remoteCommit) {
            return 'same';
        }

        $currentIsAncestor = $this->git(['merge-base', '--is-ancestor', $currentCommit, $remoteCommit])['success'];
        $remoteIsAncestor = $this->git(['merge-base', '--is-ancestor', $remoteCommit, $currentCommit])['success'];

        return match (true) {
            $currentIsAncestor && !$remoteIsAncestor => 'behind',
            $remoteIsAncestor && !$currentIsAncestor => 'ahead',
            default => 'diverged',
        };
    }

    private function sanitizeRemoteUrl(string $url): string
    {
        return preg_replace('#https://[^/@]+@#', 'https://', $url) ?? $url;
    }

    private function result(bool $success, string $message, array $command = []): array
    {
        return [
            'success' => $success,
            'message' => $message,
            'output' => trim(($command['output'] ?? '') . "\n" . ($command['error'] ?? '')),
        ];
    }
}
