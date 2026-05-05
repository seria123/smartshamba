<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Crops\Models\Crop;
use App\Modules\Crops\Models\CropCycle;
use App\Modules\Crops\Models\CropHarvestRecord;
use App\Modules\Crops\Models\CropLossRecord;
use App\Modules\Crops\Models\CropSeason;
use App\Modules\Crops\Models\CropTreatmentApplication;
use App\Modules\Crops\Models\CropVariety;
use App\Modules\Inventory\Models\InventoryProduct;
use App\Modules\Inventory\Models\InventoryProductCategory;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\UsersPermissions\Models\OrganizationMembership;
use App\Modules\UsersPermissions\Models\Role;
use Database\Seeders\CoreFoundationSeeder;
use Database\Seeders\UsersPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CropsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_crops_dashboard(): void
    {
        $this->get('/admin/crops')->assertRedirect(route('login'));
    }

    public function test_user_without_crop_permission_cannot_access_crops_dashboard(): void
    {
        $this->seedAccess();
        $user = $this->memberWithRole('farm-hand');

        $this->actingAs($user)->get(route('crops.dashboard'))->assertForbidden();
    }

    public function test_admin_can_access_crops_dashboard(): void
    {
        $this->actingAs($this->adminUser())->get(route('crops.dashboard'))->assertOk()->assertSee('Crops dashboard');
    }

    public function test_crop_permissions_are_seeded(): void
    {
        $this->seedAccess();

        foreach (['crops.view', 'crops.manage', 'crop-master.view', 'crop-master.create', 'crop-master.update', 'crop-master.deactivate', 'crop-seasons.view', 'crop-seasons.create', 'crop-seasons.update', 'crop-seasons.close', 'crop-cycles.view', 'crop-cycles.create', 'crop-cycles.update', 'crop-cycles.close', 'crop-activities.view', 'crop-activities.create', 'crop-activities.update', 'crop-activities.approve', 'crop-scouting.view', 'crop-scouting.create', 'crop-treatments.view', 'crop-treatments.create', 'crop-harvests.view', 'crop-harvests.create', 'crop-losses.view', 'crop-losses.create'] as $key) {
            $this->assertDatabaseHas('permissions', ['key' => $key]);
        }

        $farmManager = Role::query()->where('key', 'farm-manager')->firstOrFail();
        $auditor = Role::query()->where('key', 'auditor')->firstOrFail();
        $this->assertTrue($farmManager->permissions()->where('key', 'crop-cycles.close')->exists());
        $this->assertTrue($auditor->permissions()->where('key', 'crop-cycles.view')->exists());
        $this->assertFalse($auditor->permissions()->where('key', 'crop-cycles.create')->exists());
    }

    public function test_crop_can_be_created_updated_and_deactivated(): void
    {
        [$organization] = $this->scope();
        $admin = $this->adminUser();

        $this->actingAs($admin)->post(route('crops.crops.store'), ['organization_id' => $organization->id, 'name' => 'Maize', 'code' => 'MAIZE', 'crop_type' => 'cereal', 'status' => 'active'])->assertRedirect(route('crops.crops.index'));
        $crop = Crop::query()->where('code', 'MAIZE')->firstOrFail();

        $this->actingAs($admin)->put(route('crops.crops.update', $crop), ['organization_id' => $organization->id, 'name' => 'Updated Maize', 'code' => 'MAIZE', 'crop_type' => 'cereal', 'default_growing_days' => 120, 'status' => 'active'])->assertRedirect(route('crops.crops.show', $crop));
        $this->actingAs($admin)->post(route('crops.crops.deactivate', $crop))->assertRedirect(route('crops.crops.show', $crop));
        $this->assertSame('inactive', $crop->fresh()->status);
    }

    public function test_variety_can_be_created_and_must_belong_to_crop(): void
    {
        $admin = $this->adminUser();
        $crop = $this->crop('BEAN');
        $otherCrop = $this->crop('RICE');

        $this->actingAs($admin)->post(route('crops.varieties.store'), ['crop_id' => $crop->id, 'name' => 'Bean Variety', 'status' => 'active'])->assertRedirect(route('crops.varieties.index'));
        $variety = CropVariety::query()->where('name', 'Bean Variety')->firstOrFail();

        $this->actingAs($admin)->post(route('crops.cycles.store'), $this->cyclePayload($crop) + ['variety_id' => $variety->id])->assertRedirect();
        $this->actingAs($admin)->post(route('crops.cycles.store'), $this->cyclePayload($otherCrop) + ['variety_id' => $variety->id])->assertStatus(422);
    }

    public function test_season_can_be_created_and_closed(): void
    {
        [$organization, $farm] = $this->scope();
        $admin = $this->adminUser();

        $this->actingAs($admin)->post(route('crops.seasons.store'), ['organization_id' => $organization->id, 'farm_id' => $farm->id, 'name' => 'Long Rains', 'code' => 'LR-2026', 'start_date' => '2026-03-01', 'season_type' => 'main', 'status' => 'active'])->assertRedirect(route('crops.seasons.index'));
        $season = CropSeason::query()->where('code', 'LR-2026')->firstOrFail();

        $this->actingAs($admin)->post(route('crops.seasons.close', $season))->assertRedirect(route('crops.seasons.show', $season));
        $this->assertSame('closed', $season->fresh()->status);
    }

    public function test_crop_cycle_can_be_created_updated_closed_and_cancelled(): void
    {
        $admin = $this->adminUser();
        $crop = $this->crop('TOM');

        $this->actingAs($admin)->post(route('crops.cycles.store'), $this->cyclePayload($crop))->assertRedirect();
        $cycle = CropCycle::query()->where('name', 'Test Crop Cycle')->firstOrFail();

        $this->actingAs($admin)->put(route('crops.cycles.update', $cycle), array_merge($this->cyclePayload($crop), ['name' => 'Updated Cycle', 'status' => 'growing']))->assertRedirect(route('crops.cycles.show', $cycle));
        $this->assertSame('growing', $cycle->fresh()->status);

        $this->actingAs($admin)->post(route('crops.cycles.close', $cycle), ['closure_notes' => 'Done.'])->assertRedirect(route('crops.cycles.show', $cycle));
        $this->assertSame('closed', $cycle->fresh()->status);

        $cancelCycle = $this->cycle($crop, 'Cancel Cycle');
        $this->actingAs($admin)->post(route('crops.cycles.cancel', $cancelCycle), ['cancellation_reason' => 'Flooded.'])->assertRedirect(route('crops.cycles.show', $cancelCycle));
        $this->assertSame('cancelled', $cancelCycle->fresh()->status);
    }

    public function test_crop_cycle_rejects_field_from_another_farm_and_variety_from_another_crop(): void
    {
        [$organization, $farm] = $this->scope();
        $admin = $this->adminUser();
        $crop = $this->crop('FIELDTEST');
        $otherCrop = $this->crop('OTHERVAR');
        $variety = CropVariety::query()->create(['crop_id' => $otherCrop->id, 'name' => 'Other Variety', 'status' => 'active']);
        $otherFarm = Farm::query()->create(['organization_id' => $organization->id, 'name' => 'Other Farm', 'status' => 'active']);
        $otherField = Field::query()->create(['organization_id' => $organization->id, 'farm_id' => $otherFarm->id, 'name' => 'Other Field', 'status' => 'active']);

        $this->actingAs($admin)->post(route('crops.cycles.store'), array_merge($this->cyclePayload($crop, $farm), ['field_id' => $otherField->id]))->assertStatus(422);
        $this->actingAs($admin)->post(route('crops.cycles.store'), array_merge($this->cyclePayload($crop, $farm), ['variety_id' => $variety->id]))->assertStatus(422);
    }

    public function test_activity_rejects_related_task_from_another_farm(): void
    {
        [$organization, $farm] = $this->scope();
        $admin = $this->adminUser();
        $cycle = $this->cycle($this->crop('ACT'));
        $otherFarm = Farm::query()->create(['organization_id' => $organization->id, 'name' => 'Other Task Farm', 'status' => 'active']);
        $task = OpsTask::query()->create(['organization_id' => $organization->id, 'farm_id' => $otherFarm->id, 'task_number' => 'TASK-CROP-OTHER', 'title' => 'Other task', 'category' => 'crop', 'priority' => 'normal', 'status' => 'draft']);

        $this->actingAs($admin)->post(route('crops.cycles.activities.store', $cycle), ['task_id' => $task->id, 'activity_type' => 'planting', 'activity_date' => '2026-05-01'])->assertStatus(422);
        $this->assertSame($farm->id, $cycle->farm_id);
    }

    public function test_activity_treatment_harvest_and_loss_records_work_with_scope_rules(): void
    {
        [$organization] = $this->scope();
        $admin = $this->adminUser();
        $cycle = $this->cycle($this->crop('REC'));
        $product = $this->product($organization, 'FERT-CROP');

        $this->actingAs($admin)->post(route('crops.cycles.activities.store', $cycle), ['activity_type' => 'planting', 'activity_date' => '2026-05-01', 'seed_quantity' => 4, 'seed_unit' => 'kg'])->assertRedirect(route('crops.cycles.show', $cycle));
        $this->assertDatabaseHas('crop_activities', ['crop_cycle_id' => $cycle->id, 'activity_type' => 'planting']);

        $this->actingAs($admin)->post(route('crops.cycles.treatments.store', $cycle), ['product_id' => $product->id, 'application_type' => 'fertilizer', 'application_date' => '2026-05-10', 'quantity_used' => 10, 'quantity_unit' => 'kg'])->assertRedirect(route('crops.cycles.show', $cycle));
        $this->assertSame('FERT-CROP', CropTreatmentApplication::query()->firstOrFail()->product_code_snapshot);

        $otherOrg = Organization::query()->create(['name' => 'Other Org', 'slug' => 'other-org', 'status' => 'active']);
        $otherProduct = $this->product($otherOrg, 'OTHER-PROD');
        $this->actingAs($admin)->post(route('crops.cycles.treatments.store', $cycle), ['product_id' => $otherProduct->id, 'application_type' => 'spray', 'application_date' => '2026-05-11'])->assertStatus(422);

        $this->actingAs($admin)->post(route('crops.cycles.harvests.store', $cycle), ['harvest_date' => '2026-07-01', 'quantity' => 0, 'unit' => 'kg'])->assertSessionHasErrors('quantity');
        $this->actingAs($admin)->post(route('crops.cycles.harvests.store', $cycle), ['harvest_date' => '2026-07-01', 'quantity' => 100, 'unit' => 'kg'])->assertRedirect(route('crops.cycles.show', $cycle));
        $this->actingAs($admin)->post(route('crops.cycles.losses.store', $cycle), ['loss_date' => '2026-07-02', 'loss_type' => 'field_loss', 'estimated_quantity' => 3])->assertRedirect(route('crops.cycles.show', $cycle));

        $this->assertSame(1, CropHarvestRecord::query()->where('crop_cycle_id', $cycle->id)->count());
        $this->assertSame(1, CropLossRecord::query()->where('crop_cycle_id', $cycle->id)->count());
    }

    private function seedAccess(): void { $this->seed(CoreFoundationSeeder::class); $this->seed(UsersPermissionsSeeder::class); }
    private function adminUser(): User { $this->seedAccess(); return User::query()->where('email', 'admin@smartshamba.test')->firstOrFail(); }
    private function scope(): array { $this->seedAccess(); return [Organization::query()->firstOrFail(), Farm::query()->firstOrFail(), Field::query()->firstOrFail()]; }

    private function memberWithRole(string $roleKey): User
    {
        $organization = Organization::query()->firstOrFail();
        $role = Role::query()->where('key', $roleKey)->firstOrFail();
        $user = User::query()->create(['name' => 'Scoped Member', 'email' => $roleKey.'@smartshamba.test', 'password' => 'password123', 'status' => 'active']);
        OrganizationMembership::query()->create(['organization_id' => $organization->id, 'user_id' => $user->id, 'role_id' => $role->id, 'role_key' => $role->key, 'status' => 'active', 'joined_at' => now()]);

        return $user;
    }

    private function crop(string $code): Crop
    {
        return Crop::query()->firstOrCreate(['code' => $code, 'organization_id' => null], ['name' => 'Crop '.$code, 'crop_type' => 'test', 'status' => 'active']);
    }

    private function cyclePayload(Crop $crop, ?Farm $farm = null): array
    {
        [$organization, $defaultFarm, $field] = $this->scope();
        $farm ??= $defaultFarm;
        $field = Field::query()->where('farm_id', $farm->id)->first() ?? $field;

        return ['organization_id' => $organization->id, 'farm_id' => $farm->id, 'field_id' => $field->id, 'crop_id' => $crop->id, 'name' => 'Test Crop Cycle', 'status' => 'planned'];
    }

    private function cycle(Crop $crop, string $name = 'Direct Cycle'): CropCycle
    {
        return CropCycle::query()->create($this->cyclePayload($crop) + ['cycle_number' => 'CYCLE-TEST-'.uniqid(), 'name' => $name]);
    }

    private function product(Organization $organization, string $code): InventoryProduct
    {
        $category = InventoryProductCategory::query()->firstOrCreate(['organization_id' => $organization->id, 'slug' => 'crop-inputs'], ['name' => 'Crop Inputs', 'status' => 'active']);

        return InventoryProduct::query()->create(['organization_id' => $organization->id, 'category_id' => $category->id, 'name' => 'Product '.$code, 'code' => $code, 'product_type' => 'input', 'unit_of_measure' => 'kg', 'status' => 'active']);
    }
}
