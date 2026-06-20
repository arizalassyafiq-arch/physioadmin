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

    public function test_application_csp_header_is_not_sent(): void
    {
        $this->withoutVite()->get('/login')
            ->assertOk()
            ->assertHeaderMissing('Content-Security-Policy');
    }

    public function test_forgot_password_page_is_accessible(): void
    {
        $this->withoutVite()->get('/forgot-password')
            ->assertOk();
    }
}
