<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Field;
use App\Models\CropCycle;
use App\Models\PestControlSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PestControlScheduleController extends Controller
{
    public function index()
    {
        $schedules = PestControlSchedule::with(['crop', 'field', 'cropCycle'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('pest_control_schedules.index', compact('schedules'));
    }

    public function create()
    {
        $crops = Crop::where('user_id', Auth::id())->get();
        $fields = Field::where('user_id', Auth::id())->get();
        $cropCycles = CropCycle::whereHas('field', function($q) { $q->where('user_id', Auth::id()); })
            ->orWhereHas('farm', function($q) { $q->where('user_id', Auth::id()); })->get();

        return view('pest_control_schedules.create', compact('crops', 'fields', 'cropCycles'));
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
            'pest_name' => 'required|string|max:255',
            'threat_level' => 'required|string|in:low,medium,high',
            'description' => 'nullable|string',
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
            'treatment_method' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric|min:0',
        ]);

        $validated['user_id'] = Auth::id();
        PestControlSchedule::create($validated);

        return redirect()->route('pest_control_schedules.index')
            ->with('success', 'Pest control schedule created successfully.');
    }

    public function show(PestControlSchedule $pestControlSchedule)
    {
        $this->authorizeOwnership($pestControlSchedule);
        return view('pest_control_schedules.show', compact('pestControlSchedule'));
    }

    public function edit(PestControlSchedule $pestControlSchedule)
    {
        $this->authorizeOwnership($pestControlSchedule);
        $crops = Crop::where('user_id', Auth::id())->get();
        $fields = Field::where('user_id', Auth::id())->get();
        $cropCycles = CropCycle::whereHas('field', function($q) { $q->where('user_id', Auth::id()); })
            ->orWhereHas('farm', function($q) { $q->where('user_id', Auth::id()); })->get();

        return view('pest_control_schedules.edit', compact('pestControlSchedule', 'crops', 'fields', 'cropCycles'));
    }

    public function update(Request $request, PestControlSchedule $pestControlSchedule)
    {
        $this->authorizeOwnership($pestControlSchedule);

        $validated = $request->validate([
            'crop_id' => 'nullable|exists:crops,id',
            'field_id' => 'nullable|exists:fields,id',
            'crop_cycle_id' => 'nullable|exists:crop_cycles,id',
            'crop_type' => 'nullable|string|max:255',
            'crop_variety' => 'nullable|string|max:255',
            'planting_date' => 'nullable|date',
            'growth_stage' => 'nullable|string|max:255',
            'pest_name' => 'required|string|max:255',
            'threat_level' => 'required|string|in:low,medium,high',
            'description' => 'nullable|string',
            'scheduled_date' => 'nullable|date',
            'inspected_date' => 'nullable|date',
            'treatment_date' => 'nullable|date',
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
            'treatment_method' => 'nullable|string|max:255',
            'treatment_notes' => 'nullable|string',
            'status' => 'required|in:scheduled,inspected,treated,cancelled',
            'cost' => 'nullable|numeric|min:0',
        ]);

        $pestControlSchedule->update($validated);

        return redirect()->route('pest_control_schedules.index')
            ->with('success', 'Pest control schedule updated successfully.');
    }

    public function destroy(PestControlSchedule $pestControlSchedule)
    {
        $this->authorizeOwnership($pestControlSchedule);
        $pestControlSchedule->delete();

        return redirect()->route('pest_control_schedules.index')
            ->with('success', 'Pest control schedule deleted successfully.');
    }

    protected function authorizeOwnership($model)
    {
        if ($model->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }
}