<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;

class SecureFileUpload implements Rule
{
    protected array $allowedMimeTypes;

    protected int $maxSize;

    protected ?string $message = null;

    public function __construct(array $allowedMimeTypes, int $maxSizeInKB)
    {
        $this->allowedMimeTypes = $allowedMimeTypes;
        $this->maxSize = $maxSizeInKB * 1024; // Convert to bytes
    }

    /**
     * Determine if the validation rule passes.
     */
    public function passes($attribute, $value): bool
    {
        if (! $value instanceof UploadedFile) {
            return false;
        }

        // Check file size
        if ($value->getSize() > $this->maxSize) {
            $this->message = 'File terlalu besar. Maksimal '.($this->maxSize / 1024).' KB.';

            return false;
        }

        // Get actual MIME type from file content (not just extension)
        $mimeType = $value->getMimeType();
        $clientMimeType = $value->getClientMimeType();

        // Validate MIME type
        if (! in_array($mimeType, $this->allowedMimeTypes) && ! in_array($clientMimeType, $this->allowedMimeTypes)) {
            $this->message = 'Tipe file tidak diizinkan. Hanya file dengan tipe: '.implode(', ', $this->allowedMimeTypes).' yang diizinkan.';

            return false;
        }

        // Additional security: Check file extension matches MIME type
        $extension = strtolower($value->getClientOriginalExtension());
        $allowedExtensions = $this->getExtensionsForMimeTypes($this->allowedMimeTypes);

        if (! in_array($extension, $allowedExtensions)) {
            $this->message = 'Ekstensi file tidak sesuai dengan tipe file.';

            return false;
        }

        // Sanitize filename - remove dangerous characters
        $filename = $value->getClientOriginalName();
        if (preg_match('/[\/\\\\\x00-\x1F\x7F]/', $filename)) {
            $this->message = 'Nama file mengandung karakter yang tidak diizinkan.';

            return false;
        }

        // Check for path traversal attempts
        if (strpos($filename, '..') !== false || strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
            $this->message = 'Nama file tidak valid.';

            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return $this->message ?? 'File tidak valid.';
    }

    /**
     * Get allowed extensions for given MIME types
     */
    protected function getExtensionsForMimeTypes(array $mimeTypes): array
    {
        $extensions = [];
        $mimeToExt = [
            'application/pdf' => ['pdf'],
            'image/jpeg' => ['jpg', 'jpeg'],
            'image/png' => ['png'],
            'image/gif' => ['gif'],
            'image/webp' => ['webp'],
            'image/*' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        ];

        foreach ($mimeTypes as $mimeType) {
            if (isset($mimeToExt[$mimeType])) {
                $extensions = array_merge($extensions, $mimeToExt[$mimeType]);
            }
        }

        return array_unique($extensions);
    }
}
