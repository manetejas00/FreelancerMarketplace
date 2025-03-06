<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /** @test */
    public function it_loads_the_homepage()
    {
        $response = $this->get('/'); // Simulate GET request

        $response->assertStatus(200); // Ensure response is OK
    }
}

