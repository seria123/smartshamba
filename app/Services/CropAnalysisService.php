<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CropAnalysisService
{
    public function analyzeCrop(string $imagePath): array
    {
        try {
            $response = Http::attach(
                'images',
                file_get_contents($imagePath),
                'crop.jpg'
            )->post(config('services.crop_api.url'));

            // ❗ STEP 1: Check HTTP success first
            if (!$response->successful()) {
                Log::warning('Crop API failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return $this->fallbackResult();
            }

            // ❗ STEP 2: Decode JSON ONCE
            $data = $response->json();

            // ❗ STEP 3: Validate structure
            if (!is_array($data)) {
                Log::warning('Crop API returned invalid JSON structure', [
                    'body' => $response->body(),
                ]);

                return $this->fallbackResult();
            }

            // ❗ STEP 4: Safe return
            return [
                'success' => true,
                'crop' => $data['crop'] ?? 'Unknown',
                'disease' => $data['disease'] ?? 'No disease detected',
                'confidence' => $data['confidence'] ?? 0,
                'raw' => $data,
            ];

        } catch (\Throwable $e) {
            Log::error('Crop analysis failed', [
                'message' => $e->getMessage(),
            ]);

            return $this->fallbackResult();
        }
    }

    private function fallbackResult(): array
    {
        return [
            'success' => false,
            'crop' => 'Unknown',
            'disease' => 'Unable to analyze crop',
            'confidence' => 0,
            'raw' => null,
        ];
    }
}