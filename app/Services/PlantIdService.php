<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Log;

class PlantIdService
{
    protected string $apiKey;
    protected string $apiUrl = 'https://plant.id/api/v3/health_assessment';

    public function __construct()
    {
        $this->apiKey = config('services.plant_id.key');
    }

    public function analyzePlantHealth(UploadedFile $image): array
{
    if (empty($this->apiKey)) {
        Log::warning('Plant.id API key not configured');
        return $this->fallback();
    }

    try {
        // 🔥 Compress + resize image BEFORE encoding
        $imageResized = Image::make($image->getRealPath())
            ->resize(1024, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('jpg', 70);

        // Convert to base64 AFTER compression
        $base64 = base64_encode($imageResized);

        Log::info('Plant.id request prepared', [
            'original_size' => $image->getSize(),
            'mime_type' => $image->getMimeType(),
            'compressed_size' => strlen($base64),
        ]);

        // 🚀 API request
        $response = Http::timeout(60)
            ->withHeaders([
                'Api-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post($this->apiUrl, [
    'images' => [$base64],
    'similar_images' => true, // 🔥 IMPORTANT
    'health' => [
        'disease' => true,
    ],
]);


        // ❌ Handle API failure clearly
        if (!$response->successful()) {
            Log::error('Plant.id API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $this->fallback();
        }

        Log::info('Plant.id response received', [
            'status' => $response->status(),
        ]);

        return $this->format($response->json());

    } catch (\Throwable $e) {
        Log::error('Plant.id exception: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
        ]);

        return $this->fallback();
    }
}

    protected function format(array $data): array
{
    // 🔍 Try ALL known Plant.id response paths
    $disease = data_get($data, 'result.disease.suggestions.0')
        ?? data_get($data, 'result.health_assessment.diseases.0')
        ?? data_get($data, 'health_assessment.diseases.0')
        ?? data_get($data, 'result.diseases.0')
        ?? data_get($data, 'result.is_plant.health_assessment.diseases.0')
        ?? null;

    // 🧨 Debug safety (remove later)
    if (!$disease) {
        Log::warning('No disease found in API response', $data);
        return $this->fallback();
    }

    $name = $disease['name']
        ?? $disease['species']
        ?? $disease['disease']['name']
        ?? 'Unknown condition';

    $description = $this->getDescriptionFromEntity($disease);

    $severityRaw = $this->mapSeverityFromEntity($disease);

    $confidence = $disease['probability']
        ?? $disease['score']
        ?? 0;

    return [
        'disease_name' => $name,
        'description' => $description,
        'severity' => $this->mapSeverity($severityRaw),
        'confidence' => $confidence * 100,
        'detected_issues' => [['type' => 'plant_disease']],
    ];
}

    protected function getDescriptionFromEntity(array $disease): string
    {
        $name = strtolower($disease['name'] ?? '');
        $map = ['drepanopeziza'=>'A fungal disease affecting cherry and plum trees',
            'powdery mildew'=>'A fungal disease appearing as white powdery spots',
            'downy mildew'=>'A fungal-like disease causing yellow spots',
            'rust'=>'Fungal diseases causing pustules on leaves and stems',
            'blight'=>'Rapid browning or withering of plant tissues',
            'mildew'=>'Fungal growth on plant surfaces',
            'aphids'=>'Small sap-sucking insects',
            'mite'=>'Tiny arachnids that suck plant juices',
            'caterpillar'=>'Larval insects that feed on leaves',
            'leaf spot'=>'Fungal or bacterial infections',
            'anthracnose'=>'Fungal disease causing dark lesions',
            'bacteria'=>'Bacterial infections',
            'fungi'=>'Fungal infections',
            'virus'=>'Viral infections',
            'abiotic'=>'Non-living factors'];
        foreach ($map as $k=>$v) {
            if (str_contains($name,$k)) return $v;
        }
        return 'Plant health issue detected.';
    }

    protected function mapSeverityFromEntity(array $disease): string
    {
        $name = strtolower($disease['name'] ?? '');
        $p = $disease['probability'] ?? 0;
        $high = ['blight','wilt','rot','rust','anthracnose','drepanopeziza'];
        $med = ['mildew','spot','canker','aphids','mite','caterpillar','leaf spot'];
        foreach ($high as $k) if (str_contains($name,$k)) return 'high';
        foreach ($med as $k) if (str_contains($name,$k)) return 'medium';
        if ($p >= 0.7) return 'high';
        if ($p >= 0.4) return 'medium';
        return 'low';
    }

    protected function mapSeverity($severity): string
    {
        if (!$severity) return 'medium';
        return match (strtolower((string)$severity)) {
            'low'=>'low','medium'=>'medium','high'=>'high','severe'=>'high',
            default=>'medium'
        };
    }

    protected function fallback(): array
    {
        return ['disease_name'=>'Analysis unavailable','description'=>'',
            'severity'=>null,'confidence'=>0,'detected_issues'=>[]];
    }
}
