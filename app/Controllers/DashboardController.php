<?php
/**
 * ============================================
 * Dashboard Controller - Backend Admin
 * ============================================
 */

declare(strict_types=1);

class DashboardController extends Controller
{
    private News $newsModel;
    private User $userModel;
    private SchoolProfile $profileModel;
    private NewsCategory $categoryModel;
    private Menu $menuModel;
    private SiteSetting $settingModel;
    private SPMBRegistration $spmbModel;
    private GalleryAlbum $albumModel;
    private GalleryItem $galleryModel;
    private HeroSlide $slideModel;
    private Staff $staffModel;
    private SiteVisit $visitModel;

    public function __construct()
    {
        $this->newsModel = new News();
        $this->userModel = new User();
        $this->profileModel = new SchoolProfile();
        $this->categoryModel = new NewsCategory();
        $this->menuModel = new Menu();
        $this->settingModel = new SiteSetting();
        $this->spmbModel = new SPMBRegistration();
        $this->albumModel = new GalleryAlbum();
        $this->galleryModel = new GalleryItem();
        $this->slideModel = new HeroSlide();
        $this->staffModel = new Staff();
        $this->visitModel = new SiteVisit();
    }

    /**
     * Before filter - require authentication + role-based access control
     */
    public function before(string $action): bool
    {
        $this->requireAuth();

        $user = $this->currentUser();
        $role = $user['role'] ?? '';

        // Admin has full access
        if ($role === 'admin') {
            return true;
        }

        // Map actions to required permissions
        $permissionMap = [
            // Berita
            'news' => 'berita',
            'newsCreate' => 'berita',
            'newsStore' => 'berita',
            'newsEdit' => 'berita',
            'newsUpdate' => 'berita',
            'newsDelete' => 'berita',
            // Kategori
            'categories' => 'kategori',
            // Galeri
            'gallery' => 'galeri',
            'galleryAlbums' => 'galeri',
            'galleryView' => 'galeri',
            'galleryVideos' => 'galeri',
            // Slider
            'heroSlides' => 'slider',
            // Profil
            'profile' => 'profil',
            'profileUpdate' => 'profil',
            // Staff
            'staff' => 'staff',
            'staffStore' => 'staff',
            'staffUpdate' => 'staff',
            'staffDelete' => 'staff',
            // SPMB
            'spmb' => 'spmb',
            'spmbDetail' => 'spmb',
            'spmbUpdateStatus' => 'spmb',
            'spmbSettings' => 'spmb',
            'spmbSettingsUpdate' => 'spmb',
        ];

        // Admin-only actions (no other role can access)
        $adminOnly = [
            'users',
            'toggleSPMBCommittee',
            'settings',
            'settingsUpdate',
            'themeUpdate',
            'menus',
            'menuStore',
            'menuUpdate',
            'menuDelete',
            'menuReorder',
            'systemUpdate',
            'systemUpdateRun',
        ];

        if (in_array($action, $adminOnly)) {
            $this->flash('error', 'Anda tidak memiliki akses ke halaman ini');
            $this->redirect('/admin');
            return false;
        }

        // Dashboard is always accessible
        if ($action === 'index') {
            return true;
        }

        // Restrict SPMB routes to Swasta only
        if (in_array($action, ['spmb', 'spmbDetail', 'spmbUpdateStatus', 'spmbSettings', 'spmbSettingsUpdate'])) {
            $spmbProfile = $this->profileModel->getProfile();
            if (empty($spmbProfile['school_type']) || $spmbProfile['school_type'] !== 'swasta') {
                $this->flash('error', 'Fitur SPMB hanya tersedia untuk sekolah swasta');
                $this->redirect('/admin');
                return false;
            }
        }

        // GTK: check configurable permissions
        if ($role === 'gtk') {
            // SPMB has extra check: must be committee member
            if (in_array($action, ['spmb', 'spmbDetail', 'spmbUpdateStatus', 'spmbSettings', 'spmbSettingsUpdate'])) {
                if (empty($user['is_spmb_committee'])) {
                    $this->flash('error', 'Anda tidak memiliki akses ke halaman ini');
                    $this->redirect('/admin');
                    return false;
                }
            }

            $requiredPerm = $permissionMap[$action] ?? null;
            if ($requiredPerm) {
                if (!$this->userModel->hasPermission($user, $requiredPerm)) {
                    $this->flash('error', 'Anda tidak memiliki akses ke halaman ini');
                    $this->redirect('/admin');
                    return false;
                }
            }
            // GTK can also delete news only if has berita permission
            return true;
        }

        // Murid/Ekskul: limited to berita and galeri (read + create draft)
        if (in_array($role, ['murid', 'ekskul'])) {
            $allowedActions = [
                'news',
                'newsCreate',
                'newsStore',
                'newsEdit',
                'newsUpdate',
                'gallery',
                'galleryAlbums',
                'galleryView',
            ];
            if (!in_array($action, $allowedActions)) {
                $this->flash('error', 'Anda tidak memiliki akses ke halaman ini');
                $this->redirect('/admin');
                return false;
            }
            return true;
        }

        // Unknown role — deny
        $this->flash('error', 'Role tidak valid');
        $this->redirect('/admin');
        return false;
    }

