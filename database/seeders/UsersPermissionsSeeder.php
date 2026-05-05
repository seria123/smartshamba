<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\UsersPermissions\Models\OrganizationMembership;
use App\Modules\UsersPermissions\Models\Permission;
use App\Modules\UsersPermissions\Models\Role;
use Illuminate\Database\Seeder;

class UsersPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = $this->seedPermissions();
        $roles = $this->seedRoles();
        $this->syncRolePermissions($roles, $permissions);
        $this->seedDemoAdmin($roles['system-admin']);
    }

    private function seedPermissions(): array
    {
        $definitions = [
            ['core.view', 'View Core Admin', 'Core/Admin'],
            ['core.manage', 'Manage Core Admin', 'Core/Admin'],
            ['access.view', 'View Users and Permissions', 'Users/Permissions'],
            ['access.manage', 'Manage Users and Permissions', 'Users/Permissions'],
            ['modules.view', 'View Module Registry', 'Modules'],
            ['modules.manage', 'Manage Module Registry', 'Modules'],
            ['reports.view', 'View Reports', 'Reports'],
            ['reports.manage', 'Manage Reports', 'Reports'],
            ['workers.view', 'View Workers', 'Workers/Labour'],
            ['workers.create', 'Create Workers', 'Workers/Labour'],
            ['workers.update', 'Update Workers', 'Workers/Labour'],
            ['workers.deactivate', 'Deactivate Workers', 'Workers/Labour'],
            ['workers.manage', 'Manage Workers', 'Workers/Labour'],
            ['teams.view', 'View Labour Teams', 'Workers/Labour'],
            ['teams.create', 'Create Labour Teams', 'Workers/Labour'],
            ['teams.update', 'Update Labour Teams', 'Workers/Labour'],
            ['teams.deactivate', 'Deactivate Labour Teams', 'Workers/Labour'],
            ['attendance.view', 'View Labour Attendance', 'Workers/Labour'],
            ['attendance.record', 'Record Labour Attendance', 'Workers/Labour'],
            ['attendance.update', 'Update Labour Attendance', 'Workers/Labour'],
            ['tasks.view', 'View Tasks', 'Tasks/Work Orders'],
            ['tasks.create', 'Create Tasks', 'Tasks/Work Orders'],
            ['tasks.update', 'Update Tasks', 'Tasks/Work Orders'],
            ['tasks.assign', 'Assign Tasks', 'Tasks/Work Orders'],
            ['tasks.submit', 'Submit Tasks', 'Tasks/Work Orders'],
            ['tasks.approve', 'Approve Tasks', 'Tasks/Work Orders'],
            ['tasks.cancel', 'Cancel Tasks', 'Tasks/Work Orders'],
            ['tasks.manage', 'Manage Tasks', 'Tasks/Work Orders'],
            ['work-orders.view', 'View Work Orders', 'Tasks/Work Orders'],
            ['work-orders.create', 'Create Work Orders', 'Tasks/Work Orders'],
            ['work-orders.update', 'Update Work Orders', 'Tasks/Work Orders'],
            ['work-orders.cancel', 'Cancel Work Orders', 'Tasks/Work Orders'],
            ['work-orders.manage', 'Manage Work Orders', 'Tasks/Work Orders'],
        ];

        $permissions = [];
        foreach ($definitions as [$key, $name, $group]) {
            $permissions[$key] = Permission::query()->updateOrCreate(
                ['key' => $key],
                [
                    'name' => $name,
                    'group' => $group,
                    'description' => $name,
                ],
            );
        }

        return $permissions;
    }

    private function seedRoles(): array
    {
        $definitions = [
            'owner' => 'Owner',
            'system-admin' => 'System Admin',
            'farm-manager' => 'Farm Manager',
            'agronomist' => 'Agronomist',
            'livestock-officer' => 'Livestock Officer',
            'storekeeper' => 'Storekeeper',
            'finance-officer' => 'Finance Officer',
            'farm-hand' => 'Farm Hand',
            'contractor' => 'Contractor',
            'auditor' => 'Auditor',
        ];

        $roles = [];
        foreach ($definitions as $key => $name) {
            $roles[$key] = Role::query()->updateOrCreate(
                ['key' => $key],
                [
                    'name' => $name,
                    'description' => $name.' role',
                    'is_system' => true,
                ],
            );
        }

        return $roles;
    }

    private function syncRolePermissions(array $roles, array $permissions): void
    {
        $all = collect($permissions)->pluck('id')->all();
        $readOnly = collect($permissions)->only(['core.view', 'access.view', 'modules.view', 'reports.view', 'workers.view', 'teams.view', 'attendance.view', 'tasks.view', 'work-orders.view'])->pluck('id')->all();
        $coreOperators = collect($permissions)->only(['core.view', 'core.manage', 'modules.view', 'reports.view'])->pluck('id')->all();
        $labourOperators = collect($permissions)->only([
            'core.view',
            'core.manage',
            'modules.view',
            'reports.view',
            'workers.view',
            'workers.create',
            'workers.update',
            'workers.deactivate',
            'workers.manage',
            'teams.view',
            'teams.create',
            'teams.update',
            'teams.deactivate',
            'attendance.view',
            'attendance.record',
            'attendance.update',
        ])->pluck('id')->all();
        $taskManagers = collect($permissions)->only([
            'core.view',
            'core.manage',
            'modules.view',
            'reports.view',
            'workers.view',
            'workers.create',
            'workers.update',
            'workers.deactivate',
            'workers.manage',
            'teams.view',
            'teams.create',
            'teams.update',
            'teams.deactivate',
            'attendance.view',
            'attendance.record',
            'attendance.update',
            'workers.view',
            'teams.view',
            'attendance.view',
            'tasks.view',
            'tasks.create',
            'tasks.update',
            'tasks.assign',
            'tasks.submit',
            'tasks.approve',
            'tasks.cancel',
            'tasks.manage',
            'work-orders.view',
            'work-orders.create',
            'work-orders.update',
            'work-orders.cancel',
            'work-orders.manage',
        ])->pluck('id')->all();
        $taskOperators = collect($permissions)->only([
            'core.view',
            'access.view',
            'modules.view',
            'reports.view',
            'workers.view',
            'teams.view',
            'attendance.view',
            'tasks.view',
            'tasks.create',
            'tasks.update',
            'work-orders.view',
        ])->pluck('id')->all();
        $taskSubmitters = collect($permissions)->only([
            'core.view',
            'tasks.view',
            'tasks.submit',
        ])->pluck('id')->all();

        $map = [
            'owner' => $all,
            'system-admin' => $all,
            'farm-manager' => $taskManagers,
            'agronomist' => $taskOperators,
            'livestock-officer' => $taskOperators,
            'storekeeper' => $readOnly,
            'finance-officer' => collect($permissions)->only(['core.view', 'reports.view', 'reports.manage'])->pluck('id')->all(),
            'farm-hand' => $taskSubmitters,
            'contractor' => $taskSubmitters,
            'auditor' => $readOnly,
        ];

        foreach ($map as $roleKey => $permissionIds) {
            $roles[$roleKey]->permissions()->sync($permissionIds);
        }
    }

    private function seedDemoAdmin(Role $role): void
    {
        $organization = Organization::query()->first();
        $farm = Farm::query()->where('organization_id', $organization?->id)->first();

        if (! $organization) {
            return;
        }

        $user = User::query()->updateOrCreate(
            ['email' => 'admin@smartshamba.test'],
            [
                'name' => 'Demo System Admin',
                'password' => 'password',
                'status' => 'active',
                'email_verified_at' => now(),
            ],
        );

        OrganizationMembership::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'user_id' => $user->id,
            ],
            [
                'role_id' => $role->id,
                'role_key' => $role->key,
                'farm_id' => $farm?->id,
                'status' => 'active',
                'joined_at' => now(),
            ],
        );
    }
}
