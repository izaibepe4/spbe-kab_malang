<?php

namespace App\Http\Controllers;

use App\Models\AgendaImage;

class AgendaPublikController extends Controller
{
    /**
     * Halaman Agenda (gambar saja) dengan preview (img).
     */
    public function index()
    {
        $images = AgendaImage::query()
            ->get(['id', 'image_path']);

        return view('public.agenda', compact('images'));
    }
}
