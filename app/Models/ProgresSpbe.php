<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProgresSpbe extends Model
{
    protected $fillable = [
        'judul',
        'gambar',
        'file_pdf_path',
        'nilai',
        'tahun',
    ];

    /**
     * Get URL untuk gambar thumbnail
     */
    public function getGambarUrlAttribute(): ?string
    {
        if (! $this->gambar) {
            return null;
        }

        return Storage::url($this->gambar);
    }

    /**
     * Get URL untuk file PDF
     */
    public function getPdfUrlAttribute(): ?string
    {
        if (! $this->file_pdf_path) {
            return null;
        }

        return Storage::url($this->file_pdf_path);
    }
}
