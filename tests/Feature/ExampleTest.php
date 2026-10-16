<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/collie');

        $collieResponse = $this->get('/collie');
        $collieResponse->assertStatus(200);

        $sppbResponse = $this->get('/sppb');
        $sppbResponse->assertStatus(200);
    }
}
