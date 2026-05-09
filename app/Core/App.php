<?php
/**
 * ============================================
 * App Class - Application Bootstrap
 * ============================================
 */

declare(strict_types=1);

class App
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();
        $this->registerRoutes();
    }

    /**
     * Register application routes
     */
    private function registerRoutes(): void
    {
        // Installer
        $this->router->add('install', ['controller' => 'Install', 'action' => 'index']);

        // Frontend routes
        $this->router->add('', ['controller' => 'Home', 'action' => 'index']);
        $this->router->add('berita', ['controller' => 'News', 'action' => 'index']);
        $this->router->add('berita/{slug}', ['controller' => 'News', 'action' => 'show']);
        $this->router->add('profil', ['controller' => 'Home', 'action' => 'profile']);
        $this->router->add('profil/gtk', ['controller' => 'Home', 'action' => 'gtk']);
        $this->router->add('galeri', ['controller' => 'Home', 'action' => 'gallery']);
        $this->router->add('galeri/{slug}', ['controller' => 'Home', 'action' => 'galleryAlbum']);
        $this->router->add('kontak', ['controller' => 'Home', 'action' => 'contact']);

        $this->router->add('prestasi', ['controller' => 'Prestasi', 'action' => 'publicIndex']);

        // SPMB routes (public)
        $this->router->add('spmb', ['controller' => 'SPMB', 'action' => 'index']);
        $this->router->add('spmb/daftar', ['controller' => 'SPMB', 'action' => 'register']);
        $this->router->add('spmb/store', ['controller' => 'SPMB', 'action' => 'store']);
        $this->router->add('spmb/cek-status', ['controller' => 'SPMB', 'action' => 'checkStatus']);

        // Auth routes
        $this->router->add('login', ['controller' => 'Auth', 'action' => 'login']);
        $this->router->add('logout', ['controller' => 'Auth', 'action' => 'logout']);

        // Admin - Dashboard
        $this->router->add('admin', ['controller' => 'Dashboard', 'action' => 'index']);

        // Admin - Uploads (CKEditor)
        $this->router->add('admin/upload/image', ['controller' => 'Upload', 'action' => 'image']);

        // Admin - Berita
        $this->router->add('admin/berita', ['controller' => 'Dashboard', 'action' => 'news']);
        $this->router->add('admin/berita/create', ['controller' => 'Dashboard', 'action' => 'newsCreate']);
        $this->router->add('admin/berita/store', ['controller' => 'Dashboard', 'action' => 'newsStore']);
        $this->router->add('admin/berita/edit/{id:[0-9]+}', ['controller' => 'Dashboard', 'action' => 'newsEdit']);
        $this->router->add('admin/berita/update/{id:[0-9]+}', ['controller' => 'Dashboard', 'action' => 'newsUpdate']);
        $this->router->add('admin/berita/delete/{id:[0-9]+}', ['controller' => 'Dashboard', 'action' => 'newsDelete']);

        // Admin - Kategori
        $this->router->add('admin/kategori', ['controller' => 'Dashboard', 'action' => 'categories']);
        $this->router->add('admin/kategori/store', ['controller' => 'Dashboard', 'action' => 'categoryStore']);
        $this->router->add('admin/kategori/update/{id:[0-9]+}', ['controller' => 'Dashboard', 'action' => 'categoryUpdate']);
        $this->router->add('admin/kategori/delete/{id:[0-9]+}', ['controller' => 'Dashboard', 'action' => 'categoryDelete']);

        // Admin - Galeri
        $this->router->add('admin/galeri', ['controller' => 'Dashboard', 'action' => 'gallery']);
        $this->router->add('admin/galeri/albums', ['controller' => 'Dashboard', 'action' => 'galleryAlbums']);
        $this->router->add('admin/galeri/videos', ['controller' => 'Dashboard', 'action' => 'galleryVideos']);

        // Admin - Profil Sekolah
        $this->router->add('admin/profil', ['controller' => 'Dashboard', 'action' => 'profile']);
        $this->router->add('admin/profil/update', ['controller' => 'Dashboard', 'action' => 'profileUpdate']);

        // Admin - Fasilitas
        $this->router->add('admin/fasilitas', ['controller' => 'Facility', 'action' => 'index']);
        $this->router->add('admin/fasilitas/store', ['controller' => 'Facility', 'action' => 'store']);
        $this->router->add('admin/fasilitas/update/{id:[0-9]+}', ['controller' => 'Facility', 'action' => 'update']);
        $this->router->add('admin/fasilitas/delete/{id:[0-9]+}', ['controller' => 'Facility', 'action' => 'delete']);

        // Admin - Ekstrakurikuler
        $this->router->add('admin/ekstrakurikuler', ['controller' => 'Ekstrakurikuler', 'action' => 'index']);
        $this->router->add('admin/ekstrakurikuler/store', ['controller' => 'Ekstrakurikuler', 'action' => 'store']);
        $this->router->add('admin/ekstrakurikuler/update/{id:[0-9]+}', ['controller' => 'Ekstrakurikuler', 'action' => 'update']);
        $this->router->add('admin/ekstrakurikuler/delete/{id:[0-9]+}', ['controller' => 'Ekstrakurikuler', 'action' => 'delete']);

        // Admin - Gallery
        $this->router->add('admin/galeri/view/{id:[0-9]+}', ['controller' => 'Dashboard', 'action' => 'galleryView']);

        // Admin - GTK (Staff)
        $this->router->add('admin/gtk', ['controller' => 'Dashboard', 'action' => 'staff']);
        $this->router->add('admin/gtk/store', ['controller' => 'Dashboard', 'action' => 'staffStore']);
        $this->router->add('admin/gtk/update/{id:[0-9]+}', ['controller' => 'Dashboard', 'action' => 'staffUpdate']);
        $this->router->add('admin/gtk/delete/{id:[0-9]+}', ['controller' => 'Dashboard', 'action' => 'staffDelete']);

        // Admin - Hero Slides
        $this->router->add('admin/slides', ['controller' => 'Dashboard', 'action' => 'heroSlides']);
        $this->router->add('admin/slides/store', ['controller' => 'Dashboard', 'action' => 'slideStore']);
        $this->router->add('admin/slides/update/{id:[0-9]+}', ['controller' => 'Dashboard', 'action' => 'slideUpdate']);
        $this->router->add('admin/slides/delete/{id:[0-9]+}', ['controller' => 'Dashboard', 'action' => 'slideDelete']);

        // Admin - Menu Management
        $this->router->add('admin/menu', ['controller' => 'Dashboard', 'action' => 'menus']);
        $this->router->add('admin/menu/store', ['controller' => 'Dashboard', 'action' => 'menuStore']);
        $this->router->add('admin/menu/update/{id:[0-9]+}', ['controller' => 'Dashboard', 'action' => 'menuUpdate']);
        $this->router->add('admin/menu/delete/{id:[0-9]+}', ['controller' => 'Dashboard', 'action' => 'menuDelete']);
        $this->router->add('admin/menu/reorder', ['controller' => 'Dashboard', 'action' => 'menuReorder']);

        // Admin - Pengaturan (Settings)
        $this->router->add('admin/pengaturan', ['controller' => 'Dashboard', 'action' => 'settings']);
        $this->router->add('admin/pengaturan/update', ['controller' => 'Dashboard', 'action' => 'settingsUpdate']);
        $this->router->add('admin/pengaturan/theme', ['controller' => 'Dashboard', 'action' => 'themeUpdate']);

        // Admin - SPMB (for Admin and GTK committee only)
        $this->router->add('admin/spmb', ['controller' => 'Dashboard', 'action' => 'spmb']);
        $this->router->add('admin/spmb/pengaturan', ['controller' => 'Dashboard', 'action' => 'spmbSettings']);
        $this->router->add('admin/spmb/pengaturan/update', ['controller' => 'Dashboard', 'action' => 'spmbSettingsUpdate']);
        $this->router->add('admin/spmb/{id:[0-9]+}', ['controller' => 'Dashboard', 'action' => 'spmbDetail']);
        $this->router->add('admin/spmb/status/{id:[0-9]+}', ['controller' => 'Dashboard', 'action' => 'spmbUpdateStatus']);

        // Admin - Prestasi
        $this->router->add('admin/prestasi', ['controller' => 'Prestasi', 'action' => 'index']);
        $this->router->add('admin/prestasi/tambah', ['controller' => 'Prestasi', 'action' => 'form']);
        $this->router->add('admin/prestasi/edit/{id:[0-9]+}', ['controller' => 'Prestasi', 'action' => 'form']);
        $this->router->add('admin/prestasi/save', ['controller' => 'Prestasi', 'action' => 'save']);
        $this->router->add('admin/prestasi/delete/{id:[0-9]+}', ['controller' => 'Prestasi', 'action' => 'delete']);

        // Admin - Users
        $this->router->add('admin/pengguna', ['controller' => 'Dashboard', 'action' => 'users']);
        $this->router->add('admin/pengguna/store', ['controller' => 'Dashboard', 'action' => 'userStore']);
        $this->router->add('admin/pengguna/update/{id:[0-9]+}', ['controller' => 'Dashboard', 'action' => 'userUpdate']);
        $this->router->add('admin/pengguna/delete/{id:[0-9]+}', ['controller' => 'Dashboard', 'action' => 'userDelete']);
        $this->router->add('admin/pengguna/spmb-committee/{id:[0-9]+}', ['controller' => 'Dashboard', 'action' => 'toggleSPMBCommittee']);

        // Admin - System Updates
        $this->router->add('admin/pembaruan', ['controller' => 'Dashboard', 'action' => 'systemUpdate']);
        $this->router->add('admin/pembaruan/run', ['controller' => 'Dashboard', 'action' => 'systemUpdateRun']);

        // API routes for AJAX
        $this->router->add('api/users/change-password', ['controller' => 'Api', 'action' => 'changePassword']);
        $this->router->add('api/news', ['controller' => 'Api', 'action' => 'news']);
        $this->router->add('api/news/store', ['controller' => 'Api', 'action' => 'newsStore']);
        $this->router->add('api/news/update/{id:[0-9]+}', ['controller' => 'Api', 'action' => 'newsUpdate']);
        $this->router->add('api/news/delete/{id:[0-9]+}', ['controller' => 'Api', 'action' => 'newsDelete']);
        $this->router->add('api/news/{id:[0-9]+}', ['controller' => 'Api', 'action' => 'newsShow']);

        // API - Categories
        $this->router->add('api/categories', ['controller' => 'Api', 'action' => 'categories']);
        $this->router->add('api/categories/store', ['controller' => 'Api', 'action' => 'categoryStore']);
        $this->router->add('api/categories/update/{id:[0-9]+}', ['controller' => 'Api', 'action' => 'categoryUpdate']);
        $this->router->add('api/categories/delete/{id:[0-9]+}', ['controller' => 'Api', 'action' => 'categoryDelete']);

        // API - Gallery
        $this->router->add('api/gallery/store', ['controller' => 'Api', 'action' => 'galleryStore']);
        $this->router->add('api/gallery/update/{id:[0-9]+}', ['controller' => 'Api', 'action' => 'galleryUpdate']);
        $this->router->add('api/gallery/delete/{id:[0-9]+}', ['controller' => 'Api', 'action' => 'galleryDelete']);
        $this->router->add('api/gallery/video/store', ['controller' => 'Api', 'action' => 'videoStore']);

        // API - Gallery Albums
        $this->router->add('api/gallery/album/store', ['controller' => 'Api', 'action' => 'albumStore']);
        $this->router->add('api/gallery/album/update/{id:[0-9]+}', ['controller' => 'Api', 'action' => 'albumUpdate']);
        $this->router->add('api/gallery/album/delete/{id:[0-9]+}', ['controller' => 'Api', 'action' => 'albumDelete']);

        // API - Slides
        $this->router->add('api/slides/create', ['controller' => 'Api', 'action' => 'slideStore']);
        $this->router->add('api/slides/update', ['controller' => 'Api', 'action' => 'slideUpdate']);
        $this->router->add('api/slides/delete/{id:[0-9]+}', ['controller' => 'Api', 'action' => 'slideDelete']);
        $this->router->add('api/slides/toggle/{id:[0-9]+}', ['controller' => 'Api', 'action' => 'slideToggle']);

        // API - Menus
        $this->router->add('api/menus/store', ['controller' => 'Api', 'action' => 'menuStore']);
        $this->router->add('api/menus/update/{id:[0-9]+}', ['controller' => 'Api', 'action' => 'menuUpdate']);
        $this->router->add('api/menus/delete/{id:[0-9]+}', ['controller' => 'Api', 'action' => 'menuDelete']);

        // API - Users  
        $this->router->add('api/users/store', ['controller' => 'Api', 'action' => 'userStore']);
        $this->router->add('api/users/update/{id:[0-9]+}', ['controller' => 'Api', 'action' => 'userUpdate']);
        $this->router->add('api/users/delete/{id:[0-9]+}', ['controller' => 'Api', 'action' => 'userDelete']);

    }

    /**
     * Run the application
     */
    public function run(): void
    {
        // Maintenance Mode Check
        $this->checkMaintenanceMode();

        $this->router->dispatch();
    }

    /**
     * Check if maintenance mode is active
     */
    private function checkMaintenanceMode(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = trim($uri, '/');

        // Allow install, admin routes, login, and static resources
        if (
            str_starts_with($uri, 'install') ||
            str_starts_with($uri, 'admin') ||
            str_starts_with($uri, 'login') ||
            str_starts_with($uri, 'api') ||
            str_starts_with($uri, 'logout') ||
            str_starts_with($uri, 'storage')
        ) {
            return;
        }

        // Check setting
        // We need manually instantiate models here since we are not in a controller
        // Using raw DB query to avoid loading full model stack if not needed, 
        // but using Model is cleaner. App.php doesn't auto-load models yet? 
        // Autoloader is in index.php, so we can use SiteSetting.

        try {
            $settingModel = new SiteSetting();
            $isMaintenance = $settingModel->get('maintenance_mode') === '1';

            if ($isMaintenance) {
                // If user is logged in as admin/staff, allow access
                if (isset($_SESSION['user']) && in_array($_SESSION['user']['role'], ['admin', 'gtk'])) {
                    return;
                }

                $profileModel = new SchoolProfile();
                $profile = $profileModel->getProfile();
                $settings = $settingModel->getAll();

                // Load maintenance view
                http_response_code(503);
                require_once VIEW_PATH . '/frontend/maintenance.php';
                exit;
            }
        } catch (\Throwable $e) {
            // If DB fails, proceed (fail open) or show generic error
            // For now fail open to allow debugging
        }
    }
}
