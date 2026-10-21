<?php

namespace Database\Seeders;

use App\Models\Layanan;
use App\Models\User;
use Illuminate\Database\Seeder;

class LayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('Tidak ada user OPD yang ditemukan. Silakan jalankan UserOpdSeeder terlebih dahulu.');
            return;
        }

        $layananTemplates = [
            'DISKOMINFO' => [
                [
                    'nama_layanan' => 'Portal Data Terbuka Daerah',
                    'deskripsi_layanan' => 'Platform untuk publikasi data terbuka pemerintah daerah yang dapat diakses oleh masyarakat dan stakeholder.',
                    'didukung_aplikasi' => true,
                    'nama_aplikasi' => 'Open Data Portal',
                    'jenis_aplikasi' => 'Web',
                    'url_akses' => 'https://data.pemda.go.id',
                ],
                [
                    'nama_layanan' => 'Sistem Informasi Geografis Daerah',
                    'deskripsi_layanan' => 'Sistem pemetaan digital untuk mendukung perencanaan pembangunan dan pelayanan publik berbasis lokasi.',
                    'didukung_aplikasi' => true,
                    'nama_aplikasi' => 'SIG Daerah',
                    'jenis_aplikasi' => 'Web',
                    'url_akses' => 'https://sig.pemda.go.id',
                ],
            ],
            'DISDIK' => [
                [
                    'nama_layanan' => 'Sistem Informasi Manajemen Sekolah',
                    'deskripsi_layanan' => 'Platform terintegrasi untuk mengelola data siswa, guru, dan administrasi sekolah di tingkat daerah.',
                    'didukung_aplikasi' => true,
                    'nama_aplikasi' => 'SIMS Daerah',
                    'jenis_aplikasi' => 'Web',
                    'url_akses' => 'https://sims.disdik.pemda.go.id',
                ],
                [
                    'nama_layanan' => 'Portal Pendaftaran Sekolah Online',
                    'deskripsi_layanan' => 'Layanan pendaftaran siswa baru secara online untuk semua jenjang pendidikan.',
                    'didukung_aplikasi' => true,
                    'nama_aplikasi' => 'PPDB Online',
                    'jenis_aplikasi' => 'Web',
                    'url_akses' => 'https://ppdb.disdik.pemda.go.id',
                ],
            ],
            'DINKES' => [
                [
                    'nama_layanan' => 'Sistem Informasi Kesehatan Daerah',
                    'deskripsi_layanan' => 'Platform untuk monitoring dan evaluasi program kesehatan serta pelayanan kesehatan masyarakat.',
                    'didukung_aplikasi' => true,
                    'nama_aplikasi' => 'SIKDA',
                    'jenis_aplikasi' => 'Web',
                    'url_akses' => 'https://sikda.dinkes.pemda.go.id',
                ],
                [
                    'nama_layanan' => 'Aplikasi Monitoring Imunisasi',
                    'deskripsi_layanan' => 'Sistem untuk memantau cakupan imunisasi dan jadwal imunisasi anak di seluruh wilayah.',
                    'didukung_aplikasi' => true,
                    'nama_aplikasi' => 'e-Imunisasi',
                    'jenis_aplikasi' => 'Mobile',
                    'url_akses' => 'https://imunisasi.dinkes.pemda.go.id',
                ],
            ],
            'DPUPR' => [
                [
                    'nama_layanan' => 'Sistem Perizinan Mendirikan Bangunan',
                    'deskripsi_layanan' => 'Layanan perizinan IMB dan PBG secara online untuk mempermudah masyarakat dalam mengurus izin bangunan.',
                    'didukung_aplikasi' => true,
                    'nama_aplikasi' => 'e-IMB',
                    'jenis_aplikasi' => 'Web',
                    'url_akses' => 'https://imb.dpupr.pemda.go.id',
                ],
                [
                    'nama_layanan' => 'Monitoring Infrastruktur Jalan',
                    'deskripsi_layanan' => 'Sistem untuk memantau kondisi infrastruktur jalan dan perencanaan pemeliharaan.',
                    'didukung_aplikasi' => true,
                    'nama_aplikasi' => 'Road Management System',
                    'jenis_aplikasi' => 'Web',
                    'url_akses' => 'https://jalan.dpupr.pemda.go.id',
                ],
            ],
            'DINSOS' => [
                [
                    'nama_layanan' => 'Sistem Informasi Kesejahteraan Sosial',
                    'deskripsi_layanan' => 'Platform untuk mengelola data penerima bantuan sosial dan program kesejahteraan masyarakat.',
                    'didukung_aplikasi' => true,
                    'nama_aplikasi' => 'SIKS',
                    'jenis_aplikasi' => 'Web',
                    'url_akses' => 'https://siks.dinsos.pemda.go.id',
                ],
            ],
            'DISNAKER' => [
                [
                    'nama_layanan' => 'Portal Bursa Kerja Online',
                    'deskripsi_layanan' => 'Platform untuk menghubungkan pencari kerja dengan perusahaan yang membutuhkan tenaga kerja.',
                    'didukung_aplikasi' => true,
                    'nama_aplikasi' => 'Job Portal',
                    'jenis_aplikasi' => 'Web',
                    'url_akses' => 'https://bursakerja.disnaker.pemda.go.id',
                ],
            ],
            'BAPPEDA' => [
                [
                    'nama_layanan' => 'Sistem Perencanaan Pembangunan Daerah',
                    'deskripsi_layanan' => 'Platform untuk perencanaan, monitoring, dan evaluasi pembangunan daerah.',
                    'didukung_aplikasi' => true,
                    'nama_aplikasi' => 'e-Planning',
                    'jenis_aplikasi' => 'Web',
                    'url_akses' => 'https://planning.bappeda.pemda.go.id',
                ],
            ],
        ];

        foreach ($users as $user) {
            $templates = $layananTemplates[$user->opd_id] ?? [];

            if (empty($templates)) {
                // Buat layanan generik jika tidak ada template khusus
                $templates = [
                    [
                        'nama_layanan' => 'Sistem Informasi ' . $user->nama_opd,
                        'deskripsi_layanan' => 'Sistem informasi untuk mendukung operasional dan pelayanan ' . $user->nama_opd,
                        'didukung_aplikasi' => true,
                        'nama_aplikasi' => 'SI-' . $user->opd_id,
                        'jenis_aplikasi' => 'Web',
                        'url_akses' => 'https://si.' . strtolower($user->opd_id) . '.pemda.go.id',
                    ],
                ];
            }

            foreach ($templates as $template) {
                // Buat data untuk beberapa triwulan
                for ($triwulan = 1; $triwulan <= 4; $triwulan++) {
                    $tahun = $triwulan <= 2 ? 2024 : 2025;

                    Layanan::create([
                        'user_id' => $user->id,
                        'tahun' => $tahun,
                        'triwulan' => $triwulan,
                        'unit_pelaksana' => $user->nama_opd,
                        'nama_pic' => $user->name,
                        'email_pic' => $user->email,
                        'hp_telp_pic' => $user->no_hp,
                        'bidang_bagian' => 'Bidang Teknologi Informasi',
                        'status_layanan' => 'berjalan',
                        'nama_layanan' => $template['nama_layanan'],
                        'deskripsi_layanan' => $template['deskripsi_layanan'],
                        'didukung_aplikasi' => $template['didukung_aplikasi'],
                        'target_pengguna' => 'Publik/Masyarakat',
                        'kementerian_lembaga_terkait' => 'Kementerian Komunikasi dan Informatika',
                        'supplier_data' => 'Database Internal',
                        'data_masuk' => 'Data Master, Data Transaksi',
                        'data_hasil' => 'Laporan, Dashboard, API',
                        'keterbukaan_data' => 'terbatas',
                        'jenis_data' => 'Administratif',
                        'frekuensi_pembaruan' => 'Harian',
                        'terhubung_sistem_lain' => true,
                        'nama_aplikasi' => $template['nama_aplikasi'] ?? null,
                        'jenis_aplikasi' => $template['jenis_aplikasi'] ?? null,
                        'url_akses' => $template['url_akses'] ?? null,
                        'hasil_output_aplikasi' => 'Dashboard monitoring, Laporan, Notifikasi',
                        'lokasi_penyimpanan' => 'Server Lokal Data Center Pemda',
                    ]);
                }
            }

            $this->command->info("✓ Data layanan untuk {$user->nama_opd} berhasil dibuat");
        }

        $this->command->info('Semua data layanan berhasil dibuat!');
    }
}
