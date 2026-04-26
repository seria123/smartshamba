@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Buyer Details</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('buyers.edit', $buyer) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i> Edit
                        </a>
                        <a href="{{ route('buyers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Contact Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150"><strong>Name</strong></td>
                                            <td>{{ $buyer->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Company</strong></td>
                                            <td>{{ $buyer->company_name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone</strong></td>
                                            <td>{{ $buyer->phone }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email</strong></td>
                                            <td>{{ $buyer->email ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Address</strong></td>
                                            <td>{{ $buyer->address ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Business Details</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150"><strong>Farm</strong></td>
                                            <td>{{ $buyer->farm?->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Type</strong></td>
                                            <td>{{ ucfirst($buyer->buyer_type) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Credit Limit</strong></td>
                                            <td>{{ $buyer->credit_limit ? number_format($buyer->credit_limit, 2) : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Rating</strong></td>
                                            <td>{{ $buyer->rating ? $buyer->rating . '/5' : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $buyer->is_active ? 'success' : 'secondary' }}">
                                                    {{ $buyer->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Statistics</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <h4 class="text-success">{{ number_format($stats['total_orders'], 2) }}</h4>
                                            <small class="text-muted">Total Orders</small>
                                        </div>
                                        <div class="col-4">
                                            <h4 class="text-warning">{{ number_format($stats['pending_payments'], 2) }}</h4>
                                            <small class="text-muted">Pending Payments</small>
                                        </div>
                                        <div class="col-4">
                                            <h4 class="text-info">{{ $buyer->revenues()->count() }}</h4>
                                            <small class="text-muted">Sales</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($buyer->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Notes</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $buyer->notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($buyer->orders && $buyer->orders->count() > 0)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Recent Orders</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Order Date</th>
                                                    <th>Order Number</th>
                                                    <th class="text-end">Amount</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($buyer->orders as $order)
                                                <tr>
                                                    <td>{{ $order->order_date?->format('M d, Y') ?? 'N/A' }}</td>
                                                    <td>{{ $order->order_number ?? 'N/A' }}</td>
                                                    <td class="text-end">{{ number_format($order->total_amount, 2) }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $order->status_color }}">
                                                            {{ ucfirst($order->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
