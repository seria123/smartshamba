@extends('layouts.MainLayout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex flex-col gap-6">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h3 class="text-2xl font-bold mb-0">🌾 SmartShamba Dashboard</h3>
                <small class="text-gray-500">System overview & analytics</small>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm whitespace-nowrap">
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
        @if(isset($stats['active_sensors']))
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <strong class="text-blue-800">🌱 Insight:</strong>
            <span class="text-blue-700">{{ $stats['active_sensors'] ?? 0 }} sensors are active across all farms.</span>
        </div>
        @endif

        {{-- PRIMARY METRICS --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach([
                ['label'=>'Total Farmers','value'=>$stats['total_farmers'] ?? 0,'icon'=>'fa-users','color'=>'primary'],
                ['label'=>'Total Farms','value'=>$stats['total_farms'] ?? 0,'icon'=>'fa-seedling','color'=>'success'],
                ['label'=>'Crops Planted','value'=>$stats['total_crops'] ?? 0,'icon'=>'fa-seedling','color'=>'info'],
                ['label'=>'Livestock','value'=>$stats['total_livestock'] ?? 0,'icon'=>'fa-cow','color'=>'warning'],
                ['label'=>'Active Today','value'=>$stats['active_users_today'] ?? 0,'icon'=>'fa-user-clock','color'=>'danger'],
                ['label'=>'Revenue','value'=>number_format($stats['total_revenue'] ?? 0),'icon'=>'fa-dollar-sign','color'=>'success'],
            ] as $item)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
                <div class="flex items-center gap-3">
                    <div class="bg-{{ $item['color'] }} text-white rounded-xl p-2 flex-shrink-0">
                        <i class="fas {{ $item['icon'] }}"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-500 truncate">{{ $item['label'] }}</p>
                        <p class="text-xl font-bold text-gray-800">{{ $item['value'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- SECONDARY METRICS --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach([
                ['label'=>'Fields','value'=>$stats['total_fields'] ?? 0,'icon'=>'fa-border-all','color'=>'secondary'],
                ['label'=>'Sensors','value'=>$stats['total_sensors'] ?? 0,'icon'=>'fa-microchip','color'=>'info'],
                ['label'=>'Active Sensors','value'=>$stats['active_sensors'] ?? 0,'icon'=>'fa-signal','color'=>'success'],
                ['label'=>'Staff','value'=>$stats['total_staff'] ?? 0,'icon'=>'fa-users-cog','color'=>'warning'],
                ['label'=>'Harvests','value'=>$stats['total_harvests'] ?? 0,'icon'=>'fa-basket-shopping','color'=>'primary'],
            ] as $item)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
                <div class="flex items-center gap-3">
                    <div class="bg-{{ $item['color'] }} text-white rounded-xl p-2 flex-shrink-0">
                        <i class="fas {{ $item['icon'] }}"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-500 truncate">{{ $item['label'] }}</p>
                        <p class="text-xl font-bold text-gray-800">{{ $item['value'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- CHARTS GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
                <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-chart-line text-blue-600"></i> Farmers Growth Over Time
                </h5>
                <div style="height: 280px;">
                    <canvas id="farmersGrowthChart"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
                <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-purple-600"></i> Crop Distribution
                </h5>
                <div style="height: 280px;">
                    <canvas id="cropDistChart"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
                <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-amber-600"></i> Farm Registrations
                </h5>
                <div style="height: 280px;">
                    <canvas id="farmRegChart"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4">
                <h5 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="fas fa-cow text-emerald-600"></i> Livestock by Type
                </h5>
                <div style="height: 280px;">
                    <canvas id="livestockDistChart"></canvas>
                </div>
            </div>
        </div>

        {{-- RECENT ACTIVITY --}}
        <div class="card-modern overflow-hidden flex flex-col" style="max-height: 400px;">
            <div class="px-4 py-3 border-b bg-gray-50">
                <h5 class="font-semibold text-gray-800 flex items-center gap-2 mb-0">
                    <i class="fas fa-clock text-gray-500"></i> Recent Activity
                </h5>
            </div>
            <div class="overflow-y-auto flex-1">
                <table class="table table-hover align-middle mb-0">
                    <thead>
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
                            <td class="whitespace-nowrap">{{ $activity['date']->format('M d, Y H:i') }}</td>
                            <td>
                                <span class="badge bg-{{ $activity['status'] === 'success' ? 'success' : 'danger' }}">
                                    {{ ucfirst($activity['status']) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
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
canvas { max-height: 280px; }
</style>
@endsection

{{-- SCRIPTS --}}
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69', '#6610f2', '#6f42c1', '#e83e8c', '#fd7e14'];

    const farmersCtx = document.getElementById('farmersGrowthChart');
    if (farmersCtx) {
        new Chart(farmersCtx, {
            type: 'line',
            data: {
                labels: @json($chartData['farmers_growth']['labels'] ?? []),
                datasets: [{
                    label: 'Total Farmers', data: @json($chartData['farmers_growth']['data'] ?? []),
                    borderColor: colors[0], backgroundColor: 'rgba(78, 115, 223, 0.05)', fill: true, tension: 0.4,
                    pointRadius: 4, pointBackgroundColor: colors[0]
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
        });
    }

    const cropCtx = document.getElementById('cropDistChart');
    if (cropCtx) {
        new Chart(cropCtx, {
            type: 'doughnut',
            data: {
                labels: @json($chartData['crop_distribution']['labels'] ?? []),
                datasets: [{ data: @json($chartData['crop_distribution']['data'] ?? []), backgroundColor: colors.slice(0, 5), borderWidth: 2, borderColor: '#ffffff' }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } } } }
        });
    }

    const farmRegCtx = document.getElementById('farmRegChart');
    if (farmRegCtx) {
        new Chart(farmRegCtx, {
            type: 'bar',
            data: {
                labels: @json($chartData['farm_registrations']['labels'] ?? []),
                datasets: [{ label: 'New Farms', data: @json($chartData['farm_registrations']['data'] ?? []), backgroundColor: colors[1], borderRadius: 4, barThickness: 30 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
        });
    }

    const livestockCtx = document.getElementById('livestockDistChart');
    if (livestockCtx) {
        new Chart(livestockCtx, {
            type: 'bar',
            data: {
                labels: @json($chartData['livestock_distribution']['labels'] ?? []),
                datasets: [{ label: 'Head Count', data: @json($chartData['livestock_distribution']['data'] ?? []), backgroundColor: colors[3], borderRadius: 4, barThickness: 30 }]
            },
            options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } } }
        });
    }
});
</script>
@endsection