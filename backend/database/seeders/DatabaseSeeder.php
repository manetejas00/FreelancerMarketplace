<?php

namespace Database\Seeders;
use App\Models\FreelancerProfile;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use App\Models\Bid;

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
        $users = [
            ['name' => 'freelancer', 'email' => 'freelancer@example.com', 'role' => 'freelancer'],
            ['name' => 'freelancer 2', 'email' => 'freelancer2@example.com', 'role' => 'freelancer'],
            ['name' => 'client', 'email' => 'client@example.com', 'role' => 'client'],
            ['name' => 'client 2', 'email' => 'client2@example.com', 'role' => 'client'],
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
            $user->assignRole($userData['role']);
            if ($userData['role'] === 'freelancer') {
                FreelancerProfile::create([
                    'user_id' => $user->id
                ]);
            }
        }
        // FreelancerProfile::factory(10)->create();
        // Job::factory(5)->create();
        // JobApplication::factory(10)->create();
        // Bid::factory(10)->create();
    }
}
