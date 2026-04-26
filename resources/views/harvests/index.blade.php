@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Harvest & Yield Tracking</h4>
                    <div class="btn-group">
                        <a href="{{ route('harvests.dashboard') }}" class="btn btn-info text-white">
                            <i class="fas fa-chart-line me-2"></i> Dashboard
                        </a>
                        <a href="{{ route('harvests.statistics') }}" class="btn btn-secondary">
                            <i class="fas fa-chart-bar me-2"></i> Statistics
                        </a>
                        <a href="{{ route('harvests.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> Record Harvest
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" 
                                    placeholder="Search by batch or crop..." 
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="crop_id" class="form-select">
                                    <option value="">All Crops</option>
                                    @foreach(\App\Models\Crop::all() as $crop)
                                    <option value="{{ $crop->id }}" {{ request('crop_id') == $crop->id ? 'selected' : '' }}>
                                        {{ $crop->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="quality_grade" class="form-select">
                                    <option value="">All Grades</option>
                                    <option value="grade_a" {{ request('quality_grade') == 'grade_a' ? 'selected' : '' }}>Grade A</option>
                                    <option value="grade_b" {{ request('quality_grade') == 'grade_b' ? 'selected' : '' }}>Grade B</option>
                                    <option value="grade_c" {{ request('quality_grade') == 'grade_c' ? 'selected' : '' }}>Grade C</option>
                                    <option value="reject" {{ request('quality_grade') == 'reject' ? 'selected' : '' }}>Reject</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary w-100">Filter</button>
                            </div>
                        </div>
                    </form>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ number_format($stats['total_harvested'], 1) }}</h3>
                                    <small>Total Harvested</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ number_format($stats['total_losses'], 1) }}</h3>
                                    <small>Total Losses</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $stats['grade_a_count'] }}</h3>
                                    <small>Grade A</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $stats['grade_b_count'] }}</h3>
                                    <small>Grade B</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $stats['grade_c_count'] }}</h3>
                                    <small>Grade C</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Batch</th>
                                    <th>Crop</th>
                                    <th>Field</th>
                                    <th>Quantity</th>
                                    <th>Grade</th>
                                    <th>Losses</th>
                                    <th>Storage</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($harvests as $harvest)
                                <tr>
                                    <td>{{ $harvest->harvest_date->format('M d, Y') }}</td>
                                    <td>{{ $harvest->harvest_batch ?? '-' }}</td>
                                    <td>{{ $harvest->crop?->name ?? 'N/A' }}</td>
                                    <td>{{ $harvest->field?->name ?? 'N/A' }}</td>
                                    <td>{{ number_format($harvest->quantity_harvested, 2) }} {{ $harvest->unit }}</td>
                                    <td>
                                        <span class="badge bg-{{ $harvest->quality_color }}">
                                            {{ $harvest->quality_label }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($harvest->loss_quantity > 0)
                                        <span class="text-danger">{{ number_format($harvest->loss_quantity, 2) }} ({{ $harvest->loss_percentage }}%)</span>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>{{ $harvest->storage_location_label }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('harvests.show', $harvest) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('harvests.edit', $harvest) }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        No harvest records found. <a href="{{ route('harvests.create') }}">Record your first harvest</a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $harvests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
