<?php

namespace App\Services;

use App\Models\CropAnalysis;
use App\Models\Field;
use App\Models\CropCycle;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CropAnalysisService extends BaseAnalysisService
{
    protected PlantIdService $plantIdService;
    protected WeatherService $weatherService;

    public function __construct(
        OpenAIService $openaiService,
        PlantIdService $plantIdService,
        WeatherService $weatherService
    ) {
        parent::__construct($openaiService);
        $this->plantIdService = $plantIdService;
        $this->weatherService = $weatherService;
    }

    /**
     * Perform crop analysis on an uploaded image.
     */
    public function analyze(UploadedFile $image, ?int $fieldId = null, ?int $cropCycleId = null)
    {
        $path = $this->storeImage($image, 'crop-analyses');

        try {
            $result = $this->performAnalysis($image);

            // Get weather context if field exists
            $weather = $this->getWeatherForField($fieldId);

            $analysis = $this->createRecord(
                [
                    'image_path' => $path,
                    'diagnosis' => $result['disease_name'],
                    'description' => $result['description'],
                    'severity' => $result['severity'],
                    'recommendation' => $result['recommendation'],
                    'detected_issues' => $result['detected_issues'],
                    'confidence' => $result['confidence'],
                ],
                $fieldId, // relationId (for field_id)
                [
                    'field_id' => $fieldId,
                    'crop_cycle_id' => $cropCycleId,
                    'weather_data' => $weather ? json_encode($weather) : null,
                ]
            );

            return $analysis;
        } catch (\Exception $e) {
            Log::error('Crop analysis failed: ' . $e->getMessage());
            $fallback = $this->handleFailure($e->getMessage());

            $analysis = $this->createRecord(
                [
                    'image_path' => $path,
                    'diagnosis' => 'Analysis Unavailable',
                    'description' => 'The analysis service is currently unavailable. Please try again later.',
                    'severity' => null,
                    'recommendation' => 'Please try again later or contact support if the problem persists.',
                    'detected_issues' => [],
                    'confidence' => 0,
                ],
                $fieldId,
                [
                    'field_id' => $fieldId,
                    'crop_cycle_id' => $cropCycleId,
                    'weather_data' => null,
                ]
            );

            return $analysis;
        }
    }

    /**
     * Get the Eloquent model class.
     */
    protected function getModelClass(): string
    {
        return CropAnalysis::class;
    }

    /**
     * Get the foreign key name.
     */
    protected function getRelationIdName(): string
    {
        return 'field_id';
    }

    /**
     * Perform analysis using Plant.id service.
     */
    protected function performAnalysis(UploadedFile $image): array
    {
        try {
            $result = $this->plantIdService->analyzePlantHealth($image);

            return $this->normalizeAndEnrich($result);
        } catch (\Throwable $e) {
            Log::error('Analysis failed: ' . $e->getMessage());

            return $this->fallback();
        }
    }

    /**
     * Normalize Plant.id response structure and enrich with AI recommendation.
     */
    protected function normalizeAndEnrich(array $data): array
    {
        $diseaseName = $data['disease_name']
            ?? $data['result']['disease']['name']
            ?? $data['result']['classification']['suggestion']
            ?? 'Unknown disease';

        $severity = $data['severity']
            ?? $data['result']['severity']
            ?? 'medium';

        $confidence = $data['confidence']
            ?? $data['result']['score']
            ?? 0;

        $description = $data['description']
            ?? $data['result']['disease']['description']
            ?? '';

        $data['disease_name'] = $diseaseName;
        $data['severity'] = $severity;
        $data['confidence'] = $confidence;
        $data['description'] = $description;

        $data = $this->enrich($data);

        return $data;
    }

    /**
     * Enrich result with AI-generated recommendation.
     */
    protected function enrich(array $result): array
    {
        $weather = $this->getWeatherForField(request()->field_id ?? null);

        try {
            $ai = $this->openaiService->generateRecommendation(
                $result['disease_name'],
                'crop',
                $result['severity'] ?? 'medium',
                $result['description'] ?? '',
                $weather
            );
        } catch (\Throwable $e) {
            $ai = null;
            Log::warning('OpenAI unavailable, using local recommendation engine');
        }

        $result['recommendation'] = $ai
            ?: $this->generateLocalRecommendation($result);

        return $result;
    }

    /**
     * Get weather for a specific field.
     */
    protected function getWeatherForField(?int $fieldId): ?array
    {
        if (!$fieldId) {
            return null;
        }

        $field = Field::find($fieldId);

        if (!$field || !$field->location) {
            return null;
        }

        $weather = $this->weatherService->getWeather($field->location);

        if (!$weather || !isset($weather['data']['values'])) {
            return null;
        }

        $values = $weather['data']['values'];

        return [
            'temperature' => $values['temperature'] ?? null,
            'humidity' => $values['humidity'] ?? null,
            'rain_probability' => $values['precipitationProbability'] ?? null,
            'cloud_cover' => $values['cloudCover'] ?? null,
            'wind_speed' => $values['windSpeed'] ?? null,
        ];
    }

    /**
     * Generate local recommendation based on disease type and severity.
     */
    protected function generateLocalRecommendation(array $result): string
    {
        $disease = strtolower($result['disease_name'] ?? '');
        $severity = $result['severity'] ?? 'medium';

        $recommendations = [
            'powdery mildew' => [
                'high' => 'Apply sulfur-based fungicide immediately. Improve air circulation and avoid overhead watering.',
                'medium' => 'Apply fungicide and remove affected leaves.',
                'low' => 'Monitor and improve airflow.',
            ],
            'fungal' => [
                'high' => 'Apply fungicide immediately and remove infected tissue.',
                'medium' => 'Apply treatment and improve conditions.',
                'low' => 'Monitor and prevent excess moisture.',
            ],
            'bacteria' => [
                'high' => 'Remove infected plants and apply copper-based treatment.',
                'medium' => 'Improve sanitation and remove affected parts.',
                'low' => 'Monitor and maintain hygiene.',
            ],
            'virus' => [
                'high' => 'Remove infected plants immediately and control vectors.',
                'medium' => 'Monitor and control pests.',
                'low' => 'Observe and prevent spread.',
            ],
        ];

        foreach ($recommendations as $key => $levels) {
            if (str_contains($disease, $key)) {
                return ($levels[$severity] ?? $levels['medium'])
                    . ' Practice good crop rotation and field hygiene.';
            }
        }

        return match ($severity) {
            'high' => 'Treat immediately and isolate affected plants.',
            'low' => 'Monitor and maintain good farming practices.',
            default => 'Apply standard care and monitor progress.',
        };
    }

    /**
     * Fallback result when analysis fails.
     */
    protected function fallback(): array
    {
        return [
            'disease_name' => 'Unable to analyze',
            'description' => 'Image unclear or insufficient data.',
            'severity' => null,
            'confidence' => 0,
            'detected_issues' => [],
            'recommendation' => 'Retake image under good lighting.',
        ];
    }
}
