<?php

use Illuminate\Support\Facades\Route;
use App\Services\PlantIdService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

Route::get('/test-plant-id-service', function () {
    // Test with actual image if available, otherwise test service initialization
    try {
        $service = app(PlantIdService::class);
        
        // Check if service has API key configured
        $hasApiKey = !empty($service->apiKey);
        
        Log::info('Plant ID Service Test', [
            'has_api_key' => $hasApiKey,
            'api_key_length' => $hasApiKey ? strlen($service->apiKey) : 0,
            'api_url' => $service->apiUrl ?? 'NOT SET'
        ]);
        
        if (!$hasApiKey) {
            return response()->json([
                'success' => false,
                'error' => 'API key not configured in service',
                'service_config' => [
                    'has_api_key' => $hasApiKey,
                    'api_url' => $service->apiUrl ?? 'NOT SET'
                ]
            ], 500);
        }
        
        // Try to make a simple HTTP test to the API (without image)
        $response = Http::timeout(10)
            ->withHeaders([
                'Api-Key' => $service->apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post($service->apiUrl, [
                'images' => [''], // Empty image to test auth
                'plant_language' => 'en',
                'similar_images' => false,
            ]);
        
        if ($response->successful()) {
            return response()->json([
                'success' => true,
                'message' => 'API connection successful',
                'api_url' => $service->apiUrl
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'API connection failed',
                'status' => $response->status(),
                'body' => $response->body(),
                'api_url' => $service->apiUrl
            ], 500);
        }
    } catch (\Exception $e) {
        Log::error('Plant ID service test failed', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});