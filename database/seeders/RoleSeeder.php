<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Permissions (firstOrCreate to avoid duplicates)
        Permission::firstOrCreate(['name' => 'view dashboard']);
        Permission::firstOrCreate(['name' => 'manage users']);
        Permission::firstOrCreate(['name' => 'view crops']);
        Permission::firstOrCreate(['name' => 'delete crops']);

        // Roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $farmer = Role::firstOrCreate(['name' => 'farmer']);
        $agronomist = Role::firstOrCreate(['name' => 'agronomist']);

        // Assign permissions to admin (give all)
        $admin->givePermissionTo(Permission::all());

        // Assign specific permissions to farmer
        $farmer->givePermissionTo(['view crops']);

        // Assign specific permissions to agronomist
        $agronomist->givePermissionTo([
            'view crops',
            'view dashboard',
        ]);
    }
}
