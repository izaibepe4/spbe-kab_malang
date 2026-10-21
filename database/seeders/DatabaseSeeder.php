<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            UserOpdSeeder::class,
            LayananSeeder::class,
            IndikatorSpbeSeeder::class,
        ]);

        $this->command->info('Semua data seeder berhasil dijalankan!');
        $this->command->info('');
        $this->command->info('Akun demo (password: password):');
        $this->command->info('Admin:');
        $this->command->info('   - admin@example.com');
        $this->command->info('   - spbe.admin@example.com');
        $this->command->info('   - sistem.admin@example.com');
        $this->command->info('');
        $this->command->info('User OPD:');
        $this->command->info('   - diskominfo@example.com (DISKOMINFO)');
        $this->command->info('   - disdik@example.com (DISDIK)');
        $this->command->info('   - dinkes@example.com (DINKES)');
        $this->command->info('   - dpupr@example.com (DPUPR)');
        $this->command->info('   - dinsos@example.com (DINSOS)');
        $this->command->info('   - disnaker@example.com (DISNAKER)');
        $this->command->info('   - diskopukm@example.com (DISKOPUKM)');
        $this->command->info('   - disperindag@example.com (DISPERINDAG)');
        $this->command->info('   - dispertanian@example.com (DISPERTANIAN)');
        $this->command->info('   - dispar@example.com (DISPAR)');
        $this->command->info('   - bappeda@example.com (BAPPEDA)');
        $this->command->info('   - bkd@example.com (BKD)');
    }
}
