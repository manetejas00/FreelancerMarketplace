<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Exception;

class AuthService
{
    public static function createUser(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public static function getRoleByName(string $roleName)
    {
        return Cache::rememberForever("role_{$roleName}", function () use ($roleName) {
            return Role::where('name', $roleName)->first();
        });
    }
}
