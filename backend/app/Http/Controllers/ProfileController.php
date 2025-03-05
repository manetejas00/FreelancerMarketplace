<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FreelancerProfile;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255', // Validate name separately
            'skills' => 'required|string',
            'experience' => 'nullable|integer|min:0|max:100',
            'portfolio' => 'nullable|string',
            'hourly_rate' => 'required|numeric|min:0',
            'company_name' => 'nullable|string|max:255',
            'project_details' => 'nullable|string',
            'working_developers_count' => 'nullable|integer|min:0',
        ]);

        try {
            $user = Auth::user();
            $user->update(['name' => $validatedData['name']]); // Update name in users table

            $profile = FreelancerProfile::updateOrCreate(
                ['user_id' => $user->id],
                array_merge($validatedData, ['user_id' => $user->id])
            );

            return response()->json([
                'message' => 'Profile saved successfully',
                'profile' => $profile,
                'name' => $user->name,
                'role' => $user->getRoleNames()->first(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to save profile', 'details' => $e->getMessage()], 500);
        }
    }


    public function show()
    {
        $user = Auth::user(); // Get authenticated user

        $profile = FreelancerProfile::firstOrCreate(
            ['user_id' => $user->id], // Find existing profile
            [
                'skills' => '',
                'experience' => 0, // Ensure experience is an integer
                'portfolio' => '',
                'hourly_rate' => 0,
                'company_name' => '',
                'project_details' => '',
                'working_developers_count' => 0,
            ]
        );

        // Merge user's name and role into the profile object
        $profile->name = $user->name;
        $profile->role = $user->getRoleNames()->first();

        return response()->json($profile);
    }


    public function listFreelancers()
{
    $user = Auth::user();

    // Ensure only clients can access this
    if (!$user->hasRole('client')) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // Fetch all freelancers with their profiles, roles, and applied jobs
    $freelancers = FreelancerProfile::with(['user:id,name', 'jobs'])
        ->get()
        ->map(function ($freelancer) {
            return [
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
                'applied_jobs' => $freelancer->jobs->map(function ($job) {
                    return [
                        'title' => $job->title,
                    ];
                })
            ];
        });

    return response()->json($freelancers);
}


}
