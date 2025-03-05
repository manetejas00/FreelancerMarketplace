<?php

namespace Database\Factories;

use App\Models\JobApplication;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobApplicationFactory extends Factory
{
    protected $model = JobApplication::class;

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
            'job_id' => Job::factory(),
            'freelancer_id' => $freelancer->id,
            'cover_letter' => $this->faker->paragraph,
            'status' => $this->faker->randomElement(['pending', 'accepted', 'rejected']),
        ];
    }
}
