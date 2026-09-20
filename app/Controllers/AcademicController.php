<?php
declare(strict_types=1);

class AcademicController extends Controller
{
    public function before(string $action): bool
    {
        if (in_array($action, ['agenda', 'calendar', 'show', 'pdf'], true)) { return true; }
        $this->requireAuth();
        if (($this->currentUser()['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo 'Anda tidak memiliki akses ke halaman ini.';
            return false;
        }
        return true;
    }

    private function queryText(string $key, string $default = ''): string
    {
        return isset($_GET[$key]) && is_string($_GET[$key]) ? $_GET[$key] : $default;
    }

    private function pageData(string $title): array
    {
        return ['title' => $title, 'profile' => (new SchoolProfile())->getProfile() ?: [], 'settings' => (new SiteSetting())->getAll()];
    }

    public function agenda(): void
    {
        $archive = $this->queryText('arsip') === '1';
        $page = min(10000, max(1, (int) $this->queryText('page', '1')));
        $this->view('frontend.academic.agenda', $this->pageData('Agenda Kegiatan') + [
            'events' => (new SchoolEvent())->publicAgenda($archive, $page), 'archive' => $archive, 'page' => $page,
        ], 'frontend');
    }

    public function calendar(): void
    {
        $year = $this->queryText('tahun');
        $this->view('frontend.academic.calendar', $this->pageData('Kalender Pendidikan') + [
            'calendarData' => (new SchoolEvent())->calendarData($year === '' ? null : (int) $year, $this->queryText('bulan') ?: null),
        ], 'frontend');
    }

    public function show(string $id): void
    {
        $event = (new SchoolEvent())->publishedEvent((int) $id);
        if (!$event) { http_response_code(404); echo 'Kegiatan tidak ditemukan.'; return; }
        $this->view('frontend.academic.detail', $this->pageData($event['title']) + ['event' => $event], 'frontend');
    }

    public function pdf(string $id): void
    {
        $year = (new SchoolEvent())->year((int) $id);
        $path = (string) ($year['pdf_path'] ?? '');
        if (!$year || !$year['is_published'] || !preg_match('~^academic_documents/[a-zA-Z0-9]+\.pdf$~D', $path)) {
            http_response_code(404); return;
        }
        $file = realpath(STORAGE_PATH . '/' . $path);
        $directory = realpath(STORAGE_PATH . '/academic_documents');
        if (!$file || !$directory || !str_starts_with($file, $directory . DIRECTORY_SEPARATOR) || !is_file($file)) { http_response_code(404); return; }
        header('Content-Type: application/pdf');
        header('X-Content-Type-Options: nosniff');
        header('Content-Disposition: attachment; filename="kalender-pendidikan-' . (int) $year['start_year'] . '-' . ((int) $year['start_year'] + 1) . '.pdf"');
        header('Content-Length: ' . filesize($file));
        readfile($file);
    }

    public function agendaAdmin(): void { $this->adminPage('agenda'); }
    public function calendarAdmin(): void { $this->adminPage('calendar'); }

    private function adminPage(string $kind): void
    {
        $model = new SchoolEvent();
        $years = $model->years(false);
        $defaultYear = $years[0]['id'] ?? 0;
        foreach ($years as $year) { if ((int) $year['start_year'] === AcademicCalendar::currentYear()) { $defaultYear = $year['id']; } }
        $yearId = (int) $this->queryText('year', (string) $defaultYear);
        $page = min(10000, max(1, (int) $this->queryText('page', '1')));
        $edit = $model->find((int) $this->queryText('edit', '0'));
        if ($edit && $edit['kind'] !== $kind) { $edit = false; }
        $old = $_SESSION['academic_form'] ?? null;
        unset($_SESSION['academic_form']);
        if (is_array($old) && ($old['kind'] ?? '') === $kind) { $edit = $old; }
        $this->view('backend.academic.index', [
            'title' => $kind === 'agenda' ? 'Agenda Kegiatan' : 'Kalender Pendidikan', 'kind' => $kind,
            'years' => $years, 'selectedYearId' => $yearId, 'page' => $page,
            'events' => $model->adminEvents($kind, $yearId, $page), 'edit' => $edit ?: [],
            'user' => $this->currentUser(), 'flash' => $this->getFlash(),
        ], 'backend');
    }

    private function requirePost(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { http_response_code(405); header('Allow: POST'); exit; }
        $this->requireCsrf();
    }

    public function save(): void
    {
        $this->requirePost();
        $kind = $this->post('kind') === 'calendar' ? 'calendar' : 'agenda';
        $url = $kind === 'agenda' ? '/admin/agenda' : '/admin/kalender-pendidikan';
        try {
            $model = new SchoolEvent();
            $yearId = filter_var($this->post('academic_year_id'), FILTER_VALIDATE_INT);
            $year = $model->year($yearId ?: 0);
            if (!$year) { throw new InvalidArgumentException('Buat dan pilih tahun pelajaran terlebih dahulu.'); }
            $data = AcademicCalendar::validateEvent($_POST, (int) $year['start_year']);
            $data['academic_year_id'] = (int) $year['id'];
            $id = filter_var($this->post('id', 0), FILTER_VALIDATE_INT);
            if ($id === false || $id < 0) { throw new InvalidArgumentException('ID kegiatan tidak valid.'); }
            if ($id) {
                $existing = $model->find($id);
                if (!$existing || $existing['kind'] !== $kind) { throw new InvalidArgumentException('Kegiatan tidak ditemukan.'); }
                $model->update($id, $data);
            } else { $model->create($data); }
            unset($_SESSION['academic_form']);
            $this->flash('success', 'Kegiatan berhasil disimpan.' . (!$year['is_published'] ? ' Tahun pelajaran masih draft, sehingga kegiatan belum tampil di publik.' : ''));
            $url .= '?year=' . (int) $year['id'];
        } catch (InvalidArgumentException $e) {
            $_SESSION['academic_form'] = array_filter(array_intersect_key($_POST, array_flip(['id', 'academic_year_id', 'title', 'category', 'start_date', 'end_date', 'event_time', 'location', 'description', 'kind', 'status', 'show_in_calendar'])), 'is_scalar');
            $this->flash('error', $e->getMessage());
        } catch (Throwable $e) {
            error_log('Academic event save failed: ' . $e->getMessage());
            $this->flash('error', 'Kegiatan belum dapat disimpan. Periksa migrasi database lalu coba lagi.');
        }
        $this->redirect($url);
    }

    public function delete(string $id): void
    {
        $this->requirePost();
        $model = new SchoolEvent();
        $event = $model->find((int) $id);
        if ($event) { $model->delete((int) $id); }
        $this->flash('success', 'Kegiatan berhasil dihapus.');
        $this->redirect(($event['kind'] ?? '') === 'calendar' ? '/admin/kalender-pendidikan' : '/admin/agenda');
    }

    public function saveYear(): void
    {
        $this->requirePost();
        $uploaded = null;
        try {
            $start = filter_var($this->post('start_year'), FILTER_VALIDATE_INT);
            if ($start === false || $start < 1900 || $start > 2200) { throw new InvalidArgumentException('Tahun awal harus antara 1900 dan 2200.'); }
            $replace = $this->post('remove_pdf') === '1';
            if (isset($_FILES['pdf']) && ($_FILES['pdf']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $uploaded = $this->uploadFile($_FILES['pdf'], 'academic_documents', ['application/pdf'], 10 * 1024 * 1024);
                if (!$uploaded) { throw new InvalidArgumentException($this->uploadErrorMessage('PDF gagal diunggah')); }
                $replace = true;
            }
            (new SchoolEvent())->saveYear($start, $this->post('is_published') === '1', $uploaded ?: null, $replace);
            $this->flash('success', 'Tahun pelajaran ' . AcademicCalendar::label($start) . ' berhasil disimpan. Data tahun lain tetap tersimpan.');
        } catch (Throwable $e) {
            if ($uploaded && is_file(STORAGE_PATH . '/' . $uploaded)) { unlink(STORAGE_PATH . '/' . $uploaded); }
            error_log('Academic year save failed: ' . $e->getMessage());
            $this->flash('error', $e instanceof InvalidArgumentException ? $e->getMessage() : 'Tahun pelajaran belum dapat disimpan. Periksa migrasi database.');
        }
        $this->redirect('/admin/kalender-pendidikan');
    }
}
