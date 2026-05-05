<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Warehouse;
use App\Modules\Inventory\Models\InventoryMovement;
use App\Modules\Inventory\Models\InventoryProduct;
use App\Modules\Inventory\Models\InventoryProductCategory;
use App\Modules\Inventory\Models\InventoryStockLot;
use App\Modules\Inventory\Models\InventorySupplier;
use App\Modules\UsersPermissions\Models\OrganizationMembership;
use App\Modules\UsersPermissions\Models\Role;
use Database\Seeders\CoreFoundationSeeder;
use Database\Seeders\UsersPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryInputsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_inventory_dashboard(): void
    {
        $this->get(route('inventory.dashboard'))->assertRedirect(route('login'));
    }

    public function test_user_without_inventory_permission_cannot_access_inventory_dashboard(): void
    {
        $this->seedAccess();
        $user = $this->memberWithRole('farm-hand');

        $this->actingAs($user)->get(route('inventory.dashboard'))->assertForbidden();
    }

    public function test_admin_can_access_inventory_dashboard(): void
    {
        $this->actingAs($this->adminUser())->get(route('inventory.dashboard'))->assertOk()->assertSee('Inventory dashboard');
    }

    public function test_inventory_permissions_are_seeded(): void
    {
        $this->seedAccess();

        foreach ([
            'inventory.view', 'inventory.manage',
            'products.view', 'products.create', 'products.update', 'products.deactivate',
            'suppliers.view', 'suppliers.create', 'suppliers.update', 'suppliers.deactivate',
            'stock.view', 'stock.receive', 'stock.issue', 'stock.transfer', 'stock.adjust', 'stock.manage',
        ] as $key) {
            $this->assertDatabaseHas('permissions', ['key' => $key]);
        }

        $storekeeper = Role::query()->where('key', 'storekeeper')->firstOrFail();
        $this->assertTrue($storekeeper->permissions()->where('key', 'stock.receive')->exists());
    }

    public function test_product_category_can_be_created_updated_and_deactivated(): void
    {
        [$organization] = $this->scope();
        $admin = $this->adminUser();

        $this->actingAs($admin)->post(route('inventory.categories.store'), [
            'organization_id' => $organization->id,
            'name' => 'Seeds',
            'slug' => 'seeds',
            'status' => 'active',
        ])->assertRedirect(route('inventory.categories.index'));

        $category = InventoryProductCategory::query()->where('slug', 'seeds')->firstOrFail();

        $this->actingAs($admin)->put(route('inventory.categories.update', $category), [
            'organization_id' => $organization->id,
            'name' => 'Seed Inputs',
            'slug' => 'seed-inputs',
            'status' => 'active',
        ])->assertRedirect(route('inventory.categories.show', $category));

        $this->actingAs($admin)->post(route('inventory.categories.deactivate', $category))->assertRedirect(route('inventory.categories.show', $category));
        $this->assertSame('inactive', $category->fresh()->status);
    }

    public function test_product_can_be_created_updated_and_deactivated(): void
    {
        [$organization] = $this->scope();
        $admin = $this->adminUser();
        $category = $this->category($organization);

        $this->actingAs($admin)->post(route('inventory.products.store'), [
            'organization_id' => $organization->id,
            'category_id' => $category->id,
            'name' => 'Maize Seed',
            'code' => 'SEED-001',
            'product_type' => 'seed',
            'unit_of_measure' => 'kg',
            'status' => 'active',
        ])->assertRedirect(route('inventory.products.index'));

        $product = InventoryProduct::query()->where('code', 'SEED-001')->firstOrFail();

        $this->actingAs($admin)->put(route('inventory.products.update', $product), [
            'organization_id' => $organization->id,
            'category_id' => $category->id,
            'name' => 'Hybrid Maize Seed',
            'code' => 'SEED-001',
            'product_type' => 'seed',
            'unit_of_measure' => 'kg',
            'status' => 'active',
        ])->assertRedirect(route('inventory.products.show', $product));

        $this->actingAs($admin)->post(route('inventory.products.deactivate', $product))->assertRedirect(route('inventory.products.show', $product));
        $this->assertSame('inactive', $product->fresh()->status);
    }

    public function test_supplier_can_be_created_updated_and_deactivated(): void
    {
        [$organization] = $this->scope();
        $admin = $this->adminUser();

        $this->actingAs($admin)->post(route('inventory.suppliers.store'), [
            'organization_id' => $organization->id,
            'name' => 'Input Supplier',
            'code' => 'SUP-100',
            'supplier_type' => 'agro-inputs',
            'status' => 'active',
        ])->assertRedirect(route('inventory.suppliers.index'));

        $supplier = InventorySupplier::query()->where('code', 'SUP-100')->firstOrFail();

        $this->actingAs($admin)->put(route('inventory.suppliers.update', $supplier), [
            'organization_id' => $organization->id,
            'name' => 'Updated Supplier',
            'code' => 'SUP-100',
            'supplier_type' => 'general',
            'status' => 'active',
        ])->assertRedirect(route('inventory.suppliers.show', $supplier));

        $this->actingAs($admin)->post(route('inventory.suppliers.deactivate', $supplier))->assertRedirect(route('inventory.suppliers.show', $supplier));
        $this->assertSame('inactive', $supplier->fresh()->status);
    }

    public function test_receiving_and_issuing_stock_updates_quantity_and_creates_movements(): void
    {
        [$organization, $farm, $warehouse] = $this->scope();
        $admin = $this->adminUser();
        $product = $this->product($organization);

        $this->actingAs($admin)->post(route('inventory.stock.receive'), [
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'lot_number' => 'LOT-001',
            'quantity' => 100,
            'unit_of_measure' => 'kg',
            'unit_cost' => 10,
            'movement_type' => 'purchase_receipt',
        ])->assertRedirect(route('inventory.stock.balances'));

        $lot = InventoryStockLot::query()->where('lot_number', 'LOT-001')->firstOrFail();
        $this->assertSame('100.00', $lot->quantity_on_hand);
        $this->assertDatabaseHas('inventory_movements', ['stock_lot_id' => $lot->id, 'movement_type' => 'purchase_receipt']);

        $this->actingAs($admin)->post(route('inventory.stock.issue'), [
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'stock_lot_id' => $lot->id,
            'quantity' => 40,
            'movement_type' => 'issue',
        ])->assertRedirect(route('inventory.stock.balances'));

        $this->assertSame('60.00', $lot->fresh()->quantity_on_hand);
        $this->assertDatabaseHas('inventory_movements', ['stock_lot_id' => $lot->id, 'movement_type' => 'issue']);
    }

    public function test_issue_and_outgoing_adjustment_cannot_exceed_quantity_on_hand(): void
    {
        [$organization, $farm, $warehouse] = $this->scope();
        $admin = $this->adminUser();
        $lot = $this->lot($organization, $farm, $warehouse, 20);

        $payload = [
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'stock_lot_id' => $lot->id,
            'quantity' => 30,
        ];

        $this->actingAs($admin)->post(route('inventory.stock.issue'), $payload + ['movement_type' => 'issue'])->assertStatus(422);
        $this->actingAs($admin)->post(route('inventory.stock.adjust'), $payload + ['movement_type' => 'adjustment_out'])->assertStatus(422);
        $this->assertSame('20.00', $lot->fresh()->quantity_on_hand);
    }

    public function test_transfer_and_adjustments_update_stock_and_create_movements(): void
    {
        [$organization, $farm, $warehouse] = $this->scope();
        $admin = $this->adminUser();
        $lot = $this->lot($organization, $farm, $warehouse, 50);
        $destination = Warehouse::query()->create([
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'name' => 'Destination Store',
            'status' => 'active',
        ]);

        $this->actingAs($admin)->post(route('inventory.stock.transfer'), [
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'stock_lot_id' => $lot->id,
            'to_warehouse_id' => $destination->id,
            'quantity' => 15,
            'movement_type' => 'transfer',
        ])->assertRedirect(route('inventory.stock.balances'));

        $this->assertSame('35.00', $lot->fresh()->quantity_on_hand);
        $this->assertDatabaseHas('inventory_movements', ['movement_type' => 'transfer_out']);
        $this->assertDatabaseHas('inventory_movements', ['movement_type' => 'transfer_in']);

        $this->actingAs($admin)->post(route('inventory.stock.adjust'), [
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'stock_lot_id' => $lot->id,
            'quantity' => 5,
            'movement_type' => 'adjustment_in',
        ])->assertRedirect(route('inventory.stock.balances'));

        $this->actingAs($admin)->post(route('inventory.stock.adjust'), [
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'stock_lot_id' => $lot->id,
            'quantity' => 10,
            'movement_type' => 'loss',
        ])->assertRedirect(route('inventory.stock.balances'));

        $this->assertSame('30.00', $lot->fresh()->quantity_on_hand);
        $this->assertDatabaseHas('inventory_movements', ['movement_type' => 'adjustment_in']);
        $this->assertDatabaseHas('inventory_movements', ['movement_type' => 'loss']);
    }

    public function test_transfer_destination_lot_preserves_source_lot_details(): void
    {
        [$organization, $farm, $warehouse] = $this->scope();
        $admin = $this->adminUser();
        $supplier = InventorySupplier::query()->create([
            'organization_id' => $organization->id,
            'name' => 'Transfer Supplier',
            'supplier_type' => 'general',
            'status' => 'active',
        ]);
        $lot = $this->lot($organization, $farm, $warehouse, 50);
        $lot->update([
            'supplier_id' => $supplier->id,
            'lot_number' => 'LOT-PRESERVE-001',
            'batch_number' => 'BATCH-PRESERVE-001',
            'expiry_date' => '2026-12-31',
            'unit_of_measure' => 'litres',
            'unit_cost' => 123.45,
            'currency' => 'USD',
        ]);
        $destination = Warehouse::query()->create([
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'name' => 'Preserved Lot Store',
            'status' => 'active',
        ]);

        $this->actingAs($admin)->post(route('inventory.stock.transfer'), [
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'stock_lot_id' => $lot->id,
            'to_warehouse_id' => $destination->id,
            'quantity' => 12,
            'movement_type' => 'transfer',
        ])->assertRedirect(route('inventory.stock.balances'));

        $destinationLot = InventoryStockLot::query()
            ->where('warehouse_id', $destination->id)
            ->where('lot_number', 'LOT-PRESERVE-001')
            ->firstOrFail();

        $this->assertSame($lot->organization_id, $destinationLot->organization_id);
        $this->assertSame($lot->farm_id, $destinationLot->farm_id);
        $this->assertSame($lot->product_id, $destinationLot->product_id);
        $this->assertSame($lot->supplier_id, $destinationLot->supplier_id);
        $this->assertSame($lot->lot_number, $destinationLot->lot_number);
        $this->assertSame($lot->batch_number, $destinationLot->batch_number);
        $this->assertSame($lot->expiry_date->format('Y-m-d'), $destinationLot->expiry_date->format('Y-m-d'));
        $this->assertSame($lot->unit_of_measure, $destinationLot->unit_of_measure);
        $this->assertSame($lot->unit_cost, $destinationLot->unit_cost);
        $this->assertSame($lot->currency, $destinationLot->currency);
        $this->assertSame($destination->id, $destinationLot->warehouse_id);
        $this->assertSame('12.00', $destinationLot->quantity_on_hand);
    }

    public function test_warehouse_must_belong_to_selected_farm(): void
    {
        [$organization, $farm] = $this->scope();
        $admin = $this->adminUser();
        $product = $this->product($organization);
        $otherFarm = Farm::query()->create(['organization_id' => $organization->id, 'name' => 'Other Farm', 'status' => 'active']);
        $otherWarehouse = Warehouse::query()->create(['organization_id' => $organization->id, 'farm_id' => $otherFarm->id, 'name' => 'Other Store', 'status' => 'active']);

        $this->actingAs($admin)->post(route('inventory.stock.receive'), [
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'warehouse_id' => $otherWarehouse->id,
            'product_id' => $product->id,
            'lot_number' => 'BAD-LOT',
            'quantity' => 10,
            'unit_of_measure' => 'kg',
            'unit_cost' => 10,
            'movement_type' => 'purchase_receipt',
        ])->assertStatus(422);
    }

    private function seedAccess(): void { $this->seed(CoreFoundationSeeder::class); $this->seed(UsersPermissionsSeeder::class); }
    private function adminUser(): User { $this->seedAccess(); return User::query()->where('email', 'admin@smartshamba.test')->firstOrFail(); }
    private function scope(): array { $this->seedAccess(); return [Organization::firstOrFail(), Farm::firstOrFail(), Warehouse::firstOrFail()]; }

    private function memberWithRole(string $roleKey): User
    {
        $organization = Organization::firstOrFail();
        $role = Role::query()->where('key', $roleKey)->firstOrFail();
        $user = User::query()->create(['name' => 'Scoped Member', 'email' => $roleKey.'@smartshamba.test', 'password' => 'password123', 'status' => 'active']);
        OrganizationMembership::query()->create(['organization_id' => $organization->id, 'user_id' => $user->id, 'role_id' => $role->id, 'role_key' => $role->key, 'status' => 'active', 'joined_at' => now()]);
        return $user;
    }

    private function category(Organization $organization): InventoryProductCategory
    {
        return InventoryProductCategory::query()->create(['organization_id' => $organization->id, 'name' => 'Inputs', 'slug' => 'inputs', 'status' => 'active']);
    }

    private function product(Organization $organization): InventoryProduct
    {
        return InventoryProduct::query()->create(['organization_id' => $organization->id, 'category_id' => $this->category($organization)->id, 'name' => 'Test Product', 'code' => 'PROD-'.uniqid(), 'product_type' => 'input', 'unit_of_measure' => 'kg', 'status' => 'active']);
    }

    private function lot(Organization $organization, Farm $farm, Warehouse $warehouse, float $quantity): InventoryStockLot
    {
        return InventoryStockLot::query()->create(['organization_id' => $organization->id, 'farm_id' => $farm->id, 'warehouse_id' => $warehouse->id, 'product_id' => $this->product($organization)->id, 'lot_number' => 'LOT-'.uniqid(), 'quantity_on_hand' => $quantity, 'reserved_quantity' => 0, 'unit_of_measure' => 'kg', 'unit_cost' => 10, 'currency' => 'KES', 'status' => 'active']);
    }
}
