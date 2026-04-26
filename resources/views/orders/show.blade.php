@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Order Details</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('orders.edit', $order) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i> Edit Order
                        </a>
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="200"><strong>Order Number</strong></td>
                                    <td>{{ $order->order_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Order Date</strong></td>
                                    <td>{{ $order->order_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Delivery Date</strong></td>
                                    <td>{{ $order->delivery_date ? $order->delivery_date->format('M d, Y') : 'Not set' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Buyer</strong></td>
                                    <td>{{ $order->buyer?->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Crop</strong></td>
                                    <td>{{ $order->crop?->name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="200"><strong>Quantity</strong></td>
                                    <td>{{ number_format($order->quantity, 2) }} {{ $order->unit }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Price per Unit</strong></td>
                                    <td>{{ number_format($order->price_per_unit, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Amount</strong></td>
                                    <td class="text-success fw-bold fs-5">{{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $order->status_color }}">{{ ucfirst($order->status) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Payment</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $order->payment_status_color }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Payment Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Paid Amount</strong></td>
                                            <td class="text-success">{{ number_format($order->paid_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Outstanding</strong></td>
                                            <td class="text-danger">{{ number_format($order->getOutstandingAmount(), 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Payment Due Date</strong></td>
                                            <td>{{ $order->payment_due_date ? $order->payment_due_date->format('M d, Y') : 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @if($order->delivery_address)
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Delivery Address</h6>
                                </div>
                                <div class="card-body">
                                    <p>{{ $order->delivery_address }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if($order->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Notes</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $order->notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Actions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="btn-group">
                                        <form action="{{ route('orders.status', $order) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="btn btn-outline-info" {{ $order->status === 'confirmed' ? 'disabled' : '' }}>
                                                Confirm Order
                                            </button>
                                        </form>
                                        <form action="{{ route('orders.status', $order) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="processing">
                                            <button type="submit" class="btn btn-outline-primary" {{ $order->status === 'processing' || $order->status === 'delivered' ? 'disabled' : '' }}>
                                                Start Processing
                                            </button>
                                        </form>
                                        <form action="{{ route('orders.status', $order) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="shipped">
                                            <button type="submit" class="btn btn-outline-warning" {{ $order->status === 'shipped' || $order->status === 'delivered' ? 'disabled' : '' }}>
                                                Mark Shipped
                                            </button>
                                        </form>
                                        <form action="{{ route('orders.status', $order) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="delivered">
                                            <button type="submit" class="btn btn-outline-success" {{ $order->status === 'delivered' ? 'disabled' : '' }}>
                                                Mark Delivered
                                            </button>
                                        </form>
                                    </div>

                                    @if($order->payment_status !== 'paid')
                                    <form action="{{ route('orders.payment', $order) }}" method="POST" class="mt-3">
                                        @csrf
                                        <div class="row g-2 align-items-center">
                                            <div class="col-auto">
                                                <input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount" required>
                                            </div>
                                            <div class="col-auto">
                                                <input type="text" name="method" class="form-control" placeholder="Payment method">
                                            </div>
                                            <div class="col-auto">
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-dollar-sign me-2"></i> Record Payment
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
