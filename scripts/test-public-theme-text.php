<?php
declare(strict_types=1);
require __DIR__ . '/security-bootstrap.php';

$checks = 0;
function textCheck(bool $result, string $message): void
{
    global $checks;
    if (!$result) { throw new RuntimeException($message); }
    $checks++;
}

$cases = [
    [null, ''], ['', ''], ['Belajar bersama', 'Belajar bersama'],
    ['<p>Berilmu <strong>dan berkarakter</strong></p>', 'Berilmu dan berkarakter'],
    ['<p>Pertama</p><p>Kedua<br>Ketiga</p>', 'Pertama Kedua Ketiga'],
    ['<ul><li>Satu</li><li>Dua</li></ul>', 'Satu Dua'],
    ['&lt;p&gt;Berilmu &lt;strong&gt;dan berkarakter&lt;/strong&gt;&lt;/p&gt;', 'Berilmu dan berkarakter'],
    ['&amp;lt;p&amp;gt;Belajar&amp;lt;/p&amp;gt;', 'Belajar'],
    ['<p>Guru&nbsp;&amp;&nbsp;Murid</p>', 'Guru & Murid'],
    ['<p></p><br>&nbsp;', ''],
    ["<p>  Sekolah\n  Nusantara </p>", 'Sekolah Nusantara'],
    ['<p>2 &lt; 3 &amp; 5 &gt; 4</p>', '2 < 3 & 5 > 4'],
];
foreach ($cases as [$input, $expected]) {
    $summary = PublicTheme::summary($input);
    textCheck($summary === $expected, 'Summary differs: ' . json_encode($input));
    $dom = new DOMDocument();
    @$dom->loadHTML('<?xml encoding="UTF-8"><div id="test">' . e($summary) . '</div>');
    textCheck($dom->getElementById('test')->textContent === $expected, 'Visible text differs after escaping');
}

foreach ([
    '<img src=x onerror="alert(1)"><p>Sekolah</p>',
    '&lt;img src=x onerror="alert(1)"&gt;&lt;p&gt;Sekolah&lt;/p&gt;',
    '<a href="java&#x73;cript:alert(1)">Sekolah</a>',
    '<svg onload="alert(1)"></svg><p>Sekolah</p>',
] as $input) {
    $summary = PublicTheme::summary($input);
    textCheck($summary === 'Sekolah', 'Active markup leaked into summary');
    textCheck(!str_contains(e($summary), '<'), 'Summary produced active HTML');
}

// Article bodies keep formatting through the sanitizer, not the summary path.
$rich = Security::sanitizeHtml('<p>Visi <strong>sekolah</strong><img src=x onerror="alert(1)"></p>');
textCheck(str_contains($rich, '<strong>sekolah</strong>'), 'Article formatting lost');
textCheck(!str_contains($rich, 'onerror'), 'Article sanitizer weakened');
echo "PASS: {$checks} public theme text checks.\n";
