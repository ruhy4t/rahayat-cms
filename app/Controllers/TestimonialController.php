<?php

declare(strict_types=1);

class TestimonialController extends Controller
{
    private const MAX_PHOTO_SIZE = 1 * 1024 * 1024;
    private const RELATIONSHIPS = ['Orang Tua/Wali', 'Alumni', 'Siswa', 'Mitra Sekolah', 'Tokoh Masyarakat', 'Lainnya'];
    private const STATUSES = ['pending', 'approved', 'rejected'];

    private Testimonial $testimonialModel;

    public function __construct()
    {
        $this->testimonialModel = new Testimonial();
    }

    public function before(string $action): bool
    {
        if (in_array($action, ['publicIndex', 'store'], true)) {
            return true;
        }

        $this->requireAuth();
        if (($this->currentUser()['role'] ?? '') !== 'admin') {
            $this->flash('error', 'Pengelolaan testimoni hanya dapat diakses administrator.');
            $this->redirect('/admin');
            return false;
        }

        return true;
    }

    public function publicIndex(): void
    {
        $this->view('frontend.testimonials', [
            'title' => 'Testimoni',
            'testimonials' => $this->testimonialModel->getApproved(),
            'relationships' => self::RELATIONSHIPS,
            'flash' => $this->getFlash(),
        ], 'frontend');
    }

