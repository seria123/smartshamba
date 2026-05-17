<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Field;
use Illuminate\Http\Request;

class CropController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $crops = Crop::with('field')->paginate(20);

        return view('crops.index', compact('crops'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fields = Field::all();

        return view('crops.create', compact('fields'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'name' => 'required|string|max:255',
            'variety' => 'nullable|string|max:255',
            'planting_date' => 'nullable|date',
            'expected_harvest_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = $request->user()->id;

        $crop = Crop::firstOrCreate(
            [
                'field_id' => $validated['field_id'],
                'name' => $validated['name'],
                'variety' => $validated['variety'] ?? null,
            ],
            $validated
        );

        if (! $crop->wasRecentlyCreated) {
            $crop->update($validated);
        }

        return redirect()->route('crops.index')
            ->with('success', 'Crop created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Crop $crop)
    {
        $crop->load('field');

        // Get latest disease analysis for this crop
        $latestAnalysis = null;
        if ($crop->field) {
            $latestAnalysis = \App\Models\CropAnalysis::where('field_id', $crop->field_id)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        return view('crops.show', compact('crop', 'latestAnalysis'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Crop $crop)
    {
        $fields = Field::all();

        return view('crops.edit', compact('crop', 'fields'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Crop $crop)
    {
        $validated = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'name' => 'required|string|max:255',
            'variety' => 'nullable|string|max:255',
            'planting_date' => 'nullable|date',
            'expected_harvest_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $crop->update($validated);

        return redirect()->route('crops.index')
            ->with('success', 'Crop updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Crop $crop)
    {
        $crop->delete();

        return redirect()->route('crops.index')
            ->with('success', 'Crop deleted successfully.');
    }
    
}
