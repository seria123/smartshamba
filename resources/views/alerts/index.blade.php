@extends('layouts.MainLayout')

@section('title', 'Alerts - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Alerts</h1>
        <a href="{{ route('alerts.create') }}" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i>Add Alert
        </a>
    </div>

    <!-- Alerts Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sensor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Severity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($alerts as $alert)
                    <tr class="{{ !$alert->is_read ? 'bg-yellow-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $alert->type === 'critical' ? 'bg-red-100 text-red-800' : ($alert->type === 'warning' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ ucfirst($alert->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ Str::limit($alert->message, 50) }}</div>
                            <div class="text-sm text-gray-500">{{ $alert->parameter }}: {{ $alert->value }} (threshold: {{ $alert->threshold }})</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $alert->sensorReading?->sensor?->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $alert->severity === 'high' ? 'bg-red-100 text-red-800' : ($alert->severity === 'medium' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800') }}">
                                {{ ucfirst($alert->severity) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($alert->is_read)
                                <span class="text-green-600 text-sm"><i class="fas fa-check-circle"></i> Read</span>
                            @else
                                <span class="text-red-600 text-sm"><i class="fas fa-envelope"></i> Unread</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('alerts.show', $alert->id) }}" class="text-primary hover:text-primary-dark mr-3">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('alerts.edit', $alert->id) }}" class="text-yellow-500 hover:text-yellow-700 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(!$alert->is_read)
                                <a href="{{ route('alerts.markAsRead', $alert->id) }}" class="text-green-500 hover:text-green-700 mr-3" title="Mark as read">
                                    <i class="fas fa-check"></i>
                                </a>
                            @endif
                            <form action="{{ route('alerts.destroy', $alert->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure you want to delete this alert?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No alerts found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
