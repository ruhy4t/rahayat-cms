<?php
/**
 * ============================================
 * API Controller - AJAX Endpoints
 * ============================================
 */

declare(strict_types=1);

class ApiController extends Controller
{
    private News $newsModel;
    private NewsCategory $categoryModel;
    private HeroSlide $slideModel;
    private Menu $menuModel;
    private User $userModel;
    private GalleryItem $galleryItemModel;
    private GalleryAlbum $galleryAlbumModel;

    public function __construct()
    {
        $this->newsModel = new News();
        $this->categoryModel = new NewsCategory();
        $this->slideModel = new HeroSlide();
        $this->menuModel = new Menu();
        $this->userModel = new User();
        $this->galleryItemModel = new GalleryItem();
        $this->galleryAlbumModel = new GalleryAlbum();
    }

    /**
     * Before filter - check auth + role-based access for protected endpoints
     */
    public function before(string $action): bool
    {
        // Public endpoints
        $publicActions = ['news', 'newsShow'];

        if (!in_array($action, $publicActions)) {
            if (!$this->isLoggedIn()) {
                $this->jsonError('Unauthorized', 401);
                return false;
            }

            $user = $this->currentUser();
            $role = $user['role'] ?? '';

            // Admin has full access
            if ($role === 'admin') {
                return true;
            }

            // Admin-only endpoints
            $adminOnly = [
                'userStore',
                'userUpdate',
                'userDelete',
                'menuStore',
                'menuUpdate',
                'menuDelete',
            ];
            if (in_array($action, $adminOnly)) {
                $this->jsonError('Anda tidak memiliki akses', 403);
                return false;
            }

            // Permission-mapped endpoints
            $permissionMap = [
                'categoryStore' => 'kategori',
                'categoryUpdate' => 'kategori',
                'categoryDelete' => 'kategori',
                'slideStore' => 'slider',
                'slideUpdate' => 'slider',
                'slideDelete' => 'slider',
                'slideToggle' => 'slider',
                'galleryStore' => 'galeri',
                'galleryUpdate' => 'galeri',
                'galleryDelete' => 'galeri',
                'albumStore' => 'galeri',
                'albumUpdate' => 'galeri',
                'albumDelete' => 'galeri',
                'videoStore' => 'galeri',
                'newsStore' => 'berita',
                'newsUpdate' => 'berita',
                'newsDelete' => 'berita',
            ];

            $requiredPerm = $permissionMap[$action] ?? null;
            if ($requiredPerm && !$this->userModel->hasPermission($user, $requiredPerm)) {
                $this->jsonError('Anda tidak memiliki akses', 403);
                return false;
            }

            // Murid/Ekskul cannot delete news
            if (in_array($role, ['murid', 'ekskul']) && $action === 'newsDelete') {
                $this->jsonError('Anda tidak memiliki akses untuk menghapus berita', 403);
                return false;
            }
        }

        return true;
    }

    /**
     * Get all news (paginated)
     */
    public function news(): void
    {
        $page = (int) ($this->get('page', 1));
        $perPage = (int) ($this->get('per_page', ITEMS_PER_PAGE));

        $result = $this->newsModel->paginateWithAuthor($page, $perPage);

        $this->jsonSuccess($result);
    }

    /**
     * Get single news
     */
    public function newsShow(string $id): void
    {
        $news = $this->newsModel->find((int) $id);

        if (!$news) {
            $this->jsonError('Berita tidak ditemukan', 404);
        }

        $this->jsonSuccess($news);
    }

