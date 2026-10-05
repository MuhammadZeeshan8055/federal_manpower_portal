<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_dashboard_design_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Good morning, Zeeshan.')
            ->assertSee('Federal Manpower Portal');
    }

    public function test_the_login_design_renders_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertOk()
            ->assertSee('Sign in to your account')
            ->assertSee('People. Possibility.');
    }
}
