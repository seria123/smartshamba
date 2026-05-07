<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    protected string $apiKey;

    protected string $baseUrl = 'https://api.tomorrow.io/v4/weather';

    public function __construct()
    {
        $this->apiKey = config('services.tomorrow.key');
    }

    public function getWeather(string $location)
    {
        try {
            $response = Http::get("{$this->baseUrl}/realtime", [
                'location' => $location,
                'apikey' => $this->apiKey,
            ]);

            if ($response->failed()) {
                Log::error('Weather API failed', [
                    'response' => $response->body(),
                ]);

                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Weather API exception', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
