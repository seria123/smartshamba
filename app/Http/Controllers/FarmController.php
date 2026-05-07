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
            'location' => 'required|string|max:255',
            'subcounty' => 'nullable|string|max:255',
            'physical_address' => 'nullable|string|max:500',
            'farm_type' => 'required|string|in:crop,livestock,mixed',
            'ownership_type' => 'required|string|in:owned,leased,community',
            'storage_facilities' => 'required|boolean',
            'estimated_budget' => 'nullable|numeric|min:0',
            'main_purpose' => 'nullable|string|in:commercial,subsistence',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'size_hectares' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            // Staff fields (stored in farm_operation_details)
            'staff_permanent' => 'nullable|integer|min:0',
            'staff_casual' => 'nullable|integer|min:0',
            // Conditional fields
            'crops' => 'required_if:farm_type,crop,mixed|array|nullable',
            'crops.*' => 'string|exists:crops,name',
            'season_type' => 'required_if:farm_type,crop,mixed|string|in:short_rain,long_rain,year-round|nullable',
            'livestock_types' => 'required_if:farm_type,livestock,mixed|array|nullable',
            'livestock_types.*' => 'string|exists:livestock_types,name',
            'production_goal' => 'required_if:farm_type,livestock,mixed|string|in:meat,milk,eggs,breeding|nullable',
        ]);

        // Extract staff counts (not direct columns)
        $staffPermanent = $validated['staff_permanent'] ?? 0;
        $staffCasual = $validated['staff_casual'] ?? 0;
        unset($validated['staff_permanent'], $validated['staff_casual']);

        // Build farm_operation_details array
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

        // Add staff counts
        $operationDetails['staff_permanent'] = $staffPermanent;
        $operationDetails['staff_casual'] = $staffCasual;

        $farm = Farm::create([
            ...$validated,
            'user_id' => auth()->id(),
            'farm_operation_details' => ! empty($operationDetails) ? $operationDetails : null,
        ]);

        return redirect()->route('farms.index')
            ->with('success', 'Farm created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Farm $farm)
    {
        $farm->load([
            'fields.sensors',
            'fields.crop',
            'images',
            'documents.uploader',
        ]);

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
            'location' => 'required|string|max:255',
            'subcounty' => 'nullable|string|max:255',
            'physical_address' => 'nullable|string|max:500',
            'farm_type' => 'required|string|in:crop,livestock,mixed',
            'ownership_type' => 'required|string|in:owned,leased,community',
            'storage_facilities' => 'required|boolean',
            'estimated_budget' => 'nullable|numeric|min:0',
            'main_purpose' => 'nullable|string|in:commercial,subsistence',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'size_hectares' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            // Staff fields (stored in farm_operation_details)
            'staff_permanent' => 'nullable|integer|min:0',
            'staff_casual' => 'nullable|integer|min:0',
            // Conditional fields
            'crops' => 'required_if:farm_type,crop,mixed|array|nullable',
            'crops.*' => 'string|exists:crops,name',
            'season_type' => 'required_if:farm_type,crop,mixed|string|in:short_rain,long_rain,year-round|nullable',
            'livestock_types' => 'required_if:farm_type,livestock,mixed|array|nullable',
            'livestock_types.*' => 'string|exists:livestock_types,name',
            'production_goal' => 'required_if:farm_type,livestock,mixed|string|in:meat,milk,eggs,breeding|nullable',
        ]);

        // Extract staff counts (not direct columns)
        $staffPermanent = $validated['staff_permanent'] ?? 0;
        $staffCasual = $validated['staff_casual'] ?? 0;
        unset($validated['staff_permanent'], $validated['staff_casual']);

        // Build farm_operation_details based on the farm_type
        $operationDetails = $farm->farm_operation_details ?? [];
        $newFarmType = $validated['farm_type'];

        if (in_array($newFarmType, ['crop', 'mixed'])) {
            $operationDetails['crops'] = $request->input('crops', []);
            $operationDetails['season_type'] = $request->input('season_type');
        } else {
            unset($operationDetails['crops'], $operationDetails['season_type']);
        }

        if (in_array($newFarmType, ['livestock', 'mixed'])) {
            $operationDetails['livestock_types'] = $request->input('livestock_types', []);
            $operationDetails['production_goal'] = $request->input('production_goal');
        } else {
            unset($operationDetails['livestock_types'], $operationDetails['production_goal']);
        }

        // Always update staff counts
        $operationDetails['staff_permanent'] = $staffPermanent;
        $operationDetails['staff_casual'] = $staffCasual;

        $farm->update(array_merge($validated, [
            'farm_operation_details' => ! empty($operationDetails) ? $operationDetails : null,
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
