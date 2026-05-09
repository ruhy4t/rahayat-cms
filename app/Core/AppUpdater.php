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

        return [
            'enabled' => UPDATE_ENABLED,
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
            'update_available' => $updateAvailable,
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
