<?php

declare(strict_types=1);

final class SecurityMigrationController extends Controller
{
    public function before(string $action): bool
    {
        $this->requireRole('admin');
        header('Cache-Control: private, no-store');
        return true;
    }

    public function index(): void
    {
        $this->requireRole('admin');
        $this->view('backend.system.migration', ['title' => 'Migrasi Keamanan',
            'user' => $this->currentUser(), 'flash' => $this->getFlash(),
            'result' => $_SESSION['_security_migration_result'] ?? null], 'backend');
    }

    public function run(): void
    {
        $this->requireRole('admin');
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Allow: POST'); http_response_code(405); return;
        }
        if (!$this->validateCsrf()) {
            http_response_code(403);
            echo 'Sesi tidak valid. Buka kembali halaman migrasi dan coba lagi.';
            return;
        }
        if (($_POST['backup_ready'] ?? '') !== '1') {
            $this->flash('error', 'Pastikan backup lengkap database, storage, dan kunci enkripsi telah disimpan.');
            $this->redirect('/admin/pembaruan/migrasi');
            return;
        }
        try {
            $result = SecurityDataMigration::runBatch((int) ($_SESSION['_security_migration_cursor'] ?? 0));
            $_SESSION['_security_migration_cursor'] = $result['complete'] ? 0 : $result['cursor'];
            $_SESSION['_security_migration_result'] = $result;
            $this->flash('success', $result['complete'] ? 'Migrasi selesai. Backup setiap perubahan telah diverifikasi.' : 'Tahap berhasil. Klik lanjutkan untuk memproses tahap berikutnya.');
        } catch (Throwable $error) {
            error_log('Security migration batch failed: ' . get_class($error));
            $this->flash('error', 'Tahap belum selesai. Periksa struktur SQL, ruang disk, izin storage, dan kunci enkripsi hosting. Setelah diperbaiki, coba lagi; data yang sudah dienkripsi akan dilewati.');
        }
        $this->redirect('/admin/pembaruan/migrasi');
    }
}
