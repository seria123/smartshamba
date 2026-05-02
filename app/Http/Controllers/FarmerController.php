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

        return view('farms.onboarding', compact('crops', 'livestockTypes'));
    }

    /**
     * Save farm onboarding details (Step 1: Farm Identity + Operation Details)
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
            // Conditional fields
            'crops' => 'required_if:farm_type,crop,mixed|array|nullable',
            'crops.*' => 'string|exists:crops,name',
            'season_type' => 'required_if:farm_type,crop,mixed|string|in:short_rain,long_rain,year-round|nullable',
            'livestock_types' => 'required_if:farm_type,livestock,mixed|array|nullable',
            'livestock_types.*' => 'string|exists:livestock_types,name',
            'production_goal' => 'required_if:farm_type,livestock,mixed|string|in:meat,milk,eggs,breeding|nullable',
        ]);

        // Build farm_operation_details array
        $operationDetails = [];
        
        if (in_array($validated['farm_type'], ['crop', 'mixed'])) {
            $operationDetails['crops'] = $request->input('crops', []);
            $operationDetails['season_type'] = $request->input('season_type');
        }
        
        if (in_array($validated['farm_type'], ['livestock', 'mixed'])) {
            $operationDetails['livestock_types'] = $request->input('livestock_types', []);
            $operationDetails['production_goal'] = $request->input('production_goal');
        }

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