    /**
     * Check if current user is restricted (murid/ekskul) — must use draft only
     */
    private function isRestrictedRole(): bool
    {
        $user = $this->currentUser();
        return in_array($user['role'] ?? '', ['murid', 'ekskul']);
    }

    /**
     * Dashboard index
     */
    public function index(): void
    {
        $data = [
            'title' => 'Dashboard',
            'user' => $this->currentUser(),
            'stats' => [
                'news_count' => $this->newsModel->count(),
                'news_published' => $this->newsModel->countPublished(),
                'user_count' => $this->userModel->count(),
                'visitors_today' => $this->visitModel->countVisitorsToday(),
                'page_views_today' => $this->visitModel->countPageViewsToday(),
                'storage_size' => $this->formatBytes($this->directorySize(STORAGE_PATH)),
            ],
            'recentNews' => $this->newsModel->getRecent(5),
            'topContent' => $this->visitModel->getTopContent(6, 30),
            'flash' => $this->getFlash()
        ];

        $this->view('backend.dashboard', $data, 'backend');
    }

    private function directorySize(string $path): int
    {
        if (!is_dir($path)) {
            return 0;
        }

        $size = 0;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }

        return $size;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $value = (float) $bytes;
        $unit = 0;

        while ($value >= 1024 && $unit < count($units) - 1) {
            $value /= 1024;
            $unit++;
        }

