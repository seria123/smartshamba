<?php

namespace App\Http\Controllers;

use App\Models\CropAnalysis;
use App\Models\Farm;
use App\Models\IrrigationLog;
use App\Models\Report;
use App\Models\SensorReading;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PDF;

class ReportController extends Controller
{
    /**
     * Display a listing of reports.
     */
    public function index(Request $request): View
    {
        $query = Report::with(['farm', 'generatedBy']);

        if ($request->filled('farm_id')) {
            $query->where('farm_id', $request->farm_id);
        }

        if ($request->filled('report_type')) {
            $query->where('report_type', $request->report_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(15);
        $farms = Farm::all();

        return view('reports.index', compact('reports', 'farms'));
    }

    /**
     * Show the form for creating a new report.
     */
    public function create(): View
    {
        $farms = Farm::all();

        $reportTypes = [
            Report::TYPE_DAILY_SUMMARY => 'Daily Summary',
            Report::TYPE_WEEKLY_SUMMARY => 'Weekly Summary',
            Report::TYPE_MONTHLY_SUMMARY => 'Monthly Summary',
            Report::TYPE_CROP_ANALYSIS => 'Crop Analysis Report',
            Report::TYPE_IRRIGATION_REPORT => 'Irrigation Report',
            Report::TYPE_SENSOR_ANALYSIS => 'Sensor Analysis Report',
            Report::TYPE_FINANCIAL => 'Financial Report',
            Report::TYPE_YIELD_PREDICTION => 'Yield Prediction',
            Report::TYPE_WEATHER_IMPACT => 'Weather Impact Report',
            Report::TYPE_AUTOMATION_PERFORMANCE => 'Automation Performance',
        ];

        return view('reports.create', compact('farms', 'reportTypes'));
    }

    /**
     * Generate a new report.
     */
    public function generate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'report_type' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'report_period_start' => 'required|date',
            'report_period_end' => 'required|date|after:report_period_start',
        ]);

        $validated['generated_by'] = auth()->id();
        $validated['status'] = Report::STATUS_GENERATING;

        $report = Report::create($validated);

        // Generate report data in background
        $this->generateReportData($report);

