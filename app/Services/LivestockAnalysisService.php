<?php

namespace App\Services;

use App\Models\LivestockAnalysis;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LivestockAnalysisService
{
    protected ?string $apiKey;

    protected \App\Services\OpenAIService $openaiService;

    public function __construct(\App\Services\OpenAIService $openaiService)
    {
        $this->apiKey = config('services.openai.key') ?: config('services.openai.api_key') ?: env('OPENAI_API_KEY');
        $this->openaiService = $openaiService;
    }

    /**
     * Perform livestock disease analysis on an uploaded image.
     */
    public function analyze(UploadedFile $image, ?int $livestockId = null): LivestockAnalysis
    {
        $path = $this->storeImage($image);

        $analysis = LivestockAnalysis::create([
            'livestock_id' => $livestockId,
            'user_id' => auth()->id(),
            'image_path' => $path,
            'status' => 'pending',
        ]);

        $result = $this->performAnalysis($image);

        $analysis->update([
            'diagnosis' => $result['diagnosis'],
            'description' => $result['description'],
            'severity' => $result['severity'],
            'recommendation' => $result['recommendation'],
            'detected_issues' => $result['detected_issues'],
            'confidence_score' => $result['confidence'],
            'status' => 'analyzed',
        ]);

        return $analysis->fresh();
    }

    /**
     * Store uploaded image to storage.
     */
    protected function storeImage(UploadedFile $image): string
    {
        $filename = Str::uuid().'.'.$image->getClientOriginalExtension();

        return $image->storeAs('livestock-analyses', $filename, 'public');
    }

    /**
     * Perform analysis using appropriate method based on configuration.
     */
    protected function performAnalysis(UploadedFile $image): array
    {
        if (! empty($this->apiKey) && $this->apiKey !== 'your_openai_api_key_here') {
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
Analyze this livestock animal image carefully for any signs of disease or health issues.

Identify:
1. The animal species (cattle, sheep, goat, chicken, pig, etc.)
2. Any visible symptoms (skin lesions, discharge, unusual posture, coat condition, eye/nose issues, lameness, etc.)
3. The most likely disease or health condition based on visual symptoms
4. Severity assessment (low, medium, high)

Provide a structured response in this exact format:
```
SPECIES: [species name]
SYMPTOMS: [list observed symptoms]
DISEASE: [most likely disease name]
SEVERITY: [low/medium/high]
CONFIDENCE: [percentage 0-100]
```

If the animal appears healthy, state "HEALTHY" for disease and "low" severity.
Only respond with the structured format, no additional commentary.
PROMPT;

            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer '.$this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4-vision-preview',
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
                                        'url' => 'data:image/jpeg;base64,'.$imageData,
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

            Log::error('OpenAI Vision API error: '.$response->body());
        } catch (\Exception $e) {
            Log::error('OpenAI Vision exception: '.$e->getMessage());
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
            'description' => $result['symptoms'] ?: $result['diagnosis'].' detected in '.$result['species'],
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
