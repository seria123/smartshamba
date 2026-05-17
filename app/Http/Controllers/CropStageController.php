<?php

namespace App\Http\Controllers;

use App\Models\CropStage;
use Illuminate\Http\Request;

class CropStageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cropStages = CropStage::with(['cropCycle', 'crop'])->get();

        return view('crop_stages.index', compact('cropStages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cropCycles = \App\Models\CropCycle::all();

        return view('crop_stages.create', compact('cropCycles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'crop_cycle_id' => 'required|exists:crop_cycles,id',
            'stage_name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        CropStage::create($validated);

        return redirect()->route('crop_stages.index')
            ->with('success', 'Crop stage created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CropStage $cropStage)
    {
        $cropStage->load(['cropCycle', 'crop']);

        return view('crop_stages.show', compact('cropStage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CropStage $cropStage)
    {
        $cropCycles = \App\Models\CropCycle::all();

        $cropStage->load(['cropCycle']);

        return view('crop_stages.edit', compact('cropStage', 'cropCycles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CropStage $cropStage)
    {
        $validated = $request->validate([
            'crop_cycle_id' => 'required|exists:crop_cycles,id',
            'stage_name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $cropStage->update($validated);

        return redirect()->route('crop_stages.show', $cropStage)
            ->with('success', 'Crop stage updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CropStage $cropStage)
    {
        $cropStage->delete();

        return redirect()->route('crop_stages.index')
            ->with('success', 'Crop stage deleted successfully.');
    }
  
}
