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
            // Per-crop details (array of objects)
            'crops' => 'required_if:farm_type,crop,mixed|array|nullable',
            'crops.*.name' => 'nullable|string|exists:crops,name',
            'crops.*.season_type' => 'nullable|string|in:short_rain,long_rain,year-round',
            'crops.*.variety' => 'nullable|string|max:255',
            'crops.*.quantity' => 'nullable|numeric|min:0',
            'crops.*.notes' => 'nullable|string|max:500',
            // season_type kept for backward compat
            'season_type' => 'nullable|string|in:short_rain,long_rain,year-round',
            // Per-livestock details (array of objects)
            'livestock' => 'required_if:farm_type,livestock,mixed|array|nullable',
            'livestock.*.name' => 'nullable|string|exists:livestock_types,name',
            'livestock.*.quantity' => 'nullable|integer|min:1',
            'livestock.*.production_goal' => 'nullable|string|in:meat,milk,eggs,breeding',
            'livestock.*.notes' => 'nullable|string|max:500',
            // livestock_types kept for backward compat
            'livestock_types' => 'nullable|array',
            'livestock_types.*' => 'string|exists:livestock_types,name',
            // production_goal kept for backward compat
            'production_goal' => 'nullable|string|in:meat,milk,eggs,breeding',
        ]);

        // Extract staff counts (not direct columns)
        $staffPermanent = $validated['staff_permanent'] ?? 0;
        $staffCasual = $validated['staff_casual'] ?? 0;
        unset($validated['staff_permanent'], $validated['staff_casual']);

        // Build farm_operation_details array
        $operationDetails = [];
        $farmType = $validated['farm_type'];

        if (in_array($farmType, ['crop', 'mixed'])) {
            $rawCrops = $request->input('crops', []);
            // Sanitize: keep only entries with a crop name
            $operationDetails['crops_details'] = array_values(array_filter($rawCrops, fn($c) => !empty($c['name'])));
            if ($request->filled('season_type')) {
                $operationDetails['season_type'] = $request->input('season_type');
            }
            // Legacy flat key for backwards compat
            $operationDetails['crops'] = array_column($operationDetails['crops_details'], 'name');
        }

        if (in_array($farmType, ['livestock', 'mixed'])) {
            $rawLs = $request->input('livestock', []);
            $operationDetails['livestock_details'] = array_values(array_filter($rawLs, fn($l) => !empty($l['name'])));
            if ($request->filled('production_goal')) {
                $operationDetails['production_goal'] = $request->input('production_goal');
            }
            // Legacy flat key for backwards compat
            $operationDetails['livestock_types'] = array_column($operationDetails['livestock_details'], 'name');
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
            // Staff fields
            'staff_permanent' => 'nullable|integer|min:0',
            'staff_casual' => 'nullable|integer|min:0',
            // Per-crop detail fields
            'crops' => 'nullable|array',
            'crops.*.name' => 'nullable|string|exists:crops,name',
            'crops.*.season_type' => 'nullable|string|in:short_rain,long_rain,year-round',
            'crops.*.variety' => 'nullable|string|max:255',
            'crops.*.quantity' => 'nullable|numeric|min:0',
            'crops.*.notes' => 'nullable|string|max:500',
            // season_type kept for backward compat
            'season_type' => 'nullable|string|in:short_rain,long_rain,year-round',
            // Per-livestock detail fields
            'livestock' => 'nullable|array',
            'livestock.*.name' => 'nullable|string|exists:livestock_types,name',
            'livestock.*.quantity' => 'nullable|integer|min:1',
            'livestock.*.production_goal' => 'nullable|string|in:meat,milk,eggs,breeding',
            'livestock.*.notes' => 'nullable|string|max:500',
            // Legacy flat keys
            'livestock_types' => 'nullable|array',
            'livestock_types.*' => 'string|exists:livestock_types,name',
            'production_goal' => 'nullable|string|in:meat,milk,eggs,breeding',
        ]);

        // Extract staff counts
        $staffPermanent = $validated['staff_permanent'] ?? 0;
        $staffCasual = $validated['staff_casual'] ?? 0;
        unset($validated['staff_permanent'], $validated['staff_casual']);

        // Merge old operation details so we don't lose unrelated keys
        $operationDetails = $farm->farm_operation_details ?? [];
        $newFarmType = $validated['farm_type'];

        if (in_array($newFarmType, ['crop', 'mixed'])) {
            $rawCrops = $request->input('crops', []);
            $operationDetails['crops_details'] = array_values(array_filter($rawCrops, fn($c) => !empty($c['name'])));
            if ($request->filled('season_type')) {
                $operationDetails['season_type'] = $request->input('season_type');
            }
            // Legacy flat key
            $operationDetails['crops'] = array_column($operationDetails['crops_details'], 'name');
        } else {
            unset($operationDetails['crops_details'], $operationDetails['season_type'], $operationDetails['crops']);
        }

        if (in_array($newFarmType, ['livestock', 'mixed'])) {
            $rawLs = $request->input('livestock', []);
            $operationDetails['livestock_details'] = array_values(array_filter($rawLs, fn($l) => !empty($l['name'])));
            if ($request->filled('production_goal')) {
                $operationDetails['production_goal'] = $request->input('production_goal');
            }
            // Legacy flat key
            $operationDetails['livestock_types'] = array_column($operationDetails['livestock_details'], 'name');
        } else {
            unset($operationDetails['livestock_details'], $operationDetails['production_goal'], $operationDetails['livestock_types']);
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
