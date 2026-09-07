<?php

declare(strict_types=1);
require __DIR__ . '/security-bootstrap.php';

// Runs against the local development server using temporary synthetic records.
$base = 'http://rahayat-cms.test';
$db = Database::getInstance();
$suffix = bin2hex(random_bytes(8));
$password = 'Synthetic-' . $suffix;
$users = [];
$newsIds = [];
$registrationId = null;
$file = STORAGE_PATH . '/spmb/http-test-' . $suffix . '.pdf';
$checks = 0;
function verify(bool $result, string $message): void {
    global $checks;
    if (!$result) { throw new RuntimeException($message); }
    $checks++;
}
function client(): CurlHandle {
    $handle = curl_init();
    curl_setopt_array($handle, [CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEFILE => '', CURLOPT_TIMEOUT => 20]);
    return $handle;
}
function request(CurlHandle $handle, string $path, ?array $data = null): array {
    global $base;
    curl_setopt_array($handle, [CURLOPT_URL => $base . $path, CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_POST => $data !== null, CURLOPT_HTTPGET => $data === null]);
    if ($data !== null) { curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($data)); }
    $headers = '';
    curl_setopt($handle, CURLOPT_HEADERFUNCTION, static function ($handle, $line) use (&$headers) { $headers .= $line; return strlen($line); });
    $body = curl_exec($handle);
    if ($body === false) { throw new RuntimeException('HTTP transport failed.'); }
    return ['status' => curl_getinfo($handle, CURLINFO_RESPONSE_CODE), 'body' => $body, 'headers' => $headers];
}
function token(string $html): string {
    if (preg_match('/name="(?:csrf_token|_csrf_token)" value="([a-f0-9]+)"/', $html, $match)
        || preg_match('/name="csrf-token" content="([a-f0-9]+)"/', $html, $match)) { return $match[1]; }
    throw new RuntimeException('CSRF token not found.');
}
try {
    $roles = [
        'admin' => ['role' => 'admin'], 'student' => ['role' => 'murid'],
        'committee' => ['role' => 'gtk', 'is_spmb_committee' => 1, 'permissions' => '["spmb"]'],
        'other-gtk' => ['role' => 'gtk', 'is_spmb_committee' => 0, 'permissions' => '["spmb"]'],
    ];
    foreach ($roles as $label => $role) {
        $username = 'http-' . $label . '-' . $suffix;
        $id = (int) $db->insert('users', array_merge(['username' => $username, 'email' => $username . '@example.invalid',
            'name' => 'HTTP synthetic test', 'password' => Security::hashPassword($password), 'is_active' => 1], $role));
        $users[$label] = ['id' => $id, 'username' => $username];
    }
    $bytes = "%PDF-1.4\nSYNTHETIC HTTP TEST\n";
    file_put_contents($file, $bytes);
    PrivateDocument::encryptFile($file);
    $path = '/index.php?url=storage/spmb/' . basename($file);
    $anonymous = client();
    foreach (['storage/spmb/', 'storage/./spmb/', 'storage/SPMB/', 'storage/spmb./'] as $variant) {
        $result = request($anonymous, '/index.php?url=' . $variant . basename($file));
        verify(in_array($result['status'], [403, 404], true), 'Anonymous document bypass: ' . $variant);
        verify(!str_contains($result['body'], 'SYNTHETIC HTTP TEST'), 'Private bytes leaked');
    }
    foreach ($users as $label => $user) {
        $handle = client();
        $login = request($handle, '/login');
        request($handle, '/login', [CSRF_TOKEN_NAME => token($login['body']), 'username' => $user['username'], 'password' => $password]);
        $result = request($handle, $path);
        $allowed = in_array($label, ['admin', 'committee'], true);
        verify($result['status'] === ($allowed ? 200 : 403), 'Role authorization failed: ' . $label);
        if ($allowed) {
            verify($result['body'] === $bytes, 'Decrypted HTTP document differs');
            verify(str_contains($result['headers'], 'private, no-store'), 'Private cache header missing');
        }
        $migration = request($handle, '/admin/pembaruan/migrasi');
        verify(str_contains($migration['body'], 'name="backup_ready"') === ($label === 'admin'), 'Migration admin isolation failed');
        if ($label === 'admin') {
            verify($migration['status'] === 200, 'Migration page failed to render');
            verify(request($handle, '/admin/pembaruan/migrasi/run')['status'] === 405, 'Migration permits GET mutation');
            verify(request($handle, '/admin/pembaruan/migrasi/run', ['backup_ready' => '1', CSRF_TOKEN_NAME => 'invalid'])['status'] === 403, 'Migration accepts invalid CSRF');
            $missingBackup = request($handle, '/admin/pembaruan/migrasi/run', [CSRF_TOKEN_NAME => token($migration['body'])]);
            verify(str_contains($missingBackup['body'], 'Pastikan backup lengkap'), 'Migration backup acknowledgement missing');
            curl_setopt($handle, CURLOPT_HTTPHEADER, ['X-Requested-With: XMLHttpRequest']);
            $ajaxInvalid = request($handle, '/admin/pembaruan/migrasi/run', ['backup_ready' => '1', CSRF_TOKEN_NAME => 'invalid']);
            verify($ajaxInvalid['status'] === 403 && json_decode($ajaxInvalid['body'], true)['success'] === false, 'Migration AJAX CSRF response invalid');
            $ajaxMissing = request($handle, '/admin/pembaruan/migrasi/run', [CSRF_TOKEN_NAME => token($migration['body'])]);
            verify($ajaxMissing['status'] === 422 && json_decode($ajaxMissing['body'], true)['success'] === false, 'Migration AJAX acknowledgement response invalid');
            curl_setopt($handle, CURLOPT_HTTPHEADER, []);
            $db->update('users', ['is_active' => 0], 'id = ?', [$user['id']]);
            verify(request($handle, $path)['status'] === 403, 'Disabled user still downloads');
        }
    }
    $news = new News();
    foreach (['draft', 'future'] as $kind) {
        $id = (int) $news->create(['title' => 'Private HTTP ' . $suffix, 'slug' => 'http-' . $kind . '-' . $suffix,
            'content' => 'Synthetic private news', 'status' => $kind === 'draft' ? 'draft' : 'published',
            'published_at' => date('Y-m-d H:i:s', time() + 86400), 'author_id' => $users['student']['id']]);
        $newsIds[] = $id;
        verify(request($anonymous, '/api/news/' . $id)['status'] === 404, 'Private API news readable');
    }
    verify(!str_contains(request($anonymous, '/api/news?per_page=100')['body'], $suffix), 'Private API list leak');
    verify(!str_contains(request($anonymous, '/berita')['body'], 'Private HTTP ' . $suffix), 'Private frontend list leak');

    $spmb = new SPMBRegistration();
    $number = $spmb->generateRegistrationNumber();
    $name = 'SYNTHETIC-' . $suffix;
    $registrationId = (int) $spmb->create(['registration_number' => $number, 'student_name' => $name,
        'nisn' => '0000000001', 'nik' => '0000000000000001', 'birth_date' => '2013-02-03', 'gender' => 'P',
        'status' => 'pending', 'notes' => 'SECRET-NOTE-' . $suffix, 'previous_school' => 'SECRET-SCHOOL-' . $suffix]);
    $page = request($anonymous, '/spmb/cek-status?nomor=' . $number);
    verify(!str_contains($page['body'], $name), 'GET number exposes identity');
    $data = [CSRF_TOKEN_NAME => token($page['body']), 'registration_number' => $number, 'nisn' => '0000000001', 'birth_date' => '2013-02-03'];
    $wrong = request($anonymous, '/spmb/cek-status', array_replace($data, ['nisn' => '0000000002']));
    verify(!str_contains($wrong['body'], $name), 'Wrong identity verification accepted');
    $correct = request($anonymous, '/spmb/cek-status', $data);
    verify(str_contains($correct['body'], $name), 'Correct identity verification rejected');
    verify(!str_contains($correct['body'], 'SECRET-NOTE-') && !str_contains($correct['body'], 'SECRET-SCHOOL-'), 'Excessive status data exposed');
    verify(str_contains($correct['body'], 'name="content-protection" content="true"'), 'Private page copy/print protection lost');
} finally {
    if ($registrationId) { $db->delete('spmb_registrations', 'id = ?', [$registrationId]); }
    foreach ($newsIds as $id) { $db->delete('news', 'id = ?', [$id]); }
    foreach ($users as $user) { $db->delete('users', 'id = ?', [$user['id']]); }
    if (is_file($file)) { unlink($file); }
}
echo "PASS: {$checks} HTTP security checks; synthetic records removed.\n";
