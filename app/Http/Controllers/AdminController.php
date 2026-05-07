<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Crop;
use App\Models\Farm;
use App\Models\Field;
use App\Models\Harvest;
use App\Models\Livestock;
use App\Models\Revenue;
use App\Models\Sensor;
use App\Models\Staff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        $stats = [
            'total_farmers' => User::where('role', 'user')->count(),
            'total_farms' => Farm::count(),
            'total_crops' => Crop::count(),
            'total_livestock' => Livestock::count(),
            'active_users_today' => User::where('status', 'active')
                ->whereNotNull('last_seen_at')
                ->where('last_seen_at', '>=', now()->startOfDay())
                ->count(),
            'total_revenue' => Revenue::sum('amount'),
            // Existing stats for backward compatibility
            'total_fields' => Field::count(),
            'total_sensors' => Sensor::count(),
            'active_sensors' => Sensor::where('status', 'active')->count(),
            'total_staff' => Staff::count(),
            'total_harvests' => Harvest::count(),
        ];

        $recentActivity = Alert::with('sensorReading.sensor.field')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($alert) {
                return [
                    'type' => 'alert',
                    'details' => $alert->message,
                    'user' => 'System',
                    'date' => $alert->created_at,
                    'status' => $alert->is_read ? 'success' : 'error',
                ];
            });

        // Chart Data
        $chartData = [
            // 1. Farmers growth over time (last 12 months)
            'farmers_growth' => $this->getFarmersGrowthData(),

            // 2. Crop distribution by category
            'crop_distribution' => $this->getCropDistributionData(),

            // 3. Farm registrations per month (last 12 months)
            'farm_registrations' => $this->getFarmRegistrationsData(),

            // 4. Livestock types distribution
            'livestock_distribution' => $this->getLivestockDistributionData(),
        ];

        return view('admin.dashboard', compact('stats', 'recentActivity', 'chartData'));
    }

    /**
     * Get farmers growth data for the last 12 months (line chart).
     */
    private function getFarmersGrowthData(): array
    {
        $months = collect();
        $farmersCount = collect();

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push($date->format('M Y'));

            $count = User::where('role', 'user')
                ->where('created_at', '<=', $date->endOfMonth())
                ->count();
            $farmersCount->push($count);
        }

        return [
            'labels' => $months->toArray(),
            'data' => $farmersCount->toArray(),
        ];
    }

    /**
     * Get crop distribution grouped by category (pie chart).
     */
    private function getCropDistributionData(): array
    {
        $distribution = Crop::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category');

        return [
            'labels' => $distribution->keys()->toArray(),
            'data' => $distribution->values()->toArray(),
        ];
    }

    /**
     * Get farm registrations per month for the last 12 months (bar chart).
     */
    private function getFarmRegistrationsData(): array
    {
        $months = collect();
        $counts = collect();

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push($date->format('M'));

            $count = Farm::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $counts->push($count);
        }

        return [
            'labels' => $months->toArray(),
            'data' => $counts->toArray(),
        ];
    }

    /**
     * Get livestock distribution by type (bar chart).
     */
    private function getLivestockDistributionData(): array
    {
        $distribution = \App\Models\LivestockType::withCount('livestock')
            ->orderBy('livestock_count', 'desc')
            ->get()
            ->pluck('livestock_count', 'name');

        // If no livestock types exist, return empty
        if ($distribution->isEmpty()) {
            return ['labels' => [], 'data' => []];
        }

        return [
            'labels' => $distribution->keys()->toArray(),
            'data' => $distribution->values()->toArray(),
        ];
    }

    /**
     * Onboard a new staff member (admin only).
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

        Staff::create($validated);

        return redirect()->route('dashboard')
            ->with('success', 'Staff member onboarded successfully');
    }

    /**
     * Fire/terminate a staff member (admin only).
     */
    public function fireWorker(Staff $staff)
    {
        $staff->update([
            'status' => 'terminated',
            'termination_date' => now(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Staff member terminated successfully');
    }
}
