@extends('layouts.MainLayout')

@section('title', 'Livestock Type Details - SmartShamba')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ $livestockType->name }}</h4>
                    <div>
                        <a href="{{ route('livestock-types.edit', $livestockType) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('livestock-types.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Name</label>
                                <p class="form-control-plaintext">{{ $livestockType->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Slug</label>
                                <p class="form-control-plaintext">{{ $livestockType->slug }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <p class="form-control-plaintext">{{ $livestockType->description ?: 'No description provided' }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tracking</label>
                        <p class="form-control-plaintext">
                            @if($livestockType->requires_individual_tracking)
                                <span class="badge bg-success">Requires Individual Tracking</span>
                            @else
                                <span class="badge bg-secondary">No Individual Tracking Required</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Associated Livestock -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Associated Livestock ({{ $livestockType->livestock->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($livestockType->livestock->count() > 0)
                        <div class="row">
                            @foreach($livestockType->livestock as $livestock)
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $livestock->name ?: 'Unnamed' }}</h6>
                                            <p class="card-text small text-muted">
                                                Tag: {{ $livestock->tag_number ?: 'N/A' }}<br>
                                                Status: <span class="badge bg-{{ $livestock->status === 'healthy' ? 'success' : 'warning' }}">{{ ucfirst($livestock->status) }}</span>
                                            </p>
                                            <a href="{{ route('livestock.show', $livestock) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No livestock of this type yet.</p>
                        <a href="{{ route('livestock.create') }}?type={{ $livestockType->id }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add First Livestock
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection