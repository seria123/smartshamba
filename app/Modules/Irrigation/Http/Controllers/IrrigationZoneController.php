<?php

namespace App\Modules\Irrigation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Field;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Site;
use App\Modules\Irrigation\Http\Controllers\Concerns\ValidatesIrrigationScope;
use App\Modules\Irrigation\Models\IrrigationWaterSource;
use App\Modules\Irrigation\Models\IrrigationZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class IrrigationZoneController extends Controller
{
    use ValidatesIrrigationScope;

    public function index(): View { return view('irrigation::zones.index', ['zones' => IrrigationZone::with(['farm', 'field', 'waterSource'])->orderBy('code')->paginate(20)]); }
    public function create(): View { return view('irrigation::zones.form', $this->formData()); }
    public function show(IrrigationZone $zone): View { return view('irrigation::zones.show', ['zone' => $zone->load(['organization', 'farm', 'site', 'field', 'waterSource', 'schedules', 'events', 'readings', 'issues'])]); }
    public function edit(IrrigationZone $zone): View { return view('irrigation::zones.form', $this->formData($zone)); }
    public function store(Request $request): RedirectResponse { IrrigationZone::query()->create($this->validated($request) + ['created_by' => $request->user()->id]); return redirect()->route('irrigation.zones.index')->with('status', 'Irrigation zone created.'); }
    public function update(Request $request, IrrigationZone $zone): RedirectResponse { $zone->update($this->validated($request, $zone) + ['updated_by' => $request->user()->id]); return redirect()->route('irrigation.zones.show', $zone)->with('status', 'Irrigation zone updated.'); }
    public function deactivate(IrrigationZone $zone): RedirectResponse { $zone->update(['status' => 'inactive']); return redirect()->route('irrigation.zones.show', $zone)->with('status', 'Irrigation zone deactivated.'); }

    private function formData(?IrrigationZone $zone = null): array
    {
        return [
            'zone' => $zone,
            'organizations' => Organization::query()->orderBy('name')->get(),
            'farms' => Farm::query()->orderBy('name')->get(),
            'sites' => Site::query()->orderBy('name')->get(),
            'fields' => Field::query()->orderBy('name')->get(),
            'sources' => IrrigationWaterSource::query()->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request, ?IrrigationZone $zone = null): array
    {
        $validator = validator($request->all(), [
            'organization_id' => ['required', 'integer', Rule::exists('organizations', 'id')],
            'farm_id' => ['required', 'integer', Rule::exists('farms', 'id')->where('organization_id', $request->input('organization_id'))],
            'site_id' => ['nullable', 'integer', Rule::exists('sites', 'id')],
            'field_id' => ['nullable', 'integer', Rule::exists('fields', 'id')],
            'water_source_id' => ['nullable', 'integer', Rule::exists('irrigation_water_sources', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', Rule::unique('irrigation_zones', 'code')->where('farm_id', $request->input('farm_id'))->ignore($zone?->id)],
            'zone_type' => ['required', Rule::in(['field_zone', 'greenhouse_zone', 'nursery_zone', 'orchard_zone', 'general_zone', 'other'])],
            'irrigation_method' => ['required', Rule::in(['drip', 'sprinkler', 'furrow', 'flood', 'manual', 'pivot', 'micro_sprinkler', 'other'])],
            'area' => ['nullable', 'numeric', 'min:0'],
            'area_unit' => ['nullable', 'string', 'max:50'],
            'status' => ['required', Rule::in(['active', 'inactive', 'under_maintenance', 'archived'])],
            'notes' => ['nullable', 'string'],
        ]);
        $this->addFarmScopedValidation($validator, ['site_id' => 'sites', 'field_id' => 'fields', 'water_source_id' => 'irrigation_water_sources']);

        return $validator->validate();
    }
}
