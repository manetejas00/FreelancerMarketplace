<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FreelancerProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Exception;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function registerUser(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'role' => 'required|in:client,freelancer',
            ], [
                'email.unique' => 'This email is already taken.',
                'password.min' => 'Password must be at least 6 characters.',
                'role.in' => 'Invalid role selection.',
            ]);

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Assign role
            $role = Role::where('name', $request->role)->first();
            if (!$role) {
                return response()->json(['error' => 'Role does not exist'], 400);
            }

            $user->assignRole($role);

            // If the user is a freelancer, create a freelancer profile
            if ($request->role === 'freelancer') {
                FreelancerProfile::create([
                    'user_id' => $user->id
                ]);
            }

            return response()->json([
                'message' => 'User registered successfully!',
                'user' => $user
            ], 201);
        } catch (Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            return response()->json(['error' => 'Registration failed. Please try again.'], 500);
        }
    }


    /**
     * Authenticate user and return token.
     */
    public function authenticateUser(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ], [
                'email.required' => 'Email is required.',
                'password.required' => 'Password is required.',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['Invalid credentials.'],
                ]);
            }

            return response()->json([
                'token' => $user->createToken('auth-token')->plainTextToken,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->getRoleNames()->first(),
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        } catch (Exception $e) {
            Log::error('Login failed: ' . $e->getMessage());
            return response()->json(['error' => 'Authentication failed. Please try again.'], 500);
        }
    }

    /**
     * Logout user and revoke token.
     */
    public function logoutUser(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        } catch (Exception $e) {
            Log::error('Logout failed: ' . $e->getMessage());
            return response()->json(['error' => 'Logout failed. Please try again.'], 500);
        }
    }
}
