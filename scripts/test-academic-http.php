<?php
declare(strict_types=1);
require __DIR__ . '/security-bootstrap.php';

// Local development only. Synthetic accounts/data are removed in finally.
$base = 'http://rahayat-cms.test';
$db = Database::getInstance();
$suffix = bin2hex(random_bytes(6));
$password = 'Academic-test-' . $suffix;
$userIds = [];
$yearId = null;
$uploadedPaths = [];
$profileBackup = null;
$messageSettingsBackup = [];
$messageKeys = ['principal_message_enabled', 'principal_message_title', 'principal_message_summary', 'principal_message_button'];
$fixture = STORAGE_PATH . '/cache/academic-' . $suffix . '.pdf';
$checks = 0;
function verifyAcademicHttp(bool $value, string $message): void {
    global $checks;
    if (!$value) { throw new RuntimeException($message); }
    $checks++;
}
function academicClient(): CurlHandle {
    $handle = curl_init();
    curl_setopt_array($handle, [CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEFILE => '', CURLOPT_TIMEOUT => 20, CURLOPT_USERAGENT => 'AcademicTestBot/1.0']);
    return $handle;
}
function academicRequest(CurlHandle $handle, string $path, ?array $data = null, bool $multipart = false): array {
    global $base;
    curl_setopt_array($handle, [CURLOPT_URL => $base . $path, CURLOPT_FOLLOWLOCATION => true, CURLOPT_POST => $data !== null, CURLOPT_HTTPGET => $data === null]);
    if ($data !== null) { curl_setopt($handle, CURLOPT_POSTFIELDS, $multipart ? $data : http_build_query($data)); }
    $body = curl_exec($handle);
    if ($body === false) { throw new RuntimeException('HTTP request failed'); }
    return ['status' => curl_getinfo($handle, CURLINFO_RESPONSE_CODE), 'body' => $body, 'url' => curl_getinfo($handle, CURLINFO_EFFECTIVE_URL)];
}
function academicToken(string $html): string {
    if (preg_match('/name="(?:csrf_token|csrf-token)" (?:value|content)="([a-f0-9]+)"/', $html, $match)) { return $match[1]; }
    throw new RuntimeException('CSRF token missing');
}
try {
    $start = 2180;
    while ($db->fetchColumn('SELECT COUNT(*) FROM academic_years WHERE start_year = ?', [$start])) { $start++; }
    if ($start > 2190) { throw new RuntimeException('No free synthetic year'); }
    $clients = [];
    foreach (['admin', 'gtk'] as $role) {
        $username = 'academic-' . $role . '-' . $suffix;
        $userIds[] = (int) $db->insert('users', ['username' => $username, 'email' => $username . '@example.invalid', 'name' => 'Academic test', 'role' => $role, 'password' => Security::hashPassword($password), 'is_active' => 1]);
        $client = academicClient();
        $login = academicRequest($client, '/login');
        academicRequest($client, '/login', [CSRF_TOKEN_NAME => academicToken($login['body']), 'username' => $username, 'password' => $password]);
        $clients[$role] = $client;
    }
    $admin = $clients['admin'];
    $anon = academicClient();
    verifyAcademicHttp(academicRequest($clients['gtk'], '/admin/agenda')['status'] === 403, 'GTK must not administer academic records');
    verifyAcademicHttp(str_ends_with(academicRequest($anon, '/admin/agenda')['url'], '/login'), 'Anonymous must authenticate');
    $page = academicRequest($admin, '/admin/kalender-pendidikan');
    verifyAcademicHttp(str_contains($page['body'], 'Tambah tahun pelajaran'), 'Admin calendar renders');
    $token = academicToken($page['body']);
    verifyAcademicHttp(academicRequest($admin, '/admin/kegiatan/save')['status'] === 405, 'GET save rejected');
    verifyAcademicHttp(academicRequest($admin, '/admin/kalender-pendidikan/tahun/save')['status'] === 405, 'GET year save rejected');
    academicRequest($admin, '/admin/kalender-pendidikan/tahun/save', ['csrf_token' => 'invalid', 'start_year' => $start, 'is_published' => '1']);
    verifyAcademicHttp(!$db->fetchColumn('SELECT COUNT(*) FROM academic_years WHERE start_year = ?', [$start]), 'Invalid CSRF must not write');
    academicRequest($admin, '/admin/kalender-pendidikan/tahun/save', ['csrf_token' => $token, 'start_year' => $start]);
    $yearId = (int) $db->fetchColumn('SELECT id FROM academic_years WHERE start_year = ?', [$start]);
    verifyAcademicHttp($yearId > 0, 'Create year');
    verifyAcademicHttp(!str_contains(academicRequest($anon, '/kalender-pendidikan?tahun=' . $start)['body'], 'value="' . $start . '" selected'), 'Draft year hidden');
    $event = ['csrf_token' => $token, 'kind' => 'agenda', 'academic_year_id' => $yearId, 'title' => 'Academic ' . $suffix, 'category' => 'ujian', 'start_date' => "$start-07-08", 'end_date' => "$start-07-12", 'event_time' => '08:30', 'location' => 'Aula', 'description' => '<script>alert(1)</script>', 'show_in_calendar' => '1', 'status' => 'published'];
    academicRequest($admin, '/admin/kegiatan/save', $event);
    $eventId = (int) $db->fetchColumn('SELECT id FROM school_events WHERE academic_year_id = ?', [$yearId]);
    verifyAcademicHttp($eventId > 0, 'Create agenda');
    verifyAcademicHttp(academicRequest($anon, '/agenda/' . $eventId)['status'] === 404, 'Published event in draft year hidden');
    file_put_contents($fixture, "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF\n");
    academicRequest($admin, '/admin/kalender-pendidikan/tahun/save', ['csrf_token' => $token, 'start_year' => (string) $start, 'is_published' => '1', 'pdf' => new CURLFile($fixture, 'application/pdf', 'calendar.pdf')], true);
    $pdfPath = (string) $db->fetchColumn('SELECT pdf_path FROM academic_years WHERE id = ?', [$yearId]);
    if ($pdfPath) { $uploadedPaths[] = $pdfPath; }
    verifyAcademicHttp(str_starts_with($pdfPath, 'academic_documents/'), 'Validated PDF uploaded');
    $detail = academicRequest($anon, '/agenda/' . $eventId);
    verifyAcademicHttp($detail['status'] === 200 && str_contains($detail['body'], 'Academic ' . $suffix), 'Public detail after publishing year');
    verifyAcademicHttp(!str_contains($detail['body'], '<script>alert(1)</script>'), 'Description escaped');
    $calendar = academicRequest($anon, '/kalender-pendidikan?tahun=' . $start . '&bulan=' . $start . '-07');
    verifyAcademicHttp(str_contains($calendar['body'], 'Academic ' . $suffix), 'Linked agenda in calendar');
    verifyAcademicHttp(str_contains($calendar['body'], 'data-calendar-date="' . $start . '-07-31"'), 'Monthly grid rendered');
    verifyAcademicHttp(academicRequest($anon, '/kalender-pendidikan/pdf/' . $yearId)['body'] === file_get_contents($fixture), 'PDF bytes served by download route');
    file_put_contents($fixture, '<?php echo "not a PDF";');
    academicRequest($admin, '/admin/kalender-pendidikan/tahun/save', ['csrf_token' => $token, 'start_year' => (string) $start, 'is_published' => '1', 'pdf' => new CURLFile($fixture, 'application/pdf', 'fake.pdf')], true);
    verifyAcademicHttp($db->fetchColumn('SELECT pdf_path FROM academic_years WHERE id = ?', [$yearId]) === $pdfPath, 'Forged PDF MIME rejected without replacing document');
    foreach (['/storage/' . $pdfPath, '/index.php?url=storage/' . $pdfPath, '/index.php?url=storage/./' . $pdfPath] as $path) {
        verifyAcademicHttp(in_array(academicRequest($anon, $path)['status'], [403, 404], true), 'Direct PDF blocked');
    }
    $bad = array_replace($event, ['id' => $eventId, 'end_date' => ($start + 1) . '-07-01']);
    $invalid = academicRequest($admin, '/admin/kegiatan/save', $bad);
    verifyAcademicHttp(str_contains($invalid['body'], 'Tanggal kegiatan harus berada'), 'Out-of-year feedback');
    verifyAcademicHttp($db->fetchColumn('SELECT end_date FROM school_events WHERE id = ?', [$eventId]) === "$start-07-12", 'Invalid edit preserves record');
    academicRequest($admin, '/admin/kegiatan/save', array_replace($event, ['id' => $eventId, 'show_in_calendar' => '0']));
    verifyAcademicHttp(!str_contains(academicRequest($anon, '/kalender-pendidikan?tahun=' . $start . '&bulan=' . $start . '-07')['body'], 'Academic ' . $suffix), 'Unlink agenda');
    academicRequest($admin, '/admin/kegiatan/save', array_replace($event, ['id' => $eventId, 'status' => 'draft']));
    verifyAcademicHttp(academicRequest($anon, '/agenda/' . $eventId)['status'] === 404, 'Draft detail hidden');
    academicRequest($admin, '/admin/kalender-pendidikan/tahun/save', ['csrf_token' => $token, 'start_year' => $start]);
    verifyAcademicHttp(academicRequest($anon, '/kalender-pendidikan/pdf/' . $yearId)['status'] === 404, 'Unpublished year PDF blocked');
    verifyAcademicHttp(academicRequest($admin, '/admin/kegiatan/delete/' . $eventId)['status'] === 405, 'GET delete rejected');
    academicRequest($admin, '/admin/kegiatan/delete/' . $eventId, ['csrf_token' => $token]);
    verifyAcademicHttp(!$db->fetchColumn('SELECT COUNT(*) FROM school_events WHERE id = ?', [$eventId]), 'Delete event');
    $profile = academicRequest($admin, '/admin/profil');
    verifyAcademicHttp(str_contains($profile['body'], 'name="principal_message_summary"') && str_contains($profile['body'], 'name="principal_message_enabled"'), 'Profile message controls');
    $profileBackup = $db->fetch('SELECT * FROM school_profile LIMIT 1');
    foreach ($messageKeys as $key) { $messageSettingsBackup[$key] = $db->fetch('SELECT * FROM site_settings WHERE setting_key = ?', [$key]); }
    $dom = new DOMDocument(); @$dom->loadHTML('<?xml encoding="UTF-8">' . $profile['body']);
    $xpath = new DOMXPath($dom);
    $profilePost = [];
    foreach ($xpath->query('//form[@id="profileForm"]//input[@name]') as $input) {
        $type = $input->getAttribute('type');
        if (in_array($type, ['file', 'submit'], true) || (in_array($type, ['checkbox', 'radio'], true) && !$input->hasAttribute('checked'))) { continue; }
        $value = $input->getAttribute('value');
        $profilePost[$input->getAttribute('name')] = $type === 'time' ? substr($value, 0, 5) : $value;
    }
    foreach ($xpath->query('//form[@id="profileForm"]//textarea[@name]') as $input) { $profilePost[$input->getAttribute('name')] = $input->textContent; }
    foreach ($xpath->query('//form[@id="profileForm"]//select[@name]') as $select) {
        $options = $xpath->query('.//option[@selected]', $select);
        $option = $options->length ? $options->item(0) : $xpath->query('.//option', $select)->item(0);
        $profilePost[$select->getAttribute('name')] = $option ? $option->getAttribute('value') : '';
    }
    $profilePost = array_replace($profilePost, ['principal_message_enabled' => '1', 'principal_message_title' => 'Pesan ' . $suffix, 'principal_message_summary' => 'Ringkasan ' . $suffix, 'principal_message_button' => 'Baca pesan', 'welcome_message' => '<p>Lengkap ' . $suffix . '</p>']);
    $savedProfile = academicRequest($admin, '/admin/profil/update', $profilePost);
    if (!str_contains($savedProfile['body'], 'Profil sekolah berhasil diperbarui')) {
        $failureDom = new DOMDocument(); @$failureDom->loadHTML($savedProfile['body']);
        $failureXpath = new DOMXPath($failureDom);
        foreach ($failureXpath->query('//div[contains(@class,"bg-red-100")]') as $notice) { fwrite(STDERR, trim($notice->textContent) . "\n"); }
    }
    verifyAcademicHttp(str_contains($savedProfile['body'], 'Profil sekolah berhasil diperbarui'), 'Save profile message');
    verifyAcademicHttp(str_contains(academicRequest($anon, '/')['body'], 'Ringkasan ' . $suffix), 'Saved message on home');
    verifyAcademicHttp(str_contains(academicRequest($anon, '/pesan-kepala-sekolah')['body'], 'Lengkap ' . $suffix), 'Saved full message');
    unset($profilePost['principal_message_enabled']);
    academicRequest($admin, '/admin/profil/update', $profilePost);
    verifyAcademicHttp(!str_contains(academicRequest($anon, '/')['body'], 'Ringkasan ' . $suffix), 'Disable home message');
    verifyAcademicHttp(academicRequest($anon, '/pesan-kepala-sekolah')['status'] === 404, 'Disabled full message hidden');
} finally {
    if ($profileBackup) {
        $profileId = (int) $profileBackup['id'];
        unset($profileBackup['id']);
        $db->update('school_profile', $profileBackup, 'id = ?', [$profileId]);
        foreach ($messageSettingsBackup as $key => $row) {
            if ($row) { unset($row['id']); $db->update('site_settings', $row, 'setting_key = ?', [$key]); }
            else { $db->delete('site_settings', 'setting_key = ?', [$key]); }
        }
    }
    if ($yearId) {
        $db->delete('school_events', 'academic_year_id = ?', [$yearId]);
        $db->delete('academic_years', 'id = ?', [$yearId]);
    }
    foreach ($userIds as $id) { $db->delete('users', 'id = ?', [$id]); }
    foreach ($uploadedPaths as $path) { if (is_file(STORAGE_PATH . '/' . $path)) { unlink(STORAGE_PATH . '/' . $path); } }
    if (is_file($fixture)) { unlink($fixture); }
}
echo "PASS: {$checks} academic HTTP checks. Synthetic records removed.\n";
