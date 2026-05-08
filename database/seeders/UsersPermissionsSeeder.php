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
            ['reports.analytics', 'View Reports Analytics', 'Reports'],
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
            ['irrigation.view', 'View Irrigation', 'Irrigation'],
            ['irrigation.manage', 'Manage Irrigation', 'Irrigation'],
            ['water-sources.view', 'View Water Sources', 'Irrigation'],
            ['water-sources.create', 'Create Water Sources', 'Irrigation'],
            ['water-sources.update', 'Update Water Sources', 'Irrigation'],
            ['water-sources.deactivate', 'Deactivate Water Sources', 'Irrigation'],
            ['irrigation-zones.view', 'View Irrigation Zones', 'Irrigation'],
            ['irrigation-zones.create', 'Create Irrigation Zones', 'Irrigation'],
            ['irrigation-zones.update', 'Update Irrigation Zones', 'Irrigation'],
            ['irrigation-zones.deactivate', 'Deactivate Irrigation Zones', 'Irrigation'],
            ['irrigation-schedules.view', 'View Irrigation Schedules', 'Irrigation'],
            ['irrigation-schedules.create', 'Create Irrigation Schedules', 'Irrigation'],
            ['irrigation-schedules.update', 'Update Irrigation Schedules', 'Irrigation'],
            ['irrigation-schedules.cancel', 'Cancel Irrigation Schedules', 'Irrigation'],
            ['irrigation-events.view', 'View Irrigation Events', 'Irrigation'],
            ['irrigation-events.create', 'Create Irrigation Events', 'Irrigation'],
            ['irrigation-events.update', 'Update Irrigation Events', 'Irrigation'],
            ['irrigation-events.cancel', 'Cancel Irrigation Events', 'Irrigation'],
            ['water-readings.view', 'View Water Readings', 'Irrigation'],
            ['water-readings.create', 'Create Water Readings', 'Irrigation'],
            ['irrigation-issues.view', 'View Irrigation Issues', 'Irrigation'],
            ['irrigation-issues.create', 'Create Irrigation Issues', 'Irrigation'],
            ['irrigation-issues.update', 'Update Irrigation Issues', 'Irrigation'],
            ['irrigation-issues.resolve', 'Resolve Irrigation Issues', 'Irrigation'],
            ['assets.view', 'View Assets and Maintenance', 'Assets / Maintenance'],
            ['assets.manage', 'Manage Assets and Maintenance', 'Assets / Maintenance'],
            ['asset-categories.view', 'View Asset Categories', 'Assets / Maintenance'],
            ['asset-categories.create', 'Create Asset Categories', 'Assets / Maintenance'],
            ['asset-categories.update', 'Update Asset Categories', 'Assets / Maintenance'],
            ['asset-categories.deactivate', 'Deactivate Asset Categories', 'Assets / Maintenance'],
            ['assets.create', 'Create Assets', 'Assets / Maintenance'],
            ['assets.update', 'Update Assets', 'Assets / Maintenance'],
            ['assets.deactivate', 'Deactivate Assets', 'Assets / Maintenance'],
            ['maintenance-schedules.view', 'View Maintenance Schedules', 'Assets / Maintenance'],
            ['maintenance-schedules.create', 'Create Maintenance Schedules', 'Assets / Maintenance'],
            ['maintenance-schedules.update', 'Update Maintenance Schedules', 'Assets / Maintenance'],
            ['maintenance-schedules.cancel', 'Cancel Maintenance Schedules', 'Assets / Maintenance'],
            ['maintenance-records.view', 'View Maintenance Records', 'Assets / Maintenance'],
            ['maintenance-records.create', 'Create Maintenance Records', 'Assets / Maintenance'],
            ['maintenance-records.update', 'Update Maintenance Records', 'Assets / Maintenance'],
            ['breakdowns.view', 'View Breakdowns', 'Assets / Maintenance'],
            ['breakdowns.create', 'Create Breakdowns', 'Assets / Maintenance'],
            ['breakdowns.update', 'Update Breakdowns', 'Assets / Maintenance'],
            ['breakdowns.resolve', 'Resolve Breakdowns', 'Assets / Maintenance'],
            ['asset-usage.view', 'View Asset Usage', 'Assets / Maintenance'],
            ['asset-usage.create', 'Create Asset Usage', 'Assets / Maintenance'],
            ['finance.view', 'View Finance Costing', 'Finance / Costing'],
            ['finance.manage', 'Manage Finance Costing', 'Finance / Costing'],
            ['finance.reports', 'View Finance Cost Reports', 'Finance / Costing'],
            ['sales.view', 'View Sales Revenue', 'Sales / Revenue'],
            ['sales.manage', 'Manage Sales Revenue', 'Sales / Revenue'],
            ['sales.reports', 'View Sales Revenue Reports', 'Sales / Revenue'],
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
        $irrigationView = ['irrigation.view', 'water-sources.view', 'irrigation-zones.view', 'irrigation-schedules.view', 'irrigation-events.view', 'water-readings.view', 'irrigation-issues.view'];
        $irrigationOps = ['irrigation.view', 'irrigation.manage', 'water-sources.view', 'water-sources.create', 'water-sources.update', 'water-sources.deactivate', 'irrigation-zones.view', 'irrigation-zones.create', 'irrigation-zones.update', 'irrigation-zones.deactivate', 'irrigation-schedules.view', 'irrigation-schedules.create', 'irrigation-schedules.update', 'irrigation-schedules.cancel', 'irrigation-events.view', 'irrigation-events.create', 'irrigation-events.update', 'irrigation-events.cancel', 'water-readings.view', 'water-readings.create', 'irrigation-issues.view', 'irrigation-issues.create', 'irrigation-issues.update', 'irrigation-issues.resolve'];
        $assetView = ['assets.view', 'asset-categories.view', 'maintenance-schedules.view', 'maintenance-records.view', 'breakdowns.view', 'asset-usage.view'];
        $assetOps = ['assets.view', 'assets.manage', 'asset-categories.view', 'asset-categories.create', 'asset-categories.update', 'asset-categories.deactivate', 'assets.create', 'assets.update', 'assets.deactivate', 'maintenance-schedules.view', 'maintenance-schedules.create', 'maintenance-schedules.update', 'maintenance-schedules.cancel', 'maintenance-records.view', 'maintenance-records.create', 'maintenance-records.update', 'breakdowns.view', 'breakdowns.create', 'breakdowns.update', 'breakdowns.resolve', 'asset-usage.view', 'asset-usage.create'];
        $assetLimitedView = ['assets.view', 'maintenance-records.view', 'breakdowns.view', 'asset-usage.view'];
        $financeView = ['finance.view', 'finance.reports'];
        $financeOps = ['finance.view', 'finance.manage', 'finance.reports'];
        $salesView = ['sales.view', 'sales.reports'];
        $salesOps = ['sales.view', 'sales.manage', 'sales.reports'];
        $readOnly = collect($permissions)->only(array_merge(['core.view', 'access.view', 'modules.view', 'reports.view', 'reports.analytics', 'workers.view', 'teams.view', 'attendance.view', 'tasks.view', 'work-orders.view', 'inventory.view', 'products.view', 'suppliers.view', 'stock.view'], $cropView, $livestockView, $irrigationView, $assetView, $financeView, $salesView))->pluck('id')->all();
        $coreOperators = collect($permissions)->only(['core.view', 'core.manage', 'modules.view', 'reports.view', 'reports.analytics'])->pluck('id')->all();
        $labourOperators = collect($permissions)->only([
            'core.view',
            'core.manage',
            'modules.view',
            'reports.view',
            'reports.analytics',
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
            'reports.analytics',
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
            'core.view', 'modules.view', 'reports.view', 'reports.analytics',
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
            'farm-manager' => collect($taskManagers)->merge($inventoryManagers)->merge(collect($permissions)->only($cropOps)->pluck('id')->all())->merge(collect($permissions)->only($livestockOps)->pluck('id')->all())->merge(collect($permissions)->only($irrigationOps)->pluck('id')->all())->merge(collect($permissions)->only($assetOps)->pluck('id')->all())->merge(collect($permissions)->only($financeOps)->pluck('id')->all())->merge(collect($permissions)->only($salesOps)->pluck('id')->all())->unique()->all(),
            'agronomist' => collect($taskOperators)->merge(collect($permissions)->only($agronomistCropOps)->pluck('id')->all())->merge(collect($permissions)->only($irrigationOps)->pluck('id')->all())->merge(collect($permissions)->only(['assets.view', 'asset-usage.view'])->pluck('id')->all())->unique()->all(),
            'livestock-officer' => collect($taskOperators)->merge(collect($permissions)->only($livestockOps)->pluck('id')->all())->merge(collect($permissions)->only(['assets.view', 'asset-usage.view'])->pluck('id')->all())->unique()->all(),
            'storekeeper' => collect($inventoryOperators)->merge(collect($permissions)->only($irrigationView)->pluck('id')->all())->merge(collect($permissions)->only($assetLimitedView)->pluck('id')->all())->unique()->all(),
            'finance-officer' => collect($permissions)->only(array_merge(['core.view', 'reports.view', 'reports.analytics', 'reports.manage', 'inventory.view', 'products.view', 'suppliers.view', 'stock.view'], $irrigationView, $financeOps, $salesOps))->pluck('id')->all(),
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
