<?php
/**
 * ============================================
 * Upload Controller - Handles File Uploads
 * ============================================
 */

declare(strict_types=1);

class UploadController extends Controller
{
    /**
     * Handle Image Upload from CKEditor
     */
    public function image(): void
    {
        // Require authentication
        $this->requireAuth();

        header('Content-Type: application/json');

        if (empty($_FILES['upload'])) {
            http_response_code(400);
            echo json_encode(['error' => ['message' => 'No file uploaded.']]);
            return;
        }

        $file = $_FILES['upload'];

        // Upload
        $uploadDir = 'uploads/news/' . date('Y/m');
        $uploadPath = $this->uploadFile($file, $uploadDir, UPLOAD_ALLOWED_TYPES, UPLOAD_MAX_SIZE);

        if ($uploadPath) {
            $url = '/storage/' . $uploadPath;
            $this->rememberEditorUpload(
                (string) $this->post('editor_upload_batch', ''),
                $url,
                'image',
                (string) ($file['name'] ?? 'Gambar berita')
            );

            // Return JSON response for CKEditor
            echo json_encode([
                'url' => $url,
                'default' => $url,
            ]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => ['message' => $this->uploadErrorMessage('Gagal mengunggah gambar')]]);
        }
    }

    /**
     * Handle PDF upload from the news editor.
     */
    public function pdf(): void
    {
        $this->requireAuth();

        header('Content-Type: application/json');

        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $this->post(CSRF_TOKEN_NAME);
        if (!Security::validateCsrfToken($token)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }

        if (empty($_FILES['pdf'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Tidak ada file PDF yang diunggah.']);
            return;
        }

        $uploadDir = 'uploads/news/pdf/' . date('Y/m');
        $uploadPath = $this->uploadFile($_FILES['pdf'], $uploadDir, ['application/pdf'], 10 * 1024 * 1024);

        if (!$uploadPath) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => $this->uploadErrorMessage('Gagal mengunggah PDF')]);
            return;
        }

        $url = '/storage/' . $uploadPath;
        $this->rememberEditorUpload(
            (string) $this->post('editor_upload_batch', ''),
            $url,
            'pdf',
            pathinfo((string) ($_FILES['pdf']['name'] ?? 'Dokumen PDF'), PATHINFO_FILENAME)
        );

        echo json_encode([
            'success' => true,
            'url' => $url,
            'embedHtml' => $this->pdfEmbedHtml($url, $_FILES['pdf']['name'] ?? 'Dokumen PDF'),
        ]);
    }

    private function pdfEmbedHtml(string $url, string $filename): string
    {
        $title = pathinfo($filename, PATHINFO_FILENAME) ?: 'Dokumen PDF';
        $safeTitle = Security::escape($title);
        $safeUrl = Security::escape($url);

        return '<figure class="pdf-embed"><iframe src="' . $safeUrl . '" title="' . $safeTitle . '" loading="lazy"></iframe><figcaption>' . $safeTitle . '</figcaption></figure>';
    }
}
