<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeedType;
use App\Models\FoodStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FoodStockController extends Controller
{
    public function index(Request $request)
    {
        $query = FoodStock::with(['feedType', 'supplier'])
            ->where('user_id', Auth::id())
            ->where('is_active', true);

        if ($request->has('feed_type_id')) {
            $query->where('feed_type_id', $request->feed_type_id);
        }

        $stocks = $query->orderBy('created_at', 'desc')->paginate(15);

        $stocks->getCollection()->transform(function ($stock) {
            $stock['is_low_stock'] = $stock->isLowStock();
            $stock['is_expired'] = $stock->isExpired();

            return $stock;
        });

        return response()->json([
            'success' => true,
            'data' => $stocks,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'feed_type_id' => 'required|exists:feed_types,id',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'unit_cost' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $validated['user_id'] = Auth::id();

        $stock = FoodStock::create($validated);

        // Return JSON for API requests, redirect for web form submissions
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Food stock created successfully',
                'data' => $stock->load('feedType'),
            ], 201);
        }

        return redirect()->route('food-stocks.index')
            ->with('success', 'Food stock created successfully');
    }

    public function show(FoodStock $foodStock)
    {
        $this->authorizeOwnership($foodStock);

        return response()->json([
            'success' => true,
            'data' => $foodStock->load('feedType'),
        ]);
    }

    public function update(Request $request, FoodStock $foodStock)
    {
        $this->authorizeOwnership($foodStock);

        $validated = $request->validate([
            'feed_type_id' => 'sometimes|exists:feed_types,id',
            'quantity' => 'sometimes|numeric|min:0',
            'unit' => 'sometimes|string|max:50',
            'unit_cost' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $foodStock->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Food stock updated successfully',
            'data' => $foodStock->fresh('feedType'),
        ]);
    }

    public function destroy(FoodStock $foodStock)
    {
        $this->authorizeOwnership($foodStock);

        $foodStock->delete();

        return response()->json([
            'success' => true,
            'message' => 'Food stock deleted successfully',
        ]);
    }

    public function alerts()
    {
        $userId = Auth::id();
        $feedTypes = FeedType::with(['foodStocks' => function ($query) use ($userId) {
            $query->where('user_id', $userId)->where('is_active', true);
        }])->get();

        $alerts = [];
        foreach ($feedTypes as $feedType) {
            $totalQty = $feedType->foodStocks->sum('quantity');
            if ($totalQty < $feedType->min_threshold) {
                $alerts[] = [
                    'feed_type' => $feedType->name,
                    'current_quantity' => $totalQty,
                    'min_threshold' => $feedType->min_threshold,
                    'unit' => $feedType->default_unit,
                    'shortage' => $feedType->min_threshold - $totalQty,
                ];
            }
        }

        $expired = FoodStock::where('user_id', $userId)
            ->where('is_active', true)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now())
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'low_stock_alerts' => $alerts,
                'total_alerts' => count($alerts),
                'expired_stocks' => $expired,
            ],
        ]);
    }

    public function summary()
    {
        $userId = Auth::id();
        $feedTypes = FeedType::with(['foodStocks' => function ($query) use ($userId) {
            $query->where('user_id', $userId)->where('is_active', true);
        }])->get();

        $summary = $feedTypes->map(function ($feedType) {
            $totalQty = $feedType->foodStocks->sum('quantity');

            return [
                'feed_type' => $feedType->name,
                'total_quantity' => $totalQty,
                'unit' => $feedType->default_unit,
                'min_threshold' => $feedType->min_threshold,
                'is_low_stock' => $totalQty < $feedType->min_threshold,
                'locations_count' => $feedType->foodStocks->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    protected function authorizeOwnership(FoodStock $foodStock): void
    {
        if ($foodStock->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }
}
