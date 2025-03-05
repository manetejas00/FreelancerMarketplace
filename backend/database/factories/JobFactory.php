<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;


class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition()
    {
        $categories = ["Web Development", "Design", "Marketing", "Writing"];
        return [
            'client_id' => User::factory(),
            'title' => $this->faker->jobTitle,
            'description' => $this->faker->paragraph,
            'budget' => $this->faker->randomFloat(2, 100, 10000),
            'category' => $this->faker->randomElement($categories),
        ];
    }
}
