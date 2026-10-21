<?php

namespace App\Http\Controllers;

use App\Models\PengetahuanItem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicDocumentController extends Controller
{
    /**
     * Tampilkan PDF Pengetahuan secara inline (tab baru).
     */
    public function pengetahuan(int $id)
    {
        // Mulai dari query dasar
        $q = PengetahuanItem::query();

        // Tambahkan filter 'aktif' HANYA bila kolomnya memang ada
        if (Schema::hasColumn('pengetahuan_items', 'aktif')) {
            $q->where('aktif', true);
        }

        $item = $q->findOrFail($id);

        // Wajib punya path file
        abort_unless($item->file_pdf_path, 404);

        $filePath = ltrim($item->file_pdf_path, '/');

        // Pastikan file ada di storage
        if (! Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        $filename = Str::slug($item->judul_item ?: 'pengetahuan').'.pdf';
        $fullPath = Storage::disk('public')->path($filePath);

        // Serve PDF dengan header inline untuk preview di iframe (bukan download)
        // Menggunakan response()->file() dengan header yang tepat
        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.addslashes($filename).'"',
            'Cache-Control' => 'public, max-age=3600',
            'Accept-Ranges' => 'bytes',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN', // Allow iframe dari same origin
        ]);
    }
}
