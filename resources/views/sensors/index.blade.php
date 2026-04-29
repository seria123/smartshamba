@extends('layouts.MainLayout')

@section('title', 'Sensors - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-wide">Sensors</h1>
            <p class="text-sm text-gray-500 mt-2">Monitor and manage field sensors for real-time data collection</p>
        </div>
        <a href="{{ route('sensors.create') }}" class="inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white px-6 py-3 rounded-lg border-2 border-emerald-800 hover:border-emerald-600 transition-all duration-200 shadow-sm hover:shadow-md font-semibold text-sm">
            <i class="fas fa-plus"></i>
            <span>Add Sensor</span>
        </a>
    </div>

    <!-- Sensors Table -->
    <div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Serial Number</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Field</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($sensors as $sensor)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ $sensor->name }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $sensor->type }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <span class="font-mono text-xs bg-gray-100 px-2.5 py-1 rounded">{{ $sensor->serial_number }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $sensor->field->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($sensor->status === 'active')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-300">
                                    <i class="fas fa-circle w-1.5 h-1.5 mr-1.5"></i>Active
                                </span>
                            @elseif($sensor->status === 'inactive')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-300">
                                    <i class="fas fa-circle w-1.5 h-1.5 mr-1.5"></i>Inactive
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 border border-yellow-300">
                                    <i class="fas fa-circle w-1.5 h-1.5 mr-1.5"></i>Maintenance
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('sensors.show', $sensor->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-emerald-200 text-emerald-600 hover:bg-emerald-50 hover:border-emerald-400 transition-all duration-200" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('sensors.edit', $sensor->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-yellow-200 text-yellow-600 hover:bg-yellow-50 hover:border-yellow-400 transition-all duration-200" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('sensors.destroy', $sensor->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-red-200 text-red-600 hover:bg-red-50 hover:border-red-400 transition-all duration-200" title="Delete" onclick="return confirm('Are you sure you want to delete this sensor?')">
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
                                <i class="fas fa-satellite-dish text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500 font-medium mb-4">No sensors found. Create one to get started.</p>
                                <a href="{{ route('sensors.create') }}" class="inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 text-white px-5 py-2.5 rounded-lg border-2 border-emerald-800 hover:border-emerald-600 transition-all duration-200 font-semibold text-sm">
                                    <i class="fas fa-plus"></i>
                                    <span>Add First Sensor</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
