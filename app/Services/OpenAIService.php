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
     * @param  string  $type  (crop/livestock)
     * @param  array|null  $weather  Optional weather data: [temperature, humidity, rain_probability, cloud_cover, wind_speed]
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

    public function analyzeImage(array $data): array
    {
        if (empty($this->apiKey)) {
            Log::warning('OpenAI: No API key configured for image analysis');

            return $this->fallbackResponse();
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert agricultural consultant. Analyze the provided crop or livestock image and provide a diagnosis. Return results in the specified JSON format.',
                    ],
                    [
                        'role' => 'user',
                        'content' => 'Analyze this image data and provide: disease name, description, severity (low/medium/high), and confidence (0-100). Data: '.json_encode($data),
                    ],
                ],
                'max_tokens' => 300,
                'temperature' => 0.5,
            ]);

            if ($response->successful()) {
                $content = $response->json()['choices'][0]['message']['content'] ?? '';

                return $this->parseOpenAIResponse($content);
            }

            Log::error('OpenAI image analysis error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $this->fallbackResponse();
        } catch (\Throwable $e) {
            Log::error('OpenAI fallback failed or quota exceeded', [
                'message' => $e->getMessage(),
            ]);

            return $this->fallbackResponse();
        }
    }

    public function answerSupportQuestion(string $question, array $context = [], array $knowledgeArticles = []): string
    {
        if (empty($this->apiKey) || $this->apiKey === 'your_openai_api_key_here') {
            Log::warning('OpenAI: No API key configured for support help bot');

            return '';
        }

        try {
            $knowledge = collect($knowledgeArticles)
                ->take(5)
                ->map(function (array $article) {
                    return [
                        'title' => $article['title'] ?? '',
                        'category' => $article['category'] ?? '',
                        'summary' => $article['summary'] ?? '',
                        'steps' => $article['steps'] ?? [],
                    ];
                })
                ->values()
                ->all();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(20)->post('https://api.openai.com/v1/chat/completions', [
                'model' => config('services.openai.model', $this->model),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are SmartShamba Farm Help Bot, a practical support assistant for Kenyan farmers. Give concise, safe, actionable answers. Use the provided farm context and help articles when relevant. If the issue could be serious, advise opening a support ticket and contacting a qualified agronomist or vet.',
                    ],
                    [
                        'role' => 'user',
                        'content' => json_encode([
                            'question' => $question,
                            'farm_context' => $context,
                            'knowledge_base_matches' => $knowledge,
                            'response_style' => 'Answer in short paragraphs. Include immediate steps and when to escalate.',
                        ]),
                    ],
                ],
                'max_tokens' => 700,
                'temperature' => 0.4,
            ]);

            if ($response->successful()) {
                return trim($response->json('choices.0.message.content', ''));
            }

            Log::error('OpenAI support bot error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Throwable $e) {
            Log::error('OpenAI support bot exception', ['message' => $e->getMessage()]);
        }

        return '';
    }

    protected function parseOpenAIResponse(string $content): array
    {
        return [
            'disease' => 'Analysis result',
            'description' => trim($content),
            'severity' => 'medium',
            'confidence' => 50,
        ];
    }

    protected function fallbackResponse(): array
    {
        return [
            'disease' => 'Possible crop stress detected',
            'description' => 'AI unavailable. Check leaves for pests, discoloration, or wilting.',
            'severity' => 'medium',
            'confidence' => 50,
        ];
    }
}
