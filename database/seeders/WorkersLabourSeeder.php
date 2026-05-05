<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Workers\Models\LabourAttendanceRecord;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Database\Seeder;

class WorkersLabourSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $organization = Organization::query()->first();
        $farm = Farm::query()->where('organization_id', $organization?->id)->first();

        if (! $organization || ! $farm) {
            return;
        }

        $recorder = User::query()->where('email', 'admin@smartshamba.test')->first();

        $worker = LabourWorker::query()->updateOrCreate(
            [
                'farm_id' => $farm->id,
                'worker_code' => 'LAB-001',
            ],
            [
                'organization_id' => $organization->id,
                'user_id' => null,
                'name' => 'Demo Farm Worker',
                'phone' => '+254700000001',
                'email' => null,
                'employment_type' => 'full-time',
                'primary_role' => 'General farm hand',
                'status' => 'active',
                'start_date' => now()->toDateString(),
                'rate_type' => 'daily',
                'default_rate' => 1200,
                'notes' => 'Local/testing demo labour record.',
            ],
        );

        $team = LabourTeam::query()->updateOrCreate(
            [
                'farm_id' => $farm->id,
                'name' => 'Demo Labour Team',
            ],
            [
                'organization_id' => $organization->id,
                'code' => 'TEAM-001',
                'team_type' => 'general',
                'supervisor_worker_id' => $worker->id,
                'status' => 'active',
                'notes' => 'Local/testing demo labour team.',
            ],
        );

        $team->workers()->syncWithoutDetaching([
            $worker->id => ['joined_at' => now()],
        ]);

        LabourAttendanceRecord::query()->updateOrCreate(
            [
                'labour_worker_id' => $worker->id,
                'date' => now()->toDateString(),
            ],
            [
                'organization_id' => $organization->id,
                'farm_id' => $farm->id,
                'labour_team_id' => $team->id,
                'status' => 'present',
                'check_in_at' => '08:00',
                'check_out_at' => '17:00',
                'hours_worked' => 8,
                'recorded_by' => $recorder?->id,
                'notes' => 'Local/testing demo attendance.',
            ],
        );
    }
}
