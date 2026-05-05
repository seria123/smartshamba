<?php

namespace App\Modules\Crops\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Crops\Http\Controllers\Concerns\ValidatesCropScope;
use App\Modules\Crops\Models\Crop;
use App\Modules\Crops\Models\CropCycle;
use App\Modules\Crops\Models\CropSeason;
use App\Modules\Crops\Models\CropVariety;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CropCycleController extends Controller
{
    use ValidatesCropScope;

    public function index(): View
    {
        return view('crops::cycles.index', ['cycles' => CropCycle::with(['farm', 'field', 'crop', 'variety', 'season'])->latest()->paginate(20)]);
    }

    public function create(): View { return view('crops::cycles.form', $this->formData()); }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['cycle_number'] = $this->nextCycleNumber();
        $cycle = CropCycle::query()->create($data);

        return redirect()->route('crops.cycles.show', $cycle)->with('status', 'Crop cycle created.');
    }

    public function show(CropCycle $cycle): View
    {
        return view('crops::cycles.show', [
            'cycle' => $cycle->load([
                'organization', 'farm', 'field', 'crop', 'variety', 'season', 'manager',
                'activities.task', 'activities.worker', 'activities.team', 'activities.plantingDetail',
                'scoutingObservations', 'treatments.product', 'harvests', 'losses',
            ]),
            'tasks' => \App\Modules\Tasks\Models\OpsTask::query()->where('farm_id', $cycle->farm_id)->orderByDesc('id')->get(),
            'workers' => \App\Modules\Workers\Models\LabourWorker::query()->where('farm_id', $cycle->farm_id)->orderBy('name')->get(),
            'teams' => \App\Modules\Workers\Models\LabourTeam::query()->where('farm_id', $cycle->farm_id)->orderBy('name')->get(),
            'products' => \App\Modules\Inventory\Models\InventoryProduct::query()->where('organization_id', $cycle->organization_id)->orderBy('name')->get(),
            'users' => User::query()->orderBy('name')->get(),
        ]);
    }

    public function edit(CropCycle $cycle): View { return view('crops::cycles.form', $this->formData($cycle)); }

    public function update(Request $request, CropCycle $cycle): RedirectResponse
    {
        $cycle->update($this->validated($request));
        return redirect()->route('crops.cycles.show', $cycle)->with('status', 'Crop cycle updated.');
    }

    public function close(Request $request, CropCycle $cycle): RedirectResponse
    {
        $data = $request->validate(['closure_notes' => ['nullable', 'string', 'max:2000']]);
        $cycle->update(['status' => 'closed', 'closed_at' => now(), 'closed_by' => $request->user()->id, 'closure_notes' => $data['closure_notes'] ?? null]);

        return redirect()->route('crops.cycles.show', $cycle)->with('status', 'Crop cycle closed.');
    }

    public function cancel(Request $request, CropCycle $cycle): RedirectResponse
    {
        $data = $request->validate(['cancellation_reason' => ['nullable', 'string', 'max:2000']]);
        $cycle->update(['status' => 'cancelled', 'cancelled_at' => now(), 'cancellation_reason' => $data['cancellation_reason'] ?? null]);

        return redirect()->route('crops.cycles.show', $cycle)->with('status', 'Crop cycle cancelled.');
    }

    private function formData(?CropCycle $cycle = null): array
    {
        return [
            'cycle' => $cycle,
            'organizations' => Organization::query()->orderBy('name')->get(),
            'farms' => Farm::query()->with('organization')->orderBy('name')->get(),
            'fields' => Field::query()->with('farm')->orderBy('name')->get(),
            'crops' => Crop::query()->orderBy('name')->get(),
            'varieties' => CropVariety::query()->with('crop')->orderBy('name')->get(),
            'seasons' => CropSeason::query()->with('farm')->orderByDesc('id')->get(),
            'users' => User::query()->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'organization_id' => ['required', 'integer', Rule::exists('organizations', 'id')],
            'farm_id' => ['required', 'integer', Rule::exists('farms', 'id')],
            'field_id' => ['required', 'integer', Rule::exists('fields', 'id')],
            'crop_id' => ['required', 'integer', Rule::exists('crop_crops', 'id')],
            'variety_id' => ['nullable', 'integer', Rule::exists('crop_varieties', 'id')],
            'season_id' => ['nullable', 'integer', Rule::exists('crop_seasons', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'planned_start_date' => ['nullable', 'date'],
            'actual_planting_date' => ['nullable', 'date'],
            'expected_harvest_date' => ['nullable', 'date'],
            'area_planted' => ['nullable', 'numeric', 'min:0'],
            'area_unit' => ['nullable', 'string', 'max:255'],
            'plant_population' => ['nullable', 'integer', 'min:0'],
            'spacing' => ['nullable', 'string', 'max:255'],
            'seed_source' => ['nullable', 'string', 'max:255'],
            'manager_user_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'status' => ['required', Rule::in(['planned', 'planted', 'growing', 'harvesting', 'closed', 'cancelled'])],
        ]);
        $this->ensureFarmBelongsToOrganization((int) $data['farm_id'], (int) $data['organization_id']);
        $this->ensureFieldBelongsToFarm((int) $data['field_id'], (int) $data['farm_id']);
        $this->ensureCropAvailableToOrganization((int) $data['crop_id'], (int) $data['organization_id']);
        $this->ensureVarietyBelongsToCrop($data['variety_id'] ?? null, (int) $data['crop_id']);
        $this->ensureSeasonBelongsToScope($data['season_id'] ?? null, (int) $data['organization_id'], (int) $data['farm_id']);

        return $data;
    }
}
