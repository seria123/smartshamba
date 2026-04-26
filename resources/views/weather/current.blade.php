@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-cloud"></i> Weather - {{ $farm->name }}</h1>
                <a href="{{ route('weather.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

            @if($currentWeather)
            <!-- Current Weather -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h2 class="display-4">{{ $currentWeather->temperature }}°C</h2>
                                    <p class="lead">{{ ucfirst($currentWeather->weather_condition ?? 'Unknown') }}</p>
                                    <p>Feels like: {{ $currentWeather->feels_like }}°C</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-6 mb-2">
                                            <i class="fas fa-tint"></i> Humidity: {{ $currentWeather->humidity }}%
                                        </div>
                                        <div class="col-6 mb-2">
                                            <i class="fas fa-wind"></i> Wind: {{ $currentWeather->wind_speed }} m/s
                                        </div>
                                        <div class="col-6 mb-2">
                                            <i class="fas fa-cloud-rain"></i> Precipitation: {{ $currentWeather->precipitation }} mm
                                        </div>
                                        <div class="col-6 mb-2">
                                            <i class="fas fa-sun"></i> UV Index: {{ $currentWeather->uv_index }}
                                        </div>
                                        <div class="col-6 mb-2">
                                            <i class="fas fa-eye"></i> Visibility: {{ $currentWeather->visibility }} km
                                        </div>
                                        <div class="col-6 mb-2">
                                            <i class="fas fa-compress-arrows-alt"></i> Pressure: {{ $currentWeather->pressure }} hPa
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-info">
                No weather data available for this farm. Weather data can be recorded manually or via API integration.
            </div>
            @endif

            <!-- Recommendations -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Recommendations</h5>
                </div>
                <div class="card-body">
                    @if($currentWeather && $currentWeather->isGoodForIrrigation())
                        <div class="alert alert-success">
                            <i class="fas fa-check"></i> Good conditions for irrigation today.
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Not recommended for irrigation today.
                        </div>
                    @endif

                    @if($currentWeather && $currentWeather->isGoodForSpraying())
                        <div class="alert alert-success">
                            <i class="fas fa-check"></i> Good conditions for spraying operations.
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Avoid spraying operations today due to weather conditions.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
