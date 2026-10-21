<?php

namespace App\Http\Controllers;

use App\Models\RegulasiCategory;
use App\Models\RegulasiItem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RegulasiPublikController extends Controller
{
    /**
     * Regulasi: kategori + item dengan link ke PDF preview di tab baru.
     */
    public function index()
    {
        $categories = RegulasiCategory::query()
            ->with(['items' => fn ($q) => $q,
            ])
            ->get(['id', 'nama_kategori']);

        return view('public.regulasi', compact('categories'));
    }

    /**
     * Serve PDF Regulasi dengan header inline untuk preview di tab baru
     */
    public function viewPdf(int $id)
    {
        $item = RegulasiItem::findOrFail($id);

        // Wajib punya path file
        abort_unless($item->file_pdf_path, 404);

        $filePath = ltrim($item->file_pdf_path, '/');

        // Pastikan file ada di storage
        if (! Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        $filename = Str::slug($item->judul_regulasi ?: 'regulasi').'.pdf';
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
