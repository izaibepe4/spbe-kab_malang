<?php

namespace App\Http\Controllers;

use App\Models\PaparanCategory;
use App\Models\PaparanItem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaparanPublikController extends Controller
{
    /**
     * Paparan: kategori + item dengan preview/embed.
     * Jika item punya file_pdf_path → preview PDF di halaman.
     * Jika punya embed_url → embed Google Slides/Drive.
     */
    public function index()
    {
        $categories = PaparanCategory::query()
            ->with(['items' => fn ($q) => $q,
            ])
            ->get(['id', 'nama_kategori']);

        return view('public.paparan', compact('categories'));
    }

    /**
     * Serve PDF Paparan dengan header inline untuk preview di halaman
     */
    public function viewPdf(int $id)
    {
        $item = PaparanItem::findOrFail($id);

        // Pastikan tipe adalah PDF
        if ($item->tipe_embed !== 'pdf') {
            abort(404, 'File bukan PDF');
        }

        // Wajib punya path file
        abort_unless($item->file_pdf_path, 404);

        $filePath = ltrim($item->file_pdf_path, '/');

        // Pastikan file ada di storage
        if (! Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        $filename = Str::slug($item->judul_item ?: 'paparan').'.pdf';
        $fullPath = Storage::disk('public')->path($filePath);

        // Serve PDF dengan header inline untuk preview di iframe (bukan download)
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
