<?php

namespace App\Modules\Irrigation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Irrigation\Http\Controllers\Concerns\ValidatesIrrigationScope;
use App\Modules\Irrigation\Models\IrrigationWaterReading;
use App\Modules\Irrigation\Models\IrrigationWaterSource;
use App\Modules\Irrigation\Models\IrrigationZone;
use App\Modules\Workers\Models\LabourWorker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WaterReadingController extends Controller
{
    use ValidatesIrrigationScope;

    public function index(): View { return view('irrigation::readings.index', ['readings' => IrrigationWaterReading::with(['waterSource', 'zone', 'recordedByUser', 'recordedByWorker'])->latest('reading_date')->paginate(20)]); }
    public function create(): View { return view('irrigation::readings.form', $this->formData()); }
    public function store(Request $request): RedirectResponse { IrrigationWaterReading::query()->create($this->validated($request) + ['created_by' => $request->user()->id]); return redirect()->route('irrigation.readings.index')->with('status', 'Water reading recorded.'); }

    private function formData(): array
    {
        return ['organizations' => Organization::orderBy('name')->get(), 'farms' => Farm::orderBy('name')->get(), 'sources' => IrrigationWaterSource::orderBy('name')->get(), 'zones' => IrrigationZone::orderBy('name')->get(), 'workers' => LabourWorker::orderBy('name')->get()];
    }

    private function validated(Request $request): array
    {
        $validator = validator($request->all(), [
            'organization_id' => ['required', 'integer', Rule::exists('organizations', 'id')],
            'farm_id' => ['required', 'integer', Rule::exists('farms', 'id')->where('organization_id', $request->input('organization_id'))],
            'water_source_id' => ['nullable', 'integer', Rule::exists('irrigation_water_sources', 'id')],
            'irrigation_zone_id' => ['nullable', 'integer', Rule::exists('irrigation_zones', 'id')],
            'reading_date' => ['required', 'date'],
            'reading_type' => ['required', Rule::in(['meter_reading', 'tank_level', 'flow_rate', 'pressure', 'soil_moisture', 'rainfall', 'manual_observation', 'other'])],
            'value' => ['required', 'numeric'],
            'unit_of_measure' => ['required', 'string', 'max:50'],
            'recorded_by_worker_id' => ['nullable', 'integer', Rule::exists('labour_workers', 'id')],
            'notes' => ['nullable', 'string'],
        ]);
        $this->addReadingTargetValidation($validator);
        $this->addFarmScopedValidation($validator, ['water_source_id' => 'irrigation_water_sources', 'irrigation_zone_id' => 'irrigation_zones', 'recorded_by_worker_id' => 'labour_workers']);

        return $validator->validate() + ['recorded_by_user_id' => $request->user()->id];
    }
}
