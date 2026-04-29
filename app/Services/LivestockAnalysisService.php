<?php

namespace App\Services;

use App\Models\LivestockAnalysis;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LivestockAnalysisService extends BaseAnalysisService
{
    protected ?string $apiKey;

    public function __construct(OpenAIService $openaiService)
    {
        parent::__construct($openaiService);
        $this->apiKey = config('services.openai.key') ?: config('services.openai.api_key') ?: env('OPENAI_API_KEY');
    }

    /**
     * Perform livestock disease analysis on an uploaded image.
     */
    public function analyze(UploadedFile $image, ?int $livestockId = null)
    {
        $path = $this->storeImage($image, 'livestock-analyses');

        try {
            $result = $this->performAnalysis($image);

            $analysis = $this->createRecord([
                'image_path' => $path,
                'diagnosis' => $result['diagnosis'],
                'description' => $result['description'],
                'severity' => $result['severity'],
                'recommendation' => $result['recommendation'],
                'detected_issues' => $result['detected_issues'],
                'confidence' => $result['confidence'],
            ], $livestockId);

            return $analysis->fresh();
        } catch (\Exception $e) {
            Log::error('Livestock analysis failed: ' . $e->getMessage());
            $fallback = $this->handleFailure($e->getMessage());

            $analysis = $this->createRecord([
                'image_path' => $path,
                'diagnosis' => $fallback['diagnosis'],
                'description' => $fallback['description'],
                'severity' => $fallback['severity'],
                'recommendation' => $fallback['recommendation'],
                'detected_issues' => $fallback['detected_issues'],
                'confidence' => $fallback['confidence'],
            ], $livestockId);

            return $analysis;
        }
    }

    /**
     * Get the Eloquent model class.
     */
    protected function getModelClass(): string
    {
        return LivestockAnalysis::class;
    }

    /**
     * Get the foreign key name.
     */
    protected function getRelationIdName(): string
    {
        return 'livestock_id';
    }

    /**
     * Perform analysis using OpenAI Vision API or simulation fallback.
     */
    protected function performAnalysis(UploadedFile $image): array
    {
        if (!empty($this->apiKey) && $this->apiKey !== 'your_openai_api_key_here') {
            return $this->runOpenAIVisionAnalysis($image);
        }

        Log::warning('LivestockAnalysis: Using simulation mode - no OpenAI API key configured');

        return $this->runSimulationAnalysis($image);
    }

    /**
     * Use OpenAI Vision API to analyze livestock image for disease detection.
     */
    protected function runOpenAIVisionAnalysis(UploadedFile $image): array
    {
        try {
            $imageData = base64_encode(file_get_contents($image->getRealPath()));

            $prompt = <<<'PROMPT'
You are a veterinary AI assistant.

Analyze ONLY livestock animals (cow, goat, sheep, pig, chicken, etc).
DO NOT provide plant or crop diseases.

Return EXACTLY in this format:

SPECIES: <animal>
SYMPTOMS: <comma separated symptoms>
DISEASE: <disease name or HEALTHY>
SEVERITY: <low|medium|high>
CONFIDENCE: <0-100>

If image is not livestock, respond:
DISEASE: INVALID_IMAGE
PROMPT;

            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4.1',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => $prompt,
                                ],
                                [
                                    'type' => 'image_url',
                                    'image_url' => [
                                        'url' => 'data:image/jpeg;base64,' . $imageData,
                                        'detail' => 'high',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'max_tokens' => 300,
                    'temperature' => 0.3,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';

                return $this->parseOpenAIVisionResponse($content);
            }

            Log::error('OpenAI Vision API error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('OpenAI Vision exception: ' . $e->getMessage());
        }

        return $this->runSimulationAnalysis($image);
    }

    /**
     * Parse OpenAI Vision response into structured array.
     */
    protected function parseOpenAIVisionResponse(string $content): array
    {
        $lines = explode("\n", $content);
        $result = [
            'species' => 'Unknown',
            'symptoms' => '',
            'diagnosis' => 'Unknown Condition',
            'severity' => 'medium',
            'confidence' => 75.0,
        ];

        foreach ($lines as $line) {
            if (stripos($line, 'SPECIES:') === 0) {
                $result['species'] = trim(substr($line, 8));
            } elseif (stripos($line, 'SYMPTOMS:') === 0) {
                $result['symptoms'] = trim(substr($line, 9));
            } elseif (stripos($line, 'DISEASE:') === 0) {
                $result['diagnosis'] = trim(substr($line, 8));
            } elseif (stripos($line, 'SEVERITY:') === 0) {
                $severity = trim(substr($line, 9));
                if (in_array($severity, ['low', 'medium', 'high'])) {
                    $result['severity'] = $severity;
                }
            } elseif (stripos($line, 'CONFIDENCE:') === 0) {
                $confidence = floatval(trim(substr($line, 11)));
                $result['confidence'] = min(100, max(0, $confidence));
            }
        }

        // If healthy detected, adjust accordingly
        if (stripos($result['diagnosis'], 'HEALTHY') !== false || stripos($result['diagnosis'], 'healthy') !== false) {
            $result['diagnosis'] = 'Healthy';
            $result['severity'] = null;
            $result['confidence'] = 95.0;
        }

        // Generate AI-powered recommendation
        $recommendation = $this->openaiService->generateRecommendation(
            $result['diagnosis'],
            $result['species'] ?? 'livestock',
            $result['severity'] ?? 'medium',
            $result['diagnosis']
        );

        $issues = [];
        if ($result['diagnosis'] !== 'Healthy' && $result['symptoms']) {
            $symptomList = array_filter(explode(',', $result['symptoms']));
            foreach (array_slice($symptomList, 0, 3) as $idx => $symptom) {
                $issues[] = [
                    'type' => 'symptom',
                    'description' => trim($symptom),
                    'confidence' => $result['confidence'] - ($idx * 5),
                ];
            }
        }

        return [
            'diagnosis' => $result['diagnosis'],
            'description' => $result['symptoms'] ?: $result['diagnosis'] . ' detected in ' . $result['species'],
            'severity' => $result['severity'],
            'recommendation' => $recommendation,
            'detected_issues' => $issues,
            'confidence' => $result['confidence'],
        ];
    }

    /**
     * Simulation mode: randomly select a disease and generate analysis.
     */
    protected function runSimulationAnalysis(UploadedFile $image): array
    {
        $diseases = LivestockAnalysis::getCommonDiseases();
        $selectedDisease = $diseases[array_rand($diseases)];

        $confidence = rand(70, 99) + (rand(0, 99) / 100);

        $issues = [];
        if ($selectedDisease['name'] !== 'Healthy') {
            $issues[] = [
                'type' => 'simulated_symptom',
                'location' => 'general',
                'affected_area_percent' => rand(5, 40),
            ];
        }

        // Generate AI-powered recommendation for the selected disease
        $recommendation = $this->openaiService->generateRecommendation(
            $selectedDisease['name'],
            'various livestock',
            $selectedDisease['severity'] ?? 'medium',
            $selectedDisease['description']
        );

        return [
            'diagnosis' => $selectedDisease['name'],
            'description' => $selectedDisease['description'],
            'severity' => $selectedDisease['severity'],
            'recommendation' => $recommendation,
            'detected_issues' => $issues,
            'confidence' => $confidence,
        ];
    }
}
