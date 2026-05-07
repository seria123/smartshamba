<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\Farm;
use App\Models\Livestock;
use App\Models\Revenue;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $query = Revenue::with(['farm', 'crop', 'buyer']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('invoice_number', 'like', "%{$request->search}%")
                    ->orWhereHas('buyer', function ($bq) use ($request) {
                        $bq->where('name', 'like', "%{$request->search}%");
                    });
            });
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('farm_id')) {
            $query->where('farm_id', $request->farm_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('sale_date', [$request->start_date, $request->end_date]);
        }

        $revenues = $query->orderBy('sale_date', 'desc')->paginate(15);

        $stats = [
            'total_revenue' => $query->sum('amount'),
            'paid' => $query->where('payment_status', 'paid')->sum('amount'),
            'pending' => $query->where('payment_status', 'pending')->sum('amount'),
        ];

        return view('revenues.index', compact('revenues', 'stats'));
    }

    public function show(Revenue $revenue)
    {
        $revenue->load(['farm', 'crop', 'buyer']);

        return view('revenues.show', compact('revenue'));
    }

    public function create()
    {
        $farms = Farm::all();
        $buyers = Buyer::all();
        $livestock = Livestock::where('status', '!=', 'sold')->get();

        return view('revenues.create', compact('farms', 'buyers', 'livestock'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'crop_id' => 'nullable|exists:crops,id',
            'livestock_id' => 'nullable|exists:livestock,id',
            'buyer_id' => 'nullable|exists:buyers,id',
            'amount' => 'required|numeric|min:0',
            'sale_date' => 'required|date',
            'quantity_sold' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string',
            'price_per_unit' => 'nullable|numeric|min:0',
            'payment_status' => 'nullable|in:pending,partial,paid',
            'payment_date' => 'nullable|date',
            'payment_method' => 'nullable|string',
            'invoice_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Revenue::create($validated);

        return redirect()->route('revenues.index')
            ->with('success', 'Revenue recorded successfully');
    }

    public function edit(Revenue $revenue)
    {
        $farms = Farm::all();
        $buyers = Buyer::all();
        $livestock = Livestock::where('status', '!=', 'sold')->get();

        return view('revenues.edit', compact('revenue', 'farms', 'buyers', 'livestock'));
    }

    public function update(Request $request, Revenue $revenue)
    {
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'crop_id' => 'nullable|exists:crops,id',
            'livestock_id' => 'nullable|exists:livestock,id',
            'buyer_id' => 'nullable|exists:buyers,id',
            'amount' => 'required|numeric|min:0',
            'sale_date' => 'required|date',
            'quantity_sold' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string',
            'price_per_unit' => 'nullable|numeric|min:0',
            'payment_status' => 'nullable|in:pending,partial,paid',
            'payment_date' => 'nullable|date',
            'payment_method' => 'nullable|string',
            'invoice_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $revenue->update($validated);

        return redirect()->route('revenues.show', $revenue)
            ->with('success', 'Revenue updated successfully');
    }

    public function destroy(Revenue $revenue)
    {
        $revenue->delete();

        return redirect()->route('revenues.index')
            ->with('success', 'Revenue deleted successfully');
    }

    public function markAsPaid(Request $request, Revenue $revenue)
    {
        $revenue->update([
            'payment_status' => 'paid',
            'payment_date' => now(),
            'payment_method' => $request->payment_method ?? 'cash',
        ]);

        return redirect()->back()->with('success', 'Payment marked as paid');
    }
}
