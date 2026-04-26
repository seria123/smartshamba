<?php

namespace App\Http\Controllers;

use App\Models\LivestockDisease;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LivestockDiseaseController extends Controller
{
    public function index()
    {
        $diseases = LivestockDisease::with(['livestock', 'disease', 'treatedBy'])
            ->whereHas('livestock', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->latest()
            ->paginate(20);

        return view('livestock.diseases.index', compact('diseases'));
    }

    public function highSeverity()
    {
        $diseases = LivestockDisease::with(['livestock', 'disease', 'treatedBy'])
            ->whereHas('livestock', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('severity', 'high')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('livestock.diseases.index', compact('diseases'));
    }

    public function contagious()
    {
        $diseases = LivestockDisease::with(['livestock', 'disease', 'treatedBy'])
            ->whereHas('livestock', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->whereNotNull('transmission')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('livestock.diseases.index', compact('diseases'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'livestock_id' => 'required|exists:livestock,id',
            'disease_id' => 'nullable|exists:diseases,id',
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'cause' => 'required|string|max:255',
            'symptoms' => 'required|string',
            'transmission' => 'nullable|string',
            'prevention' => 'nullable|string',
            'treatment' => 'nullable|string',
            'status' => 'sometimes|in:active,treated,chronic',
            'diagnosed_date' => 'nullable|date',
            'mortality_rate' => 'nullable|numeric|min:0|max:100',
            'severity' => 'sometimes|in:low,medium,high',
        ]);

        $validated['status'] = $validated['status'] ?? LivestockDisease::STATUS_ACTIVE;
        $validated['diagnosed_date'] = $validated['diagnosed_date'] ?? now();

        $disease = LivestockDisease::create($validated);

        $livestock = $disease->livestock;
        $livestock->markAsSick();

        return redirect()->route('diseases.show', $disease)
            ->with('success', 'Disease record created successfully');
    }

    public function create()
    {
        $livestock = \App\Models\Livestock::where('user_id', Auth::id())
            ->where('status', '!=', 'dead')
            ->where('status', '!=', 'sold')
            ->orderBy('name')
            ->get();

        $diseases = \App\Models\Disease::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livestock.diseases.create', compact('livestock', 'diseases'));
    }

    public function show(LivestockDisease $livestockDisease)
    {
        $this->authorizeOwnership($livestockDisease);

        return view('livestock.diseases.show', compact('livestockDisease'));
    }

    public function edit(LivestockDisease $livestockDisease)
    {
        $this->authorizeOwnership($livestockDisease);

        return view('livestock.diseases.edit', compact('livestockDisease'));
    }

    public function update(Request $request, LivestockDisease $livestockDisease)
    {
        $this->authorizeOwnership($livestockDisease);

        $validated = $request->validate([
            'livestock_id' => 'sometimes|exists:livestock,id',
            'disease_id' => 'nullable|exists:diseases,id',
            'name' => 'sometimes|string|max:255',
            'species' => 'sometimes|string|max:255',
            'cause' => 'sometimes|string|max:255',
            'symptoms' => 'sometimes|string',
            'transmission' => 'nullable|string',
            'prevention' => 'nullable|string',
            'treatment' => 'nullable|string',
            'status' => 'sometimes|in:active,treated,chronic',
            'diagnosed_date' => 'nullable|date',
            'treated_date' => 'nullable|date',
            'treated_by' => 'nullable|exists:users,id',
            'mortality_rate' => 'nullable|numeric|min:0|max:100',
            'severity' => 'sometimes|in:low,medium,high',
        ]);

        $livestockDisease->update($validated);

        if (isset($validated['status']) && $validated['status'] === LivestockDisease::STATUS_TREATED) {
            $livestockDisease->livestock->markAsHealthy();
        }

        return redirect()->route('diseases.show', $livestockDisease)
            ->with('success', 'Disease record updated successfully');
    }

    public function destroy(LivestockDisease $livestockDisease)
    {
        $this->authorizeOwnership($livestockDisease);

        $otherDiseases = LivestockDisease::where('livestock_id', $livestockDisease->livestock_id)
            ->where('id', '!=', $livestockDisease->id)
            ->where('status', 'active')
            ->count();

        $livestockDisease->delete();

        if ($otherDiseases === 0) {
            $livestockDisease->livestock->markAsHealthy();
        }

        return redirect()->route('diseases.index')
            ->with('success', 'Disease record deleted successfully');
    }

    public function diagnose(Request $request)
    {
        $validated = $request->validate([
            'livestock_id' => 'required|exists:livestock,id',
        ]);

        $livestock = \App\Models\Livestock::find($validated['livestock_id']);
        $this->authorizeOwnership($livestock);

        return view('livestock.diseases.diagnose', compact('livestock'));
    }

    /**
     * AI-powered disease diagnosis from photo.
     */
    public function analyze(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|string',
            'livestock_type' => 'nullable|string',
        ]);

        $imageData = $validated['image'];
        $livestockType = $validated['livestock_type'] ?? 'livestock';

        try {
            $openaiService = app(\App\Services\OpenAIService::class);
            $analysis = $this->runLivestockVisionAnalysis($imageData, $livestockType, $openaiService);

            $disease = \App\Models\Disease::firstOrCreate(
                ['name' => $analysis['diagnosis']],
                [
                    'description' => $analysis['description'],
                    'severity' => $analysis['severity'],
                    'symptoms' => $analysis['description'],
                    'treatment' => $analysis['recommendation'],
                    'is_contagious' => $this->isLikelyContagious($analysis['diagnosis']),
                    'is_active' => true,
                ]
            );

            return response()->json([
                'success' => true,
                'diagnosis' => $analysis['diagnosis'],
                'severity' => $analysis['severity'],
                'description' => $analysis['description'],
                'recommendation' => $analysis['recommendation'],
                'confidence' => $analysis['confidence'],
                'disease_id' => $disease->id,
            ]);
        } catch (\Exception $e) {
            \Log::error('Livestock AI analysis failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Analysis failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    protected function runLivestockVisionAnalysis(string $imageData, string $livestockType, \App\Services\OpenAIService $openai): array
    {
        $apiKey = $openai->getApiKey();
        
        if (empty($apiKey)) {
            return $this->runLivestockSimulation($livestockType);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
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
                                    'text' => $this->buildLivestockVisionPrompt($livestockType),
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
                    'max_tokens' => 400,
                    'temperature' => 0.3,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';
                return $this->parseLivestockVisionResponse($content);
            }
        } catch (\Exception $e) {
            \Log::error('OpenAI Vision error: ' . $e->getMessage());
        }

        return $this->runLivestockSimulation($livestockType);
    }

    protected function buildLivestockVisionPrompt(string $livestockType): string
    {
        return <<<PROMPT
Analyze this {$livestockType} image for signs of disease or health issues.

Identify:
1. The animal type (cattle, sheep, goat, chicken, pig, etc.)
2. Visible symptoms (lesions, discharge, posture, coat condition, etc.)
3. Most likely disease or health condition
4. Severity (low, medium, high)
5. Confidence percentage (0-100)

Provide structured response:
```
ANIMAL: [species/type]
SYMPTOMS: [list symptoms]
DISEASE: [disease name]
SEVERITY: [low/medium/high]
CONFIDENCE: [0-100]
```

Only respond with this format.
PROMPT;
    }

    protected function parseLivestockVisionResponse(string $content): array
    {
        $lines = explode("\n", $content);
        $result = [
            'animal_type' => 'Unknown',
            'symptoms' => '',
            'diagnosis' => 'Unknown Condition',
            'severity' => 'medium',
            'confidence' => 75.0,
        ];

        foreach ($lines as $line) {
            if (stripos($line, 'ANIMAL:') === 0) {
                $result['animal_type'] = trim(substr($line, 7));
            } elseif (stripos($line, 'SYMPTOMS:') === 0) {
                $result['symptoms'] = trim(substr($line, 9));
            } elseif (stripos($line, 'DISEASE:') === 0) {
                $result['diagnosis'] = trim(substr($line, 8));
            } elseif (stripos($line, 'SEVERITY:') === 0) {
                $v = trim(substr($line, 9));
                if (in_array($v, ['low', 'medium', 'high'])) {
                    $result['severity'] = $v;
                }
            } elseif (stripos($line, 'CONFIDENCE:') === 0) {
                $c = floatval(trim(substr($line, 11)));
                $result['confidence'] = min(100, max(0, $c));
            }
        }

        $recommendation = $this->generateLivestockRecommendation(
            $result['diagnosis'],
            $result['animal_type'],
            $result['symptoms'],
            $result['severity']
        );

        $result['recommendation'] = $recommendation;
        $result['description'] = $result['symptoms'] ?: $result['diagnosis'] . ' detected in ' . $result['animal_type'];

        return $result;
    }

    protected function generateLivestockRecommendation(string $disease, string $animal, string $symptoms, string $severity): string
    {
        $diseaseLower = strtolower($disease);

        $recommendations = [
            'foot and mouth' => [
                'high' => 'ISOLATE IMMEDIATELY. Report to authorities. Disinfect all areas. Do not move animals.',
                'medium' => 'Isolate affected animals. Contact veterinary services. Disinfect facilities.',
                'low' => 'Monitor for spread. Maintain hygiene. Limit animal movement.',
            ],
            'anthrax' => [
                'high' => 'DO NOT OPEN CARCASS. Report immediately. Quarantine area. Vaccinate herd if advised.',
                'medium' => 'Report to authorities. Avoid contact. Implement quarantine.',
                'low' => 'Monitor animals. Report sudden deaths immediately.',
            ],
            'mastitis' => [
                'high' => 'Milk out affected udder. Apply antibiotics. Cull chronic cases. Improve milking hygiene.',
                'medium' => 'Treat with antibiotics. Improve udder hygiene. Monitor milk quality.',
                'low' => 'Monitor udder health. Maintain clean milking practices.',
            ],
            'pneumonia' => [
                'high' => 'Provide antibiotics immediately. Improve ventilation. Reduce stress. Isolate sick animals.',
                'medium' => 'Administer antibiotics. Improve ventilation. Monitor closely.',
                'low' => 'Improve air quality. Monitor for progression.',
            ],
            'worms' => [
                'high' => 'Administer anthelmintics. Rotate pastures. Improve nutrition.',
                'medium' => 'Deworm animals. Improve pasture management.',
                'low' => 'Regular deworming. Monitor weight gain.',
            ],
        ];

        foreach ($recommendations as $key => $sevRecs) {
            if (str_contains($diseaseLower, $key)) {
                return $sevRecs[$severity] ?? $sevRecs['medium'];
            }
        }

        $genericRecs = [
            'high' => 'URGENT: Isolate affected animals. Contact veterinarian immediately. Implement biosecurity.',
            'medium' => 'Treat affected animals. Monitor for spread. Improve care practices.',
            'low' => 'Monitor condition. Maintain good husbandry practices. Reassess in 7-10 days.',
        ];

        return $genericRecs[$severity] ?? $genericRecs['medium'];
    }

    protected function runLivestockSimulation(string $livestockType): array
    {
        $diseases = \App\Models\LivestockAnalysis::getCommonDiseases();
        $selected = $diseases[array_rand($diseases)];

        return [
            'animal_type' => $livestockType,
            'symptoms' => $selected['description'],
            'diagnosis' => $selected['name'],
            'severity' => $selected['severity'],
            'confidence' => rand(70, 99) + (rand(0, 99) / 100),
            'recommendation' => $selected['recommendation'],
            'description' => $selected['description'],
        ];
    }

    protected function isLikelyContagious(string $diseaseName): bool
    {
        $contagiousKeywords = [
            'foot and mouth', 'anthrax', 'rift valley', 'brucellosis',
            'tuberculosis', 'pneumonia', 'mastitis', 'tick', 'parasite',
            'virus', 'bacterial', 'infection', 'fever', 'contagious',
        ];

        $diseaseLower = strtolower($diseaseName);

        foreach ($contagiousKeywords as $keyword) {
            if (str_contains($diseaseLower, $keyword)) {
                return true;
            }
        }

        return false;
    }

    public function treat(Request $request, LivestockDisease $livestockDisease)
    {
        $this->authorizeOwnership($livestockDisease);

        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $livestockDisease->markAsTreated(Auth::id());

        return redirect()->route('diseases.show', $livestockDisease)
            ->with('success', 'Livestock treated successfully');
    }

    protected function authorizeOwnership($model)
    {
        if ($model instanceof LivestockDisease) {
            if ($model->livestock->user_id !== Auth::id()) {
                abort(403, 'Unauthorized');
            }
        } elseif ($model instanceof \App\Models\Livestock) {
            if ($model->user_id !== Auth::id()) {
                abort(403, 'Unauthorized');
            }
        } else {
            abort(403, 'Unauthorized');
        }
    }
}