        return number_format($value, $unit === 0 ? 0 : 1) . ' ' . $units[$unit];
    }

    /**
     * News management
     */
    public function news(): void
    {
        $page = (int) ($this->get('page', 1));
        $result = $this->newsModel->paginateWithAuthor($page);

        $data = [
            'title' => 'Kelola Berita',
            'user' => $this->currentUser(),
            'news' => $result['data'],
            'pagination' => $result,
            'categories' => $this->categoryModel->getActive(),
            'flash' => $this->getFlash()
        ];

        $this->view('backend.news.index', $data, 'backend');
    }

    /**
     * Create news form
     */
    public function newsCreate(): void
    {
        $data = [
            'title' => 'Tambah Berita',
            'user' => $this->currentUser(),
            'categories' => $this->categoryModel->getActive()
        ];

        $this->view('backend.news.form', $data, 'backend');
    }

    /**
     * Store news
     */
    public function newsStore(): void
    {
        $this->requireCsrf();

        $data = [
            'title' => $this->postSafe('title'),
            'content' => $this->post('content', ''),
            'excerpt' => $this->postSafe('excerpt'),
            'category_id' => $this->post('category_id') ?: null,
            'category' => $this->postSafe('category'),
            'status' => $this->postSafe('status') ?: 'draft',
            'author_id' => $_SESSION['user_id'],
            'published_at' => $this->post('status') === 'published' ? date('Y-m-d H:i:s') : null
        ];

        // Murid/Ekskul: force draft status
        if ($this->isRestrictedRole()) {
            $data['status'] = 'draft';
            $data['published_at'] = null;
        }

        // Generate slug
        $data['slug'] = $this->newsModel->generateSlug($data['title']);

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $imagePath = $this->uploadFile($_FILES['image']);
            if (!$imagePath) {
                $this->flash('error', $this->uploadErrorMessage('Gambar berita gagal diunggah'));
                $this->redirect('/admin/berita/create');
            }
            $data['image'] = $imagePath;
        }

        $this->newsModel->create($data);

        $this->flash('success', 'Berita berhasil ditambahkan');
        $this->redirect('/admin/berita');
    }

    /**
     * Edit news form
     */
    public function newsEdit(string $id): void
    {
        $news = $this->newsModel->find((int) $id);

        if (!$news) {
            $this->flash('error', 'Berita tidak ditemukan');
            $this->redirect('/admin/berita');
        }

        $data = [
            'title' => 'Edit Berita',
            'user' => $this->currentUser(),
            'news' => $news,
            'categories' => $this->categoryModel->getActive()
        ];

        $this->view('backend.news.form', $data, 'backend');
    }

    /**
     * Update news
     */
    public function newsUpdate(string $id): void
    {
        $this->requireCsrf();

        $newsId = (int) $id;
        $news = $this->newsModel->find($newsId);

        if (!$news) {
            $this->flash('error', 'Berita tidak ditemukan');
            $this->redirect('/admin/berita');
        }

        $data = [
            'title' => $this->postSafe('title'),
            'content' => $this->post('content', ''),
            'excerpt' => $this->postSafe('excerpt'),
            'category_id' => $this->post('category_id') ?: null,
            'category' => $this->postSafe('category'),
            'status' => $this->postSafe('status') ?: 'draft'
        ];

        // Murid/Ekskul: force draft status
        if ($this->isRestrictedRole()) {
            $data['status'] = 'draft';
        }

        // Update slug if title changed
        if ($data['title'] !== $news['title']) {
            $data['slug'] = $this->newsModel->generateSlug($data['title'], $newsId);
        }

        // Update published_at if status changed to published
        if ($data['status'] === 'published' && $news['status'] !== 'published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $imagePath = $this->uploadFile($_FILES['image']);
            if (!$imagePath) {
                $this->flash('error', $this->uploadErrorMessage('Gambar berita gagal diunggah'));
                $this->redirect('/admin/berita/edit/' . $newsId);
            }
            $data['image'] = $imagePath;
        }

        $this->newsModel->update($newsId, $data);

        $this->flash('success', 'Berita berhasil diperbarui');
        $this->redirect('/admin/berita');
    }

    /**
     * Delete news
     */
    public function newsDelete(string $id): void
    {
        $this->requireCsrf();

        $newsId = (int) $id;
        $this->newsModel->delete($newsId);

        $this->flash('success', 'Berita berhasil dihapus');
        $this->redirect('/admin/berita');
    }

    // ===================================================
    // CATEGORIES
    // ===================================================

    public function categories(): void
    {
        $data = [
            'title' => 'Kelola Kategori',
            'user' => $this->currentUser(),
            'categories' => $this->categoryModel->getAllWithCount(),
            'flash' => $this->getFlash()
        ];

        $this->view('backend.categories.index', $data, 'backend');
    }

    // ===================================================
    // MENU MANAGEMENT
    // ===================================================

    public function menus(): void
    {
        try {
            $menus = $this->menuModel->getAllForAdmin();
            $parentMenus = $this->menuModel->getParentMenus();
        } catch (\Throwable $e) {
            error_log('Menu management load failed: ' . $e->getMessage());
            $this->flash('error', 'Menu belum bisa dimuat karena struktur database belum lengkap. Upload versi terbaru lalu buka ulang halaman ini, atau jalankan database/migrations/repair_hosting_schema_1_0_2.sql. Jika hosting MySQL lama, jalankan database/migrations/repair_hosting_schema_menu_profile_legacy.sql lewat phpMyAdmin.');
            $menus = [];
            $parentMenus = [];
        }

        $data = [
            'title' => 'Kelola Menu',
            'user' => $this->currentUser(),
            'menus' => $menus,
            'parentMenus' => $parentMenus,
            'flash' => $this->getFlash()
        ];

        $this->view('backend.menus.index', $data, 'backend');
    }

    public function menuReorder(): void
    {
        $this->requireCsrf();
        $order = json_decode(file_get_contents('php://input'), true)['order'] ?? [];
        $this->menuModel->updateOrder($order);
        $this->json(['success' => true]);
    }

    public function menuStore(): void
    {
        $this->requireCsrf();

        $title = $this->postSafe('title');
        $url = $this->postSafe('url');

        if (empty($title) || empty($url)) {
            $this->json(['success' => false, 'message' => 'Judul dan URL harus diisi']);
            return;
        }

        $parentId = $this->normalizeMenuParentId($this->post('parent_id'));

        $data = [
            'title' => $title,
            'url' => $url,
            'icon' => $this->postSafe('icon') ?: null,
            'parent_id' => $parentId,
            'sort_order' => (int) $this->post('sort_order', 0),
            'is_active' => $this->post('is_active') ? 1 : 0,
            'target' => $this->normalizeMenuTarget($this->post('target')),
            'menu_location' => $this->normalizeMenuLocation($this->post('menu_location'))
        ];

        try {
            $this->menuModel->create($data);
            $this->json(['success' => true, 'message' => 'Menu berhasil ditambahkan']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Gagal menyimpan menu: ' . $e->getMessage()]);
        }
    }

    public function menuUpdate(string $id): void
    {
        $this->requireCsrf();

        $menuId = (int) $id;
        $menu = $this->menuModel->find($menuId);

        if (!$menu) {
            $this->json(['success' => false, 'message' => 'Menu tidak ditemukan']);
            return;
        }

        $title = $this->postSafe('title');
        $url = $this->postSafe('url');

        if (empty($title) || empty($url)) {
            $this->json(['success' => false, 'message' => 'Judul dan URL harus diisi']);
            return;
        }

        $parentId = $this->normalizeMenuParentId($this->post('parent_id'));
        if ($parentId === $menuId) {
            $this->json(['success' => false, 'message' => 'Menu tidak bisa menjadi parent untuk dirinya sendiri']);
            return;
        }
        if ($parentId !== null && $this->menuModel->hasChildren($menuId)) {
            $this->json(['success' => false, 'message' => 'Menu utama yang memiliki sub menu tidak bisa dijadikan sub menu']);
            return;
        }

        $data = [
            'title' => $title,
            'url' => $url,
            'icon' => $this->postSafe('icon') ?: null,
            'parent_id' => $parentId,
            'sort_order' => (int) $this->post('sort_order', 0),
            'is_active' => $this->post('is_active') ? 1 : 0,
            'target' => $this->normalizeMenuTarget($this->post('target')),
            'menu_location' => $this->normalizeMenuLocation($this->post('menu_location'))
        ];

        try {
            $this->menuModel->update($menuId, $data);
            $this->json(['success' => true, 'message' => 'Menu berhasil diperbarui']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Gagal memperbarui menu: ' . $e->getMessage()]);
        }
    }

    public function menuDelete(string $id): void
    {
        $this->requireCsrf();

        $menuId = (int) $id;
        $menu = $this->menuModel->find($menuId);

        if (!$menu) {
            $this->json(['success' => false, 'message' => 'Menu tidak ditemukan']);
            return;
        }

        try {
            $this->menuModel->delete($menuId);
            $this->json(['success' => true, 'message' => 'Menu berhasil dihapus']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Gagal menghapus menu: ' . $e->getMessage()]);
        }
    }

    private function normalizeMenuParentId(mixed $value): ?int
    {
        if ($value === null || $value === '' || (string) $value === '0') {
            return null;
        }

        return (int) $value;
    }

    private function normalizeMenuTarget(mixed $value): string
    {
        return in_array($value, ['_self', '_blank'], true) ? (string) $value : '_self';
    }

    private function normalizeMenuLocation(mixed $value): string
    {
        return in_array($value, ['header', 'footer', 'both'], true) ? (string) $value : 'header';
    }

    // ===================================================
    // SETTINGS (THEME)
    // ===================================================

    public function settings(): void
    {
        $data = [
            'title' => 'Pengaturan',
            'user' => $this->currentUser(),
            'settings' => $this->settingModel->getAll(),
            'themes' => $this->settingModel->getAvailableThemes(),
            'currentTheme' => $this->settingModel->getTheme(),
            'profile' => $this->profileModel->getProfile(),
            'flash' => $this->getFlash()
        ];

        $this->view('backend.settings.index', $data, 'backend');
    }

    public function themeUpdate(): void
    {
        $this->requireCsrf();
        $input = json_decode(file_get_contents('php://input'), true);
        $theme = $input['theme'] ?? 'indigo-modern';

        $this->settingModel->set('theme', $theme);
        $this->json(['success' => true, 'message' => 'Tema berhasil diubah']);
    }

    public function settingsUpdate(): void
    {
        $this->requireCsrf();

        $keys = [
            'site_title',
            'meta_description',
            'footer_text',
            'social_facebook',
            'social_instagram',
            'social_twitter',
            'social_youtube',
            'maintenance_mode',
            'maintenance_message'
        ];

        foreach ($keys as $key) {
            $value = $this->post($key);
            if ($value !== null) {
                // For boolean toggles that might be absent if unchecked (handled in JS but safe to handle '0' here if generic)
                // The DashboardController::settingsUpdate logic checks if value !== null.
                // Our JS for maintenance form appends '0' if missing, so it sends '0'.
                $this->settingModel->set($key, $value);
            }
        }

        $this->json(['success' => true, 'message' => 'Pengaturan berhasil disimpan']);
    }

    // ===================================================
    // SPMB MANAGEMENT
    // ===================================================

    public function spmbSettings(): void
    {
        $data = [
            'title' => 'Pengaturan SPMB',
            'user' => $this->currentUser(),
            'settings' => $this->settingModel->getAll(),
            'flash' => $this->getFlash(),
            'availableDocuments' => [
                'akta_kelahiran' => 'Akta Kelahiran',
                'kartu_keluarga' => 'Kartu Keluarga',
                'ktp_ortu' => 'KTP Orang Tua/Wali',
                'pas_foto' => 'Pas Foto 3x4',
                'ijazah' => 'Ijazah / SKL',
                'rapor' => 'Scan Rapor Terakhir',
            ]
        ];

        $this->view('backend.spmb.settings', $data, 'backend');
    }

    public function spmbSettingsUpdate(): void
    {
        $this->requireCsrf();

        try {
            // Save boolean safely
            $this->settingModel->set('spmb_enabled', $this->post('spmb_enabled') ? '1' : '0');
            $this->settingModel->set('spmb_start_date', $this->postSafe('spmb_start_date'));
            $this->settingModel->set('spmb_end_date', $this->postSafe('spmb_end_date'));
            $this->settingModel->set('spmb_quota', (string) (int) $this->post('spmb_quota', 0));

            $documents = $this->post('spmb_documents') ?? [];
            if (!is_array($documents))
                $documents = [];
            $this->settingModel->set('spmb_documents', json_encode($documents));

            $this->json(['success' => true, 'message' => 'Pengaturan SPMB berhasil disimpan']);
        } catch (\Throwable $e) {
            $this->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function spmb(): void
    {
        $status = $this->get('status');
        $page = (int) ($this->get('page', 1));

        $data = [
            'title' => 'Kelola SPMB',
            'user' => $this->currentUser(),
            'registrations' => $this->spmbModel->paginateByStatus($page, 20, $status),
            'stats' => $this->spmbModel->getStats(),
            'currentStatus' => $status,
            'flash' => $this->getFlash()
        ];

        $this->view('backend.spmb.index', $data, 'backend');
    }

    public function spmbDetail(string $id): void
    {
        $registration = $this->spmbModel->find((int) $id);

        if (!$registration) {
            $this->flash('error', 'Data pendaftaran tidak ditemukan');
            $this->redirect('/admin/spmb');
            return;
        }

        $data = [
            'title' => 'Detail Pendaftaran',
            'user' => $this->currentUser(),
            'registration' => $registration,
            'documents' => $this->spmbModel->getDocuments((int) $id),
            'flash' => $this->getFlash()
        ];

        $this->view('backend.spmb.detail', $data, 'backend');
    }

    public function spmbUpdateStatus(string $id): void
    {
        $this->requireCsrf();

        $status = $this->post('status');
        $notes = $this->postSafe('notes');
        $userId = $_SESSION['user_id'];

        if (!array_key_exists($status, SPMBRegistration::STATUS_LABELS)) {
            $this->json(['success' => false, 'message' => 'Status tidak valid']);
            return;
        }

        $this->spmbModel->updateStatus((int) $id, $status, $userId, $notes);

        $this->json(['success' => true, 'message' => 'Status berhasil diubah']);
    }

    // ===================================================
    // GALLERY
    // ===================================================

    public function gallery(): void
    {
        // Redirect to albums view as the main gallery view
        $this->galleryAlbums();
    }

    /**
     * Gallery Albums Management
     */
    public function galleryAlbums(): void
    {
        $data = [
            'title' => 'Album Galeri',
            'user' => $this->currentUser(),
            'albums' => $this->albumModel->getAll(),
            'flash' => $this->getFlash()
        ];

        $this->view('backend.gallery.albums', $data, 'backend');
    }

    /**
     * View Gallery Album Items
     */
    public function galleryView(string $id): void
    {
        $albumId = (int) $id;
        $album = $this->albumModel->find($albumId);

        if (!$album) {
            $this->flash('error', 'Album tidak ditemukan');
            $this->redirect('/admin/galeri');
        }

        $data = [
            'title' => 'Foto Album: ' . $album['title'],
            'user' => $this->currentUser(),
            'album' => $album,
            'items' => $this->galleryModel->getByAlbum($albumId),
            'flash' => $this->getFlash()
        ];

        $this->view('backend.gallery.view', $data, 'backend');
    }

    // ===================================================
    // HERO SLIDES
    // ===================================================

    public function heroSlides(): void
    {
        $data = [
            'title' => 'Kelola Slider',
            'user' => $this->currentUser(),
            'slides' => $this->slideModel->getAllForAdmin(),
            'flash' => $this->getFlash()
        ];

        $this->view('backend.slides.index', $data, 'backend');
    }

    // ===================================================
    // PROFILE
    // ===================================================

    public function profile(): void
    {
        $profile = $this->profileModel->getProfile();

        $data = [
            'title' => 'Profil Sekolah',
            'user' => $this->currentUser(),
            'profile' => $profile,
            'flash' => $this->getFlash()
        ];

        $this->view('backend.profile.index', $data, 'backend');
    }

    public function profileUpdate(): void
    {
        $this->requireCsrf();

        try {
            $data = [
                // Informasi Umum
                'name' => $this->postSafe('name'),
                'tagline' => $this->postSafe('tagline'),
                'school_type' => $this->normalizeSchoolType($this->post('school_type')),
                'spmb_link' => $this->postSafe('spmb_link'),
                'npsn' => $this->postSafe('npsn'),
                'accreditation' => $this->postSafe('accreditation') ?: null,
                'established_year' => $this->normalizeYear($this->post('established_year')),
                'principal_name' => $this->postSafe('principal_name'),
                'principal_nip' => $this->postSafe('principal_nip'),

                // Visi & Misi
                'vision' => $this->post('vision'),
                'mission' => $this->post('mission'),
                'motto' => $this->post('motto'),
                'history' => $this->post('history'),
                'welcome_message' => $this->post('welcome_message'),
                'principal_quote' => $this->post('principal_quote'),

                // Kontak
                'address' => $this->postSafe('address'),
                'phone' => $this->postSafe('phone'),
                'email' => $this->postSafe('email'),
                'website' => $this->postSafe('website'),
                'google_maps_embed' => $this->post('google_maps_embed'),

                // Statistik
                'total_students' => max(0, (int) $this->post('total_students', 0)),
                'total_teachers' => max(0, (int) $this->post('total_teachers', 0)),
                'graduation_rate' => min(100, max(0, (int) $this->post('graduation_rate', 100))),

                // Watermark
                'watermark_enabled' => $this->post('watermark_enabled') ? 1 : 0,
            ];

            $data = array_merge($data, $this->profileOperatingHoursData());

            // Handle logo upload
            if (!empty($_FILES['logo']['name'])) {
                $logoPath = $this->uploadFile($_FILES['logo'], 'logos');
                if (!$logoPath) {
                    throw new RuntimeException($this->uploadErrorMessage('Logo gagal diunggah'));
                }
                $data['logo'] = $logoPath;
            }

            // Handle principal photo upload
            if (!empty($_FILES['principal_photo']['name'])) {
                $photoPath = $this->uploadFile($_FILES['principal_photo'], 'photos');
                if (!$photoPath) {
                    throw new RuntimeException($this->uploadErrorMessage('Foto kepala sekolah gagal diunggah'));
                }
                $data['principal_photo'] = $photoPath;
            }

            $this->profileModel->saveProfile($data);
            $this->flash('success', 'Profil sekolah berhasil diperbarui');
        } catch (\Throwable $e) {
            error_log('Profile update failed: ' . $e->getMessage());
            $this->flash('error', $this->profileUpdateErrorMessage($e));
        }

        $this->redirect('/admin/profil');
    }

    private function normalizeSchoolType(mixed $value): string
    {
        return in_array($value, ['negeri', 'swasta'], true) ? (string) $value : 'negeri';
    }

    private function normalizeYear(mixed $value): ?int
    {
        $year = trim((string) ($value ?? ''));
        if ($year === '') {
            return null;
        }

        if (!preg_match('/^\d{4}$/', $year)) {
            throw new RuntimeException('Tahun berdiri harus berupa 4 digit, misalnya 1998.');
        }

        $yearInt = (int) $year;
        $currentYear = (int) date('Y') + 1;
        if ($yearInt < 1800 || $yearInt > $currentYear) {
            throw new RuntimeException('Tahun berdiri tidak valid.');
        }

        return $yearInt;
    }

    private function profileOperatingHoursData(): array
    {
        $defaults = [
            'monday' => ['07:00', '15:00', 0],
            'tuesday' => ['07:00', '15:00', 0],
            'wednesday' => ['07:00', '15:00', 0],
            'thursday' => ['07:00', '15:00', 0],
            'friday' => ['07:00', '15:00', 0],
            'saturday' => ['07:00', '12:00', 0],
            'sunday' => ['07:00', '15:00', 1],
        ];

        $data = [];
        foreach ($defaults as $day => [$openDefault, $closeDefault]) {
            $isClosed = $this->post("is_closed_{$day}") ? 1 : 0;
            $data["{$day}_open"] = $this->normalizeTime($this->post("{$day}_open"), $openDefault);
            $data["{$day}_close"] = $this->normalizeTime($this->post("{$day}_close"), $closeDefault);
            $data["is_closed_{$day}"] = $isClosed;
        }

        return $data;
    }

    private function normalizeTime(mixed $value, string $default): string
    {
        $time = trim((string) ($value ?? ''));
        if ($time === '') {
            return $default;
        }

        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time)) {
            throw new RuntimeException('Format jam operasional tidak valid.');
        }

        return $time;
    }

    private function profileUpdateErrorMessage(\Throwable $e): string
    {
        $message = $e->getMessage();

        if ($message !== '' && str_contains($message, 'Unknown column')) {
            return 'Profil gagal disimpan karena ada kolom database yang belum lengkap: ' . $message . '. Jalankan ulang installer terbaru atau migration repair.';
        }

        if ($message !== '' && str_contains($message, 'Data truncated')) {
            return 'Profil gagal disimpan karena ada nilai form yang tidak cocok dengan tipe kolom database: ' . $message;
        }

        return 'Profil gagal disimpan: ' . ($message ?: 'Terjadi error tidak diketahui. Cek error_log hosting untuk detail.');
    }

    // ===================================================
    // STAFF (GTK)
    // ===================================================

    public function staff(): void
    {
        $data = [
            'title' => 'Kelola GTK (Guru & Tendik)',
            'user' => $this->currentUser(),
            'staff' => $this->staffModel->all(),
            'flash' => $this->getFlash()
        ];

        $this->view('backend.staff.index', $data, 'backend');
    }

    public function staffStore(): void
    {
        $this->requireCsrf();

        $data = [
            'name' => $this->postSafe('name'),
            'nip' => $this->postSafe('nip'),
            'position' => $this->postSafe('position'),
            'subject' => $this->postSafe('subject'),
            'email' => $this->postSafe('email'),
            'phone' => $this->postSafe('phone'),
            'is_teacher' => $this->post('is_teacher') ? 1 : 0,
            'is_active' => $this->post('is_active') ? 1 : 0,
            'sort_order' => (int) $this->post('sort_order', 0)
        ];

        if (!empty($_FILES['photo']['name'])) {
            $photoPath = $this->uploadFile($_FILES['photo'], 'staff');
            if (!$photoPath) {
                $this->flash('error', $this->uploadErrorMessage('Foto GTK gagal diunggah'));
                $this->redirect('/admin/gtk');
            }
            $data['photo'] = $photoPath;
        }

        $this->staffModel->create($data);

        $this->flash('success', 'Data GTK berhasil ditambahkan');
        $this->redirect('/admin/gtk');
    }

    public function staffUpdate(string $id): void
    {
        $this->requireCsrf();

        $staffId = (int) $id;
        $staff = $this->staffModel->find($staffId);

        if (!$staff) {
            $this->flash('error', 'Data GTK tidak ditemukan');
            $this->redirect('/admin/gtk');
            return;
        }

        $data = [
            'name' => $this->postSafe('name'),
            'nip' => $this->postSafe('nip'),
            'position' => $this->postSafe('position'),
            'subject' => $this->postSafe('subject'),
            'email' => $this->postSafe('email'),
            'phone' => $this->postSafe('phone'),
            'is_teacher' => $this->post('is_teacher') ? 1 : 0,
            'is_active' => $this->post('is_active') ? 1 : 0,
            'sort_order' => (int) $this->post('sort_order', 0)
        ];

        if (!empty($_FILES['photo']['name'])) {
            $photoPath = $this->uploadFile($_FILES['photo'], 'staff');
            if (!$photoPath) {
                $this->flash('error', $this->uploadErrorMessage('Foto GTK gagal diunggah'));
                $this->redirect('/admin/gtk');
            }
            $data['photo'] = $photoPath;
        }

        $this->staffModel->update($staffId, $data);

        $this->flash('success', 'Data GTK berhasil diperbarui');
        $this->redirect('/admin/gtk');
    }

    public function staffDelete(string $id): void
    {
        $this->requireCsrf();

        $staffId = (int) $id;
        $this->staffModel->delete($staffId);

        $this->flash('success', 'Data GTK berhasil dihapus');
        $this->redirect('/admin/gtk');
    }

    // ===================================================
    // USERS
    // ===================================================

    public function users(): void
    {
        $users = $this->userModel->all();

        $data = [
            'title' => 'Kelola Pengguna',
            'user' => $this->currentUser(),
            'users' => $users,
            'flash' => $this->getFlash()
        ];

        $this->view('backend.users.index', $data, 'backend');
    }

    public function toggleSPMBCommittee(string $id): void
    {
        $this->requireCsrf();

        $userId = (int) $id;
        $user = $this->userModel->find($userId);

        if (!$user || $user['role'] !== 'gtk') {
            $this->json(['success' => false, 'message' => 'User tidak valid']);
            return;
        }

        $newStatus = !$user['is_spmb_committee'];
        $this->userModel->update($userId, ['is_spmb_committee' => $newStatus ? 1 : 0]);

        $this->json(['success' => true, 'is_committee' => $newStatus]);
    }

    public function systemUpdate(): void
    {
        $updater = new AppUpdater();

        $data = [
            'title' => 'Pembaruan Sistem',
            'user' => $this->currentUser(),
            'updateStatus' => $updater->getStatus(),
            'flash' => $this->getFlash(),
        ];

        $this->view('backend.system.update', $data, 'backend');
    }

    public function systemUpdateRun(): void
    {
        $this->requireCsrf();

        $updater = new AppUpdater();
        $result = $updater->update();

        $this->flash($result['success'] ? 'success' : 'error', $result['message'] . (!empty($result['output']) ? "\n" . $result['output'] : ''));
        $this->redirect('/admin/pembaruan');
    }
}
