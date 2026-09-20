<?php
declare(strict_types=1);
require __DIR__ . '/security-bootstrap.php';
require_once APP_PATH . '/Core/Security.php';
$checks = 0;
function academicCheck(bool $value, string $message): void {
    global $checks;
    if (!$value) { throw new RuntimeException($message); }
    $checks++;
}
academicCheck(AcademicCalendar::currentYear(new DateTimeImmutable('2026-06-30')) === 2025, 'June boundary');
academicCheck(AcademicCalendar::currentYear(new DateTimeImmutable('2026-07-01')) === 2026, 'July boundary');
$months = AcademicCalendar::months(2027);
academicCheck(count($months) === 12 && array_key_first($months) === '2027-07' && array_key_last($months) === '2028-06', 'Academic month order');
academicCheck(AcademicCalendar::validDate('2028-02-29') && !AcademicCalendar::validDate('2027-02-29'), 'Leap dates');
$input = ['title' => 'Ujian', 'category' => 'ujian', 'kind' => 'agenda', 'status' => 'published', 'start_date' => '2027-07-01', 'end_date' => '2028-06-30', 'event_time' => '08:30', 'show_in_calendar' => '1'];
academicCheck(AcademicCalendar::validateEvent($input, 2027)['show_in_calendar'] === 1, 'Agenda links to calendar');
foreach ([['start_date' => '2027-06-30'], ['end_date' => '2028-07-01'], ['end_date' => '2027-06-30'], ['event_time' => '24:00'], ['category' => 'invalid'], ['status' => 'invalid'], ['title' => []], ['title' => ''], ['start_date' => '2027-02-29']] as $bad) {
    try { AcademicCalendar::validateEvent(array_replace($input, $bad), 2027); throw new RuntimeException('Invalid event accepted'); }
    catch (InvalidArgumentException) { $checks++; }
}

$db = Database::getInstance();
$pdo = $db->getConnection();
$pdo->beginTransaction();
try {
    // Roll back every fixture, including any existing year publication settings.
    $model = new SchoolEvent();
    $start = 2196;
    $model->saveYear($start, true, null, false);
    $year = $db->fetch('SELECT * FROM academic_years WHERE start_year = ?', [$start]);
    $yearId = (int) $year['id'];
    $draftStart = 2197;
    $model->saveYear($draftStart, false, null, false);
    $draftYear = $db->fetch('SELECT * FROM academic_years WHERE start_year = ?', [$draftStart]);
    $base = ['academic_year_id' => $yearId, 'title' => 'Academic fixture <script>alert(1)</script>', 'category' => 'ujian', 'kind' => 'agenda', 'status' => 'published', 'start_date' => '2196-07-30', 'end_date' => '2196-08-03', 'show_in_calendar' => 1, 'description' => '<img src=x onerror=alert(1)>', 'location' => 'Aula'];
    $id = (int) $model->create($base);
    $draft = (int) $model->create(array_replace($base, ['status' => 'draft']));
    $privateYearId = (int) $model->create(array_replace($base, ['academic_year_id' => (int) $draftYear['id'], 'start_date' => '2197-07-01', 'end_date' => '2197-07-01']));
    $unlinked = (int) $model->create(array_replace($base, ['show_in_calendar' => 0]));
    academicCheck($model->publishedEvent($id) !== false, 'Public detail');
    academicCheck($model->publishedEvent($draft) === false && $model->publishedEvent($privateYearId) === false, 'Draft isolation');
    $july = array_column($model->calendar($yearId, '2196-07'), 'id');
    $august = array_column($model->calendar($yearId, '2196-08'), 'id');
    academicCheck(in_array($id, $july) && in_array($id, $august), 'Multi-month overlap');
    academicCheck(!in_array($draft, $july) && !in_array($unlinked, $july), 'Calendar visibility');
    academicCheck($model->calendarData(2197)['year'] === null, 'Draft year not selectable');
    academicCheck($model->calendarData(2196, '../../etc')['month'] === '2196-07', 'Invalid month fallback');
    $calendarData = $model->calendarData(2196, '2196-08');
    ob_start(); include VIEW_PATH . '/frontend/academic/calendar-widget.php'; $html = ob_get_clean();
    academicCheck(!str_contains($html, '<script>') && !str_contains($html, '<img src=x'), 'Escape calendar text');
    $dom = new DOMDocument(); @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    $xpath = new DOMXPath($dom);
    academicCheck($xpath->query('//button[@data-calendar-date]')->length === 31, 'All dates rendered');
    academicCheck($xpath->query('//button[@data-calendar-date="2196-08-03"]//i')->length > 0, 'Range end marked');
    academicCheck($xpath->query('//button[@data-calendar-date="2196-08-04"]//i')->length === 0, 'Range stops after end');
    $profile = ['name' => 'Sekolah Uji', 'principal_name' => 'Kepala Sekolah Uji', 'principal_photo' => 'photos/existing.jpg', 'welcome_message' => '<p>Pesan lengkap</p>'];
    $settings = ['principal_message_enabled' => '1', 'principal_message_title' => 'Pesan Uji', 'principal_message_summary' => 'Ringkasan <script>alert(1)</script>', 'principal_message_button' => 'Baca Pesan'];
    $upcomingEvents = [$model->find($id)];
    $news = $slides = $facilities = $ekskul = $testimonials = [];
    $spmbPublic = ['active' => false];
    foreach (['indigo-modern', 'emerald-campus', 'crimson-bold', 'cendekia-smp'] as $theme) {
        $data = compact('profile', 'settings', 'theme', 'news', 'slides', 'facilities', 'ekskul', 'testimonials', 'upcomingEvents', 'calendarData');
        ob_start(); include VIEW_PATH . '/frontend/home.php'; $home = ob_get_clean();
        academicCheck(substr_count($home, 'id="principal-message-title"') === 1, 'Principal card on ' . $theme);
        academicCheck(substr_count($home, 'id="academic-home-title"') === 1, 'Academic section on ' . $theme);
        academicCheck(strpos($home, 'id="principal-message-title"') < strpos($home, 'id="academic-home-title"'), 'Section order on ' . $theme);
        academicCheck(!str_contains($home, '<script>alert(1)</script>'), 'Escaped summary on ' . $theme);
    }
    $settings['principal_message_enabled'] = '0';
    ob_start(); include VIEW_PATH . '/frontend/academic/principal-card.php'; $hidden = ob_get_clean();
    academicCheck(trim($hidden) === '', 'Principal toggle hides card');
    $upcomingEvents = []; $calendarData = [];
    ob_start(); include VIEW_PATH . '/frontend/academic/home-section.php'; $hidden = ob_get_clean();
    academicCheck(trim($hidden) === '', 'Empty academic section hidden');
    $model->update($id, ['status' => 'draft']);
    academicCheck($model->publishedEvent($id) === false, 'Unpublish event');
    $model->delete($id);
    academicCheck($model->find($id) === false, 'Delete event');
} finally { $pdo->rollBack(); }
echo "PASS: {$checks} academic checks. All database fixtures rolled back.\n";