    /**
     * Store news via AJAX
     */
    public function newsStore(): void
    {
        // Validate CSRF
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $this->post(CSRF_TOKEN_NAME);
        if (!Security::validateCsrfToken($token)) {
            $this->jsonError('Invalid CSRF token', 403);
        }

        // Validate input
        $title = $this->postSafe('title');
        $content = $this->post('content', '');

        if (empty($title)) {
            $this->jsonError('Judul harus diisi', 422, ['title' => 'Judul harus diisi']);
        }

        // Helper to get category name
        $categoryId = $this->post('category_id');
        $categoryName = $this->postSafe('category');

        if ($categoryId) {
            $catEntry = $this->categoryModel->find((int) $categoryId);
            if ($catEntry) {
                $categoryName = $catEntry['name'];
            }
        }

        $data = [
            'title' => $title,
            'content' => $content,
            'excerpt' => $this->postSafe('excerpt') ?: mb_substr(strip_tags($content), 0, 150) . '...',
            'category_id' => $categoryId ?: null,
            'category' => $categoryName ?: 'Umum',
            'status' => $this->postSafe('status') ?: 'draft',
            'author_id' => $_SESSION['user_id'],
            'slug' => $this->newsModel->generateSlug($title)
        ];

        // Set published_at if publishing
        if ($data['status'] === 'published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        // Murid/Ekskul: force draft status (server-side enforcement)
        $user = $this->currentUser();
        if (in_array($user['role'] ?? '', ['murid', 'ekskul'])) {
            $data['status'] = 'draft';
            $data['published_at'] = null;
        }

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $imagePath = $this->uploadFile($_FILES['image']);
            if ($imagePath) {
                $data['image'] = $imagePath;
            }
        }

        try {
            $id = $this->newsModel->create($data);
            $news = $this->newsModel->find((int) $id);

            $this->jsonSuccess($news, 'Berita berhasil ditambahkan');
        } catch (Exception $e) {
            $this->jsonError('Gagal menyimpan berita: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update news via AJAX
     */
    public function newsUpdate(string $id): void
    {
        // Validate CSRF
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $this->post(CSRF_TOKEN_NAME);
        if (!Security::validateCsrfToken($token)) {
            $this->jsonError('Invalid CSRF token', 403);
        }

        $newsId = (int) $id;
        $news = $this->newsModel->find($newsId);

        if (!$news) {
            $this->jsonError('Berita tidak ditemukan', 404);
        }

        // Validate input
        $title = $this->postSafe('title');
        if (empty($title)) {
            $this->jsonError('Judul harus diisi', 422, ['title' => 'Judul harus diisi']);
        }

        $content = $this->post('content', '');

        // Helper to get category name
        $categoryId = $this->post('category_id');
        $categoryName = $this->postSafe('category');

        if ($categoryId) {
            $catEntry = $this->categoryModel->find((int) $categoryId);
            if ($catEntry) {
                $categoryName = $catEntry['name'];
            }
        }

        $data = [
            'title' => $title,
            'content' => $content,
            'excerpt' => $this->postSafe('excerpt') ?: mb_substr(strip_tags($content), 0, 150) . '...',
            'category_id' => $categoryId ?: null,
            'category' => $categoryName ?: 'Umum',
            'status' => $this->postSafe('status') ?: 'draft'
        ];

        // Update slug if title changed
        if ($title !== $news['title']) {
            $data['slug'] = $this->newsModel->generateSlug($title, $newsId);
        }

        // Update published_at if status changed to published
        if ($data['status'] === 'published' && $news['status'] !== 'published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        // Murid/Ekskul: force draft status (server-side enforcement)
        $currentUser = $this->currentUser();
        if (in_array($currentUser['role'] ?? '', ['murid', 'ekskul'])) {
            $data['status'] = 'draft';
            unset($data['published_at']);
        }

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $imagePath = $this->uploadFile($_FILES['image']);
            if ($imagePath) {
                $data['image'] = $imagePath;
            }
        }

        try {
            $this->newsModel->update($newsId, $data);
            $updatedNews = $this->newsModel->find($newsId);

            $this->jsonSuccess($updatedNews, 'Berita berhasil diperbarui');
        } catch (Exception $e) {
            $this->jsonError('Gagal memperbarui berita: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete news via AJAX
     */
    public function newsDelete(string $id): void
    {
        // Validate CSRF
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $this->post(CSRF_TOKEN_NAME);
        if (!Security::validateCsrfToken($token)) {
            $this->jsonError('Invalid CSRF token', 403);
        }

        $newsId = (int) $id;
        $news = $this->newsModel->find($newsId);

        if (!$news) {
            $this->jsonError('Berita tidak ditemukan', 404);
        }

        try {
            $this->newsModel->delete($newsId);
            $this->jsonSuccess(null, 'Berita berhasil dihapus');
        } catch (Exception $e) {
            $this->jsonError('Gagal menghapus berita: ' . $e->getMessage(), 500);
        }
    }

    // =========================================================
    // CATEGORY CRUD
    // =========================================================

    /**
     * Get all categories
     */
    public function categories(): void
    {
        $categories = $this->categoryModel->getActive();
        $this->jsonSuccess($categories);
    }

    /**
     * Store category
     */
    public function categoryStore(): void
    {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $this->post(CSRF_TOKEN_NAME) ?? $this->post('csrf_token');
        if (!Security::validateCsrfToken($token)) {
            $this->jsonError('Invalid CSRF token', 403);
            return;
        }

        $name = $this->postSafe('name');
        if (empty($name)) {
            $this->jsonError('Nama kategori harus diisi', 422);
            return;
        }

        $data = [
            'name' => $name,
            'slug' => $this->categoryModel->generateSlug($name),
            'color' => $this->post('color') ?: '#4F46E5',
            'is_active' => $this->post('is_active') ? 1 : 0
        ];

        try {
            $id = $this->categoryModel->create($data);
            $category = $this->categoryModel->find((int) $id);
            $this->jsonSuccess($category, 'Kategori berhasil ditambahkan');
        } catch (Exception $e) {
            $this->jsonError('Gagal menyimpan kategori: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update category
     */
    public function categoryUpdate(string $id): void
    {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $this->post(CSRF_TOKEN_NAME) ?? $this->post('csrf_token');
        if (!Security::validateCsrfToken($token)) {
            $this->jsonError('Invalid CSRF token', 403);
            return;
        }

        $categoryId = (int) $id;
        $category = $this->categoryModel->find($categoryId);

        if (!$category) {
            $this->jsonError('Kategori tidak ditemukan', 404);
            return;
        }

        $name = $this->postSafe('name');
        if (empty($name)) {
            $this->jsonError('Nama kategori harus diisi', 422);
            return;
        }

        $data = [
            'name' => $name,
            'color' => $this->post('color') ?: $category['color'],
            'is_active' => $this->post('is_active') ? 1 : 0
        ];

        // Update slug if name changed
        if ($name !== $category['name']) {
            $data['slug'] = $this->categoryModel->generateSlug($name, $categoryId);
        }

        try {
            $this->categoryModel->update($categoryId, $data);
            $updated = $this->categoryModel->find($categoryId);
            $this->jsonSuccess($updated, 'Kategori berhasil diperbarui');
        } catch (Exception $e) {
            $this->jsonError('Gagal memperbarui kategori: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete category
     */
    public function categoryDelete(string $id): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $input['csrf_token'] ?? $this->post(CSRF_TOKEN_NAME);
        if (!Security::validateCsrfToken($token)) {
            $this->jsonError('Invalid CSRF token', 403);
            return;
        }

        $categoryId = (int) $id;
        $category = $this->categoryModel->find($categoryId);

        if (!$category) {
            $this->jsonError('Kategori tidak ditemukan', 404);
            return;
        }

        try {
            $this->categoryModel->delete($categoryId);
            $this->jsonSuccess(null, 'Kategori berhasil dihapus');
        } catch (Exception $e) {
            $this->jsonError('Gagal menghapus kategori: ' . $e->getMessage(), 500);
        }
    }

    // =========================================================
    // SLIDE CRUD
    // =========================================================

    /**
     * Store slide
     */
    public function slideStore(): void
    {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $this->post(CSRF_TOKEN_NAME) ?? $this->post('csrf_token');
        if (!Security::validateCsrfToken($token)) {
            $this->jsonError('Invalid CSRF token', 403);
            return;
        }

        // Handle image upload (required for new slide)
        if (empty($_FILES['image']['name'])) {
            $this->jsonError('Gambar slide harus diunggah', 422);
            return;
        }

        $imagePath = $this->uploadFile($_FILES['image'], 'slides');
        if (!$imagePath) {
            $this->jsonError('Gagal mengunggah gambar', 422);
            return;
        }

        $data = [
            'title' => $this->postSafe('title') ?: null,
            'subtitle' => $this->postSafe('subtitle') ?: null,
            'image' => $imagePath,
            'button_text' => $this->postSafe('button_text') ?: null,
            'button_url' => $this->postSafe('button_url') ?: null,
            'sort_order' => (int) $this->post('sort_order', 0),
            'is_active' => $this->post('is_active') ? 1 : 0
        ];

        try {
            $id = $this->slideModel->create($data);
            $slide = $this->slideModel->find((int) $id);
            $this->jsonSuccess($slide, 'Slide berhasil ditambahkan');
        } catch (Exception $e) {
            $this->jsonError('Gagal menyimpan slide: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update slide
     */
    public function slideUpdate(): void
    {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $this->post(CSRF_TOKEN_NAME) ?? $this->post('csrf_token');
        if (!Security::validateCsrfToken($token)) {
            $this->jsonError('Invalid CSRF token', 403);
            return;
        }

        $slideId = (int) $this->post('id');
        $slide = $this->slideModel->find($slideId);

        if (!$slide) {
            $this->jsonError('Slide tidak ditemukan', 404);
            return;
        }

        $data = [
            'title' => $this->postSafe('title') ?: null,
            'subtitle' => $this->postSafe('subtitle') ?: null,
            'sort_order' => (int) $this->post('sort_order', 0),
            'is_active' => $this->post('is_active') ? 1 : 0
        ];

        // Handle image upload if provided
        if (!empty($_FILES['image']['name'])) {
            $imagePath = $this->uploadFile($_FILES['image'], 'slides');
            if ($imagePath) {
                $data['image'] = $imagePath;
            }
        }

        try {
            $this->slideModel->update($slideId, $data);
            $updated = $this->slideModel->find($slideId);
            $this->jsonSuccess($updated, 'Slide berhasil diperbarui');
        } catch (Exception $e) {
            $this->jsonError('Gagal memperbarui slide: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete slide
     */
    public function slideDelete(string $id): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $input['csrf_token'] ?? $this->post(CSRF_TOKEN_NAME);
        if (!Security::validateCsrfToken($token)) {
            $this->jsonError('Invalid CSRF token', 403);
            return;
        }

        $slideId = (int) $id;
        $slide = $this->slideModel->find($slideId);

        if (!$slide) {
            $this->jsonError('Slide tidak ditemukan', 404);
            return;
        }

        try {
            $this->slideModel->delete($slideId);
            $this->jsonSuccess(null, 'Slide berhasil dihapus');
        } catch (Exception $e) {
            $this->jsonError('Gagal menghapus slide: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Toggle slide active status
     */
    public function slideToggle(string $id): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $input['csrf_token'] ?? $this->post(CSRF_TOKEN_NAME);
        if (!Security::validateCsrfToken($token)) {
            $this->jsonError('Invalid CSRF token', 403);
            return;
        }

        $slideId = (int) $id;

        try {
            $result = $this->slideModel->toggleActive($slideId);
            if ($result) {
                $this->jsonSuccess(null, 'Status slide berhasil diubah');
            } else {
                $this->jsonError('Slide tidak ditemukan', 404);
            }
        } catch (Exception $e) {
            $this->jsonError('Gagal mengubah status: ' . $e->getMessage(), 500);
        }
    }

    // =========================================================
    // MENU CRUD
    // =========================================================

    /**
     * Store menu
     */
    public function menuStore(): void
    {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $this->post(CSRF_TOKEN_NAME) ?? $this->post('csrf_token');
        if (!Security::validateCsrfToken($token)) {
            $this->jsonError('Invalid CSRF token', 403);
            return;
        }

        $title = $this->postSafe('title');
        $url = $this->postSafe('url');

        if (empty($title) || empty($url)) {
            $this->jsonError('Judul dan URL harus diisi', 422);
            return;
        }

        $data = [
            'title' => $title,
            'url' => $url,
            'icon' => $this->postSafe('icon') ?: null,
            'parent_id' => $this->post('parent_id') ?: null,
            'sort_order' => (int) $this->post('sort_order', 0),
            'is_active' => $this->post('is_active') ? 1 : 0,
            'target' => $this->post('target') ?: '_self',
            'menu_location' => $this->post('menu_location') ?: 'header'
        ];

        try {
            $id = $this->menuModel->create($data);
            $menu = $this->menuModel->find((int) $id);
            $this->jsonSuccess($menu, 'Menu berhasil ditambahkan');
        } catch (Exception $e) {
            $this->jsonError('Gagal menyimpan menu: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update menu
     */
    public function menuUpdate(string $id): void
    {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $this->post(CSRF_TOKEN_NAME) ?? $this->post('csrf_token');
        if (!Security::validateCsrfToken($token)) {
            $this->jsonError('Invalid CSRF token', 403);
            return;
        }

        $menuId = (int) $id;
        $menu = $this->menuModel->find($menuId);

        if (!$menu) {
            $this->jsonError('Menu tidak ditemukan', 404);
            return;
        }

        $title = $this->postSafe('title');
        $url = $this->postSafe('url');

        if (empty($title) || empty($url)) {
            $this->jsonError('Judul dan URL harus diisi', 422);
            return;
        }

        $data = [
            'title' => $title,
            'url' => $url,
            'icon' => $this->postSafe('icon') ?: null,
            'parent_id' => $this->post('parent_id') ?: null,
            'sort_order' => (int) $this->post('sort_order', 0),
            'is_active' => $this->post('is_active') ? 1 : 0,
            'target' => $this->post('target') ?: '_self',
            'menu_location' => $this->post('menu_location') ?: 'header'
        ];

        try {
            $this->menuModel->update($menuId, $data);
            $updated = $this->menuModel->find($menuId);
            $this->jsonSuccess($updated, 'Menu berhasil diperbarui');
        } catch (Exception $e) {
            $this->jsonError('Gagal memperbarui menu: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete menu
     */
    public function menuDelete(string $id): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $input['csrf_token'] ?? $this->post(CSRF_TOKEN_NAME);
        if (!Security::validateCsrfToken($token)) {
            $this->jsonError('Invalid CSRF token', 403);
            return;
        }

        $menuId = (int) $id;
        $menu = $this->menuModel->find($menuId);

        if (!$menu) {
            $this->jsonError('Menu tidak ditemukan', 404);
            return;
        }

        try {
            $this->menuModel->delete($menuId);
            $this->jsonSuccess(null, 'Menu berhasil dihapus');
        } catch (Exception $e) {
            $this->jsonError('Gagal menghapus menu: ' . $e->getMessage(), 500);
        }
    }

    // =========================================================
    // USER CRUD
    // =========================================================

    /**
     * Store user
     */
    public function userStore(): void
    {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $this->post(CSRF_TOKEN_NAME) ?? $this->post('csrf_token');
        if (!Security::validateCsrfToken($token)) {
            $this->jsonError('Invalid CSRF token', 403);
            return;
        }

        $username = $this->postSafe('username');
        $email = $this->postSafe('email');
        $password = $this->post('password');
        $name = $this->postSafe('name');

        if (empty($username) || empty($email) || empty($password) || empty($name)) {
            $this->jsonError('Semua field harus diisi', 422);
            return;
        }
        if (strlen($password) < 8) {
            $this->jsonError('Password minimal 8 karakter', 422);
            return;
        }

        // Check if username or email exists
        if ($this->userModel->findByUsername($username)) {
            $this->jsonError('Username sudah digunakan', 422);
            return;
        }
        if ($this->userModel->findByEmail($email)) {
            $this->jsonError('Email sudah digunakan', 422);
            return;
        }

        // Validate role
        $validRoles = ['admin', 'gtk', 'murid', 'ekskul'];
        $role = $this->post('role') ?: 'murid';
        if (!in_array($role, $validRoles)) {
            $role = 'murid';
        }

        $data = [
            'username' => $username,
            'email' => $email,
            'password' => $password, // Will be hashed in createUser
            'name' => $name,
            'role' => $role,
            'is_active' => $this->post('is_active') ? 1 : 0
        ];

        // Handle GTK permissions
        if ($role === 'gtk') {
            $permissions = $this->post('permissions');
            if (is_array($permissions)) {
                // Filter to valid permissions only
                $data['permissions'] = array_values(array_intersect($permissions, User::GTK_PERMISSIONS));
            } else {
                // Default: all permissions
                $data['permissions'] = User::GTK_PERMISSIONS;
            }
        }

        try {
            $id = $this->userModel->createUser($data);
            $user = $this->userModel->find((int) $id);
            unset($user['password']);
            $this->jsonSuccess($user, 'Pengguna berhasil ditambahkan');
        } catch (Exception $e) {
            $this->jsonError('Gagal menyimpan pengguna: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update user
     */
    public function userUpdate(string $id): void
    {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $this->post(CSRF_TOKEN_NAME) ?? $this->post('csrf_token');
        if (!Security::validateCsrfToken($token)) {
            $this->jsonError('Invalid CSRF token', 403);
            return;
        }

        $userId = (int) $id;
        $user = $this->userModel->find($userId);

        if (!$user) {
            $this->jsonError('Pengguna tidak ditemukan', 404);
            return;
        }

        // Validate role
        $validRoles = ['admin', 'gtk', 'murid', 'ekskul'];
        $newRole = $this->post('role') ?: $user['role'];
        if (!in_array($newRole, $validRoles)) {
            $newRole = $user['role'];
        }

        $data = [
            'name' => $this->postSafe('name') ?: $user['name'],
            'email' => $this->postSafe('email') ?: $user['email'],
            'role' => $newRole,
            'is_active' => array_key_exists('is_active', $_POST) ? (intval($this->post('is_active')) ? 1 : 0) : $user['is_active']
        ];

        // Handle GTK permissions
        if ($newRole === 'gtk') {
            $permissions = $this->post('permissions');
            if (is_array($permissions)) {
                $data['permissions'] = json_encode(array_values(array_intersect($permissions, User::GTK_PERMISSIONS)));
            }
        } else {
            // Clear permissions for non-GTK roles
            $data['permissions'] = null;
        }

        // Check if email is taken by another user
        $existing = $this->userModel->findByEmail($data['email']);
        if ($existing && $existing['id'] != $userId) {
            $this->jsonError('Email sudah digunakan', 422);
            return;
        }

        // Update password if provided
        $password = $this->post('password');
        if (!empty($password)) {
            if (strlen($password) < 8) {
                $this->jsonError('Password minimal 8 karakter', 422);
                return;
            }
            $this->userModel->updatePassword($userId, $password);
        }

        try {
            $this->userModel->update($userId, $data);
            $updated = $this->userModel->find($userId);
            unset($updated['password']);
            $this->jsonSuccess($updated, 'Pengguna berhasil diperbarui');
        } catch (Exception $e) {
            $this->jsonError('Gagal memperbarui pengguna: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete user
     */
    public function userDelete(string $id): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $input['csrf_token'] ?? $this->post(CSRF_TOKEN_NAME);
        if (!Security::validateCsrfToken($token)) {
            $this->jsonError('Invalid CSRF token', 403);
            return;
        }

        $userId = (int) $id;
        $user = $this->userModel->find($userId);

        if (!$user) {
            $this->jsonError('Pengguna tidak ditemukan', 404);
            return;
        }

        // Prevent deleting self
        if ($userId === (int) $_SESSION['user_id']) {
            $this->jsonError('Tidak dapat menghapus akun sendiri', 422);
            return;
        }

        try {
            $this->userModel->delete($userId);
            $this->jsonSuccess(null, 'Pengguna berhasil dihapus');
        } catch (Exception $e) {
            $this->jsonError('Gagal menghapus pengguna: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store gallery item (upload image/video)
     */
    public function galleryStore(): void
    {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $this->post(CSRF_TOKEN_NAME) ?? $this->post('csrf_token');
        if (!Security::validateCsrfToken($token)) {
            $this->jsonError('Invalid CSRF token', 403);
            return;
        }

        // Get album ID
        $albumId = (int) $this->post('album_id');
        if (!$albumId) {
            $this->jsonError('Album harus dipilih', 422);
            return;
        }

        // Check if album exists
        $album = $this->galleryAlbumModel->find($albumId);
        if (!$album) {
            $this->jsonError('Album tidak ditemukan', 404);
            return;
        }

        // Handle file upload
        $type = $this->post('type', 'image');
        $filePath = null;
        $youtubeUrl = null;
        $youtubeVideoId = null;

        if ($type === 'image') {
            if (empty($_FILES['file']['name'])) {
                $this->jsonError('Gambar harus diunggah', 422);
                return;
            }

            $filePath = $this->uploadFile($_FILES['file'], 'uploads/gallery');
            if (!$filePath) {
                $this->jsonError('Gagal mengunggah gambar', 422);
                return;
            }
        } elseif ($type === 'video') {
            $youtubeUrl = $this->postSafe('youtube_url');
            if (!$youtubeUrl) {
                $this->jsonError('URL YouTube harus diisi', 422);
                return;
            }

            // Extract video ID from YouTube URL
            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $youtubeUrl, $matches)) {
                $youtubeVideoId = $matches[1];
            } else {
                $this->jsonError('URL YouTube tidak valid', 422);
                return;
            }
        }

        $data = [
            'album_id' => $albumId,
            'title' => $this->postSafe('title') ?: null,
            'description' => $this->postSafe('description') ?: null,
            'type' => $type,
            'file_path' => $filePath,
            'youtube_url' => $youtubeUrl,
            'youtube_video_id' => $youtubeVideoId,
            'is_active' => 1,
            'sort_order' => (int) $this->post('sort_order', 0)
        ];

        try {
            $id = $this->galleryItemModel->create($data);
            $item = $this->galleryItemModel->find((int) $id);
            $this->jsonSuccess($item, 'Gambar berhasil ditambahkan');
        } catch (Exception $e) {
            $this->jsonError('Gagal menyimpan gambar: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update gallery item
     */
    public function galleryUpdate(string $id): void
    {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $this->post(CSRF_TOKEN_NAME) ?? $this->post('csrf_token');
        if (!Security::validateCsrfToken($token)) {
            $this->jsonError('Invalid CSRF token', 403);
            return;
        }

        $itemId = (int) $id;
        $item = $this->galleryItemModel->find($itemId);

        if (!$item) {
            $this->jsonError('Gambar tidak ditemukan', 404);
            return;
        }

        // Prepare data to update
        $data = [
            'title' => $this->postSafe('title') ?: null,
            'description' => $this->postSafe('description') ?: null,
            'is_active' => $this->post('is_active') ? 1 : 0
        ];

        $type = $this->post('type', $item['type']);

        // Handle video (YouTube URL) update
        if ($type === 'video') {
            $youtubeUrl = $this->postSafe('youtube_url');
            if ($youtubeUrl && $youtubeUrl !== ($item['youtube_url'] ?? '')) {
                // Extract video ID
                $videoId = GalleryItem::extractYouTubeId($youtubeUrl);
                if (!$videoId) {
                    $this->jsonError('URL YouTube tidak valid', 422);
                    return;
                }
                $data['youtube_url'] = $youtubeUrl;
                $data['youtube_video_id'] = $videoId;
            }
        }

        // Handle file replacement if new file uploaded (for image items)
        if ($type === 'image' && !empty($_FILES['file']['name'])) {
            $filePath = $this->uploadFile($_FILES['file'], 'uploads/gallery');
            if ($filePath) {
                // Delete old file
                if (!empty($item['file_path'])) {
                    $oldPath = STORAGE_PATH . '/' . $item['file_path'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $data['file_path'] = $filePath;
            } else {
                $this->jsonError('Gagal mengunggah gambar baru', 422);
                return;
            }
        }

        try {
            $this->galleryItemModel->update($itemId, $data);
            $updatedItem = $this->galleryItemModel->find($itemId);
            $this->jsonSuccess($updatedItem, 'Item berhasil diperbarui');
        } catch (Exception $e) {
            $this->jsonError('Gagal memperbarui item: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete gallery item
     */
    public function galleryDelete(string $id): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $input['csrf_token'] ?? $this->post(CSRF_TOKEN_NAME);
        if (!Security::validateCsrfToken($token)) {
            $this->jsonError('Invalid CSRF token', 403);
            return;
        }

        $itemId = (int) $id;
        $item = $this->galleryItemModel->find($itemId);
        if (!$item) {
            $this->jsonError('Gambar tidak ditemukan', 404);
            return;
        }

        try {
            // Delete file from storage
            if (!empty($item['file_path'])) {
                $filePath = STORAGE_PATH . '/' . $item['file_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $this->galleryItemModel->delete($itemId);
            $this->jsonSuccess(null, 'Gambar berhasil dihapus');
        } catch (Exception $e) {
            $this->jsonError('Gagal menghapus gambar: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store gallery album
     */
    public function albumStore(): void
    {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $this->post(CSRF_TOKEN_NAME) ?? $this->post('csrf_token');
        if (!Security::validateCsrfToken($token)) {
            $this->jsonError('Invalid CSRF token', 403);
            return;
        }

        $title = $this->postSafe('title');
        if (!$title) {
            $this->jsonError('Judul album harus diisi', 422);
            return;
        }

        // Validate album type
        $type = $this->postSafe('type');
        if (!in_array($type, ['foto', 'video'])) {
            $type = 'foto';
        }

        $data = [
            'title' => $title,
            'slug' => $this->galleryAlbumModel->generateSlug($title),
            'description' => $this->postSafe('description') ?: null,
            'type' => $type,
            'is_active' => $this->post('is_active') ? 1 : 0
        ];

        // Handle Cover Image Upload
        if (!empty($_FILES['file']['name'])) {
            $filePath = $this->uploadFile($_FILES['file'], 'uploads/albums');
            if ($filePath) {
                $data['cover_image'] = $filePath;
            } else {
                $this->jsonError('Gagal mengunggah gambar sampul', 422);
                return;
            }
        }

        try {
            $id = $this->galleryAlbumModel->create($data);
            $album = $this->galleryAlbumModel->find((int) $id);
            $this->jsonSuccess($album, 'Album berhasil ditambahkan');
        } catch (Exception $e) {
            $this->jsonError('Gagal menyimpan album: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update gallery album
     */
    public function albumUpdate(string $id): void
    {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $this->post(CSRF_TOKEN_NAME) ?? $this->post('csrf_token');
        if (!Security::validateCsrfToken($token)) {
            $this->jsonError('Invalid CSRF token', 403);
            return;
        }

        $albumId = (int) $id;
        $album = $this->galleryAlbumModel->find($albumId);
        if (!$album) {
            $this->jsonError('Album tidak ditemukan', 404);
            return;
        }

        $title = $this->postSafe('title');
        if (!$title) {
            $this->jsonError('Judul album harus diisi', 422);
            return;
        }

        // Validate album type
        $type = $this->postSafe('type');

        $data = [
            'title' => $title,
            'slug' => $this->galleryAlbumModel->generateSlug($title, $albumId),
            'description' => $this->postSafe('description') ?: null,
            'is_active' => $this->post('is_active') ? 1 : 0
        ];

        if ($type && in_array($type, ['foto', 'video'])) {
            $data['type'] = $type;
        }

        // Handle Cover Image Upload
        if (!empty($_FILES['file']['name'])) {
            $filePath = $this->uploadFile($_FILES['file'], 'uploads/albums');
            if ($filePath) {
                // Delete old cover
                if (!empty($album['cover_image'])) {
                    $oldPath = STORAGE_PATH . '/' . $album['cover_image'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $data['cover_image'] = $filePath;
            } else {
                $this->jsonError('Gagal mengunggah gambar sampul baru', 422);
                return;
            }
        }

        try {
            $this->galleryAlbumModel->update($albumId, $data);
            $updated = $this->galleryAlbumModel->find($albumId);
            $this->jsonSuccess($updated, 'Album berhasil diperbarui');
        } catch (Exception $e) {
            $this->jsonError('Gagal memperbarui album: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete gallery album
     */
    public function albumDelete(string $id): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $input['csrf_token'] ?? $this->post(CSRF_TOKEN_NAME);
        if (!Security::validateCsrfToken($token)) {
            $this->jsonError('Invalid CSRF token', 403);
            return;
        }

        $albumId = (int) $id;
        $album = $this->galleryAlbumModel->find($albumId);
        if (!$album) {
            $this->jsonError('Album tidak ditemukan', 404);
            return;
        }

        try {
            $this->galleryAlbumModel->delete($albumId);
            $this->jsonSuccess(null, 'Album berhasil dihapus');
        } catch (Exception $e) {
            $this->jsonError('Gagal menghapus album: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Change Password
     */
    public function changePassword(): void
    {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $this->post(CSRF_TOKEN_NAME) ?? $this->post('csrf_token');
        if (!Security::validateCsrfToken($token)) {
            $this->jsonError('Invalid CSRF token', 403);
            return;
        }

        $userId = (int) $_SESSION['user_id'];
        $oldPassword = $this->post('old_password');
        $newPassword = $this->post('new_password');

        $user = $this->userModel->find($userId);
        if (!$user) {
            $this->jsonError('User tidak ditemukan', 404);
            return;
        }
        
        if (!Security::verifyPassword($oldPassword, $user['password'])) {
            $this->jsonError('Password lama tidak sesuai', 422);
            return;
        }
        if (strlen((string) $newPassword) < 8) {
            $this->jsonError('Password baru minimal 8 karakter', 422);
            return;
        }

        try {
            $this->userModel->updatePassword($userId, $newPassword);
            $this->jsonSuccess(null, 'Password berhasil diubah');
        } catch (Exception $e) {
            $this->jsonError('Gagal mengubah password: ' . $e->getMessage(), 500);
        }
    }
}
