<?php
/**
 * ============================================
 * SPMB Controller - Public Registration
 * Sistem Penerimaan Murid Baru
 * ============================================
 */

declare(strict_types=1);

class SPMBController extends Controller
{
    private SPMBRegistration $spmbModel;
    private SchoolProfile $profileModel;
    private SiteSetting $settingModel;

    public function __construct()
    {
        $this->spmbModel = new SPMBRegistration();
        $this->profileModel = new SchoolProfile();
        $this->settingModel = new SiteSetting();
    }

    /**
     * Check if SPMB is available
     */
    private function checkSPMBAvailable(): bool
    {
        $profile = $this->profileModel->getProfile();

        // Only available for private schools
        if (!$profile || $profile['school_type'] !== 'swasta') {
            return false;
        }

        // Check if SPMB is enabled and inside the configured public period.
        if (!$this->settingModel->isSPMBPeriodActive()) {
            return false;
        }

        return true;
    }

    /**
     * SPMB landing page
     */
    public function index(): void
    {
        if (!$this->checkSPMBAvailable()) {
            $this->flash('error', 'Pendaftaran SPMB belum dibuka');
            $this->redirect('/');
            return;
        }

        $profile = $this->profileModel->getProfile();
        $startDate = $this->settingModel->get('spmb_start_date');
        $endDate = $this->settingModel->get('spmb_end_date');

        $quota = (int) $this->settingModel->get('spmb_quota', '0');
        $totalRegistered = $this->spmbModel->getStats()['total'] ?? 0;

        $data = [
            'title' => 'SPMB - ' . ($profile['name'] ?? SCHOOL_NAME),
            'profile' => $profile,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'quota' => $quota,
            'totalRegistered' => $totalRegistered,
            'flash' => $this->getFlash(),
            'enableContentProtection' => true
        ];

        $this->view('frontend.spmb.index', $data, 'frontend');
    }

    /**
     * Registration form
     */
    public function register(): void
    {
        if (!$this->checkSPMBAvailable()) {
            $this->flash('error', 'Pendaftaran SPMB belum dibuka');
            $this->redirect('/');
            return;
        }

        $profile = $this->profileModel->getProfile();

        $settings = $this->settingModel->getAll();
        $savedDocumentsRaw = $settings['spmb_documents'] ?? '[]';
        $selectedDocuments = json_decode($savedDocumentsRaw, true);
        if (!is_array($selectedDocuments)) {
            $selectedDocuments = [];
        }

        $quota = (int) ($settings['spmb_quota'] ?? '0');
        $totalRegistered = $this->spmbModel->getStats()['total'] ?? 0;

        $data = [
            'title' => 'Formulir Pendaftaran SPMB',
            'profile' => $profile,
            'selectedDocuments' => $selectedDocuments,
            'quota' => $quota,
            'totalRegistered' => $totalRegistered,
            'flash' => $this->getFlash(),
            'enableContentProtection' => true
        ];

        $submissionToken = bin2hex(random_bytes(24));
        $_SESSION['_spmb_submissions'][$submissionToken] = null;
        $_SESSION['_spmb_submissions'] = array_slice($_SESSION['_spmb_submissions'], -10, null, true);
        $data['submissionToken'] = $submissionToken;
        $this->view('frontend.spmb.register', $data, 'frontend');
    }

