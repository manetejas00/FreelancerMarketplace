<?php
namespace App\Http\Controllers;

use App\Http\Requests\SaveFreelancerProfileRequest;
use App\Services\FreelancerProfileService;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(FreelancerProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * Store or update freelancer profile
     */
    public function saveFreelancerProfile(SaveFreelancerProfileRequest $request)
    {
        try {
            $data = $request->validated();
            $result = $this->profileService->saveProfile($data);

            return response()->json([
                'message' => 'Profile saved successfully',
                'profile' => $result['profile'],
                'name' => $result['name'],
                'role' => $result['role'],
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
            return response()->json($this->profileService->getProfile());
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

            return response()->json($this->profileService->listFreelancers());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch freelancers', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Get user details along with their bids
     */
    public function getUserDetailsApplicants($id)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $this->profileService->getUserDetails($id)
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
