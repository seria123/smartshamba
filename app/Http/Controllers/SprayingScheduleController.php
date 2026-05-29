<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Field;
use App\Models\CropCycle;
use App\Models\SprayingSchedule;
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
        $cropCycles = CropCycle::where('user_id', Auth::id())->get();

        return view('spraying_schedules.create', compact('crops', 'fields', 'cropCycles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'crop_id' => 'nullable|exists:crops,id',
            'field_id' => 'nullable|exists:fields,id',
            'crop_cycle_id' => 'nullable|exists:crop_cycles,id',
            'spray_type' => 'required|string|in:pesticide,herbicide,fungicide,insecticide',
            'chemical_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'scheduled_date' => 'nullable|date',
            'application_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
        ]);

        $validated['user_id'] = Auth::id();
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
        $cropCycles = CropCycle::where('user_id', Auth::id())->get();

        return view('spraying_schedules.edit', compact('sprayingSchedule', 'crops', 'fields', 'cropCycles'));
    }

    public function update(Request $request, SprayingSchedule $sprayingSchedule)
    {
        $this->authorizeOwnership($sprayingSchedule);

        $validated = $request->validate([
            'crop_id' => 'nullable|exists:crops,id',
            'field_id' => 'nullable|exists:fields,id',
            'crop_cycle_id' => 'nullable|exists:crop_cycles,id',
            'spray_type' => 'required|string|in:pesticide,herbicide,fungicide,insecticide',
            'chemical_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'scheduled_date' => 'nullable|date',
            'applied_date' => 'nullable|date',
            'application_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:scheduled,completed,cancelled',
            'cost' => 'nullable|numeric|min:0',
        ]);

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