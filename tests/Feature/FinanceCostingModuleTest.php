<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Finance\Models\FinanceCostAllocation;
use App\Modules\Finance\Models\FinanceCostCategory;
use App\Modules\Finance\Models\FinanceCostCentre;
use App\Modules\Finance\Models\FinanceCostEntry;
use App\Modules\Inventory\Models\InventoryMovement;
use App\Modules\UsersPermissions\Models\OrganizationMembership;
use App\Modules\UsersPermissions\Models\Role;
use Database\Seeders\CoreFoundationSeeder;
use Database\Seeders\UsersPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FinanceCostingModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_finance_dashboard(): void
    {
        $this->get('/admin/finance')->assertRedirect(route('login'));
    }

    public function test_user_without_finance_view_cannot_access_finance_dashboard(): void
    {
        $this->seedAccess();
        $this->actingAs($this->memberWithRole('farm-hand'))->get(route('finance.dashboard'))->assertForbidden();
    }

    public function test_user_with_finance_view_can_access_finance_dashboard(): void
    {
        $this->actingAs($this->adminUser())->get(route('finance.dashboard'))->assertOk()->assertSee('Finance dashboard');
    }

    public function test_finance_permissions_are_seeded(): void
    {
        $this->seedAccess();
        foreach (['finance.view', 'finance.manage', 'finance.reports'] as $key) {
            $this->assertDatabaseHas('permissions', ['key' => $key]);
        }
        $this->assertTrue(Role::where('key', 'finance-officer')->firstOrFail()->permissions()->where('key', 'finance.manage')->exists());
    }

    public function test_user_with_finance_manage_can_create_cost_category(): void
    {
        [$organization] = $this->scope();
        $this->actingAs($this->adminUser())->post(route('finance.categories.store'), ['organization_id' => $organization->id, 'name' => 'Compost', 'cost_nature' => 'variable', 'default_source_module' => 'manual', 'is_active' => 1])->assertRedirect(route('finance.categories.index'));
        $this->assertDatabaseHas('finance_cost_categories', ['name' => 'Compost']);
    }

    public function test_user_with_finance_manage_can_create_cost_centre(): void
    {
        [$organization, $farm] = $this->scope();
        $this->actingAs($this->adminUser())->post(route('finance.cost-centres.store'), ['organization_id' => $organization->id, 'farm_id' => $farm->id, 'name' => 'North Block', 'centre_type' => 'field', 'is_active' => 1])->assertRedirect(route('finance.cost-centres.index'));
        $this->assertDatabaseHas('finance_cost_centres', ['name' => 'North Block']);
    }

    public function test_user_with_finance_manage_can_create_draft_cost_entry_with_one_allocation(): void
    {
        $this->actingAs($this->adminUser())->post(route('finance.cost-entries.store'), $this->entryPayload())->assertRedirect();
        $entry = FinanceCostEntry::firstOrFail();
        $this->assertSame('draft', $entry->status);
        $this->assertSame(1, $entry->allocations()->count());
    }

    public function test_cost_entry_amount_must_be_positive(): void
    {
        $this->actingAs($this->adminUser())->post(route('finance.cost-entries.store'), $this->entryPayload(['amount' => 0, 'allocation_amount' => 0]))->assertSessionHasErrors(['amount', 'allocation_amount']);
    }

    public function test_allocation_total_must_equal_cost_entry_amount_before_confirmation(): void
    {
        $this->actingAs($this->adminUser())->post(route('finance.cost-entries.store'), $this->entryPayload(['amount' => 500, 'allocation_amount' => 250]))->assertSessionHasErrors('allocation_amount');
    }

    public function test_confirming_draft_cost_entry_records_confirmer_and_timestamp(): void
    {
        $admin = $this->adminUser();
        $this->actingAs($admin)->post(route('finance.cost-entries.store'), $this->entryPayload())->assertRedirect();
        $entry = FinanceCostEntry::firstOrFail();

        $this->actingAs($admin)->post(route('finance.cost-entries.confirm', $entry))->assertRedirect(route('finance.cost-entries.show', $entry));

        $this->assertSame('confirmed', $entry->fresh()->status);
        $this->assertSame($admin->id, $entry->fresh()->confirmed_by);
        $this->assertNotNull($entry->fresh()->confirmed_at);
    }

    public function test_confirmed_entries_are_included_in_reports_and_voided_are_excluded(): void
    {
        $admin = $this->adminUser();
        $this->actingAs($admin)->post(route('finance.cost-entries.store'), $this->entryPayload(['title' => 'Confirmed cost', 'amount' => 1000, 'allocation_amount' => 1000]));
        $confirmed = FinanceCostEntry::firstOrFail();
        $this->actingAs($admin)->post(route('finance.cost-entries.confirm', $confirmed));
        $this->actingAs($admin)->post(route('finance.cost-entries.store'), $this->entryPayload(['title' => 'Voided cost', 'amount' => 900, 'allocation_amount' => 900]));
        $voided = FinanceCostEntry::where('title', 'Voided cost')->firstOrFail();
        $this->actingAs($admin)->post(route('finance.cost-entries.void', $voided), ['void_reason' => 'Duplicate']);

        $this->actingAs($admin)->get(route('finance.reports.summary'))->assertOk()->assertSee('1,000.00')->assertDontSee('1,900.00');
    }

    public function test_user_cannot_manage_finance_records_outside_organization_scope(): void
    {
        [$organization] = $this->scope();
        $otherOrg = Organization::create(['name' => 'Other Finance Org', 'slug' => 'other-finance-org', 'status' => 'active']);
        $otherFarm = Farm::create(['organization_id' => $otherOrg->id, 'name' => 'Other Finance Farm', 'status' => 'active']);

        $this->actingAs($this->adminUser())->post(route('finance.cost-entries.store'), $this->entryPayload(['organization_id' => $organization->id, 'farm_id' => $otherFarm->id]))->assertSessionHasErrors('farm_id');
    }

    public function test_finance_creation_does_not_create_inventory_or_forbidden_financial_tables(): void
    {
        $this->actingAs($this->adminUser())->post(route('finance.cost-entries.store'), $this->entryPayload())->assertRedirect();

        $this->assertSame(0, InventoryMovement::count());
        foreach (['accounting_ledgers', 'journal_entries', 'payroll_records', 'sales_invoices', 'supplier_payments', 'bank_reconciliations', 'tax_filings'] as $table) {
            $this->assertFalse(Schema::hasTable($table), $table.' should not exist in Finance / Costing.');
        }
        $this->assertSame(1, FinanceCostAllocation::count());
    }

    private function seedAccess(): void { $this->seed(CoreFoundationSeeder::class); $this->seed(UsersPermissionsSeeder::class); }
    private function adminUser(): User { $this->seedAccess(); return User::where('email', 'admin@smartshamba.test')->firstOrFail(); }
    private function scope(): array { $this->seedAccess(); $organization = Organization::firstOrFail(); $farm = Farm::where('organization_id', $organization->id)->firstOrFail(); return [$organization, $farm]; }

    private function memberWithRole(string $roleKey): User
    {
        $organization = Organization::firstOrFail();
        $role = Role::where('key', $roleKey)->firstOrFail();
        $user = User::create(['name' => 'Finance Member', 'email' => $roleKey.'-finance@smartshamba.test', 'password' => 'password123', 'status' => 'active']);
        OrganizationMembership::create(['organization_id' => $organization->id, 'user_id' => $user->id, 'role_id' => $role->id, 'role_key' => $role->key, 'status' => 'active', 'joined_at' => now()]);
        return $user;
    }

    private function entryPayload(array $overrides = []): array
    {
        [$organization, $farm] = $this->scope();
        $category = FinanceCostCategory::firstOrCreate(['organization_id' => $organization->id, 'farm_id' => null, 'name' => 'Labour'], ['cost_nature' => 'variable', 'default_source_module' => 'labour', 'is_active' => true]);
        $centre = FinanceCostCentre::firstOrCreate(['organization_id' => $organization->id, 'farm_id' => $farm->id, 'name' => 'Farm Operations'], ['centre_type' => 'farm', 'is_active' => true]);

        return array_merge([
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'cost_category_id' => $category->id,
            'cost_centre_id' => $centre->id,
            'entry_date' => '2026-05-01',
            'title' => 'Manual labour costing',
            'source_module' => 'manual',
            'amount' => 500,
            'currency' => 'KES',
            'payment_state' => 'not_tracked',
            'allocation_type' => 'general_farm',
            'allocation_label' => 'General farm',
            'allocation_amount' => 500,
        ], $overrides);
    }
}