        return redirect()->route('reports.show', $report)
            ->with('success', 'Report generation started.');
    }

    /**
     * Generate report data based on type.
     */
    private function generateReportData(Report $report): void
    {
        try {
            $data = match ($report->report_type) {
                Report::TYPE_DAILY_SUMMARY => $this->generateDailySummary($report),
                Report::TYPE_WEEKLY_SUMMARY => $this->generateWeeklySummary($report),
                Report::TYPE_MONTHLY_SUMMARY => $this->generateMonthlySummary($report),
                Report::TYPE_IRRIGATION_REPORT => $this->generateIrrigationReport($report),
                Report::TYPE_SENSOR_ANALYSIS => $this->generateSensorAnalysis($report),
                Report::TYPE_CROP_ANALYSIS => $this->generateCropAnalysis($report),
                default => [],
            };

            $report->update([
                'data' => $data,
                'status' => Report::STATUS_COMPLETED,
            ]);
        } catch (\Exception $e) {
            $report->update([
                'status' => Report::STATUS_FAILED,
            ]);
        }
    }

    /**
     * Generate daily summary report.
     */
    private function generateDailySummary(Report $report): array
    {
        $date = $report->report_period_start->toDateString();

        $tasks = Task::whereDate('scheduled_date', $date)->get();
        $irrigations = IrrigationLog::whereDate('started_at', $date)->get();
        $sensorReadings = SensorReading::whereDate('recorded_at', $date)->get();

        return [
            'tasks_summary' => [
                'total' => $tasks->count(),
                'completed' => $tasks->where('status', Task::STATUS_COMPLETED)->count(),
                'pending' => $tasks->where('status', Task::STATUS_PENDING)->count(),
            ],
            'irrigation_summary' => [
                'total_sessions' => $irrigations->count(),
                'total_water_liters' => $irrigations->sum('water_used_liters'),
                'total_duration_minutes' => $irrigations->sum('duration_minutes'),
            ],
            'sensor_summary' => [
                'readings_count' => $sensorReadings->count(),
                'avg_temperature' => $sensorReadings->avg('temperature'),
                'avg_humidity' => $sensorReadings->avg('humidity'),
                'avg_soil_moisture' => $sensorReadings->avg('soil_moisture'),
            ],
        ];
    }

    /**
     * Generate weekly summary report.
     */
    private function generateWeeklySummary(Report $report): array
    {
        $startDate = $report->report_period_start;
        $endDate = $report->report_period_end;

        $tasks = Task::whereBetween('scheduled_date', [$startDate, $endDate])->get();
        $irrigations = IrrigationLog::whereBetween('started_at', [$startDate, $endDate])->get();

        return [
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
                'days' => $startDate->diffInDays($endDate) + 1,
            ],
            'tasks_summary' => [
                'total' => $tasks->count(),
                'completed' => $tasks->where('status', Task::STATUS_COMPLETED)->count(),
                'pending' => $tasks->where('status', Task::STATUS_PENDING)->count(),
                'completion_rate' => $tasks->count() > 0
                    ? round(($tasks->where('status', Task::STATUS_COMPLETED)->count() / $tasks->count()) * 100, 2)
                    : 0,
            ],
            'irrigation_summary' => [
                'total_sessions' => $irrigations->count(),
                'total_water_liters' => $irrigations->sum('water_used_liters'),
                'total_duration_minutes' => $irrigations->sum('duration_minutes'),
                'avg_daily_water' => $irrigations->sum('water_used_liters') / 7,
            ],
        ];
    }

    /**
     * Generate monthly summary report.
     */
    private function generateMonthlySummary(Report $report): array
    {
        $startDate = $report->report_period_start;
        $endDate = $report->report_period_end;

        $tasks = Task::whereBetween('scheduled_date', [$startDate, $endDate])->get();
        $irrigations = IrrigationLog::whereBetween('started_at', [$startDate, $endDate])->get();
        $cropAnalyses = CropAnalysis::whereBetween('analyzed_at', [$startDate, $endDate])->get();

        return [
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
                'days' => $startDate->diffInDays($endDate) + 1,
            ],
            'tasks_summary' => [
                'total' => $tasks->count(),
                'completed' => $tasks->where('status', Task::STATUS_COMPLETED)->count(),
                'pending' => $tasks->where('status', Task::STATUS_PENDING)->count(),
                'completion_rate' => $tasks->count() > 0
                    ? round(($tasks->where('status', Task::STATUS_COMPLETED)->count() / $tasks->count()) * 100, 2)
                    : 0,
                'estimated_cost' => $tasks->sum('estimated_cost'),
                'actual_cost' => $tasks->sum('actual_cost'),
            ],
            'irrigation_summary' => [
                'total_sessions' => $irrigations->count(),
                'total_water_liters' => $irrigations->sum('water_used_liters'),
                'total_duration_minutes' => $irrigations->sum('duration_minutes'),
                'avg_daily_water' => $irrigations->sum('water_used_liters') / 30,
            ],
            'crop_analysis_summary' => [
                'total_analyses' => $cropAnalyses->count(),
                'avg_health_score' => $cropAnalyses->avg('health_score'),
            ],
        ];
    }

    /**
     * Generate irrigation report.
     */
    private function generateIrrigationReport(Report $report): array
    {
        $startDate = $report->report_period_start;
        $endDate = $report->report_period_end;

        $logs = IrrigationLog::whereBetween('started_at', [$startDate, $endDate])
            ->with('irrigationZone')
            ->get();

        return [
            'total_sessions' => $logs->count(),
            'total_water_liters' => $logs->sum('water_used_liters'),
            'total_duration_minutes' => $logs->sum('duration_minutes'),
            'by_zone' => $logs->groupBy('irrigation_zone_id')->map(function ($zoneLogs) {
                return [
                    'sessions' => $zoneLogs->count(),
                    'water_liters' => $zoneLogs->sum('water_used_liters'),
                    'duration_minutes' => $zoneLogs->sum('duration_minutes'),
                ];
            }),
            'avg_session_duration' => $logs->avg('duration_minutes'),
            'avg_water_per_session' => $logs->avg('water_used_liters'),
        ];
    }

    /**
     * Generate sensor analysis report.
     */
    private function generateSensorAnalysis(Report $report): array
    {
        $startDate = $report->report_period_start;
        $endDate = $report->report_period_end;

        $readings = SensorReading::whereBetween('recorded_at', [$startDate, $endDate])->get();

        return [
            'total_readings' => $readings->count(),
            'temperature' => [
                'min' => $readings->min('temperature'),
                'max' => $readings->max('temperature'),
                'avg' => $readings->avg('temperature'),
            ],
            'humidity' => [
                'min' => $readings->min('humidity'),
                'max' => $readings->max('humidity'),
                'avg' => $readings->avg('humidity'),
            ],
            'soil_moisture' => [
                'min' => $readings->min('soil_moisture'),
                'max' => $readings->max('soil_moisture'),
                'avg' => $readings->avg('soil_moisture'),
            ],
        ];
    }

    /**
     * Generate crop analysis report.
     */
    private function generateCropAnalysis(Report $report): array
    {
        $startDate = $report->report_period_start;
        $endDate = $report->report_period_end;

        $analyses = CropAnalysis::whereBetween('analyzed_at', [$startDate, $endDate])->get();

        return [
            'total_analyses' => $analyses->count(),
            'avg_health_score' => $analyses->avg('health_score'),
            'by_disease' => $analyses->groupBy('detected_disease')->map(function ($group) {
                return $group->count();
            }),
        ];
    }

    /**
     * Display the specified report.
     */
    public function show(Report $report): View
    {
        $report->load(['farm', 'generatedBy']);

        return view('reports.show', compact('report'));
    }

    /**
     * Download report as PDF.
     */
    public function download(Report $report): RedirectResponse
    {
        if (! $report->file_path || ! file_exists(storage_path('app/'.$report->file_path))) {
            return redirect()->back()->with('error', 'Report file not found.');
        }

        return response()->download(storage_path('app/'.$report->file_path));
    }

    /**
     * Remove the specified report from storage.
     */
    public function destroy(Report $report): RedirectResponse
    {
        if ($report->file_path && file_exists(storage_path('app/'.$report->file_path))) {
            unlink(storage_path('app/'.$report->file_path));
        }

        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'Report deleted successfully.');
    }
}
