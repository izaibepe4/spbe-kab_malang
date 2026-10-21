<?php

namespace Database\Seeders;

use App\Models\IndikatorSpbe;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IndikatorSpbeSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data default indikator SPBE
        $defaultIndikators = [
            [
                'kategori' => 'Kebijakan SPBE',
                'nilai' => 10,
            ],
            [
                'kategori' => 'Tata Kelola SPBE',
                'nilai' => 10,
            ],
            [
                'kategori' => 'Manajemen SPBE',
                'nilai' => 11,
            ],
            [
                'kategori' => 'Layanan SPBE',
                'nilai' => 16,
            ],
        ];

        foreach ($defaultIndikators as $indikator) {
            IndikatorSpbe::firstOrCreate(
                ['kategori' => $indikator['kategori']],
                ['nilai' => $indikator['nilai']]
            );
        }
    }
}
