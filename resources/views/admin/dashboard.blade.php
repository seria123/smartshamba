@extends('layouts.MainLayout')

@section('title', 'Admin Dashboard - SmartShamba')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">🌾 SmartShamba Dashboard</h3>
            <small class="text-muted">System overview & analytics</small>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-{{ Auth::user()->isManager() ? 'warning' : 'primary' }}">
                {{ Auth::user()->isManager() ? 'Manager' : 'Super Admin' }}
            </span>

            @if(Auth::user()->isAdmin())
                <button class="btn btn-sm btn-dark">
                    <i class="fas fa-user-shield"></i> Admin Control
                </button>
            @endif

            <a href="{{ route('logout') }}" class="btn btn-sm btn-outline-danger">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>

    {{-- INSIGHT --}}
    <div class="alert alert-info">
        🌱 <strong>Insight:</strong>
        {{ $stats['active_sensors'] ?? 0 }} sensors are active across all farms.
    </div>

    {{-- STATS --}}
    <div class="row g-4 mb-4">
        @foreach([
            ['label'=>'Farms','value'=>$stats['total_farms'] ?? 0,'icon'=>'fa-seedling','color'=>'primary'],
            ['label'=>'Fields','value'=>$stats['total_fields'] ?? 0,'icon'=>'fa-border-all','color'=>'success'],
            ['label'=>'Sensors','value'=>$stats['total_sensors'] ?? 0,'icon'=>'fa-microchip','color'=>'info'],
            ['label'=>'Workers','value'=>$stats['total_workers'] ?? 0,'icon'=>'fa-users','color'=>'danger'],
        ] as $item)

        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <div class="bg-{{ $item['color'] }} text-white rounded-circle d-flex align-items-center justify-content-center"
                             style="width:50px;height:50px;">
                            <i class="fas {{ $item['icon'] }}"></i>
                        </div>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">{{ $item['label'] }}</h6>
                        <h4 class="fw-bold mb-0">{{ $item['value'] }}</h4>
                    </div>
                </div>
            </div>
        </div>

        @endforeach
    </div>

    {{-- CHARTS --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">📈 Farm Growth</div>
                <div class="card-body">
                    <canvas id="farmChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">🛰 Sensor Status</div>
                <div class="card-body">
                    <canvas id="sensorChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- RECENT ACTIVITY --}}
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">🕒 Recent Activity</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>Details</th>
                            <th>User</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentActivity as $activity)
                        <tr>
                            <td><strong>{{ $activity['type'] }}</strong></td>
                            <td>{{ $activity['details'] }}</td>
                            <td>{{ $activity['user'] }}</td>
                            <td>{{ $activity['date']->format('M d, Y H:i') }}</td>
                            <td>
                                <span class="badge bg-{{ $activity['status'] === 'success' ? 'success' : 'danger' }}">
                                    {{ ucfirst($activity['status']) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No recent activity
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

{{-- STYLES --}}
@section('styles')
<style>
.card {
    border-radius: 12px;
}

.card-header {
    font-weight: 600;
    background: #f8f9fa;
}

canvas {
    max-height: 280px;
}
</style>
@endsection

{{-- SCRIPTS --}}
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // FARM CHART
    const farmCanvas = document.getElementById('farmChart');
    if (farmCanvas) {
        new Chart(farmCanvas, {
            type: 'line',
            data: {
                labels: @json($chartData['months'] ?? ['Jan','Feb','Mar']),
                datasets: [{
                    label: 'Farms Created',
                    data: @json($chartData['farms'] ?? [3,7,12]),
                    fill: true,
                    tension: 0.4
                }]
            }
        });
    }

    // SENSOR CHART
    const sensorCanvas = document.getElementById('sensorChart');
    if (sensorCanvas) {
        new Chart(sensorCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Inactive'],
                datasets: [{
                    data: [
                        {{ $stats['active_sensors'] ?? 0 }},
                        {{ ($stats['total_sensors'] ?? 0) - ($stats['active_sensors'] ?? 0) }}
                    ]
                }]
            }
        });
    }

});
</script>
@endsection