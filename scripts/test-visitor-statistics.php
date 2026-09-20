<?php
declare(strict_types=1);

// Isolated fixtures: never connect to or modify the site's database.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../app/Core/Database.php';
require __DIR__ . '/../app/Models/Model.php';
require __DIR__ . '/../app/Models/SiteVisit.php';
date_default_timezone_set('Asia/Jakarta');
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
echo "Passed {$checks} visitor statistics checks.\n";
