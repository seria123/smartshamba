<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\UsersPermissions\Models\OrganizationMembership;
use App\Modules\UsersPermissions\Models\Role;
use App\Modules\Workers\Models\LabourAttendanceRecord;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Database\Seeders\CoreFoundationSeeder;
use Database\Seeders\UsersPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkersLabourModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_labour_dashboard(): void
    {
        $this->get(route('labour.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_user_without_labour_permission_cannot_access_labour_dashboard(): void
    {
        $this->seedAccess();
        $user = $this->memberWithRole('farm-hand');

        $this->actingAs($user)
            ->get(route('labour.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_can_access_labour_dashboard(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('labour.dashboard'))
            ->assertOk()
            ->assertSee('Labour dashboard');
    }

    public function test_labour_permissions_are_seeded(): void
    {
        $this->seedAccess();

        foreach ([
            'workers.view',
            'workers.create',
            'workers.update',
            'workers.deactivate',
            'workers.manage',
            'teams.view',
            'teams.create',
            'teams.update',
            'teams.deactivate',
            'attendance.view',
            'attendance.record',
            'attendance.update',
        ] as $permissionKey) {
            $this->assertDatabaseHas('permissions', ['key' => $permissionKey]);
        }

        $manager = Role::query()->where('key', 'farm-manager')->firstOrFail();
        $this->assertTrue($manager->permissions()->where('key', 'attendance.record')->exists());

        $auditor = Role::query()->where('key', 'auditor')->firstOrFail();
        $this->assertTrue($auditor->permissions()->where('key', 'workers.view')->exists());
        $this->assertFalse($auditor->permissions()->where('key', 'workers.create')->exists());
    }

    public function test_worker_can_be_created_updated_and_deactivated(): void
    {
        [$organization, $farm] = $this->coreScope();
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->post(route('labour.workers.store'), [
                'organization_id' => $organization->id,
                'farm_id' => $farm->id,
                'worker_code' => 'W-100',
                'name' => 'Test Worker',
                'employment_type' => 'full-time',
                'primary_role' => 'General labour',
                'status' => 'active',
                'start_date' => '2026-05-01',
                'rate_type' => 'daily',
                'default_rate' => 1000,
            ])
            ->assertRedirect(route('labour.workers.index'));

        $worker = LabourWorker::query()->where('worker_code', 'W-100')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('labour.workers.update', $worker), [
                'organization_id' => $organization->id,
                'farm_id' => $farm->id,
                'worker_code' => 'W-100',
                'name' => 'Updated Worker',
                'employment_type' => 'seasonal',
                'primary_role' => 'Irrigation hand',
                'status' => 'active',
                'rate_type' => 'daily',
                'default_rate' => 1100,
            ])
            ->assertRedirect(route('labour.workers.show', $worker));

        $this->assertDatabaseHas('labour_workers', [
            'id' => $worker->id,
            'name' => 'Updated Worker',
            'primary_role' => 'Irrigation hand',
        ]);

        $this->actingAs($admin)
            ->post(route('labour.workers.deactivate', $worker))
            ->assertRedirect(route('labour.workers.show', $worker));

        $this->assertDatabaseHas('labour_workers', [
            'id' => $worker->id,
            'status' => 'inactive',
        ]);
    }

    public function test_team_can_be_created_and_assigned_workers(): void
    {
        [$organization, $farm] = $this->coreScope();
        $admin = $this->adminUser();
        $worker = $this->worker($organization, $farm, 'W-200');

        $this->actingAs($admin)
            ->post(route('labour.teams.store'), [
                'organization_id' => $organization->id,
                'farm_id' => $farm->id,
                'name' => 'Harvest Crew',
                'code' => 'HC',
                'team_type' => 'harvest',
                'supervisor_worker_id' => $worker->id,
                'status' => 'active',
            ])
            ->assertRedirect();

        $team = LabourTeam::query()->where('name', 'Harvest Crew')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('labour.teams.workers.store', $team), [
                'labour_worker_id' => $worker->id,
            ])
            ->assertRedirect(route('labour.teams.show', $team));

        $this->assertDatabaseHas('labour_team_worker', [
            'labour_team_id' => $team->id,
            'labour_worker_id' => $worker->id,
        ]);
    }

    public function test_attendance_can_be_recorded(): void
    {
        [$organization, $farm] = $this->coreScope();
        $admin = $this->adminUser();
        $worker = $this->worker($organization, $farm, 'W-300');
        $team = LabourTeam::query()->create([
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'name' => 'Attendance Team',
            'team_type' => 'general',
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->post(route('labour.attendance.store'), [
                'organization_id' => $organization->id,
                'farm_id' => $farm->id,
                'labour_worker_id' => $worker->id,
                'labour_team_id' => $team->id,
                'date' => '2026-05-05',
                'status' => 'present',
                'check_in_at' => '08:00',
                'check_out_at' => '17:00',
                'hours_worked' => 8,
            ])
            ->assertRedirect(route('labour.attendance.index'));

        $record = LabourAttendanceRecord::query()->where('labour_worker_id', $worker->id)->firstOrFail();

        $this->assertSame($organization->id, $record->organization_id);
        $this->assertSame($farm->id, $record->farm_id);
        $this->assertSame($team->id, $record->labour_team_id);
        $this->assertSame('2026-05-05', $record->date->format('Y-m-d'));
        $this->assertSame('present', $record->status);
        $this->assertSame($admin->id, $record->recorded_by);
    }

    public function test_validation_rejects_missing_required_parent_records(): void
    {
        $this->actingAs($this->adminUser())
            ->post(route('labour.workers.store'), [
                'worker_code' => 'BAD-1',
                'name' => 'Invalid Worker',
                'employment_type' => 'full-time',
                'primary_role' => 'General',
                'status' => 'active',
            ])
            ->assertSessionHasErrors(['organization_id', 'farm_id']);

        $this->actingAs($this->adminUser())
            ->post(route('labour.attendance.store'), [
                'date' => '2026-05-05',
                'status' => 'present',
            ])
            ->assertSessionHasErrors(['organization_id', 'farm_id', 'labour_worker_id']);
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
