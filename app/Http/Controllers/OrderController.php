<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\Crop;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['farm', 'buyer', 'crop']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('buyer_id')) {
            $query->where('buyer_id', $request->buyer_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('order_date', [$request->start_date, $request->end_date]);
        }

        $orders = $query->orderBy('order_date', 'desc')->paginate(15);

        $stats = [
            'total_orders' => $query->count(),
            'total_value' => $query->sum('total_amount'),
            'pending_value' => $query->whereIn('status', ['pending', 'confirmed'])->sum('total_amount'),
        ];

        return view('orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order)
    {
        $order->load(['farm', 'buyer', 'crop']);

        return view('orders.show', compact('order'));
    }

    public function create()
    {
        $buyers = Buyer::where('is_active', true)->get();
        $crops = Crop::all();

        return view('orders.create', compact('buyers', 'crops'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'buyer_id' => 'required|exists:buyers,id',
            'crop_id' => 'nullable|exists:crops,id',
            'order_date' => 'required|date',
            'delivery_date' => 'nullable|date',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'nullable|string',
            'price_per_unit' => 'required|numeric|min:0',
            'delivery_address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['order_number'] = Order::generateOrderNumber();
        $validated['total_amount'] = $validated['quantity'] * $validated['price_per_unit'];

        Order::create($validated);

        return redirect()->route('orders.index')
            ->with('success', 'Order created successfully');
    }

    public function edit(Order $order)
    {
        $buyers = Buyer::where('is_active', true)->get();
        $crops = Crop::all();

        return view('orders.edit', compact('order', 'buyers', 'crops'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'buyer_id' => 'required|exists:buyers,id',
            'crop_id' => 'nullable|exists:crops,id',
            'order_date' => 'required|date',
            'delivery_date' => 'nullable|date',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'nullable|string',
            'price_per_unit' => 'required|numeric|min:0',
            'status' => 'nullable|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'payment_status' => 'nullable|in:unpaid,partial,paid',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_due_date' => 'nullable|date',
            'delivery_address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['total_amount'] = $validated['quantity'] * $validated['price_per_unit'];

        $order->update($validated);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order updated successfully');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Order deleted successfully');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Order status updated');
    }

    public function recordPayment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'method' => 'nullable|string',
        ]);

        $newPaid = $order->paid_amount + $validated['amount'];

        $order->update([
            'paid_amount' => $newPaid,
            'payment_status' => $newPaid >= $order->total_amount ? 'paid' : 'partial',
        ]);

        return redirect()->back()->with('success', 'Payment recorded');
    }
}
