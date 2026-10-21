<?php

namespace Tests\Unit;

use App\Models\Layanan;
use App\Models\User;
use App\Policies\LayananPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LayananPolicyTest extends TestCase
{
    use RefreshDatabase;

    private LayananPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new LayananPolicy;
    }

    public function test_admin_can_view_any_layanan(): void
    {
        $admin = \App\Models\Admin::factory()->make();

        $this->assertTrue($this->policy->viewAny($admin));
    }

    public function test_opd_user_can_view_any_layanan(): void
    {
        $opdUser = User::factory()->make();

        $this->assertTrue($this->policy->viewAny($opdUser));
    }

    public function test_admin_can_update_any_layanan(): void
    {
        $admin = \App\Models\Admin::factory()->make();
        $layanan = Layanan::factory()->make();

        $this->assertTrue($this->policy->update($admin, $layanan));
    }

    public function test_opd_user_can_only_update_their_own_layanan(): void
    {
        $opdUser = User::factory()->create();

        $ownLayanan = Layanan::factory()->make(['user_id' => $opdUser->id]);
        $otherLayanan = Layanan::factory()->make(['user_id' => 999]);

        $this->assertTrue($this->policy->update($opdUser, $ownLayanan));
        $this->assertFalse($this->policy->update($opdUser, $otherLayanan));
    }

    public function test_opd_user_can_only_delete_their_own_layanan(): void
    {
        $opdUser = User::factory()->create();

        $ownLayanan = Layanan::factory()->make(['user_id' => $opdUser->id]);
        $otherLayanan = Layanan::factory()->make(['user_id' => 999]);

        $this->assertTrue($this->policy->delete($opdUser, $ownLayanan));
        $this->assertFalse($this->policy->delete($opdUser, $otherLayanan));
    }
}
