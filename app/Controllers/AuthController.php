<?php
/**
 * ============================================
 * Auth Controller
 * ============================================
 */

declare(strict_types=1);

class AuthController extends Controller
{
    private const LOGIN_MAX_ATTEMPTS = 5;
    private const LOGIN_LOCK_SECONDS = 120;
    private const CAPTCHA_AFTER_ATTEMPTS = 3;
    private const CAPTCHA_EXPIRY_SECONDS = 120;

    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Show login form
     */
    public function login(): void
    {
        // Redirect if already logged in
        if ($this->isLoggedIn()) {
            $this->redirect('/admin');
        }

        // Handle POST request
        if (Security::isPost()) {
            $this->handleLogin();
            return;
        }

        require_once APP_PATH . '/Models/SiteSetting.php';
        $settingModel = new SiteSetting();
        $themeName = $settingModel->getTheme();
        $availableThemes = $settingModel->getAvailableThemes();

        $data = [
            'title' => 'Login',
            'flash' => $this->getFlash(),
            'captchaRequired' => (bool) ($_SESSION['_login_captcha_required'] ?? false),
            'lastUsername' => (string) ($_SESSION['_login_username'] ?? ''),
            'themeName' => $themeName,
            'themeConfig' => $availableThemes[$themeName] ?? $availableThemes['indigo-modern']
        ];

        $this->view('auth.login', $data);
    }

    /**
     * Handle login form submission
     */
    private function handleLogin(): void
    {
        // Validate CSRF
        $this->requireCsrf();

        $username = $this->postSafe('username');
        $password = $this->post('password', '');
        $_SESSION['_login_username'] = $username;

        // Validate input
        if (empty($username) || empty($password)) {
            $this->flash('error', 'Username dan password harus diisi');
            $this->redirect('/login');
        }

        if (!empty($this->post('website', ''))) {
            $this->flash('error', 'Permintaan login tidak valid.');
            $this->redirect('/login');
        }

        $rateLimit = $this->loginRateLimitStatus(
            $username,
            self::LOGIN_MAX_ATTEMPTS,
            self::LOGIN_LOCK_SECONDS
        );

        if ($rateLimit['locked']) {
            $this->flash(
                'error',
                'Login dikunci sementara. Silakan coba lagi dalam '
                . $this->formatRetryAfter($rateLimit['retry_after']) . '.'
            );
            $this->redirect('/login');
        }

        $captchaRequired = $rateLimit['attempts'] >= self::CAPTCHA_AFTER_ATTEMPTS
            || (bool) ($_SESSION['_login_captcha_required'] ?? false);

        if ($captchaRequired && !$this->validateCaptcha((string) $this->post('captcha', ''))) {
            $_SESSION['_login_captcha_required'] = true;
            $rateLimit = $this->recordFailedLogin(
                $username,
                self::LOGIN_MAX_ATTEMPTS,
                self::LOGIN_LOCK_SECONDS
            );

            if ($rateLimit['locked']) {
                $message = 'Login dikunci selama 2 menit karena 5 kali percobaan gagal.';
            } else {
                $message = 'Kode CAPTCHA salah atau sudah kedaluwarsa. Sisa percobaan: '
                    . $rateLimit['remaining'] . '.';
            }

            $this->flash('error', $message);
            $this->redirect('/login');
        }

        // Authenticate
        $user = $this->userModel->authenticate($username, $password);

        if (!$user) {
            $rateLimit = $this->recordFailedLogin(
                $username,
                self::LOGIN_MAX_ATTEMPTS,
                self::LOGIN_LOCK_SECONDS
            );

            if ($rateLimit['locked']) {
                $message = 'Login dikunci selama 2 menit karena 5 kali percobaan gagal.';
            } else {
                $message = 'Username atau password salah. Sisa percobaan: '
                    . $rateLimit['remaining'] . '.';
            }

            if ($rateLimit['attempts'] >= self::CAPTCHA_AFTER_ATTEMPTS) {
                $_SESSION['_login_captcha_required'] = true;
            }

            $this->flash('error', $message);
            $this->redirect('/login');
        }

        $this->clearLoginRateLimit($username);
        unset(
            $_SESSION['_login_captcha_required'],
            $_SESSION['_login_captcha'],
            $_SESSION['_login_username']
        );

        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user'] = $user;
        session_regenerate_id(true);

        // Redirect to intended URL or dashboard
        $redirectUrl = $_SESSION['redirect_after_login'] ?? '/admin';
        unset($_SESSION['redirect_after_login']);

        $this->flash('success', 'Selamat datang, ' . $user['name']);
        $this->redirect($redirectUrl);
    }

