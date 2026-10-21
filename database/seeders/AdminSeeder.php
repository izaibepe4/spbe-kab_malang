<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminData = [
            [
                'name' => 'Super Administrator',
                'email' => 'admin@example.com',
                'password' => 'password',
            ],
            [
                'name' => 'Administrator SPBE',
                'email' => 'spbe.admin@example.com',
                'password' => 'password',
            ],
            [
                'name' => 'Administrator Sistem',
                'email' => 'sistem.admin@example.com',
                'password' => 'password',
            ],
        ];

        foreach ($adminData as $admin) {
            Admin::firstOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'password' => Hash::make($admin['password']),
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command->info('Admin accounts berhasil dibuat!');
    }
}
