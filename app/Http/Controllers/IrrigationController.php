<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\IrrigationLog;
use App\Models\IrrigationZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IrrigationController extends Controller
{
    /**
     * Display a listing of irrigation zones.
     */
    public function index(Request $request): View
    {
        $query = IrrigationZone::with(['field']);

        if ($request->filled('field_id')) {
            $query->where('field_id', $request->field_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $zones = $query->orderBy('name', 'asc')->paginate(15);
        $fields = Field::all();

        return view('irrigation.index', compact('zones', 'fields'));
    }

    /**
     * Show the form for creating a new irrigation zone.
     */
    public function create(): View
    {
        $fields = Field::all();

        $scheduleTypes = [
            IrrigationZone::SCHEDULE_DAILY => 'Daily',
            IrrigationZone::SCHEDULE_WEEKDAYS => 'Weekdays',
            IrrigationZone::SCHEDULE_CUSTOM => 'Custom Days',
            IrrigationZone::SCHEDULE_MANUAL => 'Manual Only',
        ];

        $statuses = [
            IrrigationZone::STATUS_ACTIVE => 'Active',
            IrrigationZone::STATUS_INACTIVE => 'Inactive',
            IrrigationZone::STATUS_MAINTENANCE => 'Under Maintenance',
            IrrigationZone::STATUS_ERROR => 'Error',
        ];

        return view('irrigation.create', compact('fields', 'scheduleTypes', 'statuses'));
    }

    /**
     * Store a newly created irrigation zone in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'field_id' => 'required|exists:fields,id',
            'controller_id' => 'nullable|string|max:100',
            'valve_id' => 'nullable|string|max:100',
            'status' => 'required|string',
            'flow_rate_lph' => 'nullable|numeric|min:0',
            'duration_minutes' => 'nullable|integer|min:1',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'schedule_type' => 'required|string',
            'schedule_days' => 'nullable|array',
            'soil_moisture_threshold' => 'nullable|numeric|min:0|max:100',
        ]);

        $validated['is_active'] = $request->has('is_active');

        IrrigationZone::create($validated);

        return redirect()->route('irrigation.index')
            ->with('success', 'Irrigation zone created successfully.');
    }

    /**
     * Display the specified irrigation zone.
     */
    public function show(IrrigationZone $irrigation): View
    {
        $irrigation->load(['field', 'logs' => function ($query) {
            $query->orderBy('started_at', 'desc')->limit(10);
        }]);

        return view('irrigation.show', compact('irrigation'));
    }

    /**
     * Show the form for editing the specified irrigation zone.
     */
    public function edit(IrrigationZone $irrigation): View
    {
        $fields = Field::all();

        $scheduleTypes = [
            IrrigationZone::SCHEDULE_DAILY => 'Daily',
            IrrigationZone::SCHEDULE_WEEKDAYS => 'Weekdays',
            IrrigationZone::SCHEDULE_CUSTOM => 'Custom Days',
            IrrigationZone::SCHEDULE_MANUAL => 'Manual Only',
        ];

        $statuses = [
            IrrigationZone::STATUS_ACTIVE => 'Active',
            IrrigationZone::STATUS_INACTIVE => 'Inactive',
            IrrigationZone::STATUS_MAINTENANCE => 'Under Maintenance',
            IrrigationZone::STATUS_ERROR => 'Error',
        ];

        return view('irrigation.edit', compact('irrigation', 'fields', 'scheduleTypes', 'statuses'));
    }

    /**
     * Update the specified irrigation zone in storage.
     */
    public function update(Request $request, IrrigationZone $irrigation): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'field_id' => 'required|exists:fields,id',
            'controller_id' => 'nullable|string|max:100',
            'valve_id' => 'nullable|string|max:100',
            'status' => 'required|string',
            'flow_rate_lph' => 'nullable|numeric|min:0',
            'duration_minutes' => 'nullable|integer|min:1',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'schedule_type' => 'required|string',
            'schedule_days' => 'nullable|array',
            'soil_moisture_threshold' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $irrigation->update($validated);

        return redirect()->route('irrigation.show', $irrigation)
            ->with('success', 'Irrigation zone updated successfully.');
    }

/**
     * Remove the specified irrigation zone from storage.
     */
    public function destroy(IrrigationZone $irrigation): RedirectResponse
    {
        $irrigation->delete();

        return redirect()->route('irrigation.index')
            ->with('success', 'Irrigation zone deleted successfully.');
    }

    /**
     * Start irrigation manually.
     */
    public function start(IrrigationZone $irrigation): RedirectResponse
    {
        $log = IrrigationLog::create([
            'irrigation_zone_id' => $irrigation->id,
            'triggered_by' => auth()->id(),
            'event_type' => IrrigationLog::EVENT_MANUAL,
            'started_at' => now(),
            'status' => IrrigationLog::STATUS_RUNNING,
        ]);

        return redirect()->back()->with('success', 'Irrigation started.');
    }

    /**
     * Stop irrigation manually.
     */
    public function stop(IrrigationZone $irrigation): RedirectResponse
    {
        $activeLog = $irrigation->getActiveLog();

        if ($activeLog) {
            $activeLog->update([
                'ended_at' => now(),
                'duration_minutes' => $activeLog->started_at->diffInMinutes(now()),
                'status' => IrrigationLog::STATUS_COMPLETED,
            ]);
        }

        return redirect()->back()->with('success', 'Irrigation stopped.');
    }

    /**
     * Create a new irrigation record with detailed data.
     */
    public function createRecord(IrrigationZone $irrigation): View
    {
        $fields = Field::all();

        $methods = [
            IrrigationLog::METHOD_DRIP => 'Drip',
            IrrigationLog::METHOD_SPRINKLER => 'Sprinkler',
            IrrigationLog::METHOD_FLOOD => 'Flood',
            IrrigationLog::METHOD_CENTER_PIVOT => 'Center Pivot',
            IrrigationLog::METHOD_SUBSURFACE => 'Subsurface',
            IrrigationLog::METHOD_MANUAL => 'Manual',
        ];

        $sources = [
            IrrigationLog::SOURCE_BOREHOLE => 'Borehole',
            IrrigationLog::SOURCE_RIVER => 'River',
            IrrigationLog::SOURCE_DAM => 'Dam',
            IrrigationLog::SOURCE_RAINWATER => 'Rainwater',
            IrrigationLog::SOURCE_MUNICIPAL => 'Municipal',
            IrrigationLog::SOURCE_WELL => 'Well',
            IrrigationLog::SOURCE_CANAL => 'Canal',
        ];

        $costTypes = [
            IrrigationLog::COST_TYPE_PUMP => 'Pump',
            IrrigationLog::COST_TYPE_FUEL => 'Fuel',
            IrrigationLog::COST_TYPE_ELECTRICITY => 'Electricity',
            IrrigationLog::COST_TYPE_LABOR => 'Labor',
        ];

        return view('irrigation.create-record', compact('irrigation', 'fields', 'methods', 'sources', 'costTypes'));
    }

    /**
     * Store a new irrigation record.
     */
    public function storeRecord(Request $request, IrrigationZone $irrigation): RedirectResponse
    {
        $validated = $request->validate([
            'started_at' => 'required|date',
            'ended_at' => 'nullable|date|after:started_at',
            'irrigation_method' => 'required|string',
            'water_source' => 'required|string',
            'duration_minutes' => 'nullable|integer|min:0',
            'water_used_liters' => 'nullable|numeric|min:0',
            'estimated_volume_liters' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'cost_type' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if (!$validated['duration_minutes'] && $validated['started_at'] && $validated['ended_at']) {
            $start = \Carbon\Carbon::parse($validated['started_at']);
            $end = \Carbon\Carbon::parse($validated['ended_at']);
            $validated['duration_minutes'] = $start->diffInMinutes($end);
        }

        $validated['irrigation_zone_id'] = $irrigation->id;
        $validated['triggered_by'] = auth()->id();
        $validated['event_type'] = IrrigationLog::EVENT_MANUAL;
        $validated['status'] = $validated['ended_at'] ? IrrigationLog::STATUS_COMPLETED : IrrigationLog::STATUS_RUNNING;

        IrrigationLog::create($validated);

        return redirect()->route('irrigation.logs')->with('success', 'Irrigation record created successfully.');
    }

    /**
     * Get irrigation logs.
     */
    public function logs(Request $request): View
    {
        $query = IrrigationLog::with(['irrigationZone.field', 'triggeredByUser']);

        if ($request->filled('zone_id')) {
            $query->where('irrigation_zone_id', $request->zone_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('started_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('started_at', '<=', $request->to_date);
        }

        $logs = $query->orderBy('started_at', 'desc')->paginate(20);
        $zones = IrrigationZone::with('field')->get();

        return view('irrigation.logs', compact('logs', 'zones'));
    }

    /**
     * Get active irrigation zones for dashboard.
     */
    public function getActiveZones()
    {
        return IrrigationZone::with('field')
            ->where('is_active', true)
            ->where('status', IrrigationZone::STATUS_ACTIVE)
            ->get();
    }

    /**
     * Get today's irrigation summary.
     */
    public function getTodaySummary()
    {
        $totalWater = IrrigationLog::whereDate('started_at', now()->toDateString())
            ->where('status', IrrigationLog::STATUS_COMPLETED)
            ->sum('water_used_liters');

        $totalDuration = IrrigationLog::whereDate('started_at', now()->toDateString())
            ->where('status', IrrigationLog::STATUS_COMPLETED)
            ->sum('duration_minutes');

        $activeCount = IrrigationLog::where('status', IrrigationLog::STATUS_RUNNING)->count();

        return [
            'total_water_liters' => $totalWater ?? 0,
            'total_duration_minutes' => $totalDuration ?? 0,
            'active_irrigations' => $activeCount,
        ];
    }
}
