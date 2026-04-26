@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-download"></i> Data Export</h1>
            </div>

            <div class="row">
                <!-- Sensor Readings Export -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-thermometer-half"></i> Sensor Readings</h5>
                        </div>
                        <div class="card-body">
                            <p>Export temperature, humidity, soil moisture, and light data.</p>
                            <form method="GET" action="{{ route('exports.sensorReadings') }}">
                                <div class="mb-2">
                                    <select name="farm_id" class="form-control">
                                        <option value="">All Farms</option>
                                        @foreach($farms as $farm)
                                            <option value="{{ $farm->id }}">{{ $farm->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <input type="date" name="from_date" class="form-control" placeholder="From Date">
                                </div>
                                <div class="mb-2">
                                    <input type="date" name="to_date" class="form-control" placeholder="To Date">
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-download"></i> Export CSV
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tasks Export -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-tasks"></i> Tasks</h5>
                        </div>
                        <div class="card-body">
                            <p>Export all farm tasks and activities.</p>
                            <form method="GET" action="{{ route('exports.tasks') }}">
                                <div class="mb-2">
                                    <select name="farm_id" class="form-control">
                                        <option value="">All Farms</option>
                                        @foreach($farms as $farm)
                                            <option value="{{ $farm->id }}">{{ $farm->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <select name="status" class="form-control">
                                        <option value="">All Statuses</option>
                                        <option value="pending">Pending</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <input type="date" name="from_date" class="form-control" placeholder="From Date">
                                </div>
                                <div class="mb-2">
                                    <input type="date" name="to_date" class="form-control" placeholder="To Date">
                                </div>
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-download"></i> Export CSV
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Irrigation Logs Export -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-tint"></i> Irrigation Logs</h5>
                        </div>
                        <div class="card-body">
                            <p>Export irrigation sessions and water usage data.</p>
                            <form method="GET" action="{{ route('exports.irrigationLogs') }}">
                                <div class="mb-2">
                                    <select name="farm_id" class="form-control">
                                        <option value="">All Farms</option>
                                        @foreach($farms as $farm)
                                            <option value="{{ $farm->id }}">{{ $farm->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <input type="date" name="from_date" class="form-control" placeholder="From Date">
                                </div>
                                <div class="mb-2">
                                    <input type="date" name="to_date" class="form-control" placeholder="To Date">
                                </div>
                                <button type="submit" class="btn btn-info w-100">
                                    <i class="fas fa-download"></i> Export CSV
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Weather Data Export -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-warning">
                            <h5 class="mb-0"><i class="fas fa-cloud"></i> Weather Data</h5>
                        </div>
                        <div class="card-body">
                            <p>Export weather observations and forecasts.</p>
                            <form method="GET" action="{{ route('exports.weatherData') }}">
                                <div class="mb-2">
                                    <select name="farm_id" class="form-control" required>
                                        <option value="">Select Farm</option>
                                        @foreach($farms as $farm)
                                            <option value="{{ $farm->id }}">{{ $farm->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <input type="date" name="from_date" class="form-control" placeholder="From Date">
                                </div>
                                <div class="mb-2">
                                    <input type="date" name="to_date" class="form-control" placeholder="To Date">
                                </div>
                                <button type="submit" class="btn btn-warning w-100">
                                    <i class="fas fa-download"></i> Export CSV
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Crops Export -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-seedling"></i> Crops</h5>
                        </div>
                        <div class="card-body">
                            <p>Export crop information and harvest data.</p>
                            <form method="GET" action="{{ route('exports.crops') }}">
                                <div class="mb-2">
                                    <select name="farm_id" class="form-control">
                                        <option value="">All Farms</option>
                                        @foreach($farms as $farm)
                                            <option value="{{ $farm->id }}">{{ $farm->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-download"></i> Export CSV
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Crop Analyses Export -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-purple text-white" style="background-color: #6f42c1;">
                            <h5 class="mb-0"><i class="fas fa-microscope"></i> Crop Analyses</h5>
                        </div>
                        <div class="card-body">
                            <p>Export AI crop analysis results.</p>
                            <form method="GET" action="{{ route('exports.cropAnalyses') }}">
                                <div class="mb-2">
                                    <select name="farm_id" class="form-control">
                                        <option value="">All Farms</option>
                                        @foreach($farms as $farm)
                                            <option value="{{ $farm->id }}">{{ $farm->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <input type="date" name="from_date" class="form-control" placeholder="From Date">
                                </div>
                                <div class="mb-2">
                                    <input type="date" name="to_date" class="form-control" placeholder="To Date">
                                </div>
                                <button type="submit" class="btn" style="background-color: #6f42c1; color: white;">
                                    <i class="fas fa-download"></i> Export CSV
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
