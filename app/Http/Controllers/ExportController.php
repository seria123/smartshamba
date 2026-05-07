<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Services\ExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    protected ExportService $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * Show export options page.
     */
    public function index(): \Illuminate\View\View
    {
        $farms = Farm::all();

        return view('exports.index', compact('farms'));
    }

    /**
     * Export sensor readings.
     */
    public function exportSensorReadings(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $farm = $request->filled('farm_id') ? Farm::find($request->farm_id) : null;
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date) : null;
        $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date) : null;

        return $this->exportService->exportSensorReadings(
            $farm,
            $fromDate,
            $toDate,
            $request->get('format', 'csv')
        );
    }

    /**
     * Export tasks.
     */
    public function exportTasks(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $farm = $request->filled('farm_id') ? Farm::find($request->farm_id) : null;
        $status = $request->filled('status') ? $request->status : null;
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date) : null;
        $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date) : null;

        return $this->exportService->exportTasks(
            $farm,
            $status,
            $fromDate,
            $toDate,
            $request->get('format', 'csv')
        );
    }

    /**
     * Export irrigation logs.
     */
    public function exportIrrigationLogs(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $farm = $request->filled('farm_id') ? Farm::find($request->farm_id) : null;
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date) : null;
        $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date) : null;

        return $this->exportService->exportIrrigationLogs(
            $farm,
            $fromDate,
            $toDate,
            $request->get('format', 'csv')
        );
    }

    /**
     * Export weather data.
     */
    public function exportWeatherData(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $request->validate([
            'farm_id' => 'required|exists:farms,id',
        ]);

        $farm = Farm::find($request->farm_id);
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date) : null;
        $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date) : null;

        return $this->exportService->exportWeatherData(
            $farm,
            $fromDate,
            $toDate,
            $request->get('format', 'csv')
        );
    }

    /**
     * Export crops.
     */
    public function exportCrops(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $farm = $request->filled('farm_id') ? Farm::find($request->farm_id) : null;

        return $this->exportService->exportCrops(
            $farm,
            $request->get('format', 'csv')
        );
    }

    /**
     * Export crop analyses.
     */
    public function exportCropAnalyses(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $farm = $request->filled('farm_id') ? Farm::find($request->farm_id) : null;
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date) : null;
        $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date) : null;

        return $this->exportService->exportCropAnalyses(
            $farm,
            $fromDate,
            $toDate,
            $request->get('format', 'csv')
        );
    }
}
