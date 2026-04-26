@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Harvest Dashboard</h4>
                    <div class="btn-group">
                        <form method="GET" class="d-flex gap-2">
                            <select name="month" class="form-select">
                                @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ Carbon\Carbon::createFromDate($year, $m, 1)->format('F') }}
                                </option>
                                @endfor
                            </select>
                            <select name="year" class="form-select">
                                @for($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                            <button type="submit" class="btn btn-secondary">Filter</button>
                        </form>
                        <a href="{{ route('harvests.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i> All Records
                        </a>
                        <a href="{{ route('harvests.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> New Harvest
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ number_format($stats['total_quantity'], 1) }}</h3>
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
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ number_format($stats['net_quantity'], 1) }}</h3>
                                    <small>Net Yield</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ number_format($stats['grade_a_percentage'], 1) }}%</h3>
                                    <small>Grade A Rate</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($stats['by_crop']->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Harvest by Crop</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Crop</th>
                                            <th class="text-end">Quantity</th>
                                            <th class="text-end">Losses</th>
                                            <th class="text-end">Net</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stats['by_crop'] as $cropId => $data)
                                        <tr>
                                            <td>{{ $data['name'] }}</td>
                                            <td class="text-end">{{ number_format($data['quantity'], 1) }}</td>
                                            <td class="text-end text-danger">{{ number_format($data['losses'], 1) }}</td>
                                            <td class="text-end fw-bold text-success">{{ number_format($data['quantity'] - $data['losses'], 1) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <h5 class="mb-3">Recent Harvests - {{ Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</h5>
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
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($harvests as $harvest)
                                <tr>
                                    <td>{{ $harvest->harvest_date->format('M d') }}</td>
                                    <td>{{ $harvest->harvest_batch ?? '-' }}</td>
                                    <td>{{ $harvest->crop?->name ?? 'N/A' }}</td>
                                    <td>{{ $harvest->field?->name ?? 'N/A' }}</td>
                                    <td>{{ number_format($harvest->quantity_harvested, 1) }} {{ $harvest->unit }}</td>
                                    <td>
                                        <span class="badge bg-{{ $harvest->quality_color }}">
                                            {{ $harvest->quality_label }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($harvest->loss_quantity > 0)
                                        <span class="text-danger">{{ number_format($harvest->loss_quantity, 1) }}</span>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('harvests.show', $harvest) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        No harvests recorded for this month
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
