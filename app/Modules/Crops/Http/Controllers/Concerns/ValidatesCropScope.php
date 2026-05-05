<?php

namespace App\Modules\Crops\Http\Controllers\Concerns;

use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Crops\Models\Crop;
use App\Modules\Crops\Models\CropActivity;
use App\Modules\Crops\Models\CropCycle;
use App\Modules\Crops\Models\CropSeason;
use App\Modules\Crops\Models\CropVariety;
use App\Modules\Inventory\Models\InventoryProduct;
use App\Modules\Tasks\Models\OpsTask;
use App\Modules\Workers\Models\LabourTeam;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Support\Facades\DB;

trait ValidatesCropScope
{
    private function ensureFarmBelongsToOrganization(int $farmId, int $organizationId): void
    {
        $farm = Farm::query()->findOrFail($farmId);
        abort_unless((int) $farm->organization_id === $organizationId, 422);
    }

    private function ensureFieldBelongsToFarm(int $fieldId, int $farmId): Field
    {
        $field = Field::query()->findOrFail($fieldId);
        abort_unless((int) $field->farm_id === $farmId, 422);

        return $field;
    }

    private function ensureCropAvailableToOrganization(int $cropId, int $organizationId): Crop
    {
        $crop = Crop::query()->findOrFail($cropId);
        abort_unless($crop->organization_id === null || (int) $crop->organization_id === $organizationId, 422);

        return $crop;
    }

    private function ensureVarietyBelongsToCrop(?int $varietyId, int $cropId): ?CropVariety
    {
        if (! $varietyId) {
            return null;
        }

        $variety = CropVariety::query()->findOrFail($varietyId);
        abort_unless((int) $variety->crop_id === $cropId, 422);

        return $variety;
    }

    private function ensureSeasonBelongsToScope(?int $seasonId, int $organizationId, int $farmId): ?CropSeason
    {
        if (! $seasonId) {
            return null;
        }

        $season = CropSeason::query()->findOrFail($seasonId);
        abort_unless((int) $season->organization_id === $organizationId && ($season->farm_id === null || (int) $season->farm_id === $farmId), 422);

        return $season;
    }

    private function ensureCycleScope(CropCycle $cycle, int $organizationId, int $farmId): void
    {
        abort_unless((int) $cycle->organization_id === $organizationId && (int) $cycle->farm_id === $farmId, 422);
    }

    private function ensureTaskBelongsToScope(?int $taskId, int $organizationId, int $farmId): ?OpsTask
    {
        if (! $taskId) {
            return null;
        }

        $task = OpsTask::query()->findOrFail($taskId);
        abort_unless((int) $task->organization_id === $organizationId && (int) $task->farm_id === $farmId, 422);

        return $task;
    }

    private function ensureProductBelongsToOrganization(?int $productId, int $organizationId): ?InventoryProduct
    {
        if (! $productId) {
            return null;
        }

        $product = InventoryProduct::query()->findOrFail($productId);
        abort_unless((int) $product->organization_id === $organizationId, 422);

        return $product;
    }

    private function ensureActivityBelongsToCycle(?int $activityId, CropCycle $cycle): ?CropActivity
    {
        if (! $activityId) {
            return null;
        }

        $activity = CropActivity::query()->findOrFail($activityId);
        abort_unless((int) $activity->crop_cycle_id === (int) $cycle->id, 422);

        return $activity;
    }

    private function ensureWorkerBelongsToFarm(?int $workerId, int $farmId): void
    {
        if ($workerId) {
            abort_unless((int) LabourWorker::query()->findOrFail($workerId)->farm_id === $farmId, 422);
        }
    }

    private function ensureTeamBelongsToFarm(?int $teamId, int $farmId): void
    {
        if ($teamId) {
            abort_unless((int) LabourTeam::query()->findOrFail($teamId)->farm_id === $farmId, 422);
        }
    }

    private function nextCycleNumber(): string
    {
        $year = now()->format('Y');
        $count = DB::table('crop_cycles')->where('cycle_number', 'like', 'CYCLE-'.$year.'-%')->count() + 1;

        return 'CYCLE-'.$year.'-'.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
