@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Revenue Details</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('revenues.edit', $revenue) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i> Edit
                        </a>
                        <a href="{{ route('revenues.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="200"><strong>Sale Date</strong></td>
                                    <td>{{ $revenue->sale_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Farm</strong></td>
                                    <td>{{ $revenue->farm?->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Crop</strong></td>
                                    <td>{{ $revenue->crop?->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Buyer</strong></td>
                                    <td>{{ $revenue->buyer?->name ?? 'Direct Sale' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Quantity</strong></td>
                                    <td>{{ $revenue->quantity_sold ?? 'N/A' }} {{ $revenue->unit ?? '' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="200"><strong>Amount</strong></td>
                                    <td class="text-success fw-bold fs-5">{{ number_format($revenue->amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Price per Unit</strong></td>
                                    <td>{{ $revenue->price_per_unit ? number_format($revenue->price_per_unit, 2) : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Status</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $revenue->status_color }}">
                                            {{ ucfirst($revenue->payment_status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Date</strong></td>
                                    <td>{{ $revenue->payment_date ? $revenue->payment_date->format('M d, Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created</strong></td>
                                    <td>{{ $revenue->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Additional Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1 text-muted">Payment Method</p>
                                            <p>{{ $revenue->payment_method ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1 text-muted">Invoice Number</p>
                                            <p>{{ $revenue->invoice_number ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($revenue->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Notes</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $revenue->notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Profit Calculcation</h6>
                                </div>
                                <div class="card-body">
                                    @php $profit = $revenue->calculateProfit(); @endphp
                                    <p class="mb-0">
                                        <strong>Estimated Profit:</strong>
                                        <span class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($profit, 2) }}
                                        </span>
                                    </p>
                                    <small class="text-muted">
                                        Based on expenses recorded for this farm up to the sale date
                                    </small>
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
