<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserOpdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $opdData = [
            [
                'name' => 'Operator DISKOMINFO',
                'email' => 'diskominfo@example.com',
                'nama_opd' => 'Dinas Komunikasi dan Informatika',
                'opd_id' => 'DISKOMINFO',
                'nip' => '198501012010011001',
                'no_hp' => '081234567890',
            ],
            [
                'name' => 'Operator DISDIK',
                'email' => 'disdik@example.com',
                'nama_opd' => 'Dinas Pendidikan',
                'opd_id' => 'DISDIK',
                'nip' => '198502022010012002',
                'no_hp' => '081234567891',
            ],
            [
                'name' => 'Operator DINKES',
                'email' => 'dinkes@example.com',
                'nama_opd' => 'Dinas Kesehatan',
                'opd_id' => 'DINKES',
                'nip' => '198503032010013003',
                'no_hp' => '081234567892',
            ],
            [
                'name' => 'Operator DPUPR',
                'email' => 'dpupr@example.com',
                'nama_opd' => 'Dinas Pekerjaan Umum dan Penataan Ruang',
                'opd_id' => 'DPUPR',
                'nip' => '198504042010014004',
                'no_hp' => '081234567893',
            ],
            [
                'name' => 'Operator DINSOS',
                'email' => 'dinsos@example.com',
                'nama_opd' => 'Dinas Sosial',
                'opd_id' => 'DINSOS',
                'nip' => '198505052010015005',
                'no_hp' => '081234567894',
            ],
            [
                'name' => 'Operator DISNAKER',
                'email' => 'disnaker@example.com',
                'nama_opd' => 'Dinas Tenaga Kerja',
                'opd_id' => 'DISNAKER',
                'nip' => '198506062010016006',
                'no_hp' => '081234567895',
            ],
            [
                'name' => 'Operator DISKOPUKM',
                'email' => 'diskopukm@example.com',
                'nama_opd' => 'Dinas Koperasi dan UKM',
                'opd_id' => 'DISKOPUKM',
                'nip' => '198507072010017007',
                'no_hp' => '081234567896',
            ],
            [
                'name' => 'Operator DISPERINDAG',
                'email' => 'disperindag@example.com',
                'nama_opd' => 'Dinas Perindustrian dan Perdagangan',
                'opd_id' => 'DISPERINDAG',
                'nip' => '198508082010018008',
                'no_hp' => '081234567897',
            ],
            [
                'name' => 'Operator DISPERTANIAN',
                'email' => 'dispertanian@example.com',
                'nama_opd' => 'Dinas Pertanian',
                'opd_id' => 'DISPERTANIAN',
                'nip' => '198509092010019009',
                'no_hp' => '081234567898',
            ],
            [
                'name' => 'Operator DISPAR',
                'email' => 'dispar@example.com',
                'nama_opd' => 'Dinas Pariwisata',
                'opd_id' => 'DISPAR',
                'nip' => '198510102010020010',
                'no_hp' => '081234567899',
            ],
            [
                'name' => 'Operator BAPPEDA',
                'email' => 'bappeda@example.com',
                'nama_opd' => 'Badan Perencanaan Pembangunan Daerah',
                'opd_id' => 'BAPPEDA',
                'nip' => '198511112010021011',
                'no_hp' => '081234567800',
            ],
            [
                'name' => 'Operator BKD',
                'email' => 'bkd@example.com',
                'nama_opd' => 'Badan Kepegawaian Daerah',
                'opd_id' => 'BKD',
                'nip' => '198512122010022012',
                'no_hp' => '081234567801',
            ],
        ];

        foreach ($opdData as $opd) {
            User::firstOrCreate(
                ['email' => $opd['email']],
                [
                    'name' => $opd['name'],
                    'password' => Hash::make('password'),
                    'nama_opd' => $opd['nama_opd'],
                    'opd_id' => $opd['opd_id'],
                    'nip' => $opd['nip'],
                    'no_hp' => $opd['no_hp'],
                    'role' => 'opd',
                    'is_approved' => true,
                    'approval_status' => 'approved',
                    'approved_at' => now(),
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command->info('User OPD berhasil dibuat!');
    }
}
