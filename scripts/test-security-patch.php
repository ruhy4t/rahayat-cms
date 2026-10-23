<?php

declare(strict_types=1);

/**
 * Regression checks for the security patch of 2026-10-01:
 *   - Router resolves the declared action name before authorization
 *   - ApiController / DashboardController deny unmapped actions by default
 *   - PrestasiController authorizes from the routed action, not the URI
 *   - URL/redirect/embed validation helpers
 *   - profile rich-text read-time guard and nested sanitizer regression
 *
 * Run with: php scripts/test-security-patch.php
 */

require __DIR__ . '/security-bootstrap.php';

// requireAuth() records the intended URL, which is absent in CLI runs.
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/admin';

// This suite must run without a database. Declaring the storage stub here keeps
// the autoloader from loading the real PDO wrapper when a controller builds a
// model it never queries.
class Database
{
    public static function getInstance(): self
    {
        return new self();
    }
}

final class RedirectSignal extends Exception
{
}

final class JsonSignal extends Exception
{
    public function __construct(public int $status, string $message)
    {
        parent::__construct($message);
    }
}

$checks = 0;
function check(bool $value, string $message): void
{
    global $checks;
    if (!$value) {
        throw new RuntimeException($message);
    }
    $checks++;
}

function syntheticUser(string $role, array $permissions = [], int $id = 7): array
{
    return [
        'id' => $id,
        'role' => $role,
        'is_active' => 1,
        'is_spmb_committee' => 0,
        'permissions' => $permissions === [] ? null : json_encode($permissions),
        'name' => 'Synthetic',
    ];
}

function userModelWithoutDatabase(): User
{
    return (new ReflectionClass(User::class))->newInstanceWithoutConstructor();
}

/** @return array{0: bool, 1: string} */
function attempt(object $controller, string $action): array
{
    try {
        return [(bool) $controller->before($action), 'allowed'];
    } catch (JsonSignal $e) {
        return [false, 'json:' . $e->status];
    } catch (RedirectSignal $e) {
        return [false, 'redirect:' . $e->getMessage()];
    }
}

/** Controller test doubles: no database, no real redirect/exit. */
class PatchTestApiController extends ApiController
{
    public ?array $fakeUser = null;

    public function __construct()
    {
    }

    public function injectUserModel(User $model): void
    {
        (new ReflectionProperty(ApiController::class, 'userModel'))->setValue($this, $model);
    }

    protected function isLoggedIn(): bool
    {
        return $this->fakeUser !== null;
    }

    protected function currentUser(): ?array
    {
        return $this->fakeUser;
    }

    protected function jsonError(string $message, int $statusCode = 400, array $errors = []): void
    {
        throw new JsonSignal($statusCode, $message);
    }

    protected function redirect(string $url): void
    {
        throw new RedirectSignal($url);
    }

    protected function flash(string $type, string $message): void
    {
    }
}

class PatchTestDashboardController extends DashboardController
{
    public ?array $fakeUser = null;

    public function __construct()
    {
    }

    public function injectUserModel(User $model): void
    {
        (new ReflectionProperty(DashboardController::class, 'userModel'))->setValue($this, $model);
    }

    protected function isLoggedIn(): bool
    {
        return $this->fakeUser !== null;
    }

    protected function currentUser(): ?array
    {
        return $this->fakeUser;
    }

    protected function jsonError(string $message, int $statusCode = 400, array $errors = []): void
    {
        throw new JsonSignal($statusCode, $message);
    }

    protected function redirect(string $url): void
    {
        throw new RedirectSignal($url);
    }

    protected function flash(string $type, string $message): void
    {
    }
}

class PatchTestPrestasiController extends PrestasiController
{
    public ?array $fakeUser = null;

    public function __construct()
    {
    }

    protected function isLoggedIn(): bool
    {
        return $this->fakeUser !== null;
    }

    protected function currentUser(): ?array
    {
        return $this->fakeUser;
    }

    protected function jsonError(string $message, int $statusCode = 400, array $errors = []): void
    {
        throw new JsonSignal($statusCode, $message);
    }

    protected function redirect(string $url): void
    {
        throw new RedirectSignal($url);
    }

    protected function flash(string $type, string $message): void
    {
    }
}

