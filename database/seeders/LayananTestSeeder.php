<?php

namespace Database\Seeders;

use App\Models\Layanan;
use App\Models\User;
use Illuminate\Database\Seeder;

class LayananTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua user yang ada
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('Tidak ada user yang ditemukan. Silakan buat user terlebih dahulu.');
            return;
        }

        $this->command->info("Membuat data test layanan untuk {$users->count()} OPD...");

        // Untuk setiap user, buat data layanan untuk 2024 dan 2025
        foreach ($users as $user) {
            $this->command->info("Membuat data untuk: {$user->nama_opd}");

            // Untuk tahun 2024 - buat data untuk triwulan 1 dan 2
            $this->createLayananForUser($user, 2024, [1, 2]);

            // Untuk tahun 2025 - buat data untuk triwulan 3 dan 4
            $this->createLayananForUser($user, 2025, [3, 4]);
        }

        $this->command->info('Selesai membuat data test layanan!');
    }

    private function createLayananForUser(User $user, int $tahun, array $triwulans): void
    {
        foreach ($triwulans as $triwulan) {
            // Buat 2-3 layanan per triwulan untuk setiap user
            $jumlahLayanan = rand(2, 3);

            for ($i = 1; $i <= $jumlahLayanan; $i++) {
                $didukungAplikasi = fake()->boolean(70);

                Layanan::create([
                    'user_id' => $user->id,
                    'tahun' => $tahun,
                    'triwulan' => $triwulan,
                    'unit_pelaksana' => $user->nama_opd,
                    'nama_pic' => $user->name,
                    'email_pic' => $user->email,
                    'hp_telp_pic' => $user->no_hp ?? fake()->phoneNumber(),
                    'bidang_bagian' => fake()->randomElement([
                        'Bidang Teknologi Informasi',
                        'Bidang Pelayanan Publik',
                        'Bidang Administrasi',
                        'Bidang Perencanaan',
                        'Seksi Data dan Informasi',
                        'Seksi Aplikasi',
                        'Bagian Umum'
                    ]),
                    'status_layanan' => fake()->randomElement(['berjalan', 'direncanakan', 'dihentikan']),
                    'nama_layanan' => fake()->randomElement([
                        'Sistem Informasi Pelayanan Publik',
                        'Aplikasi Perizinan Online',
                        'Portal Data Terbuka',
                        'Sistem Manajemen Dokumen',
                        'Aplikasi Monitoring Kinerja',
                        'Sistem Pelaporan Online',
                        'Portal Pengaduan Masyarakat',
                        'Aplikasi Absensi Digital',
                        'Sistem Inventory Aset',
                        'Portal Berita dan Informasi'
                    ]) . " {$user->nama_opd} " . fake()->randomElement(['v1.0', 'v2.0', 'Beta']),
                    'deskripsi_layanan' => fake()->paragraph(2),
                    'didukung_aplikasi' => $didukungAplikasi,
                    'target_pengguna' => fake()->randomElement(['Publik/Masyarakat', 'Internal Pemerintahan']),
                    'kementerian_lembaga_terkait' => fake()->optional(0.6)->randomElement([
                        'Kementerian Komunikasi dan Informatika',
                        'Kementerian Dalam Negeri',
                        'Kementerian Pendayagunaan Aparatur Negara dan Reformasi Birokrasi',
                        'Badan Siber dan Sandi Negara'
                    ]),
                    'supplier_data' => fake()->optional(0.7)->randomElement([
                        'Database Internal',
                        'API Eksternal',
                        'Input Manual',
                        'Integrasi Sistem Lain'
                    ]),
                    'data_masuk' => fake()->optional(0.8)->randomElement([
                        'Data Penduduk, Data Perizinan',
                        'Data Transaksi, Data Pengguna',
                        'Data Laporan, Data Monitoring',
                        'Data Master, Data Referensi'
                    ]),
                    'data_hasil' => fake()->optional(0.8)->randomElement([
                        'Laporan Statistik, Dashboard',
                        'Sertifikat Digital, Dokumen',
                        'Notifikasi, Alert System',
                        'Data Analitik, Grafik'
                    ]),
                    'keterbukaan_data' => fake()->randomElement(['terbuka', 'terbatas', 'tertutup']),
                    'jenis_data' => fake()->optional(0.7)->randomElement([
                        'Statistik',
                        'Keuangan',
                        'Geospasial',
                        'Administratif',
                        'Pelayanan Publik'
                    ]),
                    'frekuensi_pembaruan' => fake()->randomElement([
                        'Realtime',
                        'Harian',
                        'Mingguan',
                        'Bulanan',
                        'Tiga Bulanan',
                        'Tahunan'
                    ]),
                    'terhubung_sistem_lain' => fake()->boolean(60),
                    'nama_aplikasi' => $didukungAplikasi ? fake()->randomElement([
                        'SIPADU (Sistem Pelayanan Administrasi Terpadu)',
                        'SIMPEG (Sistem Informasi Manajemen Pegawai)',
                        'SIMDA (Sistem Informasi Manajemen Daerah)',
                        'e-Office (Electronic Office)',
                        'LPSE (Layanan Pengadaan Secara Elektronik)'
                    ]) : null,
                    'jenis_aplikasi' => $didukungAplikasi ? fake()->randomElement(['Web', 'Mobile', 'Desktop']) : null,
                    'jenis_aplikasi_lainnya' => null,
                    'url_akses' => $didukungAplikasi ? fake()->randomElement([
                        'https://layanan.' . strtolower(str_replace(' ', '', $user->nama_opd)) . '.go.id',
                        'https://portal.' . strtolower(str_replace(' ', '', $user->nama_opd)) . '.go.id',
                        'https://app.' . strtolower(str_replace(' ', '', $user->nama_opd)) . '.go.id'
                    ]) : null,
                    'hasil_output_aplikasi' => $didukungAplikasi ? fake()->randomElement([
                        'Dokumen perizinan, Sertifikat, Laporan status',
                        'Dashboard monitoring, Grafik statistik, Alert notifikasi',
                        'Data export Excel/PDF, API response, Web service'
                    ]) : null,
                    'lokasi_penyimpanan' => fake()->randomElement([
                        'Server Lokal Data Center Pemda',
                        'Cloud Server (AWS/GCP/Azure)',
                        'Hybrid (Lokal + Cloud)',
                        'Data Center Provinsi'
                    ]),
                ]);
            }

            $this->command->info("  ✓ Triwulan {$triwulan} tahun {$tahun}: {$jumlahLayanan} layanan");
        }
    }
}
