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
        $uploadPath = $this->uploadFile($file, $uploadDir, UPLOAD_ALLOWED_TYPES, 2 * 1024 * 1024);

        if ($uploadPath) {
            // Return JSON response for CKEditor
            echo json_encode([
                'url' => '/storage/' . $uploadPath
            ]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => ['message' => $this->uploadErrorMessage('Gagal mengunggah gambar')]]);
        }
    }
}
