@extends('layouts.MainLayout')

@section('title', 'Alert Details - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Alert Details</h1>
        <div class="flex space-x-2">
            <a href="{{ route('alerts.edit', $alert->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('alerts.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <!-- Alert Details -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-500">Type</p>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                    {{ $alert->type === 'critical' ? 'bg-red-100 text-red-800' : ($alert->type === 'warning' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                    {{ ucfirst($alert->type) }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-500">Severity</p>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                    {{ $alert->severity === 'high' ? 'bg-red-100 text-red-800' : ($alert->severity === 'medium' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800') }}">
                    {{ ucfirst($alert->severity) }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-500">Status</p>
                @if($alert->is_read)
                    <span class="text-green-600"><i class="fas fa-check-circle"></i> Read</span>
                @else
                    <span class="text-red-600"><i class="fas fa-envelope"></i> Unread</span>
                @endif
            </div>
        </div>

        <div class="mt-6">
            <p class="text-sm text-gray-500">Message</p>
            <p class="text-lg font-medium text-gray-800">{{ $alert->message }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div>
                <p class="text-sm text-gray-500">Parameter</p>
                <p class="text-lg font-medium text-gray-800">{{ ucfirst(str_replace('_', ' ', $alert->parameter)) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Value</p>
                <p class="text-lg font-medium text-gray-800">{{ $alert->value ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Threshold</p>
                <p class="text-lg font-medium text-gray-800">{{ $alert->threshold ?? 'N/A' }}</p>
            </div>
        </div>

        @if($alert->sensorReading)
            <div class="mt-6">
                <p class="text-sm text-gray-500">Related Sensor Reading</p>
                <p class="text-lg font-medium text-gray-800">
                    {{ $alert->sensorReading->sensor->name ?? 'Unknown Sensor' }} - 
                    {{ $alert->sensorReading->timestamp->format('M d, Y H:i') }}
                </p>
            </div>
        @endif

        <div class="mt-6">
            <p class="text-sm text-gray-500">Created At</p>
            <p class="text-lg font-medium text-gray-800">{{ $alert->created_at->format('M d, Y H:i:s') }}</p>
        </div>
    </div>

    @if(!$alert->is_read)
        <div class="bg-white rounded-lg shadow-md p-6">
            <a href="{{ route('alerts.markAsRead', $alert->id) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition inline-block">
                <i class="fas fa-check mr-2"></i>Mark as Read
            </a>
        </div>
    @endif
</div>
@endsection
