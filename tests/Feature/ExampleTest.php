<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_public_homepage_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Your next chapter starts')
            ->assertSee('Latest Overseas Vacancies');
    }

    public function test_the_login_design_renders_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertOk()
            ->assertSee('Sign in to your account')
            ->assertSee('People. Possibility.');
    }

    public function test_the_public_website_pages_render_successfully(): void
    {
        $pages = [
            '/about-us' => 'People, preparation and responsible placement.',
            '/contact-us' => 'Send an enquiry',
            '/jobs' => 'Latest Overseas Vacancies',
            '/countries' => 'Popular recruitment destinations',
            '/services' => 'Support throughout the recruitment journey.',
            '/employers' => 'Register with us',
        ];

        foreach ($pages as $url => $text) {
            $this->get($url)->assertOk()->assertSee($text);
        }
    }
}
