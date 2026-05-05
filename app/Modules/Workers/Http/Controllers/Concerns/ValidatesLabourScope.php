<?php

namespace App\Modules\Workers\Http\Controllers\Concerns;

use App\Modules\Core\Models\Farm;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;

trait ValidatesLabourScope
{
    private function ensureFarmBelongsToOrganization(int $farmId, int $organizationId): void
    {
        $farm = Farm::query()->findOrFail($farmId);

        abort_unless((int) $farm->organization_id === $organizationId, 422);
    }

    private function ensureWorkerBelongsToFarm(int $workerId, int $farmId): LabourWorker
    {
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
}
