<?php

namespace App\Services;

use App\Models\CropAnalysis;
use App\Models\Field;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CropAnalysisService
{
    public function __construct(
        protected OpenAIService $openaiService,
        protected PlantIdService $plantIdService,
        protected WeatherService $weatherService
    ) {}

    public function analyze(UploadedFile $image, ?int $fieldId = null): CropAnalysis
    {
        try {
            $result = $this->fetchAnalysis($image);
        } catch (\Exception $e) {
            Log::error('Crop analysis fetch failed: '.$e->getMessage());
            $result = $this->fallbackAnalysisResult();
        }

        $path = $this->storeImage($image);

        // 🌦️ Get weather context if field exists
        $weather = $this->getWeatherForField($fieldId);

        return CropAnalysis::create([
            'field_id' => $fieldId,
            'user_id' => Auth::id(),
            'image_path' => $path,

            'diagnosis' => $result['disease_name'] ?? 'Analysis Unavailable',
            'description' => $result['description'] ?? 'The AI could not fully analyze this image. Please try again with a clearer photo under good lighting.',
            'severity' => $result['severity'] ?? null,
            'confidence_score' => $result['confidence'] ?? 0,
            'detected_issues' => $result['detected_issues'] ?? [],

            'weather_data' => $weather ? json_encode($weather) : null,

            'recommendation' => $result['recommendation'] ?? 'Please consult an agricultural expert for a thorough assessment.',
            'status' => 'analyzed',
        ]);
    }

    protected function fallbackAnalysisResult(): array
    {
        return [
            'disease_name' => 'Analysis Unavailable',
            'description' => 'The analysis service is currently unavailable. Please try again later.',
            'severity' => null,
            'confidence' => 0,
            'detected_issues' => [],
            'recommendation' => 'Please try again later or contact support if the problem persists.',
        ];
    }

   protected function fetchAnalysis(UploadedFile $image): array
{
    try {
        $result = $this->plantIdService->analyzePlantHealth($image);

        return $this->normalizeAndEnrich($result);

    } catch (\Throwable $e) {
        Log::error('Analysis failed: ' . $e->getMessage());

        return $this->fallback();
    }
}
protected function normalizeAndEnrich(array $data): array
{
    // 🔍 Normalize Plant.id response structure safely
    $diseaseName =
        $data['disease_name']
        ?? $data['result']['disease']['name']
        ?? $data['result']['classification']['suggestion']
        ?? 'Unknown disease';

    $severity =
        $data['severity']
        ?? $data['result']['severity']
        ?? 'medium';

    $confidence =
        $data['confidence']
        ?? $data['result']['score']
        ?? 0;

    $description =
        $data['description']
        ?? $data['result']['disease']['description']
        ?? '';

    // 🌦️ attach weather context if exists later
    $data['disease_name'] = $diseaseName;
    $data['severity'] = $severity;
    $data['confidence'] = $confidence;
    $data['description'] = $description;

    // 🧠 generate recommendation
    $data = $this->enrich($data);

    return $data;
}

    protected function enrich(array $result): array
    {
       $weather = $this->getWeatherForField(request()->field_id ?? null);

        try {
            $ai = $this->openaiService->generateRecommendation(
                $result['disease_name'],
                'crop',
                $result['severity'] ?? 'medium',
                $result['description'] ?? '',
                $weather // 🌦️ weather-aware AI prompt
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
     * 🌦️ Get weather for a specific field
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

    protected function storeImage(UploadedFile $image): string
    {
        return $image->storeAs(
            'crop-analyses',
            Str::uuid() . '.' . $image->getClientOriginalExtension(),
            'public'
        );
    }
}