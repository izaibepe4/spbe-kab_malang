<?php

namespace Database\Factories;

use App\Models\Layanan;
use Illuminate\Database\Eloquent\Factories\Factory;

class LayananFactory extends Factory
{
    protected $model = Layanan::class;

    public function definition(): array
    {
        $didukungAplikasi = fake()->boolean(70); // 70% ya, 30% tidak

        return [
            'user_id' => \App\Models\User::factory(),
            'tahun' => fake()->year(),
            'unit_pelaksana' => fake()->randomElement([
                'Dinas Komunikasi dan Informatika',
                'Dinas Pendidikan',
                'Dinas Kesehatan',
                'Dinas Perhubungan',
                'Bappeda',
            ]),
            'nama_pic' => fake()->name(),
            'email_pic' => fake()->safeEmail(),
            'hp_telp_pic' => fake()->phoneNumber(),
            'bidang_bagian' => fake()->randomElement(['Bidang TI', 'Bidang Pelayanan', 'Bidang Administrasi']),
            'status_layanan' => fake()->randomElement(['berjalan', 'direncanakan', 'dihentikan']),
            'nama_layanan' => fake()->sentence(3),
            'deskripsi_layanan' => fake()->paragraph(),
            'didukung_aplikasi' => $didukungAplikasi,
            'target_pengguna' => fake()->randomElement(['Publik/Masyarakat', 'Internal Pemerintahan']),
            'kementerian_lembaga_terkait' => fake()->optional()->company(),
            'supplier_data' => fake()->optional()->company(),
            'data_masuk' => fake()->optional()->words(3, true),
            'data_hasil' => fake()->optional()->words(3, true),
            'keterbukaan_data' => fake()->optional()->randomElement(['terbuka', 'terbatas', 'tertutup']),
            'jenis_data' => fake()->optional()->words(2, true),
            'frekuensi_pembaruan' => fake()->optional()->randomElement([
                'Realtime',
                'Harian',
                'Mingguan',
                'Bulanan',
                'Tiga Bulanan',
                'Enam Bulanan',
                'Tahunan',
                'Dua Tahunan',
                'Tiga Tahunan',
                'Lima Tahunan',
            ]),
            'terhubung_sistem_lain' => fake()->boolean(),
            'nama_aplikasi' => $didukungAplikasi ? fake()->words(2, true) : null,
            'jenis_aplikasi' => $didukungAplikasi ? fake()->randomElement(['Web', 'Mobile', 'Desktop']) : null,
            'jenis_aplikasi_lainnya' => null,
            'url_akses' => $didukungAplikasi ? fake()->url() : null,
            'hasil_output_aplikasi' => $didukungAplikasi ? fake()->sentence() : null,
            'lokasi_penyimpanan' => fake()->optional()->randomElement(['Server Lokal', 'Cloud', 'Hybrid']),
        ];
    }

    public function withAplikasi(): static
    {
        return $this->state(fn (array $attributes) => [
            'didukung_aplikasi' => true,
            'nama_aplikasi' => fake()->words(2, true),
            'jenis_aplikasi' => fake()->randomElement(['Web', 'Mobile', 'Desktop']),
            'url_akses' => fake()->url(),
            'hasil_output_aplikasi' => fake()->sentence(),
        ]);
    }

    public function withoutAplikasi(): static
    {
        return $this->state(fn (array $attributes) => [
            'didukung_aplikasi' => false,
            'nama_aplikasi' => null,
            'jenis_aplikasi' => null,
            'url_akses' => null,
            'hasil_output_aplikasi' => null,
        ]);
    }
}
