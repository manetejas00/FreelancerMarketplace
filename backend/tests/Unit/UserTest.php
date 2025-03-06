<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    /** @test */
    public function it_creates_a_user()
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);
    }
    /** @test */
    public function it_requires_a_name_to_register()
    {
        $response = $this->post('/register', [
            'name' => 'client',
            'email' => 'client@example.com',
            'password' => 'password'
        ]);

        $response->assertSessionHasErrors('name'); // Checks validation error
    }

    /** @test */
public function it_fetches_users()
{
    $response = $this->getJson('/api/users');

    $response->assertStatus(200)
             ->assertJsonStructure([
                 '*' => ['id', 'name', 'email']
             ]);
}

}
