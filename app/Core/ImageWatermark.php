<?php
/**
 * ============================================
 * Image Watermark Helper
 * ============================================
 * Adds text watermark to uploaded images using PHP GD.
 */

declare(strict_types=1);

class ImageWatermark
{
    /**
     * Apply watermark text to an image file.
     *
     * @param string $filepath  Absolute path to the image file
     * @param string $text      Watermark text (e.g. "Property of Sekolah Rahayat")
     * @param int    $opacity   Opacity percentage (0 = transparent, 100 = opaque), default 30
     * @return bool True on success, false on failure
     */
    public static function apply(string $filepath, string $text, int $opacity = 30): bool
    {
        if (!file_exists($filepath) || !extension_loaded('gd')) {
            return false;
        }

        $mimeType = mime_content_type($filepath);
        $image = self::createImageFromFile($filepath, $mimeType);

        if (!$image) {
            return false;
        }

        $width = imagesx($image);
        $height = imagesy($image);

        // Skip very small images (thumbnails, icons)
        $minSize = defined('WATERMARK_MIN_SIZE') ? WATERMARK_MIN_SIZE : 200;
        if ($width < $minSize || $height < $minSize) {
            imagedestroy($image);
            return false;
        }

        // Calculate font size based on image dimensions
        $diagonal = sqrt($width * $width + $height * $height);
        $fontSize = max(12, (int) ($diagonal / 30));

        // Use built-in GD font for simplicity (no TTF dependency)
        // We'll draw repeated diagonal text pattern for professional watermark
        $alpha = (int) (127 - ($opacity / 100 * 127)); // GD alpha: 0=opaque, 127=transparent

        // Create watermark color (white with transparency)
        $watermarkColor = imagecolorallocatealpha($image, 255, 255, 255, $alpha);
        // Shadow color for contrast on light backgrounds
        $shadowColor = imagecolorallocatealpha($image, 0, 0, 0, (int) ($alpha + (127 - $alpha) * 0.5));

        // Try to find a TTF font
        $fontPath = self::findFont();

        if ($fontPath) {
            // TTF-based watermark (better quality)
            self::applyTTFWatermark($image, $text, $fontPath, $fontSize, $watermarkColor, $shadowColor, $width, $height);
        } else {
            // Fallback: GD built-in font watermark
            self::applyGDWatermark($image, $text, $watermarkColor, $shadowColor, $width, $height);
        }

        // Save the watermarked image
        $result = self::saveImage($image, $filepath, $mimeType);
        imagedestroy($image);

        return $result;
    }

    /**
     * Apply TTF-based diagonal watermark pattern
     */
    private static function applyTTFWatermark(
        \GdImage $image,
        string $text,
        string $fontPath,
        int $fontSize,
        int $color,
        int $shadowColor,
        int $width,
        int $height
    ): void {
        $angle = -30; // Diagonal angle

        // Calculate text bounding box
        $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
        $textWidth = abs($bbox[2] - $bbox[0]);
        $textHeight = abs($bbox[7] - $bbox[1]);

        // Calculate spacing between watermark repetitions
        $spacingX = $textWidth + max(100, (int) ($width * 0.15));
        $spacingY = $textHeight + max(80, (int) ($height * 0.15));

        // Draw repeated diagonal watermark pattern across the entire image
        for ($y = -$height; $y < $height * 2; $y += $spacingY) {
            for ($x = -$width; $x < $width * 2; $x += $spacingX) {
                // Shadow (offset by 2px)
                imagettftext($image, $fontSize, $angle, $x + 2, $y + 2, $shadowColor, $fontPath, $text);
                // Main text
                imagettftext($image, $fontSize, $angle, $x, $y, $color, $fontPath, $text);
            }
        }
    }

    /**
     * Fallback: GD built-in font watermark (no TTF required)
     */
    private static function applyGDWatermark(
        \GdImage $image,
        string $text,
        int $color,
        int $shadowColor,
        int $width,
        int $height
    ): void {
        // Use the largest built-in font
        $font = 5;
        $charWidth = imagefontwidth($font);
        $charHeight = imagefontheight($font);
        $textWidth = $charWidth * strlen($text);

        // Calculate spacing
        $spacingX = $textWidth + max(80, (int) ($width * 0.12));
        $spacingY = $charHeight + max(60, (int) ($height * 0.12));

        // Draw repeated pattern
        for ($y = 0; $y < $height; $y += $spacingY) {
            for ($x = -$textWidth; $x < $width + $textWidth; $x += $spacingX) {
                // Offset every other row for diagonal effect
                $offsetX = ($y / $spacingY) % 2 === 0 ? 0 : (int) ($spacingX / 2);
                // Shadow
                imagestring($image, $font, $x + $offsetX + 1, $y + 1, $text, $shadowColor);
                // Main text
                imagestring($image, $font, $x + $offsetX, $y, $text, $color);
            }
        }
    }

    /**
     * Create GD image resource from file
     */
    private static function createImageFromFile(string $filepath, string $mimeType): \GdImage|false
    {
        return match ($mimeType) {
            'image/jpeg' => imagecreatefromjpeg($filepath),
            'image/png' => imagecreatefrompng($filepath),
            'image/gif' => imagecreatefromgif($filepath),
            'image/webp' => imagecreatefromwebp($filepath),
            default => false,
        };
    }

    /**
     * Save GD image to file
     */
    private static function saveImage(\GdImage $image, string $filepath, string $mimeType): bool
    {
        return match ($mimeType) {
            'image/jpeg' => imagejpeg($image, $filepath, 90),
            'image/png' => imagepng($image, $filepath, 8),
            'image/gif' => imagegif($image, $filepath),
            'image/webp' => imagewebp($image, $filepath, 90),
            default => false,
        };
    }

    /**
     * Try to find a TTF font file
     */
    private static function findFont(): string|false
    {
        // Check common font paths
        $candidates = [];

        // Project-level custom font
        if (defined('BASE_PATH')) {
            $candidates[] = BASE_PATH . '/public/fonts/watermark.ttf';
        }

        // Windows system fonts
        $winFonts = 'C:/Windows/Fonts/';
        if (is_dir($winFonts)) {
            $candidates[] = $winFonts . 'arial.ttf';
            $candidates[] = $winFonts . 'segoeui.ttf';
            $candidates[] = $winFonts . 'calibri.ttf';
            $candidates[] = $winFonts . 'tahoma.ttf';
        }

        // Linux system fonts
        $linuxPaths = [
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
            '/usr/share/fonts/TTF/DejaVuSans.ttf',
            '/usr/share/fonts/dejavu/DejaVuSans.ttf',
        ];
        $candidates = array_merge($candidates, $linuxPaths);

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return false;
    }
}
