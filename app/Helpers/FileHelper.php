<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FileHelper
{
    /**
     * Sanitize filename to prevent path traversal and XSS
     */
    public static function sanitizeFilename(string $filename): string
    {
        // Remove path traversal attempts
        $filename = str_replace(['../', '..\\', '/', '\\'], '', $filename);

        // Remove null bytes and control characters
        $filename = preg_replace('/[\x00-\x1F\x7F]/', '', $filename);

        // Remove dangerous characters
        $filename = preg_replace('/[<>:"|?*]/', '', $filename);

        // Limit length
        $filename = Str::limit($filename, 255);

        // Ensure filename is not empty
        if (empty($filename)) {
            $filename = 'file_'.time();
        }

        return $filename;
    }

    /**
     * Validate file MIME type matches extension
     */
    public static function validateMimeType(UploadedFile $file, array $allowedMimeTypes): bool
    {
        $mimeType = $file->getMimeType();
        $clientMimeType = $file->getClientMimeType();

        return in_array($mimeType, $allowedMimeTypes) ||
            in_array($clientMimeType, $allowedMimeTypes);
    }

    /**
     * Get safe filename with timestamp prefix
     */
    public static function getSafeFilename(UploadedFile $file, string $prefix = ''): string
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);

        $sanitized = self::sanitizeFilename($nameWithoutExt);
        $prefix = $prefix ? $prefix.'_' : '';

        return $prefix.$sanitized.'_'.time().'.'.$extension;
    }
}
