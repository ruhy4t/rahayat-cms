<?php
/**
 * ============================================
 * Prestasi Controller
 * ============================================
 */

declare(strict_types=1);

class PrestasiController extends Controller
{
    private Prestasi $prestasiModel;

    public function __construct()
    {
        // We need auth and permission check for all methods EXCEPT publicIndex
        // The router sets 'action' in the URL parsing, but inside the controller we might not know it easily.
        // A simple check is the URI.
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = trim($uri, '/');

        $this->prestasiModel = new Prestasi();

        // If not accessing admin routes, bypass auth
        if (!str_starts_with($uri, 'admin/prestasi')) {
            return;
        }

        $this->requireAuth();

        // Ensure user has prestasi permission array
        $user = $this->currentUser();
        $userModel = new User();

        if (!$userModel->hasPermission($user, 'prestasi')) {
            $this->redirect('/admin');
        }
    }

    /**
     * Display list of prestasi
     */
    public function index(): void
    {
        $data = [
            'title' => 'Kelola Prestasi',
            'user' => $this->currentUser(),
            'prestasi' => $this->prestasiModel->getAllWithAuthor(),
            'flash' => $this->getFlash()
        ];

        $this->view('backend.prestasi.index', $data, 'backend');
    }

    /**
     * Display prestasi on public page
     */
    public function publicIndex(): void
    {
        // Don't require auth here

        $category = $this->get('kategori', '');

        if ($category && in_array($category, ['Sekolah', 'Guru', 'Murid'])) {
            // Since our model doesn't have a specific `getByCategory` we can use `findAllBy`
            $prestasi = $this->prestasiModel->findAllBy('category', $category, 'date', 'DESC');
        } else {
            $prestasi = $this->prestasiModel->getAllWithAuthor();
        }

        $data = [
            'title' => 'Prestasi',
            'prestasi' => $prestasi,
            'current_category' => $category
        ];

        $this->view('frontend.prestasi', $data, 'frontend');
    }

    /**
     * Show form for creating/editing prestasi
     */
    public function form(?int $id = null): void
    {
        $prestasi = null;
        if ($id) {
            $prestasi = $this->prestasiModel->findById($id);
            if (!$prestasi) {
                $_SESSION['flash'] = ['type' => 'error', 'message' => 'Data prestasi tidak ditemukan'];
                $this->redirect('/admin/prestasi');
            }
        }

        $data = [
            'title' => $id ? 'Edit Prestasi' : 'Tambah Prestasi Baru',
            'user' => $this->currentUser(),
            'prestasi' => $prestasi
        ];

        $this->view('backend.prestasi.form', $data, 'backend');
    }

    /**
     * Save prestasi data (create or update)
     */
    public function save(): void
    {
        if (!Security::isPost()) {
            $this->redirect('/admin/prestasi');
        }

        $this->requireCsrf();

        $id = $this->post('id') ? (int) $this->post('id') : null;
        $title = $this->post('title');
        $description = $this->post('description');
        $category = $this->post('category');
        $date = $this->post('date');

        // Validation
        if (empty($title) || empty($category) || empty($date)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Judul, Kategori, dan Tanggal wajib diisi'];
            $this->redirect($id ? '/admin/prestasi/edit/' . $id : '/admin/prestasi/tambah');
        }

        $data = [
            'title' => Security::sanitize($title),
            'description' => $description, // Keep HTML for rich text if defined
            'category' => $category,
            'date' => $date
        ];

        // Ensure category is valid enum
        if (!in_array($category, ['Sekolah', 'Guru', 'Murid'])) {
            $data['category'] = 'Sekolah';
        }

        // Handle Image Upload
        if (!empty($_FILES['image']['tmp_name'])) {
            $uploadedPath = $this->uploadFile($_FILES['image'], 'prestasi');
            if ($uploadedPath !== false) {
                $data['image'] = $uploadedPath;

                // Delete old image if exists
                if ($id) {
                    $old = $this->prestasiModel->findById($id);
                    if ($old && !empty($old['image'])) {
                        $oldPath = STORAGE_PATH . '/' . $old['image'];
                        if (file_exists($oldPath) && is_file($oldPath)) {
                            unlink($oldPath);
                        }
                    }
                }
            } else {
                $_SESSION['flash'] = ['type' => 'error', 'message' => $this->uploadErrorMessage('Gambar prestasi gagal diunggah')];
                $this->redirect($id ? "/admin/prestasi/edit/$id" : '/admin/prestasi/tambah');
            }
        }

        if ($id) {
            // Update
            $this->prestasiModel->update($id, $data);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data prestasi berhasil diperbarui'];
        } else {
            // Create
            $data['created_by'] = $this->currentUser()['id'];
            $this->prestasiModel->create($data);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data prestasi berhasil ditambahkan'];
        }

        $this->redirect('/admin/prestasi');
    }

    /**
     * Delete prestasi
     */
    public function delete(int $id): void
    {
        if (!Security::isPost()) {
            $this->jsonError('Metode tidak diizinkan', 405);
        }

        $this->requireCsrf();

        $prestasi = $this->prestasiModel->findById($id);
        if (!$prestasi) {
            $this->jsonError('Data tidak ditemukan', 404);
        }

        // Delete associated image
        if (!empty($prestasi['image'])) {
            $imagePath = STORAGE_PATH . '/' . $prestasi['image'];
            if (file_exists($imagePath) && is_file($imagePath)) {
                unlink($imagePath);
            }
        }

        if ($this->prestasiModel->delete($id)) {
            $this->json(['success' => true, 'message' => 'Data prestasi berhasil dihapus']);
        }

        $this->jsonError('Gagal menghapus data');
    }
}
