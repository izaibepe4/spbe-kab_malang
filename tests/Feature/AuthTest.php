<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Login
     */
    public function test_user_can_view_login_page(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'is_approved' => true,
            'nama_opd' => 'Dinas Pendidikan',
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user, 'web');
    }

    public function test_user_cannot_login_with_invalid_email(): void
    {
        $response = $this->post('/login', [
            'email' => 'invalid@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('web');
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'is_approved' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('web');
    }

    public function test_user_cannot_login_when_not_approved(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'is_approved' => false,
            'nama_opd' => 'Dinas Pendidikan',
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('web');
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create([
            'is_approved' => true,
        ]);

        $this->actingAs($user, 'web')
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest('web');
    }

    public function test_authenticated_user_redirected_from_login_page(): void
    {
        $user = User::factory()->create([
            'is_approved' => true,
        ]);

        $response = $this->actingAs($user, 'web')
            ->get('/login');

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test Register
     */
    public function test_user_can_view_register_page(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    public function test_user_can_register_successfully(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'nip' => '123456789012345678',
            'no_hp' => '081234567890',
            'nama_opd' => 'Dinas Pendidikan',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/register/success');
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
            'nama_opd' => 'Dinas Pendidikan',
            'is_approved' => false,
        ]);
    }

    public function test_user_cannot_register_with_duplicate_email(): void
    {
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'nip' => '123456789012345678',
            'no_hp' => '081234567890',
            'nama_opd' => 'Dinas Kesehatan',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_user_cannot_register_with_duplicate_opd(): void
    {
        User::factory()->create([
            'nama_opd' => 'Dinas Pendidikan',
        ]);

        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'nip' => '123456789012345678',
            'no_hp' => '081234567890',
            'nama_opd' => 'Dinas Pendidikan',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('nama_opd');
    }

    public function test_user_cannot_register_with_invalid_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'nip' => '123456789012345678',
            'no_hp' => '081234567890',
            'nama_opd' => 'Dinas Pendidikan',
            'password' => 'short', // Too short
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_user_cannot_register_with_password_containing_special_chars(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'nip' => '123456789012345678',
            'no_hp' => '081234567890',
            'nama_opd' => 'Dinas Pendidikan',
            'password' => 'password@123', // Contains special char
            'password_confirmation' => 'password@123',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_user_cannot_register_with_password_without_letters(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'nip' => '123456789012345678',
            'no_hp' => '081234567890',
            'nama_opd' => 'Dinas Pendidikan',
            'password' => '12345678', // Only numbers
            'password_confirmation' => '12345678',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_user_cannot_register_with_password_without_numbers(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'nip' => '123456789012345678',
            'no_hp' => '081234567890',
            'nama_opd' => 'Dinas Pendidikan',
            'password' => 'password', // Only letters
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_user_cannot_register_with_mismatched_passwords(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'nip' => '123456789012345678',
            'no_hp' => '081234567890',
            'nama_opd' => 'Dinas Pendidikan',
            'password' => 'password123',
            'password_confirmation' => 'password456',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_user_cannot_register_with_invalid_opd(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'nip' => '123456789012345678',
            'no_hp' => '081234567890',
            'nama_opd' => 'Invalid OPD Name',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('nama_opd');
    }

    public function test_user_cannot_register_with_missing_required_fields(): void
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => '',
            'nip' => '',
            'no_hp' => '',
            'nama_opd' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'nip', 'no_hp', 'nama_opd', 'password']);
    }

    public function test_authenticated_user_redirected_from_register_page(): void
    {
        $user = User::factory()->create([
            'is_approved' => true,
        ]);

        $response = $this->actingAs($user, 'web')
            ->get('/register');

        $response->assertRedirect('/dashboard');
    }

    /**
     * Test Password Reset (Lupa Kata Sandi)
     */
    public function test_user_can_view_password_reset_request_page(): void
    {
        $response = $this->get('/password/reset');

        $response->assertStatus(200);
    }

    public function test_user_can_request_password_reset(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'is_approved' => true,
        ]);

        $response = $this->post('/password/email', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('status');
    }

    public function test_user_cannot_request_password_reset_with_invalid_email(): void
    {
        $response = $this->post('/password/email', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('oldpassword123'),
            'is_approved' => true,
        ]);

        $token = Password::createToken($user);

        $response = $this->post('/password/reset', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('status');

        // Verify password was changed
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_user_cannot_reset_password_with_invalid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'is_approved' => true,
        ]);

        $response = $this->post('/password/reset', [
            'token' => 'invalid-token',
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_user_cannot_reset_password_with_mismatched_passwords(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'is_approved' => true,
        ]);

        $token = Password::createToken($user);

        $response = $this->post('/password/reset', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_user_cannot_reset_password_with_invalid_password_format(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'is_approved' => true,
        ]);

        $token = Password::createToken($user);

        $response = $this->post('/password/reset', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'short', // Too short
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_user_cannot_reset_password_with_special_characters(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'is_approved' => true,
        ]);

        $token = Password::createToken($user);

        $response = $this->post('/password/reset', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'password@123', // Contains special char
            'password_confirmation' => 'password@123',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_user_can_reset_password_when_not_approved(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'is_approved' => false,
        ]);

        $token = Password::createToken($user);

        $response = $this->post('/password/reset', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        // Should still work, but user won't be able to login until approved
        $response->assertRedirect('/login');
        $response->assertSessionHas('status');
    }
}
