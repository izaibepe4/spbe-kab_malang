<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class FaviconController extends Controller
{
    /**
     * Serve favicon dengan ukuran yang tepat (32x32) dengan mempertahankan aspect ratio
     * Logo ditempatkan di tengah canvas square dengan padding transparan
     */
    public function favicon(): Response
    {
        $logoPath = public_path('assets/img/logo-pemkab.png');

        if (! File::exists($logoPath)) {
            \Log::error('Favicon: Logo file not found at: '.$logoPath);
            abort(404, 'Favicon logo not found');
        }

        // Cek apakah GD Library tersedia
        if (! function_exists('imagecreatefrompng')) {
            // Jika GD tidak tersedia, langsung return file asli
            $fileContent = file_get_contents($logoPath);

            return response($fileContent, 200)
                ->header('Content-Type', 'image/png')
                ->header('Cache-Control', 'public, max-age=31536000, immutable')
                ->header('Pragma', 'public')
                ->header('Expires', gmdate('D, d M Y H:i:s', time() + 31536000).' GMT');
        }

        // Baca file gambar
        $image = @imagecreatefrompng($logoPath);
        if (! $image) {
            \Log::error('Favicon: Failed to load image from: '.$logoPath);
            // Fallback: return file asli
            $fileContent = file_get_contents($logoPath);

            return response($fileContent, 200)
                ->header('Content-Type', 'image/png')
                ->header('Cache-Control', 'public, max-age=31536000, immutable')
                ->header('Pragma', 'public')
                ->header('Expires', gmdate('D, d M Y H:i:s', time() + 31536000).' GMT');
        }

        // Ukuran favicon (harus square)
        $canvasSize = 32;

        // Get dimensi original
        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);

        // Hitung ukuran logo yang di-resize dengan mempertahankan aspect ratio
        // Logo akan di-fit ke dalam canvas dengan padding
        $padding = 2; // Padding 2px di setiap sisi
        $maxLogoSize = $canvasSize - ($padding * 2);

        $ratio = $originalWidth / $originalHeight;
        if ($ratio > 1) {
            // Lebar lebih besar dari tinggi
            $logoWidth = $maxLogoSize;
            $logoHeight = (int) ($maxLogoSize / $ratio);
        } else {
            // Tinggi lebih besar dari lebar atau sama
            $logoHeight = $maxLogoSize;
            $logoWidth = (int) ($maxLogoSize * $ratio);
        }

        // Hitung posisi tengah untuk logo
        $x = (int) (($canvasSize - $logoWidth) / 2);
        $y = (int) (($canvasSize - $logoHeight) / 2);

        // Buat canvas square dengan ukuran yang tepat
        $favicon = imagecreatetruecolor($canvasSize, $canvasSize);

        // Enable alpha blending dan save alpha
        imagealphablending($favicon, false);
        imagesavealpha($favicon, true);

        // Fill dengan transparent
        $transparent = imagecolorallocatealpha($favicon, 0, 0, 0, 127);
        imagefill($favicon, 0, 0, $transparent);

        // Resize logo dengan mempertahankan aspect ratio dan letakkan di tengah
        imagecopyresampled(
            $favicon,
            $image,
            $x, $y,  // Posisi di canvas (tengah)
            0, 0,    // Posisi di source (dari awal)
            $logoWidth, $logoHeight,  // Ukuran di canvas
            $originalWidth, $originalHeight  // Ukuran source
        );

        // Output sebagai PNG
        ob_start();
        imagepng($favicon);
        $imageData = ob_get_clean();

        // Cleanup
        imagedestroy($image);
        imagedestroy($favicon);

        return response($imageData, 200)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=31536000, immutable')
            ->header('Pragma', 'public')
            ->header('Expires', gmdate('D, d M Y H:i:s', time() + 31536000).' GMT');
    }

    /**
     * Serve favicon 16x16 untuk browser yang membutuhkan ukuran lebih kecil
     * Logo ditempatkan di tengah canvas square dengan padding transparan
     */
    public function favicon16(): Response
    {
        $logoPath = public_path('assets/img/logo-pemkab.png');

        if (! File::exists($logoPath)) {
            \Log::error('Favicon16: Logo file not found at: '.$logoPath);
            abort(404, 'Favicon logo not found');
        }

        // Cek apakah GD Library tersedia
        if (! function_exists('imagecreatefrompng')) {
            // Jika GD tidak tersedia, langsung return file asli
            $fileContent = file_get_contents($logoPath);

            return response($fileContent, 200)
                ->header('Content-Type', 'image/png')
                ->header('Cache-Control', 'public, max-age=31536000, immutable')
                ->header('Pragma', 'public')
                ->header('Expires', gmdate('D, d M Y H:i:s', time() + 31536000).' GMT');
        }

        $image = @imagecreatefrompng($logoPath);
        if (! $image) {
            \Log::error('Favicon16: Failed to load image from: '.$logoPath);
            // Fallback: return file asli
            $fileContent = file_get_contents($logoPath);

            return response($fileContent, 200)
                ->header('Content-Type', 'image/png')
                ->header('Cache-Control', 'public, max-age=31536000, immutable')
                ->header('Pragma', 'public')
                ->header('Expires', gmdate('D, d M Y H:i:s', time() + 31536000).' GMT');
        }

        // Ukuran favicon (harus square)
        $canvasSize = 16;

        // Get dimensi original
        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);

        // Hitung ukuran logo yang di-resize dengan mempertahankan aspect ratio
        // Logo akan di-fit ke dalam canvas dengan padding
        $padding = 1; // Padding 1px di setiap sisi (karena ukuran kecil)
        $maxLogoSize = $canvasSize - ($padding * 2);

        $ratio = $originalWidth / $originalHeight;
        if ($ratio > 1) {
            // Lebar lebih besar dari tinggi
            $logoWidth = $maxLogoSize;
            $logoHeight = (int) ($maxLogoSize / $ratio);
        } else {
            // Tinggi lebih besar dari lebar atau sama
            $logoHeight = $maxLogoSize;
            $logoWidth = (int) ($maxLogoSize * $ratio);
        }

        // Hitung posisi tengah untuk logo
        $x = (int) (($canvasSize - $logoWidth) / 2);
        $y = (int) (($canvasSize - $logoHeight) / 2);

        // Buat canvas square dengan ukuran yang tepat
        $favicon = imagecreatetruecolor($canvasSize, $canvasSize);
        imagealphablending($favicon, false);
        imagesavealpha($favicon, true);

        $transparent = imagecolorallocatealpha($favicon, 0, 0, 0, 127);
        imagefill($favicon, 0, 0, $transparent);

        // Resize logo dengan mempertahankan aspect ratio dan letakkan di tengah
        imagecopyresampled(
            $favicon,
            $image,
            $x, $y,  // Posisi di canvas (tengah)
            0, 0,    // Posisi di source (dari awal)
            $logoWidth, $logoHeight,  // Ukuran di canvas
            $originalWidth, $originalHeight  // Ukuran source
        );

        ob_start();
        imagepng($favicon);
        $imageData = ob_get_clean();

        imagedestroy($image);
        imagedestroy($favicon);

        return response($imageData, 200)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=31536000, immutable')
            ->header('Pragma', 'public')
            ->header('Expires', gmdate('D, d M Y H:i:s', time() + 31536000).' GMT');
    }

    /**
     * Serve favicon.ico untuk browser yang mencari favicon.ico di root
     * Menggunakan logo-pemkab.png yang sama
     */
    public function faviconIco(): Response
    {
        $logoPath = public_path('assets/img/logo-pemkab.png');

        if (! File::exists($logoPath)) {
            \Log::error('FaviconIco: Logo file not found at: '.$logoPath);
            abort(404, 'Favicon logo not found');
        }

        // Cek apakah GD Library tersedia
        if (! function_exists('imagecreatefrompng')) {
            // Jika GD tidak tersedia, langsung return file asli
            $fileContent = file_get_contents($logoPath);

            return response($fileContent, 200)
                ->header('Content-Type', 'image/x-icon')
                ->header('Cache-Control', 'public, max-age=31536000, immutable')
                ->header('Pragma', 'public')
                ->header('Expires', gmdate('D, d M Y H:i:s', time() + 31536000).' GMT');
        }

        // Baca file gambar
        $image = @imagecreatefrompng($logoPath);
        if (! $image) {
            \Log::error('FaviconIco: Failed to load image from: '.$logoPath);
            // Fallback: return file asli
            $fileContent = file_get_contents($logoPath);

            return response($fileContent, 200)
                ->header('Content-Type', 'image/x-icon')
                ->header('Cache-Control', 'public, max-age=31536000, immutable')
                ->header('Pragma', 'public')
                ->header('Expires', gmdate('D, d M Y H:i:s', time() + 31536000).' GMT');
        }

        // Ukuran favicon (harus square)
        $canvasSize = 32;

        // Get dimensi original
        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);

        // Hitung ukuran logo yang di-resize dengan mempertahankan aspect ratio
        $padding = 2;
        $maxLogoSize = $canvasSize - ($padding * 2);

        $ratio = $originalWidth / $originalHeight;
        if ($ratio > 1) {
            $logoWidth = $maxLogoSize;
            $logoHeight = (int) ($maxLogoSize / $ratio);
        } else {
            $logoHeight = $maxLogoSize;
            $logoWidth = (int) ($maxLogoSize * $ratio);
        }

        // Hitung posisi tengah untuk logo
        $x = (int) (($canvasSize - $logoWidth) / 2);
        $y = (int) (($canvasSize - $logoHeight) / 2);

        // Buat canvas square dengan ukuran yang tepat
        $favicon = imagecreatetruecolor($canvasSize, $canvasSize);

        // Enable alpha blending dan save alpha
        imagealphablending($favicon, false);
        imagesavealpha($favicon, true);

        // Fill dengan transparent
        $transparent = imagecolorallocatealpha($favicon, 0, 0, 0, 127);
        imagefill($favicon, 0, 0, $transparent);

        // Resize logo dengan mempertahankan aspect ratio dan letakkan di tengah
        imagecopyresampled(
            $favicon,
            $image,
            $x, $y,
            0, 0,
            $logoWidth, $logoHeight,
            $originalWidth, $originalHeight
        );

        // Output sebagai PNG (browser akan menerima PNG sebagai favicon.ico)
        ob_start();
        imagepng($favicon);
        $imageData = ob_get_clean();

        // Cleanup
        imagedestroy($image);
        imagedestroy($favicon);

        return response($imageData, 200)
            ->header('Content-Type', 'image/x-icon')
            ->header('Cache-Control', 'public, max-age=31536000')
            ->header('Pragma', 'public')
            ->header('Expires', gmdate('D, d M Y H:i:s', time() + 31536000).' GMT');
    }
}
