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
use App\Modules\Notifications\Models\FarmNotification;
use App\Modules\Notifications\Models\NotificationRule;
use App\Modules\Sales\Models\SalesRecord;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\UsersPermissions\Models\OrganizationMembership;
use App\Modules\UsersPermissions\Models\Role;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class NotificationsAlertsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_notifications_dashboard(): void
    {
        $this->get('/admin/notifications')->assertRedirect(route('login'));
    }

    public function test_user_with_notifications_view_can_access_dashboard(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('notifications.dashboard'))
            ->assertOk()
            ->assertSee('Alerts dashboard');
    }

    public function test_user_without_notifications_permission_is_denied(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->actingAs($this->memberWithRole('farm-hand'))
            ->get(route('notifications.dashboard'))
            ->assertForbidden();
    }

    public function test_notification_list_and_rules_routes_load(): void
    {
        $user = $this->adminUser();

        $this->actingAs($user)->get(route('notifications.index'))->assertOk();
        $this->actingAs($user)->get(route('notifications.rules.index'))->assertOk();
    }

    public function test_notification_lifecycle_actions(): void
    {
        $user = $this->adminUser();
        $notification = $this->notification();

        $this->actingAs($user)->post(route('notifications.mark-read', $notification))->assertRedirect();
        $this->assertSame('read', $notification->fresh()->status);
        $this->assertNotNull($notification->fresh()->read_at);

        $this->actingAs($user)->post(route('notifications.mark-unread', $notification))->assertRedirect();
        $this->assertSame('unread', $notification->fresh()->status);
        $this->assertNull($notification->fresh()->read_at);

        $this->actingAs($user)->post(route('notifications.dismiss', $notification))->assertRedirect();
        $this->assertSame('dismissed', $notification->fresh()->status);
        $this->assertNotNull($notification->fresh()->dismissed_at);

        $notification = $this->notification(['title' => 'Resolve this alert']);
        $this->actingAs($user)->post(route('notifications.resolve', $notification))->assertRedirect();
        $this->assertSame('resolved', $notification->fresh()->status);
        $this->assertNotNull($notification->fresh()->resolved_at);
    }

    public function test_refresh_does_not_mutate_source_modules_and_is_idempotent(): void
    {
        $user = $this->adminUser();
        [$organization, $farm] = $this->scope();
        $task = OpsTask::create([
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'task_number' => 'TASK-NOTIFY-1',
            'title' => 'Notification overdue task',
            'category' => 'general',
            'priority' => 'medium',
            'status' => 'open',
            'due_date' => now()->subDay()->toDateString(),
            'created_by' => $user->id,
        ]);

        $sourceCounts = [
            OpsTask::class => OpsTask::count(),
            FinanceCostEntry::class => FinanceCostEntry::count(),
            SalesRecord::class => SalesRecord::count(),
            InventoryMovement::class => InventoryMovement::count(),
            CropCycle::class => CropCycle::count(),
            LivestockAnimal::class => LivestockAnimal::count(),
            Asset::class => Asset::count(),
        ];

        $this->actingAs($user)->post(route('notifications.refresh'))->assertRedirect();
        $firstCount = FarmNotification::where('source_type', 'ops_task')->where('source_id', $task->id)->count();
        $this->assertSame(1, $firstCount);

        $this->actingAs($user)->post(route('notifications.refresh'))->assertRedirect();
        $this->assertSame(1, FarmNotification::where('source_type', 'ops_task')->where('source_id', $task->id)->whereNotIn('status', ['dismissed', 'resolved'])->count());

        foreach ($sourceCounts as $model => $count) {
            $this->assertSame($count, $model::count(), $model.' count changed during notification refresh.');
        }
    }

    public function test_route_list_contains_expected_notification_routes(): void
    {
        $this->seed(DatabaseSeeder::class);

        Artisan::call('route:list', ['--path' => 'admin/notifications']);

        $this->assertTrue(Route::has('notifications.dashboard'));
        $this->assertTrue(Route::has('notifications.refresh'));
        $this->assertTrue(Route::has('notifications.rules.index'));
    }

    public function test_permissions_and_rules_are_seeded(): void
    {
        $this->seed(DatabaseSeeder::class);

        foreach (['notifications.view', 'notifications.manage', 'notifications.rules.manage'] as $key) {
            $this->assertDatabaseHas('permissions', ['key' => $key]);
        }

        $this->assertDatabaseHas('notification_rules', ['signal_type' => 'tasks.overdue']);
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

    private function notification(array $overrides = []): FarmNotification
    {
        [$organization, $farm] = $this->scope();
        $rule = NotificationRule::firstOrFail();

        return FarmNotification::create(array_replace([
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'notification_rule_id' => $rule->id,
            'title' => 'Test notification',
            'message' => 'Test notification message.',
            'severity' => 'medium',
            'status' => 'unread',
            'source_module' => 'tests',
            'source_type' => 'manual',
            'source_id' => 1,
            'signal_type' => 'tests.manual',
            'generated_at' => now(),
        ], $overrides));
    }

    private function memberWithRole(string $roleKey): User
    {
        $organization = Organization::firstOrFail();
        $farm = Farm::where('organization_id', $organization->id)->first();
        $role = Role::where('key', $roleKey)->firstOrFail();
        $user = User::create(['name' => 'Notifications Member', 'email' => $roleKey.'-notifications@smartshamba.test', 'password' => 'password123', 'status' => 'active']);

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
