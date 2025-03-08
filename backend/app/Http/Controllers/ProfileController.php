<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveFreelancerProfileRequest;
use App\Services\FreelancerProfileService;
use Illuminate\Support\Facades\Auth;
use App\Helpers\EncryptionHelper;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    use HasRoles;

    protected $profileService;

    public function __construct(FreelancerProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * Store or update freelancer profile
     */
    public function saveFreelancerProfile(Request $request)
    {
        try {
            // 🔓 Decrypt the encrypted input
            $decryptedJson = EncryptionHelper::decodeId($request->input('encrypted'));
            $decryptedData = json_decode($decryptedJson, true);

            if (!is_array($decryptedData)) {
                return response()->json(['error' => 'Invalid decrypted data format'], 400);
            }

            // ✅ Validate decrypted data
            $validator = Validator::make($decryptedData, [
                'name' => 'required|string|max:255',
                'role' => 'required|string|max:255',
                'bio' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'details' => $validator->errors(),
                ], 422);
            }

            // ✅ Save profile
            $result = $this->profileService->saveProfile($validator->validated());

            return response()->json([
                'message' => 'Profile saved successfully',
                'profile' => $result['profile'],
                'name' => $result['name'],
                'role' => $result['role'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to save profile',
                'details' => $e->getMessage(),
            ], 500);
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
    public function getUserDetailsApplicants($encodedJobId)
    {
        try {
            $userId = EncryptionHelper::decodeId($encodedJobId);
            return response()->json([
                'success' => true,
                'data' => $this->profileService->getUserDetails($userId)
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
