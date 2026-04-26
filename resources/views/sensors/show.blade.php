@extends('layouts.MainLayout')

@section('title', 'Sensor Details - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">{{ $sensor->name }}</h1>
        <div class="flex space-x-2">
            <a href="{{ route('sensors.edit', $sensor->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('sensors.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <!-- Sensor Details -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-500">Field</p>
                <p class="text-lg font-medium text-gray-800">{{ $sensor->field->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Type</p>
                <p class="text-lg font-medium text-gray-800">{{ ucfirst($sensor->type) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Serial Number</p>
                <p class="text-lg font-medium text-gray-800">{{ $sensor->serial_number }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Status</p>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $sensor->status === 'active' ? 'bg-green-100 text-green-800' : ($sensor->status === 'inactive' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800') }}">
                    {{ ucfirst($sensor->status) }}
                </span>
            </div>
        </div>
        @if($sensor->description)
            <div class="mt-6">
                <p class="text-sm text-gray-500">Description</p>
                <p class="text-gray-800">{{ $sensor->description }}</p>
            </div>
        @endif
    </div>

    <!-- Sensor Readings -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">
            <i class="fas fa-chart-line text-blue-500 mr-2"></i>Sensor Readings
        </h2>
        @if($sensor->sensorReadings->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Soil Moisture</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Temperature</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Humidity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Soil pH</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($sensor->sensorReadings->sortByDesc('timestamp')->take(10) as $reading)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $reading->timestamp->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $reading->soil_moisture !== null ? $reading->soil_moisture . '%' : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $reading->temperature !== null ? $reading->temperature . '°C' : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $reading->humidity !== null ? $reading->humidity . '%' : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $reading->soil_ph !== null ? 'pH ' . $reading->soil_ph : 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No sensor readings available.</p>
        @endif
    </div>
</div>
@endsection
