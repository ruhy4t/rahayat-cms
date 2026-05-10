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

        $this->view('frontend.spmb.register', $data, 'frontend');
    }

    /**
     * Store registration
     */
    public function store(): void
    {
        try {
            $this->requireCsrf();

            if ($this->isRateLimited('spmb-store', 5, 600)) {
                $this->json(['success' => false, 'message' => 'Terlalu banyak percobaan. Silakan coba lagi beberapa menit lagi.'], 429);
                return;
            }

            if (!$this->checkSPMBAvailable()) {
                $this->json(['success' => false, 'message' => 'Pendaftaran SPMB belum dibuka']);
                return;
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
                if (!empty($_FILES[$type]['name'])) {
                    $uploadPath = $this->uploadFile($_FILES[$type], 'spmb', $documentAllowedTypes, $documentMaxSize);
                    if (!$uploadPath) {
                        $this->json([
                            'success' => false,
                            'message' => $this->uploadErrorMessage('Dokumen ' . str_replace('_', ' ', $type) . ' gagal diunggah')
                        ]);
                        return;
                    }
                    $documents[$type] = $uploadPath;
                }
            }

            if (!empty($documents)) {
                $data['documents'] = json_encode($documents);
            }

            // Validate required fields
            if (
                empty($data['student_name']) || empty($data['nisn']) || empty($data['nik']) ||
                empty($data['birth_date']) || empty($data['gender']) || empty($data['address']) ||
                empty($data['address_village']) || empty($data['address_district']) ||
                empty($data['address_city']) || empty($data['address_province']) ||
                empty($data['previous_school_npsn'])
            ) {
                $this->json(['success' => false, 'message' => 'Mohon lengkapi data yang wajib diisi (termasuk NIK, NISN, Alamat Lengkap, dan NPSN Sekolah Asal)']);
                return;
            }

            // Save registration
            $id = $this->spmbModel->create($data);

            if ($id) {
                $this->json([
                    'success' => true,
                    'message' => 'Pendaftaran berhasil!',
                    'registration_number' => $registrationNumber
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Gagal menyimpan pendaftaran']);
            }
        } catch (\Throwable $e) {
            error_log('SPMB registration failed: ' . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => APP_DEBUG ? 'Terjadi kesalahan server: ' . $e->getMessage() : 'Terjadi kesalahan server. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Check registration status
     */
    public function checkStatus(): void
    {
        $registrationNumber = trim((string) ($this->get('nomor') ?? $this->post('registration_number')));
        $profile = $this->profileModel->getProfile();

        $data = [
            'title' => 'Cek Status Pendaftaran',
            'profile' => $profile,
            'registration' => null,
            'searched' => false,
            'enableContentProtection' => true
        ];

        if ($registrationNumber) {
            if ($this->isRateLimited('spmb-status', 20, 600)) {
                http_response_code(429);
                $this->flash('error', 'Terlalu banyak percobaan cek status. Silakan coba lagi beberapa menit lagi.');
                $this->redirect('/spmb/cek-status');
                return;
            }
            $data['searched'] = true;
            $data['registration'] = $this->spmbModel->findByRegistrationNumber($registrationNumber);
        }

        $this->view('frontend.spmb.status', $data, 'frontend');
    }
}
