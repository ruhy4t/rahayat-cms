<?php
declare(strict_types=1);

// Isolated fixtures: never connect to or modify the site's database.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../app/Core/Database.php';
require __DIR__ . '/../app/Models/Model.php';
require __DIR__ . '/../app/Models/SiteVisit.php';
date_default_timezone_set('Asia/Jakarta');
define('STORAGE_PATH', sys_get_temp_dir() . '/rahayat-statistics-' . bin2hex(random_bytes(8)));
$pdo = new PDO('sqlite::memory:', null, null, [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
$db = (new ReflectionClass(Database::class))->newInstanceWithoutConstructor();
(new ReflectionProperty(Database::class, 'connection'))->setValue($db, $pdo);
(new ReflectionProperty(Database::class, 'instance'))->setValue(null, $db);
$pdo->exec('CREATE TABLE site_visits (visitor_key TEXT, visited_on TEXT)');
$model = new SiteVisit();
$checks = 0;
function expectStats(array $expected, string $date): void {
    global $model, $checks;
    $actual = $model->getPublicStatistics(new DateTimeImmutable($date));
    if ($actual !== $expected) { throw new RuntimeException($date . ': ' . json_encode($actual)); }
    $checks++;
}
expectStats(['today' => 0, 'week' => 0, 'month' => 0, 'total' => 0], '2026-09-20');
$insert = $pdo->prepare('INSERT INTO site_visits VALUES (?, ?)');
foreach ([['a','2026-08-01'], ['a','2026-08-31'], ['a','2026-09-01'], ['b','2026-09-01'],
          ['a','2026-09-14'], ['b','2026-09-20'], ['b','2026-09-20'], ['c','2026-09-20']] as $row) {
    $insert->execute($row);
}
expectStats(['today' => 2, 'week' => 3, 'month' => 3, 'total' => 8], '2026-09-20');
expectStats(['today' => 2, 'week' => 2, 'month' => 2, 'total' => 8], '2026-09-01');
expectStats(['today' => 0, 'week' => 0, 'month' => 3, 'total' => 8], '2026-09-21');
expectStats(['today' => 0, 'week' => 0, 'month' => 0, 'total' => 8], '2027-01-01');
function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
foreach ([null, ['today' => 0, 'week' => 12, 'month' => 1234, 'total' => 1234567]] as $visitorStatistics) {
    ob_start();
    include __DIR__ . '/../views/layouts/partials/visitor-statistics.php';
    $html = ob_get_clean();
    $needle = $visitorStatistics === null ? 'Statistik sementara tidak tersedia.' : '1.234.567';
    if (!str_contains($html, $needle)) { throw new RuntimeException('Widget rendering failed'); }
    $checks++;
}

// The cache holds aggregate numbers only and must skip repeat DB aggregation.
$cacheFile = STORAGE_PATH . '/cache/public-visitor-statistics-v1.json';
try {
    $initial = $model->getPublicStatistics();
    if (!is_file($cacheFile)) { throw new RuntimeException('Statistics cache was not created'); }
    $checks++;
    $pdo->exec('ALTER TABLE site_visits RENAME TO hidden_visits');
    try {
        if ($model->getPublicStatistics() !== $initial) { throw new RuntimeException('Statistics cache miss'); }
        $checks++;
    } finally { $pdo->exec('ALTER TABLE hidden_visits RENAME TO site_visits'); }
    $insert = $pdo->prepare('INSERT INTO site_visits VALUES (?, ?)');
    $insert->execute(['cache-fixture', date('Y-m-d')]);
    if ($model->getPublicStatistics() !== $initial) { throw new RuntimeException('Cache expired prematurely'); }
    $checks++;
    $cached = json_decode(file_get_contents($cacheFile), true);
    if (array_keys($cached['statistics']) !== ['today','week','month','total']) { throw new RuntimeException('Cache contains non-aggregate fields'); }
    $checks++;
    if ($cached['expires_at'] > time() + 30) { throw new RuntimeException('Cache TTL too long'); }
    $checks++;
    foreach (['expired', 'wrong-date', 'malformed', 'invalid-values', 'future-ttl'] as $variant) {
        $entry = $cached;
        if ($variant === 'expired') { $entry['expires_at'] = time() - 1; }
        if ($variant === 'wrong-date') { $entry['date'] = '2000-01-01'; }
        if ($variant === 'invalid-values') { $entry['statistics']['total'] = 'invalid'; }
        if ($variant === 'future-ttl') { $entry['expires_at'] = time() + 3600; }
        file_put_contents($cacheFile, $variant === 'malformed' ? '{bad-json' : json_encode($entry));
        if ($model->getPublicStatistics()['total'] !== $initial['total'] + 1) { throw new RuntimeException('Invalid cache not refreshed: ' . $variant); }
        $checks++;
    }
    if ($model->getPublicStatistics(new DateTimeImmutable('2026-09-20'))['total'] !== $initial['total'] + 1) { throw new RuntimeException('Explicit date report failed'); }
    $checks++;
    unlink($cacheFile); rmdir(STORAGE_PATH . '/cache');
    file_put_contents(STORAGE_PATH . '/cache', 'cache-directory-unavailable');
    if ($model->getPublicStatistics()['total'] !== $initial['total'] + 1) { throw new RuntimeException('Unavailable cache blocked statistics'); }
    $checks++;
} finally {
    if (is_file($cacheFile)) { unlink($cacheFile); }
    if (is_dir(STORAGE_PATH . '/cache')) { rmdir(STORAGE_PATH . '/cache'); }
    if (is_file(STORAGE_PATH . '/cache')) { unlink(STORAGE_PATH . '/cache'); }
    if (is_dir(STORAGE_PATH)) { rmdir(STORAGE_PATH); }
}
echo "Passed {$checks} visitor statistics checks.\n";
