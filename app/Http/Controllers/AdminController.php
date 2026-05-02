<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Worker;
use Illuminate\Http\Request;
use App\Models\Farm;
use App\Models\Field;
use App\Models\Sensor;
use App\Models\Harvest;
use App\Models\User;


class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
 public function dashboard()
{
    $stats = [
        'total_farms' => Farm::count(),
        'total_fields' => Field::count(),
        'total_sensors' => Sensor::count(),
        'active_sensors' => Sensor::where('status', 'active')->count(),
        'total_workers' => Worker::count(),
        'total_harvests' => Harvest::count(),
    ];

    $recentActivity = Alert::with('sensorReading.sensor.field')
        ->latest()
        ->take(10)
        ->get()
        ->map(function ($alert) {
            return [
                'type' => 'alert',
                'details' => $alert->description,
                'user' => 'System',
                'date' => $alert->created_at,
                'status' => $alert->is_read ? 'success' : 'error',
            ];
        });

    return view('admin.dashboard', compact('stats', 'recentActivity'));
}

    /**
     * Onboard a new worker (admin only).
     */
    public function onboardWorker(Request $request)
    {
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'role' => 'required|string',
            'daily_wage' => 'required|numeric|min:0',
            'payment_type' => 'required|in:daily,weekly,monthly,piece_rate',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:100',
            'emergency_phone' => 'nullable|string|max:20',
            'hire_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        Worker::create($validated);

        return redirect()->route('dashboard')
            ->with('success', 'Worker onboarded successfully');
    }

    /**
     * Fire/terminate a worker (admin only).
     */
    public function fireWorker(Worker $worker)
    {
        $worker->update([
            'status' => 'terminated',
            'termination_date' => now(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Worker terminated successfully');
    }
}
