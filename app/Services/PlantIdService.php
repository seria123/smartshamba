<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class PlantIdService
{
    protected string $apiKey;

    protected string $apiUrl;

    protected OpenAIService $openaiService;

    public function __construct(OpenAIService $openaiService)
    {
        $this->apiKey = config('services.plant_id.key');
        $this->apiUrl = config('services.plant_id.url', 'https://api.plant.id/v3/health_assessment');
        $this->openaiService = $openaiService;
        
        // Log for debugging
        Log::debug('PlantIdService constructed', [
            'api_key_present' => !empty($this->apiKey),
            'api_key_length' => strlen($this->apiKey ?? ''),
            'api_key_value' => $this->apiKey ?? 'NULL',
            'api_url' => $this->apiUrl,
            'config_key' => config('services.plant_id.key'),
            'config_url' => config('services.plant_id.url')
        ]);
    }
    public function analyzePlantHealth(UploadedFile $image): array
    {
        if (empty($this->apiKey)) {
            Log::warning('API key not configured');
            return $this->fallback();
        }

        Log::debug('Plant ID service configuration', [
            'api_key_present' => !empty($this->apiKey),
            'api_key_length' => strlen($this->apiKey),
            'api_url' => $this->apiUrl
        ]);

        try {
            $base64 = base64_encode(file_get_contents($image->getRealPath()));

            $response = Http::timeout(60)
                ->withHeaders([
                    'Api-Key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl, [
                    'images' => [$base64],
                    'plant_language' => 'en',
                    'similar_images' => true,
                ]);

            if (! $response->successful()) {
                Log::error('API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'url' => $this->apiUrl,
                    'key_prefix' => substr($this->apiKey, 0, 10) . '...'
                ]);

                return $this->fallback();
            }

            $data = $response->json();

            Log::info('Kindwise response', [
                'response' => $data
            ]);

            return $this->format($data);

        } catch (\Throwable $e) {
            Log::error('Exception: '.$e->getMessage());
            return $this->fallback();
        }
    }
    protected function format(array $data): array
    {
        if (! isset($data['result'])) {
            return $this->fallback();
        }

        $suggestions = data_get($data, 'result.disease.suggestions', []);
        $isHealthy = data_get($data, 'result.is_healthy', null);

        if ($isHealthy === true) {
            return [
                'disease_name' => 'Healthy plant',
                'description' => 'The plant appears healthy.',
                'severity' => 'low',
                'confidence' => 90,
                'detected_issues' => [],
            ];
        }

        if (empty($suggestions)) {
            return [
                'disease_name' => 'No disease detected',
                'description' => 'No visible disease detected.',
                'severity' => 'low',
                'confidence' => 70,
                'detected_issues' => [],
            ];
        }

        $disease = $suggestions[0];

        return [
            'disease_name' => $disease['name'] ?? 'Unknown condition',
            'description' => data_get($disease, 'details.description') ?? '',
            'severity' => $this->mapSeverityFromEntity($disease),
            'confidence' => round(($disease['probability'] ?? 0) * 100, 2),
            'detected_issues' => [['type' => 'plant_disease']],
        ];
    }

    protected function openaiFallback(UploadedFile $image): array
    {
        Log::warning('Plant.id failed, attempting OpenAI fallback');

        try {
            $result = $this->openaiService->analyzeImage([]);

            return [
                'disease_name' => $result['disease'] ?? 'Possible crop stress detected',
                'description' => $result['description'] ?? 'AI analysis unavailable.',
                'severity' => $result['severity'] ?? 'medium',
                'confidence' => $result['confidence'] ?? 50,
                'detected_issues' => [['type' => 'rule_based_fallback']],
            ];
        } catch (\Throwable $e) {
            Log::error('OpenAI fallback failed or quota exceeded', [
                'message' => $e->getMessage(),
            ]);

            return [
                'disease_name' => 'Possible crop stress detected',
                'description' => 'AI unavailable. Check leaves for pests, discoloration, or wilting.',
                'severity' => 'medium',
                'confidence' => 50,
                'detected_issues' => [['type' => 'rule_based_fallback']],
            ];
        }
    }

    protected function mapSeverityFromEntity(array $disease): string
    {
        $name = strtolower($disease['name'] ?? '');
        $p = $disease['probability'] ?? 0;

        if (str_contains($name, 'blight') || str_contains($name, 'rot') || $p > 0.75) {
            return 'high';
        }

        if ($p > 0.4) {
            return 'medium';
        }

        return 'low';
    }

    protected function fallback(): array
    {
        return [
            'disease_name' => 'Possible crop stress',
            'description' => 'Image unclear or no disease detected. Check for discoloration, pests, or wilting.',
            'severity' => 'medium',
            'confidence' => 40,
            'detected_issues' => [['type' => 'unknown_issue']],
        ];
    }
}
