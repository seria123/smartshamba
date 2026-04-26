@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-cloud"></i> Weather Data</h1>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label>Farm</label>
                            <select name="farm_id" class="form-control">
                                <option value="">All Farms</option>
                                @foreach($farms as $farm)
                                    <option value="{{ $farm->id }}" {{ request('farm_id') == $farm->id ? 'selected' : '' }}>{{ $farm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Weather Condition</label>
                            <select name="condition" class="form-control">
                                <option value="">All Conditions</option>
                                <option value="clear" {{ request('condition') == 'clear' ? 'selected' : '' }}>Clear</option>
                                <option value="cloudy" {{ request('condition') == 'cloudy' ? 'selected' : '' }}>Cloudy</option>
                                <option value="rainy" {{ request('condition') == 'rainy' ? 'selected' : '' }}>Rainy</option>
                                <option value="partly_cloudy" {{ request('condition') == 'partly_cloudy' ? 'selected' : '' }}>Partly Cloudy</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>From Date</label>
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label>To Date</label>
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">Filter</button>
                            <a href="{{ route('weather.index') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Weather Data Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date/Time</th>
                                    <th>Farm</th>
                                    <th>Temperature</th>
                                    <th>Humidity</th>
                                    <th>Conditions</th>
                                    <th>Wind</th>
                                    <th>Precipitation</th>
                                    <th>UV Index</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($weatherData as $weather)
                                    <tr>
                                        <td>{{ $weather->recorded_at->format('M d, H:i') }}</td>
                                        <td>{{ $weather->farm->name }}</td>
                                        <td>{{ $weather->temperature ? $weather->temperature . '°C' : 'N/A' }}</td>
                                        <td>{{ $weather->humidity ? $weather->humidity . '%' : 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ ucfirst($weather->weather_condition ?? 'Unknown') }}</span>
                                        </td>
                                        <td>{{ $weather->wind_speed ? $weather->wind_speed . ' m/s' : 'N/A' }}</td>
                                        <td>{{ $weather->precipitation ? $weather->precipitation . ' mm' : '0 mm' }}</td>
                                        <td>{{ $weather->uv_index ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No weather data found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $weatherData->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
