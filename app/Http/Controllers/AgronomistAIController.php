<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AgronomistAIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AgronomistAIController extends Controller
{
    protected $aiService;

    public function __construct(AgronomistAIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Analyze crop health.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function analyzeHealth(Request $request): JsonResponse
    {
        $request->validate([
            'image_path' => 'sometimes|string',
            'field_id' => 'sometimes|integer|exists:fields,id',
        ]);

        $result = $this->aiService->analyzeCropHealth(
            $request->input('image_path'),
            $request->input('field_id')
        );

        return response()->json($result);
    }

    /**
     * Suggest fertilizer for a crop in a field.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function suggestFertilizer(Request $request): JsonResponse
    {
        $request->validate([
            'crop_id' => 'required|integer|exists:crops,id',
            'field_id' => 'required|integer|exists:fields,id',
        ]);

        $result = $this->aiService->suggestFertilizer(
            $request->input('crop_id'),
            $request->input('field_id')
        );

        return response()->json($result);
    }

    /**
     * Predict harvest time for a crop cycle.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function predictHarvest(Request $request): JsonResponse
    {
        $request->validate([
            'crop_cycle_id' => 'required|integer|exists:crop_cycles,id',
        ]);

        $result = $this->aiService->predictHarvestTime(
            $request->input('crop_cycle_id')
        );

        return response()->json($result);
    }

    /**
     * Detect risks for a field.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function detectRisks(Request $request): JsonResponse
    {
        $request->validate([
            'field_id' => 'required|integer|exists:fields,id',
        ]);

        $result = $this->aiService->detectRisks(
            $request->input('field_id')
        );

        return response()->json($result);
    }
}
