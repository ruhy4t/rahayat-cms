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
        if ($action === 'show') {
            return true;
        }

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

    /**
     * Display a single active extracurricular item on the public website.
     */
    public function show(string $id): void
    {
        $item = $this->ekskulModel->find((int) $id);

        if (!$item || empty($item['is_active'])) {
            http_response_code(404);
            $this->view('errors.404', ['title' => 'Ekstrakurikuler Tidak Ditemukan'], 'frontend');
            return;
        }

        $this->view('frontend.ekstrakurikuler-detail', [
            'title' => $item['name'],
            'ekskulItem' => $item,
        ], 'frontend');
    }

    public function store(): void
    {
        $this->requireCsrf();

        $data = $this->validatedData();

        if (!empty($_FILES['image']['name'])) {
            $imagePath = $this->uploadFile($_FILES['image'], 'ekstrakurikuler');
            if (!$imagePath) {
                $this->flash('error', $this->uploadErrorMessage('Gambar ekstrakurikuler gagal diunggah'));
                $this->redirect('/admin/ekstrakurikuler');
            }
            $data['image'] = $imagePath;
        }

        $this->ekskulModel->create($data);
        $this->flash('success', 'Ekstrakurikuler berhasil ditambahkan');
        $this->redirect('/admin/ekstrakurikuler');
    }

    public function update(string $id): void
    {
        $this->requireCsrf();
        $id = (int) $id;

        $data = $this->validatedData();

        if (!empty($_FILES['image']['name'])) {
            $imagePath = $this->uploadFile($_FILES['image'], 'ekstrakurikuler');
            if (!$imagePath) {
                $this->flash('error', $this->uploadErrorMessage('Gambar ekstrakurikuler gagal diunggah'));
                $this->redirect('/admin/ekstrakurikuler');
            }
            $data['image'] = $imagePath;
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

    private function validatedData(): array
    {
        $supervisors = $this->supervisorsInput();
        $schedules = $this->schedulesInput();
        $achievements = $this->achievementsInput();

        return [
            'name' => mb_substr($this->postSafe('name'), 0, 100),
            'description' => mb_substr($this->postSafe('description'), 0, 5000),
            // Keep the first entry in legacy columns for installations that have
            // not completed the schema upgrade yet.
            'supervisor' => mb_substr((string) ($supervisors[0]['name'] ?? ''), 0, 100),
            'schedule' => mb_substr($this->scheduleLabel($schedules[0] ?? []), 0, 100),
            'supervisors_json' => $this->encodeList($supervisors),
            'schedules_json' => $this->encodeList($schedules),
            'achievements_json' => $this->encodeList($achievements),
            'sort_order' => max(0, (int) $this->post('sort_order', 0)),
            'is_active' => $this->post('is_active') ? 1 : 0,
        ];
    }

    private function supervisorsInput(): array
    {
        $names = $this->arrayInput('supervisor_names', 20);
        $roles = $this->arrayInput('supervisor_roles', 20);
        $items = [];

        foreach ($names as $index => $name) {
            $name = $this->cleanValue($name, 100);
            if ($name === '') {
                continue;
            }
            $role = $this->cleanValue($roles[$index] ?? 'Pembina', 20);
            $items[] = ['name' => $name, 'role' => in_array($role, ['Pembina', 'Pelatih'], true) ? $role : 'Pembina'];
        }

        return $items;
    }

    private function schedulesInput(): array
    {
        $days = $this->arrayInput('schedule_days', 20);
        $times = $this->arrayInput('schedule_times', 20);
        $notes = $this->arrayInput('schedule_notes', 20);
        $allowedDays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $items = [];

        foreach ($days as $index => $day) {
            $day = $this->cleanValue($day, 10);
            $time = $this->cleanValue($times[$index] ?? '', 60);
            $note = $this->cleanValue($notes[$index] ?? '', 120);
            if (!in_array($day, $allowedDays, true) || ($time === '' && $note === '')) {
                continue;
            }
            $items[] = ['day' => $day, 'time' => $time, 'note' => $note];
        }

        return $items;
    }

    private function achievementsInput(): array
    {
        $titles = $this->arrayInput('achievement_titles', 30);
        $years = $this->arrayInput('achievement_years', 30);
        $items = [];

        foreach ($titles as $index => $title) {
            $title = $this->cleanValue($title, 180);
            if ($title === '') {
                continue;
            }
            $year = $this->cleanValue($years[$index] ?? '', 4);
            $items[] = [
                'title' => $title,
                'year' => preg_match('/^(19|20)\d{2}$/', $year) ? $year : '',
            ];
        }

        return $items;
    }

    private function arrayInput(string $key, int $limit): array
    {
        $value = $this->post($key, []);
        return is_array($value) ? array_slice($value, 0, $limit) : [];
    }

    private function cleanValue(mixed $value, int $length): string
    {
        return mb_substr(trim(Security::sanitize(is_scalar($value) ? (string) $value : '')), 0, $length);
    }

    private function scheduleLabel(array $schedule): string
    {
        return trim(implode(' ', array_filter([
            (string) ($schedule['day'] ?? ''),
            (string) ($schedule['time'] ?? ''),
            (string) ($schedule['note'] ?? ''),
        ])));
    }

    private function encodeList(array $items): string
    {
        return json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]';
    }
}
