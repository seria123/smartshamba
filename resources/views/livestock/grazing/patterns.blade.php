@extends('layouts.MainLayout')

@section('title', 'Grazing Patterns - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('livestock.show', $livestock) }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Grazing Patterns</h1>
            <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm">{{ $livestock->name ?? 'Unnamed' }}</span>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('livestock.grazing.patterns', $livestock) }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-filter mr-2"></i>Apply
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Grazing Time</p>
                    <p class="text-2xl font-bold text-gray-800">{{ round($totalDuration / 60, 1) }} hours</p>
                </div>
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Field Visits</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalVisits }}</p>
                </div>
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-tree"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Unique Fields</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $patterns->groupBy('field_id')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Grazing Patterns Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800">Field Activity</h2>
        </div>
        @if($patterns->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visits</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Duration</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($patterns as $pattern)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($pattern->date)->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $pattern->field->name ?? 'Unknown Field' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ round($pattern->total_minutes / 60, 1) }} hrs
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $pattern->visit_count }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ round($pattern->total_minutes / max($pattern->visit_count, 1), 0) }} min
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-8 text-center">
            <i class="fas fa-chart-area text-gray-300 text-5xl mb-4"></i>
            <p class="text-gray-500">No grazing data available for the selected period.</p>
        </div>
        @endif
    </div>
</div>
@endsection
