<?php

namespace Database\Factories;

use App\Models\Bid;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BidFactory extends Factory
{
    protected $model = Bid::class;

    public function definition()
    {
        $freelancer = User::whereHas('roles', function ($query) {
            $query->where('name', 'freelancer');
        })
        ->whereBetween('id', [1, 4])
        ->inRandomOrder()
        ->first();

        // If no freelancer exists in that range, create one
        if (!$freelancer) {
            $freelancer = User::factory()->create(['role' => 'freelancer']);
        }
        return [
            'job_id' => Job::factory(), // Create a related job
            'user_id' => $freelancer->id,  // Only freelancers can bid
            'rate' => $this->faker->randomFloat(2, 10, 1000), // Random rate
            'cover_letter' => $this->faker->paragraph(),
            'status' => 'pending', // Default status
        ];
    }
}