    /**
     * Store registration
     */
    public function store(): void
    {
        $documents = [];
        try {
            if (!Security::isPost()) { $this->json(['success' => false, 'message' => 'Metode tidak diizinkan.'], 405); }
            $this->requireCsrf();
            $submissionToken = (string) $this->post('submission_token', '');
            if (!array_key_exists($submissionToken, $_SESSION['_spmb_submissions'] ?? [])) {
                $this->json(['success' => false, 'message' => 'Formulir kedaluwarsa. Muat ulang halaman pendaftaran.'], 422);
            }
            if (is_string($_SESSION['_spmb_submissions'][$submissionToken])) {
                $this->json(['success' => true, 'registration_number' => $_SESSION['_spmb_submissions'][$submissionToken]]);
            }

            if ($this->isRateLimited('spmb-store', 5, 600)) {
                $this->json(['success' => false, 'message' => 'Terlalu banyak percobaan. Silakan coba lagi beberapa menit lagi.'], 429);
                return;
            }

            if (!$this->checkSPMBAvailable()) {
                $this->json(['success' => false, 'message' => 'Pendaftaran SPMB belum dibuka']);
                return;
            }

            if (!Security::validatePublicCaptcha(
                'spmb',
                (string) $this->post('captcha_token', ''),
                $this->post('captcha_answer', '')
            )) {
                $this->json([
                    'success' => false,
                    'message' => 'Jawaban verifikasi keamanan tidak tepat. Silakan periksa dan coba kembali.'
                ], 422);
                return;
            }

            foreach ($_POST as $field => $value) {
                if (!is_scalar($value)) {
                    $this->json(['success' => false, 'message' => 'Isian formulir tidak valid.'], 422);
                }
            }

            // Generate registration number
            $registrationNumber = $this->spmbModel->generateRegistrationNumber();

            // Collect form data
            $data = [
                'registration_number' => $registrationNumber,
                'student_name' => $this->postSafe('student_name'),
                'nisn' => $this->postSafe('nisn'),
                'nik' => $this->postSafe('nik'),
                'birth_date' => $this->post('birth_date'),
                'birth_place' => $this->postSafe('birth_place'),
                'gender' => $this->post('gender'),
                'religion' => $this->postSafe('religion'),
                'address' => $this->postSafe('address'),
                'address_village' => $this->postSafe('address_village'),
                'address_district' => $this->postSafe('address_district'),
                'address_city' => $this->postSafe('address_city'),
                'address_province' => $this->postSafe('address_province'),
                'father_name' => $this->postSafe('father_name'),
                'father_occupation' => $this->postSafe('father_occupation'),
                'father_phone' => $this->postSafe('father_phone'),
                'mother_name' => $this->postSafe('mother_name'),
                'mother_occupation' => $this->postSafe('mother_occupation'),
                'mother_phone' => $this->postSafe('mother_phone'),
                'email' => $this->postSafe('email'),
                'phone' => $this->postSafe('phone'),
                'previous_school' => $this->postSafe('previous_school'),
                'previous_school_npsn' => $this->postSafe('previous_school_npsn'),
                'previous_school_address' => $this->postSafe('previous_school_address'),
                'graduation_year' => $this->post('graduation_year'),
                'status' => 'pending'
            ];

            $errors = SPMBValidation::errors($data);
            if ($this->post('agreement') !== 'on') { $errors['agreement'] = 'Persetujuan penggunaan data wajib diberikan.'; }
            if ($errors) { $this->json(['success' => false, 'message' => 'Periksa kembali isian formulir.', 'errors' => $errors], 422); }
            $data['graduation_year'] = $data['graduation_year'] === '' ? null : $data['graduation_year'];
            $data['privacy_accepted_at'] = date('Y-m-d H:i:s');

            // Get dynamic documents settings
            $settings = $this->settingModel->getAll();
            $savedDocumentsRaw = $settings['spmb_documents'] ?? '[]';
            $documentTypes = json_decode($savedDocumentsRaw, true);
            if (!is_array($documentTypes)) {
                $documentTypes = [];
            }

            // Handle document uploads
            $documents = [];
            $documentAllowedTypes = array_merge(UPLOAD_ALLOWED_TYPES, ['application/pdf']);
            $documentMaxSize = 2 * 1024 * 1024;

            foreach ($documentTypes as $type) {
                if (!is_string($type) || !preg_match('/^[a-z0-9_]+$/D', $type)) { continue; }
                if (!empty($_FILES[$type]['name'])) {
                    $uploadPath = $this->uploadFile($_FILES[$type], 'spmb', $documentAllowedTypes, $documentMaxSize);
                    if (!$uploadPath) {
                        $this->cleanupDocuments($documents);
                        $this->json([
                            'success' => false,
                            'message' => $this->uploadErrorMessage('Dokumen ' . str_replace('_', ' ', $type) . ' gagal diunggah')
                        ]);
                        return;
                    }
                    $documents[$type] = $uploadPath;
                    PrivateDocument::encryptFile(STORAGE_PATH . '/' . $uploadPath);
                }
            }

            if (!empty($documents)) {
                $data['documents'] = json_encode($documents);
            }

            // Save registration
            $id = $this->spmbModel->create($data);

            if ($id) {
                $_SESSION['_spmb_submissions'][$submissionToken] = $registrationNumber;
                Security::consumePublicCaptcha('spmb', (string) $this->post('captcha_token', ''));
                $this->json([
                    'success' => true,
                    'message' => 'Pendaftaran berhasil!',
                    'registration_number' => $registrationNumber
                ]);
            } else {
                $this->cleanupDocuments($documents);
                $this->json(['success' => false, 'message' => 'Gagal menyimpan pendaftaran']);
            }
        } catch (\Throwable $e) {
            $this->cleanupDocuments($documents);
            error_log('SPMB registration failed: ' . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Check registration status
     */
    private function cleanupDocuments(array $documents): void
    {
        $root = realpath(STORAGE_PATH . '/spmb');
        foreach ($documents as $path) {
            $real = realpath(STORAGE_PATH . '/' . $path);
            if ($root && $real && str_starts_with($real, $root . DIRECTORY_SEPARATOR) && is_file($real)) { unlink($real); }
        }
    }

    public function checkStatus(): void
    {
        header('Cache-Control: private, no-store');
        header('Referrer-Policy: no-referrer');
        $number = Security::isPost() ? strtoupper(trim((string) $this->post('registration_number', ''))) : '';
        $data = ['title' => 'Cek Status Pendaftaran', 'profile' => $this->profileModel->getProfile(),
            'registration' => null, 'searched' => false, 'enableContentProtection' => true,
            'registrationNumber' => $number, 'statusError' => null];
        if (Security::isPost()) {
            $this->requireCsrf();
            if ($this->isRateLimited('spmb-status', 10, 600)) {
                http_response_code(429);
                $data['statusError'] = 'Terlalu banyak percobaan. Coba lagi dalam 10 menit.';
            } else {
                $data['searched'] = true;
                $registration = strlen($number) <= 50 ? $this->spmbModel->findByRegistrationNumber($number) : false;
                if ($registration
                    && hash_equals((string) $registration['nisn'], trim((string) $this->post('nisn', '')))
                    && hash_equals((string) $registration['birth_date'], (string) $this->post('birth_date', ''))) {
                    $data['registration'] = array_intersect_key($registration, array_flip([
                        'registration_number', 'student_name', 'status', 'created_at'
                    ]));
                }
            }
        }
        $this->view('frontend.spmb.status', $data, 'frontend');
    }
}
