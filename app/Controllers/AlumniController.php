<?php

declare(strict_types=1);

class AlumniController extends Controller
{
    private const STATUSES = ['pending', 'approved', 'rejected'];
    private const CONTINUATION_OPTIONS = ['SMA', 'SMK', 'MA', 'Pesantren', 'Paket C', 'Bekerja', 'Tidak Melanjutkan', 'Lainnya'];
    private const EMPLOYMENT_STATUSES = ['Pelajar/Mahasiswa', 'Bekerja', 'Wirausaha', 'Belum Bekerja', 'Tidak Bekerja', 'Lainnya'];
    private const MAX_PHOTO_SIZE = 1 * 1024 * 1024;

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
            'continuationOptions' => self::CONTINUATION_OPTIONS,
            'employmentStatuses' => self::EMPLOYMENT_STATUSES,
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

        [$whatsapp, $email] = $this->contactInputs();
        $contact = $this->encodeContact($whatsapp, $email);
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
            [$item['whatsapp_plain'], $item['email_plain']] = $this->decryptContact($item['contact_encrypted'] ?? null);
            unset($item['contact_encrypted'], $item['contact_hash'], $item['submitted_ip_hash']);
        }
        unset($item);

        $this->view('backend.alumni.index', [
            'title' => 'Kelola Alumni',
            'user' => $this->currentUser(),
            'alumniItems' => $items,
            'statistics' => $this->alumniModel->adminStatistics(),
            'continuationOptions' => self::CONTINUATION_OPTIONS,
            'employmentStatuses' => self::EMPLOYMENT_STATUSES,
            'flash' => $this->getFlash(),
        ], 'backend');
    }

    public function export(): void
    {
        $items = $this->alumniModel->getAllForAdmin();
        foreach ($items as &$item) {
            [$item['whatsapp_plain'], $item['email_plain']] = $this->decryptContact($item['contact_encrypted'] ?? null);
            unset($item['contact_encrypted'], $item['contact_hash'], $item['submitted_ip_hash']);
        }
        unset($item);

        AlumniExcelExporter::download($items);
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

        [$whatsapp, $email] = $this->contactInputs();
        if ($whatsapp !== '' || $email !== '') {
            [$savedWhatsapp, $savedEmail] = $existing
                ? $this->decryptContact($existing['contact_encrypted'] ?? null)
                : ['', ''];
            $whatsapp = $whatsapp !== '' ? $whatsapp : $savedWhatsapp;
            $email = $email !== '' ? $email : $savedEmail;
            if (!$this->validContact($whatsapp, $email)) {
                $this->flash('error', 'Nomor WhatsApp dan alamat email harus diisi dengan format yang valid.');
                $this->redirect('/admin/alumni');
            }
            $contact = $this->encodeContact($whatsapp, $email);
            $data['contact_encrypted'] = DataCipher::encrypt($contact);
            $data['contact_hash'] = DataCipher::blindIndex($contact);
        } elseif (!$existing) {
            $this->flash('error', 'Nomor WhatsApp dan alamat email wajib diisi.');
            $this->redirect('/admin/alumni');
        }

        $status = (string) $this->post('status', 'approved');
        $data['status'] = in_array($status, self::STATUSES, true) ? $status : 'approved';
        $data['is_featured'] = $this->post('is_featured') ? 1 : 0;
        $data['sort_order'] = max(0, (int) $this->post('sort_order', 0));
        $data['approved_at'] = $data['status'] === 'approved'
            ? (($existing['approved_at'] ?? null) ?: date('Y-m-d H:i:s'))
            : null;

        $croppedPhoto = trim((string) $this->post('cropped_photo', ''));
        if ($croppedPhoto !== '') {
            $photo = $this->saveCroppedPortrait($croppedPhoto, 'alumni', self::MAX_PHOTO_SIZE);
            if (!$photo) {
                $this->flash('error', $this->uploadErrorMessage('Hasil pengaturan foto alumni gagal disimpan'));
                $this->redirect('/admin/alumni');
            }
            $data['photo'] = $photo;
            $this->deletePhoto($existing['photo'] ?? null);
        } elseif ($this->hasPhotoUpload()) {
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
        foreach (['name', 'final_class', 'occupation', 'institution', 'city', 'story', 'achievement'] as $field) {
            $fields[$field] = $this->clean((string) $this->post($field, ''));
        }
        $continuationType = $this->clean((string) $this->post('further_education', ''));
        $continuationStatus = $this->clean((string) $this->post('further_education_status', ''));
        $continuationDetail = $this->clean((string) $this->post('further_education_detail', ''));
        $employmentStatus = $this->clean((string) $this->post('employment_status', ''));
        $requiresSchoolDetails = !in_array($continuationType, ['Bekerja', 'Tidak Melanjutkan'], true);
        if (!$requiresSchoolDetails) {
            $continuationStatus = '';
            $continuationDetail = '';
        }
        $fields['further_education'] = $continuationType;
        if ($continuationStatus !== '') {
            $fields['further_education'] .= ' — ' . $continuationStatus;
        }
        if ($continuationDetail !== '') {
            $fields['further_education'] .= ' — ' . $continuationDetail;
        }
        $fields['continuation_type'] = $continuationType;
        $fields['continuation_status'] = $continuationStatus;
        $fields['continuation_institution'] = $continuationDetail;
        $fields['employment_status'] = $employmentStatus;
        $year = (int) $this->post('graduation_year', 0);
        [$whatsapp, $email] = $this->contactInputs();
        $consent = $admin || (bool) $this->post('consent');

        if ($fields['name'] === '' || mb_strlen($fields['name']) > 100
            || $year < 1950 || $year > ((int) date('Y') + 1)
            || mb_strlen($fields['final_class']) > 60
            || $continuationType === ''
            || (!$admin && !in_array($continuationType, self::CONTINUATION_OPTIONS, true))
            || mb_strlen($fields['further_education']) > 160
            || mb_strlen($continuationDetail) > 120
            || ($continuationStatus !== '' && !in_array($continuationStatus, ['Negeri', 'Swasta'], true))
            || ($requiresSchoolDetails && ($continuationStatus === '' || $continuationDetail === ''))
            || ($employmentStatus !== '' && !in_array($employmentStatus, self::EMPLOYMENT_STATUSES, true))
            || mb_strlen($fields['occupation']) > 120
            || mb_strlen($fields['institution']) > 160
            || mb_strlen($fields['city']) > 100
            || mb_strlen($fields['story']) > 1500
            || mb_strlen($fields['achievement']) > 1000
            || (!$admin && !$this->validContact($whatsapp, $email))
            || !$consent) {
            $this->flash('error', 'Periksa kembali nama, tahun lulus, tujuan setelah lulus, panjang isian, kontak, dan persetujuan publikasi.');
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

    private function contactInputs(): array
    {
        return [
            $this->clean((string) $this->post('whatsapp', '')),
            mb_strtolower($this->clean((string) $this->post('email', ''))),
        ];
    }

    private function validContact(string $whatsapp, string $email): bool
    {
        $digits = preg_replace('/\D+/', '', $whatsapp) ?? '';
        return mb_strlen($whatsapp) <= 30
            && strlen($digits) >= 8
            && strlen($digits) <= 20
            && mb_strlen($email) <= 160
            && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function encodeContact(string $whatsapp, string $email): string
    {
        return (string) json_encode(
            ['whatsapp' => $whatsapp, 'email' => $email],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }

    private function decryptContact(?string $encrypted): array
    {
        $plain = trim((string) DataCipher::decrypt($encrypted));
        $decoded = json_decode($plain, true);
        if (is_array($decoded)) {
            return [
                trim((string) ($decoded['whatsapp'] ?? '')),
                trim((string) ($decoded['email'] ?? '')),
            ];
        }

        // Data lama hanya memiliki satu kolom kontak.
        return filter_var($plain, FILTER_VALIDATE_EMAIL) !== false
            ? ['', $plain]
            : [$plain, ''];
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
