<?php

namespace App\Services;

use App\Models\Crop;
use App\Models\CropCycle;
use App\Models\Field;
use App\Models\Sensor;
use App\Models\SensorReading;
use App\Models\WeatherData;
use Carbon\Carbon;

class AgronomistAIService
{
    protected $cropAnalysisService;
    protected $fertilizerCalculator;
    protected $cropCycleAnalysisService;
    protected $weatherService;

    public function __construct(
        CropAnalysisService $cropAnalysisService,
        FertilizerCalculator $fertilizerCalculator,
        CropCycleAnalysisService $cropCycleAnalysisService,
        WeatherService $weatherService
    ) {
        $this->cropAnalysisService = $cropAnalysisService;
        $this->fertilizerCalculator = $fertilizerCalculator;
        $this->cropCycleAnalysisService = $cropCycleAnalysisService;
        $this->weatherService = $weatherService;
    }

    /**
     * Analyze crop health using image and/or sensor data.
     *
     * @param  string  $imagePath  Path to crop image (optional)
     * @param  int     $fieldId    Field ID to get sensor data (optional)
     * @return array
     */
    public function analyzeCropHealth(?string $imagePath = null, ?int $fieldId = null): array
    {
        $result = [
            'success' => false,
            'health_score' => 0,
            'status' => 'unknown',
            'details' => [],
            'recommendations' => [],
        ];

        // Analyze image if provided
        if ($imagePath && file_exists($imagePath)) {
            $imageAnalysis = $this->cropAnalysisService->analyzeCrop($imagePath);
            if ($imageAnalysis['success']) {
                $result['success'] = true;
                $result['details']['image_analysis'] = $imageAnalysis;
                // We can use the disease and confidence from image analysis to compute health score
                $healthScore = $this->calculateHealthScoreFromImage($imageAnalysis);
                $result['health_score'] = $healthScore;
                $result['status'] = $this->getHealthStatus($healthScore);
            }
        }

        // Get sensor data for the field if fieldId is provided
        if ($fieldId) {
            $sensorData = $this->getFieldSensorData($fieldId);
            if ($sensorData) {
                $result['details']['sensor_data'] = $sensorData;
                $sensorHealthScore = $this->calculateHealthScoreFromSensors($sensorData);
                // Combine health scores if we have both
                if ($result['success']) {
                    $result['health_score'] = round(($result['health_score'] + $sensorHealthScore) / 2);
                } else {
                    $result['health_score'] = $sensorHealthScore;
                    $result['success'] = true;
                }
                $result['status'] = $this->getHealthStatus($result['health_score']);
            }
        }

        // If we have no data, we cannot analyze
        if (! $result['success']) {
            $result['status'] = 'insufficient_data';
            return $result;
        }

        // Generate recommendations based on health score and data
        $result['recommendations'] = $this->generateHealthRecommendations($result['health_score'], $result['details']);

        return $result;
    }

    /**
     * Suggest fertilizer for a crop in a field.
     *
     * @param  int  $cropId
     * @param  int  $fieldId
     * @return array
     */
    public function suggestFertilizer(int $cropId, int $fieldId): array
    {
        $crop = Crop::find($cropId);
        $field = Field::find($fieldId);

        if (! $crop || ! $field) {
            return [
                'success' => false,
                'message' => 'Crop or field not found',
            ];
        }

        // Get the latest sensor reading for the field (if any)
        $soilReading = $this->getLatestSoilReading($fieldId);

        $fertilizerSuggestion = $this->fertilizerCalculator->calculate($crop, $field, $soilReading);

        return [
            'success' => true,
            'crop' => $crop->name,
            'field' => $field->name,
            'fertilizer_suggestion' => $fertilizerSuggestion,
        ];
    }

    /**
     * Predict harvest time for a crop cycle.
     *
     * @param  int  $cropCycleId
     * @return array
     */
    public function predictHarvestTime(int $cropCycleId): array
    {
        $cropCycle = CropCycle::find($cropCycleId);

        if (! $cropCycle) {
            return [
                'success' => false,
                'message' => 'Crop cycle not found',
            ];
        }

        $analysis = $this->cropCycleAnalysisService->analyze($cropCycle);

        return [
            'success' => true,
            'crop_cycle_id' => $cropCycle->id,
            'crop' => $cropCycle->crop->name,
            'current_stage' => $analysis['current_stage'],
            'days_since_planting' => $analysis['days_since_planting'],
            'progress_percentage' => $analysis['progress_percentage'],
            'expected_harvest_date' => $analysis['expected_harvest_date'],
            'recommended_activities' => $analysis['recommended_activities'],
        ];
    }

