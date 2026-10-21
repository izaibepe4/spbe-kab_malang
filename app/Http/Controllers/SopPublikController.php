<?php

namespace App\Http\Controllers;

use App\Models\SopItem;
use Illuminate\Support\Facades\Storage;

class SopPublikController extends Controller
{
    /**
     * SOP: dengan preview/embed.
     * Model SopItem punya accessor getUrlAttribute() → $sop->url.
     * Field: slug, file_path, tipe (pdf|image|link?), ringkasan, urutan, aktif
     */
    public function index()
    {
        $items = SopItem::query()
            ->get(['id', 'slug', 'file_path', 'tipe', 'ringkasan']);

        return view('public.sop', compact('items'));
    }

    /**
     * Serve PDF SOP dengan header inline untuk preview di halaman
     */
    public function viewPdf(int $id)
    {
        $item = SopItem::query()
            ->findOrFail($id);

        // Pastikan tipe adalah PDF
        if ($item->tipe !== 'pdf') {
            abort(404, 'File bukan PDF');
        }

        // Wajib punya path file
        abort_unless($item->file_path, 404);

        $filePath = ltrim($item->file_path, '/');

        // Pastikan file ada di storage
        if (! Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        $filename = 'sop-'.$item->id.'.pdf';
        $fullPath = Storage::disk('public')->path($filePath);

        // Serve PDF dengan header inline untuk preview di iframe (bukan download)
        // Menggunakan response()->file() yang lebih reliable
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
