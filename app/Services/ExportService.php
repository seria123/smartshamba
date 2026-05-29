<?php

namespace App\Services;

use App\Models\{
    Crop, CropAnalysis, CropCycle, Equipment, Farm, Field, Harvest,
    IrrigationLog, Livestock, LivestockType, SensorReading, Staff, Task, WeatherData
};
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    private const MAX_CSV_ROWS = 50000;

    /*
    |--------------------------------------------------------------------------
    | GENERIC EXPORT ENGINE
    |--------------------------------------------------------------------------
    */

    /**
     * Handles CSV / XLSX export.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    private function exportToFormat(
        Collection $data,
        string $filename,
        string $format,
        array $headers,
        callable $rowMapper
    ) {
        $rows = $data->map($rowMapper)->toArray();
        array_unshift($rows, $headers);

        // XLSX EXPORT
        if ($format === 'xlsx') {
            return Excel::download(
                new \App\Exports\GenericExport($rows),
                $filename . '.xlsx'
            );
        }

        // CSV LIMIT GUARD
        if (count($rows) > self::MAX_CSV_ROWS) {
            abort(413, 'Export too large. Please filter your data.');
        }

        // BUILD CSV
        $csvContent = implode("\n", array_map(function ($row) {
            return implode(',', array_map(fn ($cell) =>
                '"' . str_replace('"', '""', (string) $cell) . '"',
                $row
            ));
        }, $rows));

        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, $filename . '.csv', [
            'Content-Type' => 'text/csv',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => 0,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SENSOR READINGS
    |--------------------------------------------------------------------------
    */

    public function exportSensorReadings(
        ?Farm $farm,
        ?Carbon $fromDate,
        ?Carbon $toDate,
        string $format = 'csv'
    ) {
        $query = SensorReading::with(['sensor', 'sensor.field']);

if ($farm) {
            $query->whereHas('sensor.field.farm', fn ($q) => $q->where('id', $farm->id));
        }

        if ($fromDate) $query->where('recorded_at', '>=', $fromDate);
        if ($toDate) $query->where('recorded_at', '<=', $toDate);

        $data = $query->orderByDesc('recorded_at')->get();

        return $this->exportToFormat(
            $data,
            'sensor_readings_' . now()->format('Y_m_d_His'),
            $format,
            ['Recorded At', 'Field', 'Sensor', 'Temp', 'Humidity', 'Moisture', 'Light Intensity', 'Rain Detected'],
            fn ($r) => [
                $r->recorded_at,
                $r->sensor->field->name ?? 'N/A',
                $r->sensor->name,
                $r->temperature,
                $r->humidity,
                $r->soil_moisture,
                $r->light_intensity,
                $r->rain_detected ? 'Yes' : 'No',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | TASKS
    |--------------------------------------------------------------------------
    */

    public function exportTasks(
        ?Farm $farm,
        ?string $status,
        ?Carbon $fromDate,
        ?Carbon $toDate,
        string $format = 'csv'
    ) {
        $query = Task::with(['field', 'crop', 'assignedUser']);

        if ($farm) {
            $query->whereHas('field.farm', fn ($q) => $q->where('id', $farm->id));
        }

        if ($status) $query->where('status', $status);
        if ($fromDate) $query->whereDate('scheduled_date', '>=', $fromDate);
        if ($toDate) $query->whereDate('scheduled_date', '<=', $toDate);

        $data = $query->orderByDesc('scheduled_date')->get();

        return $this->exportToFormat(
            $data,
            'tasks_' . now()->format('Y_m_d_His'),
            $format,
            ['ID','Title','Status','Priority','Field','Crop','Assigned','Scheduled'],
            fn ($t) => [
                $t->id,
                $t->title,
                $t->status,
                $t->priority,
                $t->field->name ?? 'N/A',
                $t->crop->name ?? 'N/A',
                $t->assignedUser->name ?? 'N/A',
                $t->scheduled_date,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | IRRIGATION LOGS
    |--------------------------------------------------------------------------
    */

    public function exportIrrigationLogs(
        ?Farm $farm,
        ?Carbon $fromDate,
        ?Carbon $toDate,
        string $format = 'csv'
    ) {
        $query = IrrigationLog::with(['irrigationZone.field']);

        if ($farm) {
            $query->whereHas('irrigationZone.field.farm', fn ($q) => $q->where('id', $farm->id));
        }

        if ($fromDate) $query->where('started_at', '>=', $fromDate);
        if ($toDate) $query->where('started_at', '<=', $toDate);

        $data = $query->orderByDesc('started_at')->get();

        return $this->exportToFormat(
            $data,
            'irrigation_logs_' . now()->format('Y_m_d_His'),
            $format,
            ['Zone','Field','Event','Start','End','Duration','Water','Status'],
            fn ($l) => [
                $l->irrigationZone->name ?? 'N/A',
                $l->irrigationZone->field->name ?? 'N/A',
                $l->event_type,
                $l->started_at,
                $l->ended_at,
                $l->duration_minutes,
                $l->water_used_liters,
                $l->status,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CROPS (SIMPLE EXAMPLE)
    |--------------------------------------------------------------------------
    */

    public function exportCrops(?Farm $farm, string $format = 'csv')
    {
        $query = Crop::with(['field.farm']);

        if ($farm) {
            $query->whereHas('field.farm', fn ($q) => $q->where('id', $farm->id));
        }

        $data = $query->orderByDesc('planting_date')->get();

        return $this->exportToFormat(
            $data,
            'crops_' . now()->format('Y_m_d_His'),
            $format,
            ['Name','Variety','Field','Farm','Status'],
            fn ($c) => [
                $c->name,
                $c->variety,
                $c->field->name ?? 'N/A',
                $c->field->farm->name ?? 'N/A',
                $c->status,
            ]
        );
    }
    
    /*
    |--------------------------------------------------------------------------
    | CROP ANALYSES
    |--------------------------------------------------------------------------
    */


public function exportWeatherData($format = 'csv'): StreamedResponse
{
    if (!is_string($format)) {
        abort(400, 'Format must be a string like csv, excel, pdf');
    }

    $format = strtolower($format);

    $data = WeatherData::all();

    return match ($format) {
        'csv' => $this->exportToCsv($data, 'weather_data.csv'),
        default => abort(400, "Unsupported export format: $format"),
    };
}



public function exportCropAnalyses(
        ?Farm $farm,
        ?Carbon $fromDate,
        ?Carbon $toDate,
        string $format = 'csv'
    ) {
        $query = CropAnalysis::with(['field.farm', 'cropCycle']);

        if ($farm) {
            $query->whereHas('field.farm', fn ($q) =>
                $q->where('id', $farm->id)
            );
        }

        if ($fromDate) {
            $query->where('analyzed_at', '>=', $fromDate);
        }

        if ($toDate) {
            $query->where('analyzed_at', '<=', $toDate);
        }

        $data = $query->orderByDesc('analyzed_at')->get();

        return $this->exportToFormat(
            $data,
            'crop_analyses_' . now()->format('Y_m_d_His'),
            $format,
            [
                'Field',
                'Farm',
                'Analyzed At',
                'Diagnosis',
                'Confidence',
                'Recommendation',
                'Image'
            ],
            fn ($a) => [
                $a->field->name ?? 'N/A',
                $a->field->farm->name ?? 'N/A',
                $a->analyzed_at,
                $a->diagnosis ?? 'N/A',
                $a->confidence_score,
                $a->recommendation ?? 'N/A',
                $a->image_path,
            ]
        );
    }

    public function exportLivestock(?Farm $farm, string $format = 'csv')
    {
        $query = Livestock::with(['farm', 'type']);

        if ($farm) {
            $query->whereHas('farm', fn ($q) => $q->where('id', $farm->id));
        }

        $data = $query->orderBy('tag_number')->get();

        return $this->exportToFormat(
            $data,
            'livestock_' . now()->format('Y_m_d_His'),
            $format,
            ['Tag Number', 'Tracking ID', 'Name', 'Type', 'Farm', 'Status', 'Gender', 'Weight', 'Date Acquired', 'Purchase Price', 'Sale Price'],
            fn ($l) => [
                $l->tag_number,
                $l->tracking_id,
                $l->name,
                $l->type->name ?? 'N/A',
                $l->farm->name ?? 'N/A',
                $l->status,
                $l->gender,
                $l->weight,
                $l->date_acquired,
                $l->purchase_price,
                $l->sale_price,
            ]
        );
    }

    public function exportEquipment(?Farm $farm, string $format = 'csv')
    {
        $query = Equipment::with(['farm', 'assignedStaff']);

        if ($farm) {
            $query->whereHas('farm', fn ($q) => $q->where('id', $farm->id));
        }

        $data = $query->orderBy('name')->get();

        return $this->exportToFormat(
            $data,
            'equipment_' . now()->format('Y_m_d_His'),
            $format,
            ['Name', 'Type', 'Model Number', 'Serial Number', 'Farm', 'Status', 'Condition', 'Purchase Date', 'Purchase Cost', 'Assigned To'],
            fn ($e) => [
                $e->name,
                $e->type,
                $e->model_number,
                $e->serial_number,
                $e->farm->name ?? 'N/A',
                $e->status,
                $e->condition,
                $e->purchase_date,
                $e->purchase_cost,
                $e->assignedStaff->name ?? 'N/A',
            ]
        );
    }

    public function exportFields(?Farm $farm, string $format = 'csv')
    {
        $query = Field::with(['farm']);

        if ($farm) {
            $query->whereHas('farm', fn ($q) => $q->where('id', $farm->id));
        }

        $data = $query->orderBy('name')->get();

        return $this->exportToFormat(
            $data,
            'fields_' . now()->format('Y_m_d_His'),
            $format,
            ['Name', 'Farm', 'Size (ha)', 'Location', 'Soil Type', 'Water Source', 'GPS Latitude', 'GPS Longitude', 'Description'],
            fn ($f) => [
                $f->name,
                $f->farm->name ?? 'N/A',
                $f->size_hectares,
                $f->location,
                $f->soil_type,
                $f->water_source,
                $f->gps_latitude,
                $f->gps_longitude,
                $f->description,
            ]
        );
    }

    public function exportCropCycles(
        ?Farm $farm,
        ?Carbon $fromDate,
        ?Carbon $toDate,
        string $format = 'csv'
    ) {
        $query = CropCycle::with(['farm', 'field', 'crop', 'staff']);

        if ($farm) {
            $query->whereHas('farm', fn ($q) => $q->where('id', $farm->id));
        }

        if ($fromDate) {
            $query->where('start_date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->where('start_date', '<=', $toDate);
        }

        $data = $query->orderByDesc('start_date')->get();

        return $this->exportToFormat(
            $data,
            'crop_cycles_' . now()->format('Y_m_d_His'),
            $format,
            ['Crop Name', 'Farm', 'Field', 'Variety', 'Category', 'Season', 'Start Date', 'Expected Harvest', 'Status', 'Responsible Staff'],
            fn ($c) => [
                $c->crop_name,
                $c->farm->name ?? 'N/A',
                $c->field->name ?? 'N/A',
                $c->variety,
                $c->category,
                $c->season,
                $c->start_date,
                $c->expected_harvest_date,
                $c->status,
                $c->staff->name ?? 'N/A',
            ]
        );
    }

    public function exportLivestockPdf(?Farm $farm)
    {
        $livestock = $farm ? ($farm->livestock ?? collect()) : Livestock::all();
        $farmLabel = $farm ? ($farm->name ?? 'Unknown Farm') : 'All Farms';

        $pdf = PDF::loadView('exports.pdf.livestock', [
            'livestock' => $livestock,
            'farmLabel' => $farmLabel,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
            'total' => $livestock->count(),
            'byStatus' => $livestock->groupBy('status')->map->count(),
        ]);

        return $pdf->download('livestock.pdf');
    }

    public function exportHarvestsPdf(?Farm $farm)
    {
        $harvests = $farm ? ($farm->harvests ?? collect()) : Harvest::all();
        $farmLabel = $farm ? ($farm->name ?? 'Unknown Farm') : 'All Farms';

        $pdf = PDF::loadView('exports.pdf.harvests', [
            'harvests' => $harvests,
            'farmLabel' => $farmLabel,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
            'total' => $harvests->count(),
            'stats' => [
                'total_qty' => $harvests->sum('quantity_harvested'),
                'losses' => $harvests->sum('loss_quantity'),
                'grade_a_count' => $harvests->where('quality_grade', Harvest::GRADE_A)->count(),
                'grade_b_count' => $harvests->where('quality_grade', Harvest::GRADE_B)->count(),
                'grade_c_count' => $harvests->where('quality_grade', Harvest::GRADE_C)->count(),
            ],
        ]);

        return $pdf->download('harvests.pdf');
    }

    public function exportFarmPdf(Farm $farm)
    {
        $stats = [
            'fields_count' => $farm->fields()->count(),
            'livestock_count' => $farm->livestock()->count(),
            'crops_count' => $farm->cropCycles()->count(),
            'equipment_count' => $farm->equipment()->count(),
            'harvest_records' => $farm->harvests()->count(),
        ];

        $pdf = PDF::loadView('exports.pdf.farm_summary', [
            'farm' => $farm,
            'stats' => $stats,
            'generatedAt' => now()->format('Y-m-d H:i:s'),
        ]);

        return $pdf->download($farm->name . '_summary.pdf');
    }

    private function exportToCsv(Collection $data, string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $csvContent = $data->map(function ($row) {
            return implode(',', array_map(fn ($cell) =>
                '"' . str_replace('"', '""', (string) $cell) . '"',
                (array) $row
            ));
        })->implode("\n");

        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => 0,
        ]);
    }

    public function importLivestockCsv(string $path, int $userId): array
    {
        $rows = array_map('str_getcsv', file($path));
        $header = array_map('trim', array_shift($rows));
        $results = [];

        foreach ($rows as $row) {
            $data = array_combine($header, $row) ?: [];

            try {
                $livestockType = LivestockType::where('name', $data['type'] ?? null)->first();
                $farm = Farm::where('name', $data['farm'] ?? null)->first();

                Livestock::create([
                    'user_id' => $userId,
                    'farm_id' => $farm?->id,
                    'livestock_type_id' => $livestockType?->id,
                    'tag_number' => $data['tag_number'] ?? null,
                    'tracking_id' => $data['tracking_id'] ?? null,
                    'name' => $data['name'] ?? null,
                    'status' => $data['status'] ?? 'healthy',
                    'gender' => $data['gender'] ?? null,
                    'weight' => $data['weight'] ?? null,
                    'date_acquired' => $data['date_acquired'] ?? null,
                    'birth_date' => $data['birth_date'] ?? null,
                    'purchase_price' => $data['purchase_price'] ?? null,
                    'sale_price' => $data['sale_price'] ?? null,
                    'notes' => $data['notes'] ?? null,
                ]);

                $results[] = ['status' => 'success'];
            } catch (\Throwable $e) {
                $results[] = ['status' => 'error', 'message' => $e->getMessage()];
            }
        }

        return $results;
    }

    public function importCropsCsv(string $path, int $userId): array
    {
        $rows = array_map('str_getcsv', file($path));
        $header = array_map('trim', array_shift($rows));
        $results = [];

        foreach ($rows as $row) {
            $data = array_combine($header, $row) ?: [];

            try {
                $field = Field::where('name', $data['field'] ?? null)->first();

                Crop::create([
                    'user_id' => $userId,
                    'field_id' => $field?->id,
                    'name' => $data['name'] ?? null,
                    'variety' => $data['variety'] ?? null,
                    'category' => $data['category'] ?? null,
                    'planting_date' => $data['planting_date'] ?? null,
                    'expected_harvest_date' => $data['expected_harvest_date'] ?? null,
                    'status' => $data['status'] ?? 'growing',
                    'yield_estimate' => $data['yield_estimate'] ?? null,
                    'notes' => $data['notes'] ?? null,
                ]);

                $results[] = ['status' => 'success'];
            } catch (\Throwable $e) {
                $results[] = ['status' => 'error', 'message' => $e->getMessage()];
            }
        }

        return $results;
    }

    public function importEquipmentCsv(string $path, int $userId): array
    {
        $rows = array_map('str_getcsv', file($path));
        $header = array_map('trim', array_shift($rows));
        $results = [];

        foreach ($rows as $row) {
            $data = array_combine($header, $row) ?: [];

            try {
                $farm = Farm::where('name', $data['farm'] ?? null)->first();

                Equipment::create([
                    'farm_id' => $farm?->id,
                    'name' => $data['name'] ?? null,
                    'type' => $data['type'] ?? null,
                    'status' => $data['status'] ?? 'active',
                    'condition' => $data['condition'] ?? null,
                    'model_number' => $data['model_number'] ?? null,
                    'serial_number' => $data['serial_number'] ?? null,
                    'purchase_date' => $data['purchase_date'] ?? null,
                    'purchase_cost' => $data['purchase_cost'] ?? null,
                    'description' => $data['description'] ?? null,
                ]);

                $results[] = ['status' => 'success'];
            } catch (\Throwable $e) {
                $results[] = ['status' => 'error', 'message' => $e->getMessage()];
            }
        }

        return $results;
    }
}