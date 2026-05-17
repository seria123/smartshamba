<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activities = Activity::with(['field', 'cropCycle', 'cropStage', 'staff', 'supervisor'])->get();

        return view('activities.index', compact('activities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fields = \App\Models\Field::all();
        $cropCycles = \App\Models\CropCycle::all();
        $cropStages = \App\Models\CropStage::all();
        $staff = \App\Models\Staff::all();

        return view('activities.create', compact('fields', 'cropCycles', 'cropStages', 'staff'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'crop_stage_id' => 'required|exists:crop_stages,id',
            'crop_cycle_id' => 'required|exists:crop_cycles,id',
            'field_id' => 'required|exists:fields,id',
            'activity_name' => 'required|string|max:255',
            'activity_type' => 'required|string|in:weeding,fertilizer_application,spraying,irrigation,pruning_training,scouting_inspection,thinning_gapping,soil_crop_nutrition,harvesting',
            'description' => 'nullable|string',
            'cost' => 'nullable|numeric',
            'activity_date' => 'required|date',
            'staff_id' => 'nullable|exists:staff,id',
            'quantity' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:pending,approved,rejected',
            'supervisor_id' => 'nullable|exists:staff,id',
            'labor_type' => 'nullable|string|max:255',
        ]);

        Activity::create($validated);

        return redirect()->route('activities.index')
            ->with('success', 'Activity created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity)
    {
        $activity->load(['field', 'cropCycle', 'cropStage', 'staff', 'supervisor', 'assignedStaff', 'equipment', 'images']);

        return view('activities.show', compact('activity'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity)
    {
        $fields = \App\Models\Field::all();
        $cropCycles = \App\Models\CropCycle::all();
        $cropStages = \App\Models\CropStage::all();
        $staff = \App\Models\Staff::all();

        $activity->load(['assignedStaff', 'equipment']);

        return view('activities.edit', compact('activity', 'fields', 'cropCycles', 'cropStages', 'staff'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'crop_stage_id' => 'required|exists:crop_stages,id',
            'crop_cycle_id' => 'required|exists:crop_cycles,id',
            'field_id' => 'required|exists:fields,id',
            'activity_name' => 'required|string|max:255',
            'activity_type' => 'required|string|in:weeding,fertilizer_application,spraying,irrigation,pruning_training,scouting_inspection,thinning_gapping,soil_crop_nutrition,harvesting',
            'description' => 'nullable|string',
            'cost' => 'nullable|numeric',
            'activity_date' => 'required|date',
            'staff_id' => 'nullable|exists:staff,id',
            'quantity' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:pending,approved,rejected',
            'supervisor_id' => 'nullable|exists:staff,id',
            'labor_type' => 'nullable|string|max:255',
        ]);

        $activity->update($validated);

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Activity updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()->route('activities.index')
            ->with('success', 'Activity deleted successfully.');
    }
}
