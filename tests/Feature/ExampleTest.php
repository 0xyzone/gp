<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Unauthenticated access to '/' redirects to '/login' for Filament panel
        $response = $this->get('/');
        $response->assertRedirect('/login');

        // Customer display is publicly accessible
        $display = $this->get('/display');
        $display->assertStatus(200);
    }
}
