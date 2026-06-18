<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_settings_with_logout_action(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Klinik',
            'email' => 'admin@example.com',
        ]);

        $this->actingAs($admin)
            ->withoutVite()
            ->get(route('settings'))
            ->assertOk()
            ->assertSee('Pengaturan Akun')
            ->assertSee('Admin Klinik')
            ->assertSee('admin@example.com')
            ->assertSee('Keluar dari Sistem');
    }

    public function test_admin_can_logout_from_settings_action(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->withoutVite()
            ->post(route('logout'))
            ->assertRedirect('/login');

        $this->assertGuest();
    }
}
