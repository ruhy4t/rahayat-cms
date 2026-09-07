<?php

declare(strict_types=1);
require __DIR__ . '/security-bootstrap.php';

$checks = 0;
function check(bool $value, string $message): void {
    global $checks;
    if (!$value) { throw new RuntimeException($message); }
    $checks++;
}
function nextRequest(): void {
    foreach (['checked' => false, 'user' => null] as $property => $value) {
        (new ReflectionProperty(AuthSession::class, $property))->setValue(null, $value);
    }
}
$workspace = STORAGE_PATH . '/cache/security-test-' . bin2hex(random_bytes(8));
mkdir($workspace, 0700, true);
session_save_path($workspace);
session_start();
$pdo = Database::getInstance()->getConnection();
$pdo->beginTransaction();
$rateFile = null;
try {
    foreach ([1, 2, 10] as $depth) {
        $html = str_repeat('<section>', $depth) . '<img src=x onerror="window.__audit=1">' . str_repeat('</section>', $depth);
        $clean = Security::sanitizeHtml($html);
        check(!str_contains($clean, 'onerror'), 'Nested sanitizer bypass');
        check($clean === Security::sanitizeHtml($clean), 'Sanitizer is not idempotent');
    }
    foreach (['javascript:alert(1)', "java\nscript:alert(1)", '//example.invalid/x', '/\\example.invalid'] as $url) {
        check(!str_contains(Security::sanitizeHtml('<a href="' . $url . '">link</a>'), 'href='), 'Unsafe URL survives');
    }
    check(str_contains(Security::sanitizeHtml('<p><strong>Valid</strong><a href="https://example.com">link</a></p>'), '<strong>Valid</strong>'), 'Valid HTML lost');
    check(!str_contains(Security::sanitizeHtml('<iframe src="https://example.com/storage/uploads/news/x.pdf"></iframe>'), '<iframe'), 'External PDF iframe survives');

    $key = 'regression|' . bin2hex(random_bytes(8));
    $rateFile = STORAGE_PATH . '/rate_limits/request_' . hash('sha256', $key) . '.json';
    check(!RateLimiter::hit($key, 2, 600), 'First request blocked');
    check(!RateLimiter::hit($key, 2, 600), 'Second request blocked');
    $_SESSION = [];
    check(RateLimiter::hit($key, 2, 600), 'Rate limit reset by session');
    $_SERVER['REMOTE_ADDR'] = '192.0.2.4';
    $_SERVER['HTTP_CF_CONNECTING_IP'] = $_SERVER['HTTP_X_FORWARDED_FOR'] = '192.0.2.99';
    check(RateLimiter::clientIp() === '192.0.2.4', 'Untrusted proxy header accepted');

    $identity = 'audit-' . bin2hex(random_bytes(6));
    $userId = (int) Database::getInstance()->insert('users', ['username' => $identity, 'email' => $identity . '@example.invalid',
        'name' => 'Synthetic Audit', 'password' => password_hash('Synthetic-only-Password', PASSWORD_DEFAULT), 'role' => 'admin', 'is_active' => 1]);
    $users = new User();
    AuthSession::establish($users->find($userId));
    $session = $_SESSION;
    nextRequest();
    check(AuthSession::current()['id'] === $userId, 'Valid session rejected');
    check(AuthSession::canReadSpmb(AuthSession::current()), 'Admin denied SPMB');
    check(!AuthSession::canReadSpmb(['id' => $userId, 'role' => 'murid', 'is_active' => 1]), 'Student allowed SPMB');
    check(!AuthSession::canReadSpmb(['id' => $userId, 'role' => 'gtk', 'is_active' => 1, 'is_spmb_committee' => 0]), 'Noncommittee allowed SPMB');
    check(AuthSession::canReadSpmb(['id' => $userId, 'role' => 'gtk', 'is_active' => 1, 'is_spmb_committee' => 1, 'permissions' => '["spmb"]']), 'Committee denied SPMB');
    foreach (['is_active' => 0, 'role' => 'murid', 'password' => 'changed', 'permissions' => '[]'] as $field => $value) {
        $original = $users->find($userId)[$field];
        Database::getInstance()->update('users', [$field => $value], 'id = ?', [$userId]);
        $_SESSION = $session; nextRequest();
        check(AuthSession::current() === null, 'Changed account retained session: ' . $field);
        Database::getInstance()->update('users', [$field => $original], 'id = ?', [$userId]);
    }
    $_SESSION = $session; $_SESSION['_auth_seen'] = time() - 86400; nextRequest();
    check(AuthSession::current() === null, 'Expired session accepted');

    $spmb = new SPMBRegistration();
    $number = $spmb->generateRegistrationNumber();
    check(strlen($number) <= 50 && $number !== $spmb->generateRegistrationNumber(), 'Invalid random registration number');
    $data = ['registration_number' => $number, 'student_name' => 'Synthetic Student', 'nisn' => '0012345678', 'nik' => '0012345678901234',
        'birth_date' => '2013-01-02', 'birth_place' => 'Synthetic City', 'gender' => 'P', 'address' => 'Synthetic Address',
        'address_village' => 'Village', 'address_district' => 'District', 'address_city' => 'City', 'address_province' => 'Province',
        'previous_school_npsn' => '12345678', 'status' => 'pending', 'notes' => 'Internal private note'];
    check(SPMBValidation::errors($data) === [], 'Valid registration rejected');
    check(isset(SPMBValidation::errors(array_replace($data, ['nik' => 'bad']))['nik']), 'Invalid NIK accepted');
    check(isset(SPMBValidation::errors(array_replace($data, ['birth_date' => '2026-02-30']))['birth_date']), 'Invalid birth date accepted');
    $id = (int) $spmb->create($data);
    $raw = Database::getInstance()->fetch('SELECT * FROM spmb_registrations WHERE id = ?', [$id]);
    check($raw['nik'] === null && $raw['student_name'] === '[Terlindungi]' && str_starts_with($raw['private_payload'], 'v1:'), 'Private data stored in cleartext');
    $revealed = $spmb->find($id);
    check($revealed['nik'] === $data['nik'] && $revealed['birth_date'] === $data['birth_date'], 'Encrypted record round trip failed');
    $spmb->updateStatus($id, 'review', $userId, 'New internal note');
    check($spmb->find($id)['notes'] === 'New internal note' && $spmb->find($id)['nisn'] === $data['nisn'], 'Status update lost private fields');
    check($spmb->findByRegistrationNumber($number)['student_name'] === $data['student_name'], 'Status lookup failed');

    $document = $workspace . '/test.pdf';
    $binary = "%PDF-1.4\nSYNTHETIC ONLY\x00\x01\n ";
    file_put_contents($document, $binary);
    PrivateDocument::encryptFile($document);
    check(!str_contains(file_get_contents($document), 'SYNTHETIC'), 'Document stored as plaintext');
    check(PrivateDocument::read($document) === $binary, 'Document bytes changed');
    PrivateDocument::encryptFile($document);
    check(PrivateDocument::read($document) === $binary, 'Repeated encryption broke document');

    $news = new News();
    $draft = (int) $news->create(['title' => 'Synthetic draft', 'slug' => $identity, 'content' => 'Private draft', 'author_id' => $userId, 'status' => 'draft']);
    $future = (int) $news->create(['title' => 'Synthetic future', 'slug' => $identity . '-future', 'content' => 'Future', 'author_id' => $userId,
        'status' => 'published', 'published_at' => date('Y-m-d H:i:s', time() + 86400)]);
    check(!$news->isPublic($news->find($draft)) && !$news->isPublic($news->find($future)), 'Private news treated as public');
    $public = $news->paginatePublic(1, 100);
    check(!in_array($draft, array_column($public['data'], 'id')) && !in_array($future, array_column($public['data'], 'id')), 'Private news leaked in listing');
    check(!isset($public['data'][0]['content']), 'Public list includes complete body');
    $users->delete($userId); $_SESSION = $session; nextRequest();
    check(AuthSession::current() === null, 'Deleted user retained session');

    $profile = ['name' => 'Synthetic School'];
    $quota = 0;
    $selectedDocuments = [];
    $submissionToken = bin2hex(random_bytes(24));
    ob_start();
    require VIEW_PATH . '/frontend/spmb/register.php';
    $form = ob_get_clean();
    check(str_contains($form, 'name="submission_token" value="' . $submissionToken . '"'), 'Submission token absent from form');
    check(str_contains($form, 'id="spmbErrors" role="alert"'), 'Accessible form feedback absent');
    check(!str_contains($form, 'localStorage.setItem'), 'Registration persisted on shared device');
    preg_match_all('/<script[^>]*>([\s\S]*?)<\/script>/', $form, $scripts);
    $jsFile = $workspace . '/form.js';
    file_put_contents($jsFile, implode("\n", $scripts[1]));
    $process = proc_open(['node', '--check', $jsFile], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
    if (!is_resource($process)) { throw new RuntimeException('Node required to check form JavaScript.'); }
    $output = stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
    fclose($pipes[1]); fclose($pipes[2]);
    check(proc_close($process) === 0, 'Registration JavaScript syntax failed: ' . $output);
} finally {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    session_destroy();
    if ($rateFile && is_file($rateFile)) { unlink($rateFile); }
    foreach (glob($workspace . '/*') ?: [] as $file) { unlink($file); }
    rmdir($workspace);
}
echo "PASS: {$checks} security checks; database fixtures rolled back.\n";
