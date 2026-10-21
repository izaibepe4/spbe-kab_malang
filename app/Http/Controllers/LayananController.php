<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Layanan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LayananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $user = Auth::guard('web')->user();
        $admin = Auth::guard('admin')->user();
        $isAdmin = $admin instanceof Admin;

        $query = Layanan::with('user')
            ->where('tahun', $tahun);

        // Admin bisa lihat semua data, OPD hanya data sendiri
        if ($isAdmin) {
            // Admin bisa melihat semua data dari semua OPD
            // Ambil semua OPD (termasuk yang belum di-approve) untuk admin
            $users = User::orderBy('nama_opd')->get();

            // Filter by OPD if requested
            if ($request->has('opd_id') && $request->opd_id) {
                $query->where('user_id', $request->opd_id);
            }
        } else {
            // Pastikan user adalah OPD (User model)
            if (! $user instanceof User) {
                abort(403, 'Akses ditolak. Hanya OPD yang dapat mengakses halaman ini.');
            }
            // OPD hanya bisa lihat data sendiri
            $query->where('user_id', $user->id);
            $users = collect();
        }

        $layanans = $query->orderBy('created_at', 'desc')->get();

        return view('layanan.index', compact('layanans', 'tahun', 'users', 'isAdmin'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::guard('web')->user();
        $admin = Auth::guard('admin')->user();
        $isAdmin = $admin instanceof Admin;

        // Admin bisa create untuk semua OPD, OPD hanya untuk dirinya sendiri
        if ($isAdmin) {
            // Admin bisa create untuk semua OPD (termasuk yang belum di-approve)
            $users = User::orderBy('nama_opd')->get();
        } else {
            // Pastikan user adalah OPD (User model)
            if (! $user instanceof User) {
                abort(403, 'Akses ditolak. Hanya OPD yang dapat mengakses halaman ini.');
            }
            $users = collect();
        }

        $tahun = date('Y');

        return view('layanan.create', compact('users', 'tahun', 'isAdmin', 'user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::guard('web')->user();
        $admin = Auth::guard('admin')->user();
        $isAdmin = $admin instanceof Admin;

        $validated = $request->validate([
            'tahun' => 'required|integer|min:2020|max:2100',
            'triwulan' => 'required|integer|min:1|max:4',
            'unit_pelaksana' => 'required|string|max:255',
            'nama_pic' => 'nullable|string|max:255',
            'email_pic' => 'nullable|email|max:255',
            'hp_telp_pic' => 'nullable|string|max:255',
            'bidang_bagian' => 'required|string|max:255',
            'status_layanan' => 'required|in:berjalan,direncanakan,dihentikan',
            'nama_layanan' => 'required|string|max:255',
            'deskripsi_layanan' => 'nullable|string',
            'didukung_aplikasi' => 'boolean',
            'target_pengguna' => 'required|in:Publik/Masyarakat,Internal Pemerintahan',
            'kementerian_lembaga_terkait' => 'nullable|string',
            'supplier_data' => 'nullable|string',
            'data_masuk' => 'nullable|string',
            'data_hasil' => 'nullable|string',
            'keterbukaan_data' => 'nullable|in:terbuka,terbatas,tertutup',
            'jenis_data' => 'nullable|string|max:255',
            'frekuensi_pembaruan' => 'nullable|in:Realtime,Harian,Mingguan,Bulanan,Tiga Bulanan,Enam Bulanan,Tahunan,Dua Tahunan,Tiga Tahunan,Lima Tahunan',
            'terhubung_sistem_lain' => 'boolean',
            'nama_aplikasi' => 'nullable|string|max:255',
            'jenis_aplikasi' => 'nullable|string|max:255',
            'jenis_aplikasi_lainnya' => 'nullable|string|max:255',
            'url_akses' => 'nullable|string',
            'hasil_output_aplikasi' => 'nullable|string',
            'lokasi_penyimpanan' => 'nullable|string',
        ]);

        if ($isAdmin) {
            // Admin bisa create untuk OPD manapun
            $user_id = $request->input('user_id');
            if (! $user_id) {
                return back()->withErrors(['user_id' => 'Pilih OPD terlebih dahulu.'])->withInput();
            }
            $selectedUser = User::findOrFail($user_id);
            $validated['user_id'] = $selectedUser->id;
            $validated['unit_pelaksana'] = $selectedUser->nama_opd;
        } else {
            // Pastikan user adalah OPD (User model)
            if (! $user instanceof User) {
                abort(403, 'Akses ditolak. Hanya OPD yang dapat mengakses halaman ini.');
            }
            // OPD pakai user sendiri
            $validated['user_id'] = $user->id;
            $validated['unit_pelaksana'] = $user->nama_opd; // Selalu pakai nama_opd dari user, tidak bisa diubah
        }

        Layanan::create($validated);

        return redirect()->route('layanan.index', ['tahun' => $validated['tahun']])
            ->with('success', 'Data layanan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Layanan $layanan)
    {
        $user = Auth::guard('web')->user();
        $admin = Auth::guard('admin')->user();
        $isAdmin = $admin instanceof Admin;

        // Admin bisa lihat semua data, OPD hanya data sendiri
        if (! $isAdmin) {
            // Pastikan user adalah OPD (User model)
            if (! $user instanceof User) {
                abort(403, 'Akses ditolak. Hanya OPD yang dapat mengakses halaman ini.');
            }
            // Authorization: OPD hanya bisa lihat data sendiri
            if ($layanan->user_id !== $user->id) {
                abort(403);
            }
        }

        return view('layanan.show', compact('layanan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Layanan $layanan)
    {
        $user = Auth::guard('web')->user();
        $admin = Auth::guard('admin')->user();
        $isAdmin = $admin instanceof Admin;

        // Admin bisa edit semua data, OPD hanya data sendiri
        if (! $isAdmin) {
            // Pastikan user adalah OPD (User model)
            if (! $user instanceof User) {
                abort(403, 'Akses ditolak. Hanya OPD yang dapat mengakses halaman ini.');
            }
            // Authorization: OPD hanya bisa edit data sendiri
            if ($layanan->user_id !== $user->id) {
                abort(403);
            }
            $users = collect();
        } else {
            // Admin bisa edit untuk semua OPD (termasuk yang belum di-approve)
            $users = User::orderBy('nama_opd')->get();
        }

        return view('layanan.edit', compact('layanan', 'users', 'isAdmin', 'user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Layanan $layanan)
    {
        $user = Auth::guard('web')->user();
        $admin = Auth::guard('admin')->user();
        $isAdmin = $admin instanceof Admin;

        // Admin bisa update semua data, OPD hanya data sendiri
        if (! $isAdmin) {
            // Pastikan user adalah OPD (User model)
            if (! $user instanceof User) {
                abort(403, 'Akses ditolak. Hanya OPD yang dapat mengakses halaman ini.');
            }
            // Authorization: OPD hanya bisa update data sendiri
            if ($layanan->user_id !== $user->id) {
                abort(403);
            }
        }

        $validated = $request->validate([
            'tahun' => 'required|integer|min:2020|max:2100',
            'triwulan' => 'required|integer|min:1|max:4',
            'unit_pelaksana' => 'required|string|max:255',
            'nama_pic' => 'nullable|string|max:255',
            'email_pic' => 'nullable|email|max:255',
            'hp_telp_pic' => 'nullable|string|max:255',
            'bidang_bagian' => 'required|string|max:255',
            'status_layanan' => 'required|in:berjalan,direncanakan,dihentikan',
            'nama_layanan' => 'required|string|max:255',
            'deskripsi_layanan' => 'nullable|string',
            'didukung_aplikasi' => 'boolean',
            'target_pengguna' => 'required|in:Publik/Masyarakat,Internal Pemerintahan',
            'kementerian_lembaga_terkait' => 'nullable|string',
            'supplier_data' => 'nullable|string',
            'data_masuk' => 'nullable|string',
            'data_hasil' => 'nullable|string',
            'keterbukaan_data' => 'nullable|in:terbuka,terbatas,tertutup',
            'jenis_data' => 'nullable|string|max:255',
            'frekuensi_pembaruan' => 'nullable|in:Realtime,Harian,Mingguan,Bulanan,Tiga Bulanan,Enam Bulanan,Tahunan,Dua Tahunan,Tiga Tahunan,Lima Tahunan',
            'terhubung_sistem_lain' => 'boolean',
            'nama_aplikasi' => 'nullable|string|max:255',
            'jenis_aplikasi' => 'nullable|string|max:255',
            'jenis_aplikasi_lainnya' => 'nullable|string|max:255',
            'url_akses' => 'nullable|string',
            'hasil_output_aplikasi' => 'nullable|string',
            'lokasi_penyimpanan' => 'nullable|string',
        ]);

        if ($isAdmin) {
            // Admin bisa update user_id dan unit_pelaksana jika diperlukan
            if ($request->has('user_id') && $request->input('user_id') != $layanan->user_id) {
                $selectedUser = User::findOrFail($request->input('user_id'));
                $validated['user_id'] = $selectedUser->id;
                $validated['unit_pelaksana'] = $selectedUser->nama_opd;
            } else {
                // Tetap gunakan user_id dan unit_pelaksana yang sudah ada
                $validated['user_id'] = $layanan->user_id;
                $validated['unit_pelaksana'] = $layanan->unit_pelaksana;
            }
        } else {
            // OPD: unit_pelaksana selalu dari nama_opd user, tidak bisa diubah
            $validated['unit_pelaksana'] = $user->nama_opd;
            $validated['user_id'] = $user->id;
        }

        $layanan->update($validated);

        return redirect()->route('layanan.index', ['tahun' => $validated['tahun']])
            ->with('success', 'Data layanan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Layanan $layanan)
    {
        $user = Auth::guard('web')->user();
        $admin = Auth::guard('admin')->user();
        $isAdmin = $admin instanceof Admin;

        // Admin bisa delete semua data, OPD hanya data sendiri
        if (! $isAdmin) {
            // Pastikan user adalah OPD (User model)
            if (! $user instanceof User) {
                abort(403, 'Akses ditolak. Hanya OPD yang dapat mengakses halaman ini.');
            }
            // Authorization: OPD hanya bisa delete data sendiri
            if ($layanan->user_id !== $user->id) {
                abort(403);
            }
        }

        $tahun = $layanan->tahun;
        $layanan->delete();

        return redirect()->route('layanan.index', ['tahun' => $tahun])
            ->with('success', 'Data layanan berhasil dihapus.');
    }
}
