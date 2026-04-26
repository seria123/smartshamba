<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Permissions
        Permission::create(['name' => 'view dashboard']);
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'view crops']);
        Permission::create(['name' => 'delete crops']);

        // Roles
        $admin = Role::create(['name' => 'admin']);
        $farmer = Role::create(['name' => 'farmer']);
        $agronomist = Role::create(['name' => 'agronomist']);

        // Assign permissions
        $admin->givePermissionTo(Permission::all());

        $farmer->givePermissionTo(['view crops']);

        $agronomist->givePermissionTo([
            'view crops',
            'view dashboard'
        ]);
    }
}