    /**
     * Generate a short-lived, one-time PNG CAPTCHA.
     */
    public function captcha(): void
    {
        if (empty($_SESSION['_login_captcha_required'])) {
            http_response_code(404);
            exit;
        }

        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code = '';
        for ($i = 0; $i < 5; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }

        $secret = bin2hex(random_bytes(32));
        $_SESSION['_login_captcha'] = [
            'hash' => hash_hmac('sha256', $code, $secret),
            'secret' => $secret,
            'expires_at' => time() + self::CAPTCHA_EXPIRY_SECONDS,
        ];

        // Draw at half size and upscale at the end so the built-in GD font
        // remains portable while still being easy for humans to read.
        $width = 105;
        $height = 35;
        $image = imagecreatetruecolor($width, $height);
        $background = imagecolorallocate($image, 245, 247, 250);
        imagefilledrectangle($image, 0, 0, $width, $height, $background);

        for ($i = 0; $i < 9; $i++) {
            $lineColor = imagecolorallocate(
                $image,
                random_int(130, 205),
                random_int(130, 205),
                random_int(130, 205)
            );
            imageline(
                $image,
                random_int(0, $width),
                random_int(0, $height),
                random_int(0, $width),
                random_int(0, $height),
                $lineColor
            );
        }

        for ($i = 0; $i < 160; $i++) {
            $dotColor = imagecolorallocate(
                $image,
                random_int(120, 220),
                random_int(120, 220),
                random_int(120, 220)
            );
            imagesetpixel($image, random_int(0, $width - 1), random_int(0, $height - 1), $dotColor);
        }

        foreach (str_split($code) as $index => $character) {
            $textColor = imagecolorallocate(
                $image,
                random_int(20, 75),
                random_int(30, 90),
                random_int(60, 130)
            );
            imagestring(
                $image,
                5,
                10 + ($index * 19) + random_int(-2, 2),
                random_int(8, 15),
                $character,
                $textColor
            );
        }

        $output = imagecreatetruecolor(210, 70);
        imagecopyresampled($output, $image, 0, 0, 0, 0, 210, 70, $width, $height);

        header('Content-Type: image/png');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        imagepng($output);
        imagedestroy($output);
        imagedestroy($image);
        exit;
    }

    private function validateCaptcha(string $answer): bool
    {
        $captcha = $_SESSION['_login_captcha'] ?? null;
        unset($_SESSION['_login_captcha']);

        if (!is_array($captcha)
            || empty($captcha['hash'])
            || empty($captcha['secret'])
            || (int) ($captcha['expires_at'] ?? 0) < time()) {
            return false;
        }

        $answerHash = hash_hmac(
            'sha256',
            strtoupper(trim($answer)),
            (string) $captcha['secret']
        );

        return hash_equals((string) $captcha['hash'], $answerHash);
    }

    private function formatRetryAfter(int $seconds): string
    {
        if ($seconds >= 60) {
            $minutes = (int) ceil($seconds / 60);
            return $minutes . ' menit';
        }

        return max(1, $seconds) . ' detik';
    }

    /**
     * Logout
     */
    public function logout(): void
    {
        // Clear session
        $_SESSION = [];

        // Delete session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Destroy session
        session_destroy();

        $this->redirect('/login');
    }
}
