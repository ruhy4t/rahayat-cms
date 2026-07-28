<?php

declare(strict_types=1);

class AlumniController extends Controller
{
    private const STATUSES = ['pending', 'approved', 'rejected'];
    private const MAX_PHOTO_SIZE = 2 * 1024 * 1024;

    private Alumni $alumniModel;

    public function __construct()
    {
        $this->alumniModel = new Alumni();
    }

    public function before(string $action): bool
    {
        if (in_array($action, ['publicIndex', 'show', 'store'], true)) {
            return true;
        }
        $this->requireAuth();
        if (($this->currentUser()['role'] ?? '') !== 'admin') {
            $this->flash('error', 'Pengelolaan alumni hanya dapat diakses administrator.');
            $this->redirect('/admin');
            return false;
        }
        return true;
    }

    public function publicIndex(): void
    {
        $term = mb_substr(trim((string) $this->get('q', '')), 0, 80);
        $yearRaw = trim((string) $this->get('tahun', ''));
        $year = preg_match('/^\d{4}$/', $yearRaw) ? (int) $yearRaw : null;
        $city = mb_substr(trim((string) $this->get('kota', '')), 0, 100);
        $occupation = mb_substr(trim((string) $this->get('pekerjaan', '')), 0, 120);
        $page = max(1, (int) $this->get('page', 1));

        $this->view('frontend.alumni.index', [
            'title' => 'Direktori Alumni',
            'result' => $this->alumniModel->searchPublic($term, $year, $city, $occupation, $page),
            'filters' => $this->alumniModel->publicFilters(),
            'query' => compact('term', 'yearRaw', 'city', 'occupation'),
            'flash' => $this->getFlash(),
            'enableContentProtection' => true,
        ], 'frontend');
    }

    public function show(string $id): void
    {
        $alumni = $this->alumniModel->findPublic((int) $id);
        if (!$alumni) {
            http_response_code(404);
            $this->view('errors.404', ['title' => 'Alumni Tidak Ditemukan'], 'frontend');
            return;
        }
        $this->view('frontend.alumni.show', [
            'title' => $alumni['name'],
            'alumni' => $alumni,
            'enableContentProtection' => true,
        ], 'frontend');
    }

    public function store(): void
    {
        if (!Security::isPost()) {
            $this->redirect('/alumni');
        }
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesi formulir telah berakhir. Muat ulang halaman lalu coba kembali.');
            $this->redirect('/alumni#daftar-alumni');
        }
        if (trim((string) $this->post('website', '')) !== '') {
            $this->flash('success', 'Data alumni telah dikirim untuk diverifikasi.');
            $this->redirect('/alumni');
        }
        if (!Security::validatePublicCaptcha(
            'alumni',
            (string) $this->post('captcha_token', ''),
            $this->post('captcha_answer', '')
        )) {
            $this->flash('error', 'Jawaban verifikasi keamanan tidak tepat. Silakan coba kembali.');
            $this->redirect('/alumni#daftar-alumni');
        }

