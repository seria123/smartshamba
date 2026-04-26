<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class FieldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fields = Field::with(['farm', 'sensors'])->get();

        return view('fields.index', compact('fields'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $farms = Farm::all();

        return view('fields.create', compact('farms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'name' => 'required|string|max:255',
            'size_hectares' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'soil_type' => 'nullable|string|max:255',
            'gps_latitude' => 'nullable|between:-90,90',
            'gps_longitude' => 'nullable|between:-180,180',
            'description' => 'nullable|string',
        ]);

        $validated['user_id'] = $request->user()->id;

        Field::create($validated);

        return redirect()->route('fields.index')
            ->with('success', 'Field created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Field $field)
    {
        $field->load(['farm', 'sensors.sensorReadings']);

        return view('fields.show', compact('field'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Field $field)
    {
        $farms = Farm::all();

        return view('fields.edit', compact('field', 'farms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Field $field)
    {
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'name' => 'required|string|max:255',
            'size_hectares' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'soil_type' => 'nullable|string|max:255',
            'gps_latitude' => 'nullable|between:-90,90',
            'gps_longitude' => 'nullable|between:-180,180',
            'description' => 'nullable|string',
        ]);

        $field->update(Arr::except($validated, ['user_id']));

        return redirect()->route('fields.index')
            ->with('success', 'Field updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Field $field)
    {
        $field->delete();

        return redirect()->route('fields.index')
            ->with('success', 'Field deleted successfully.');
    }
}
