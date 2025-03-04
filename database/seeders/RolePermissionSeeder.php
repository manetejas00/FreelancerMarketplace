<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'create profile',
            'edit profile',
            'delete profile',
            'view profile',
            'apply for job'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $freelancer = Role::firstOrCreate(['name' => 'freelancer']);
        $freelancer->givePermissionTo(['view profile', 'apply for job']);

        $client = Role::firstOrCreate(['name' => 'client']);
        $client->givePermissionTo(['create profile', 'edit profile', 'delete profile', 'view profile']);

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());
    }
}
