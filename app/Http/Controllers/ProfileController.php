<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FreelancerProfile;
use App\Models\ClientProfile;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function getProfile()
    {
        $user = Auth::user();

        if ($user->hasRole('freelancer')) {
            $profile = FreelancerProfile::where('user_id', $user->id)->first();
        } elseif ($user->hasRole('client')) {
            $profile = ClientProfile::where('user_id', $user->id)->first();
        } else {
            return response()->json(['error' => 'Invalid role'], 403);
        }

        return response()->json(['user' => $user, 'profile' => $profile]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('freelancer')) {
            $profile = FreelancerProfile::updateOrCreate(
                ['user_id' => $user->id],
                $request->only(['skills', 'experience', 'portfolio', 'hourly_rate'])
            );
        } elseif ($user->hasRole('client')) {
            $profile = ClientProfile::updateOrCreate(
                ['user_id' => $user->id],
                $request->only(['company_name', 'projects', 'number_of_developers'])
            );
        } else {
            return response()->json(['error' => 'Invalid role'], 403);
        }

        return response()->json(['message' => 'Profile updated successfully', 'profile' => $profile]);
    }
}
