<?php

use Illuminate\Support\Facades\Route;
use App\Services\PlantIdService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

Route::get('/test-plant-id', function () {
    // Check config
    $key = config('services.plant_id.key');
    $url = config('services.plant_id.url');
    
    Log::info('Plant ID Config', [
        'key' => $key ? substr($key, 0, 10) . '...' : 'EMPTY',
        'url' => $url
    ]);
    
    // Check .env
    $envKey = env('PLANT_ID_KEY');
    $envUrl = env('PLANT_ID_URL');
    
    Log::info('Plant ID Env', [
        'key' => $envKey ? substr($envKey, 0, 10) . '...' : 'EMPTY',
        'url' => $envUrl
    ]);
    
    return response()->json([
        'config' => [
            'key_present' => !empty($key),
            'key_length' => strlen($key ?? ''),
            'url' => $url
        ],
        'env' => [
            'key_present' => !empty($envKey),
            'key_length' => strlen($envKey ?? ''),
            'url' => $envUrl
        ]
    ]);
});