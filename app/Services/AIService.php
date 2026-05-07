<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AIService
{
    public function diagnose(array $symptoms, ?string $diseaseName = null)
    {
        $symptomText = implode(', ', $symptoms);

        $prompt = "
        A livestock animal shows these symptoms: $symptomText.

        ".($diseaseName ? "The suspected disease is $diseaseName." : '').'

        Give:
        1. Likely disease
        2. Treatment
        3. Prevention

        Keep it simple for a farmer in Kenya.
        ';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.config('services.openai.key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/responses', [
            'model' => 'gpt-4.1-mini',
            'input' => $prompt,
        ]);

        return $response->json();
    }
}
