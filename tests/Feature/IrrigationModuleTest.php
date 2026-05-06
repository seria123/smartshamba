<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Site;
use App\Modules\Crops\Models\Crop;
use App\Modules\Crops\Models\CropCycle;
use App\Modules\Irrigation\Models\IrrigationEvent;
use App\Modules\Irrigation\Models\IrrigationIssue;
use App\Modules\Irrigation\Models\IrrigationSchedule;
use App\Modules\Irrigation\Models\IrrigationWaterReading;
use App\Modules\Irrigation\Models\IrrigationWaterSource;
use App\Modules\Irrigation\Models\IrrigationZone;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\UsersPermissions\Models\OrganizationMembership;
use App\Modules\UsersPermissions\Models\Role;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Database\Seeders\CoreFoundationSeeder;
use Database\Seeders\UsersPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IrrigationModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_irrigation_dashboard(): void
    {
        $this->get('/admin/irrigation')->assertRedirect(route('login'));
    }

    public function test_user_without_irrigation_permission_cannot_access_irrigation_dashboard(): void
    {
        $this->seedAccess();
        $user = $this->memberWithRole('farm-hand');

        $this->actingAs($user)->get(route('irrigation.dashboard'))->assertForbidden();
    }

    public function test_admin_can_access_irrigation_dashboard(): void
    {
        $this->actingAs($this->adminUser())->get(route('irrigation.dashboard'))->assertOk()->assertSee('Irrigation dashboard');
    }

    public function test_irrigation_permissions_are_seeded(): void
    {
        $this->seedAccess();

        foreach (['irrigation.view', 'irrigation.manage', 'water-sources.view', 'water-sources.create', 'water-sources.update', 'water-sources.deactivate', 'irrigation-zones.view', 'irrigation-zones.create', 'irrigation-zones.update', 'irrigation-zones.deactivate', 'irrigation-schedules.view', 'irrigation-schedules.create', 'irrigation-schedules.update', 'irrigation-schedules.cancel', 'irrigation-events.view', 'irrigation-events.create', 'irrigation-events.update', 'irrigation-events.cancel', 'water-readings.view', 'water-readings.create', 'irrigation-issues.view', 'irrigation-issues.create', 'irrigation-issues.update', 'irrigation-issues.resolve'] as $key) {
            $this->assertDatabaseHas('permissions', ['key' => $key]);
        }

        $manager = Role::query()->where('key', 'farm-manager')->firstOrFail();
        $agronomist = Role::query()->where('key', 'agronomist')->firstOrFail();
        $auditor = Role::query()->where('key', 'auditor')->firstOrFail();
        $this->assertTrue($manager->permissions()->where('key', 'irrigation-issues.resolve')->exists());
        $this->assertTrue($agronomist->permissions()->where('key', 'irrigation-events.create')->exists());
        $this->assertTrue($auditor->permissions()->where('key', 'irrigation-events.view')->exists());
        $this->assertFalse($auditor->permissions()->where('key', 'irrigation-events.create')->exists());
    }

    public function test_water_source_can_be_created_updated_and_deactivated(): void
    {
        [$organization, $farm] = $this->scope();
        $admin = $this->adminUser();

        $payload = ['organization_id' => $organization->id, 'farm_id' => $farm->id, 'name' => 'Main Borehole', 'code' => 'BH-1', 'source_type' => 'borehole', 'status' => 'active'];
        $this->actingAs($admin)->post(route('irrigation.water-sources.store'), $payload)->assertRedirect(route('irrigation.water-sources.index'));
        $source = IrrigationWaterSource::query()->where('code', 'BH-1')->firstOrFail();

        $this->actingAs($admin)->put(route('irrigation.water-sources.update', $source), $payload + ['name' => 'Updated Borehole'])->assertRedirect(route('irrigation.water-sources.show', $source));
        $this->actingAs($admin)->post(route('irrigation.water-sources.deactivate', $source))->assertRedirect(route('irrigation.water-sources.show', $source));
        $this->assertSame('inactive', $source->fresh()->status);
    }

    public function test_zone_can_be_created_and_rejects_field_or_source_from_another_farm(): void
    {
        [$organization, $farm, $field, $site] = $this->scope();
        $admin = $this->adminUser();
        $source = $this->source($organization, $farm);
        $otherFarm = Farm::query()->create(['organization_id' => $organization->id, 'name' => 'Other Farm', 'status' => 'active']);
        $otherField = Field::query()->create(['organization_id' => $organization->id, 'farm_id' => $otherFarm->id, 'name' => 'Other Field', 'status' => 'active']);
        $otherSource = $this->source($organization, $otherFarm, 'OTHER-SRC');

        $this->actingAs($admin)->post(route('irrigation.zones.store'), $this->zonePayload($organization, $farm, $field, $site, $source))->assertRedirect(route('irrigation.zones.index'));
        $this->actingAs($admin)->post(route('irrigation.zones.store'), array_merge($this->zonePayload($organization, $farm, $field, $site, $source, 'BAD-FIELD'), ['field_id' => $otherField->id]))->assertSessionHasErrors('field_id');
        $this->actingAs($admin)->post(route('irrigation.zones.store'), array_merge($this->zonePayload($organization, $farm, $field, $site, $source, 'BAD-SOURCE'), ['water_source_id' => $otherSource->id]))->assertSessionHasErrors('water_source_id');
    }

    public function test_schedule_can_be_created_updated_cancelled_and_completed(): void
    {
        $admin = $this->adminUser();
        $zone = $this->zone();

        $this->actingAs($admin)->post(route('irrigation.schedules.store'), $this->schedulePayload($zone))->assertRedirect(route('irrigation.schedules.index'));
        $schedule = IrrigationSchedule::query()->firstOrFail();
        $this->actingAs($admin)->put(route('irrigation.schedules.update', $schedule), array_merge($this->schedulePayload($zone), ['priority' => 'high']))->assertRedirect(route('irrigation.schedules.show', $schedule));
        $this->assertSame('high', $schedule->fresh()->priority);
        $this->actingAs($admin)->post(route('irrigation.schedules.cancel', $schedule), ['cancellation_reason' => 'Rain'])->assertRedirect(route('irrigation.schedules.show', $schedule));
        $this->assertSame('cancelled', $schedule->fresh()->status);
        $this->actingAs($admin)->post(route('irrigation.schedules.complete', $schedule))->assertRedirect(route('irrigation.schedules.show', $schedule));
        $this->assertSame('completed', $schedule->fresh()->status);
    }

    public function test_schedule_rejects_wrong_farm_scope_links_and_user_scope(): void
    {
        [$organization, $farm, $field] = $this->scope();
        $admin = $this->adminUser();
        $zone = $this->zone();
        $otherFarm = Farm::query()->create(['organization_id' => $organization->id, 'name' => 'Other Schedule Farm', 'status' => 'active']);
        $otherZone = $this->zone($otherFarm, 'OTHER-ZONE');
        $otherCycle = $this->cycle($otherFarm);
        $otherTask = OpsTask::query()->create(['organization_id' => $organization->id, 'farm_id' => $otherFarm->id, 'task_number' => 'TASK-IRR-OTHER', 'title' => 'Other irrigation task', 'category' => 'irrigation', 'priority' => 'normal', 'status' => 'draft']);
        $otherWorker = LabourWorker::query()->create(['organization_id' => $organization->id, 'farm_id' => $otherFarm->id, 'worker_code' => 'OTH-W', 'name' => 'Other Worker', 'employment_type' => 'casual', 'primary_role' => 'irrigation', 'status' => 'active']);
        $otherTeam = LabourTeam::query()->create(['organization_id' => $organization->id, 'farm_id' => $otherFarm->id, 'name' => 'Other Team', 'code' => 'OTH-T', 'team_type' => 'irrigation', 'status' => 'active']);
        $outsider = User::query()->create(['name' => 'Outsider', 'email' => 'outsider@test.test', 'password' => 'password', 'status' => 'active']);

        $this->actingAs($admin)->post(route('irrigation.schedules.store'), array_merge($this->schedulePayload($zone), ['irrigation_zone_id' => $otherZone->id]))->assertSessionHasErrors('irrigation_zone_id');
        $this->actingAs($admin)->post(route('irrigation.schedules.store'), array_merge($this->schedulePayload($zone), ['crop_cycle_id' => $otherCycle->id]))->assertSessionHasErrors('crop_cycle_id');
        $this->actingAs($admin)->post(route('irrigation.schedules.store'), array_merge($this->schedulePayload($zone), ['related_task_id' => $otherTask->id]))->assertSessionHasErrors('related_task_id');
        $this->actingAs($admin)->post(route('irrigation.schedules.store'), array_merge($this->schedulePayload($zone), ['assigned_worker_id' => $otherWorker->id]))->assertSessionHasErrors('assigned_worker_id');
        $this->actingAs($admin)->post(route('irrigation.schedules.store'), array_merge($this->schedulePayload($zone), ['assigned_team_id' => $otherTeam->id]))->assertSessionHasErrors('assigned_team_id');
        $this->actingAs($admin)->post(route('irrigation.schedules.store'), array_merge($this->schedulePayload($zone), ['assigned_user_id' => $outsider->id]))->assertSessionHasErrors('assigned_user_id');

        $this->assertSame($farm->id, $field->farm_id);
    }

    public function test_event_can_be_recorded_and_rejects_wrong_farm_links(): void
    {
        [$organization] = $this->scope();
        $admin = $this->adminUser();
        $zone = $this->zone();
        $otherFarm = Farm::query()->create(['organization_id' => $organization->id, 'name' => 'Other Event Farm', 'status' => 'active']);
        $otherSource = $this->source($organization, $otherFarm, 'EVT-OTHER-SRC');

        $this->actingAs($admin)->post(route('irrigation.events.store'), $this->eventPayload($zone))->assertRedirect(route('irrigation.events.index'));
        $this->assertSame(1, IrrigationEvent::query()->count());
        $this->actingAs($admin)->post(route('irrigation.events.store'), array_merge($this->eventPayload($zone), ['water_source_id' => $otherSource->id]))->assertSessionHasErrors('water_source_id');
    }

    public function test_reading_requires_source_or_zone(): void
    {
        [$organization, $farm] = $this->scope();
        $admin = $this->adminUser();
        $source = $this->source($organization, $farm);

        $payload = ['organization_id' => $organization->id, 'farm_id' => $farm->id, 'reading_date' => '2026-05-01', 'reading_type' => 'meter_reading', 'value' => 100, 'unit_of_measure' => 'litres'];
        $this->actingAs($admin)->post(route('irrigation.readings.store'), $payload)->assertSessionHasErrors('water_source_id');
        $this->actingAs($admin)->post(route('irrigation.readings.store'), $payload + ['water_source_id' => $source->id])->assertRedirect(route('irrigation.readings.index'));
        $this->assertSame(1, IrrigationWaterReading::query()->count());
    }

    public function test_issue_can_be_created_and_resolved_with_scope_validation(): void
    {
        [$organization, $farm, $field] = $this->scope();
        $admin = $this->adminUser();
        $zone = $this->zone();
        $otherFarm = Farm::query()->create(['organization_id' => $organization->id, 'name' => 'Other Issue Farm', 'status' => 'active']);
        $otherField = Field::query()->create(['organization_id' => $organization->id, 'farm_id' => $otherFarm->id, 'name' => 'Issue Other Field', 'status' => 'active']);

        $payload = ['organization_id' => $organization->id, 'farm_id' => $farm->id, 'irrigation_zone_id' => $zone->id, 'field_id' => $field->id, 'issue_date' => '2026-05-01', 'issue_type' => 'low_pressure', 'severity' => 'medium', 'status' => 'open', 'description' => 'Pressure dropped.'];
        $this->actingAs($admin)->post(route('irrigation.issues.store'), $payload)->assertRedirect(route('irrigation.issues.index'));
        $issue = IrrigationIssue::query()->firstOrFail();
        $this->actingAs($admin)->post(route('irrigation.issues.resolve', $issue), ['resolution_notes' => 'Flushed line.'])->assertRedirect(route('irrigation.issues.show', $issue));
        $this->assertSame('resolved', $issue->fresh()->status);
        $this->actingAs($admin)->post(route('irrigation.issues.store'), array_merge($payload, ['field_id' => $otherField->id]))->assertSessionHasErrors('field_id');
    }

    private function seedAccess(): void { $this->seed(CoreFoundationSeeder::class); $this->seed(UsersPermissionsSeeder::class); }
    private function adminUser(): User { $this->seedAccess(); return User::query()->where('email', 'admin@smartshamba.test')->firstOrFail(); }
    private function scope(): array { $this->seedAccess(); $organization = Organization::firstOrFail(); $farm = Farm::where('organization_id', $organization->id)->firstOrFail(); return [$organization, $farm, Field::where('farm_id', $farm->id)->firstOrFail(), Site::where('farm_id', $farm->id)->firstOrFail()]; }

    private function memberWithRole(string $roleKey): User
    {
        $organization = Organization::firstOrFail();
        $role = Role::where('key', $roleKey)->firstOrFail();
        $user = User::query()->create(['name' => 'Scoped Member', 'email' => $roleKey.'@smartshamba.test', 'password' => 'password123', 'status' => 'active']);
        OrganizationMembership::query()->create(['organization_id' => $organization->id, 'user_id' => $user->id, 'role_id' => $role->id, 'role_key' => $role->key, 'status' => 'active', 'joined_at' => now()]);
        return $user;
    }

    private function source(Organization $organization, Farm $farm, string $code = 'SRC-1'): IrrigationWaterSource
    {
        return IrrigationWaterSource::query()->create(['organization_id' => $organization->id, 'farm_id' => $farm->id, 'name' => 'Source '.$code, 'code' => $code, 'source_type' => 'borehole', 'status' => 'active']);
    }

    private function zone(?Farm $farm = null, string $code = 'ZONE-1'): IrrigationZone
    {
        [$organization, $defaultFarm, $field, $site] = $this->scope();
        $farm ??= $defaultFarm;
        $field = Field::where('farm_id', $farm->id)->first() ?? Field::query()->create(['organization_id' => $organization->id, 'farm_id' => $farm->id, 'name' => 'Field '.$code, 'status' => 'active']);
        $site = Site::where('farm_id', $farm->id)->first();
        $source = $this->source($organization, $farm, 'SRC-'.$code);
        return IrrigationZone::query()->create($this->zonePayload($organization, $farm, $field, $site, $source, $code));
    }

    private function zonePayload(Organization $organization, Farm $farm, Field $field, ?Site $site, IrrigationWaterSource $source, string $code = 'ZONE-1'): array
    {
        return ['organization_id' => $organization->id, 'farm_id' => $farm->id, 'site_id' => $site?->id, 'field_id' => $field->id, 'water_source_id' => $source->id, 'name' => 'Zone '.$code, 'code' => $code, 'zone_type' => 'field_zone', 'irrigation_method' => 'drip', 'status' => 'active'];
    }

    private function schedulePayload(IrrigationZone $zone): array
    {
        return ['organization_id' => $zone->organization_id, 'farm_id' => $zone->farm_id, 'irrigation_zone_id' => $zone->id, 'field_id' => $zone->field_id, 'scheduled_date' => '2026-05-01', 'priority' => 'normal', 'status' => 'planned'];
    }

    private function eventPayload(IrrigationZone $zone): array
    {
        return ['organization_id' => $zone->organization_id, 'farm_id' => $zone->farm_id, 'irrigation_zone_id' => $zone->id, 'water_source_id' => $zone->water_source_id, 'field_id' => $zone->field_id, 'irrigation_date' => '2026-05-01', 'duration_minutes' => 45, 'water_volume' => 1000, 'water_volume_unit' => 'litres', 'method' => 'drip', 'status' => 'recorded'];
    }

    private function cycle(Farm $farm): CropCycle
    {
        $crop = Crop::query()->firstOrCreate(['organization_id' => null, 'code' => 'IRR-CROP'], ['name' => 'Irrigation Crop', 'crop_type' => 'test', 'status' => 'active']);
        $field = Field::where('farm_id', $farm->id)->first() ?? Field::query()->create(['organization_id' => $farm->organization_id, 'farm_id' => $farm->id, 'name' => 'Cycle Field', 'status' => 'active']);
        return CropCycle::query()->create(['organization_id' => $farm->organization_id, 'farm_id' => $farm->id, 'field_id' => $field->id, 'crop_id' => $crop->id, 'cycle_number' => 'IRR-CYCLE-'.uniqid(), 'name' => 'Irrigation Cycle', 'status' => 'planned']);
    }
}
