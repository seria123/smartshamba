<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Farm;
use App\Models\Field;
use App\Models\PlantingSchedule;
use Illuminate\Http\Request;

class PlantingScheduleController extends Controller
{
    /**
     * Display the planting calendar view.
     */
    public function index(Request $request)
    {
        $query = PlantingSchedule::with(['crop', 'field', 'farm']);

        // Filter by user if authenticated and not admin
        if (auth()->check() && auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }

        // Apply filters
        if ($request->has('crop_id') && $request->crop_id) {
            $query->where('crop_id', $request->crop_id);
        }
        if ($request->has('field_id') && $request->field_id) {
            $query->where('field_id', $request->field_id);
        }
        if ($request->has('farm_id') && $request->farm_id) {
            $query->where('farm_id', $request->farm_id);
        }
        if ($request->has('season') && $request->season) {
            $query->where('season', $request->season);
        }
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        if ($request->has('view') && $request->view === 'calendar') {
            // For calendar view, load all needed data
            $schedules = $query->get();

            return view('planting_schedules.calendar', compact('schedules'));
        }

        $schedules = $query->orderBy('planting_date', 'asc')->paginate(15);

        $crops = Crop::orderBy('name')->get();
        $fields = Field::orderBy('name')->get();
        $farms = Farm::orderBy('name')->get();

        $upcoming = PlantingSchedule::upcoming()
            ->when(auth()->check() && auth()->user()->role !== 'admin', fn ($q) => $q->where('user_id', auth()->id()))
            ->limit(10)
            ->get();

        $active = PlantingSchedule::active()
            ->when(auth()->check() && auth()->user()->role !== 'admin', fn ($q) => $q->where('user_id', auth()->id()))
            ->get();

        return view('planting_schedules.index', compact(
            'schedules',
            'upcoming',
            'active',
            'crops',
            'fields',
            'farms'
        ));
    }

    /**
     * Display the calendar view.
     */
    public function calendar(Request $request)
    {
        $query = PlantingSchedule::with(['crop', 'field']);

        if (auth()->check() && auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }

        $schedules = $query->get();

        // Group by month for summary
        $monthlyStats = $schedules
            ->groupBy(fn ($s) => $s->planting_date->format('Y-m'))
            ->map(fn ($group) => [
                'count' => $group->count(),
                'crops' => $group->pluck('crop.name')->unique()->count(),
            ]);

        return view('planting_schedules.calendar', [
            'schedules' => $schedules,
            'monthlyStats' => $monthlyStats,
        ]);
    }

    /**
     * Show the create form.
     */
    public function create()
    {
        $crops = Crop::orderBy('name')->get();
        $fields = Field::orderBy('name')->get();
        $farms = Farm::orderBy('name')->get();
        $cropCycles = \App\Models\CropCycle::where('expected_harvest_date', '>=', now()->subMonths(3))->get();

        return view('planting_schedules.create', compact('crops', 'fields', 'farms', 'cropCycles'));
    }