    public function store(): void
    {
        if (!Security::isPost()) {
            $this->redirect('/testimoni');
        }
        if (!$this->validateCsrf()) {
            $this->flash('error', 'Sesi formulir telah berakhir. Muat ulang halaman lalu coba kembali.');
            $this->redirect('/testimoni#kirim-testimoni');
        }

        // Honeypot: bots commonly fill this visually hidden field.
        if (trim((string) $this->post('website', '')) !== '') {
            $this->flash('success', 'Terima kasih. Testimoni Anda telah dikirim untuk ditinjau.');
            $this->redirect('/testimoni');
        }
        if (!Security::validatePublicCaptcha(
            'testimonial',
            (string) $this->post('captcha_token', ''),
            $this->post('captcha_answer', '')
        )) {
            $this->flash('error', 'Jawaban verifikasi keamanan tidak tepat. Silakan coba kembali.');
            $this->redirect('/testimoni#kirim-testimoni');
        }

        $ip = trim((string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        $ipHash = (string) DataCipher::blindIndex('ip:' . $ip);
        $lastSubmit = (int) ($_SESSION['_testimonial_last_submit'] ?? 0);
        if ((time() - $lastSubmit) < 60 || $this->testimonialModel->countRecentByIpHash($ipHash) >= 3) {
            $this->flash('error', 'Terlalu banyak pengiriman. Silakan coba kembali beberapa saat lagi.');
            $this->redirect('/testimoni');
        }

        $data = $this->validatedData(false);
        if ($data === null) {
            $this->redirect('/testimoni#kirim-testimoni');
        }
        $data['status'] = 'pending';
        $data['is_featured'] = 0;
        $data['sort_order'] = 0;
        $data['submitted_ip_hash'] = $ipHash;

        if ($this->hasPhotoUpload()) {
            $photo = $this->uploadTestimonialPhoto();
            if (!$photo) {
                $this->flash('error', $this->uploadErrorMessage('Foto gagal diunggah'));
                $this->redirect('/testimoni#kirim-testimoni');
            }
            $data['photo'] = $photo;
        }

        Security::consumePublicCaptcha('testimonial', (string) $this->post('captcha_token', ''));
        $this->testimonialModel->create($data);
        $_SESSION['_testimonial_last_submit'] = time();
        $this->flash('success', 'Terima kasih. Testimoni Anda telah dikirim dan akan tampil setelah disetujui admin.');
        $this->redirect('/testimoni');
    }

    public function adminIndex(): void
    {
        $this->view('backend.testimonials.index', [
            'title' => 'Kelola Testimoni',
            'user' => $this->currentUser(),
            'testimonials' => $this->testimonialModel->getAllForAdmin(),
            'relationships' => self::RELATIONSHIPS,
            'flash' => $this->getFlash(),
        ], 'backend');
    }

    public function adminSave(): void
    {
        $this->requireCsrf();
        $id = (int) $this->post('id', 0);
        $existing = $id > 0 ? $this->testimonialModel->find($id) : false;
        if ($id > 0 && !$existing) {
            $this->flash('error', 'Testimoni tidak ditemukan.');
            $this->redirect('/admin/testimoni');
        }

        $data = $this->validatedData(true);
        if ($data === null) {
            $this->redirect('/admin/testimoni');
        }

        $status = (string) $this->post('status', 'approved');
        $data['status'] = in_array($status, self::STATUSES, true) ? $status : 'approved';
        $data['is_featured'] = $this->post('is_featured') ? 1 : 0;
        $data['sort_order'] = max(0, (int) $this->post('sort_order', 0));
        $data['approved_at'] = $data['status'] === 'approved'
            ? (($existing['approved_at'] ?? null) ?: date('Y-m-d H:i:s'))
            : null;
        $data['submitted_ip_hash'] = $existing['submitted_ip_hash'] ?? null;

        if ($this->hasPhotoUpload()) {
            $photo = $this->uploadTestimonialPhoto();
            if (!$photo) {
                $this->flash('error', $this->uploadErrorMessage('Foto gagal diunggah'));
                $this->redirect('/admin/testimoni');
            }
            $data['photo'] = $photo;
            $this->deletePhoto($existing['photo'] ?? null);
        } elseif ($existing && $this->post('remove_photo')) {
            $this->deletePhoto($existing['photo'] ?? null);
            $data['photo'] = null;
        }

        if ($existing) {
            $this->testimonialModel->update($id, $data);
            $message = 'Testimoni berhasil diperbarui.';
        } else {
            $this->testimonialModel->create($data);
            $message = 'Testimoni berhasil ditambahkan.';
        }

        $this->flash('success', $message);
        $this->redirect('/admin/testimoni');
    }

    public function updateStatus(string $id): void
    {
        $this->requireCsrf();
        $testimonial = $this->testimonialModel->find((int) $id);
        $status = (string) $this->post('status', '');
        if (!$testimonial || !in_array($status, self::STATUSES, true)) {
            $this->flash('error', 'Data atau status testimoni tidak valid.');
            $this->redirect('/admin/testimoni');
        }

        $this->testimonialModel->update((int) $id, [
            'status' => $status,
            'approved_at' => $status === 'approved'
                ? (($testimonial['approved_at'] ?? null) ?: date('Y-m-d H:i:s'))
                : null,
        ]);
        $this->flash('success', 'Status testimoni berhasil diperbarui.');
        $this->redirect('/admin/testimoni');
    }

    public function delete(string $id): void
    {
        $this->requireCsrf();
        $testimonial = $this->testimonialModel->find((int) $id);
        if ($testimonial) {
            $this->deletePhoto($testimonial['photo'] ?? null);
            $this->testimonialModel->delete((int) $id);
        }
        $this->flash('success', 'Testimoni berhasil dihapus.');
        $this->redirect('/admin/testimoni');
    }

    private function validatedData(bool $isAdmin): ?array
    {
        $name = trim((string) $this->post('name', ''));
        $relationship = trim((string) $this->post('relationship', ''));
        $graduationYear = trim((string) $this->post('graduation_year', ''));
        $occupation = trim((string) $this->post('occupation', ''));
        $testimonial = trim((string) $this->post('testimonial', ''));
        $contact = trim((string) $this->post('contact', ''));
        $consent = $isAdmin || (bool) $this->post('consent');
        $cleanName = $this->cleanPlainText($name);
        $cleanOccupation = $this->cleanPlainText($occupation);
        $cleanTestimonial = $this->cleanPlainText($testimonial);
        $cleanContact = $this->cleanPlainText($contact);

        if ($cleanName === '' || mb_strlen($cleanName) > 100
            || !in_array($relationship, self::RELATIONSHIPS, true)
            || mb_strlen($cleanTestimonial) < 20 || mb_strlen($cleanTestimonial) > 1200
            || mb_strlen($cleanOccupation) > 120 || mb_strlen($cleanContact) > 120
            || !$consent) {
            $this->flash('error', 'Periksa kembali isian. Nama, kategori, testimoni minimal 20 karakter, dan persetujuan publikasi wajib diisi.');
            return null;
        }

        if ($graduationYear !== '' && (!preg_match('/^\d{4}$/', $graduationYear)
            || (int) $graduationYear < 1950 || (int) $graduationYear > ((int) date('Y') + 1))) {
            $this->flash('error', 'Tahun lulus tidak valid.');
            return null;
        }

        return [
            'name' => $cleanName,
            'relationship' => $relationship,
            'graduation_year' => $graduationYear !== '' ? (int) $graduationYear : null,
            'occupation' => $cleanOccupation,
            'testimonial' => $cleanTestimonial,
            'contact' => $cleanContact,
            'consent' => 1,
        ];
    }

    private function cleanPlainText(string $value): string
    {
        $value = strip_tags($value);
        return trim((string) preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value));
    }

    private function hasPhotoUpload(): bool
    {
        return isset($_FILES['photo']) && (int) ($_FILES['photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    }

    private function uploadTestimonialPhoto(): string|false
    {
        return $this->uploadFile(
            $_FILES['photo'],
            'testimonials',
            ['image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/webp'],
            self::MAX_PHOTO_SIZE
        );
    }

    private function deletePhoto(?string $photo): void
    {
        if (!$photo || !str_starts_with(str_replace('\\', '/', $photo), 'testimonials/')) {
            return;
        }

        $testimonialRoot = realpath(STORAGE_PATH . '/testimonials');
        $path = realpath(STORAGE_PATH . '/' . $photo);
        if ($testimonialRoot === false || $path === false) {
            return;
        }

        $prefix = rtrim($testimonialRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (str_starts_with($path, $prefix) && is_file($path)) {
            unlink($path);
        }
    }
}
