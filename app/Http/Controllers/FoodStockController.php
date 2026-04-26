<?php

namespace App\Http\Controllers;

use App\Models\FeedType;
use App\Models\FoodStock;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FoodStockController extends Controller
{
    /**
     * Display a listing of food stocks.
     */
    public function index(Request $request): View
    {
        $query = FoodStock::with(['feedType', 'supplier', 'user']);

        if ($request->filled('feed_type_id')) {
            $query->where('feed_type_id', $request->feed_type_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'low') {
                $query->whereHas('feedType', function ($q) {
                    $q->whereColumn('min_threshold', '>', 'food_stocks.quantity');
                });
            } elseif ($request->status === 'expired') {
                $query->whereNotNull('expiry_date')
                    ->whereDate('expiry_date', '<', now());
            } elseif ($request->status === 'expiring_soon') {
                $query->whereNotNull('expiry_date')
                    ->whereDate('expiry_date', '<=', now()->addDays(30));
            }
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $stocks = $query->orderBy('expiry_date', 'asc')->paginate(20);
        $feedTypes = FeedType::all();
        $suppliers = Supplier::all();

        return view('livestock.food-stocks.index', compact('stocks', 'feedTypes', 'suppliers'));
    }

    /**
     * Show the form for creating a new food stock.
     */
    public function create(): View
    {
        $feedTypes = FeedType::all();
        $suppliers = Supplier::all();

        return view('livestock.food-stocks.create', compact('feedTypes', 'suppliers'));
    }

    /**
     * Store a newly created food stock in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'feed_type_id' => 'required|exists:feed_types,id',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:20',
            'unit_cost' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date|after_or_equal:today',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['is_active'] = true;

        FoodStock::create($validated);

        return redirect()->route('food-stocks.index')
            ->with('success', 'Food stock added successfully.');
    }

    /**
     * Display the specified food stock.
     */
    public function show(FoodStock $foodStock): View
    {
        $foodStock->load(['feedType', 'supplier', 'user']);

        return view('livestock.food-stocks.show', compact('foodStock'));
    }

    /**
     * Show the form for editing the specified food stock.
     */
    public function edit(FoodStock $foodStock): View
    {
        $feedTypes = FeedType::all();
        $suppliers = Supplier::all();

        return view('livestock.food-stocks.edit', compact('foodStock', 'feedTypes', 'suppliers'));
    }

    /**
     * Update the specified food stock in storage.
     */
    public function update(Request $request, FoodStock $foodStock): RedirectResponse
    {
        $validated = $request->validate([
            'feed_type_id' => 'required|exists:feed_types,id',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:20',
            'unit_cost' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date|after_or_equal:today',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'notes' => 'nullable|string',
        ]);

        $foodStock->update($validated);

        return redirect()->route('food-stocks.index')
            ->with('success', 'Food stock updated successfully.');
    }

    /**
     * Remove the specified food stock from storage.
     */
    public function destroy(FoodStock $foodStock): RedirectResponse
    {
        $foodStock->delete();

        return redirect()->route('food-stocks.index')
            ->with('success', 'Food stock deleted successfully.');
    }

    /**
     * Adjust stock quantity (add/subtract).
     */
    public function adjust(Request $request, FoodStock $foodStock): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'operation' => 'required|in:add,subtract,set',
            'notes' => 'nullable|string',
        ]);

        $oldQuantity = $foodStock->quantity;

        switch ($validated['operation']) {
            case 'add':
                $foodStock->quantity += $validated['quantity'];
                break;
            case 'subtract':
                $foodStock->quantity -= $validated['quantity'];
                break;
            case 'set':
                $foodStock->quantity = $validated['quantity'];
                break;
        }

        if ($foodStock->quantity < 0) {
            return redirect()->back()->with('error', 'Insufficient stock for this operation.');
        }

        $foodStock->save();

        return redirect()->back()->with('success', sprintf(
            'Stock adjusted from %s %s to %s %s.',
            $oldQuantity,
            $foodStock->unit,
            $foodStock->quantity,
            $foodStock->unit
        ));
    }
}
