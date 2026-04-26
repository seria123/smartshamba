@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Harvest Statistics</h4>
                    <a href="{{ route('harvests.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Back to Records
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-secondary w-100">Update Statistics</button>
                            </div>
                        </div>
                    </form>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $stats['total_harvests'] }}</h3>
                                    <small>Total Harvests</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ number_format($stats['total_quantity'], 1) }}</h3>
                                    <small>Total Quantity</small>
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
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ number_format($stats['average_quality'], 1) }}%</h3>
                                    <small>Avg Quality</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($stats['by_crop']->count() > 0)
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">By Crop</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Crop</th>
                                                    <th class="text-center">Harvests</th>
                                                    <th class="text-end">Quantity</th>
                                                    <th class="text-end">Losses</th>
                                                    <th class="text-end">Loss %</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($stats['by_crop'] as $crop)
                                                <tr>
                                                    <td>{{ $crop['name'] }}</td>
                                                    <td class="text-center">{{ $crop['total_harvests'] }}</td>
                                                    <td class="text-end">{{ number_format($crop['total_quantity'], 1) }}</td>
                                                    <td class="text-end text-danger">{{ number_format($crop['total_losses'], 1) }}</td>
                                                    <td class="text-end">
                                                        @if($crop['total_quantity'] > 0)
                                                        {{ number_format(($crop['total_losses'] / $crop['total_quantity']) * 100, 1) }}%
                                                        @else
                                                        0%
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">By Field</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Field</th>
                                                    <th class="text-center">Harvests</th>
                                                    <th class="text-end">Quantity</th>
                                                    <th class="text-end">Losses</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($stats['by_field'] as $field)
                                                <tr>
                                                    <td>{{ $field['name'] }}</td>
                                                    <td class="text-center">{{ $field['total_harvests'] }}</td>
                                                    <td class="text-end">{{ number_format($field['total_quantity'], 1) }}</td>
                                                    <td class="text-end text-danger">{{ number_format($field['total_losses'], 1) }}</td>
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

                    @if($stats['by_month']->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Monthly Trend</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th class="text-end">Total Quantity</th>
                                            <th class="text-end">Total Losses</th>
                                            <th class="text-end">Net Yield</th>
                                            <th class="text-end">Loss %</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stats['by_month'] as $monthData)
                                        <tr>
                                            <td>{{ $monthData['month'] }}</td>
                                            <td class="text-end">{{ number_format($monthData['total_quantity'], 1) }}</td>
                                            <td class="text-end text-danger">{{ number_format($monthData['total_losses'], 1) }}</td>
                                            <td class="text-end fw-bold text-success">
                                                {{ number_format($monthData['total_quantity'] - $monthData['total_losses'], 1) }}
                                            </td>
                                            <td class="text-end">
                                                @if($monthData['total_quantity'] > 0)
                                                {{ number_format(($monthData['total_losses'] / $monthData['total_quantity']) * 100, 1) }}%
                                                @else
                                                0%
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
