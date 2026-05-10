<?php
/**
 * ============================================
 * Base Controller Class
 * ============================================
 */

declare(strict_types=1);

abstract class Controller
{
    protected array $data = [];
    protected string $lastUploadError = '';

    /**
     * Render a view
     */
    protected function view(string $view, array $data = [], ?string $layout = null): void
    {
        // Merge data
        $this->data = array_merge($this->data, $data);
        $isInstallView = str_starts_with($view, 'install.');

        // Inject frontend menus if not in admin backend
        if ($layout === 'frontend' && !isset($this->data['headerMenus'])) {
            $headerMenus = [];
            $footerMenus = [];

            try {
                require_once APP_PATH . '/Models/Menu.php';
                $menuModel = new Menu();
                $headerMenus = $menuModel->getHierarchical('header');
                $footerMenus = $menuModel->getHierarchical('footer');
            } catch (\Throwable $e) {
                error_log('Frontend menu load failed: ' . $e->getMessage());
                $headerMenus = [
                    ['title' => 'Beranda', 'url' => '/', 'target' => '_self', 'children' => []],
                    ['title' => 'Profil', 'url' => '/profil', 'target' => '_self', 'children' => []],
                    ['title' => 'Berita', 'url' => '/berita', 'target' => '_self', 'children' => []],
                    ['title' => 'Galeri', 'url' => '/galeri', 'target' => '_self', 'children' => []],
                    ['title' => 'Kontak', 'url' => '/kontak', 'target' => '_self', 'children' => []],
                ];
            }

            // Build the public SPMB menu from school status, independent of manual menu rows.
            $profile = $this->data['profile'] ?? [];
            if (empty($profile)) {
                try {
                    require_once APP_PATH . '/Models/SchoolProfile.php';
                    $profileModel = new SchoolProfile();
                    $profile = $profileModel->getProfile();
                    $this->data['profile'] = $profile; // Ensuring profile is available
                } catch (\Throwable $e) {
                    error_log('Frontend profile load failed: ' . $e->getMessage());
                    $profile = [];
                    $this->data['profile'] = [];
                }
            }

            $spmbPublic = $this->data['spmbPublic'] ?? $this->resolvePublicSpmbState(
                $profile,
                $this->data['settings'] ?? null
            );

            $headerMenus = $this->applySpmbMenuRules($headerMenus, $spmbPublic);
            $footerMenus = $this->applySpmbMenuRules($footerMenus, $spmbPublic);

            $this->data['spmbPublic'] = $spmbPublic;
            $this->data['headerMenus'] = $headerMenus;
            $this->data['footerMenus'] = $footerMenus;
        }

        // Ensure theme configuration is injected
        if (!$isInstallView && !isset($this->data['themeConfig'])) {
            try {
                require_once APP_PATH . '/Models/SiteSetting.php';
                $settingModel = new SiteSetting();
                $themeName = $settingModel->getTheme();
                $availableThemes = $settingModel->getAvailableThemes();

                $this->data['themeName'] = $themeName;
                $this->data['themeConfig'] = $availableThemes[$themeName] ?? $availableThemes['indigo-modern'];
            } catch (\Throwable $e) {
                error_log('Frontend theme load failed: ' . $e->getMessage());
                $this->data['themeName'] = 'indigo-modern';
                $this->data['themeConfig'] = [
                    'name' => 'Indigo Modern',
                    'primary' => '#4F46E5',
                    'description' => 'Default fallback theme',
                ];
            }
        }

        // Extract data to variables
        extract($this->data);

        // Start output buffering
        ob_start();

        // Include the view file
        $viewFile = VIEW_PATH . '/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) {
            throw new RuntimeException("View not found: {$view}");
        }
        require $viewFile;

        $content = ob_get_clean();

        if ($layout === 'frontend') {
            $this->trackFrontendVisit($view, $data);
        }

