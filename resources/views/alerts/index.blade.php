@extends('layouts.MainLayout')

@section('title', 'Alerts - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-wide">Alerts</h1>
            <p class="text-sm text-gray-500 mt-2">Monitor system alerts and sensor readings</p>
        </div>
        <a href="{{ route('alerts.create') }}" class="inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white px-6 py-3 rounded-lg border-2 border-emerald-800 hover:border-emerald-600 transition-all duration-200 shadow-sm hover:shadow-md font-semibold text-sm">
            <i class="fas fa-plus"></i>
            <span>Add Alert</span>
        </a>
    </div>

    <!-- Alerts Table -->
    <div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Message</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Sensor</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Severity</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($alerts as $alert)
                    <tr class="hover:bg-gray-50 transition-colors duration-150 {{ !$alert->is_read ? 'bg-yellow-50' : '' }}">
                        <td class="px-6 py-4">
                            @if($alert->type === 'critical')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-300">
                                    <i class="fas fa-exclamation-circle mr-1.5"></i>Critical
                                </span>
                            @elseif($alert->type === 'warning')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 border border-yellow-300">
                                    <i class="fas fa-warning mr-1.5"></i>Warning
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-300">
                                    <i class="fas fa-info-circle mr-1.5"></i>Info
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ Str::limit($alert->message, 50) }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $alert->parameter }}: {{ $alert->value }} (threshold: {{ $alert->threshold }})</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $alert->sensorReading?->sensor?->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($alert->severity === 'high')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-300">
                                    High
                                </span>
                            @elseif($alert->severity === 'medium')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 border border-orange-300">
                                    Medium
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-300">
                                    Low
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($alert->is_read)
                                <span class="inline-flex items-center text-green-700 font-medium">
                                    <i class="fas fa-check-circle mr-1.5"></i>Read
                                </span>
                            @else
                                <span class="inline-flex items-center text-red-700 font-medium">
                                    <i class="fas fa-envelope mr-1.5"></i>Unread
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('alerts.show', $alert->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-emerald-200 text-emerald-600 hover:bg-emerald-50 hover:border-emerald-400 transition-all duration-200" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('alerts.edit', $alert->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-yellow-200 text-yellow-600 hover:bg-yellow-50 hover:border-yellow-400 transition-all duration-200" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('alerts.markAsRead', $alert) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-green-200 text-green-600 hover:bg-green-50 hover:border-green-400 transition-all duration-200"
                                        title="Mark as read">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form action="{{ route('alerts.destroy', $alert->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-red-200 text-red-600 hover:bg-red-50 hover:border-red-400 transition-all duration-200" title="Delete" onclick="return confirm('Are you sure you want to delete this alert?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-bell text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500 font-medium mb-4">No alerts found. Your system is running smoothly!</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
