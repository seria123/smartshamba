<?php

namespace App\Services;

use App\Models\LivestockWoundAnalysis;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LivestockWoundAnalysisService
{
    protected ?string $apiKey;

    protected \App\Services\OpenAIService $openaiService;

    public function __construct(\App\Services\OpenAIService $openaiService)
    {
        $this->apiKey = config('services.openai.key') ?: config('services.openai.api_key') ?: env('OPENAI_API_KEY');
        $this->openaiService = $openaiService;
    }

    /**
     * Perform wound analysis on an uploaded image.
     */
    public function analyzeWound(UploadedFile $image, ?int $livestockId = null): LivestockWoundAnalysis
    {
        $path = $this->storeImage($image);

        $analysis = LivestockWoundAnalysis::create([
            'livestock_id' => $livestockId,
            'user_id' => auth()->id(),
            'image_path' => $path,
            'status' => 'pending',
        ]);

        $result = $this->performWoundAnalysis($image);

        $analysis->update([
            'wound_type' => $result['wound_type'],
            'severity' => $result['severity'],
            'description' => $result['description'],
            'treatment_plan' => $result['treatment_plan'],
            'urgency' => $result['urgency'],
            'estimated_healing_time' => $result['estimated_healing_time'],
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

        return $image->storeAs('livestock-wounds', $filename, 'public');
    }

    /**
     * Perform wound analysis using OpenAI Vision.
     */
    protected function performWoundAnalysis(UploadedFile $image): array
    {
        if (! empty($this->apiKey) && $this->apiKey !== 'your_openai_api_key_here') {
            return $this->runVisionWoundAnalysis($image);
        }

        Log::warning('LivestockWoundAnalysis: Using simulation mode - no OpenAI API key configured');

        return $this->runWoundSimulation();
    }

    /**
     * Use OpenAI Vision API to analyze livestock wound.
     */
    protected function runVisionWoundAnalysis(UploadedFile $image): array
    {
        try {
            $imageData = base64_encode(file_get_contents($image->getRealPath()));

            $prompt = <<<'PROMPT'
Analyze this livestock wound image carefully.

Identify:
1. Type of wound (laceration, abrasion, puncture, contusion, burn, infection/abscess, etc.)
2. Severity (minor, moderate, severe, critical)
3. Visible symptoms (bleeding, swelling, redness, discharge, inflammation, foreign objects, etc.)
4. Urgency level (immediate, urgent, routine, monitoring)
5. Estimated healing time (days to weeks)
6. Confidence percentage (0-100)

Provide structured response:
```
WOUND_TYPE: [wound type]
SEVERITY: [minor/moderate/severe/critical]
SYMPTOMS: [list visible symptoms]
URGENCY: [immediate/urgent/routine/monitoring]
HEALING_TIME: [estimated time]
CONFIDENCE: [0-100]
```

Then provide:
- Brief description of the wound condition
- Recommended treatment plan (step-by-step)
- Any immediate actions needed
- When to seek veterinary assistance

Only respond with this format, no additional commentary.
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
                    'max_tokens' => 500,
                    'temperature' => 0.3,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';

                return $this->parseWoundVisionResponse($content);
            }

            Log::error('OpenAI Vision Wound API error: '.$response->body());
        } catch (\Exception $e) {
            Log::error('OpenAI Vision Wound exception: '.$e->getMessage());
        }

        return $this->runWoundSimulation();
    }

    /**
     * Parse OpenAI Vision wound response.
     */
    protected function parseWoundVisionResponse(string $content): array
    {
        $lines = explode("\n", $content);
        $result = [
            'wound_type' => 'Unknown',
            'severity' => 'moderate',
            'symptoms' => '',
            'urgency' => 'routine',
            'estimated_healing_time' => '7-14 days',
            'confidence' => 75.0,
            'description' => '',
            'treatment_plan' => '',
        ];

        $parsingDescription = false;
        $parsingTreatment = false;
        $descriptionParts = [];
        $treatmentParts = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if (stripos($line, 'WOUND_TYPE:') === 0) {
                $result['wound_type'] = trim(substr($line, 11));
            } elseif (stripos($line, 'SEVERITY:') === 0) {
                $v = trim(substr($line, 9));
                $result['severity'] = $v;
            } elseif (stripos($line, 'SYMPTOMS:') === 0) {
                $result['symptoms'] = trim(substr($line, 9));
            } elseif (stripos($line, 'URGENCY:') === 0) {
                $v = trim(substr($line, 8));
                if (in_array($v, ['immediate', 'urgent', 'routine', 'monitoring'])) {
                    $result['urgency'] = $v;
                }
            } elseif (stripos($line, 'HEALING_TIME:') === 0) {
                $result['estimated_healing_time'] = trim(substr($line, 13));
            } elseif (stripos($line, 'CONFIDENCE:') === 0) {
                $c = floatval(trim(substr($line, 11)));
                $result['confidence'] = min(100, max(0, $c));
            } elseif (stripos($line, '---') !== false || stripos($line, '```') !== false) {
                continue;
            } elseif (! empty($line) && ! stripos($line, 'WOUND_TYPE:') && ! stripos($line, 'SEVERITY:')
                && ! stripos($line, 'SYMPTOMS:') && ! stripos($line, 'URGENCY:')
                && ! stripos($line, 'HEALING_TIME:') && ! stripos($line, 'CONFIDENCE:')) {
                // This is likely description or treatment text
                if (stripos($line, 'treatment') !== false || stripos($line, 'plan') !== false
                    || stripos($line, 'recommend') !== false || stripos($line, 'apply') !== false
                    || stripos($line, 'clean') !== false || $parsingTreatment) {
                    $parsingTreatment = true;
                    if (! empty($line)) {
                        $treatmentParts[] = $line;
                    }
                } else {
                    if (! $parsingTreatment) {
                        $descriptionParts[] = $line;
                    }
                }
            }
        }

        $result['description'] = implode(' ', $descriptionParts) ?: $result['symptoms'];
        $result['treatment_plan'] = implode('\n', $treatmentParts) ?: $this->generateDefaultTreatment($result['wound_type'], $result['severity']);

        // Build detected issues array
        $issues = [];
        if (! empty($result['symptoms'])) {
            $symptomList = array_filter(explode(',', $result['symptoms']));
            foreach (array_slice($symptomList, 0, 5) as $idx => $symptom) {
                $issues[] = [
                    'type' => 'symptom',
                    'description' => trim($symptom),
                    'severity' => $result['severity'],
                    'confidence' => max(50, $result['confidence'] - ($idx * 10)),
                ];
            }
        }
        $result['detected_issues'] = $issues;

        // Map urgency from AI output
        $result['urgency'] = $this->mapUrgencyLevel($result['urgency'], $result['severity']);

        return $result;
    }

    protected function mapUrgencyLevel(string $urgency, string $severity): string
    {
        $urgencyMap = [
            'immediate' => 'immediate',
            'urgent' => 'urgent',
            'routine' => 'routine',
            'monitoring' => 'monitoring',
        ];

        if (isset($urgencyMap[$urgency])) {
            return $urgencyMap[$urgency];
        }

        // Fallback based on severity
        $severityUrgency = [
            'critical' => 'immediate',
            'severe' => 'urgent',
            'moderate' => 'routine',
            'minor' => 'monitoring',
        ];

        return $severityUrgency[$severity] ?? 'routine';
    }

    protected function generateDefaultTreatment(string $woundType, string $severity): string
    {
        $treatments = [
            'laceration' => [
                'minor' => 'Clean wound with saline solution. Apply antiseptic. Monitor for infection.',
                'moderate' => 'Clean and debride wound. Apply antibiotic ointment. Consider sutures. Bandage properly.',
                'severe' => 'Immediate veterinary care needed. Clean and debride. Sutures likely required. Antibiotics recommended.',
                'critical' => 'URGENT VETERINARY CARE. Control bleeding. Clean if possible. Transport to vet immediately.',
            ],
            'abrasion' => [
                'minor' => 'Clean with saline. Apply antiseptic spray. Keep area clean and dry.',
                'moderate' => 'Clean thoroughly. Apply antibiotic ointment. Bandage to protect. Monitor for infection.',
                'severe' => 'Clean and debride. Apply antibiotic. Bandage. Consider systemic antibiotics.',
                'critical' => 'Veterinary care needed. Extensive cleaning and treatment required.',
            ],
            'puncture' => [
                'minor' => 'Clean thoroughly with saline. Apply antiseptic. Monitor closely for infection.',
                'moderate' => 'Deep cleaning required. May need to open puncture for drainage. Antibiotics recommended.',
                'severe' => 'Veterinary care needed. Risk of deep infection or tetanus.',
                'critical' => 'URGENT VETERINARY CARE. Risk of serious infection or internal damage.',
            ],
            'contusion' => [
                'minor' => 'Cold compress for 24 hours. Monitor for swelling. Rest animal.',
                'moderate' => 'Cold compress. Anti-inflammatory if available. Monitor closely.',
                'severe' => 'Veterinary assessment needed. May have internal damage.',
                'critical' => 'URGENT VETERINARY CARE. Possible internal injuries.',
            ],
            'burn' => [
                'minor' => 'Cool with clean water. Apply burn ointment. Keep clean and protected.',
                'moderate' => 'Cool burn. Apply antibiotic ointment. Bandage loosely. Monitor for infection.',
                'severe' => 'Veterinary care needed. Fluid therapy may be required.',
                'critical' => 'URGENT VETERINARY CARE. Severe burns can be life-threatening.',
            ],
            'infection' => [
                'minor' => 'Clean with antiseptic. Apply antibiotic ointment. Monitor closely.',
                'moderate' => 'Clean thoroughly. Systemic antibiotics needed. May need drainage.',
                'severe' => 'Veterinary care required. IV antibiotics likely needed.',
                'critical' => 'URGENT VETERINARY CARE. Sepsis risk - emergency treatment needed.',
            ],
            'abscess' => [
                'minor' => 'Clean area. Apply warm compress to promote drainage. Monitor.',
                'moderate' => 'May need lancing and drainage. Clean thoroughly. Antibiotics recommended.',
                'severe' => 'Veterinary care for drainage and antibiotics.',
                'critical' => 'URGENT VETERINARY CARE. Risk of systemic infection.',
            ],
        ];

        $woundLower = strtolower($woundType);
        foreach ($treatments as $key => $sevTreatments) {
            if (str_contains($woundLower, $key)) {
                return $sevTreatments[$severity] ?? $sevTreatments['moderate'];
            }
        }

        // Generic treatment based on severity
        $genericTreatments = [
            'minor' => 'Clean wound with saline. Apply antiseptic. Monitor for signs of infection.',
            'moderate' => 'Clean wound thoroughly. Apply antibiotic treatment. Bandage if needed. Monitor closely.',
            'severe' => 'Clean and treat wound. Antibiotics recommended. Veterinary consultation advised.',
            'critical' => 'URGENT VETERINARY CARE REQUIRED. Stabilize animal and transport immediately.',
        ];

        return $genericTreatments[$severity] ?? $genericTreatments['moderate'];
    }

    /**
     * Simulation mode for when OpenAI is unavailable.
     */
    protected function runWoundSimulation(): array
    {
        $woundTypes = [
            ['type' => 'Laceration', 'description' => 'Deep cut or tear in the skin', 'severity' => 'moderate', 'urgency' => 'routine'],
            ['type' => 'Abrasion', 'description' => 'Scrape or graze of the skin surface', 'severity' => 'minor', 'urgency' => 'monitoring'],
            ['type' => 'Puncture Wound', 'description' => 'Deep penetrating wound, risk of infection', 'severity' => 'moderate', 'urgency' => 'routine'],
            ['type' => 'Contusion', 'description' => 'Bruise or blunt force trauma', 'severity' => 'minor', 'urgency' => 'monitoring'],
            ['type' => 'Burn', 'description' => 'Thermal injury to skin and tissue', 'severity' => 'moderate', 'urgency' => 'routine'],
            ['type' => 'Abscess', 'description' => 'Localized infection with pus collection', 'severity' => 'moderate', 'urgency' => 'routine'],
            ['type' => 'Infected Wound', 'description' => 'Wound showing signs of bacterial infection', 'severity' => 'severe', 'urgency' => 'urgent'],
        ];

        $selected = $woundTypes[array_rand($woundTypes)];
        $severityLevels = ['minor', 'moderate', 'severe', 'critical'];
        $severityIndex = array_search($selected['severity'], $severityLevels);

        return [
            'wound_type' => $selected['type'],
            'severity' => $selected['severity'],
            'symptoms' => 'Swelling, redness, '.($selected['severity'] === 'severe' ? 'discharge, ' : '').'tenderness',
            'urgency' => $selected['urgency'],
            'estimated_healing_time' => $this->estimateHealingTime($selected['severity']),
            'confidence' => rand(65, 85) + (rand(0, 99) / 100),
            'description' => $selected['description'],
            'treatment_plan' => $this->generateDefaultTreatment($selected['type'], $selected['severity']),
            'detected_issues' => [
                [
                    'type' => 'wound',
                    'description' => $selected['description'],
                    'severity' => $selected['severity'],
                    'confidence' => rand(65, 85),
                ],
            ],
        ];
    }

    protected function estimateHealingTime(string $severity): string
    {
        $healingTimes = [
            'minor' => '3-7 days',
            'moderate' => '7-14 days',
            'severe' => '2-4 weeks',
            'critical' => '4+ weeks',
        ];

        return $healingTimes[$severity] ?? '7-14 days';
    }
}
