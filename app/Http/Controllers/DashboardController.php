<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Farm;
use App\Models\Field;
use App\Models\Harvest;
use App\Models\Sensor;
use App\Models\Staff;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        // Farm overview statistics
        $totalFarms = Farm::count();
        $totalFields = Field::count();
        $totalSensors = Sensor::count();
        $activeSensors = Sensor::where('status', 'active')->count();

        // Worker statistics
        $totalWorkers = Staff::count();
        $activeWorkers = Staff::where('status', 'active')->count();

        // Harvest statistics
        $totalHarvests = Harvest::count();
        $thisYearHarvests = Harvest::whereYear('harvest_date', now()->year)->count();
        $thisYearQuantity = Harvest::whereYear('harvest_date', now()->year)->sum('quantity_harvested');

        // Get recent alerts
        $recentAlerts = Alert::with('sensorReading.sensor.field')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get unread alerts count
        $unreadAlertsCount = Alert::where('is_read', false)->count();

        // Get latest sensor readings
        $sensors = Sensor::with(['field', 'latestReading'])
            ->where('status', 'active')
            ->get();

        // Get farms with their fields count
        $farms = Farm::withCount('fields')->get();

        // Get recent activity (system events, recent actions)
        $recentActivity = [];
        // Could be extended to track system events, user actions, etc.
        // For now, populate with recent alerts as activity indicators
        $recentActivity = $recentAlerts->map(function ($alert) {
            return [
                'type' => 'alert',
                'details' => $alert->description,
                'user' => 'System',
                'date' => $alert->created_at,
                'status' => $alert->is_read ? 'success' : 'error',
            ];
        })->take(10)->toArray();

        return view('dashboard', compact('totalFarms', 'totalFields', 'totalSensors', 'activeSensors', 'totalWorkers', 'activeWorkers', 'totalHarvests', 'thisYearHarvests', 'thisYearQuantity', 'sensors', 'farms', 'recentAlerts', 'unreadAlertsCount', 'recentActivity'));
    }
}
