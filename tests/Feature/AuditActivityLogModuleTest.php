<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Audit\Models\AuditActivityLog;
use App\Modules\Audit\Services\AuditLogger;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\UsersPermissions\Models\OrganizationMembership;
use App\Modules\UsersPermissions\Models\Role;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuditActivityLogModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_audit_dashboard(): void
    {
        $this->get('/admin/audit')->assertRedirect(route('login'));
    }

    public function test_user_with_audit_view_can_access_dashboard(): void
    {
        $this->actingAs($this->adminUser())->get(route('audit.dashboard'))->assertOk();
    }

    public function test_user_without_audit_permission_is_denied(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->actingAs($this->memberWithRole('farm-hand'))->get(route('audit.dashboard'))->assertForbidden();
    }

    public function test_audit_logs_index_loads(): void
    {
        $this->actingAs($this->adminUser())->get(route('audit.logs.index'))->assertOk();
    }

    public function test_audit_log_detail_page_loads(): void
    {
        $user = $this->adminUser();
        $log = AuditActivityLog::firstOrFail();

        $this->actingAs($user)->get(route('audit.logs.show', $log))->assertOk();
    }

    public function test_audit_reports_load(): void
    {
        $user = $this->adminUser();

        foreach (['audit.reports.modules', 'audit.reports.actors', 'audit.reports.actions', 'audit.reports.daily'] as $route) {
            $this->actingAs($user)->get(route($route))->assertOk();
        }
    }

    public function test_audit_logger_can_record_event(): void
    {
        $user = $this->adminUser();
        [$organization, $farm] = $this->scope();

        $this->actingAs($user);
        $log = app(AuditLogger::class)->record([
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'module' => 'tests',
            'event' => 'created',
            'subject_label' => 'Test subject',
            'description' => 'A test audit event was recorded.',
            'after_values' => ['status' => 'created'],
        ]);

        $this->assertDatabaseHas('audit_activity_logs', [
            'id' => $log->id,
            'actor_user_id' => $user->id,
            'module' => 'tests',
            'event' => 'created',
        ]);
        $this->assertSame(['status' => 'created'], $log->fresh()->after_values);
    }

    public function test_audit_logs_are_not_editable_or_deletable_through_routes(): void
    {
        $this->adminUser();

        $this->assertFalse(Route::has('audit.logs.create'));
        $this->assertFalse(Route::has('audit.logs.edit'));
        $this->assertFalse(Route::has('audit.logs.store'));
        $this->assertFalse(Route::has('audit.logs.update'));
        $this->assertFalse(Route::has('audit.logs.destroy'));
    }

    public function test_route_list_contains_only_intended_get_routes(): void
    {
        $this->adminUser();
        Artisan::call('route:list', ['--path' => 'admin/audit']);

        $routes = collect(Route::getRoutes())
            ->filter(fn ($route) => str_starts_with($route->uri(), 'admin/audit'))
            ->values();

        $this->assertCount(7, $routes);
        $this->assertTrue($routes->every(fn ($route) => $route->methods() === ['GET', 'HEAD']));
        $this->assertTrue(Route::has('audit.dashboard'));
        $this->assertTrue(Route::has('audit.logs.index'));
        $this->assertTrue(Route::has('audit.logs.show'));
        $this->assertTrue(Route::has('audit.reports.modules'));
        $this->assertTrue(Route::has('audit.reports.actors'));
        $this->assertTrue(Route::has('audit.reports.actions'));
        $this->assertTrue(Route::has('audit.reports.daily'));
    }

    private function adminUser(): User
    {
        $this->seed(DatabaseSeeder::class);
        return User::where('email', 'admin@smartshamba.test')->firstOrFail();
    }

    private function scope(): array
    {
        return [Organization::firstOrFail(), Farm::firstOrFail()];
    }

    private function memberWithRole(string $roleKey): User
    {
        $organization = Organization::firstOrFail();
        $farm = Farm::where('organization_id', $organization->id)->first();
        $role = Role::where('key', $roleKey)->firstOrFail();
        $user = User::create(['name' => 'Audit Member', 'email' => $roleKey.'-audit@smartshamba.test', 'password' => 'password123', 'status' => 'active']);
        OrganizationMembership::create(['organization_id' => $organization->id, 'farm_id' => $farm?->id, 'user_id' => $user->id, 'role_id' => $role->id, 'role_key' => $role->key, 'status' => 'active', 'joined_at' => now()]);

        return $user;
    }
}
