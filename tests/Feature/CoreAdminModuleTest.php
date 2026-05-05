<?php

namespace Tests\Feature;

use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\ModuleRegistry;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Paddock;
use App\Modules\Core\Models\Site;
use App\Modules\Core\Models\Warehouse;
use Database\Seeders\CoreFoundationSeeder;
use Database\Seeders\UsersPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreAdminModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_core_dashboard_loads(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin);
        $this->get(route('core.dashboard'))
            ->assertOk()
            ->assertSee('Core dashboard');
    }

    public function test_core_relationships_work(): void
    {
        [$organization, $farm, $site, $field, $paddock, $warehouse] = $this->coreRecords();

        $this->assertTrue($organization->farms->contains($farm));
        $this->assertTrue($organization->sites->contains($site));
        $this->assertTrue($organization->fields->contains($field));
        $this->assertTrue($organization->paddocks->contains($paddock));
        $this->assertTrue($organization->warehouses->contains($warehouse));

        $this->assertTrue($farm->sites->contains($site));
        $this->assertTrue($farm->fields->contains($field));
        $this->assertTrue($farm->paddocks->contains($paddock));
        $this->assertTrue($farm->warehouses->contains($warehouse));

        $this->assertTrue($site->fields->contains($field));
        $this->assertTrue($site->paddocks->contains($paddock));
        $this->assertTrue($site->warehouses->contains($warehouse));
    }

    public function test_basic_list_pages_load(): void
    {
        $this->coreRecords();
        $admin = $this->adminUser();

        $this->actingAs($admin);

        foreach ([
            'core.organizations.index',
            'core.farms.index',
            'core.sites.index',
            'core.fields.index',
            'core.paddocks.index',
            'core.warehouses.index',
            'core.modules.index',
        ] as $route) {
            $this->get(route($route))->assertOk();
        }
    }

    public function test_module_registry_seeder_is_idempotent(): void
    {
        $this->seed(CoreFoundationSeeder::class);
        $this->seed(CoreFoundationSeeder::class);

        $this->assertSame(12, ModuleRegistry::count());
        $this->assertDatabaseHas('module_registry', [
            'key' => 'core',
            'status' => 'active',
        ]);
    }

    public function test_site_creation_rejects_missing_required_parent_records(): void
    {
        $this->actingAs($this->adminUser())
            ->post(route('core.sites.store'), [
            'organization_id' => 999,
            'farm_id' => 999,
            'name' => 'Invalid Site',
            'status' => 'active',
        ])->assertSessionHasErrors(['organization_id', 'farm_id']);
    }

    public function test_field_creation_rejects_missing_required_parent_records(): void
    {
        $this->actingAs($this->adminUser())
            ->post(route('core.fields.store'), [
            'name' => 'Invalid Field',
            'status' => 'active',
            'area_unit' => 'acres',
        ])->assertSessionHasErrors(['organization_id', 'farm_id']);
    }

    public function test_site_must_belong_to_selected_farm(): void
    {
        [$organization, $farm] = $this->coreRecords();
        $otherFarm = Farm::query()->create([
            'organization_id' => $organization->id,
            'name' => 'Other Farm',
            'status' => 'active',
        ]);
        $otherSite = Site::query()->create([
            'organization_id' => $organization->id,
            'farm_id' => $otherFarm->id,
            'name' => 'Other Site',
            'status' => 'active',
        ]);

        $this->actingAs($this->adminUser())
            ->post(route('core.fields.store'), [
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'site_id' => $otherSite->id,
            'name' => 'Invalid Field',
            'status' => 'active',
            'area_unit' => 'acres',
        ])->assertStatus(422);
    }

    private function coreRecords(): array
    {
        $organization = Organization::query()->create([
            'name' => 'Test Organization',
            'slug' => 'test-organization',
            'status' => 'active',
        ]);

        $farm = Farm::query()->create([
            'organization_id' => $organization->id,
            'name' => 'Test Farm',
            'code' => 'TF',
            'status' => 'active',
        ]);

        $site = Site::query()->create([
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'name' => 'Test Site',
            'status' => 'active',
        ]);

        $field = Field::query()->create([
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'site_id' => $site->id,
            'name' => 'Test Field',
            'area_unit' => 'acres',
            'status' => 'active',
        ]);

        $paddock = Paddock::query()->create([
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'site_id' => $site->id,
            'name' => 'Test Paddock',
            'area_unit' => 'acres',
            'status' => 'active',
        ]);

        $warehouse = Warehouse::query()->create([
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'site_id' => $site->id,
            'name' => 'Test Warehouse',
            'status' => 'active',
        ]);

        return [$organization->fresh(), $farm->fresh(), $site->fresh(), $field->fresh(), $paddock->fresh(), $warehouse->fresh()];
    }

    private function adminUser(): \App\Models\User
    {
        $this->seed(CoreFoundationSeeder::class);
        $this->seed(UsersPermissionsSeeder::class);

        return \App\Models\User::query()->where('email', 'admin@smartshamba.test')->firstOrFail();
    }
}
