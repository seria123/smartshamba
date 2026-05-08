<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Assets\Models\Asset;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Crops\Models\CropCycle;
use App\Modules\Finance\Models\FinanceCostEntry;
use App\Modules\Inventory\Models\InventoryMovement;
use App\Modules\Livestock\Models\LivestockAnimal;
use App\Modules\Livestock\Models\LivestockAnimalGroup;
use App\Modules\Sales\Models\SalesRecord;
use App\Modules\UsersPermissions\Models\OrganizationMembership;
use App\Modules\UsersPermissions\Models\Permission;
use App\Modules\UsersPermissions\Models\Role;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ReportsAnalyticsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports_routes_require_authentication(): void
    {
        $this->get('/admin/reports')->assertRedirect(route('login'));
    }

    public function test_user_with_reports_permission_can_access_dashboard(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('reports.dashboard'))
            ->assertOk()
            ->assertSee('Farm performance dashboard');
    }

    public function test_user_without_reports_permission_cannot_access_reports(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->actingAs($this->memberWithRole('farm-hand'))
            ->get(route('reports.dashboard'))
            ->assertForbidden();
    }

    public function test_reports_permissions_are_seeded(): void
    {
        $this->seed(DatabaseSeeder::class);

        foreach (['reports.view', 'reports.analytics'] as $key) {
            $this->assertDatabaseHas('permissions', ['key' => $key]);
        }

        $this->assertTrue(Role::where('key', 'finance-officer')->firstOrFail()->permissions()->where('key', 'reports.analytics')->exists());
    }

    public function test_dashboard_loads_with_seeded_data(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('reports.dashboard'))
            ->assertOk()
            ->assertSee('Total confirmed costs')
            ->assertSee('Recent sales');
    }

    public function test_all_reports_load(): void
    {
        $user = $this->adminUser();

        foreach ($this->reportRoutes() as $route) {
            $this->actingAs($user)->get(route($route))->assertOk();
        }
    }

    public function test_filters_validate_bad_date_ranges(): void
    {
        $this->actingAs($this->adminUser())
            ->from(route('reports.dashboard'))
            ->get(route('reports.dashboard', ['date_from' => '2026-05-10', 'date_to' => '2026-05-01']))
            ->assertRedirect(route('reports.dashboard'))
            ->assertSessionHasErrors('date_to');
    }

    public function test_reports_do_not_mutate_operational_tables(): void
    {
        $user = $this->adminUser();
        $counts = [
            FinanceCostEntry::class => FinanceCostEntry::count(),
            SalesRecord::class => SalesRecord::count(),
            InventoryMovement::class => InventoryMovement::count(),
            CropCycle::class => CropCycle::count(),
            LivestockAnimal::class => LivestockAnimal::count(),
            LivestockAnimalGroup::class => LivestockAnimalGroup::count(),
            Asset::class => Asset::count(),
        ];

        foreach ($this->reportRoutes() as $route) {
            $this->actingAs($user)->get(route($route))->assertOk();
        }

        foreach ($counts as $model => $count) {
            $this->assertSame($count, $model::count(), $model.' count changed while loading reports.');
        }
    }

    public function test_reports_routes_resolve(): void
    {
        $this->seed(DatabaseSeeder::class);

        Artisan::call('route:list', ['--path' => 'admin/reports']);

        $this->assertTrue(Route::has('reports.dashboard'));
        $this->assertTrue(Route::has('reports.asset-health'));
    }

    private function reportRoutes(): array
    {
        return [
            'reports.farm-performance',
            'reports.crop-profitability',
            'reports.livestock-profitability',
            'reports.cost-vs-revenue',
            'reports.customers',
            'reports.cost-drivers',
            'reports.monthly-trends',
            'reports.operations',
            'reports.inventory-snapshot',
            'reports.asset-health',
        ];
    }

    private function adminUser(): User
    {
        $this->seed(DatabaseSeeder::class);

        return User::where('email', 'admin@smartshamba.test')->firstOrFail();
    }

    private function memberWithRole(string $roleKey): User
    {
        $organization = Organization::firstOrFail();
        $farm = Farm::where('organization_id', $organization->id)->first();
        $role = Role::where('key', $roleKey)->firstOrFail();
        $user = User::create(['name' => 'Reports Member', 'email' => $roleKey.'-reports@smartshamba.test', 'password' => 'password123', 'status' => 'active']);

        OrganizationMembership::create([
            'organization_id' => $organization->id,
            'farm_id' => $farm?->id,
            'user_id' => $user->id,
            'role_id' => $role->id,
            'role_key' => $role->key,
            'status' => 'active',
            'joined_at' => now(),
        ]);

        return $user;
    }
}
