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
        $user = \App\Models\User::first() ?? \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);

        $collieResponse = $this->actingAs($user)->get('/collie');
        $collieResponse->assertStatus(200);

        $sppbResponse = $this->actingAs($user)->get('/sppb');
        $sppbResponse->assertStatus(200);
    }
}
