<?php

namespace App\Http\Controllers;

use App\Models\SectionInfoSpbe;
use App\Models\TujuanSpbe;

class InfoSpbePublikController extends Controller
{
    /**
     * Info SPBE: gabungkan SectionInfoSpbe + TujuanSpbe dalam 1 halaman.
     * Section: judul + deskripsi_html (rich-text)
     * Tujuan: ikon (optional), judul, deskripsi
     */
    public function index()
    {
        $sections = SectionInfoSpbe::query()
            ->get(['id', 'judul', 'deskripsi_html']);

        $tujuan = TujuanSpbe::query()
            ->get(['id', 'ikon', 'judul', 'deskripsi']);

        return view('public.info_spbe', compact('sections', 'tujuan'));
    }
}
