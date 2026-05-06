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
            ['inventory.view', 'View Inventory', 'Inventory/Inputs'],
            ['inventory.manage', 'Manage Inventory', 'Inventory/Inputs'],
            ['products.view', 'View Products', 'Inventory/Inputs'],
            ['products.create', 'Create Products', 'Inventory/Inputs'],
            ['products.update', 'Update Products', 'Inventory/Inputs'],
            ['products.deactivate', 'Deactivate Products', 'Inventory/Inputs'],
            ['suppliers.view', 'View Suppliers', 'Inventory/Inputs'],
            ['suppliers.create', 'Create Suppliers', 'Inventory/Inputs'],
            ['suppliers.update', 'Update Suppliers', 'Inventory/Inputs'],
            ['suppliers.deactivate', 'Deactivate Suppliers', 'Inventory/Inputs'],
            ['stock.view', 'View Stock', 'Inventory/Inputs'],
            ['stock.receive', 'Receive Stock', 'Inventory/Inputs'],
            ['stock.issue', 'Issue Stock', 'Inventory/Inputs'],
            ['stock.transfer', 'Transfer Stock', 'Inventory/Inputs'],
            ['stock.adjust', 'Adjust Stock', 'Inventory/Inputs'],
            ['stock.manage', 'Manage Stock', 'Inventory/Inputs'],
            ['crops.view', 'View Crops', 'Crops'],
            ['crops.manage', 'Manage Crops', 'Crops'],
            ['crop-master.view', 'View Crop Master', 'Crops'],
            ['crop-master.create', 'Create Crop Master', 'Crops'],
            ['crop-master.update', 'Update Crop Master', 'Crops'],
            ['crop-master.deactivate', 'Deactivate Crop Master', 'Crops'],
            ['crop-seasons.view', 'View Crop Seasons', 'Crops'],
            ['crop-seasons.create', 'Create Crop Seasons', 'Crops'],
            ['crop-seasons.update', 'Update Crop Seasons', 'Crops'],
            ['crop-seasons.close', 'Close Crop Seasons', 'Crops'],
            ['crop-cycles.view', 'View Crop Cycles', 'Crops'],
            ['crop-cycles.create', 'Create Crop Cycles', 'Crops'],
            ['crop-cycles.update', 'Update Crop Cycles', 'Crops'],
            ['crop-cycles.close', 'Close Crop Cycles', 'Crops'],
            ['crop-activities.view', 'View Crop Activities', 'Crops'],
            ['crop-activities.create', 'Create Crop Activities', 'Crops'],
            ['crop-activities.update', 'Update Crop Activities', 'Crops'],
            ['crop-activities.approve', 'Approve Crop Activities', 'Crops'],
            ['crop-scouting.view', 'View Crop Scouting', 'Crops'],
            ['crop-scouting.create', 'Create Crop Scouting', 'Crops'],
            ['crop-treatments.view', 'View Crop Treatments', 'Crops'],
            ['crop-treatments.create', 'Create Crop Treatments', 'Crops'],
            ['crop-harvests.view', 'View Crop Harvests', 'Crops'],
            ['crop-harvests.create', 'Create Crop Harvests', 'Crops'],
            ['crop-losses.view', 'View Crop Losses', 'Crops'],
            ['crop-losses.create', 'Create Crop Losses', 'Crops'],
            ['livestock.view', 'View Livestock', 'Livestock'],
            ['livestock.manage', 'Manage Livestock', 'Livestock'],
            ['livestock-species.view', 'View Livestock Species', 'Livestock'],
            ['livestock-species.create', 'Create Livestock Species', 'Livestock'],
            ['livestock-species.update', 'Update Livestock Species', 'Livestock'],
            ['livestock-species.deactivate', 'Deactivate Livestock Species', 'Livestock'],
            ['livestock-animals.view', 'View Livestock Animals', 'Livestock'],
            ['livestock-animals.create', 'Create Livestock Animals', 'Livestock'],
            ['livestock-animals.update', 'Update Livestock Animals', 'Livestock'],
            ['livestock-animals.deactivate', 'Deactivate Livestock Animals', 'Livestock'],
            ['livestock-groups.view', 'View Livestock Groups', 'Livestock'],
            ['livestock-groups.create', 'Create Livestock Groups', 'Livestock'],
            ['livestock-groups.update', 'Update Livestock Groups', 'Livestock'],
            ['livestock-groups.deactivate', 'Deactivate Livestock Groups', 'Livestock'],
            ['livestock-health.view', 'View Livestock Health', 'Livestock'],
            ['livestock-health.create', 'Create Livestock Health Records', 'Livestock'],
            ['livestock-breeding.view', 'View Livestock Breeding', 'Livestock'],
            ['livestock-breeding.create', 'Create Livestock Breeding Records', 'Livestock'],
            ['livestock-births.view', 'View Livestock Births', 'Livestock'],
            ['livestock-births.create', 'Create Livestock Birth Records', 'Livestock'],
            ['livestock-feed.view', 'View Livestock Feed', 'Livestock'],
            ['livestock-feed.create', 'Create Livestock Feed Records', 'Livestock'],
            ['livestock-movements.view', 'View Livestock Movements', 'Livestock'],
            ['livestock-movements.create', 'Create Livestock Movement Records', 'Livestock'],
            ['livestock-mortality.view', 'View Livestock Mortality', 'Livestock'],
            ['livestock-mortality.create', 'Create Livestock Mortality Records', 'Livestock'],
            ['livestock-yields.view', 'View Livestock Yields', 'Livestock'],
            ['livestock-yields.create', 'Create Livestock Yield Records', 'Livestock'],
            ['livestock-withdrawals.view', 'View Livestock Withdrawals', 'Livestock'],
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
        $cropView = ['crops.view', 'crop-master.view', 'crop-seasons.view', 'crop-cycles.view', 'crop-activities.view', 'crop-scouting.view', 'crop-treatments.view', 'crop-harvests.view', 'crop-losses.view'];
        $cropOps = ['crops.view', 'crops.manage', 'crop-master.view', 'crop-master.create', 'crop-master.update', 'crop-master.deactivate', 'crop-seasons.view', 'crop-seasons.create', 'crop-seasons.update', 'crop-seasons.close', 'crop-cycles.view', 'crop-cycles.create', 'crop-cycles.update', 'crop-cycles.close', 'crop-activities.view', 'crop-activities.create', 'crop-activities.update', 'crop-activities.approve', 'crop-scouting.view', 'crop-scouting.create', 'crop-treatments.view', 'crop-treatments.create', 'crop-harvests.view', 'crop-harvests.create', 'crop-losses.view', 'crop-losses.create'];
        $agronomistCropOps = ['crops.view', 'crop-master.view', 'crop-seasons.view', 'crop-cycles.view', 'crop-cycles.create', 'crop-cycles.update', 'crop-activities.view', 'crop-activities.create', 'crop-scouting.view', 'crop-scouting.create', 'crop-treatments.view', 'crop-treatments.create', 'crop-harvests.view', 'crop-harvests.create', 'crop-losses.view', 'crop-losses.create'];
        $livestockView = ['livestock.view', 'livestock-species.view', 'livestock-animals.view', 'livestock-groups.view', 'livestock-health.view', 'livestock-breeding.view', 'livestock-births.view', 'livestock-feed.view', 'livestock-movements.view', 'livestock-mortality.view', 'livestock-yields.view', 'livestock-withdrawals.view'];
        $livestockOps = ['livestock.view', 'livestock.manage', 'livestock-species.view', 'livestock-species.create', 'livestock-species.update', 'livestock-species.deactivate', 'livestock-animals.view', 'livestock-animals.create', 'livestock-animals.update', 'livestock-animals.deactivate', 'livestock-groups.view', 'livestock-groups.create', 'livestock-groups.update', 'livestock-groups.deactivate', 'livestock-health.view', 'livestock-health.create', 'livestock-breeding.view', 'livestock-breeding.create', 'livestock-births.view', 'livestock-births.create', 'livestock-feed.view', 'livestock-feed.create', 'livestock-movements.view', 'livestock-movements.create', 'livestock-mortality.view', 'livestock-mortality.create', 'livestock-yields.view', 'livestock-yields.create', 'livestock-withdrawals.view'];
        $readOnly = collect($permissions)->only(array_merge(['core.view', 'access.view', 'modules.view', 'reports.view', 'workers.view', 'teams.view', 'attendance.view', 'tasks.view', 'work-orders.view', 'inventory.view', 'products.view', 'suppliers.view', 'stock.view'], $cropView, $livestockView))->pluck('id')->all();
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
        $inventoryManagers = collect($permissions)->only([
            'core.view', 'modules.view', 'reports.view',
            'inventory.view', 'inventory.manage',
            'products.view', 'products.create', 'products.update', 'products.deactivate',
            'suppliers.view', 'suppliers.create', 'suppliers.update', 'suppliers.deactivate',
            'stock.view', 'stock.receive', 'stock.issue', 'stock.transfer', 'stock.adjust', 'stock.manage',
        ])->pluck('id')->all();
        $inventoryOperators = collect($permissions)->only([
            'core.view', 'inventory.view',
            'products.view', 'products.create', 'products.update',
            'suppliers.view', 'suppliers.create', 'suppliers.update',
            'stock.view', 'stock.receive', 'stock.issue', 'stock.transfer', 'stock.adjust', 'stock.manage',
        ])->pluck('id')->all();

        $map = [
            'owner' => $all,
            'system-admin' => $all,
            'farm-manager' => collect($taskManagers)->merge($inventoryManagers)->merge(collect($permissions)->only($cropOps)->pluck('id')->all())->merge(collect($permissions)->only($livestockOps)->pluck('id')->all())->unique()->all(),
            'agronomist' => collect($taskOperators)->merge(collect($permissions)->only($agronomistCropOps)->pluck('id')->all())->unique()->all(),
            'livestock-officer' => collect($taskOperators)->merge(collect($permissions)->only($livestockOps)->pluck('id')->all())->unique()->all(),
            'storekeeper' => $inventoryOperators,
            'finance-officer' => collect($permissions)->only(['core.view', 'reports.view', 'reports.manage', 'inventory.view', 'products.view', 'suppliers.view', 'stock.view'])->pluck('id')->all(),
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
