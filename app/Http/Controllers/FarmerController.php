<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FarmerController extends Controller
{
    /**
     * Show onboarding form
     */
    public function create()
    {
        // Load available crops and livestock types for dropdowns
        $crops = \App\Models\Crop::select('id', 'name')->orderBy('name')->get();
        $livestockTypes = \App\Models\LivestockType::select('id', 'name')->orderBy('name')->get();

        return view('farms.create', compact('crops', 'livestockTypes'));
    }

    /**
     * Save farm onboarding details (Step 1: Farm Identity + Operation Details)
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

        $farm = Farm::updateOrCreate(
            ['user_id' => auth()->id()],
            array_merge($validated, [
                'farm_operation_details' => !empty($operationDetails) ? $operationDetails : null,
            ])
        );

        return redirect()->route('dashboard')
            ->with('success', 'Farm profile created')
            ->with('info', 'Base configuration loaded');
    }
}