    /**
     * Store a new planting schedule.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'crop_id' => 'required|exists:crops,id',
            'field_id' => 'nullable|exists:fields,id',
            'farm_id' => 'nullable|exists:farms,id',
            'crop_cycle_id' => 'nullable|exists:crop_cycles,id',
            'planting_date' => 'required|date',
            'planting_window_start' => 'nullable|date|before:planting_window_end',
            'planting_window_end' => 'nullable|date|after:planting_window_start',
            'expected_harvest_date' => 'nullable|date|after_or_equal:planting_date',
            'actual_harvest_date' => 'nullable|date|after:planting_date',
            'estimated_quantity' => 'nullable|numeric|min:0',
            'quantity_unit' => 'required|string|max:50',
            'actual_quantity' => 'nullable|numeric|min:0',
            'actual_quantity_unit' => 'nullable|string|max:50',
            'status' => 'required|string|in:planned,planted,growing,ready_for_harvest,harvested,cancelled',
            'season' => 'required|string|in:spring,summer,fall,winter,year_round',
            'variety' => 'nullable|string|max:255',
            'completion_percentage' => 'required|integer|min:0|max:100',
            'current_stage' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Auto-assign farm if field is selected but farm isn't
        if (! $validated['farm_id'] && ! empty($validated['field_id'])) {
            $field = Field::find($validated['field_id']);
            if ($field) {
                $validated['farm_id'] = $field->farm_id;
            }
        }

        // Auto-assign user if authenticated
        if (auth()->check()) {
            $validated['user_id'] = auth()->id();
        }

        $planting = PlantingSchedule::create($validated);

        return redirect()
            ->route('planting_schedules.index')
            ->with('success', 'Planting schedule created successfully.');
    }

    /**
     * Show the edit form.
     */
    public function edit(PlantingSchedule $plantingSchedule)
    {
        $this->authorizeAccess($plantingSchedule);

        $crops = Crop::orderBy('name')->get();
        $fields = Field::orderBy('name')->get();
        $farms = Farm::orderBy('name')->get();
        $cropCycles = \App\Models\CropCycle::where('expected_harvest_date', '>=', now()->subMonths(3))->get();

        return view('planting_schedules.edit', compact(
            'plantingSchedule',
            'crops',
            'fields',
            'farms',
            'cropCycles'
        ));
    }

    /**
     * Update the planting schedule.
     */
    public function update(Request $request, PlantingSchedule $plantingSchedule)
    {
        $this->authorizeAccess($plantingSchedule);

        $validated = $request->validate([
            'crop_id' => 'required|exists:crops,id',
            'field_id' => 'nullable|exists:fields,id',
            'farm_id' => 'nullable|exists:farms,id',
            'crop_cycle_id' => 'nullable|exists:crop_cycles,id',
            'planting_date' => 'required|date',
            'planting_window_start' => 'nullable|date|before:planting_window_end',
            'planting_window_end' => 'nullable|date|after:planting_window_start',
            'expected_harvest_date' => 'nullable|date|after_or_equal:planting_date',
            'actual_harvest_date' => 'nullable|date|after:planting_date',
            'estimated_quantity' => 'nullable|numeric|min:0',
            'quantity_unit' => 'required|string|max:50',
            'actual_quantity' => 'nullable|numeric|min:0',
            'actual_quantity_unit' => 'nullable|string|max:50',
            'status' => 'required|string|in:planned,planted,growing,ready_for_harvest,harvested,cancelled',
            'season' => 'required|string|in:spring,summer,fall,winter,year_round',
            'variety' => 'nullable|string|max:255',
            'completion_percentage' => 'required|integer|min:0|max:100',
            'current_stage' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $plantingSchedule->update($validated);

        return redirect()
            ->route('planting_schedules.index')
            ->with('success', 'Planting schedule updated successfully.');
    }

  public function show($id)
{
    $plantingSchedule = PlantingSchedule::findOrFail($id);

    return view('planting_schedules.show', compact('plantingSchedule'));
}
    /**
     * Delete a planting schedule.
     */
    public function destroy(PlantingSchedule $plantingSchedule)
    {
        $this->authorizeAccess($plantingSchedule);
         $plantingSchedule->delete();

         return redirect()
             ->route('planting_schedules.index')
             ->with('success', 'Planting schedule deleted successfully.');
    }

    /**
     * Get upcoming plantings as JSON (for API/calendar).
     */
    public function upcoming()
    {
        $schedules = PlantingSchedule::upcoming()
            ->when(auth()->check() && auth()->user()->role !== 'admin', fn ($q) => $q->where('user_id', auth()->id()))
            ->with(['crop', 'field'])
            ->get();

        return response()->json($schedules);
    }

    /**
     * Check if user can access this planting schedule.
     */
    private function authorizeAccess(PlantingSchedule $plantingSchedule): void
    {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            if ($plantingSchedule->user_id !== auth()->id()) {
                abort(403, 'Unauthorized action.');
            }
        }
    }
}
