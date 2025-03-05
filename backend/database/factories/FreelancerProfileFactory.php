<?php

namespace Database\Factories;

use App\Models\FreelancerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FreelancerProfileFactory extends Factory
{
    protected $model = FreelancerProfile::class;

    public function definition()
    {
        // Get a random user who has the 'freelancer' role
        $freelancer = User::whereHas('roles', function ($query) {
            $query->where('name', 'freelancer');
        })->inRandomOrder()->first();

        // If no freelancer exists, create one
        if (!$freelancer) {
            $freelancer = User::factory()->create()->assignRole('freelancer');
        }

        return [
            'user_id' => $freelancer->id,
            'skills' => $this->faker->word,
            'experience' => $this->faker->sentence,
            'portfolio' => $this->faker->url,
            'hourly_rate' => $this->faker->randomFloat(2, 10, 100),
            'company_name' => $this->faker->company,
            'project_details' => $this->faker->paragraph,
            'working_developers_count' => $this->faker->numberBetween(1, 10),
        ];
    }
}
