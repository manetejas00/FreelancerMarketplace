<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FreelancerProfile;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Store or update freelancer profile
     */
    public function saveFreelancerProfile(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
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

    /**
     * Fetch freelancer profile
     */
    public function showFreelancerProfile()
    {
        try {
            $user = Auth::user();

            $profile = FreelancerProfile::firstOrNew(
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
            );

            $profile->name = $user->name;
            $profile->role = $user->getRoleNames()->first();

            return response()->json($profile);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch profile', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * List all freelancers (only accessible by clients)
     */
    public function listFreelancers()
    {
        try {
            $user = Auth::user();

            if (!$user->hasRole('client')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $freelancers = FreelancerProfile::whereHas('user', function ($query) {
                $query->role('freelancer');
            })->with(['user:id,name', 'jobs'])
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
                        'applied_jobs' => $freelancer->jobs->map(fn($job) => ['title' => $job->title])
                    ];
                });

            return response()->json($freelancers);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch freelancers', 'details' => $e->getMessage()], 500);
        }
    }

    public function getUserDetailsApplicants($id)
    {
        try {
            // Fetch user details along with their bids and bid amounts
            $user = User::with('bids:id,user_id,job_id,rate')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found or an error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
