<?php
namespace App\Services;

use App\Models\FreelancerProfile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class FreelancerProfileService
{
    public function saveProfile(array $data)
    {
        $user = Auth::user();
        $user->update(['name' => $data['name']]);

        $profile = FreelancerProfile::updateOrCreate(
            ['user_id' => $user->id],
            array_merge($data, ['user_id' => $user->id])
        );

        // Clear the cached profile
        Cache::forget("freelancer_profile_{$user->id}");

        return [
            'profile' => $profile,
            'name' => $user->name,
            'role' => $user->getRoleNames()->first(),
        ];
    }

    public function getProfile()
    {
        $user = Auth::user();
        return Cache::remember("freelancer_profile_{$user->id}", 60 * 10, function () use ($user) {
            return FreelancerProfile::firstOrNew(
                ['user_id' => $user->id],
                [
                    'skills' => '',
                    'experience' => 0,
                    'portfolio' => '',
                    'hourly_rate' => 0,
                    'company_name' => '',
                    'project_details' => '',
                    'working_developers_count' => 0,
                ]
            )->setAttribute('name', $user->name)
             ->setAttribute('role', $user->getRoleNames()->first());
        });
    }

    public function listFreelancers()
    {
        return Cache::remember('freelancers_list', 60 * 10, function () {
            return FreelancerProfile::whereHas('user', function ($query) {
                $query->role('freelancer');
            })->with(['user:id,name', 'jobs'])
                ->get()
                ->map(fn($freelancer) => [
                    'id' => $freelancer->id,
                    'name' => $freelancer->user->name,
                    'role' => $freelancer->user->getRoleNames()->first(),
                    'skills' => $freelancer->skills,
                    'experience' => $freelancer->experience,
                    'hourly_rate' => $freelancer->hourly_rate,
                    'portfolio' => $freelancer->portfolio,
                    'company_name' => $freelancer->company_name,
                    'project_details' => $freelancer->project_details,
                    'working_developers_count' => $freelancer->working_developers_count,
                    'applied_jobs' => $freelancer->jobs->map(fn($job) => ['title' => $job->title])
                ]);
        });
    }

    public function getUserDetails($id)
    {
        return Cache::remember("user_details_$id", 60 * 10, function () use ($id) {
            return User::with('bids:id,user_id,job_id,rate')->findOrFail($id);
        });
    }
}