    /**
     * Detect risks from sensor, weather, and crop data.
     *
     * @param  int  $fieldId
     * @return array
     */
    public function detectRisks(int $fieldId): array
    {
        $field = Field::find($fieldId);

        if (! $field) {
            return [
                'success' => false,
                'message' => 'Field not found',
            ];
        }

        $risks = [];

        // Get latest sensor data
        $sensorData = $this->getFieldSensorData($fieldId);
        if ($sensorData) {
            $risks = array_merge($risks, $this->detectSensorRisks($sensorData));
        }

        // Get weather data for the farm (assuming field belongs to a farm)
        $weatherData = $this->getFarmWeatherData($field->farm_id);
        if ($weatherData) {
            $risks = array_merge($risks, $this->detectWeatherRisks($weatherData));
        }

        // Get crop cycles for the field to detect crop-specific risks
        $cropCycles = $field->cropCycles()->active()->get();
        foreach ($cropCycles as $cropCycle) {
            $cropRisks = $this->detectCropRisks($cropCycle);
            $risks = array_merge($risks, $cropRisks);
        }

        // Prioritize risks
        $risks = $this->prioritizeRisks($risks);

        return [
            'success' => true,
            'field' => $field->name,
            'risks' => $risks,
        ];
    }

    // Helper methods

    protected function calculateHealthScoreFromImage(array $imageAnalysis): int
    {
        // Simple health score based on confidence and disease detection
        $confidence = $imageAnalysis['confidence'] ?? 0;
        $disease = $imageAnalysis['disease'] ?? '';

        // If no disease detected, health score is high
        if (stripos($disease, 'no disease') !== false || stripos($disease, 'healthy') !== false) {
            return min(100, 80 + $confidence * 0.2); // Base 80 plus up to 20 from confidence
        }

        // If disease detected, health score is lower
        return max(0, 50 - $confidence * 0.5); // Base 50 minus up to 50 from confidence
    }

    protected function getHealthStatus(int $score): string
    {
        if ($score >= 80) {
            return 'excellent';
        } elseif ($score >= 60) {
            return 'good';
        } elseif ($score >= 40) {
            return 'fair';
        } elseif ($score >= 20) {
            return 'poor';
        } else {
            return 'critical';
        }
    }

    protected function getFieldSensorData(int $fieldId): ?array
    {
        // Get the latest sensor reading from the field's sensors
        $latestReading = SensorReading::query()
            ->whereHas('sensor', function ($query) use ($fieldId) {
                $query->where('field_id', $fieldId);
            })
            ->orderBy('timestamp', 'desc')
            ->first();

        if (! $latestReading) {
            return null;
        }

        return [
            'soil_moisture' => $latestReading->soil_moisture,
            'temperature' => $latestReading->temperature,
            'humidity' => $latestReading->humidity,
            'soil_ph' => $latestReading->soil_ph,
            'nitrogen_level' => $latestReading->nitrogen_level,
            'phosphorus_level' => $latestReading->phosphorus_level,
            'potassium_level' => $latestReading->potassium_level,
            'timestamp' => $latestReading->timestamp,
        ];
    }

    protected function getLatestSoilReading(int $fieldId): ?object
    {
        $reading = SensorReading::query()
            ->whereHas('sensor', function ($query) use ($fieldId) {
                $query->where('field_id', $fieldId);
            })
            ->orderBy('timestamp', 'desc')
            ->first();

        return $reading;
    }

    protected function calculateHealthScoreFromSensors(array $sensorData): int
    {
        $score = 100; // Start with perfect score

        // Soil moisture: ideal range 30-70%
        if (isset($sensorData['soil_moisture'])) {
            $moisture = $sensorData['soil_moisture'];
            if ($moisture < 30) {
                $score -= 20; // Dry
            } elseif ($moisture > 70) {
                $score -= 10; // Wet (less critical than dry)
            }
        }

        // Temperature: ideal range 15-30°C for most crops
        if (isset($sensorData['temperature'])) {
            $temp = $sensorData['temperature'];
            if ($temp < 10 || $temp > 35) {
                $score -= 20;
            } elseif ($temp < 15 || $temp > 30) {
                $score -= 10;
            }
        }

        // Soil pH: ideal range 6.0-7.5 for most crops
        if (isset($sensorData['soil_ph'])) {
            $ph = $sensorData['soil_ph'];
            if ($ph < 5.5 || $ph > 8.0) {
                $score -= 15;
            } elseif ($ph < 6.0 || $ph > 7.5) {
                $score -= 10;
            }
        }

        // NPK levels: we don't have ideal values here, but we can check for extreme deficiencies
        // We'll skip for now and rely on the fertilizer calculator for NPK advice.

        return max(0, $score);
    }

    protected function generateHealthRecommendations(int $healthScore, array $details): array
    {
        $recommendations = [];

        if ($healthScore < 50) {
            $recommendations[] = 'Crop health is poor. Immediate action required.';
        } elseif ($healthScore < 70) {
            $recommendations[] = 'Crop health is fair. Consider corrective actions.';
        } else {
            $recommendations[] = 'Crop health is good. Maintain current practices.';
        }

        // Add specific recommendations based on sensor data if available
        if (isset($details['sensor_data'])) {
            $sensor = $details['sensor_data'];
            if (isset($sensor['soil_moisture']) && $sensor['soil_moisture'] < 30) {
                $recommendations[] = 'Soil moisture is low. Increase irrigation.';
            }
            if (isset($sensor['temperature']) && ($sensor['temperature'] > 35 || $sensor['temperature'] < 10)) {
                $recommendations[] = 'Temperature is extreme. Consider protective measures.';
            }
        }

        return $recommendations;
    }

