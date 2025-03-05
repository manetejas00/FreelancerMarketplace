<?php

namespace Database\Seeders;
use App\Models\FreelancerProfile;
use App\Models\Job;
use App\Models\JobApplication;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        FreelancerProfile::factory(10)->create();
        Job::factory(5)->create();
        JobApplication::factory(10)->create();
    }
}
