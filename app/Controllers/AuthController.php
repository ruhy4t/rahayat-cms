<?php
/**
 * ============================================
 * Auth Controller
 * ============================================
 */

declare(strict_types=1);

class AuthController extends Controller
{
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

        // Validate input
        if (empty($username) || empty($password)) {
            $this->flash('error', 'Username dan password harus diisi');
            $this->redirect('/login');
        }

        if ($this->isRateLimited('login:' . strtolower($username), 5, 900)) {
            $this->flash('error', 'Terlalu banyak percobaan login. Silakan coba lagi beberapa menit lagi.');
            $this->redirect('/login');
        }

        // Authenticate
        $user = $this->userModel->authenticate($username, $password);

        if (!$user) {
            $this->flash('error', 'Username atau password salah');
            $this->redirect('/login');
        }

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
