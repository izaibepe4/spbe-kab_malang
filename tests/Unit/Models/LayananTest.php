<?php

namespace Tests\Unit\Models;

use App\Models\Layanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LayananTest extends TestCase
{
    use RefreshDatabase;

    public function test_layanan_has_required_attributes(): void
    {
        $layanan = Layanan::factory()->create([
            'nama_layanan' => 'Test Layanan',
            'unit_pelaksana' => 'Dinas Test',
        ]);

        $this->assertEquals('Test Layanan', $layanan->nama_layanan);
        $this->assertEquals('Dinas Test', $layanan->unit_pelaksana);
    }

    public function test_didukung_aplikasi_boolean_values(): void
    {
        $layananYa = Layanan::factory()->withAplikasi()->create();
        $layananTidak = Layanan::factory()->withoutAplikasi()->create();

        $this->assertTrue($layananYa->didukung_aplikasi);
        $this->assertFalse($layananTidak->didukung_aplikasi);
    }

    public function test_layanan_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $layanan = Layanan::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $layanan->user);
        $this->assertEquals($user->id, $layanan->user->id);
    }
}