        // If layout is specified, wrap content in layout
        if ($layout) {
            $layoutFile = VIEW_PATH . '/layouts/' . $layout . '.php';
            if (!file_exists($layoutFile)) {
                throw new RuntimeException("Layout not found: {$layout}");
            }
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    private function applySpmbMenuRules(array $menus, array $spmbPublic): array
    {
        $menus = $this->removeSpmbMenus($menus);

        if (!empty($spmbPublic['active'])) {
            return $this->insertDynamicSpmbMenu($menus, [
                'title' => $spmbPublic['label'] ?? 'Info SPMB',
                'url' => $spmbPublic['url'] ?? '/spmb',
                'target' => $spmbPublic['target'] ?? '_self',
                'children' => [],
            ]);
        }

        return $menus;
    }

    private function removeSpmbMenus(array $menus): array
    {
        $filtered = [];

        foreach ($menus as $menu) {
            if ($this->isSpmbMenu($menu)) {
                continue;
            }

            if (!empty($menu['children'])) {
                $menu['children'] = $this->removeSpmbMenus($menu['children']);
            }

            $filtered[] = $menu;
        }

        return $filtered;
    }

    private function isSpmbMenu(array $menu): bool
    {
        $title = strtolower((string) ($menu['title'] ?? ''));
        $url = strtolower((string) ($menu['url'] ?? ''));

        return str_contains($title, 'spmb') || str_contains($url, '/spmb');
    }

    private function insertDynamicSpmbMenu(array $menus, array $spmbMenu): array
    {
        foreach ($menus as $index => $menu) {
            $title = strtolower((string) ($menu['title'] ?? ''));
            if ($title === 'kontak' || str_contains((string) ($menu['url'] ?? ''), '/kontak')) {
                array_splice($menus, $index, 0, [$spmbMenu]);
                return $menus;
            }
        }

        $menus[] = $spmbMenu;
        return $menus;
    }

    private function resolvePublicSpmbState(array $profile, ?array $settings = null): array
    {
        try {
            require_once APP_PATH . '/Models/SiteSetting.php';
            $settingModel = new SiteSetting();
            $settings ??= $settingModel->getAll();
            $isPeriodActive = $settingModel->isSPMBPeriodActive($settings);
        } catch (\Throwable $e) {
            error_log('Public SPMB state failed: ' . $e->getMessage());
            $settings ??= [];
            $isPeriodActive = false;
        }

        $schoolType = strtolower((string) ($profile['school_type'] ?? 'negeri'));
        $spmbLink = trim((string) ($profile['spmb_link'] ?? ''));
        $url = '';
        $target = '_self';

        if ($isPeriodActive && $schoolType === 'swasta') {
            $url = '/spmb';
        } elseif ($isPeriodActive && $schoolType === 'negeri' && $spmbLink !== '') {
            $url = $spmbLink;
            $target = '_blank';
        }

        return [
            'active' => $url !== '',
            'label' => 'Info SPMB',
            'url' => $url,
            'target' => $target,
            'school_type' => $schoolType,
            'start_date' => $settings['spmb_start_date'] ?? null,
            'end_date' => $settings['spmb_end_date'] ?? null,
        ];
    }

    /**
     * Return JSON response
     */
    protected function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Return success JSON response
     */
    protected function jsonSuccess(mixed $data = null, string $message = 'Success'): void
    {
        $this->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * Return error JSON response
     */
    protected function jsonError(string $message, int $statusCode = 400, array $errors = []): void
    {
        $this->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }

    /**
     * Redirect to URL
     */
    protected function redirect(string $url): void
    {
        Security::redirect($url);
    }

    /**
     * Get POST data
     */
    protected function post(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Get GET data
     */
    protected function get(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Get sanitized POST data
     */
    protected function postSafe(string $key, mixed $default = null): string
    {
        $value = $this->post($key, $default);
        return $value !== null ? Security::sanitize($value) : '';
    }

    protected function editorContent(string $key = 'content', string $existingContent = ''): string
    {
        $content = $this->persistEditorDataImages((string) $this->post($key, ''));
        $content = $this->normalizeEditorAssetUrls($content);
        $batchEmbeds = $this->editorUploadBatchEmbeds((string) $this->post('editor_upload_batch', ''));

        return $this->appendEditorEmbeds($content, (string) $this->post('editor_embeds_json', ''), $existingContent, $batchEmbeds);
    }

    protected function persistEditorDataImages(string $content): string
    {
        if ($content === '' || stripos($content, 'data:image/') === false) {
            return $content;
        }

        return preg_replace_callback('/\bsrc=([\'"])(data:image\/(?:jpeg|pjpeg|png|x-png|gif|webp);base64,[^\'"]+)\1/i', function (array $matches): string {
            $url = $this->storeEditorDataImage(html_entity_decode($matches[2], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            if (!$url) {
                return $matches[0];
            }

            return 'src=' . $matches[1] . Security::escape($url) . $matches[1];
        }, $content) ?? $content;
    }

    protected function prepareStoredEditorContent(string $content): string
    {
        $content = $this->persistEditorDataImages($content);
        $content = $this->normalizeEditorAssetUrls($content);

        return $this->removeEmptyImageFigures($content);
    }

    protected function normalizeEditorAssetUrls(string $content): string
    {
        if ($content === '' || (stripos($content, 'storage') === false && stripos($content, 'uploads/') === false)) {
            return $content;
        }

        return preg_replace_callback('/\b(src|srcset)=([\'"])(.*?)\2/i', function (array $matches): string {
            $attribute = strtolower($matches[1]);
            $quote = $matches[2];
            $value = html_entity_decode($matches[3], ENT_QUOTES | ENT_HTML5, 'UTF-8');

            if ($attribute === 'srcset') {
                $value = $this->normalizeSrcsetValue($value);
            } else {
                $value = $this->normalizeEditorAssetUrl($value);
            }

            return $matches[1] . '=' . $quote . htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8') . $quote;
        }, $content) ?? $content;
    }

    private function normalizeSrcsetValue(string $value): string
    {
        $items = array_map('trim', explode(',', $value));
        $normalized = [];

        foreach ($items as $item) {
            if ($item === '') {
                continue;
            }

            $parts = preg_split('/\s+/', $item, 2);
            $url = $this->normalizeEditorAssetUrl($parts[0] ?? '');
            $descriptor = $parts[1] ?? '';
            $normalized[] = trim($url . ' ' . $descriptor);
        }

        return implode(', ', $normalized);
    }

    private function normalizeEditorAssetUrl(string $url): string
    {
        $url = trim($url);

        if ($url === '' || str_starts_with($url, 'data:') || str_starts_with($url, 'blob:')) {
            return $url;
        }

        $parsed = parse_url($url);
        if (isset($parsed['path']) && str_starts_with($parsed['path'], '/storage/')) {
            return $this->rebuildStorageUrl($parsed['path'], $parsed);
        }

        $normalized = preg_replace('#^(\./|\../)+#', '', $url) ?? $url;
        if (str_starts_with($normalized, '/uploads/')) {
            return '/storage' . $normalized;
        }

        if (str_starts_with($normalized, 'storage/')) {
            return '/' . $normalized;
        }

        if (str_starts_with($normalized, 'uploads/')) {
            return '/storage/' . $normalized;
        }

        return $url;
    }

    private function rebuildStorageUrl(string $path, array $parsed): string
    {
        $url = $path;

        if (!empty($parsed['query'])) {
            $url .= '?' . $parsed['query'];
        }

        if (!empty($parsed['fragment'])) {
            $url .= '#' . $parsed['fragment'];
        }

        return $url;
    }

    protected function rememberEditorUpload(string $batch, string $url, string $type, string $title = ''): void
    {
        if (!$this->isValidEditorUploadBatch($batch) || !$this->isAllowedNewsEmbedUrl($url)) {
            return;
        }

        $_SESSION['_news_editor_uploads'] ??= [];
        $_SESSION['_news_editor_uploads'][$batch] ??= [];

        foreach ($_SESSION['_news_editor_uploads'][$batch] as $upload) {
            if (($upload['url'] ?? '') === $url) {
                return;
            }
        }

        $_SESSION['_news_editor_uploads'][$batch][] = [
            'type' => $type,
            'url' => $url,
            'title' => $title,
            'time' => time(),
        ];
    }

    private function editorUploadBatchEmbeds(string $batch): array
    {
        if (!$this->isValidEditorUploadBatch($batch)) {
            return [];
        }

        $uploads = $_SESSION['_news_editor_uploads'][$batch] ?? [];
        return is_array($uploads) ? $uploads : [];
    }

    private function isValidEditorUploadBatch(string $batch): bool
    {
        return preg_match('/^[a-f0-9]{16,64}$/i', $batch) === 1;
    }

    private function appendEditorEmbeds(string $content, string $embedsJson, string $existingContent = '', array $extraEmbeds = []): string
    {
        $embeds = array_merge($this->extractEditorEmbedsFromContent($existingContent), $extraEmbeds);

        if ($embedsJson !== '') {
            $postedEmbeds = json_decode($embedsJson, true);
            if (is_array($postedEmbeds)) {
                $embeds = array_merge($embeds, $postedEmbeds);
            }
        }

        foreach ($embeds as $embed) {
            if (!is_array($embed)) {
                continue;
            }

            $url = $this->normalizeEditorAssetUrl((string) ($embed['url'] ?? ''));
            if (!$this->isAllowedNewsEmbedUrl($url) || str_contains($content, $url)) {
                continue;
            }

            $type = strtolower((string) ($embed['type'] ?? ''));
            $title = (string) ($embed['title'] ?? '');
            $html = $this->buildEditorEmbedHtml($url, $type, $title);
            if ($html !== '') {
                $content .= "\n" . $html;
            }
        }

        return $this->normalizeEditorAssetUrls($content);
    }

    private function removeEmptyImageFigures(string $content): string
    {
        if ($content === '' || stripos($content, 'figure') === false || stripos($content, 'image') === false) {
            return $content;
        }

        return preg_replace('/<figure\b(?=[^>]*\bclass=(["\'])(?:(?!\1).)*\bimage\b(?:(?!\1).)*\1)[^>]*>(?:\s|&nbsp;|<p>(?:\s|&nbsp;)*<\/p>)*<\/figure>/i', '', $content) ?? $content;
    }

    private function extractEditorEmbedsFromContent(string $content): array
    {
        $content = $this->normalizeEditorAssetUrls($content);
        if ($content === '') {
            return [];
        }

        $embeds = [];
        if (preg_match_all('/\b(src|href)=([\'"])(.*?)\2/i', $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $rawUrl = html_entity_decode($match[3], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $url = str_starts_with($rawUrl, 'data:image/')
                    ? ($this->storeEditorDataImage($rawUrl) ?? '')
                    : $this->normalizeEditorAssetUrl($rawUrl);

                if (!$this->isAllowedNewsEmbedUrl($url)) {
                    continue;
                }

                $path = (string) (parse_url($url, PHP_URL_PATH) ?? '');
                $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                $embeds[] = [
                    'type' => $extension === 'pdf' ? 'pdf' : 'image',
                    'url' => $url,
                    'title' => pathinfo($path, PATHINFO_FILENAME) ?: ($extension === 'pdf' ? 'Dokumen PDF' : 'Gambar berita'),
                ];
            }
        }

        return $embeds;
    }

    private function isAllowedNewsEmbedUrl(string $url): bool
    {
        if (!str_starts_with($url, '/storage/uploads/news/')) {
            return false;
        }

        $path = (string) (parse_url($url, PHP_URL_PATH) ?? '');
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'], true);
    }

    private function buildEditorEmbedHtml(string $url, string $type, string $title = ''): string
    {
        $path = (string) (parse_url($url, PHP_URL_PATH) ?? '');
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $safeUrl = Security::escape($url);
        $safeTitle = Security::escape($title !== '' ? $title : pathinfo($path, PATHINFO_FILENAME));

        if ($type === 'pdf' || $extension === 'pdf') {
            return '<figure class="pdf-embed"><iframe src="' . $safeUrl . '" title="' . $safeTitle . '" loading="lazy"></iframe><figcaption>' . $safeTitle . '</figcaption></figure>';
        }

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
            return '<figure class="image"><img src="' . $safeUrl . '" alt="' . $safeTitle . '"></figure>';
        }

        return '';
    }

    private function storeEditorDataImage(string $dataUri): ?string
    {
        if (!preg_match('/^data:(image\/(?:jpeg|pjpeg|png|x-png|gif|webp));base64,(.+)$/is', $dataUri, $matches)) {
            return null;
        }

        $mimeType = strtolower($matches[1]);
        if (!in_array($mimeType, UPLOAD_ALLOWED_TYPES, true)) {
            return null;
        }

        $binary = base64_decode(preg_replace('/\s+/', '', $matches[2]) ?? '', true);
        if ($binary === false || $binary === '') {
            return null;
        }

        if (strlen($binary) > UPLOAD_MAX_SIZE) {
            $this->lastUploadError = 'Ukuran gambar di isi berita melebihi batas ' . number_format(UPLOAD_MAX_SIZE / 1024 / 1024, 0) . 'MB.';
            return null;
        }

        $detectedMime = (new finfo(FILEINFO_MIME_TYPE))->buffer($binary);
        if (!$detectedMime || !in_array($detectedMime, UPLOAD_ALLOWED_TYPES, true)) {
            return null;
        }

        $extension = Security::extensionForMime((string) $detectedMime);
        if (!$extension) {
            return null;
        }

        $directory = 'uploads/news/' . date('Y/m');
        $uploadDir = STORAGE_PATH . '/' . $directory;
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            return null;
        }

        if (!is_writable($uploadDir)) {
            return null;
        }

        if ($this->shouldOptimizeUpload($directory, (string) $detectedMime)) {
            $extension = 'webp';
        }

        $filename = substr(hash('sha256', $binary), 0, 32) . '.' . $extension;
        $filepath = $uploadDir . '/' . $filename;
        $publicUrl = '/storage/' . $directory . '/' . $filename;

        if (is_file($filepath)) {
            return $publicUrl;
        }

        if (file_put_contents($filepath, $binary, LOCK_EX) === false) {
            return null;
        }

        if (function_exists('chmod')) {
            @chmod($filepath, 0644);
        }

        if ($extension === 'webp') {
            $optimized = $this->optimizeImage($filepath, (string) $detectedMime);
            if ($optimized === false) {
                @unlink($filepath);
                return null;
            }
        }

        $this->applyWatermark($filepath, $directory);

        return $publicUrl;
    }

    /**
     * Validate CSRF token
     */
    protected function validateCsrf(): bool
    {
        $token = $this->post(CSRF_TOKEN_NAME) ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
        
        if (empty($token)) {
            $input = json_decode(file_get_contents('php://input'), true);
            if (is_array($input) && isset($input[CSRF_TOKEN_NAME])) {
                $token = $input[CSRF_TOKEN_NAME];
            }
        }

        return Security::validateCsrfToken($token);
    }

    /**
     * Require CSRF validation
     */
    protected function requireCsrf(): void
    {
        if (!$this->validateCsrf()) {
            if (Security::isAjax()) {
                $this->jsonError('Invalid CSRF token', 403);
            }
            // Set flash message to inform user about the error
            $this->flash('error', 'Sesi Anda telah berakhir. Silahkan coba lagi.');

            // Redirect back to referring page or admin dashboard
            $referer = $_SERVER['HTTP_REFERER'] ?? null;
            if ($referer && strpos($referer, '/admin') !== false) {
                $this->redirect($referer);
            } else {
                $this->redirect('/admin');
            }
        }
    }

    /**
     * Check if user is logged in
     */
    protected function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Get current user
     */
    protected function currentUser(): ?array
    {
        if (!$this->isLoggedIn()) {
            return null;
        }
        return $_SESSION['user'] ?? null;
    }

    /**
     * Require authentication
     */
    protected function requireAuth(): void
    {
        if (!$this->isLoggedIn()) {
            if (Security::isAjax()) {
                $this->jsonError('Unauthorized', 401);
            }
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            $this->redirect('/login');
        }
    }

    /**
     * Require specific role
     */
    protected function requireRole(string|array $roles): void
    {
        $this->requireAuth();

        $userRole = $_SESSION['user']['role'] ?? '';
        $allowedRoles = is_array($roles) ? $roles : [$roles];

        if (!in_array($userRole, $allowedRoles)) {
            if (Security::isAjax()) {
                $this->jsonError('Forbidden', 403);
            }
            $this->redirect('/admin');
        }
    }

    protected function isRateLimited(string $action, int $maxAttempts, int $windowSeconds): bool
    {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP']
            ?? $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['REMOTE_ADDR']
            ?? 'unknown';
        $ip = explode(',', (string) $ip)[0];
        $key = hash('sha256', $action . '|' . trim($ip));
        $now = time();

        $_SESSION['_rate_limits'][$key] = array_values(array_filter(
            $_SESSION['_rate_limits'][$key] ?? [],
            fn($timestamp) => ($now - (int) $timestamp) < $windowSeconds
        ));

        if (count($_SESSION['_rate_limits'][$key]) >= $maxAttempts) {
            return true;
        }

        $_SESSION['_rate_limits'][$key][] = $now;
        return false;
    }

    /**
     * Set flash message
     */
    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    /**
     * Get and clear flash message
     */
    protected function getFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }

    /**
     * Handle file upload
     */
    protected function uploadFile(array $file, string $directory = 'uploads', ?array $allowedTypes = null, ?int $maxSize = null): string|false
    {
        $this->lastUploadError = '';

        $errors = Security::validateUpload($file, $allowedTypes, $maxSize);
        if (!empty($errors)) {
            $this->lastUploadError = implode(' ', $errors);
            return false;
        }

        $mimeType = mime_content_type($file['tmp_name']);
        $extension = Security::extensionForMime((string) $mimeType);
        if (!$extension) {
            $this->lastUploadError = 'Tipe file tidak didukung (' . (string) $mimeType . '). Gunakan ' . Security::allowedUploadLabel($allowedTypes) . '.';
            return false;
        }

        $uploadDir = STORAGE_PATH . '/' . $directory;
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            $this->lastUploadError = 'Folder upload tidak bisa dibuat: ' . $directory . '. Periksa permission folder storage.';
            return false;
        }

        if (!is_writable($uploadDir)) {
            $this->lastUploadError = 'Folder upload tidak bisa ditulis: ' . $directory . '. Periksa permission folder storage.';
            return false;
        }

        $shouldOptimize = $this->shouldOptimizeUpload($directory, (string) $mimeType);
        if ($shouldOptimize) {
            $extension = 'webp';
        }

        $filename = Security::randomString(16) . '.' . $extension;
        $filepath = $uploadDir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            if ($shouldOptimize) {
                $optimized = $this->optimizeImage($filepath, (string) $mimeType);
                if ($optimized === false) {
                    unlink($filepath);
                    $this->lastUploadError = 'Gambar gagal diproses oleh server. Periksa ekstensi GD/Image WebP atau coba unggah gambar lain.';
                    return false;
                }
            }

            // Apply watermark if enabled
            $this->applyWatermark($filepath, $directory);

            return $directory . '/' . $filename;
        }

        $this->lastUploadError = 'File gagal dipindahkan ke folder upload. Periksa permission storage dan konfigurasi upload_tmp_dir hosting.';
        return false;
    }

    protected function uploadErrorMessage(string $fallback = 'Gagal mengunggah file'): string
    {
        return $this->lastUploadError !== '' ? $fallback . ': ' . $this->lastUploadError : $fallback;
    }

    private function shouldOptimizeUpload(string $directory, string $mimeType): bool
    {
        if (!function_exists('imagewebp') || !in_array($mimeType, ['image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/webp'], true)) {
            return false;
        }

        // Keep SPMB documents close to the original file characteristics.
        return !str_starts_with(str_replace('\\', '/', $directory), 'spmb');
    }

    private function optimizeImage(string $filepath, string $mimeType): bool
    {
        $source = match ($mimeType) {
            'image/jpeg', 'image/pjpeg' => imagecreatefromjpeg($filepath),
            'image/png', 'image/x-png' => imagecreatefrompng($filepath),
            'image/webp' => imagecreatefromwebp($filepath),
            default => false,
        };

        if (!$source) {
            return $mimeType === 'image/gif';
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $maxDimension = 1920;
        $ratio = min(1, $maxDimension / max($width, $height));
        $targetWidth = max(1, (int) round($width * $ratio));
        $targetHeight = max(1, (int) round($height * $ratio));

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);

        imagecopyresampled(
            $canvas,
            $source,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $width,
            $height
        );

        $result = imagewebp($canvas, $filepath, 82);
        imagedestroy($source);
        imagedestroy($canvas);

        return $result;
    }

    /**
     * Apply watermark to uploaded image if conditions are met
     */
    private function applyWatermark(string $filepath, string $directory): void
    {
        // Check if watermark is globally enabled
        if (!defined('WATERMARK_ENABLED') || !WATERMARK_ENABLED) {
            return;
        }

        // Directories excluded from watermark
        $excludedDirs = ['logos', 'photos', 'spmb', 'avatars'];
        $dirBase = explode('/', $directory)[0]; // Get first segment (e.g. 'uploads/news/2026/02' => 'uploads')
        if (in_array($dirBase, $excludedDirs)) {
            return;
        }

        // Check if file is an image
        $mimeType = mime_content_type($filepath);
        if (!$mimeType || !str_starts_with($mimeType, 'image/')) {
            return;
        }

        // Get school profile for watermark text and toggle
        try {
            $profileModel = new SchoolProfile();
            $profile = $profileModel->getProfile();

            if (!$profile) {
                return;
            }

            // Check per-school watermark toggle
            if (isset($profile['watermark_enabled']) && !$profile['watermark_enabled']) {
                return;
            }

            $schoolName = $profile['name'] ?? (defined('SCHOOL_NAME') ? SCHOOL_NAME : 'School');
            $watermarkText = 'Property of ' . $schoolName;

            $opacity = defined('WATERMARK_OPACITY') ? WATERMARK_OPACITY : 30;

            ImageWatermark::apply($filepath, $watermarkText, $opacity);
        } catch (\Throwable $e) {
            // Silently fail — don't block upload if watermark fails
            error_log('Watermark failed: ' . $e->getMessage());
        }
    }

    private function trackFrontendVisit(string $view, array $data): void
    {
        if (PHP_SAPI === 'cli' || $this->isLoggedIn()) {
            return;
        }

        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        if (str_starts_with($path, '/storage') || str_starts_with($path, '/api')) {
            return;
        }

        $userAgent = strtolower((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''));
        if ($userAgent === '' || preg_match('/bot|crawl|spider|slurp|preview|facebookexternalhit|whatsapp/i', $userAgent)) {
            return;
        }

        if (empty($_SESSION['_visitor_id'])) {
            $_SESSION['_visitor_id'] = Security::randomString(32);
        }

        $meta = $this->resolveVisitContent($view, $data);
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
        $visitorKey = hash('sha256', $_SESSION['_visitor_id'] . '|' . $ip . '|' . $userAgent);

        try {
            (new SiteVisit())->record([
                'visitor_key' => $visitorKey,
                'path' => $path,
                'title' => $meta['title'],
                'content_type' => $meta['content_type'],
                'content_id' => $meta['content_id'],
            ]);
        } catch (\Throwable $e) {
            error_log('Visit tracking failed: ' . $e->getMessage());
        }
    }

    private function resolveVisitContent(string $view, array $data): array
    {
        $title = (string) ($data['title'] ?? 'Halaman');
        $type = match ($view) {
            'frontend.home' => 'home',
            'frontend.profile' => 'profile',
            'frontend.gtk' => 'gtk',
            'frontend.gallery' => 'gallery',
            'frontend.gallery-detail' => 'gallery_album',
            'frontend.contact' => 'contact',
            'frontend.news.index' => 'news_index',
            'frontend.news.show' => 'news',
            'frontend.news.search' => 'search',
            'frontend.prestasi' => 'prestasi',
            'frontend.spmb.index' => 'spmb',
            'frontend.spmb.register' => 'spmb_register',
            'frontend.spmb.status' => 'spmb_status',
            default => str_replace('frontend.', '', $view),
        };

        $contentId = null;
        if (!empty($data['news']['id'])) {
            $contentId = (int) $data['news']['id'];
            $title = (string) $data['news']['title'];
        } elseif (!empty($data['album']['id'])) {
            $contentId = (int) $data['album']['id'];
            $title = (string) $data['album']['title'];
        }

        return [
            'title' => substr($title, 0, 255),
            'content_type' => $type,
            'content_id' => $contentId,
        ];
    }
}
