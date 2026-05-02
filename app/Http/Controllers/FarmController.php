<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use Illuminate\Http\Request;

class FarmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $farms = Farm::with('fields')->get();

        return view('farms.index', compact('farms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Load available crops and livestock types for dropdowns
        $crops = \App\Models\Crop::select('id', 'name')->orderBy('name')->get();
        $livestockTypes = \App\Models\LivestockType::select('id', 'name')->orderBy('name')->get();

        return view('farms.create', compact('crops', 'livestockTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'farm_type' => 'required|string|in:crop,livestock,mixed',
            'ownership_type' => 'required|string|in:owned,leased,community',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'size_hectares' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            // Conditional fields
            'crops' => 'required_if:farm_type,crop,mixed|array|nullable',
            'crops.*' => 'string|exists:crops,name',
            'season_type' => 'required_if:farm_type,crop,mixed|string|in:short_rain,long_rain,year-round|nullable',
            'livestock_types' => 'required_if:farm_type,livestock,mixed|array|nullable',
            'livestock_types.*' => 'string|exists:livestock_types,name',
            'production_goal' => 'required_if:farm_type,livestock,mixed|string|in:meat,milk,eggs,breeding|nullable',
        ]);

        // Build farm_operation_details array based on the new farm_type
        $operationDetails = [];
        $farmType = $validated['farm_type'];
        
        if (in_array($farmType, ['crop', 'mixed'])) {
            $operationDetails['crops'] = $request->input('crops', []);
            $operationDetails['season_type'] = $request->input('season_type');
        }
        
        if (in_array($farmType, ['livestock', 'mixed'])) {
            $operationDetails['livestock_types'] = $request->input('livestock_types', []);
            $operationDetails['production_goal'] = $request->input('production_goal');
        }

        Farm::create([
            ...$validated,
            'user_id' => auth()->id(),
            'farm_operation_details' => !empty($operationDetails) ? $operationDetails : null,
        ]);

        return redirect()->route('farms.index')
            ->with('success', 'Farm created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Farm $farm)
    {
        $farm->load(['fields.sensors', 'fields.crop']);

        return view('farms.show', compact('farm'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Farm $farm)
    {
        $crops = \App\Models\Crop::select('id', 'name')->orderBy('name')->get();
        $livestockTypes = \App\Models\LivestockType::select('id', 'name')->orderBy('name')->get();

        return view('farms.edit', compact('farm', 'crops', 'livestockTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Farm $farm)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'farm_type' => 'required|string|in:crop,livestock,mixed',
            'ownership_type' => 'required|string|in:owned,leased,community',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'size_hectares' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            // Conditional fields
            'crops' => 'required_if:farm_type,crop,mixed|array|nullable',
            'crops.*' => 'string|exists:crops,name',
            'season_type' => 'required_if:farm_type,crop,mixed|string|in:short_rain,long_rain,year-round|nullable',
            'livestock_types' => 'required_if:farm_type,livestock,mixed|array|nullable',
            'livestock_types.*' => 'string|exists:livestock_types,name',
            'production_goal' => 'required_if:farm_type,livestock,mixed|string|in:meat,milk,eggs,breeding|nullable',
        ]);

        // Build farm_operation_details based on the new farm_type
        $operationDetails = $farm->farm_operation_details ?? [];
        $newFarmType = $validated['farm_type'];

        if (in_array($newFarmType, ['crop', 'mixed'])) {
            $operationDetails['crops'] = $request->input('crops', []);
            $operationDetails['season_type'] = $request->input('season_type');
        } else {
            // Remove crop-related keys if switching away from crop/mixed
            unset($operationDetails['crops'], $operationDetails['season_type']);
        }

        if (in_array($newFarmType, ['livestock', 'mixed'])) {
            $operationDetails['livestock_types'] = $request->input('livestock_types', []);
            $operationDetails['production_goal'] = $request->input('production_goal');
        } else {
            // Remove livestock-related keys if switching away from livestock/mixed
            unset($operationDetails['livestock_types'], $operationDetails['production_goal']);
        }

        $farm->update(array_merge($validated, [
            'farm_operation_details' => !empty($operationDetails) ? $operationDetails : null,
        ]));

        return redirect()->route('farms.index')
            ->with('success', 'Farm updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Farm $farm)
    {
        $farm->delete();

        return redirect()->route('farms.index')
            ->with('success', 'Farm deleted successfully.');
    }
}
