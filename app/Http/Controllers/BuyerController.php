<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use Illuminate\Http\Request;

class BuyerController extends Controller
{
    public function index(Request $request)
    {
        $query = Buyer::with('farm');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('company_name', 'like', "%{$request->search}%")
                    ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('buyer_type')) {
            $query->where('buyer_type', $request->buyer_type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $buyers = $query->orderBy('name')->paginate(15);

        return view('buyers.index', compact('buyers'));
    }

    public function show(Buyer $buyer)
    {
        $buyer->load(['farm', 'orders' => function ($q) {
            $q->orderBy('order_date', 'desc')->limit(10);
        }]);

        $stats = [
            'total_orders' => $buyer->getTotalOrders(),
            'pending_payments' => $buyer->getPendingPayments(),
        ];

        return view('buyers.show', compact('buyer', 'stats'));
    }

    public function create()
    {
        return view('buyers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'name' => 'required|string|max:100',
            'company_name' => 'nullable|string|max:100',
            'email' => 'nullable|email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'buyer_type' => 'required|in:individual,retailer,wholesaler,processor,exporter',
            'credit_limit' => 'nullable|numeric|min:0',
            'rating' => 'nullable|numeric|min:0|max:5',
            'notes' => 'nullable|string',
        ]);

        Buyer::create($validated);

        return redirect()->route('buyers.index')
            ->with('success', 'Buyer added successfully');
    }

    public function edit(Buyer $buyer)
    {
        return view('buyers.edit', compact('buyer'));
    }

    public function update(Request $request, Buyer $buyer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'company_name' => 'nullable|string|max:100',
            'email' => 'nullable|email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'buyer_type' => 'required|in:individual,retailer,wholesaler,processor,exporter',
            'credit_limit' => 'nullable|numeric|min:0',
            'rating' => 'nullable|numeric|min:0|max:5',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $buyer->update($validated);

        return redirect()->route('buyers.show', $buyer)
            ->with('success', 'Buyer updated successfully');
    }

    public function destroy(Buyer $buyer)
    {
        $buyer->delete();

        return redirect()->route('buyers.index')
            ->with('success', 'Buyer deleted successfully');
    }
}
