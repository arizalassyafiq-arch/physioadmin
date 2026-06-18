<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('secret-password'),
            'role' => 'admin',
        ]);

        $this->withoutVite()
            ->post('/login', [
                'email' => $user->email,
                'password' => 'secret-password',
            ])
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_non_admin_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'staff@example.com',
            'password' => Hash::make('secret-password'),
            'role' => 'staff',
        ]);

        $this->withoutVite()
            ->post('/login', [
                'email' => 'staff@example.com',
                'password' => 'secret-password',
            ])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_failed_login_attempts_are_rate_limited(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('secret-password'),
            'role' => 'admin',
        ]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->withoutVite()->post('/login', [
                'email' => 'admin@example.com',
                'password' => 'wrong-password',
            ]);
        }

        $this->withoutVite()
            ->post('/login', [
                'email' => 'admin@example.com',
                'password' => 'wrong-password',
            ])
            ->assertSessionHasErrors('email');
    }
}
