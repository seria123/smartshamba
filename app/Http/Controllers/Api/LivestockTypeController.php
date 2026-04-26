<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Livestock;
use App\Models\LivestockType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LivestockTypeController extends Controller
{
    public function index()
    {
        $types = LivestockType::withCount([
            'livestock as total_count' => function ($query) {
                $query->whereIn('status', [Livestock::STATUS_HEALTHY, Livestock::STATUS_SICK]);
            },
            'livestock as healthy_count' => function ($query) {
                $query->where('status', Livestock::STATUS_HEALTHY);
            },
            'livestock as sick_count' => function ($query) {
                $query->where('status', Livestock::STATUS_SICK);
            },
        ])->get();

        return response()->json([
            'success' => true,
            'data' => $types,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:livestock_types,name',
            'description' => 'nullable|string',
            'requires_individual_tracking' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $type = LivestockType::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Livestock type created successfully',
            'data' => $type,
        ], 201);
    }

    public function show(LivestockType $livestockType)
    {
        $livestockType->loadCount([
            'livestock as total_count' => function ($query) {
                $query->whereIn('status', [Livestock::STATUS_HEALTHY, Livestock::STATUS_SICK]);
            },
        ]);

        return response()->json([
            'success' => true,
            'data' => $livestockType,
        ]);
    }

    public function update(Request $request, LivestockType $livestockType)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:livestock_types,name,'.$livestockType->id,
            'description' => 'nullable|string',
            'requires_individual_tracking' => 'boolean',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $livestockType->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Livestock type updated successfully',
            'data' => $livestockType,
        ]);
    }

    public function destroy(LivestockType $livestockType)
    {
        if ($livestockType->livestock()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete livestock type with existing livestock',
            ], 422);
        }

        $livestockType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Livestock type deleted successfully',
        ]);
    }
}
