<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Paddock;
use App\Modules\Core\Models\Warehouse;
use App\Modules\Inventory\Models\InventoryProduct;
use App\Modules\Inventory\Models\InventoryProductCategory;
use App\Modules\Inventory\Models\InventoryStockLot;
use App\Modules\Livestock\Models\LivestockAnimal;
use App\Modules\Livestock\Models\LivestockAnimalGroup;
use App\Modules\Livestock\Models\LivestockBreed;
use App\Modules\Livestock\Models\LivestockFeedRecord;
use App\Modules\Livestock\Models\LivestockMovementRecord;
use App\Modules\Livestock\Models\LivestockSpecies;
use App\Modules\Livestock\Models\LivestockTreatmentRecord;
use App\Modules\Livestock\Models\LivestockWithdrawalPeriod;
use App\Modules\Livestock\Models\LivestockYieldRecord;
use App\Modules\UsersPermissions\Models\OrganizationMembership;
use App\Modules\UsersPermissions\Models\Role;
use Database\Seeders\CoreFoundationSeeder;
use Database\Seeders\UsersPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LivestockModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_livestock_dashboard(): void
    {
        $this->get('/admin/livestock')->assertRedirect(route('login'));
    }

    public function test_user_without_livestock_permission_cannot_access_livestock_dashboard(): void
    {
        $this->seedAccess();
        $user = $this->memberWithRole('farm-hand');

        $this->actingAs($user)->get(route('livestock.dashboard'))->assertForbidden();
    }

    public function test_admin_can_access_livestock_dashboard(): void
    {
        $this->actingAs($this->adminUser())->get(route('livestock.dashboard'))->assertOk()->assertSee('Livestock dashboard');
    }

    public function test_livestock_permissions_are_seeded(): void
    {
        $this->seedAccess();

        foreach (['livestock.view', 'livestock.manage', 'livestock-species.view', 'livestock-species.create', 'livestock-species.update', 'livestock-species.deactivate', 'livestock-animals.view', 'livestock-animals.create', 'livestock-animals.update', 'livestock-animals.deactivate', 'livestock-groups.view', 'livestock-groups.create', 'livestock-groups.update', 'livestock-groups.deactivate', 'livestock-health.view', 'livestock-health.create', 'livestock-breeding.view', 'livestock-breeding.create', 'livestock-births.view', 'livestock-births.create', 'livestock-feed.view', 'livestock-feed.create', 'livestock-movements.view', 'livestock-movements.create', 'livestock-mortality.view', 'livestock-mortality.create', 'livestock-yields.view', 'livestock-yields.create', 'livestock-withdrawals.view'] as $key) {
            $this->assertDatabaseHas('permissions', ['key' => $key]);
        }

        $manager = Role::query()->where('key', 'farm-manager')->firstOrFail();
        $officer = Role::query()->where('key', 'livestock-officer')->firstOrFail();
        $auditor = Role::query()->where('key', 'auditor')->firstOrFail();
        $this->assertTrue($manager->permissions()->where('key', 'livestock-health.create')->exists());
        $this->assertTrue($officer->permissions()->where('key', 'livestock-yields.create')->exists());
        $this->assertTrue($auditor->permissions()->where('key', 'livestock-yields.view')->exists());
        $this->assertFalse($auditor->permissions()->where('key', 'livestock-yields.create')->exists());
    }

    public function test_species_can_be_created_updated_and_deactivated(): void
    {
        [$organization] = $this->scope();
        $admin = $this->adminUser();

        $this->actingAs($admin)->post(route('livestock.species.store'), ['organization_id' => $organization->id, 'name' => 'Camel', 'code' => 'CAMEL', 'species_type' => 'mammal', 'status' => 'active'])->assertRedirect(route('livestock.species.index'));
        $species = LivestockSpecies::query()->where('code', 'CAMEL')->firstOrFail();
        $this->actingAs($admin)->put(route('livestock.species.update', $species), ['organization_id' => $organization->id, 'name' => 'Camel Updated', 'code' => 'CAMEL', 'species_type' => 'mammal', 'status' => 'active'])->assertRedirect(route('livestock.species.show', $species));
        $this->actingAs($admin)->post(route('livestock.species.deactivate', $species))->assertRedirect(route('livestock.species.show', $species));

        $this->assertSame('inactive', $species->fresh()->status);
    }

    public function test_breed_can_be_created_and_must_belong_to_species(): void
    {
        $admin = $this->adminUser();
        $cattle = $this->species('CATTLE');
        $goat = $this->species('GOAT');
        $breed = LivestockBreed::query()->create(['species_id' => $cattle->id, 'name' => 'Friesian', 'code' => 'FR', 'status' => 'active']);

        $this->actingAs($admin)->post(route('livestock.breeds.store'), ['species_id' => $goat->id, 'name' => 'Boer', 'code' => 'BOER', 'status' => 'active'])->assertRedirect(route('livestock.breeds.index'));
        $this->actingAs($admin)->post(route('livestock.animals.store'), $this->animalPayload($goat) + ['breed_id' => $breed->id])->assertSessionHasErrors('breed_id');
    }

    public function test_animal_can_be_created_and_rejects_paddock_and_breed_scope_mismatches(): void
    {
        [$organization, $farm] = $this->scope();
        $admin = $this->adminUser();
        $species = $this->species('CATTLE');
        $breed = LivestockBreed::query()->create(['species_id' => $species->id, 'name' => 'Sahiwal', 'status' => 'active']);
        $otherFarm = Farm::query()->create(['organization_id' => $organization->id, 'name' => 'Other Farm', 'status' => 'active']);
        $otherPaddock = Paddock::query()->create(['organization_id' => $organization->id, 'farm_id' => $otherFarm->id, 'name' => 'Other Paddock', 'status' => 'active']);

        $this->actingAs($admin)->post(route('livestock.animals.store'), $this->animalPayload($species, $farm) + ['breed_id' => $breed->id])->assertRedirect(route('livestock.animals.index'));
        $this->actingAs($admin)->post(route('livestock.animals.store'), array_merge($this->animalPayload($species, $farm, 'BAD-PADDOCK'), ['paddock_id' => $otherPaddock->id]))->assertSessionHasErrors('paddock_id');

        $otherSpecies = $this->species('GOAT');
        $this->actingAs($admin)->post(route('livestock.animals.store'), $this->animalPayload($otherSpecies, $farm, 'BAD-BREED') + ['breed_id' => $breed->id])->assertSessionHasErrors('breed_id');
    }

    public function test_animal_group_can_be_created_and_rejects_negative_counts(): void
    {
        $admin = $this->adminUser();
        $species = $this->species('POULTRY');

        $this->actingAs($admin)->post(route('livestock.groups.store'), $this->groupPayload($species))->assertRedirect(route('livestock.groups.index'));
        $this->actingAs($admin)->post(route('livestock.groups.store'), array_merge($this->groupPayload($species, 'NEG'), ['current_count' => -1]))->assertSessionHasErrors('current_count');
    }

    public function test_treatment_product_scope_and_withdrawal_creation(): void
    {
        [$organization] = $this->scope();
        $admin = $this->adminUser();
        $animal = $this->animal();
        $product = $this->product($organization, 'MED-1');
        $otherOrg = Organization::query()->create(['name' => 'Other Org', 'slug' => 'other-org', 'status' => 'active']);
        $otherProduct = $this->product($otherOrg, 'MED-OTHER');

        $this->actingAs($admin)->post(route('livestock.animals.treatments.store', $animal), ['treatment_type' => 'treatment', 'treatment_date' => '2026-05-01', 'product_id' => $product->id, 'withdrawal_meat_days' => 7, 'withdrawal_milk_days' => 3])->assertRedirect(route('livestock.animals.show', $animal));
        $this->assertSame(1, LivestockTreatmentRecord::query()->count());
        $this->assertSame(2, LivestockWithdrawalPeriod::query()->count());

        $this->actingAs($admin)->post(route('livestock.animals.treatments.store', $animal), ['treatment_type' => 'treatment', 'treatment_date' => '2026-05-02', 'product_id' => $otherProduct->id])->assertSessionHasErrors('product_id');
    }

    public function test_feed_can_reference_product_without_deducting_inventory(): void
    {
        [$organization, $farm] = $this->scope();
        $admin = $this->adminUser();
        $animal = $this->animal();
        $product = $this->product($organization, 'FEED-1');
        $warehouse = Warehouse::query()->firstOrCreate(['organization_id' => $organization->id, 'farm_id' => $farm->id, 'name' => 'Feed Store'], ['status' => 'active']);
        $lot = InventoryStockLot::query()->create(['organization_id' => $organization->id, 'farm_id' => $farm->id, 'warehouse_id' => $warehouse->id, 'product_id' => $product->id, 'lot_number' => 'FEED-LOT', 'quantity_on_hand' => 100, 'reserved_quantity' => 0, 'unit_of_measure' => 'kg', 'unit_cost' => 10, 'currency' => 'KES', 'status' => 'active']);

        $this->actingAs($admin)->post(route('livestock.animals.feed.store', $animal), ['feed_date' => '2026-05-01', 'product_id' => $product->id, 'quantity' => 15, 'quantity_unit' => 'kg'])->assertRedirect(route('livestock.animals.show', $animal));

        $this->assertSame(1, LivestockFeedRecord::query()->count());
        $this->assertSame('100.00', $lot->fresh()->quantity_on_hand);
    }

    public function test_movement_rejects_other_farm_paddock_and_updates_current_paddock(): void
    {
        [$organization, $farm] = $this->scope();
        $admin = $this->adminUser();
        $animal = $this->animal();
        $to = Paddock::query()->create(['organization_id' => $organization->id, 'farm_id' => $farm->id, 'name' => 'Calving Pen', 'status' => 'active']);
        $otherFarm = Farm::query()->create(['organization_id' => $organization->id, 'name' => 'Other Move Farm', 'status' => 'active']);
        $otherPaddock = Paddock::query()->create(['organization_id' => $organization->id, 'farm_id' => $otherFarm->id, 'name' => 'Other Move Paddock', 'status' => 'active']);

        $this->actingAs($admin)->post(route('livestock.animals.movements.store', $animal), ['movement_date' => '2026-05-02', 'to_paddock_id' => $otherPaddock->id])->assertSessionHasErrors('to_paddock_id');
        $this->actingAs($admin)->post(route('livestock.animals.movements.store', $animal), ['movement_date' => '2026-05-02', 'to_paddock_id' => $to->id])->assertRedirect(route('livestock.animals.show', $animal));

        $this->assertSame($to->id, $animal->fresh()->paddock_id);
        $this->assertSame(1, LivestockMovementRecord::query()->count());
    }

    public function test_mortality_updates_animal_status_and_group_count(): void
    {
        $admin = $this->adminUser();
        $animal = $this->animal();
        $group = $this->group(currentCount: 10);

        $this->actingAs($admin)->post(route('livestock.animals.mortality.store', $animal), ['mortality_date' => '2026-05-03'])->assertRedirect(route('livestock.animals.show', $animal));
        $this->assertSame('dead', $animal->fresh()->status);

        $this->actingAs($admin)->post(route('livestock.groups.mortality.store', $group), ['mortality_date' => '2026-05-03', 'number_dead' => 11])->assertSessionHasErrors('number_dead');
        $this->actingAs($admin)->post(route('livestock.groups.mortality.store', $group), ['mortality_date' => '2026-05-03', 'number_dead' => 4])->assertRedirect(route('livestock.groups.show', $group));
        $this->assertSame(6, $group->fresh()->current_count);
    }

    public function test_yield_quantity_must_be_greater_than_zero(): void
    {
        $admin = $this->adminUser();
        $animal = $this->animal();

        $this->actingAs($admin)->post(route('livestock.animals.yields.store', $animal), ['yield_date' => '2026-05-01', 'yield_type' => 'milk', 'quantity' => 0, 'unit_of_measure' => 'litres'])->assertSessionHasErrors('quantity');
        $this->actingAs($admin)->post(route('livestock.animals.yields.store', $animal), ['yield_date' => '2026-05-01', 'yield_type' => 'milk', 'quantity' => 12, 'unit_of_measure' => 'litres'])->assertRedirect(route('livestock.animals.show', $animal));
        $this->assertSame(1, LivestockYieldRecord::query()->count());
    }

    private function seedAccess(): void { $this->seed(CoreFoundationSeeder::class); $this->seed(UsersPermissionsSeeder::class); }
    private function adminUser(): User { $this->seedAccess(); return User::query()->where('email', 'admin@smartshamba.test')->firstOrFail(); }
    private function scope(): array
    {
        $this->seedAccess();
        $organization = Organization::query()->firstOrFail();
        $farm = Farm::query()->where('organization_id', $organization->id)->firstOrFail();
        $paddock = Paddock::query()->where('farm_id', $farm->id)->firstOrFail();

        return [$organization, $farm, $paddock];
    }

    private function memberWithRole(string $roleKey): User
    {
        $organization = Organization::query()->firstOrFail();
        $role = Role::query()->where('key', $roleKey)->firstOrFail();
        $user = User::query()->create(['name' => 'Scoped Member', 'email' => $roleKey.'@smartshamba.test', 'password' => 'password123', 'status' => 'active']);
        OrganizationMembership::query()->create(['organization_id' => $organization->id, 'user_id' => $user->id, 'role_id' => $role->id, 'role_key' => $role->key, 'status' => 'active', 'joined_at' => now()]);

        return $user;
    }

    private function species(string $code): LivestockSpecies
    {
        return LivestockSpecies::query()->firstOrCreate(['organization_id' => null, 'code' => $code], ['name' => 'Species '.$code, 'species_type' => 'mammal', 'status' => 'active']);
    }

    private function animalPayload(LivestockSpecies $species, ?Farm $farm = null, string $code = 'AN-001'): array
    {
        [$organization, $defaultFarm, $paddock] = $this->scope();
        $farm ??= $defaultFarm;
        $paddock = Paddock::query()->where('farm_id', $farm->id)->first()
            ?? Paddock::query()->create(['organization_id' => $organization->id, 'farm_id' => $farm->id, 'name' => 'Default Livestock Paddock', 'status' => 'active']);

        return ['organization_id' => $organization->id, 'farm_id' => $farm->id, 'paddock_id' => $paddock->id, 'species_id' => $species->id, 'animal_code' => $code, 'sex' => 'female', 'source' => 'purchased', 'status' => 'active'];
    }

    private function groupPayload(LivestockSpecies $species, string $code = 'GR-001'): array
    {
        [$organization, $farm, $paddock] = $this->scope();

        return ['organization_id' => $organization->id, 'farm_id' => $farm->id, 'paddock_id' => $paddock->id, 'species_id' => $species->id, 'group_code' => $code, 'name' => 'Test Group', 'group_type' => 'batch', 'initial_count' => 20, 'current_count' => 20, 'source' => 'purchased', 'status' => 'active'];
    }

    private function animal(): LivestockAnimal
    {
        return LivestockAnimal::query()->create($this->animalPayload($this->species('CATTLE'), code: 'AN-'.uniqid()));
    }

    private function group(int $currentCount = 20): LivestockAnimalGroup
    {
        $payload = $this->groupPayload($this->species('POULTRY'), 'GR-'.uniqid());
        $payload['initial_count'] = $currentCount;
        $payload['current_count'] = $currentCount;

        return LivestockAnimalGroup::query()->create($payload);
    }

    private function product(Organization $organization, string $code): InventoryProduct
    {
        $category = InventoryProductCategory::query()->firstOrCreate(['organization_id' => $organization->id, 'slug' => 'livestock-inputs'], ['name' => 'Livestock Inputs', 'status' => 'active']);

        return InventoryProduct::query()->create(['organization_id' => $organization->id, 'category_id' => $category->id, 'name' => 'Product '.$code, 'code' => $code, 'product_type' => 'input', 'unit_of_measure' => 'kg', 'status' => 'active']);
    }
}
