<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_root_redirects_to_login(): void
    {
        $this->withoutVite()->get('/')
            ->assertRedirect('/login');
    }

    public function test_login_page_is_accessible(): void
    {
        $this->withoutVite()->get('/login')
            ->assertOk();
    }

    public function test_security_headers_are_sent(): void
    {
        config(['app.url' => 'https://klinikphysio.my.id']);

        $this->withoutVite()->get('/login')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'same-origin')
            ->assertHeader(
                'Content-Security-Policy',
                "frame-ancestors 'none'; base-uri 'self'; form-action 'self' https://klinikphysio.my.id"
            );
    }

    public function test_forgot_password_page_is_accessible(): void
    {
        $this->withoutVite()->get('/forgot-password')
            ->assertOk();
    }
}
