@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Harvest Details</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Harvest Date</span>
                            <span class="fw-semibold">{{ $harvest->harvest_date->format('M d, Y') }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Batch Number</span>
                            <span>{{ $harvest->harvest_batch ?? 'N/A' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Crop</span>
                            <span>{{ $harvest->crop?->name ?? 'N/A' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Field</span>
                            <span>{{ $harvest->field?->name ?? 'N/A' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Quantity</span>
                            <span class="fw-semibold text-success">{{ number_format($harvest->quantity_harvested, 2) }} {{ $harvest->unit }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Quality Grade</span>
                            <span class="badge bg-{{ $harvest->quality_color }}">{{ $harvest->quality_label }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Quality %</span>
                            <span>{{ $harvest->quality_percentage }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Losses & Storage</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Loss Quantity</span>
                            <span class="text-danger">{{ number_format($harvest->loss_quantity, 2) }} {{ $harvest->unit }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Loss Reason</span>
                            <span>{{ $harvest->loss_reason ?? 'N/A' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Loss %</span>
                            <span>{{ $harvest->loss_percentage }}%</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Net Quantity</span>
                            <span class="fw-semibold">{{ number_format($harvest->net_quantity, 2) }} {{ $harvest->unit }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <a href="{{ route('harvests.edit', $harvest) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i> Edit Harvest
                </a>
                <form action="{{ route('harvests.destroy', $harvest) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fas fa-trash me-2"></i> Delete Record
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Storage Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Storage Location</p>
                            <h5>{{ $harvest->storage_location_label }}</h5>
                        </div>
                        @if($harvest->moisture_content)
                        <div class="col-md-6">
                            <p class="mb-1 text-muted">Moisture Content</p>
                            <h5>{{ $harvest->moisture_content }}%</h5>
                        </div>
                        @endif
                    </div>
                    @if($harvest->storage_details)
                    <div class="mt-3">
                        <p class="mb-1 text-muted">Storage Details</p>
                        <p class="mb-0">{{ $harvest->storage_details }}</p>
                    </div>
                    @endif
                </div>
            </div>

            @if($harvest->notes)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Notes</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $harvest->notes }}</p>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <h3 class="text-success mb-1">{{ number_format($harvest->quantity_harvested, 1) }}</h3>
                                <small class="text-muted">Gross Harvest ({{ $harvest->unit }})</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <h3 class="text-danger mb-1">{{ number_format($harvest->loss_quantity, 1) }}</h3>
                                <small class="text-muted">Losses ({{ $harvest->unit }})</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <h3 class="text-primary mb-1">{{ number_format($harvest->net_quantity, 1) }}</h3>
                                <small class="text-muted">Net Yield ({{ $harvest->unit }})</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