    protected function detectSensorRisks(array $sensorData): array
    {
        $risks = [];

        if (isset($sensorData['soil_moisture'])) {
            $moisture = $sensorData['soil_moisture'];
            if ($moisture < 20) {
                $risks[] = [
                    'type' => 'drought',
                    'severity' => 'high',
                    'message' => 'Soil moisture is critically low. Risk of drought stress.',
                    'value' => $moisture,
                    'unit' => '%',
                ];
            } elseif ($moisture > 80) {
                $risks[] = [
                    'type' => 'waterlogging',
                    'severity' => 'medium',
                    'message' => 'Soil moisture is high. Risk of waterlogging and root diseases.',
                    'value' => $moisture,
                    'unit' => '%',
                ];
            }
        }

        if (isset($sensorData['temperature'])) {
            $temp = $sensorData['temperature'];
            if ($temp > 40) {
                $risks[] = [
                    'type' => 'heat_stress',
                    'severity' => 'high',
                    'message' => 'Temperature is extremely high. Risk of heat stress and crop damage.',
                    'value' => $temp,
                    'unit' => '°C',
                ];
            } elseif ($temp < 0) {
                $risks[] = [
                    'type' => 'frost',
                    'severity' => 'high',
                    'message' => 'Temperature is below freezing. Risk of frost damage.',
                    'value' => $temp,
                    'unit' => '°C',
                ];
            }
        }

        return $risks;
    }

    protected function getFarmWeatherData(int $farmId): ?array
    {
        $latestWeather = WeatherData::where('farm_id', $farmId)
            ->orderBy('recorded_at', 'desc')
            ->first();

        if (! $latestWeather) {
            return null;
        }

        return [
            'temperature' => $latestWeather->temperature,
            'humidity' => $latestWeather->humidity,
            'precipitation' => $latestWeather->precipitation,
            'wind_speed' => $latestWeather->wind_speed,
            'pressure' => $latestWeather->pressure,
            'uv_index' => $latestWeather->uv_index,
            'cloud_cover' => $latestWeather->cloud_cover,
            'weather_condition' => $latestWeather->weather_condition,
            'recorded_at' => $latestWeather->recorded_at,
        ];
    }

    protected function detectWeatherRisks(array $weatherData): array
    {
        $risks = [];

        if (isset($weatherData['precipitation']) && $weatherData['precipitation'] > 50) {
            $risks[] = [
                'type' => 'heavy_rain',
                'severity' => 'medium',
                'message' => 'Heavy rainfall detected. Risk of flooding and soil erosion.',
                'value' => $weatherData['precipitation'],
                'unit' => 'mm',
            ];
        }

        if (isset($weatherData['wind_speed']) && $weatherData['wind_speed'] > 15) {
            $risks[] = [
                'type' => 'high_wind',
                'severity' => 'medium',
                'message' => 'High wind speeds detected. Risk of crop damage and spraying inefficiency.',
                'value' => $weatherData['wind_speed'],
                'unit' => 'm/s',
            ];
        }

        if (isset($weatherData['temperature']) && $weatherData['temperature'] > 35) {
            $risks[] = [
                'type' => 'heat_wave',
                'severity' => 'high',
                'message' => 'High temperature detected. Risk of heat stress and increased water demand.',
                'value' => $weatherData['temperature'],
                'unit' => '°C',
            ];
        }

        return $risks;
    }

    protected function detectCropRisks(CropCycle $cropCycle): array
    {
        $risks = [];

        // We can check if the crop cycle is behind schedule
        $expectedDuration = $cropCycle->crop->days_to_maturity;
        if ($expectedDuration) {
            $startDate = Carbon::parse($cropCycle->start_date);
            $expectedEndDate = $startDate->copy()->addDays($expectedDuration);
            $now = Carbon::now();

            if ($now->gt($expectedEndDate)) {
                $daysLate = $now->diffInDays($expectedEndDate);
                $risks[] = [
                    'type' => 'growth_delay',
                    'severity' => 'medium',
                    'message' => "Crop cycle is {$daysLate} days behind schedule. Possible nutrient deficiency or stress.",
                    'days_late' => $daysLate,
                ];
            }
        }

        return $risks;
    }

    protected function prioritizeRisks(array $risks): array
    {
        // Sort by severity: high, medium, low
        usort($risks, function ($a, $b) {
            $severityOrder = ['high' => 3, 'medium' => 2, 'low' => 1];
            $aSeverity = $severityOrder[$a['severity']] ?? 0;
            $bSeverity = $severityOrder[$b['severity']] ?? 0;

            return $bSeverity - $aSeverity;
        });

        return $risks;
    }
}
