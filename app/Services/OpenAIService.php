<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected string $apiKey;

    protected string $model;

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY', '');
        $this->model = env('OPENAI_MODEL', 'gpt-3.5-turbo');
    }

    /**
     * Get the API key
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Generate recommendation for crop health issues
     *
     * @param string $diseaseName
     * @param string $type (crop/livestock)
     * @param string $severity
     * @param string $description
     * @param array|null $weather Optional weather data: [temperature, humidity, rain_probability, cloud_cover, wind_speed]
     */
    public function generateRecommendation(
        string $diseaseName,
        string $type,
        string $severity,
        string $description,
        ?array $weather = null
    ): string {
        if (empty($this->apiKey)) {
            Log::warning('OpenAI: No API key configured');
            return '';
        }

        try {
            $prompt = $this->buildCropRecommendationPrompt($diseaseName, $type, $severity, $description, $weather);

            Log::info('OpenAI request', [
                'disease' => $diseaseName,
                'has_weather' => $weather !== null,
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert agricultural consultant specializing in integrated pest management and crop health. Provide clear, actionable advice for farmers. Focus on practical, sustainable solutions including both organic and chemical options where appropriate. Mention safety precautions and environmental considerations.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'max_tokens' => 500,
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';
                Log::info('OpenAI response received', ['length' => strlen($content)]);
                return $content;
            }

            Log::error('OpenAI API error: '.$response->status().' - '.$response->body());
        } catch (\Exception $e) {
            Log::error('OpenAI API exception: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return '';
    }

    /**
     * Build prompt for crop recommendation with optional weather context
     */
    protected function buildCropRecommendationPrompt(
        string $diseaseName,
        string $type,
        string $severity,
        string $description,
        ?array $weather = null
    ): string {
        $prompt = "Analyze this crop condition:\n";
        $prompt .= "- Disease/condition: {$diseaseName}\n";
        $prompt .= "- Type: {$type}\n";
        $prompt .= "- Severity: {$severity}\n";
        $prompt .= "- Description: {$description}\n\n";

        if ($weather) {
            $prompt .= "Current Weather Conditions:\n";
            $prompt .= $weather['temperature'] !== null ? "- Temperature: {$weather['temperature']}°C\n" : '';
            $prompt .= $weather['humidity'] !== null ? "- Humidity: {$weather['humidity']}%\n" : '';
            $prompt .= $weather['rain_probability'] !== null ? "- Rain Probability: {$weather['rain_probability']}%\n" : '';
            $prompt .= $weather['cloud_cover'] !== null ? "- Cloud Cover: {$weather['cloud_cover']}%\n" : '';
            $prompt .= $weather['wind_speed'] !== null ? "- Wind Speed: {$weather['wind_speed']} km/h\n" : '';
            $prompt .= "\n";
        }

        $prompt .= "Provide practical recommendations for a Kenyan farmer including:\n";
        $prompt .= "1. Immediate actions to take\n";
        $prompt .= "2. Treatment options (organic and chemical if appropriate)\n";
        $prompt .= "3. Prevention strategies\n";
        $prompt .= "4. Safety considerations\n";
        $prompt .= "5. When to seek expert help\n\n";
        $prompt .= 'Keep recommendations clear, concise, and actionable.';

        return $prompt;
    }
}
