<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use App\Models\CropSeason;
use App\Models\YieldEstimation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CropController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Crop::query()->with('seasons');

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('season_type')) {
            $query->where('season_type', $request->season_type);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('variety', 'like', "%{$request->search}%");
            });
        }

        $crops = $query->orderBy('name')->paginate(15);

        return response()->json($crops);
    }

    public function show(int $id): JsonResponse
    {
        $crop = Crop::with(['seasons', 'rotations.previousCrop', 'yieldEstimations'])->find($id);

        if (! $crop) {
            return response()->json(['message' => 'Crop not found'], 404);
        }

        return response()->json(['crop' => $crop]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:crops,name',
            'category' => 'required|string|in:vegetable,fruit,grain,legume,root,tubers,other',
            'description' => 'nullable|string',
            'variety' => 'nullable|string|max:255',
            'days_to_maturity' => 'nullable|integer|min:1',
            'average_yield_per_hectare' => 'nullable|numeric|min:0',
            'yield_unit' => 'nullable|string|max:50',
            'season_type' => 'required|string|in:short_rain,long_rain,all_season',
            'growth_stages' => 'nullable|array',
            'soil_requirements' => 'nullable|array',
            'water_requirements' => 'nullable|array',
            'pest_vulnerabilities' => 'nullable|array',
            'min_temperature' => 'nullable|numeric',
            'max_temperature' => 'nullable|numeric',
            'optimal_ph_min' => 'nullable|numeric|min:0|max:14',
            'optimal_ph_max' => 'nullable|numeric|min:0|max:14',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $crop = Crop::create($validator->validated());

        return response()->json([
            'message' => 'Crop created successfully',
            'crop' => $crop,
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $crop = Crop::find($id);

        if (! $crop) {
            return response()->json(['message' => 'Crop not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255|unique:crops,name,'.$id,
            'category' => 'sometimes|string|in:vegetable,fruit,grain,legume,root,tubers,other',
            'description' => 'nullable|string',
            'variety' => 'nullable|string|max:255',
            'days_to_maturity' => 'nullable|integer|min:1',
            'average_yield_per_hectare' => 'nullable|numeric|min:0',
            'yield_unit' => 'nullable|string|max:50',
            'season_type' => 'sometimes|string|in:short_rain,long_rain,all_season',
            'growth_stages' => 'nullable|array',
            'soil_requirements' => 'nullable|array',
            'water_requirements' => 'nullable|array',
            'pest_vulnerabilities' => 'nullable|array',
            'min_temperature' => 'nullable|numeric',
            'max_temperature' => 'nullable|numeric',
            'optimal_ph_min' => 'nullable|numeric|min:0|max:14',
            'optimal_ph_max' => 'nullable|numeric|min:0|max:14',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $crop->update($validator->validated());

        return response()->json([
            'message' => 'Crop updated successfully',
            'crop' => $crop->fresh(),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $crop = Crop::find($id);

        if (! $crop) {
            return response()->json(['message' => 'Crop not found'], 404);
        }

        $crop->delete();

        return response()->json(['message' => 'Crop deleted successfully']);
    }

    public function getCalendar(Request $request): JsonResponse
    {
        $query = CropSeason::query()->with('crop');

        if ($request->has('crop_id')) {
            $query->where('crop_id', $request->crop_id);
        }

        if ($request->has('season_period')) {
            $query->where('season_period', $request->season_period);
        }

        if ($request->has('is_optimal')) {
            $query->where('is_optimal', $request->boolean('is_optimal'));
        }

        $seasons = $query->orderBy('planting_start_date')->get();

        return response()->json([
            'calendar' => $seasons,
        ]);
    }

    public function addSeason(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'crop_id' => 'required|exists:crops,id',
            'name' => 'required|string|max:255',
            'season_period' => 'required|string|in:short_rain,long_rain,all_season',
            'planting_start_date' => 'required|date',
            'planting_end_date' => 'required|date|after:planting_start_date',
            'expected_harvest_start' => 'required|date',
            'expected_harvest_end' => 'required|date|after:expected_harvest_start',
            'notes' => 'nullable|string',
            'is_optimal' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $season = CropSeason::create($validator->validated());

        return response()->json([
            'message' => 'Crop season added successfully',
            'season' => $season,
        ], 201);
    }

    public function getRotations(Request $request, int $cropId): JsonResponse
    {
        $crop = Crop::find($cropId);

        if (! $crop) {
            return response()->json(['message' => 'Crop not found'], 404);
        }

        $rotations = $crop->rotations()
            ->with('previousCrop')
            ->orderBy('sequence_order')
            ->get();

        return response()->json([
            'rotations' => $rotations,
        ]);
    }

    public function suggestRotations(Request $request, int $cropId): JsonResponse
    {
        $crop = Crop::find($cropId);

        if (! $crop) {
            return response()->json(['message' => 'Crop not found'], 404);
        }

        $previousCropId = $request->get('previous_crop_id');

        if ($previousCropId) {
            $suggested = $crop->rotations()
                ->where('previous_crop_id', $previousCropId)
                ->with('previousCrop')
                ->first();

            return response()->json([
                'suggested_rotation' => $suggested,
            ]);
        }

        $suggestions = $crop->rotations()
            ->with('previousCrop')
            ->where('yield_benefit_percentage', '>', 0)
            ->orderByDesc('yield_benefit_percentage')
            ->limit(5)
            ->get();

        return response()->json([
            'suggestions' => $suggestions,
        ]);
    }

    public function addRotation(Request $request, int $cropId): JsonResponse
    {
        $crop = Crop::find($cropId);

        if (! $crop) {
            return response()->json(['message' => 'Crop not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'previous_crop_id' => 'required|exists:crops,id|different:crop_id',
            'sequence_order' => 'required|integer|min:1',
            'yield_benefit_percentage' => 'nullable|numeric|min:-50|max:100',
            'benefits' => 'nullable|string',
            'risks' => 'nullable|string',
            'recommendations' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $rotation = $crop->rotations()->create($validator->validated());

        return response()->json([
            'message' => 'Crop rotation suggestion added',
            'rotation' => $rotation,
        ], 201);
    }

    public function getYieldEstimations(Request $request): JsonResponse
    {
        $farmerId = $request->user()->farmer->id ?? $request->get('farmer_id');

        $query = YieldEstimation::query()
            ->with(['crop', 'field'])
            ->when($farmerId, fn ($q) => $q->where('farmer_id', $farmerId))
            ->when($request->crop_id, fn ($q) => $q->where('crop_id', $request->crop_id))
            ->when($request->season, fn ($q) => $q->where('season', $request->season))
            ->when($request->year, fn ($q) => $q->where('year', $request->year))
            ->when($request->status, fn ($q) => $q->where('status', $request->status));

        $estimations = $query->orderBy('year', 'desc')->paginate(15);

        return response()->json($estimations);
    }

    public function createYieldEstimation(Request $request): JsonResponse
    {
        $farmer = $request->user()->farmer;

        if (! $farmer) {
            return response()->json(['message' => 'Farmer profile not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'crop_id' => 'required|exists:crops,id',
            'field_id' => 'nullable|exists:fields,id',
            'hectares' => 'required|numeric|min:0.01',
            'season' => 'required|string|in:short_rain,long_rain,all_season',
            'year' => 'required|integer|min:2000|max:'.(date('Y') + 1),
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $crop = Crop::find($request->crop_id);
        $hectares = $request->hectares;
        $estimatedYield = $crop->average_yield_per_hectare ? ($crop->average_yield_per_hectare * $hectares) : null;

        $estimation = YieldEstimation::create([
            ...$validator->validated(),
            'farmer_id' => $farmer->id,
            'estimated_yield' => $estimatedYield,
            'yield_unit' => $crop->yield_unit,
            'status' => 'planned',
        ]);

        $estimation->calculateYieldPerHectare();
        $estimation->save();

        return response()->json([
            'message' => 'Yield estimation created',
            'estimation' => $estimation->load('crop'),
        ], 201);
    }

    public function updateYieldEstimation(Request $request, int $id): JsonResponse
    {
        $estimation = YieldEstimation::find($id);

        if (! $estimation) {
            return response()->json(['message' => 'Yield estimation not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'hectares' => 'sometimes|numeric|min:0.01',
            'estimated_yield' => 'nullable|numeric|min:0',
            'actual_yield' => 'nullable|numeric|min:0',
            'estimated_income' => 'nullable|numeric|min:0',
            'actual_income' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'sometimes|in:planned,in_progress,harvested,failed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $estimation->update($validator->validated());

        if ($estimation->estimated_yield && $estimation->hectares) {
            $estimation->yield_per_hectare = $estimation->estimated_yield / $estimation->hectares;
            $estimation->save();
        }

        return response()->json([
            'message' => 'Yield estimation updated',
            'estimation' => $estimation->fresh()->load('crop'),
        ]);
    }

    public function getCategories(): JsonResponse
    {
        $categories = [
            'vegetable' => 'Vegetables',
            'fruit' => 'Fruits',
            'grain' => 'Grains',
            'legume' => 'Legumes',
            'root' => 'Root Crops',
            'tubers' => 'Tubers',
            'other' => 'Other',
        ];

        return response()->json(['categories' => $categories]);
    }

    public function getSeasonTypes(): JsonResponse
    {
        $seasonTypes = [
            'short_rain' => 'Short Rain Season (Vuli)',
            'long_rain' => 'Long Rain Season (Masika)',
            'all_season' => 'All Season',
        ];

        return response()->json(['season_types' => $seasonTypes]);
    }
}
