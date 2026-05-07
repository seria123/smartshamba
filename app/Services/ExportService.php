<?php

namespace App\Services;

use App\Models\Crop;
use App\Models\CropAnalysis;
use App\Models\Farm;
use App\Models\IrrigationLog;
use App\Models\Sensor;
use App\Models\SensorReading;
use App\Models\Task;
use App\Models\WeatherData;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class ExportService
{
    /**
     * Export sensor readings to CSV.
     */
    public function exportSensorReadings(
        ?Farm $farm = null,
        ?Carbon $fromDate = null,
        ?Carbon $toDate = null,
        string $format = 'csv'
    ): \Symfony\Component\HttpFoundation\BinaryFileResponse {
        $query = SensorReading::with(['sensor', 'sensor.field']);

        if ($farm) {
            $query->whereHas('sensor.field.farm', function ($q) use ($farm) {
                $q->where('id', $farm->id);
            });
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
            'Recorded At',
            'Field',
            'Sensor Name',
            'Temperature',
            'Humidity',
            'Soil Moisture',
            'Light',
            'Rain',
        ], function ($reading) {
            return [
                $reading->recorded_at->format('Y-m-d H:i:s'),
                $reading->sensor->field->name ?? 'N/A',
                $reading->sensor->name,
                $reading->temperature,
                $reading->humidity,
                $reading->soil_moisture,
                $reading->light,
                $reading->rain,
            ];
        });
    }

    /**
     * Export tasks to CSV.
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
            $query->whereHas('field.farm', function ($q) use ($farm) {
                $q->where('id', $farm->id);
            });
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
            'ID',
            'Title',
            'Description',
            'Type',
            'Status',
            'Priority',
            'Field',
            'Crop',
            'Assigned To',
            'Scheduled Date',
            'Completed Date',
            'Estimated Hours',
            'Actual Hours',
            'Estimated Cost',
            'Actual Cost',
        ], function ($task) {
            return [
                $task->id,
                $task->title,
                $task->description,
                $task->task_type,
                $task->status,
                $task->priority,
                $task->field->name ?? 'N/A',
                $task->crop->name ?? 'N/A',
                $task->assignedUser->name ?? 'N/A',
                $task->scheduled_date->format('Y-m-d'),
                $task->completed_date?->format('Y-m-d'),
                $task->estimated_hours,
                $task->actual_hours,
                $task->estimated_cost,
                $task->actual_cost,
            ];
        });
    }

    /**
     * Export irrigation logs to CSV.
     */
    public function exportIrrigationLogs(
        ?Farm $farm = null,
        ?Carbon $fromDate = null,
        ?Carbon $toDate = null,
        string $format = 'csv'
    ): \Symfony\Component\HttpFoundation\BinaryFileResponse {
        $query = IrrigationLog::with(['irrigationZone.field']);

        if ($farm) {
            $query->whereHas('irrigationZone.field.farm', function ($q) use ($farm) {
                $q->where('id', $farm->id);
            });
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
            'Zone',
            'Field',
            'Event Type',
            'Started At',
            'Ended At',
            'Duration (Minutes)',
            'Water Used (Liters)',
            'Soil Moisture Before',
            'Soil Moisture After',
            'Status',
            'Triggered By',
        ], function ($log) {
            return [
                $log->irrigationZone->name ?? 'N/A',
                $log->irrigationZone->field->name ?? 'N/A',
                $log->event_type,
                $log->started_at->format('Y-m-d H:i:s'),
                $log->ended_at?->format('Y-m-d H:i:s'),
                $log->duration_minutes,
                $log->water_used_liters,
                $log->soil_moisture_before,
                $log->soil_moisture_after,
                $log->status,
                $log->triggeredByUser->name ?? 'System',
            ];
        });
    }

    /**
     * Export weather data to CSV.
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
            'Recorded At',
            'Temperature',
            'Feels Like',
            'Humidity',
            'Pressure',
            'Wind Speed',
            'Wind Direction',
            'Precipitation',
            'Cloud Cover',
            'UV Index',
            'Visibility',
            'Weather Condition',
        ], function ($weather) {
            return [
                $weather->recorded_at->format('Y-m-d H:i:s'),
                $weather->temperature,
                $weather->feels_like,
                $weather->humidity,
                $weather->pressure,
                $weather->wind_speed,
                $weather->wind_direction,
                $weather->precipitation,
                $weather->cloud_cover,
                $weather->uv_index,
                $weather->visibility,
                $weather->weather_condition,
            ];
        });
    }

    /**
     * Export crops to CSV.
     */
    public function exportCrops(?Farm $farm = null, string $format = 'csv'): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $query = Crop::with(['field', 'field.farm']);

        if ($farm) {
            $query->whereHas('field.farm', function ($q) use ($farm) {
                $q->where('id', $farm->id);
            });
        }

        $data = $query->orderBy('planted_at', 'desc')->get();

        $filename = 'crops_'.now()->format('Y_m_d_His');

        return $this->exportToFormat($data, $filename, $format, [
            'Name',
            'Variety',
            'Field',
            'Farm',
            'Planted At',
            'Expected Harvest',
            'Status',
            'Yield Estimate (kg)',
            'Notes',
        ], function ($crop) {
            return [
                $crop->name,
                $crop->variety,
                $crop->field->name ?? 'N/A',
                $crop->field->farm->name ?? 'N/A',
                $crop->planted_at?->format('Y-m-d'),
                $crop->expected_harvest?->format('Y-m-d'),
                $crop->status,
                $crop->yield_estimate,
                $crop->notes,
            ];
        });
    }

    /**
     * Export crop analyses to CSV.
     */
    public function exportCropAnalyses(
        ?Farm $farm = null,
        ?Carbon $fromDate = null,
        ?Carbon $toDate = null,
        string $format = 'csv'
    ): \Symfony\Component\HttpFoundation\BinaryFileResponse {
        $query = CropAnalysis::with(['crop.field', 'crop.field.farm']);

        if ($farm) {
            $query->whereHas('crop.field.farm', function ($q) use ($farm) {
                $q->where('id', $farm->id);
            });
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
            'Crop',
            'Field',
            'Farm',
            'Analyzed At',
            'Health Score',
            'Detected Disease',
            'Disease Confidence',
            'Recommendations',
            'Image',
        ], function ($analysis) {
            return [
                $analysis->crop->name ?? 'N/A',
                $analysis->crop->field->name ?? 'N/A',
                $analysis->crop->field->farm->name ?? 'N/A',
                $analysis->analyzed_at->format('Y-m-d H:i:s'),
                $analysis->health_score,
                $analysis->detected_disease ?? 'None',
                $analysis->disease_confidence,
                $analysis->recommendations,
                $analysis->image_path,
            ];
        });
    }

    /**
     * Export to specified format.
     */
    private function exportToFormat(
        Collection $data,
        string $filename,
        string $format,
        array $headers,
        callable $rowMapper
    ): \Symfony\Component\HttpFoundation\BinaryFileResponse {
        $rows = $data->map($rowMapper)->toArray();
        array_unshift($rows, $headers);

        if ($format === 'xlsx') {
            return Excel::download(new \App\Exports\GenericExport($rows), $filename.'.xlsx');
        }

        // CSV format
        $csvContent = implode("\n", array_map(function ($row) {
            return implode(',', array_map(function ($cell) {
                return '"'.str_replace('"', '""', $cell).'"';
            }, $row));
        }, $rows));

        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, $filename.'.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
