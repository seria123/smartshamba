<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\SensorReading;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $alerts = Alert::with('sensorReading.sensor.field')->orderBy('created_at', 'desc')->get();
        return view('alerts.index', compact('alerts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sensorReadings = SensorReading::with('sensor')->get();
        return view('alerts.create', compact('sensorReadings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sensor_reading_id' => 'required|exists:sensor_readings,id',
            'type' => 'required|string|in:warning,critical,info',
            'severity' => 'required|string|in:low,medium,high',
            'message' => 'required|string',
            'parameter' => 'required|string',
            'value' => 'nullable|numeric',
            'threshold' => 'nullable|numeric',
        ]);

        Alert::create($validated);

        return redirect()->route('alerts.index')
            ->with('success', 'Alert created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Alert $alert)
    {
        $alert->load('sensorReading.sensor.field');
        return view('alerts.show', compact('alert'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Alert $alert)
    {
        $sensorReadings = SensorReading::with('sensor')->get();
        return view('alerts.edit', compact('alert', 'sensorReadings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Alert $alert)
    {
        $validated = $request->validate([
            'sensor_reading_id' => 'required|exists:sensor_readings,id',
            'type' => 'required|string|in:warning,critical,info',
            'severity' => 'required|string|in:low,medium,high',
            'message' => 'required|string',
            'parameter' => 'required|string',
            'value' => 'nullable|numeric',
            'threshold' => 'nullable|numeric',
            'is_read' => 'boolean',
        ]);

        $alert->update($validated);

        return redirect()->route('alerts.index')
            ->with('success', 'Alert updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Alert $alert)
    {
        $alert->delete();

        return redirect()->route('alerts.index')
            ->with('success', 'Alert deleted successfully.');
    }

    /**
     * Mark alert as read.
     */
    public function markAsRead(Alert $alert)
    {
        $alert->update(['is_read' => true]);

        return redirect()->route('alerts.index')
            ->with('success', 'Alert marked as read.');
    }
}
