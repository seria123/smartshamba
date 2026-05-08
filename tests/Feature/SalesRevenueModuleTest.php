<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Crops\Models\CropCycle;
use App\Modules\Finance\Models\FinanceCostEntry;
use App\Modules\Inventory\Models\InventoryMovement;
use App\Modules\Livestock\Models\LivestockAnimal;
use App\Modules\Livestock\Models\LivestockAnimalGroup;
use App\Modules\Sales\Models\SalesCatalogItem;
use App\Modules\Sales\Models\SalesCustomer;
use App\Modules\Sales\Models\SalesRecord;
use App\Modules\UsersPermissions\Models\OrganizationMembership;
use App\Modules\UsersPermissions\Models\Permission;
use App\Modules\UsersPermissions\Models\Role;
use Database\Seeders\CoreFoundationSeeder;
use Database\Seeders\CropsModuleSeeder;
use Database\Seeders\LivestockModuleSeeder;
use Database\Seeders\UsersPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SalesRevenueModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_sales_dashboard(): void
    {
        $this->get('/admin/sales')->assertRedirect(route('login'));
    }

    public function test_user_with_sales_view_can_access_sales_dashboard(): void
    {
        $this->actingAs($this->adminUser())->get(route('sales.dashboard'))->assertOk()->assertSee('Sales dashboard');
    }

    public function test_user_without_sales_permission_is_denied(): void
    {
        $this->seedAccess();
        $this->actingAs($this->memberWithRole('farm-hand'))->get(route('sales.dashboard'))->assertForbidden();
    }

    public function test_sales_permissions_are_seeded(): void
    {
        $this->seedAccess();
        foreach (['sales.view', 'sales.manage', 'sales.reports'] as $key) {
            $this->assertDatabaseHas('permissions', ['key' => $key]);
        }
        $this->assertTrue(Role::where('key', 'finance-officer')->firstOrFail()->permissions()->where('key', 'sales.manage')->exists());
    }

    public function test_sales_customer_can_be_created_with_valid_scope(): void
    {
        [$organization, $farm] = $this->scope();
        $this->actingAs($this->adminUser())->post(route('sales.customers.store'), [
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'name' => 'Market Buyer',
            'customer_type' => 'retailer',
            'is_active' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('sales_customers', ['name' => 'Market Buyer', 'farm_id' => $farm->id]);
    }

    public function test_sales_catalog_item_can_be_created(): void
    {
        [$organization, $farm] = $this->scope();
        $this->actingAs($this->adminUser())->post(route('sales.catalog-items.store'), [
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'name' => 'Tomatoes',
            'category' => 'crop_produce',
            'unit' => 'kg',
            'default_unit_price' => 80,
            'currency' => 'KES',
            'is_active' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('sales_catalog_items', ['name' => 'Tomatoes']);
    }

    public function test_draft_sale_is_created_and_totals_are_calculated_server_side(): void
    {
        $this->actingAs($this->adminUser())->post(route('sales.records.store'), $this->salePayload())->assertRedirect();

        $sale = SalesRecord::firstOrFail();
        $this->assertSame('draft', $sale->status);
        $this->assertEquals(950, (float) $sale->total_amount);
        $this->assertEquals(950, (float) $sale->balance_amount);
    }

    public function test_sale_cannot_be_created_without_lines(): void
    {
        $this->actingAs($this->adminUser())->post(route('sales.records.store'), $this->salePayload(['lines' => []]))->assertSessionHasErrors('lines');
    }

    public function test_draft_sale_can_be_confirmed(): void
    {
        $admin = $this->adminUser();
        $this->actingAs($admin)->post(route('sales.records.store'), $this->salePayload());
        $sale = SalesRecord::firstOrFail();

        $this->actingAs($admin)->post(route('sales.records.confirm', $sale))->assertRedirect(route('sales.records.show', $sale));

        $this->assertSame('confirmed', $sale->fresh()->status);
        $this->assertSame($admin->id, $sale->fresh()->confirmed_by);
    }

    public function test_confirmed_sale_can_receive_payment_and_blocks_overpayment(): void
    {
        $admin = $this->adminUser();
        $this->actingAs($admin)->post(route('sales.records.store'), $this->salePayload());
        $sale = SalesRecord::firstOrFail();
        $this->actingAs($admin)->post(route('sales.records.confirm', $sale));

        $this->actingAs($admin)->post(route('sales.payments.store', $sale), ['payment_date' => '2026-05-02', 'amount' => 450, 'method' => 'cash'])->assertRedirect();
        $this->assertEquals(450, (float) $sale->fresh()->amount_paid);
        $this->assertEquals(500, (float) $sale->fresh()->balance_amount);
        $this->assertSame('partial', $sale->fresh()->payment_status);

        $this->actingAs($admin)->post(route('sales.payments.store', $sale), ['payment_date' => '2026-05-02', 'amount' => 501, 'method' => 'cash'])->assertSessionHasErrors('amount');
    }

    public function test_void_sale_is_excluded_from_standard_summary_report(): void
    {
        $admin = $this->adminUser();
        $this->actingAs($admin)->post(route('sales.records.store'), $this->salePayload());
        $confirmed = SalesRecord::firstOrFail();
        $this->actingAs($admin)->post(route('sales.records.confirm', $confirmed));
        $this->actingAs($admin)->post(route('sales.records.store'), $this->salePayload(['notes' => 'Void this sale']));
        $voided = SalesRecord::where('notes', 'Void this sale')->firstOrFail();
        $this->actingAs($admin)->post(route('sales.records.void', $voided), ['void_reason' => 'Duplicate']);

        $this->actingAs($admin)->get(route('sales.reports.summary'))->assertOk()->assertSee('950.00')->assertDontSee('1,900.00');
    }

    public function test_cross_organization_farm_reference_is_rejected(): void
    {
        [$organization] = $this->scope();
        $otherOrg = Organization::create(['name' => 'Other Sales Org', 'slug' => 'other-sales-org', 'status' => 'active']);
        $otherFarm = Farm::create(['organization_id' => $otherOrg->id, 'name' => 'Other Sales Farm', 'status' => 'active']);

        $this->actingAs($this->adminUser())->post(route('sales.records.store'), $this->salePayload(['organization_id' => $organization->id, 'farm_id' => $otherFarm->id]))->assertSessionHasErrors('farm_id');
    }

    public function test_confirming_sale_does_not_mutate_operational_or_finance_modules(): void
    {
        $this->seed(CropsModuleSeeder::class);
        $this->seed(LivestockModuleSeeder::class);
        [$organization, $farm] = $this->scope();
        $cycle = CropCycle::where('organization_id', $organization->id)->where('farm_id', $farm->id)->first();
        $animal = LivestockAnimal::where('organization_id', $organization->id)->where('farm_id', $farm->id)->first();
        $group = LivestockAnimalGroup::where('organization_id', $organization->id)->where('farm_id', $farm->id)->first();

        $counts = [
            'inventory' => InventoryMovement::count(),
            'finance' => FinanceCostEntry::count(),
            'cycles' => CropCycle::count(),
            'animals' => LivestockAnimal::count(),
            'groups' => LivestockAnimalGroup::count(),
        ];

        $payload = $this->salePayload([
            'lines' => [
                ['description' => 'Linked crop produce', 'category' => 'crop_produce', 'unit' => 'kg', 'quantity' => 10, 'unit_price' => 20, 'crop_cycle_id' => $cycle?->id],
                ['description' => 'Linked livestock product', 'category' => 'animal_product', 'unit' => 'litre', 'quantity' => 5, 'unit_price' => 30, 'animal_id' => $animal?->id],
                ['description' => 'Linked group sale', 'category' => 'livestock', 'unit' => 'head', 'quantity' => 1, 'unit_price' => 300, 'animal_group_id' => $group?->id],
            ],
        ]);
        $this->actingAs($this->adminUser())->post(route('sales.records.store'), $payload)->assertRedirect();
        $this->actingAs($this->adminUser())->post(route('sales.records.confirm', SalesRecord::firstOrFail()))->assertRedirect();

        $this->assertSame($counts['inventory'], InventoryMovement::count());
        $this->assertSame($counts['finance'], FinanceCostEntry::count());
        $this->assertSame($counts['cycles'], CropCycle::count());
        $this->assertSame($counts['animals'], LivestockAnimal::count());
        $this->assertSame($counts['groups'], LivestockAnimalGroup::count());
        foreach (['accounting_ledgers', 'journal_entries', 'accounts_receivable_ledgers', 'bank_reconciliations', 'tax_filings', 'supplier_payments', 'payroll_records'] as $table) {
            $this->assertFalse(Schema::hasTable($table), $table.' should not exist in Sales / Revenue.');
        }
    }

    public function test_sales_routes_resolve(): void
    {
        $this->seedAccess();
        Artisan::call('route:list', ['--path' => 'admin/sales']);
        $this->assertTrue(Route::has('sales.dashboard'));
        $this->assertTrue(Route::has('sales.reports.gross-margin'));
    }

    private function seedAccess(): void { $this->seed(CoreFoundationSeeder::class); $this->seed(UsersPermissionsSeeder::class); }
    private function adminUser(): User { $this->seedAccess(); return User::where('email', 'admin@smartshamba.test')->firstOrFail(); }
    private function scope(): array { $this->seedAccess(); $organization = Organization::firstOrFail(); $farm = Farm::where('organization_id', $organization->id)->firstOrFail(); return [$organization, $farm]; }

    private function memberWithRole(string $roleKey): User
    {
        $organization = Organization::firstOrFail();
        $role = Role::where('key', $roleKey)->firstOrFail();
        $user = User::create(['name' => 'Sales Member', 'email' => $roleKey.'-sales@smartshamba.test', 'password' => 'password123', 'status' => 'active']);
        OrganizationMembership::create(['organization_id' => $organization->id, 'user_id' => $user->id, 'role_id' => $role->id, 'role_key' => $role->key, 'status' => 'active', 'joined_at' => now()]);
        return $user;
    }

    private function salePayload(array $overrides = []): array
    {
        [$organization, $farm] = $this->scope();
        $customer = SalesCustomer::firstOrCreate(['organization_id' => $organization->id, 'farm_id' => $farm->id, 'name' => 'Sales Test Buyer'], ['customer_type' => 'broker', 'is_active' => true]);
        $item = SalesCatalogItem::firstOrCreate(['organization_id' => $organization->id, 'farm_id' => $farm->id, 'name' => 'Sales Test Maize'], ['category' => 'crop_produce', 'unit' => 'kg', 'default_unit_price' => 50, 'currency' => 'KES', 'is_active' => true]);

        return array_replace([
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'sales_customer_id' => $customer->id,
            'sale_date' => '2026-05-01',
            'channel' => 'farm_gate',
            'currency' => 'KES',
            'discount_amount' => 50,
            'other_charges_amount' => 0,
            'notes' => 'Test sale',
            'lines' => [
                ['sales_catalog_item_id' => $item->id, 'description' => 'Sales Test Maize', 'category' => 'crop_produce', 'unit' => 'kg', 'quantity' => 20, 'unit_price' => 50, 'discount_amount' => 0],
            ],
        ], $overrides);
    }
}
