<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition()
    {
        $categories = ["Web Development", "Design", "Marketing", "Writing"];

        // Get a random user who has the 'client' role
        $client = User::whereHas('roles', function ($query) {
            $query->where('name', 'client');
        })->inRandomOrder()->first();

        // If no client exists, create one
        if (!$client) {
            $client = User::factory()->create()->assignRole('client');
        }

        return [
            'client_id' => $client->id,
            'title' => $this->faker->jobTitle,
            'description' => $this->faker->paragraph,
            'budget' => $this->faker->randomFloat(2, 100, 10000),
            'category' => $this->faker->randomElement($categories),
        ];
    }
}
