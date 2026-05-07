<?php

namespace App\Http\Controllers;

use App\Models\FertilizerType;
use Illuminate\Http\Request;

class FertilizerTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fertilizerTypes = FertilizerType::all();

        return view('fertilizer_types.index', compact('fertilizerTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('fertilizer_types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:fertilizer_types',
            'description' => 'nullable|string',
            'type' => 'required|string|in:nitrogen,phosphorus,potassium,compound,organic',
            'default_unit' => 'required|string|max:50',
            'min_threshold' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        FertilizerType::create($request->all());

        return redirect()->route('fertilizer_types.index')
            ->with('success', 'Fertilizer type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FertilizerType $fertilizerType)
    {
        return view('fertilizer_types.show', compact('fertilizerType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FertilizerType $fertilizerType)
    {
        return view('fertilizer_types.edit', compact('fertilizerType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FertilizerType $fertilizerType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:fertilizer_types,name,'.$fertilizerType->id,
            'description' => 'nullable|string',
            'type' => 'required|string|in:nitrogen,phosphorus,potassium,compound,organic',
            'default_unit' => 'required|string|max:50',
            'min_threshold' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $fertilizerType->update($request->all());

        return redirect()->route('fertilizer_types.index')
            ->with('success', 'Fertilizer type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FertilizerType $fertilizerType)
    {
        $fertilizerType->delete();

        return redirect()->route('fertilizer_types.index')
            ->with('success', 'Fertilizer type deleted successfully.');
    }
}
