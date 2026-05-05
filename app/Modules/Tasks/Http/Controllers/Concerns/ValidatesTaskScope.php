<?php

namespace App\Modules\Tasks\Http\Controllers\Concerns;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Paddock;
use App\Modules\Core\Models\Site;
use App\Modules\Core\Models\Warehouse;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\Tasks\Models\OpsWorkOrder;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;

trait ValidatesTaskScope
{
    private function ensureFarmBelongsToOrganization(int $farmId, int $organizationId): void
    {
        $farm = Farm::query()->findOrFail($farmId);

        abort_unless((int) $farm->organization_id === $organizationId, 422);
    }

    private function ensureLocationsBelongToFarm(array $data): void
    {
        $farmId = (int) $data['farm_id'];

        $checks = [
            'site_id' => Site::class,
            'field_id' => Field::class,
            'paddock_id' => Paddock::class,
            'warehouse_id' => Warehouse::class,
        ];

        foreach ($checks as $key => $model) {
            if (! ($data[$key] ?? null)) {
                continue;
            }

            $record = $model::query()->findOrFail($data[$key]);
            abort_unless((int) $record->farm_id === $farmId, 422);
        }
    }

    private function ensureWorkOrderBelongsToFarm(?int $workOrderId, int $farmId): ?OpsWorkOrder
    {
        if (! $workOrderId) {
            return null;
        }

        $workOrder = OpsWorkOrder::query()->findOrFail($workOrderId);
        abort_unless((int) $workOrder->farm_id === $farmId, 422);

        return $workOrder;
    }

    private function ensureTaskBelongsToFarm(OpsTask $task, int $farmId): void
    {
        abort_unless((int) $task->farm_id === $farmId, 422);
    }

    private function ensureWorkerBelongsToFarm(?int $workerId, int $farmId): ?LabourWorker
    {
        if (! $workerId) {
            return null;
        }

        $worker = LabourWorker::query()->findOrFail($workerId);
        abort_unless((int) $worker->farm_id === $farmId, 422);

        return $worker;
    }

    private function ensureTeamBelongsToFarm(?int $teamId, int $farmId): ?LabourTeam
    {
        if (! $teamId) {
            return null;
        }

        $team = LabourTeam::query()->findOrFail($teamId);
        abort_unless((int) $team->farm_id === $farmId, 422);

        return $team;
    }

    private function ensureUserHasOrganizationAccess(?int $userId, int $organizationId): ?User
    {
        if (! $userId) {
            return null;
        }

        $user = User::query()->findOrFail($userId);
        abort_unless($user->memberships()->where('organization_id', $organizationId)->where('status', 'active')->exists(), 422);

        return $user;
    }

    private function ensureUserHasTaskFarmAccess(?int $userId, int $organizationId, int $farmId): ?User
    {
        if (! $userId) {
            return null;
        }

        $user = User::query()->findOrFail($userId);

        abort_unless(
            $user->memberships()
                ->where('organization_id', $organizationId)
                ->where('status', 'active')
                ->where(function ($query) use ($farmId): void {
                    $query->where('farm_id', $farmId)
                        ->orWhereNull('farm_id');
                })
                ->exists(),
            422,
        );

        return $user;
    }

    private function nextNumber(string $prefix, string $table, string $column): string
    {
        $year = now()->format('Y');
        $count = \DB::table($table)->where($column, 'like', $prefix.'-'.$year.'-%')->count() + 1;

        return $prefix.'-'.$year.'-'.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
