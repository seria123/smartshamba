<?php

namespace App\Http\Controllers;

use App\Models\CropCycle;
use App\Services\CropCycleAnalysisService;
use Illuminate\Http\Request;

class CropCycleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cropCycles = CropCycle::with(['farm', 'field', 'crop'])->get();

        return view('crop_cycles.index', compact('cropCycles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $farms = \App\Models\Farm::all();
        $fields = \App\Models\Field::all();
        $crops = \App\Models\Crop::all();
        $staff = \App\Models\Staff::all();
        // Get crop cycles that have at least one harvest
        $cropCycles = \App\Models\CropCycle::whereHas('harvests')->get();

        return view('crop_cycles.create', compact('farms', 'fields', 'crops', 'cropCycles', 'staff'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'crop_id' => 'required|exists:crops,id',
            'farm_id' => 'required|exists:farms,id',
            'staff_id' => 'nullable|exists:staff,id',
            // Basic Crop Identity
            'category' => 'nullable|string|max:255',
            'variety' => 'nullable|string|max:255',
            'season' => 'nullable|string|in:rain-fed,irrigated',
            'start_date' => 'required|date',
            'expected_harvest_date' => 'nullable|date|after_or_equal:start_date',
            // Land & Soil
            'soil_type_override' => 'nullable|string|max:255',
            'ph_level' => 'nullable|numeric|between:0,14',
            'previous_crop_cycle_id' => 'nullable|exists:crop_cycles,id',
            // Water & Irrigation
            'irrigation_type' => 'nullable|string|max:255',
            'irrigation_schedule' => 'nullable|string',
            'drainage' => 'nullable|string',
            'water_source_override' => 'nullable|string|max:255',
            // Planting Details
            'seed_batch_number' => 'nullable|string|max:255',
            'seed_quantity' => 'nullable|numeric',
            'seedling_quantity' => 'nullable|integer',
            'spacing_row' => 'nullable|integer',
            'spacing_plant' => 'nullable|integer',
            'planting_method' => 'nullable|string|max:255',
            'plant_population' => 'nullable|integer',
            'germination_rate' => 'nullable|numeric|between:0,100',
            'survival_rate' => 'nullable|numeric|between:0,100',
            'planting_labor_workers' => 'nullable|integer',
            'planting_labor_cost' => 'nullable|numeric',
            'planting_notes' => 'nullable|string',
            // Machinery
            'machinery_tractor' => 'nullable|integer',
            'machinery_pump' => 'nullable|integer',
            'machinery_sprayer' => 'nullable|integer',
            'machinery_notes' => 'nullable|string',
        ]);

        $validated['current_stage'] = 'planning';
        $cropCycle = CropCycle::create($validated);

        // Handle Inputs if provided
        if ($request->has('input_type')) {
            foreach ($request->input('input_type') as $index => $type) {
                if (! empty($type)) {
                    $cropCycle->inputs()->create([
                        'input_type' => $type,
                        'name' => $request->input('input_name')[$index] ?? null,
                        'quantity' => $request->input('input_quantity')[$index] ?? null,
                        'cost' => $request->input('input_cost')[$index] ?? null,
                        'application_date' => $request->input('input_date')[$index] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('crop_cycles.show', $cropCycle)
            ->with('success', 'Crop cycle created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CropCycle $cropCycle)
    {
        $cropCycle->load([
            'farm',
            'field',
            'crop',
            'previousCycle',
            'stages',
            'inputs',
            'activities',
            'harvests',
            'revenues',
            'analyses', // Load disease analyses
        ]);
        $analysisService = new CropCycleAnalysisService;
        $analysis = $analysisService->analyze($cropCycle);

        return view('crop_cycles.show', compact('cropCycle', 'analysis'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CropCycle $cropCycle)
    {
        $farms = \App\Models\Farm::all();
        $fields = \App\Models\Field::all();
        $crops = \App\Models\Crop::all();
        $cropCycles = \App\Models\CropCycle::where('id', '!=', $cropCycle->id)->whereHas('harvests')->get();
        $staff = \App\Models\Staff::all();
        $cropCycle->load(['stages', 'inputs', 'activities']);

        return view('crop_cycles.edit', compact('cropCycle', 'farms', 'fields', 'crops', 'cropCycles', 'staff'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CropCycle $cropCycle)
    {
        $validated = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'crop_id' => 'required|exists:crops,id',
            'farm_id' => 'required|exists:farms,id',
            // Basic Crop Identity
            'category' => 'nullable|string|max:255',
            'variety' => 'nullable|string|max:255',
            'season' => 'nullable|string|in:rain-fed,irrigated',
            'start_date' => 'required|date',
            'expected_harvest_date' => 'nullable|date|after_or_equal:start_date',
            // Land & Soil
            'soil_type_override' => 'nullable|string|max:255',
            'ph_level' => 'nullable|numeric|between:0,14',
            'previous_crop_cycle_id' => 'nullable|exists:crop_cycles,id',
            // Water & Irrigation
            'irrigation_type' => 'nullable|string|max:255',
            'irrigation_schedule' => 'nullable|string',
            'drainage' => 'nullable|string',
            'water_source_override' => 'nullable|string|max:255',
            // Planting Details
            'seed_batch_number' => 'nullable|string|max:255',
            'seed_quantity' => 'nullable|numeric',
            'seedling_quantity' => 'nullable|integer',
            'spacing_row' => 'nullable|integer',
            'spacing_plant' => 'nullable|integer',
            'plant_population' => 'nullable|integer',
            'germination_rate' => 'nullable|numeric|between:0,100',
            'survival_rate' => 'nullable|numeric|between:0,100',
            'planting_labor_workers' => 'nullable|integer',
            'planting_labor_cost' => 'nullable|numeric',
            'planting_notes' => 'nullable|string',
        ]);

        $cropCycle->update($validated);

        return redirect()->route('crop_cycles.show', $cropCycle)
            ->with('success', 'Crop cycle updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CropCycle $cropCycle)
    {
        $cropCycle->delete();

        return redirect()->route('crop_cycles.index')
            ->with('success', 'Crop cycle deleted successfully.');
    }

    /**
     * Get analysis for a crop cycle.
     */
    public function analyze(CropCycle $cropCycle)
    {
        $analysisService = new CropCycleAnalysisService;
        $analysis = $analysisService->analyze($cropCycle);

        return response()->json($analysis);
    }
}
