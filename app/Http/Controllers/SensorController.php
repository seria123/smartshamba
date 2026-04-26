<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Sensor;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sensors = Sensor::with('field')->get();
        return view('sensors.index', compact('sensors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fields = Field::all();
        return view('sensors.create', compact('fields'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'serial_number' => 'required|string|unique:sensors,serial_number',
            'status' => 'required|string|in:active,inactive,maintenance',
            'description' => 'nullable|string',
        ]);

        Sensor::create($validated);

        return redirect()->route('sensors.index')
            ->with('success', 'Sensor created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sensor $sensor)
    {
        $sensor->load(['field', 'sensorReadings']);
        return view('sensors.show', compact('sensor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sensor $sensor)
    {
        $fields = Field::all();
        return view('sensors.edit', compact('sensor', 'fields'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sensor $sensor)
    {
        $validated = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'serial_number' => 'required|string|unique:sensors,serial_number,' . $sensor->id,
            'status' => 'required|string|in:active,inactive,maintenance',
            'description' => 'nullable|string',
        ]);

        $sensor->update($validated);

        return redirect()->route('sensors.index')
            ->with('success', 'Sensor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sensor $sensor)
    {
        $sensor->delete();

        return redirect()->route('sensors.index')
            ->with('success', 'Sensor deleted successfully.');
    }
}