try {
    // --- Router: declared method name is used for authorization ---------------
    $canonical = new ReflectionMethod(Router::class, 'canonicalAction');
    check($canonical->invoke(null, 'ApiController', 'userupdate') === 'userUpdate', 'Router lower-case action not canonicalised');
    check($canonical->invoke(null, 'DashboardController', 'settingsupdate') === 'settingsUpdate', 'Router dashboard action not canonicalised');
    check($canonical->invoke(null, 'ApiController', 'userUpdate') === 'userUpdate', 'Router exact-case action changed');
    check($canonical->invoke(null, 'ApiController', 'definitelyNotAnAction') === null, 'Router invented an unknown action');
    check(in_array($canonical->invoke(null, 'ApiController', 'userupdate'), ['userStore', 'userUpdate', 'userDelete', 'menuStore', 'menuUpdate', 'menuDelete'], true), 'Canonical name misses the admin-only list');

    // --- ApiController: deny by default --------------------------------------
    $api = new PatchTestApiController();
    $api->injectUserModel(userModelWithoutDatabase());

    $api->fakeUser = null;
    check(attempt($api, 'news')[0] === true, 'API public list requires a session');
    check(attempt($api, 'newsStore')[1] === 'json:401', 'API anonymous write not rejected');

    $api->fakeUser = syntheticUser('murid', ['berita']);
    check(attempt($api, 'newsStore')[0] === true, 'API murid lost news creation');
    check(attempt($api, 'newsDelete')[1] === 'json:403', 'API murid can delete news');
    foreach (['userStore', 'userUpdate', 'userDelete', 'menuStore', 'menuDelete', 'userupdate'] as $action) {
        check(attempt($api, $action)[1] === 'json:403', 'API murid reached ' . $action);
    }
    check(attempt($api, 'settingsUpdate')[1] === 'json:403', 'API unmapped action not denied by default');
    check(attempt($api, 'changePassword')[0] === true, 'API self password change blocked');
    check(attempt($api, 'categories')[0] === true, 'API category list blocked');

    $api->fakeUser = syntheticUser('gtk', ['berita']);
    check(attempt($api, 'newsStore')[0] === true, 'API gtk lost news creation');
    check(attempt($api, 'userDelete')[1] === 'json:403', 'API gtk reached userDelete');

    $api->fakeUser = syntheticUser('admin', [], 1);
    check(attempt($api, 'userStore')[0] === true, 'API admin lost user management');

    // --- DashboardController: gtk is allow-listed, not allow-by-default ------
    $dashboard = new PatchTestDashboardController();
    $dashboard->injectUserModel(userModelWithoutDatabase());

    $dashboard->fakeUser = syntheticUser('gtk', ['berita', 'profil']);
    check(attempt($dashboard, 'newsUpdate')[0] === true, 'Dashboard gtk lost news editing');
    check(attempt($dashboard, 'profileUpdate')[0] === true, 'Dashboard gtk lost profile editing');
    foreach (['settingsUpdate', 'settingsupdate', 'themeUpdate', 'menuStore', 'userStore', 'toggleSPMBCommittee', 'systemUpdateRun'] as $action) {
        check(attempt($dashboard, $action)[0] === false, 'Dashboard gtk reached ' . $action);
    }
    check(attempt($dashboard, 'index')[0] === true, 'Dashboard index blocked for gtk');

    $dashboard->fakeUser = syntheticUser('murid', ['berita']);
    check(attempt($dashboard, 'news')[0] === true, 'Dashboard news list blocked for murid');
    check(attempt($dashboard, 'settingsUpdate')[0] === false, 'Dashboard murid reached settings');

    // --- PrestasiController: authorization comes from the action -------------
    $prestasi = new PatchTestPrestasiController();
    $prestasi->fakeUser = null;
    check(attempt($prestasi, 'publicIndex')[0] === true, 'Prestasi public list blocked');
    check(attempt($prestasi, 'show')[0] === true, 'Prestasi public detail blocked');
    foreach (['index', 'save', 'delete', 'form'] as $action) {
        $result = attempt($prestasi, $action);
        check($result[0] === false && $result[1] === 'redirect:/login', 'Anonymous prestasi ' . $action . ' not blocked');
    }

    $prestasi->fakeUser = syntheticUser('gtk', ['prestasi']);
    check(attempt($prestasi, 'save')[0] === true, 'Prestasi gtk lost saving');
    check(attempt($prestasi, 'delete')[1] === 'json:403', 'Prestasi delete not admin-only');

    $prestasi->fakeUser = syntheticUser('admin', [], 1);
    check(attempt($prestasi, 'delete')[0] === true, 'Prestasi admin lost deletion');

    // --- Security helpers ----------------------------------------------------
    check(Security::isSafeLinkUrl('https://sekolah.sch.id/spmb'), 'Safe https link rejected');
    check(Security::isSafeLinkUrl('/spmb'), 'Site-relative link rejected');
    check(!Security::isSafeLinkUrl('javascript:alert(1)'), 'javascript: link accepted');
    check(!Security::isSafeLinkUrl('java&#x73;cript:alert(1)'), 'Entity encoded scheme accepted');
    check(!Security::isSafeLinkUrl('//evil.example'), 'Protocol-relative link accepted');
    check(Security::safeLinkUrl('javascript:alert(1)') === '', 'safeLinkUrl kept an unsafe value');
    $encoded = 'java&#x73;cript:alert(1)';
    for ($layer = 0; $layer < 12; $layer++) {
        check(Security::safeLinkUrl($encoded) === '', 'Repeatedly encoded unsafe URL accepted');
        $encoded = str_replace('&', '&amp;', $encoded);
    }
    check(Security::safeLinkUrl('https://example.invalid/?a=1&amp;b=2') === 'https://example.invalid/?a=1&b=2', 'Safe URL not canonicalized');

    check(Security::safeLocalPath('/admin/berita?page=2') === '/admin/berita?page=2', 'Local path rejected');
    check(Security::safeLocalPath('//evil.example') === null, 'Protocol-relative path accepted');
    check(Security::safeLocalPath('https://evil.example') === null, 'Absolute path accepted');

    check(Security::safeEmbedSrc('<iframe src="https://www.google.com/maps/embed?pb=x"></iframe>') === 'https://www.google.com/maps/embed?pb=x', 'Maps embed rejected');
    check(Security::safeEmbedSrc('<iframe src="javascript:alert(1)"></iframe>') === '', 'javascript embed accepted');
    check(Security::safeEmbedSrc('<iframe src="https://evil.example/x"></iframe>') === '', 'Foreign embed accepted');

    $clean = '<p>Visi <strong>sekolah</strong></p>';
    check(Security::sanitizeRichTextIfNeeded($clean) === $clean, 'Clean rich text was rewritten');
    check(!str_contains(Security::sanitizeRichTextIfNeeded('<p>x</p><script>alert(1)</script>'), '<script'), 'Script survived the rich-text guard');
    check(!str_contains(Security::sanitizeRichTextIfNeeded('<img src=x onerror="alert(1)">'), 'onerror'), 'Event handler survived the rich-text guard');
    foreach (['java&#x73;cript:alert(1)', 'java&#9;script:alert(1)', 'jav&#97;script:alert(1)', 'data:text/html,attack'] as $url) {
        $safe = Security::sanitizeRichTextIfNeeded('<a href="' . $url . '">Link</a>');
        check(!str_contains($safe, 'href='), 'Obfuscated scheme survived the rich-text guard');
        check(Security::sanitizeRichTextIfNeeded('<a href="' . $url . '">Link</a>') === $safe, 'Cached sanitizer result differs');
    }
    check(Security::sanitizeRichTextIfNeeded('<input autofocus name=x>') === '', 'Unexpected active element survived');
    check(Security::sanitizeRichTextIfNeeded('Sekolah &amp; murid') === 'Sekolah &amp; murid', 'Plain text changed');
    foreach (['cache', 'rate_limits', 'backups', 'logs', 'academic_documents'] as $folder) {
        check(Security::isRestrictedStoragePath(strtoupper($folder) . '/test.pdf'), 'Restricted canonical folder accepted');
        check(Security::isRestrictedStoragePath($folder . '\\test.pdf'), 'Windows restricted folder accepted');
    }
    check(!Security::isRestrictedStoragePath('uploads/cache/photo.jpg'), 'Public nested folder incorrectly blocked');

    foreach ([1, 2, 10] as $depth) {
        $html = str_repeat('<section>', $depth) . '<img src=x onerror="window.__audit=1">' . str_repeat('</section>', $depth);
        check(!str_contains(Security::sanitizeHtml($html), 'onerror'), 'Nested sanitizer bypass at depth ' . $depth);
    }

    echo 'PASS: ' . $checks . ' security-patch checks' . PHP_EOL;
} catch (Throwable $error) {
    fwrite(STDERR, 'FAIL: ' . $error->getMessage() . PHP_EOL);
    exit(1);
}
