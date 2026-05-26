<?php

namespace App\Services;

use App\Models\Crop;
use App\Models\CropAnalysis;
use App\Models\CropCycle;
use App\Models\Equipment;
use App\Models\Farm;
use App\Models\Field;
use App\Models\Harvest;
use App\Models\IrrigationLog;
use App\Models\Livestock;
use App\Models\LivestockType;
use App\Models\Sensor;
use App\Models\SensorReading;
use App\Models\Task;
use App\Models\WeatherData;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class ExportService
{
    private const MAX_CSV_ROWS = 50000;

    // ========================================================
    //  CSV / XLSX GENERIC EXPORT  (existing, extended)
    // ========================================================

    /**
     * Export sensor readings to CSV or XLSX.
     */
    public function exportSensorReadings(
        ?Farm $farm = null,
        ?Carbon $fromDate = null,
        ?Carbon $toDate = null,
        string $format = 'csv'
    ): \Symfony\Component\HttpFoundation\BinaryFileResponse {
        $query = SensorReading::with(['sensor', 'sensor.field']);

        if ($farm) {
            $query->whereHas('sensor.field.farm', fn ($q) => $q->where('id', $farm->id));
        }
        if ($fromDate) {
            $query->where('recorded_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->where('recorded_at', '<=', $toDate);
        }

        $data = $query->orderBy('recorded_at', 'desc')->get();
        $filename = 'sensor_readings_'.now()->format('Y_m_d_His');

        return $this->exportToFormat($data, $filename, $format, [
            'Recorded At', 'Field', 'Sensor Name', 'Temperature',
            'Humidity', 'Soil Moisture', 'Light', 'Rain',
        ], fn ($r) => [
            $r->recorded_at->format('Y-m-d H:i:s'),
            $r->sensor->field->name ?? 'N/A',
            $r->sensor->name,
            $r->temperature,
            $r->humidity,
            $r->soil_moisture,
            $r->light,
            $r->rain,
        ]);
    }

    /**
     * Export tasks to CSV or XLSX.
     */
    public function exportTasks(
        ?Farm $farm = null,
        ?string $status = null,
        ?Carbon $fromDate = null,
        ?Carbon $toDate = null,
        string $format = 'csv'
    ): \Symfony\Component\HttpFoundation\BinaryFileResponse {
        $query = Task::with(['field', 'crop', 'assignedUser']);

        if ($farm) {
            $query->whereHas('field.farm', fn ($q) => $q->where('id', $farm->id));
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($fromDate) {
            $query->whereDate('scheduled_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('scheduled_date', '<=', $toDate);
        }

        $data = $query->orderBy('scheduled_date', 'desc')->get();
        $filename = 'tasks_'.now()->format('Y_m_d_His');

        return $this->exportToFormat($data, $filename, $format, [
            'ID', 'Title', 'Description', 'Type', 'Status', 'Priority',
            'Field', 'Crop', 'Assigned To', 'Scheduled Date',
            'Completed Date', 'Estimated Hours', 'Actual Hours',
            'Estimated Cost', 'Actual Cost',
        ], fn ($t) => [
            $t->id, $t->title, $t->description, $t->task_type, $t->status, $t->priority,
            $t->field->name ?? 'N/A', $t->crop->name ?? 'N/A', $t->assignedUser->name ?? 'N/A',
            $t->scheduled_date?->format('Y-m-d'), $t->completed_date?->format('Y-m-d'),
            $t->estimated_hours, $t->actual_hours, $t->estimated_cost, $t->actual_cost,
        ]);
    }

    /**
     * Export irrigation logs to CSV or XLSX.
     */
    public function exportIrrigationLogs(
        ?Farm $farm = null,
        ?Carbon $fromDate = null,
        ?Carbon $toDate = null,
        string $format = 'csv'
    ): \Symfony\Component\HttpFoundation\BinaryFileResponse {
        $query = IrrigationLog::with(['irrigationZone.field']);

        if ($farm) {
            $query->whereHas('irrigationZone.field.farm', fn ($q) => $q->where('id', $farm->id));
        }
        if ($fromDate) {
            $query->where('started_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->where('started_at', '<=', $toDate);
        }

        $data = $query->orderBy('started_at', 'desc')->get();
        $filename = 'irrigation_logs_'.now()->format('Y_m_d_His');

        return $this->exportToFormat($data, $filename, $format, [
            'Zone', 'Field', 'Event Type', 'Started At', 'Ended At',
            'Duration (Minutes)', 'Water Used (Liters)', 'Soil Moisture Before',
            'Soil Moisture After', 'Status', 'Triggered By',
        ], fn ($l) => [
            $l->irrigationZone->name ?? 'N/A',
            $l->irrigationZone->field->name ?? 'N/A',
            $l->event_type,
            $l->started_at->format('Y-m-d H:i:s'),
            $l->ended_at?->format('Y-m-d H:i:s'),
            $l->duration_minutes,
            $l->water_used_liters,
            $l->soil_moisture_before,
            $l->soil_moisture_after,
            $l->status,
            $l->triggeredByUser->name ?? 'System',
        ]);
    }

    /**
     * Export weather data to CSV or XLSX.
     */
    public function exportWeatherData(
        Farm $farm,
        ?Carbon $fromDate = null,
        ?Carbon $toDate = null,
        string $format = 'csv'
    ): \Symfony\Component\HttpFoundation\BinaryFileResponse {
        $query = WeatherData::where('farm_id', $farm->id);

        if ($fromDate) {
            $query->where('recorded_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->where('recorded_at', '<=', $toDate);
        }

        $data = $query->orderBy('recorded_at', 'desc')->get();
        $filename = 'weather_data_'.now()->format('Y_m_d_His');

        return $this->exportToFormat($data, $filename, $format, [
            'Recorded At', 'Temperature', 'Feels Like', 'Humidity', 'Pressure',
            'Wind Speed', 'Wind Direction', 'Precipitation', 'Cloud Cover',
            'UV Index', 'Visibility', 'Weather Condition',
        ], fn ($w) => [
            $w->recorded_at->format('Y-m-d H:i:s'), $w->temperature, $w->feels_like,
            $w->humidity, $w->pressure, $w->wind_speed, $w->wind_direction,
            $w->precipitation, $w->cloud_cover, $w->uv_index, $w->visibility, $w->weather_condition,
        ]);
    }

    /**
     * Export crops to CSV or XLSX.
     */
    public function exportCrops(?Farm $farm = null, string $format = 'csv'): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $query = Crop::with(['field', 'field.farm']);

        if ($farm) {
            $query->whereHas('field.farm', fn ($q) => $q->where('id', $farm->id));
        }

        $data = $query->orderBy('planting_date', 'desc')->get();
        $filename = 'crops_'.now()->format('Y_m_d_His');

        return $this->exportToFormat($data, $filename, $format, [
            'Name', 'Variety', 'Category', 'Field', 'Farm',
            'Planting Date', 'Expected Harvest', 'Status',
            'Yield Estimate', 'Days to Maturity', 'Soil Type', 'Notes',
        ], fn ($c) => [
            $c->name, $c->variety, $c->category,
            $c->field->name ?? 'N/A', $c->field->farm->name ?? 'N/A',
            $c->planting_date?->format('Y-m-d'), $c->expected_harvest_date?->format('Y-m-d'),
            $c->status, $c->average_yield_per_hectare, $c->days_to_maturity,
            $c->soil_requirements['soil_type'] ?? 'N/A', $c->notes,
        ]);
    }

    /**
     * Export crop analyses to CSV or XLSX.
     */
    public function exportCropAnalyses(
        ?Farm $farm = null,
        ?Carbon $fromDate = null,
        ?Carbon $toDate = null,
        string $format = 'csv'
    ): \Symfony\Component\HttpFoundation\BinaryFileResponse {
        $query = CropAnalysis::with(['crop.field', 'crop.field.farm']);

        if ($farm) {
            $query->whereHas('crop.field.farm', fn ($q) => $q->where('id', $farm->id));
        }
        if ($fromDate) {
            $query->where('analyzed_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->where('analyzed_at', '<=', $toDate);
        }

        $data = $query->orderBy('analyzed_at', 'desc')->get();
        $filename = 'crop_analyses_'.now()->format('Y_m_d_His');

        return $this->exportToFormat($data, $filename, $format, [
            'Crop', 'Field', 'Farm', 'Analyzed At', 'Health Score',
            'Detected Disease', 'Disease Confidence', 'Recommendations', 'Image',
        ], fn ($a) => [
            $a->crop->name ?? 'N/A',
            $a->crop->field->name ?? 'N/A',
            $a->crop->field->farm->name ?? 'N/A',
            $a->analyzed_at->format('Y-m-d H:i:s'),
            $a->health_score,
            $a->detected_disease ?? 'None',
            $a->disease_confidence,
            $a->recommendations,
            $a->image_path,
        ]);
    }

    /**
     * Export livestock to CSV or XLSX.
     */
    public function exportLivestock(?Farm $farm = null, string $format = 'csv'): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $query = Livestock::with(['type', 'farm']);

        if ($farm) {
            $query->where('farm_id', $farm->id);
        }

        $data = $query->orderBy('created_at', 'desc')->get();
        $filename = 'livestock_'.now()->format('Y_m_d_His');

        return $this->exportToFormat($data, $filename, $format, [
            'Tag Number', 'Tracking ID', 'Name', 'Type',
            'Farm', 'Status', 'Gender', 'Weight',
            'Date Acquired', 'Birth Date', 'Purchase Price', 'Sale Price', 'Notes',
        ], fn ($l) => [
            $l->tag_number,
            $l->tracking_id,
            $l->name,
            $l->type->name ?? 'N/A',
            $l->farm->name ?? 'N/A',
            ucfirst($l->status),
            ucfirst($l->gender),
            $l->weight,
            $l->date_acquired?->format('Y-m-d'),
            $l->birth_date?->format('Y-m-d'),
            $l->purchase_price,
            $l->sale_price,
            $l->notes,
        ]);
    }

    /**
     * Export equipment to CSV or XLSX.
     */
    public function exportEquipment(?Farm $farm = null, string $format = 'csv'): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $query = Equipment::with(['farm', 'assignedStaff']);

        if ($farm) {
            $query->where('farm_id', $farm->id);
        }

        $data = $query->orderBy('name')->get();
        $filename = 'equipment_'.now()->format('Y_m_d_His');

        return $this->exportToFormat($data, $filename, $format, [
            'Name', 'Type', 'Model Number', 'Serial Number',
            'Farm', 'Status', 'Condition', 'Purchase Date',
            'Purchase Cost', 'Assigned To', 'Last Serviced', 'Next Service', 'Description',
        ], fn ($e) => [
            $e->name, $e->type, $e->model_number, $e->serial_number,
            $e->farm->name ?? 'N/A', $e->status, $e->condition,
            $e->purchase_date?->format('Y-m-d'), $e->purchase_cost,
            $e->assignedStaff->name ?? 'Unassigned',
            $e->last_service_date?->format('Y-m-d'),
            $e->next_service_date?->format('Y-m-d'),
            $e->description,
        ]);
    }

    /**
     * Export fields to CSV or XLSX.
     */
    public function exportFields(?Farm $farm = null, string $format = 'csv'): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $query = Field::with('farm');

        if ($farm) {
            $query->where('farm_id', $farm->id);
        }

        $data = $query->orderBy('name')->get();
        $filename = 'fields_'.now()->format('Y_m_d_His');

        return $this->exportToFormat($data, $filename, $format, [
            'Name', 'Farm', 'Size (ha)', 'Location', 'Soil Type',
            'Water Source', 'GPS Latitude', 'GPS Longitude', 'Topography', 'Rainfall Zone', 'Description',
        ], fn ($f) => [
            $f->name, $f->farm->name ?? 'N/A', $f->size_hectares,
            $f->location, $f->soil_type, $f->water_source,
            $f->gps_latitude, $f->gps_longitude, $f->topography, $f->rainfall_zone, $f->description,
        ]);
    }

    /**
     * Export crop cycles to CSV or XLSX.
     */
    public function exportCropCycles(
        ?Farm $farm = null,
        ?Carbon $fromDate = null,
        ?Carbon $toDate = null,
        string $format = 'csv'
    ): \Symfony\Component\HttpFoundation\BinaryFileResponse {
        $query = CropCycle::with(['crop', 'field', 'field.farm']);

        if ($farm) {
            $query->whereHas('field.farm', fn ($q) => $q->where('id', $farm->id));
        }
        if ($fromDate) {
            $query->where('start_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->where('end_date', '<=', $toDate);
        }

        $data = $query->orderByDesc('start_date')->get();
        $filename = 'crop_cycles_'.now()->format('Y_m_d_His');

        return $this->exportToFormat($data, $filename, $format, [
            'Crop', 'Field', 'Farm', 'Status', 'Start Date', 'End Date',
            'Seed Variety', 'Seed Source', 'Fertilizer Used', 'Pesticides', 'Notes',
        ], fn ($c) => [
            $c->crop->name ?? 'N/A', $c->field->name ?? 'N/A',
            $c->field->farm->name ?? 'N/A', $c->status,
            $c->start_date?->format('Y-m-d'), $c->end_date?->format('Y-m-d'),
            $c->seed_variety, $c->seed_source, $c->fertilizer_used, $c->pesticides_used, $c->notes,
        ]);
    }

    /**
     * Export full farm data as XLSX multi-sheet workbook.
     */
    public function exportFarmXlsx(?Farm $farm = null, ?Carbon $fromDate = null, ?Carbon $toDate = null)
    {
        $file = Storage::path('exports/farm_data_'.now()->format('Y_m_d_His').'.xlsx');
        Storage::makeDirectory('exports');

        Excel::store(new \App\Exports\FarmMultiSheetExport($farm, $fromDate, $toDate), $file);

        return response()->download($file)->deleteFileAfterSend(true);
    }

    // ========================================================
    //  PDF EXPORTS
    // ========================================================

    /**
     * Stream a PDF of all livestock records (optionally filtered by farm).
     */
    public function exportLivestockPdf(?Farm $farm = null)
    {
        $query = Livestock::with(['type', 'farm']);
        if ($farm) {
            $query->where('farm_id', $farm->id);
        }
        $livestock = $query->orderBy('created_at', 'desc')->get();
        $farmLabel = $farm?->name ?? 'All Farms';

        $pdf = PDF::loadView('exports.pdf.livestock', [
            'livestock'   => $livestock,
            'farmLabel'   => $farmLabel,
            'generatedAt' => now()->format('Y-m-d H:i'),
            'total'       => $livestock->count(),
            'byStatus'    => $livestock->groupBy('status')->map->count(),
        ]);

        return $pdf->stream('livestock_report_'.now()->format('Y_m_d_His').'.pdf');
    }

    /**
     * Stream a PDF of all harvest records.
     */
    public function exportHarvestsPdf(?Farm $farm = null)
    {
        $query = Harvest::with(['crop', 'field', 'farm']);
        if ($farm) {
            $query->where('farm_id', $farm->id);
        }
        $harvests = $query->orderBy('harvest_date', 'desc')->get();
        $farmLabel = $farm?->name ?? 'All Farms';

        $stats = [
            'total_qty'     => $harvests->sum('quantity_harvested'),
            'losses'        => $harvests->sum('loss_quantity'),
            'grade_a_count' => $harvests->where('quality_grade', Harvest::GRADE_A)->count(),
            'grade_b_count' => $harvests->where('quality_grade', Harvest::GRADE_B)->count(),
            'grade_c_count' => $harvests->where('quality_grade', Harvest::GRADE_C)->count(),
        ];

        $pdf = PDF::loadView('exports.pdf.harvests', [
            'harvests'   => $harvests,
            'farmLabel'  => $farmLabel,
            'generatedAt'=> now()->format('Y-m-d H:i'),
            'stats'      => $stats,
        ]);

        return $pdf->stream('harvests_report_'.now()->format('Y_m_d_His').'.pdf');
    }

    /**
     * Stream a PDF of full farm summary.
     */
    public function exportFarmPdf(Farm $farm)
    {
        $farm->loadMissing(['fields', 'fields.sensors', 'cropCycles.crop.field', 'livestock.type', 'equipment']);

        $stats = [
            'fields_count'      => $farm->fields()->count(),
            'livestock_count'   => $farm->livestock()->count(),
            'crops_count'       => $farm->cropCycles()->count(),
            'equipment_count'   => $farm->equipment()->count(),
            'harvest_records'   => $farm->harvests()->count(),
        ];

        $pdf = PDF::loadView('exports.pdf.farm_summary', [
            'farm'        => $farm,
            'generatedAt' => now()->format('Y-m-d H:i'),
            'stats'       => $stats,
        ]);

        return $pdf->stream('farm_summary_'.$farm->name.'_'.now()->format('Y_m_d_His').'.pdf');
    }

    // ========================================================
    //  CSV IMPORT
    // ========================================================

    /**
     * Import livestock rows from a CSV file.
     * Returns array of {rowIndex, status, message}.
     */
    public function importLivestockCsv(string $filePath, int $userId): array
    {
        $results = [];
        $expectedHeaders = ['tag_number', 'tracking_id', 'name', 'type', 'status',
                            'gender', 'weight', 'date_acquired', 'birth_date', 'purchase_price', 'sale_price', 'notes'];

        if (! file_exists($filePath) || ! is_readable($filePath)) {
            return [[0, 'error', 'File not found or unreadable']];
        }

        $handle = fopen($filePath, 'r');
        if (! $handle) {
            return [[0, 'error', 'Unable to open file']];
        }

        $rawHeaders = fgetcsv($handle);
        if ($rawHeaders === false) {
            fclose($handle);
            return [[0, 'error', 'Empty or invalid CSV file']];
        }

        $normalizedHeaders = array_map(fn ($h) => strtolower(str_replace([' ', '-', "\t"], ['_', '_', '_'], trim($h))), $rawHeaders);
        $rowIndex = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowIndex++;
            if (count($row) === 1 && trim($row[0]) === '') {
                continue;
            }

            $data = array_combine($normalizedHeaders, $row);

            if (! $data) {
                $results[] = [$rowIndex, 'error', 'Row has wrong number of columns'];
                continue;
            }

            $headerDiff = array_diff($expectedHeaders, array_keys($data));
            if (! empty($headerDiff)) {
                $results[] = [$rowIndex, 'error', 'Missing columns: '.implode(', ', $headerDiff)];
                continue;
            }

            if (empty(trim($data['tag_number'] ?? ''))) {
                $results[] = [$rowIndex, 'error', 'tag_number is required'];
                continue;
            }

            $typeId = null;
            if (! empty($data['type'])) {
                $type = LivestockType::where('name', $data['type'])->first();
                if ($type) {
                    $typeId = $type->id;
                }
            }

            $farmId = auth()->check() ? auth()->user()->farm_id : null;

            try {
                Livestock::updateOrCreate(
                    ['tag_number' => $data['tag_number'], 'user_id' => $userId],
                    [
                        'tracking_id'    => $data['tracking_id'] ?? null,
                        'name'           => $data['name'] ?? null,
                        'livestock_type_id' => $typeId,
                        'farm_id'        => $farmId,
                        'status'         => $data['status'] ?? 'healthy',
                        'gender'         => $data['gender'] ?? null,
                        'weight'         => $data['weight'] ?? null,
                        'date_acquired'  => ! empty($data['date_acquired']) ? $data['date_acquired'] : null,
                        'birth_date'     => ! empty($data['birth_date']) ? $data['birth_date'] : null,
                        'purchase_price' => $data['purchase_price'] ?? null,
                        'sale_price'     => $data['sale_price'] ?? null,
                        'notes'          => $data['notes'] ?? null,
                        'user_id'        => $userId,
                    ]
                );
                $results[] = [$rowIndex, 'success', 'Imported / updated successfully'];
            } catch (\Throwable $e) {
                $results[] = [$rowIndex, 'error', $e->getMessage()];
            }
        }

        fclose($handle);
        return $results;
    }

    /**
     * Import crops rows from CSV file.
     */
    public function importCropsCsv(string $filePath, int $userId): array
    {
        $results = [];
        $expectedHeaders = ['name', 'variety', 'category', 'field', 'planting_date',
                            'expected_harvest_date', 'status', 'yield_estimate', 'notes'];

        if (! file_exists($filePath) || ! is_readable($filePath)) {
            return [[0, 'error', 'File not found or unreadable']];
        }

        $handle = fopen($filePath, 'r');
        if (! $handle) {
            return [[0, 'error', 'Unable to open file']];
        }

        $rawHeaders = fgetcsv($handle);
        if ($rawHeaders === false) {
            fclose($handle);
            return [[0, 'error', 'Empty or invalid CSV file']];
        }

        $normalizedHeaders = array_map(fn ($h) => strtolower(str_replace([' ', '-', "\t"], ['_', '_', '_'], trim($h))), $rawHeaders);
        $rowIndex = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowIndex++;
            if (count($row) === 1 && trim($row[0]) === '') {
                continue;
            }

            $data = array_combine($normalizedHeaders, $row);
            if (! $data) {
                $results[] = [$rowIndex, 'error', 'Row has wrong number of columns'];
                continue;
            }

            if (empty(trim($data['name'] ?? ''))) {
                $results[] = [$rowIndex, 'error', 'name is required'];
                continue;
            }

            $fieldId = null;
            if (! empty($data['field'])) {
                $field = Field::where('name', $data['field'])->first();
                if ($field) {
                    $fieldId = $field->id;
                }
            }

            try {
                Crop::updateOrCreate(
                    ['name' => $data['name'], 'user_id' => $userId],
                    [
                        'variety'              => $data['variety'] ?? null,
                        'category'             => $data['category'] ?? null,
                        'field_id'             => $fieldId,
                        'planting_date'        => ! empty($data['planting_date']) ? $data['planting_date'] : null,
                        'expected_harvest_date'=> ! empty($data['expected_harvest_date']) ? $data['expected_harvest_date'] : null,
                        'status'               => $data['status'] ?? null,
                        'average_yield_per_hectare' => $data['yield_estimate'] ?? null,
                        'notes'                => $data['notes'] ?? null,
                        'user_id'              => $userId,
                    ]
                );
                $results[] = [$rowIndex, 'success', 'Imported / updated successfully'];
            } catch (\Throwable $e) {
                $results[] = [$rowIndex, 'error', $e->getMessage()];
            }
        }

        fclose($handle);
        return $results;
    }

    /**
     * Import equipment rows from CSV file.
     */
    public function importEquipmentCsv(string $filePath, int $userId): array
    {
        $results = [];
        $expectedHeaders = ['name', 'type', 'model_number', 'serial_number', 'status',
                            'condition', 'purchase_date', 'purchase_cost', 'description'];

        if (! file_exists($filePath) || ! is_readable($filePath)) {
            return [[0, 'error', 'File not found or unreadable']];
        }

        $handle = fopen($filePath, 'r');
        if (! $handle) {
            return [[0, 'error', 'Unable to open file']];
        }

        $rawHeaders = fgetcsv($handle);
        if ($rawHeaders === false) {
            fclose($handle);
            return [[0, 'error', 'Empty or invalid CSV file']];
        }

        $normalizedHeaders = array_map(fn ($h) => strtolower(str_replace([' ', '-', "\t"], ['_', '_', '_'], trim($h))), $rawHeaders);
        $rowIndex = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowIndex++;
            if (count($row) === 1 && trim($row[0]) === '') {
                continue;
            }

            $data = array_combine($normalizedHeaders, $row);
            if (! $data) {
                $results[] = [$rowIndex, 'error', 'Row has wrong number of columns'];
                continue;
            }

            if (empty(trim($data['name'] ?? ''))) {
                $results[] = [$rowIndex, 'error', 'name is required'];
                continue;
            }

            try {
                Equipment::updateOrCreate(
                    ['serial_number' => $data['serial_number']],
                    [
                        'name'         => $data['name'],
                        'type'         => $data['type'] ?? null,
                        'model_number' => $data['model_number'] ?? null,
                        'status'       => $data['status'] ?? null,
                        'condition'    => $data['condition'] ?? null,
                        'purchase_date'=> ! empty($data['purchase_date']) ? $data['purchase_date'] : null,
                        'purchase_cost'=> $data['purchase_cost'] ?? null,
                        'description'  => $data['description'] ?? null,
                    ]
                );
                $results[] = [$rowIndex, 'success', 'Imported / updated successfully'];
            } catch (\Throwable $e) {
                $results[] = [$rowIndex, 'error', $e->getMessage()];
            }
        }

        fclose($handle);
        return $results;
    }

    // ========================================================
    //  PRIVATE: CSV / XLSX serialisation helper
    // ========================================================

    /**
     * Serialise a Collection into a CSV string or trigger an Excel download.
     */
    private function exportToFormat(
        Collection $data,
        string $filename,
        string $format,
        array $headers,
        callable $rowMapper
    ): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Symfony\Component\HttpFoundation\StreamedResponse {
        $rows = $data->map($rowMapper)->toArray();
        array_unshift($rows, $headers);

        if ($format === 'xlsx') {
            return Excel::download(new \App\Exports\GenericExport($rows), $filename.'.xlsx');
        }

        // CSV — apply row limit guard
        if (count($rows) > self::MAX_CSV_ROWS) {
            abort(413, 'Export limit reached. Please use a date-range filter and try again.');
        }

        $csvContent = implode("\n", array_map(function ($row) {
            return implode(',', array_map(function ($cell) {
                return '"'.str_replace('"', '""', (string) $cell).'"';
            }, $row));
        }, $rows));

        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, $filename.'.csv', [
            'Content-Type' => 'text/csv',
            'Cache-Control'=> 'no-cache, no-store, must-revalidate',
            'Pragma'       => 'no-cache',
            'Expires'      => 0,
        ]);
    }
}
