<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Services\AuthService;
use App\Models\FreelancerProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Exception;
use App\Models\User;


class AuthController extends Controller
{
    public function registerUser(RegisterUserRequest $request)
    {
        try {
            $user = AuthService::createUser($request->all());

            $role = AuthService::getRoleByName($request->role);
            if (!$role) return response()->json(['error' => 'Role does not exist'], 400);

            $user->assignRole($role);

            if ($request->role === 'freelancer') {
                FreelancerProfile::create(['user_id' => $user->id]);
            }

            return response()->json(['message' => 'User registered!', 'user' => $user], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Registration failed.'], 500);
        }
    }

    public function authenticateUser(LoginUserRequest $request)
    {
        try {
            $email = $request->email;
            $ip = $request->ip();
            $key = "login_attempts:{$ip}";

            if (RateLimiter::tooManyAttempts($key, 5)) {
                return response()->json(['error' => 'Too many login attempts.'], 429);
            }

            $user = Cache::remember("user_{$email}", 600, fn() => User::where('email', $email)->with('roles')->first());

            if (!$user || !Hash::check($request->password, $user->password)) {
                RateLimiter::hit($key, 60);
                throw ValidationException::withMessages(['email' => ['Invalid credentials.']]);
            }

            RateLimiter::clear($key);

            return response()->json([
                'token' => $user->createToken('auth-token')->plainTextToken,
                'user' => ['id' => $user->id, 'name' => $user->name, 'role' => optional($user->roles->first())->name],
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    public function logoutUser(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
