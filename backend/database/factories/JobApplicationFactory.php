<?php

namespace Database\Factories;

use App\Models\JobApplication;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class JobApplicationFactory extends Factory
{
    protected $model = JobApplication::class;

    public function definition()
    {
        DB::table('job_applications')->truncate();
        return [
            'job_id' => Job::factory(),
            'freelancer_id' => User::factory(),
            'cover_letter' => $this->faker->paragraph,
            'status' => $this->faker->randomElement(['pending', 'accepted', 'rejected']),
        ];
    }
}
