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
            }

            // Conditionally hide SPMB menus for 'Negeri' schools
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

            if (($profile['school_type'] ?? '') === 'negeri' && !empty($profile['spmb_link'])) {
                $spmbLink = $profile['spmb_link'];
                $filterMenu = function ($menus) use (&$filterMenu, $spmbLink) {
                    $filtered = [];
                    foreach ($menus as $menu) {
                        // Redirect to external SPMB link
                        if (str_contains(strtolower($menu['title']), 'spmb') || str_contains(strtolower($menu['url']), '/spmb')) {
                            $menu['url'] = $spmbLink;
                            $menu['target'] = '_blank';
                        }
                        if (!empty($menu['children'])) {
                            $menu['children'] = $filterMenu($menu['children']);
                        }
                        $filtered[] = $menu;
                    }
                    return $filtered;
                };

                $headerMenus = $filterMenu($headerMenus);
                $footerMenus = $filterMenu($footerMenus);

            } else if (($profile['school_type'] ?? '') === 'negeri' && empty($profile['spmb_link'])) {
                // If negeri but no SPMB link, hide the menu
                $filterMenu = function ($menus) use (&$filterMenu) {
                    $filtered = [];
                    foreach ($menus as $menu) {
                        if (str_contains(strtolower($menu['title']), 'spmb') || str_contains(strtolower($menu['url']), '/spmb')) {
                            continue;
                        }
                        if (!empty($menu['children'])) {
                            $menu['children'] = $filterMenu($menu['children']);
                        }
                        $filtered[] = $menu;
                    }
                    return $filtered;
                };

                $headerMenus = $filterMenu($headerMenus);
                $footerMenus = $filterMenu($footerMenus);
            }

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
    protected function uploadFile(array $file, string $directory = 'uploads'): string|false
    {
        $errors = Security::validateUpload($file);
        if (!empty($errors)) {
            return false;
        }

        $mimeType = mime_content_type($file['tmp_name']);
        $extension = Security::extensionForMime((string) $mimeType);
        if (!$extension) {
            return false;
        }

        $uploadDir = STORAGE_PATH . '/' . $directory;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
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
                    return false;
                }
            }

            // Apply watermark if enabled
            $this->applyWatermark($filepath, $directory);

            return $directory . '/' . $filename;
        }

        return false;
    }

    private function shouldOptimizeUpload(string $directory, string $mimeType): bool
    {
        if (!function_exists('imagewebp') || !in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return false;
        }

        // Keep SPMB documents close to the original file characteristics.
        return !str_starts_with(str_replace('\\', '/', $directory), 'spmb');
    }

    private function optimizeImage(string $filepath, string $mimeType): bool
    {
        $source = match ($mimeType) {
            'image/jpeg' => imagecreatefromjpeg($filepath),
            'image/png' => imagecreatefrompng($filepath),
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
