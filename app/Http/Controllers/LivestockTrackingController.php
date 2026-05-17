<?php

namespace App\Http\Controllers;

use App\Models\Livestock;
use Illuminate\Http\Request;

class LivestockTrackingController extends Controller
{
    /**
     * Display livestock tracking overview - all livestock with current locations.
     */
    public function index(Request $request)
    {
        $query = Livestock::with(['type', 'farm', 'currentLocation.field', 'currentLocation.farm'])
            ->whereIn('status', ['healthy', 'sick']);

        // Filter by farm if provided
        if ($request->filled('farm_id')) {
            $query->where('farm_id', $request->farm_id);
        }

        // Search by tag number or name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tag_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $livestock = $query->paginate(20);
        $farms = \App\Models\Farm::all();

        // Summary stats
        $totalTracked = Livestock::whereIn('status', ['healthy', 'sick'])->count();
        $withCurrentLocation = Livestock::whereHas('currentLocation')->count();
        $inTransit = Livestock::whereHas('currentLocation', function ($q) {
            $q->where('location_type', 'transport');
        })->count();

        return view('livestock.tracking.index', compact(
            'livestock',
            'farms',
            'totalTracked',
            'withCurrentLocation',
            'inTransit'
        ));
    }

    /**
     * Show recent movements across all livestock.
     */
    public function recentMovements()
    {
        $movements = \App\Models\LivestockMovement::with(['livestock.type', 'fromFarm', 'toFarm'])
            ->orderBy('movement_date', 'desc')
            ->paginate(20);

        return view('livestock.tracking.recent_movements', compact('movements'));
    }

    /**
     * Show livestock by location (grouped by field/farm).
     */
    public function byLocation()
    {
        $locations = \App\Models\LivestockLocation::with(['livestock.type', 'field', 'farm'])
            ->active()
            ->orderBy('entered_at', 'desc')
            ->paginate(30);

        // Group by field
        $byField = $locations->groupBy('field_id');
        $byFarm = $locations->groupBy('farm_id');

        return view('livestock.tracking.by_location', compact('locations', 'byField', 'byFarm'));
    }
    public function store(Request $request, Livestock $livestock)
{
    $data = $request->validate([
        'gps_latitude' => 'required',
        'gps_longitude' => 'required',
        'movement_type' => 'nullable|string',
        'location_type' => 'nullable|string',
        'notes' => 'nullable|string',
    ]);

    $location = $livestock->locations()->create($data);

    return response()->json([
        'success' => true,
        'message' => 'Location saved',
        'data' => $location
    ]);
}
}
