<?php
/**
 * ============================================
 * Ekstrakurikuler Controller
 * ============================================
 */

declare(strict_types=1);

class EkstrakurikulerController extends Controller
{
    private Ekstrakurikuler $ekskulModel;

    public function __construct()
    {
        $this->ekskulModel = new Ekstrakurikuler();
    }

    public function before(string $action): bool
    {
        $this->requireAuth();

        $user = $this->currentUser();
        $role = $user['role'] ?? '';

        // Admin always allowed
        if ($role === 'admin') {
            return true;
        }

        // GTK: check 'fasilitas' permission, sharing it for ekstrakurikuler
        if ($role === 'gtk') {
            $userModel = new User();
            if ($userModel->hasPermission($user, 'fasilitas')) {
                return true;
            }
        }

        // Murid/Ekskul/others: denied
        $this->flash('error', 'Anda tidak memiliki akses ke halaman ini');
        $this->redirect('/admin');
        return false;
    }

    public function index(): void
    {
        $data = [
            'title' => 'Kelola Ekstrakurikuler',
            'user' => $this->currentUser(),
            'ekskul' => $this->ekskulModel->all('sort_order', 'ASC'),
            'flash' => $this->getFlash()
        ];

        $this->view('backend.ekstrakurikuler.index', $data, 'backend');
    }

    public function store(): void
    {
        $this->requireCsrf();

        $data = [
            'name' => $this->postSafe('name'),
            'description' => $this->postSafe('description'),
            'schedule' => $this->postSafe('schedule'),
            'supervisor' => $this->postSafe('supervisor'),
            'sort_order' => (int) $this->post('sort_order', 0),
            'is_active' => $this->post('is_active') ? 1 : 0
        ];

        if (!empty($_FILES['image']['name'])) {
            $imagePath = $this->uploadFile($_FILES['image'], 'ekstrakurikuler');
            if ($imagePath) {
                $data['image'] = $imagePath;
            }
        }

        $this->ekskulModel->create($data);
        $this->flash('success', 'Ekstrakurikuler berhasil ditambahkan');
        $this->redirect('/admin/ekstrakurikuler');
    }

    public function update(string $id): void
    {
        $this->requireCsrf();
        $id = (int) $id;

        $data = [
            'name' => $this->postSafe('name'),
            'description' => $this->postSafe('description'),
            'schedule' => $this->postSafe('schedule'),
            'supervisor' => $this->postSafe('supervisor'),
            'sort_order' => (int) $this->post('sort_order', 0),
            'is_active' => $this->post('is_active') ? 1 : 0
        ];

        if (!empty($_FILES['image']['name'])) {
            $imagePath = $this->uploadFile($_FILES['image'], 'ekstrakurikuler');
            if ($imagePath) {
                $data['image'] = $imagePath;
            }
        }

        $this->ekskulModel->update($id, $data);
        $this->flash('success', 'Ekstrakurikuler berhasil diperbarui');
        $this->redirect('/admin/ekstrakurikuler');
    }

    public function delete(string $id): void
    {
        $this->requireCsrf();
        $this->ekskulModel->delete((int) $id);
        $this->flash('success', 'Ekstrakurikuler berhasil dihapus');
        $this->redirect('/admin/ekstrakurikuler');
    }
}
