<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Assets\Models\Asset;
use App\Modules\Assets\Models\AssetBreakdownRecord;
use App\Modules\Assets\Models\AssetCategory;
use App\Modules\Assets\Models\AssetMaintenanceRecord;
use App\Modules\Assets\Models\AssetMaintenanceSchedule;
use App\Modules\Assets\Models\AssetUsageRecord;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Site;
use App\Modules\Core\Models\Warehouse;
use App\Modules\Inventory\Models\InventoryProduct;
use App\Modules\Inventory\Models\InventoryProductCategory;
use App\Modules\Irrigation\Models\IrrigationEvent;
use App\Modules\Irrigation\Models\IrrigationZone;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\UsersPermissions\Models\OrganizationMembership;
use App\Modules\UsersPermissions\Models\Role;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Database\Seeders\CoreFoundationSeeder;
use Database\Seeders\UsersPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetsMaintenanceModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_assets_dashboard(): void
    {
        $this->get('/admin/assets')->assertRedirect(route('login'));
    }

    public function test_user_without_asset_permission_cannot_access_assets_dashboard(): void
    {
        $this->seedAccess();
        $user = $this->memberWithRole('farm-hand');

        $this->actingAs($user)->get(route('assets.dashboard'))->assertForbidden();
    }

    public function test_admin_can_access_assets_dashboard(): void
    {
        $this->actingAs($this->adminUser())->get(route('assets.dashboard'))->assertOk()->assertSee('Assets dashboard');
    }

    public function test_asset_permissions_are_seeded(): void
    {
        $this->seedAccess();

        foreach (['assets.view', 'assets.manage', 'asset-categories.view', 'asset-categories.create', 'asset-categories.update', 'asset-categories.deactivate', 'assets.create', 'assets.update', 'assets.deactivate', 'maintenance-schedules.view', 'maintenance-schedules.create', 'maintenance-schedules.update', 'maintenance-schedules.cancel', 'maintenance-records.view', 'maintenance-records.create', 'maintenance-records.update', 'breakdowns.view', 'breakdowns.create', 'breakdowns.update', 'breakdowns.resolve', 'asset-usage.view', 'asset-usage.create'] as $key) {
            $this->assertDatabaseHas('permissions', ['key' => $key]);
        }

        $manager = Role::where('key', 'farm-manager')->firstOrFail();
        $auditor = Role::where('key', 'auditor')->firstOrFail();
        $this->assertTrue($manager->permissions()->where('key', 'breakdowns.resolve')->exists());
        $this->assertTrue($auditor->permissions()->where('key', 'assets.view')->exists());
        $this->assertFalse($auditor->permissions()->where('key', 'assets.create')->exists());
    }

    public function test_asset_category_can_be_created_updated_and_deactivated(): void
    {
        [$organization] = $this->scope();
        $admin = $this->adminUser();

        $payload = ['organization_id' => $organization->id, 'name' => 'Pumps', 'slug' => 'pumps', 'status' => 'active'];
        $this->actingAs($admin)->post(route('assets.categories.store'), $payload)->assertRedirect(route('assets.categories.index'));
        $category = AssetCategory::where('slug', 'pumps')->firstOrFail();

        $this->actingAs($admin)->put(route('assets.categories.update', $category), $payload + ['name' => 'Farm Pumps'])->assertRedirect(route('assets.categories.show', $category));
        $this->actingAs($admin)->post(route('assets.categories.deactivate', $category))->assertRedirect(route('assets.categories.show', $category));
        $this->assertSame('inactive', $category->fresh()->status);
    }

    public function test_asset_can_be_created_updated_and_deactivated(): void
    {
        $admin = $this->adminUser();
        $payload = $this->assetPayload();

        $this->actingAs($admin)->post(route('assets.items.store'), $payload)->assertRedirect(route('assets.items.index'));
        $asset = Asset::where('asset_code', 'AST-TEST-001')->firstOrFail();
        $this->actingAs($admin)->put(route('assets.items.update', $asset), $payload + ['name' => 'Updated Pump'])->assertRedirect(route('assets.items.show', $asset));
        $this->actingAs($admin)->post(route('assets.items.deactivate', $asset))->assertRedirect(route('assets.items.show', $asset));
        $this->assertSame('inactive', $asset->fresh()->status);
    }

    public function test_asset_rejects_wrong_farm_location_worker_and_user(): void
    {
        [$organization, $farm] = $this->scope();
        $admin = $this->adminUser();
        $otherFarm = Farm::create(['organization_id' => $organization->id, 'name' => 'Other Farm', 'status' => 'active']);
        $otherField = Field::create(['organization_id' => $organization->id, 'farm_id' => $otherFarm->id, 'name' => 'Other Field', 'status' => 'active']);
        $otherSite = Site::create(['organization_id' => $organization->id, 'farm_id' => $otherFarm->id, 'name' => 'Other Site', 'status' => 'active']);
        $otherWarehouse = Warehouse::create(['organization_id' => $organization->id, 'farm_id' => $otherFarm->id, 'name' => 'Other Warehouse', 'status' => 'active']);
        $otherWorker = LabourWorker::create(['organization_id' => $organization->id, 'farm_id' => $otherFarm->id, 'worker_code' => 'OTH-W', 'name' => 'Other Worker', 'employment_type' => 'casual', 'primary_role' => 'mechanic', 'status' => 'active']);
        $outsider = User::create(['name' => 'Outsider', 'email' => 'asset-outsider@test.test', 'password' => 'password', 'status' => 'active']);

        $this->actingAs($admin)->post(route('assets.items.store'), array_merge($this->assetPayload('BAD-FIELD'), ['field_id' => $otherField->id]))->assertSessionHasErrors('field_id');
        $this->actingAs($admin)->post(route('assets.items.store'), array_merge($this->assetPayload('BAD-SITE'), ['site_id' => $otherSite->id]))->assertSessionHasErrors('site_id');
        $this->actingAs($admin)->post(route('assets.items.store'), array_merge($this->assetPayload('BAD-WH'), ['warehouse_id' => $otherWarehouse->id]))->assertSessionHasErrors('warehouse_id');
        $this->actingAs($admin)->post(route('assets.items.store'), array_merge($this->assetPayload('BAD-WORKER'), ['assigned_worker_id' => $otherWorker->id]))->assertSessionHasErrors('assigned_worker_id');
        $this->actingAs($admin)->post(route('assets.items.store'), array_merge($this->assetPayload('BAD-USER'), ['assigned_user_id' => $outsider->id]))->assertSessionHasErrors('assigned_user_id');
        $this->assertSame($farm->organization_id, $organization->id);
    }

    public function test_maintenance_schedule_can_be_created_cancelled_completed_and_rejects_wrong_scope(): void
    {
        $admin = $this->adminUser();
        $asset = $this->asset();
        $payload = $this->schedulePayload($asset);

        $this->actingAs($admin)->post(route('assets.maintenance-schedules.store'), $payload)->assertRedirect(route('assets.maintenance-schedules.index'));
        $schedule = AssetMaintenanceSchedule::firstOrFail();
        $this->actingAs($admin)->post(route('assets.maintenance-schedules.cancel', $schedule), ['cancellation_reason' => 'Rain'])->assertRedirect(route('assets.maintenance-schedules.show', $schedule));
        $this->assertSame('cancelled', $schedule->fresh()->status);
        $this->actingAs($admin)->post(route('assets.maintenance-schedules.complete', $schedule))->assertRedirect(route('assets.maintenance-schedules.show', $schedule));
        $this->assertSame('completed', $schedule->fresh()->status);

        $otherFarm = Farm::create(['organization_id' => $asset->organization_id, 'name' => 'Other Schedule Farm', 'status' => 'active']);
        $otherAsset = $this->asset($otherFarm, 'AST-OTHER-SCH');
        $otherTask = OpsTask::create(['organization_id' => $asset->organization_id, 'farm_id' => $otherFarm->id, 'task_number' => 'TASK-OTHER-SCH', 'title' => 'Other task', 'category' => 'maintenance', 'priority' => 'normal', 'status' => 'draft']);
        $otherWorker = LabourWorker::create(['organization_id' => $asset->organization_id, 'farm_id' => $otherFarm->id, 'worker_code' => 'OTH-SCH-W', 'name' => 'Other Worker', 'employment_type' => 'casual', 'primary_role' => 'mechanic', 'status' => 'active']);
        $otherTeam = LabourTeam::create(['organization_id' => $asset->organization_id, 'farm_id' => $otherFarm->id, 'name' => 'Other Team', 'code' => 'OTH-SCH-T', 'team_type' => 'maintenance', 'status' => 'active']);

        $this->actingAs($admin)->post(route('assets.maintenance-schedules.store'), array_merge($payload, ['asset_id' => $otherAsset->id]))->assertSessionHasErrors('asset_id');
        $this->actingAs($admin)->post(route('assets.maintenance-schedules.store'), array_merge($payload, ['related_task_id' => $otherTask->id]))->assertSessionHasErrors('related_task_id');
        $this->actingAs($admin)->post(route('assets.maintenance-schedules.store'), array_merge($payload, ['assigned_worker_id' => $otherWorker->id]))->assertSessionHasErrors('assigned_worker_id');
        $this->actingAs($admin)->post(route('assets.maintenance-schedules.store'), array_merge($payload, ['assigned_team_id' => $otherTeam->id]))->assertSessionHasErrors('assigned_team_id');
    }

    public function test_maintenance_record_can_be_created_and_rejects_product_from_other_organization(): void
    {
        $admin = $this->adminUser();
        $asset = $this->asset();
        $payload = $this->recordPayload($asset);
        $this->actingAs($admin)->post(route('assets.maintenance-records.store'), $payload)->assertRedirect();
        $this->assertSame(1, AssetMaintenanceRecord::count());
        $this->assertSame('2026-05-01', $asset->fresh()->last_service_date->toDateString());

        $otherOrg = Organization::create(['name' => 'Other Org', 'slug' => 'other-org', 'status' => 'active']);
        $otherCategory = InventoryProductCategory::create(['organization_id' => $otherOrg->id, 'name' => 'Other Parts', 'slug' => 'other-parts', 'status' => 'active']);
        $otherProduct = InventoryProduct::create(['organization_id' => $otherOrg->id, 'category_id' => $otherCategory->id, 'name' => 'Other Part', 'code' => 'OTHER-PART', 'product_type' => 'spare_part', 'unit_of_measure' => 'each', 'status' => 'active']);
        $this->actingAs($admin)->post(route('assets.maintenance-records.store'), array_merge($payload, ['product_id' => $otherProduct->id]))->assertSessionHasErrors('product_id');
    }

    public function test_breakdown_can_be_created_and_resolved(): void
    {
        $admin = $this->adminUser();
        $asset = $this->asset();

        $this->actingAs($admin)->post(route('assets.breakdowns.store'), $this->breakdownPayload($asset))->assertRedirect(route('assets.breakdowns.index'));
        $breakdown = AssetBreakdownRecord::firstOrFail();
        $this->assertSame('broken_down', $asset->fresh()->status);
        $this->actingAs($admin)->post(route('assets.breakdowns.resolve', $breakdown), ['resolution_notes' => 'Replaced fuse.'])->assertRedirect(route('assets.breakdowns.show', $breakdown));
        $this->assertSame('resolved', $breakdown->fresh()->status);
        $this->assertSame('active', $asset->fresh()->status);
    }

    public function test_usage_can_be_created_and_rejects_wrong_farm_links(): void
    {
        $admin = $this->adminUser();
        $asset = $this->asset();
        [$organization] = $this->scope();
        $otherFarm = Farm::create(['organization_id' => $organization->id, 'name' => 'Other Usage Farm', 'status' => 'active']);
        $otherTask = OpsTask::create(['organization_id' => $organization->id, 'farm_id' => $otherFarm->id, 'task_number' => 'TASK-OTHER-USAGE', 'title' => 'Other task', 'category' => 'general', 'priority' => 'normal', 'status' => 'draft']);
        $otherZone = IrrigationZone::create(['organization_id' => $organization->id, 'farm_id' => $otherFarm->id, 'name' => 'Other Zone', 'code' => 'OTHER-USAGE-ZONE', 'zone_type' => 'field_zone', 'irrigation_method' => 'drip', 'status' => 'active']);
        $otherEvent = IrrigationEvent::create(['organization_id' => $organization->id, 'farm_id' => $otherFarm->id, 'irrigation_zone_id' => $otherZone->id, 'event_number' => 'IRR-EVT-OTHER-USAGE', 'irrigation_date' => '2026-05-01', 'status' => 'recorded']);

        $this->actingAs($admin)->post(route('assets.usage.store'), $this->usagePayload($asset))->assertRedirect(route('assets.usage.index'));
        $this->assertSame(1, AssetUsageRecord::count());
        $this->actingAs($admin)->post(route('assets.usage.store'), array_merge($this->usagePayload($asset), ['related_task_id' => $otherTask->id]))->assertSessionHasErrors('related_task_id');
        $this->actingAs($admin)->post(route('assets.usage.store'), array_merge($this->usagePayload($asset), ['related_irrigation_event_id' => $otherEvent->id]))->assertSessionHasErrors('related_irrigation_event_id');
    }

    private function seedAccess(): void { $this->seed(CoreFoundationSeeder::class); $this->seed(UsersPermissionsSeeder::class); }
    private function adminUser(): User { $this->seedAccess(); return User::where('email', 'admin@smartshamba.test')->firstOrFail(); }
    private function scope(): array { $this->seedAccess(); $organization = Organization::firstOrFail(); $farm = Farm::where('organization_id', $organization->id)->firstOrFail(); return [$organization, $farm]; }

    private function memberWithRole(string $roleKey): User
    {
        $organization = Organization::firstOrFail();
        $role = Role::where('key', $roleKey)->firstOrFail();
        $user = User::create(['name' => 'Scoped Member', 'email' => $roleKey.'-assets@smartshamba.test', 'password' => 'password123', 'status' => 'active']);
        OrganizationMembership::create(['organization_id' => $organization->id, 'user_id' => $user->id, 'role_id' => $role->id, 'role_key' => $role->key, 'status' => 'active', 'joined_at' => now()]);
        return $user;
    }

    private function category(?Organization $organization = null): AssetCategory
    {
        [$defaultOrganization] = $this->scope();
        $organization ??= $defaultOrganization;
        return AssetCategory::firstOrCreate(['organization_id' => $organization->id, 'slug' => 'test-equipment'], ['name' => 'Test Equipment', 'status' => 'active']);
    }

    private function asset(?Farm $farm = null, string $code = 'AST-TEST-001'): Asset
    {
        [$organization, $defaultFarm] = $this->scope();
        $farm ??= $defaultFarm;
        return Asset::firstOrCreate(['farm_id' => $farm->id, 'asset_code' => $code], ['organization_id' => $organization->id, 'category_id' => $this->category($organization)->id, 'name' => 'Test Pump', 'asset_type' => 'pump', 'status' => 'active', 'condition_status' => 'good']);
    }

    private function assetPayload(string $code = 'AST-TEST-001'): array
    {
        [$organization, $farm] = $this->scope();
        return ['organization_id' => $organization->id, 'farm_id' => $farm->id, 'category_id' => $this->category($organization)->id, 'asset_code' => $code, 'name' => 'Test Pump', 'asset_type' => 'pump', 'status' => 'active', 'condition_status' => 'good'];
    }

    private function schedulePayload(Asset $asset): array
    {
        return ['organization_id' => $asset->organization_id, 'farm_id' => $asset->farm_id, 'asset_id' => $asset->id, 'maintenance_type' => 'routine_service', 'scheduled_date' => '2026-05-01', 'priority' => 'normal', 'status' => 'planned'];
    }

    private function recordPayload(Asset $asset): array
    {
        return ['organization_id' => $asset->organization_id, 'farm_id' => $asset->farm_id, 'asset_id' => $asset->id, 'maintenance_type' => 'cleaning', 'maintenance_date' => '2026-05-01', 'status' => 'recorded', 'work_done' => 'Cleaned and tested.', 'next_service_date' => '2026-06-01'];
    }

    private function breakdownPayload(Asset $asset): array
    {
        return ['organization_id' => $asset->organization_id, 'farm_id' => $asset->farm_id, 'asset_id' => $asset->id, 'breakdown_date' => '2026-05-01', 'issue_type' => 'mechanical_failure', 'severity' => 'high', 'status' => 'open', 'description' => 'Pump stopped.'];
    }

    private function usagePayload(Asset $asset): array
    {
        return ['organization_id' => $asset->organization_id, 'farm_id' => $asset->farm_id, 'asset_id' => $asset->id, 'usage_date' => '2026-05-01', 'usage_type' => 'general', 'duration_minutes' => 30];
    }
}
