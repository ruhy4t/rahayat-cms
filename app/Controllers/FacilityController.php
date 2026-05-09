<?php
/**
 * ============================================
 * Facility Controller
 * ============================================
 */

declare(strict_types=1);

class FacilityController extends Controller
{
    private Facility $facilityModel;

    public function __construct()
    {
        $this->facilityModel = new Facility();
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

        // GTK: check 'fasilitas' permission
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
            'title' => 'Kelola Fasilitas',
            'user' => $this->currentUser(),
            'facilities' => $this->facilityModel->all('sort_order', 'ASC'),
            'flash' => $this->getFlash()
        ];

        $this->view('backend.facilities.index', $data, 'backend');
    }

    public function store(): void
    {
        $this->requireCsrf();

        $data = [
            'name' => $this->postSafe('name'),
            'description' => $this->postSafe('description'),
            'type' => $this->postSafe('type') ?: 'lainnya',
            'capacity' => (int) $this->post('capacity', 0),
            'sort_order' => (int) $this->post('sort_order', 0),
            'is_active' => $this->post('is_active') ? 1 : 0
        ];

        if (!empty($_FILES['image']['name'])) {
            $imagePath = $this->uploadFile($_FILES['image'], 'facilities');
            if ($imagePath) {
                $data['image'] = $imagePath;
            }
        }

        $this->facilityModel->create($data);
        $this->flash('success', 'Fasilitas berhasil ditambahkan');
        $this->redirect('/admin/fasilitas');
    }

    public function update(string $id): void
    {
        $this->requireCsrf();
        $id = (int) $id;

        $data = [
            'name' => $this->postSafe('name'),
            'description' => $this->postSafe('description'),
            'type' => $this->postSafe('type') ?: 'lainnya',
            'capacity' => (int) $this->post('capacity', 0),
            'sort_order' => (int) $this->post('sort_order', 0),
            'is_active' => $this->post('is_active') ? 1 : 0
        ];

        if (!empty($_FILES['image']['name'])) {
            $imagePath = $this->uploadFile($_FILES['image'], 'facilities');
            if ($imagePath) {
                $data['image'] = $imagePath;
            }
        }

        $this->facilityModel->update($id, $data);
        $this->flash('success', 'Fasilitas berhasil diperbarui');
        $this->redirect('/admin/fasilitas');
    }

    public function delete(string $id): void
    {
        $this->requireCsrf();
        $this->facilityModel->delete((int) $id);
        $this->flash('success', 'Fasilitas berhasil dihapus');
        $this->redirect('/admin/fasilitas');
    }
}
