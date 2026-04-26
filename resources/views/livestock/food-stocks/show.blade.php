@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Food Stock Details</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('food-stocks.edit', $foodStock) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i> Edit
                        </a>
                        <a href="{{ route('food-stocks.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Stock Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150"><strong>Feed Type</strong></td>
                                            <td>{{ $foodStock->feedType->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Quantity</strong></td>
                                            <td>{{ $foodStock->quantity }} {{ $foodStock->unit }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Unit Cost</strong></td>
                                            <td>{{ $foodStock->unit_cost ? '$' . number_format($foodStock->unit_cost, 2) : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Value</strong></td>
                                            <td>{{ $foodStock->unit_cost ? '$' . number_format($foodStock->quantity * $foodStock->unit_cost, 2) : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Expiry Date</strong></td>
                                            <td>{{ $foodStock->expiry_date ? $foodStock->expiry_date->format('d/m/Y') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status</strong></td>
                                            <td>
                                                @if($foodStock->isExpired())
                                                    <span class="badge bg-danger">Expired</span>
                                                @elseif($foodStock->isLowStock())
                                                    <span class="badge bg-warning">Low Stock</span>
                                                @else
                                                    <span class="badge bg-success">OK</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Additional Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150"><strong>Supplier</strong></td>
                                            <td>{{ $foodStock->supplier->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Added By</strong></td>
                                            <td>{{ $foodStock->user?->name ?? 'System' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created At</strong></td>
                                            <td>{{ $foodStock->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last Updated</strong></td>
                                            <td>{{ $foodStock->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
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
