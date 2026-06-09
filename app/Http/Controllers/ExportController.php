<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Equipment;
use App\Models\Farm;
use App\Models\Harvest;
use App\Models\Livestock;
use App\Models\LivestockType;
use App\Services\ExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ExportController extends Controller
{
    protected ExportService $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    // ============================================================
    //  VIEW
    // ============================================================

    /**
     * Show the unified export / import page.
     */
    public function index(): \Illuminate\View\View
    {
        $farms     = Farm::all();
        $types     = LivestockType::all();
        $fullCrops = Crop::with('field')->get();

        return view('exports.index', compact('farms', 'types', 'fullCrops'));
    }

    // ============================================================
    //  CSV EXPORTS  (original — unchanged)
    // ============================================================

public function exportSensorReadings(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $farm     = $request->filled('farm_id') ? Farm::find($request->farm_id) : null;
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date) : null;
        $toDate   = $request->filled('to_date') ? Carbon::parse($request->to_date) : null;

        return $this->exportService->exportSensorReadings($farm, $fromDate, $toDate, $request->get('format', 'csv'));
    }

    public function exportTasks(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $farm     = $request->filled('farm_id') ? Farm::find($request->farm_id) : null;
        $status   = $request->filled('status') ? $request->status : null;
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date) : null;
        $toDate   = $request->filled('to_date') ? Carbon::parse($request->to_date) : null;

        return $this->exportService->exportTasks($farm, $status, $fromDate, $toDate, $request->get('format', 'csv'));
    }

    public function exportIrrigationLogs(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $farm     = $request->filled('farm_id') ? Farm::find($request->farm_id) : null;
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date) : null;
        $toDate   = $request->filled('to_date') ? Carbon::parse($request->to_date) : null;

        return $this->exportService->exportIrrigationLogs($farm, $fromDate, $toDate, $request->get('format', 'csv'));
    }

    public function exportWeatherData(Request $request)
    {
        return $this->exportService->exportWeatherData($request->get('format', 'csv'));
    }

    public function exportCrops(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $farm = $request->filled('farm_id') ? Farm::find($request->farm_id) : null;

        return $this->exportService->exportCrops($farm, $request->get('format', 'csv'));
    }

    public function exportCropAnalyses(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $farm     = $request->filled('farm_id') ? Farm::find($request->farm_id) : null;
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date) : null;
        $toDate   = $request->filled('to_date') ? Carbon::parse($request->to_date) : null;

        return $this->exportService->exportCropAnalyses($farm, $fromDate, $toDate, $request->get('format', 'csv'));
    }

    // ============================================================
    //  ADDITIONAL CSV EXPORTS
    // ============================================================

    /**
     * Export livestock as CSV or XLSX.
     */
    public function exportLivestock(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $farm = $request->filled('farm_id') ? Farm::find($request->farm_id) : null;

        return $this->exportService->exportLivestock($farm, $request->get('format', 'csv'));
    }

    /**
     * Export equipment as CSV or XLSX.
     */
    public function exportEquipment(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $farm = $request->filled('farm_id') ? Farm::find($request->farm_id) : null;

        return $this->exportService->exportEquipment($farm, $request->get('format', 'csv'));
    }

    /**
     * Export fields as CSV or XLSX.
     */
    public function exportFields(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $farm = $request->filled('farm_id') ? Farm::find($request->farm_id) : null;

        return $this->exportService->exportFields($farm, $request->get('format', 'csv'));
    }

    /**
     * Export crop cycles as CSV or XLSX.
     */
    public function exportCropCycles(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $farm     = $request->filled('farm_id') ? Farm::find($request->farm_id) : null;
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date) : null;
        $toDate   = $request->filled('to_date') ? Carbon::parse($request->to_date) : null;

        return $this->exportService->exportCropCycles($farm, $fromDate, $toDate, $request->get('format', 'csv'));
    }

    // ============================================================
    //  PDF EXPORTS
    // ============================================================

    public function pdfLivestock(Request $request)
    {
        $farm = $request->filled('farm_id') ? Farm::find($request->farm_id) : null;

        return $this->exportService->exportLivestockPdf($farm);
    }

    public function pdfHarvests(Request $request)
    {
        $farm = $request->filled('farm_id') ? Farm::find($request->farm_id) : null;

        return $this->exportService->exportHarvestsPdf($farm);
    }

    public function pdfFarmSummary(Request $request, Farm $farm)
    {
        return $this->exportService->exportFarmPdf($farm);
    }

    // ============================================================
    //  CSV IMPORTS
    // ============================================================

    /**
     * Handle a livestock CSV upload and process it.
     */
    public function importLivestock(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:20480',
        ]);

        $userId = auth()->id();

        try {
            $path = $request->file('csv_file')->storeAs('imports',
                'livestock_import_'.now()->format('Y_m_d_His').'.csv'
            );

            $results = $this->exportService->importLivestockCsv(
                Storage::path($path),
                $userId
            );

            $successes = collect($results)->where('status', 'success')->count();
            $errors    = collect($results)->where('status', 'error')->pluck('message')->take(10)->all();

            return redirect()->route('exports.index')
                ->with('import_success', "Livestock import complete: $successes rows imported.")
                ->with('import_errors', $errors);
        } catch (ValidationException $e) {
            return redirect()->route('exports.index')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Throwable $e) {
            Log::error('Livestock import failed: '.$e->getMessage());

            return redirect()->route('exports.index')
                ->with('import_error', 'Import failed: '.$e->getMessage());
        }
    }

    /**
     * Handle a crops CSV upload and process it.
     */
    public function importCrops(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:20480',
        ]);

        $userId = auth()->id();

        try {
            $path = $request->file('csv_file')->storeAs('imports',
                'crops_import_'.now()->format('Y_m_d_His').'.csv'
            );

            $results = $this->exportService->importCropsCsv(
                Storage::path($path),
                $userId
            );

            $successes = collect($results)->where('status', 'success')->count();
            $errors    = collect($results)->where('status', 'error')->pluck('message')->take(10)->all();

            return redirect()->route('exports.index')
                ->with('import_success', "Crops import complete: $successes rows imported.")
                ->with('import_errors', $errors);
        } catch (ValidationException $e) {
            return redirect()->route('exports.index')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Throwable $e) {
            Log::error('Crops import failed: '.$e->getMessage());

            return redirect()->route('exports.index')
                ->with('import_error', 'Import failed: '.$e->getMessage());
        }
    }

    /**
     * Handle an equipment CSV upload and process it.
     */
    public function importEquipment(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:20480',
        ]);

        $userId = auth()->id();

        try {
            $path = $request->file('csv_file')->storeAs('imports',
                'equipment_import_'.now()->format('Y_m_d_His').'.csv'
            );

            $results = $this->exportService->importEquipmentCsv(
                Storage::path($path),
                $userId
            );

            $successes = collect($results)->where('status', 'success')->count();
            $errors    = collect($results)->where('status', 'error')->pluck('message')->take(10)->all();

            return redirect()->route('exports.index')
                ->with('import_success', "Equipment import complete: $successes rows imported.")
                ->with('import_errors', $errors);
        } catch (ValidationException $e) {
            return redirect()->route('exports.index')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Throwable $e) {
            Log::error('Equipment import failed: '.$e->getMessage());

            return redirect()->route('exports.index')
                ->with('import_error', 'Import failed: '.$e->getMessage());
        }
    }

    /**
     * Serve a sample CSV template for livestock import.
     */
    public function sampleLivestock()
    {
        $headers = ['tag_number','tracking_id','name','type','status','gender','weight','date_acquired','birth_date','purchase_price','sale_price','notes'];
        $row     = ['LT-001','TRK-001','Bessie','Cattle','healthy','female','450','2024-01-15','2022-03-10','1200','','Healthy cow'];

        $csv = collect([$headers, $row])
            ->map(fn ($r) => collect($r)->map(fn ($c) => '"'.str_replace('"','""',(string)$c).'"')->implode(','))
            ->implode("\n");

        return response($csv, 200, [
            'Content-Type'  => 'text/csv',
            'Content-Disposition' => 'attachment; filename="livestock_import_template.csv"',
        ]);
    }

    /**
     * Serve a sample CSV template for crops import.
     */
    public function sampleCrops()
    {
        $headers = ['name','variety','category','field','planting_date','expected_harvest_date','status','yield_estimate','notes'];
        $row     = ['Maize','H614','Grain','Field A','2024-03-01','2024-07-15','growing','3.5','High-quality seed'];

        $csv = collect([$headers, $row])
            ->map(fn ($r) => collect($r)->map(fn ($c) => '"'.str_replace('"','""',(string)$c).'"')->implode(','))
            ->implode("\n");

        return response($csv, 200, [
            'Content-Type'  => 'text/csv',
            'Content-Disposition' => 'attachment; filename="crops_import_template.csv"',
        ]);
    }

    /**
     * Serve a sample CSV template for equipment import.
     */
    public function sampleEquipment()
    {
        $headers = ['name','type','model_number','serial_number','status','condition','purchase_date','purchase_cost','description'];
        $row     = ['Tractor X500','Tractor','X500-2024','SN-001','active','good','2024-01-10','50000','Main tractor for ploughing'];

        $csv = collect([$headers, $row])
            ->map(fn ($r) => collect($r)->map(fn ($c) => '"'.str_replace('"','""',(string)$c).'"')->implode(','))
            ->implode("\n");

        return response($csv, 200, [
            'Content-Type'  => 'text/csv',
            'Content-Disposition' => 'attachment; filename="equipment_import_template.csv"',
        ]);
    }
}