        $ipHash = (string) DataCipher::blindIndex('ip:' . trim((string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown')));
        if ((time() - (int) ($_SESSION['_alumni_last_submit'] ?? 0)) < 90
            || $this->alumniModel->countRecentByIpHash($ipHash) >= 3) {
            $this->flash('error', 'Terlalu banyak pengiriman. Silakan coba kembali beberapa saat lagi.');
            $this->redirect('/alumni');
        }

        $data = $this->validatedData(false);
        if ($data === null) {
            $this->redirect('/alumni#daftar-alumni');
        }

        $contact = trim((string) $this->post('contact', ''));
        $contactHash = DataCipher::blindIndex($contact);
        if ($contactHash && $this->alumniModel->findBy('contact_hash', $contactHash)) {
            $this->flash('error', 'Kontak tersebut sudah pernah digunakan untuk pendataan alumni.');
            $this->redirect('/alumni#daftar-alumni');
        }
        $data['contact_encrypted'] = DataCipher::encrypt($contact);
        $data['contact_hash'] = $contactHash;
        $data['status'] = 'pending';
        $data['is_featured'] = 0;
        $data['sort_order'] = 0;
        $data['submitted_ip_hash'] = $ipHash;

        if ($this->hasPhotoUpload()) {
            $photo = $this->uploadAlumniPhoto();
            if (!$photo) {
                $this->flash('error', $this->uploadErrorMessage('Foto alumni gagal diunggah'));
                $this->redirect('/alumni#daftar-alumni');
            }
            $data['photo'] = $photo;
        }

        Security::consumePublicCaptcha('alumni', (string) $this->post('captcha_token', ''));
        $this->alumniModel->create($data);
        $_SESSION['_alumni_last_submit'] = time();
        $this->flash('success', 'Terima kasih. Data alumni akan tampil setelah diverifikasi admin.');
        $this->redirect('/alumni');
    }

    public function adminIndex(): void
    {
        $items = $this->alumniModel->getAllForAdmin();
        foreach ($items as &$item) {
            $item['contact_plain'] = DataCipher::decrypt($item['contact_encrypted'] ?? null);
            unset($item['contact_encrypted'], $item['contact_hash'], $item['submitted_ip_hash']);
        }
        unset($item);

        $this->view('backend.alumni.index', [
            'title' => 'Kelola Alumni',
            'user' => $this->currentUser(),
            'alumniItems' => $items,
            'flash' => $this->getFlash(),
        ], 'backend');
    }

    public function adminSave(): void
    {
        $this->requireCsrf();
        $id = (int) $this->post('id', 0);
        $existing = $id > 0 ? $this->alumniModel->find($id) : false;
        if ($id > 0 && !$existing) {
            $this->flash('error', 'Data alumni tidak ditemukan.');
            $this->redirect('/admin/alumni');
        }

        $data = $this->validatedData(true);
        if ($data === null) {
            $this->redirect('/admin/alumni');
        }

        $contact = trim((string) $this->post('contact', ''));
        if ($contact !== '') {
            $data['contact_encrypted'] = DataCipher::encrypt($contact);
            $data['contact_hash'] = DataCipher::blindIndex($contact);
        } elseif (!$existing) {
            $this->flash('error', 'Kontak verifikasi wajib diisi.');
            $this->redirect('/admin/alumni');
        }

        $status = (string) $this->post('status', 'approved');
        $data['status'] = in_array($status, self::STATUSES, true) ? $status : 'approved';
        $data['is_featured'] = $this->post('is_featured') ? 1 : 0;
        $data['sort_order'] = max(0, (int) $this->post('sort_order', 0));
        $data['approved_at'] = $data['status'] === 'approved'
            ? (($existing['approved_at'] ?? null) ?: date('Y-m-d H:i:s'))
            : null;

        if ($this->hasPhotoUpload()) {
            $photo = $this->uploadAlumniPhoto();
            if (!$photo) {
                $this->flash('error', $this->uploadErrorMessage('Foto alumni gagal diunggah'));
                $this->redirect('/admin/alumni');
            }
            $data['photo'] = $photo;
            $this->deletePhoto($existing['photo'] ?? null);
        } elseif ($existing && $this->post('remove_photo')) {
            $this->deletePhoto($existing['photo'] ?? null);
            $data['photo'] = null;
        }

        if ($existing) {
            $this->alumniModel->update($id, $data);
            $message = 'Data alumni berhasil diperbarui.';
        } else {
            $this->alumniModel->create($data);
            $message = 'Data alumni berhasil ditambahkan.';
        }
        $this->flash('success', $message);
        $this->redirect('/admin/alumni');
    }

    public function updateStatus(string $id): void
    {
        $this->requireCsrf();
        $item = $this->alumniModel->find((int) $id);
        $status = (string) $this->post('status', '');
        if (!$item || !in_array($status, self::STATUSES, true)) {
            $this->flash('error', 'Data atau status alumni tidak valid.');
            $this->redirect('/admin/alumni');
        }
        $this->alumniModel->update((int) $id, [
            'status' => $status,
            'approved_at' => $status === 'approved'
                ? (($item['approved_at'] ?? null) ?: date('Y-m-d H:i:s'))
                : null,
        ]);
        $this->flash('success', 'Status alumni berhasil diperbarui.');
        $this->redirect('/admin/alumni');
    }

    public function delete(string $id): void
    {
        $this->requireCsrf();
        $item = $this->alumniModel->find((int) $id);
        if ($item) {
            $this->deletePhoto($item['photo'] ?? null);
            $this->alumniModel->delete((int) $id);
        }
        $this->flash('success', 'Data alumni berhasil dihapus.');
        $this->redirect('/admin/alumni');
    }

    private function validatedData(bool $admin): ?array
    {
        $fields = [];
        foreach (['name', 'final_class', 'further_education', 'occupation', 'institution', 'city', 'story', 'achievement'] as $field) {
            $fields[$field] = $this->clean((string) $this->post($field, ''));
        }
        $year = (int) $this->post('graduation_year', 0);
        $contact = trim((string) $this->post('contact', ''));
        $consent = $admin || (bool) $this->post('consent');

        if ($fields['name'] === '' || mb_strlen($fields['name']) > 100
            || $year < 1950 || $year > ((int) date('Y') + 1)
            || mb_strlen($fields['final_class']) > 60
            || mb_strlen($fields['further_education']) > 160
            || mb_strlen($fields['occupation']) > 120
            || mb_strlen($fields['institution']) > 160
            || mb_strlen($fields['city']) > 100
            || mb_strlen($fields['story']) > 1500
            || mb_strlen($fields['achievement']) > 1000
            || mb_strlen($contact) > 160
            || (!$admin && $contact === '')
            || !$consent) {
            $this->flash('error', 'Periksa kembali data wajib, panjang isian, tahun lulus, kontak, dan persetujuan publikasi.');
            return null;
        }

        return $fields + [
            'graduation_year' => $year,
            'consent' => 1,
            'publish_photo' => $this->post('publish_photo') ? 1 : 0,
            'publish_occupation' => $this->post('publish_occupation') ? 1 : 0,
            'publish_city' => $this->post('publish_city') ? 1 : 0,
        ];
    }

    private function clean(string $value): string
    {
        return trim((string) preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', strip_tags($value)));
    }

    private function hasPhotoUpload(): bool
    {
        return isset($_FILES['photo']) && (int) ($_FILES['photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    }

    private function uploadAlumniPhoto(): string|false
    {
        return $this->uploadFile(
            $_FILES['photo'],
            'alumni',
            ['image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/webp'],
            self::MAX_PHOTO_SIZE
        );
    }

    private function deletePhoto(?string $photo): void
    {
        if (!$photo || !str_starts_with(str_replace('\\', '/', $photo), 'alumni/')) {
            return;
        }
        $root = realpath(STORAGE_PATH . '/alumni');
        $path = realpath(STORAGE_PATH . '/' . $photo);
        if ($root === false || $path === false) {
            return;
        }
        if (str_starts_with($path, rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR) && is_file($path)) {
            unlink($path);
        }
    }
}
