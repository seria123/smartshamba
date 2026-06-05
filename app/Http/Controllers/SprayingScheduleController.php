<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Field;
use App\Models\CropCycle;
use App\Models\SprayingSchedule;
use App\Models\ChemicalType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SprayingScheduleController extends Controller
{
    public function index()
    {
        $schedules = SprayingSchedule::with(['crop', 'field', 'cropCycle'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('spraying_schedules.index', compact('schedules'));
    }

    public function create()
    {
        $crops = Crop::where('user_id', Auth::id())->get();
        $fields = Field::where('user_id', Auth::id())->get();
        $cropCycles = CropCycle::whereHas('field', function($q) { $q->where('user_id', Auth::id()); })
            ->orWhereHas('farm', function($q) { $q->where('user_id', Auth::id()); })->get();
        $chemicalTypes = ChemicalType::all()->pluck('name', 'id');

        return view('spraying_schedules.create', compact('crops', 'fields', 'cropCycles', 'chemicalTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'crop_id' => 'nullable|exists:crops,id',
            'field_id' => 'nullable|exists:fields,id',
            'crop_cycle_id' => 'nullable|exists:crop_cycles,id',
            'crop_type' => 'nullable|string|max:255',
            'crop_variety' => 'nullable|string|max:255',
            'planting_date' => 'nullable|date',
            'growth_stage' => 'nullable|string|max:255',
            'spray_type' => 'required|string|in:pesticide,herbicide,fungicide,insecticide',
            'chemical_id' => 'required|exists:chemical_types,id',
            'active_ingredient' => 'nullable|string|max:255',
            'target_pest_disease' => 'nullable|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'scheduled_date' => 'nullable|date',
            'frequency_days' => 'nullable|integer|min:1',
            'growth_stage_trigger' => 'nullable|string|max:255',
            'rei_hours' => 'nullable|integer|min:0',
            'phi_days' => 'nullable|integer|min:0',
            'temperature' => 'nullable|numeric',
            'rain_forecast' => 'nullable|boolean',
            'wind_speed' => 'nullable|numeric|min:0',
            'mixing_ratio' => 'nullable|string|max:255',
            'water_volume' => 'nullable|numeric|min:0',
            'equipment' => 'nullable|string|max:255',
            'area_covered' => 'nullable|numeric|min:0',
            'operator' => 'nullable|string|max:255',
            'gear_gloves' => 'nullable|boolean',
            'gear_mask' => 'nullable|boolean',
            'gear_overalls' => 'nullable|boolean',
            'safety_notes' => 'nullable|string',
            'application_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
        ]);

        $validated['user_id'] = Auth::id();
        $chemicalType = ChemicalType::find($validated['chemical_id']);
        $validated['chemical_name'] = $chemicalType->name;

        SprayingSchedule::create($validated);

        return redirect()->route('spraying_schedules.index')
            ->with('success', 'Spraying schedule created successfully.');
    }

    public function show(SprayingSchedule $sprayingSchedule)
    {
        $this->authorizeOwnership($sprayingSchedule);
        return view('spraying_schedules.show', compact('sprayingSchedule'));
    }

    public function edit(SprayingSchedule $sprayingSchedule)
    {
        $this->authorizeOwnership($sprayingSchedule);
        $crops = Crop::where('user_id', Auth::id())->get();
        $fields = Field::where('user_id', Auth::id())->get();
        $cropCycles = CropCycle::whereHas('field', function($q) { $q->where('user_id', Auth::id()); })
            ->orWhereHas('farm', function($q) { $q->where('user_id', Auth::id()); })->get();
        $chemicalTypes = ChemicalType::all()->pluck('name', 'id');

        return view('spraying_schedules.edit', compact('sprayingSchedule', 'crops', 'fields', 'cropCycles', 'chemicalTypes'));
    }

    public function update(Request $request, SprayingSchedule $sprayingSchedule)
    {
        $this->authorizeOwnership($sprayingSchedule);

        $validated = $request->validate([
            'crop_id' => 'nullable|exists:crops,id',
            'field_id' => 'nullable|exists:fields,id',
            'crop_cycle_id' => 'nullable|exists:crop_cycles,id',
            'crop_type' => 'nullable|string|max:255',
            'crop_variety' => 'nullable|string|max:255',
            'planting_date' => 'nullable|date',
            'growth_stage' => 'nullable|string|max:255',
            'spray_type' => 'required|string|in:pesticide,herbicide,fungicide,insecticide',
            'chemical_id' => 'required|exists:chemical_types,id',
            'active_ingredient' => 'nullable|string|max:255',
            'target_pest_disease' => 'nullable|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'scheduled_date' => 'nullable|date',
            'applied_date' => 'nullable|date',
            'frequency_days' => 'nullable|integer|min:1',
            'growth_stage_trigger' => 'nullable|string|max:255',
            'rei_hours' => 'nullable|integer|min:0',
            'phi_days' => 'nullable|integer|min:0',
            'temperature' => 'nullable|numeric',
            'rain_forecast' => 'nullable|boolean',
            'wind_speed' => 'nullable|numeric|min:0',
            'mixing_ratio' => 'nullable|string|max:255',
            'water_volume' => 'nullable|numeric|min:0',
            'equipment' => 'nullable|string|max:255',
            'area_covered' => 'nullable|numeric|min:0',
            'operator' => 'nullable|string|max:255',
            'gear_gloves' => 'nullable|boolean',
            'gear_mask' => 'nullable|boolean',
            'gear_overalls' => 'nullable|boolean',
            'safety_notes' => 'nullable|string',
            'application_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:scheduled,completed,cancelled',
            'cost' => 'nullable|numeric|min:0',
        ]);

        $validated['user_id'] = Auth::id();
        $chemicalType = ChemicalType::find($validated['chemical_id']);
        $validated['chemical_name'] = $chemicalType->name;

        $sprayingSchedule->update($validated);

        return redirect()->route('spraying_schedules.index')
            ->with('success', 'Spraying schedule updated successfully.');
    }

    public function destroy(SprayingSchedule $sprayingSchedule)
    {
        $this->authorizeOwnership($sprayingSchedule);
        $sprayingSchedule->delete();

        return redirect()->route('spraying_schedules.index')
            ->with('success', 'Spraying schedule deleted successfully.');
    }

    protected function authorizeOwnership($model)
    {
        if ($model->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }
}