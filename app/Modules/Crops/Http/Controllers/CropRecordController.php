<?php

namespace App\Modules\Crops\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Crops\Http\Controllers\Concerns\ValidatesCropScope;
use App\Modules\Crops\Models\CropActivity;
use App\Modules\Crops\Models\CropCycle;
use App\Modules\Crops\Models\CropHarvestRecord;
use App\Modules\Crops\Models\CropLossRecord;
use App\Modules\Crops\Models\CropPlantingDetail;
use App\Modules\Crops\Models\CropScoutingObservation;
use App\Modules\Crops\Models\CropTreatmentApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CropRecordController extends Controller
{
    use ValidatesCropScope;

    public function storeActivity(Request $request, CropCycle $cycle): RedirectResponse
    {
        $data = $request->validate([
            'task_id' => ['nullable', 'integer', Rule::exists('ops_tasks', 'id')],
            'performed_by_user_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'worker_id' => ['nullable', 'integer', Rule::exists('labour_workers', 'id')],
            'team_id' => ['nullable', 'integer', Rule::exists('labour_teams', 'id')],
            'activity_type' => ['required', Rule::in(['land_preparation', 'planting', 'transplanting', 'fertilizer_application', 'spray_application', 'scouting', 'weeding', 'pruning', 'irrigation_note', 'harvest', 'loss', 'general_observation', 'other'])],
            'activity_date' => ['required', 'date'],
            'status' => ['nullable', Rule::in(['recorded', 'approved'])],
            'notes' => ['nullable', 'string', 'max:2000'],
            'planting_method' => ['nullable', 'string', 'max:255'],
            'seed_quantity' => ['nullable', 'numeric', 'min:0'],
            'seed_unit' => ['nullable', 'string', 'max:255'],
            'plant_population' => ['nullable', 'integer', 'min:0'],
            'spacing' => ['nullable', 'string', 'max:255'],
            'nursery_source' => ['nullable', 'string', 'max:255'],
            'planting_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->ensureTaskBelongsToScope($data['task_id'] ?? null, $cycle->organization_id, $cycle->farm_id);
        $this->ensureWorkerBelongsToFarm($data['worker_id'] ?? null, $cycle->farm_id);
        $this->ensureTeamBelongsToFarm($data['team_id'] ?? null, $cycle->farm_id);

        $activity = CropActivity::query()->create([
            'organization_id' => $cycle->organization_id,
            'farm_id' => $cycle->farm_id,
            'crop_cycle_id' => $cycle->id,
            'field_id' => $cycle->field_id,
            'task_id' => $data['task_id'] ?? null,
            'performed_by_user_id' => $data['performed_by_user_id'] ?? null,
            'worker_id' => $data['worker_id'] ?? null,
            'team_id' => $data['team_id'] ?? null,
            'activity_type' => $data['activity_type'],
            'activity_date' => $data['activity_date'],
            'status' => $data['status'] ?? 'recorded',
            'notes' => $data['notes'] ?? null,
        ]);

        if (in_array($activity->activity_type, ['planting', 'transplanting'], true)) {
            CropPlantingDetail::query()->create([
                'crop_activity_id' => $activity->id,
                'planting_method' => $data['planting_method'] ?? null,
                'seed_quantity' => $data['seed_quantity'] ?? null,
                'seed_unit' => $data['seed_unit'] ?? null,
                'plant_population' => $data['plant_population'] ?? null,
                'spacing' => $data['spacing'] ?? null,
                'nursery_source' => $data['nursery_source'] ?? null,
                'notes' => $data['planting_notes'] ?? null,
            ]);
        }

        return redirect()->route('crops.cycles.show', $cycle)->with('status', 'Activity recorded.');
    }

    public function storeScouting(Request $request, CropCycle $cycle): RedirectResponse
    {
        $data = $request->validate([
            'crop_activity_id' => ['nullable', 'integer', Rule::exists('crop_activities', 'id')],
            'observation_date' => ['required', 'date'],
            'observation_type' => ['required', 'string', 'max:255'],
            'severity' => ['nullable', 'string', 'max:255'],
            'affected_area' => ['nullable', 'numeric', 'min:0'],
            'affected_area_unit' => ['nullable', 'string', 'max:255'],
            'pest_or_disease' => ['nullable', 'string', 'max:255'],
            'symptoms' => ['nullable', 'string', 'max:2000'],
            'recommendation' => ['nullable', 'string', 'max:2000'],
            'observed_by' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $this->ensureActivityBelongsToCycle($data['crop_activity_id'] ?? null, $cycle);

        CropScoutingObservation::query()->create($data + [
            'organization_id' => $cycle->organization_id,
            'farm_id' => $cycle->farm_id,
            'crop_cycle_id' => $cycle->id,
            'field_id' => $cycle->field_id,
        ]);

        return redirect()->route('crops.cycles.show', $cycle)->with('status', 'Scouting observation recorded.');
    }

    public function storeTreatment(Request $request, CropCycle $cycle): RedirectResponse
    {
        $data = $request->validate([
            'crop_activity_id' => ['nullable', 'integer', Rule::exists('crop_activities', 'id')],
            'task_id' => ['nullable', 'integer', Rule::exists('ops_tasks', 'id')],
            'product_id' => ['nullable', 'integer', Rule::exists('inventory_products', 'id')],
            'application_type' => ['required', 'string', 'max:255'],
            'application_date' => ['required', 'date'],
            'quantity_used' => ['nullable', 'numeric', 'min:0'],
            'quantity_unit' => ['nullable', 'string', 'max:255'],
            'application_rate' => ['nullable', 'string', 'max:255'],
            'target_problem' => ['nullable', 'string', 'max:255'],
            'method' => ['nullable', 'string', 'max:255'],
            'weather_notes' => ['nullable', 'string', 'max:2000'],
            'phi_days' => ['nullable', 'integer', 'min:0'],
            'rei_hours' => ['nullable', 'integer', 'min:0'],
            'applied_by' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $this->ensureActivityBelongsToCycle($data['crop_activity_id'] ?? null, $cycle);
        $this->ensureTaskBelongsToScope($data['task_id'] ?? null, $cycle->organization_id, $cycle->farm_id);
        $product = $this->ensureProductBelongsToOrganization($data['product_id'] ?? null, $cycle->organization_id);

        CropTreatmentApplication::query()->create($data + [
            'organization_id' => $cycle->organization_id,
            'farm_id' => $cycle->farm_id,
            'crop_cycle_id' => $cycle->id,
            'field_id' => $cycle->field_id,
            'product_name_snapshot' => $product?->name,
            'product_code_snapshot' => $product?->code,
        ]);

        return redirect()->route('crops.cycles.show', $cycle)->with('status', 'Treatment recorded.');
    }

    public function storeHarvest(Request $request, CropCycle $cycle): RedirectResponse
    {
        $data = $request->validate([
            'crop_activity_id' => ['nullable', 'integer', Rule::exists('crop_activities', 'id')],
            'task_id' => ['nullable', 'integer', Rule::exists('ops_tasks', 'id')],
            'harvest_date' => ['required', 'date'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'unit' => ['required', 'string', 'max:255'],
            'grade' => ['nullable', 'string', 'max:255'],
            'destination' => ['nullable', 'string', 'max:255'],
            'harvested_by' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $this->ensureActivityBelongsToCycle($data['crop_activity_id'] ?? null, $cycle);
        $this->ensureTaskBelongsToScope($data['task_id'] ?? null, $cycle->organization_id, $cycle->farm_id);

        CropHarvestRecord::query()->create($data + [
            'organization_id' => $cycle->organization_id,
            'farm_id' => $cycle->farm_id,
            'crop_cycle_id' => $cycle->id,
            'field_id' => $cycle->field_id,
        ]);

        return redirect()->route('crops.cycles.show', $cycle)->with('status', 'Harvest recorded.');
    }

    public function storeLoss(Request $request, CropCycle $cycle): RedirectResponse
    {
        $data = $request->validate([
            'crop_activity_id' => ['nullable', 'integer', Rule::exists('crop_activities', 'id')],
            'loss_date' => ['required', 'date'],
            'loss_type' => ['required', 'string', 'max:255'],
            'estimated_quantity' => ['nullable', 'numeric', 'min:0'],
            'quantity_unit' => ['nullable', 'string', 'max:255'],
            'affected_area' => ['nullable', 'numeric', 'min:0'],
            'affected_area_unit' => ['nullable', 'string', 'max:255'],
            'cause' => ['nullable', 'string', 'max:255'],
            'severity' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $this->ensureActivityBelongsToCycle($data['crop_activity_id'] ?? null, $cycle);

        CropLossRecord::query()->create($data + [
            'organization_id' => $cycle->organization_id,
            'farm_id' => $cycle->farm_id,
            'crop_cycle_id' => $cycle->id,
            'field_id' => $cycle->field_id,
        ]);

        return redirect()->route('crops.cycles.show', $cycle)->with('status', 'Loss recorded.');
    }
}
