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
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-users me-1"></i> Manage Users
            </a>

            <span class="badge bg-{{ Auth::user()->isManager() ? 'warning' : 'primary' }}">
                {{ Auth::user()->isManager() ? 'Manager' : 'Super Admin' }}
            </span>

            @if(Auth::user()->isAdmin())
                <button class="btn btn-sm btn-dark">
                    <i class="fas fa-user-shield"></i> Admin Control
                </button>
            @endif

            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- INSIGHT --}}
    <div class="alert alert-info">
        🌱 <strong>Insight:</strong>
        {{ $stats['active_sensors'] ?? 0 }} sensors are active across all farms.
    </div>

    {{-- PRIMARY METRICS --}}
    <div class="row g-4 mb-4">
        @foreach([
            ['label'=>'Total Farmers','value'=>$stats['total_farmers'] ?? 0,'icon'=>'fa-users','color'=>'primary'],
            ['label'=>'Total Farms','value'=>$stats['total_farms'] ?? 0,'icon'=>'fa-seedling','color'=>'success'],
            ['label'=>'Crops Planted','value'=>$stats['total_crops'] ?? 0,'icon'=>'fa-seedling','color'=>'info'],
            ['label'=>'Livestock','value'=>$stats['total_livestock'] ?? 0,'icon'=>'fa-cow','color'=>'warning'],
            ['label'=>'Active Today','value'=>$stats['active_users_today'] ?? 0,'icon'=>'fa-user-clock','color'=>'danger'],
            ['label'=>'Revenue','value'=>number_format($stats['total_revenue'] ?? 0),'icon'=>'fa-dollar-sign','color'=>'success'],
        ] as $item)

        <div class="col-md-2">
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

    {{-- SECONDARY METRICS --}}
    <div class="row g-4 mb-4">
        @foreach([
            ['label'=>'Fields','value'=>$stats['total_fields'] ?? 0,'icon'=>'fa-border-all','color'=>'secondary'],
            ['label'=>'Sensors','value'=>$stats['total_sensors'] ?? 0,'icon'=>'fa-microchip','color'=>'info'],
            ['label'=>'Active Sensors','value'=>$stats['active_sensors'] ?? 0,'icon'=>'fa-signal','color'=>'success'],
            ['label'=>'Staff','value'=>$stats['total_staff'] ?? 0,'icon'=>'fa-users-cog','color'=>'warning'],
            ['label'=>'Harvests','value'=>$stats['total_harvests'] ?? 0,'icon'=>'fa-basket-shopping','color'=>'primary'],
        ] as $item)

        <div class="col-md-2">
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

    {{-- CHARTS GRID --}}
    <div class="row g-4 mb-4">
        {{-- Farmers Growth (Line) --}}
        <div class="col-md-8">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <i class="fas fa-chart-line me-2"></i>Farmers Growth Over Time
                </div>
                <div class="card-body">
                    <canvas id="farmersGrowthChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Crop Distribution (Pie) --}}
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-2"></i>Crop Distribution by Category
                </div>
                <div class="card-body">
                    <canvas id="cropDistChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Farm Registrations (Bar) --}}
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-2"></i>Farm Registrations (Last 12 Months)
                </div>
                <div class="card-body">
                    <canvas id="farmRegChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Livestock Types (Bar) --}}
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <i class="fas fa-cow me-2"></i>Livestock by Type
                </div>
                <div class="card-body">
                    <canvas id="livestockDistChart"></canvas>
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

    // Chart colors
    const colors = [
        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
        '#5a5c69', '#6610f2', '#6f42c1', '#e83e8c', '#fd7e14'
    ];

    // 1. Farmers Growth Over Time (Line Chart)
    const farmersCtx = document.getElementById('farmersGrowthChart');
    if (farmersCtx) {
        new Chart(farmersCtx, {
            type: 'line',
            data: {
                labels: @json($chartData['farmers_growth']['labels'] ?? []),
                datasets: [{
                    label: 'Total Farmers',
                    data: @json($chartData['farmers_growth']['data'] ?? []),
                    borderColor: colors[0],
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: colors[0],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    // 2. Crop Distribution by Category (Pie Chart)
    const cropCtx = document.getElementById('cropDistChart');
    if (cropCtx) {
        new Chart(cropCtx, {
            type: 'doughnut',
            data: {
                labels: @json($chartData['crop_distribution']['labels'] ?? []),
                datasets: [{
                    data: @json($chartData['crop_distribution']['data'] ?? []),
                    backgroundColor: colors.slice(0, 5),
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 20 }
                    }
                }
            }
        });
    }

    // 3. Farm Registrations Per Month (Bar Chart)
    const farmRegCtx = document.getElementById('farmRegChart');
    if (farmRegCtx) {
        new Chart(farmRegCtx, {
            type: 'bar',
            data: {
                labels: @json($chartData['farm_registrations']['labels'] ?? []),
                datasets: [{
                    label: 'New Farms',
                    data: @json($chartData['farm_registrations']['data'] ?? []),
                    backgroundColor: colors[1],
                    borderRadius: 4,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    // 4. Livestock Types Distribution (Bar Chart)
    const livestockCtx = document.getElementById('livestockDistChart');
    if (livestockCtx) {
        new Chart(livestockCtx, {
            type: 'bar',
            data: {
                labels: @json($chartData['livestock_distribution']['labels'] ?? []),
                datasets: [{
                    label: 'Head Count',
                    data: @json($chartData['livestock_distribution']['data'] ?? []),
                    backgroundColor: colors[3],
                    borderRadius: 4,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y', // Horizontal bar
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

});
</script>
@endsection