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

        // Validation
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mimeType = mime_content_type($file['tmp_name']);
        if (!in_array($mimeType, $allowedTypes, true)) {
            http_response_code(400);
            echo json_encode(['error' => ['message' => 'Invalid file type. Only JPG, PNG, WEBP, and GIF are allowed.']]);
            return;
        }

        if ($file['size'] > 2 * 1024 * 1024) { // 2MB
            http_response_code(400);
            echo json_encode(['error' => ['message' => 'File too large. Maximum size is 2MB.']]);
            return;
        }

        // Upload
        $uploadDir = 'uploads/news/' . date('Y/m');
        $uploadPath = $this->uploadFile($file, $uploadDir);

        if ($uploadPath) {
            // Return JSON response for CKEditor
            echo json_encode([
                'url' => '/storage/' . $uploadPath
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => ['message' => 'Failed to upload file.']]);
        }
    }
}
