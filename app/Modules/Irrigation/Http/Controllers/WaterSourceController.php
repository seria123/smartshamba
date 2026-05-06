<?php

namespace App\Modules\Irrigation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Irrigation\Models\IrrigationWaterSource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WaterSourceController extends Controller
{
    public function index(): View { return view('irrigation::water-sources.index', ['sources' => IrrigationWaterSource::with('farm')->orderBy('code')->paginate(20)]); }
    public function create(): View { return view('irrigation::water-sources.form', $this->formData()); }
    public function show(IrrigationWaterSource $source): View { return view('irrigation::water-sources.show', ['source' => $source->load(['organization', 'farm', 'zones', 'readings', 'issues'])]); }
    public function edit(IrrigationWaterSource $source): View { return view('irrigation::water-sources.form', $this->formData($source)); }
    public function store(Request $request): RedirectResponse { IrrigationWaterSource::query()->create($this->validated($request) + ['created_by' => $request->user()->id]); return redirect()->route('irrigation.water-sources.index')->with('status', 'Water source created.'); }
    public function update(Request $request, IrrigationWaterSource $source): RedirectResponse { $source->update($this->validated($request, $source) + ['updated_by' => $request->user()->id]); return redirect()->route('irrigation.water-sources.show', $source)->with('status', 'Water source updated.'); }
    public function deactivate(IrrigationWaterSource $source): RedirectResponse { $source->update(['status' => 'inactive']); return redirect()->route('irrigation.water-sources.show', $source)->with('status', 'Water source deactivated.'); }

    private function formData(?IrrigationWaterSource $source = null): array
    {
        return ['source' => $source, 'organizations' => Organization::query()->orderBy('name')->get(), 'farms' => Farm::query()->orderBy('name')->get()];
    }

    private function validated(Request $request, ?IrrigationWaterSource $source = null): array
    {
        return $request->validate([
            'organization_id' => ['required', 'integer', Rule::exists('organizations', 'id')],
            'farm_id' => ['required', 'integer', Rule::exists('farms', 'id')->where('organization_id', $request->input('organization_id'))],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', Rule::unique('irrigation_water_sources', 'code')->where('farm_id', $request->input('farm_id'))->ignore($source?->id)],
            'source_type' => ['required', Rule::in(['borehole', 'river', 'dam', 'tank', 'well', 'municipal', 'rainwater', 'pond', 'other'])],
            'capacity' => ['nullable', 'numeric', 'min:0'],
            'capacity_unit' => ['nullable', 'string', 'max:50'],
            'location_description' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'status' => ['required', Rule::in(['active', 'inactive', 'under_maintenance', 'archived'])],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
