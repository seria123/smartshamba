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
        $cropCycles = CropCycle::with(['farm'])->get();
        return view('crop_cycles.index', compact('cropCycles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $farms = \App\Models\Farm::all();
        return view('crop_cycles.create', compact('farms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'crop_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'expected_harvest' => 'required|date|after_or_equal:start_date',
            'farm_id' => 'required|exists:farms,id',
        ]);

        $cropCycle = CropCycle::create($request->all());

        return redirect()->route('crop_cycles.show', $cropCycle)
            ->with('success', 'Crop cycle created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CropCycle $cropCycle)
    {
        $analysisService = new CropCycleAnalysisService();
        $analysis = $analysisService->analyze($cropCycle);

        return view('crop_cycles.show', compact('cropCycle', $analysis));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CropCycle $cropCycle)
    {
        $farms = \App\Models\Farm::all();
        return view('crop_cycles.edit', compact('cropCycle', $farms));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CropCycle $cropCycle)
    {
        $request->validate([
            'crop_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'expected_harvest' => 'required|date|after_or_equal:start_date',
            'farm_id' => 'required|exists:farms,id',
        ]);

        $cropCycle->update($request->all());

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
        $analysisService = new CropCycleAnalysisService();
        $analysis = $analysisService->analyze($cropCycle);

        return response()->json($analysis);
    }
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fields = \App\Models\Field::with('farm')->get();
        $crops = \App\Models\Crop::all();
        return view('crop_cycles.create', compact('fields', 'crops'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id',
            'crop_id' => 'required|exists:crops,id',
            'start_date' => 'required|date',
            'expected_harvest_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $cropCycle = CropCycle::create($request->all());

        return redirect()->route('crop_cycles.show', $cropCycle)
            ->with('success', 'Crop cycle created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CropCycle $cropCycle)
    {
        $analysisService = new CropCycleAnalysisService();
        $analysis = $analysisService->analyze($cropCycle);

        return view('crop_cycles.show', compact('cropCycle', 'analysis'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CropCycle $cropCycle)
    {
        $fields = \App\Models\Field::with('farm')->get();
        $crops = \App\Models\Crop::all();
        return view('crop_cycles.edit', compact('cropCycle', 'fields', 'crops'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CropCycle $cropCycle)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id',
            'crop_id' => 'required|exists:crops,id',
            'start_date' => 'required|date',
            'expected_harvest_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $cropCycle->update($request->all());

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
        $analysisService = new CropCycleAnalysisService();
        $analysis = $analysisService->analyze($cropCycle);

        return response()->json($analysis);
    }
}
