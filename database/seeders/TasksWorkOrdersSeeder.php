<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Paddock;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\Tasks\Models\OpsWorkOrder;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Database\Seeder;

class TasksWorkOrdersSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $organization = Organization::query()->first();
        $farm = Farm::query()->where('organization_id', $organization?->id)->first();
        $user = User::query()->where('email', 'admin@smartshamba.test')->first();

        if (! $organization || ! $farm || ! $user) {
            return;
        }

        $field = Field::query()->where('farm_id', $farm->id)->first();
        $paddock = Paddock::query()->where('farm_id', $farm->id)->first();
        $worker = LabourWorker::query()->where('farm_id', $farm->id)->first();
        $team = LabourTeam::query()->where('farm_id', $farm->id)->first();

        $tomatoOrder = OpsWorkOrder::query()->updateOrCreate(
            ['work_order_number' => 'WO-2026-0001'],
            [
                'organization_id' => $organization->id,
                'farm_id' => $farm->id,
                'title' => 'Prepare Tomato Block B for planting',
                'description' => 'Demo local/testing work order for task planning.',
                'category' => 'crop',
                'priority' => 'normal',
                'status' => 'planned',
                'field_id' => $field?->id,
                'created_by' => $user->id,
                'start_date' => now()->toDateString(),
                'due_date' => now()->addDays(7)->toDateString(),
            ],
        );

        foreach (['Clear weeds', 'Apply manure', 'Prepare beds'] as $index => $title) {
            $task = OpsTask::query()->updateOrCreate(
                ['task_number' => 'TASK-2026-000'.($index + 1)],
                [
                    'organization_id' => $organization->id,
                    'farm_id' => $farm->id,
                    'work_order_id' => $tomatoOrder->id,
                    'title' => $title,
                    'category' => 'crop',
                    'priority' => 'normal',
                    'status' => 'assigned',
                    'field_id' => $field?->id,
                    'assigned_by' => $user->id,
                    'created_by' => $user->id,
                    'due_date' => now()->addDays(7)->toDateString(),
                ],
            );

            if ($worker) {
                $task->assignments()->updateOrCreate(
                    ['worker_id' => $worker->id, 'assignment_type' => 'worker'],
                    [
                        'organization_id' => $organization->id,
                        'farm_id' => $farm->id,
                        'status' => 'assigned',
                        'assigned_by' => $user->id,
                        'assigned_at' => now(),
                    ],
                );
            }
        }

        $cleaningOrder = OpsWorkOrder::query()->updateOrCreate(
            ['work_order_number' => 'WO-2026-0002'],
            [
                'organization_id' => $organization->id,
                'farm_id' => $farm->id,
                'title' => 'Clean Poultry House A',
                'category' => 'cleaning',
                'priority' => 'high',
                'status' => 'planned',
                'paddock_id' => $paddock?->id,
                'created_by' => $user->id,
                'due_date' => now()->addDays(3)->toDateString(),
            ],
        );

        $cleaningTask = OpsTask::query()->updateOrCreate(
            ['task_number' => 'TASK-2026-0004'],
            [
                'organization_id' => $organization->id,
                'farm_id' => $farm->id,
                'work_order_id' => $cleaningOrder->id,
                'title' => 'Disinfect floor',
                'category' => 'cleaning',
                'priority' => 'high',
                'status' => 'assigned',
                'paddock_id' => $paddock?->id,
                'assigned_by' => $user->id,
                'created_by' => $user->id,
                'due_date' => now()->addDays(3)->toDateString(),
            ],
        );

        if ($team) {
            $cleaningTask->assignments()->updateOrCreate(
                ['team_id' => $team->id, 'assignment_type' => 'team'],
                [
                    'organization_id' => $organization->id,
                    'farm_id' => $farm->id,
                    'status' => 'assigned',
                    'assigned_by' => $user->id,
                    'assigned_at' => now(),
                ],
            );
        }
    }
}
