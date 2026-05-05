<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\Tasks\Models\OpsTaskChecklistItem;
use App\Modules\Tasks\Models\OpsTaskUpdate;
use App\Modules\Tasks\Models\OpsWorkOrder;
use App\Modules\UsersPermissions\Models\OrganizationMembership;
use App\Modules\UsersPermissions\Models\Role;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Database\Seeders\CoreFoundationSeeder;
use Database\Seeders\UsersPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TasksWorkOrdersModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_tasks_dashboard(): void
    {
        $this->get(route('tasks.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_user_without_task_permission_cannot_access_tasks_dashboard(): void
    {
        $this->seedAccess();
        $user = $this->memberWithRole('finance-officer');

        $this->actingAs($user)
            ->get(route('tasks.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_can_access_tasks_dashboard(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('tasks.dashboard'))
            ->assertOk()
            ->assertSee('Task dashboard');
    }

    public function test_task_permissions_are_seeded(): void
    {
        $this->seedAccess();

        foreach ([
            'tasks.view',
            'tasks.create',
            'tasks.update',
            'tasks.assign',
            'tasks.submit',
            'tasks.approve',
            'tasks.cancel',
            'tasks.manage',
            'work-orders.view',
            'work-orders.create',
            'work-orders.update',
            'work-orders.cancel',
            'work-orders.manage',
        ] as $permissionKey) {
            $this->assertDatabaseHas('permissions', ['key' => $permissionKey]);
        }

        $farmManager = Role::query()->where('key', 'farm-manager')->firstOrFail();
        $this->assertTrue($farmManager->permissions()->where('key', 'tasks.approve')->exists());

        $farmHand = Role::query()->where('key', 'farm-hand')->firstOrFail();
        $this->assertTrue($farmHand->permissions()->where('key', 'tasks.submit')->exists());
        $this->assertFalse($farmHand->permissions()->where('key', 'tasks.approve')->exists());
    }

    public function test_work_order_can_be_created_updated_and_cancelled(): void
    {
        [$organization, $farm] = $this->coreScope();
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->post(route('tasks.work-orders.store'), [
                'organization_id' => $organization->id,
                'farm_id' => $farm->id,
                'title' => 'Repair water trough',
                'category' => 'maintenance',
                'priority' => 'high',
                'status' => 'planned',
            ])
            ->assertRedirect();

        $workOrder = OpsWorkOrder::query()->where('title', 'Repair water trough')->firstOrFail();
        $this->assertStringStartsWith('WO-', $workOrder->work_order_number);

        $this->actingAs($admin)
            ->put(route('tasks.work-orders.update', $workOrder), [
                'organization_id' => $organization->id,
                'farm_id' => $farm->id,
                'title' => 'Repair main water trough',
                'category' => 'maintenance',
                'priority' => 'urgent',
                'status' => 'planned',
            ])
            ->assertRedirect(route('tasks.work-orders.show', $workOrder));

        $this->assertDatabaseHas('ops_work_orders', [
            'id' => $workOrder->id,
            'title' => 'Repair main water trough',
            'priority' => 'urgent',
        ]);

        $this->actingAs($admin)
            ->post(route('tasks.work-orders.cancel', $workOrder), [
                'cancellation_reason' => 'Deferred.',
            ])
            ->assertRedirect(route('tasks.work-orders.show', $workOrder));

        $this->assertDatabaseHas('ops_work_orders', [
            'id' => $workOrder->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_task_can_be_created_updated_and_cancelled(): void
    {
        [$organization, $farm] = $this->coreScope();
        $admin = $this->adminUser();
        $workOrder = $this->workOrder($organization, $farm);
        $field = Field::query()->where('farm_id', $farm->id)->firstOrFail();

        $this->actingAs($admin)
            ->post(route('tasks.items.store'), [
                'organization_id' => $organization->id,
                'farm_id' => $farm->id,
                'work_order_id' => $workOrder->id,
                'field_id' => $field->id,
                'title' => 'Clear weeds',
                'category' => 'crop',
                'priority' => 'normal',
                'status' => 'draft',
            ])
            ->assertRedirect();

        $task = OpsTask::query()->where('title', 'Clear weeds')->firstOrFail();
        $this->assertSame($workOrder->id, $task->work_order_id);
        $this->assertStringStartsWith('TASK-', $task->task_number);

        $this->actingAs($admin)
            ->put(route('tasks.items.update', $task), [
                'organization_id' => $organization->id,
                'farm_id' => $farm->id,
                'work_order_id' => $workOrder->id,
                'field_id' => $field->id,
                'title' => 'Clear weeds and stones',
                'category' => 'crop',
                'priority' => 'high',
                'status' => 'assigned',
            ])
            ->assertRedirect(route('tasks.items.show', $task));

        $this->assertDatabaseHas('ops_tasks', [
            'id' => $task->id,
            'title' => 'Clear weeds and stones',
            'status' => 'assigned',
        ]);

        $this->actingAs($admin)
            ->post(route('tasks.items.cancel', $task), [
                'cancellation_reason' => 'No longer needed.',
            ])
            ->assertRedirect(route('tasks.items.show', $task));

        $this->assertDatabaseHas('ops_tasks', [
            'id' => $task->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_task_can_be_assigned_to_worker_team_and_user(): void
    {
        [$organization, $farm] = $this->coreScope();
        $admin = $this->adminUser();
        $task = $this->task($organization, $farm);
        $worker = $this->worker($organization, $farm, 'TW-1');
        $team = LabourTeam::query()->create([
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'name' => 'Task Team',
            'team_type' => 'general',
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->post(route('tasks.items.assign', $task), [
                'assignment_type' => 'worker',
                'worker_id' => $worker->id,
            ])
            ->assertRedirect(route('tasks.items.show', $task));

        $this->actingAs($admin)
            ->post(route('tasks.items.assign', $task), [
                'assignment_type' => 'team',
                'team_id' => $team->id,
            ])
            ->assertRedirect(route('tasks.items.show', $task));

        $this->actingAs($admin)
            ->post(route('tasks.items.assign', $task), [
                'assignment_type' => 'user',
                'user_id' => $admin->id,
            ])
            ->assertRedirect(route('tasks.items.show', $task));

        $this->assertSame(3, $task->assignments()->count());
    }

    public function test_assignment_rejects_worker_or_team_from_another_farm(): void
    {
        [$organization, $farm] = $this->coreScope();
        $admin = $this->adminUser();
        $task = $this->task($organization, $farm);
        $otherFarm = Farm::query()->create([
            'organization_id' => $organization->id,
            'name' => 'Other Farm',
            'status' => 'active',
        ]);
        $otherWorker = $this->worker($organization, $otherFarm, 'TW-OTHER');
        $otherTeam = LabourTeam::query()->create([
            'organization_id' => $organization->id,
            'farm_id' => $otherFarm->id,
            'name' => 'Other Team',
            'team_type' => 'general',
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->post(route('tasks.items.assign', $task), [
                'assignment_type' => 'worker',
                'worker_id' => $otherWorker->id,
            ])
            ->assertStatus(422);

        $this->actingAs($admin)
            ->post(route('tasks.items.assign', $task), [
                'assignment_type' => 'team',
                'team_id' => $otherTeam->id,
            ])
            ->assertStatus(422);
    }

    public function test_user_assignment_requires_matching_farm_or_organization_level_membership(): void
    {
        [$organization, $farm] = $this->coreScope();
        $admin = $this->adminUser();
        $task = $this->task($organization, $farm);
        $role = Role::query()->where('key', 'farm-hand')->firstOrFail();
        $otherFarm = Farm::query()->create([
            'organization_id' => $organization->id,
            'name' => 'Other Assignment Farm',
            'status' => 'active',
        ]);

        $sameFarmUser = $this->userWithMembership('same-farm@smartshamba.test', $organization, $role, $farm);
        $organizationUser = $this->userWithMembership('org-level@smartshamba.test', $organization, $role, null);
        $otherFarmUser = $this->userWithMembership('other-farm@smartshamba.test', $organization, $role, $otherFarm);

        $this->actingAs($admin)
            ->post(route('tasks.items.assign', $task), [
                'assignment_type' => 'user',
                'user_id' => $sameFarmUser->id,
            ])
            ->assertRedirect(route('tasks.items.show', $task));

        $this->actingAs($admin)
            ->post(route('tasks.items.assign', $task), [
                'assignment_type' => 'user',
                'user_id' => $organizationUser->id,
            ])
            ->assertRedirect(route('tasks.items.show', $task));

        $this->actingAs($admin)
            ->post(route('tasks.items.assign', $task), [
                'assignment_type' => 'user',
                'user_id' => $otherFarmUser->id,
            ])
            ->assertStatus(422);
    }

    public function test_task_update_checklist_submit_approve_and_reject_flows_work(): void
    {
        [$organization, $farm] = $this->coreScope();
        $admin = $this->adminUser();
        $task = $this->task($organization, $farm);

        $this->actingAs($admin)
            ->post(route('tasks.items.updates.store', $task), [
                'update_type' => 'progress',
                'progress_percent' => 50,
                'notes' => 'Half done.',
            ])
            ->assertRedirect(route('tasks.items.show', $task));

        $this->assertDatabaseHas('ops_task_updates', [
            'task_id' => $task->id,
            'update_type' => 'progress',
            'progress_percent' => 50,
        ]);

        $this->actingAs($admin)
            ->post(route('tasks.items.checklist.store', $task), [
                'label' => 'Take completion photo',
            ])
            ->assertRedirect(route('tasks.items.show', $task));

        $item = OpsTaskChecklistItem::query()->where('task_id', $task->id)->firstOrFail();

        $this->actingAs($admin)
            ->patch(route('tasks.checklist.toggle', $item))
            ->assertRedirect(route('tasks.items.show', $task));

        $this->assertTrue($item->fresh()->is_completed);

        $this->actingAs($admin)
            ->post(route('tasks.items.submit', $task))
            ->assertRedirect(route('tasks.items.show', $task));

        $this->assertSame('submitted', $task->fresh()->status);

        $this->actingAs($admin)
            ->post(route('tasks.items.approve', $task), [
                'approval_notes' => 'Good.',
            ])
            ->assertRedirect(route('tasks.items.show', $task));

        $this->assertSame('completed', $task->fresh()->status);

        $secondTask = $this->task($organization, $farm, 'Needs inspection');

        $this->actingAs($admin)
            ->post(route('tasks.items.reject', $secondTask), [
                'approval_notes' => 'Add more detail.',
            ])
            ->assertRedirect(route('tasks.items.show', $secondTask));

        $this->assertSame('needs_correction', $secondTask->fresh()->status);
        $this->assertGreaterThanOrEqual(1, OpsTaskUpdate::query()->where('task_id', $secondTask->id)->count());
    }

    public function test_validation_rejects_missing_organization_farm_and_title(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->post(route('tasks.work-orders.store'), [
                'category' => 'general',
                'priority' => 'normal',
                'status' => 'planned',
            ])
            ->assertSessionHasErrors(['organization_id', 'farm_id', 'title']);

        $this->actingAs($admin)
            ->post(route('tasks.items.store'), [
                'category' => 'general',
                'priority' => 'normal',
                'status' => 'draft',
            ])
            ->assertSessionHasErrors(['organization_id', 'farm_id', 'title']);
    }

    private function seedAccess(): void
    {
        $this->seed(CoreFoundationSeeder::class);
        $this->seed(UsersPermissionsSeeder::class);
    }

    private function adminUser(): User
    {
        $this->seedAccess();

        return User::query()->where('email', 'admin@smartshamba.test')->firstOrFail();
    }

    private function coreScope(): array
    {
        $this->seedAccess();

        return [
            Organization::query()->firstOrFail(),
            Farm::query()->firstOrFail(),
        ];
    }

    private function memberWithRole(string $roleKey): User
    {
        $organization = Organization::query()->firstOrFail();
        $role = Role::query()->where('key', $roleKey)->firstOrFail();
        $user = User::query()->create([
            'name' => 'Scoped Member',
            'email' => $roleKey.'@smartshamba.test',
            'password' => 'password123',
            'status' => 'active',
        ]);

        OrganizationMembership::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => $role->id,
            'role_key' => $role->key,
            'status' => 'active',
            'joined_at' => now(),
        ]);

        return $user;
    }

    private function userWithMembership(string $email, Organization $organization, Role $role, ?Farm $farm): User
    {
        $user = User::query()->create([
            'name' => 'Task Assignee',
            'email' => $email,
            'password' => 'password123',
            'status' => 'active',
        ]);

        OrganizationMembership::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => $role->id,
            'role_key' => $role->key,
            'farm_id' => $farm?->id,
            'status' => 'active',
            'joined_at' => now(),
        ]);

        return $user;
    }

    private function workOrder(Organization $organization, Farm $farm): OpsWorkOrder
    {
        return OpsWorkOrder::query()->create([
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'work_order_number' => 'WO-TEST-1',
            'title' => 'Test work order',
            'category' => 'general',
            'priority' => 'normal',
            'status' => 'planned',
        ]);
    }

    private function task(Organization $organization, Farm $farm, string $title = 'Test task'): OpsTask
    {
        return OpsTask::query()->create([
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'task_number' => 'TASK-TEST-'.uniqid(),
            'title' => $title,
            'category' => 'general',
            'priority' => 'normal',
            'status' => 'draft',
        ]);
    }

    private function worker(Organization $organization, Farm $farm, string $code): LabourWorker
    {
        return LabourWorker::query()->create([
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'worker_code' => $code,
            'name' => 'Worker '.$code,
            'employment_type' => 'full-time',
            'primary_role' => 'General labour',
            'status' => 'active',
        ]);
    }
}
