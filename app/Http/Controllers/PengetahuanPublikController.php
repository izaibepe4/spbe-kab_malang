<?php

namespace App\Http\Controllers;

use App\Models\PengetahuanCategory;

class PengetahuanPublikController extends Controller
{
    /**
     * Pengetahuan SPBE: kategori + item (link ke PDF jika ada).
     * Link saja, tanpa embed.
     */
    public function index()
    {
        $categories = PengetahuanCategory::query()
            ->with(['items' => fn ($q) => $q,
            ])
            ->get(['id', 'nama_kategori']);

        return view('public.pengetahuan', compact('categories'));
    }
}
