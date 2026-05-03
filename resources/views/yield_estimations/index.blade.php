@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Yield Analysis</h4>
                    <div class="btn-group">
                        <a href="{{ route('yield_estimations.dashboard') }}" class="btn btn-info text-white">
                            <i class="fas fa-chart-line me-2"></i> Dashboard
                        </a>
                        <a href="{{ route('yield_estimations.statistics') }}" class="btn btn-secondary">
                            <i class="fas fa-chart-bar me-2"></i> Statistics
                        </a>
                        <a href="{{ route('yield_estimations.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> New Estimation
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" 
                                    placeholder="Search by crop, season..." 
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="crop_id" class="form-select">
                                    <option value="">All Crops</option>
                                    @foreach($crops as $crop)
                                    <option value="{{ $crop->id }}" {{ request('crop_id') == $crop->id ? 'selected' : '' }}>
                                        {{ $crop->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="season" class="form-select">
                                    <option value="">All Seasons</option>
                                    <option value="short_rain" {{ request('season') == 'short_rain' ? 'selected' : '' }}>Short Rain</option>
                                    <option value="long_rain" {{ request('season') == 'long_rain' ? 'selected' : '' }}>Long Rain</option>
                                    <option value="all_season" {{ request('season') == 'all_season' ? 'selected' : '' }}>All Season</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>Planned</option>
                                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="harvested" {{ request('status') == 'harvested' ? 'selected' : '' }}>Harvested</option>
                                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary w-100">Filter</button>
                            </div>
                        </div>
                    </form>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $stats['total_estimations'] }}</h3>
                                    <small>Total Estimations</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $stats['planned'] }}</h3>
                                    <small>Planned</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $stats['in_progress'] }}</h3>
                                    <small>In Progress</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $stats['harvested'] }}</h3>
                                    <small>Harvested</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $stats['failed'] }}</h3>
                                    <small>Failed</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Crop</th>
                                    <th>Field</th>
                                    <th>Season</th>
                                    <th>Year</th>
                                    <th>Hectares</th>
                                    <th>Est. Yield</th>
                                    <th>Actual Yield</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($query as $estimation)
                                <tr>
                                    <td>{{ $estimation->crop?->name ?? 'N/A' }}</td>
                                    <td>{{ $estimation->field?->name ?? '-' }}</td>
                                    <td>{{ ucwords(str_replace('_', ' ', $estimation->season)) }}</td>
                                    <td>{{ $estimation->year }}</td>
                                    <td>{{ number_format($estimation->hectares, 2) }}</td>
                                    <td>
                                        {{ number_format($estimation->estimated_yield, 2) }}
                                        {{ $estimation->yield_unit }}
                                        @if($estimation->yield_per_hectare)
                                        <br><small class="text-muted">{{ number_format($estimation->yield_per_hectare, 2) }}/ha</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($estimation->actual_yield)
                                        {{ number_format($estimation->actual_yield, 2) }} {{ $estimation->yield_unit }}
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $estimation->status == 'planned' ? 'primary' : 
                                            ($estimation->status == 'in_progress' ? 'warning' : 
                                            ($estimation->status == 'harvested' ? 'success' : 'danger'))
                                        }}">
                                            {{ ucwords(str_replace('_', ' ', $estimation->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('yield_estimations.show', $estimation) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('yield_estimations.edit', $estimation) }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        No yield estimations found. <a href="{{ route('yield_estimations.create') }}">Create your first estimation</a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $query->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
