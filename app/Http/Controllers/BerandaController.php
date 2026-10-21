<?php

namespace App\Http\Controllers;

use App\Models\SectionInfoSpbe;
use App\Models\TujuanSpbe;

class BerandaController extends Controller
{
    /**
     * Halaman Beranda SPBE
     * Menampilkan:
     * - Hero Section
     * - Definisi dan Tujuan SPBE (dari SectionInfoSpbe dan TujuanSpbe)
     * - Berita Terkini (placeholder untuk API)
     * - Program Kerja Pembangunan Tahunan (placeholder)
     * - Perkembangan SPBE dengan Chart (placeholder untuk API)
     * - Indikator (placeholder untuk gambar dari panel)
     */
    public function index()
    {
        // Ambil section definisi dan tujuan SPBE
        $sectionInfo = SectionInfoSpbe::query()
            ->orderBy('updated_at', 'desc')
            ->first();

        // Ambil tujuan SPBE (features grid)
        $tujuanSpbe = TujuanSpbe::query()
            ->get(['id', 'ikon', 'judul', 'deskripsi']);

        // TODO: Berita terkini dari API (tim belum mendapat API)
        $beritaTerkini = [];

        // TODO: Data perkembangan SPBE dari API (untuk chart)
        $perkembanganData = null;

        // TODO: Indikator gambar dari panel (belum ada resource di Filament)
        $indikatorGambar = null;

        return view('public.beranda', compact(
            'sectionInfo',
            'tujuanSpbe',
            'beritaTerkini',
            'perkembanganData',
            'indikatorGambar'
        ));
    }
}
