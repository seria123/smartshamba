@extends('layouts.MainLayout')

@section('title', 'Spraying Schedules - SmartShamba')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-wide">Spraying Schedules</h1>
            <p class="text-sm text-gray-500 mt-2">Track pesticide, herbicide, and fungicide applications</p>
        </div>
        <a href="{{ route('spraying_schedules.create') }}" class="inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white px-6 py-3 rounded-lg border-2 border-emerald-800 hover:border-emerald-600 transition-all duration-200 shadow-sm hover:shadow-md font-semibold text-sm">
            <i class="fas fa-plus"></i>
            <span>Add Schedule</span>
        </a>
    </div>

    <div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Chemical</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Crop/Field</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Scheduled</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($schedules as $schedule)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold
                                @if($schedule->spray_type === 'pesticide') bg-red-100 text-red-800
                                @elseif($schedule->spray_type === 'herbicide') bg-yellow-100 text-yellow-800
                                @elseif($schedule->spray_type === 'fungicide') bg-blue-100 text-blue-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($schedule->spray_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $schedule->chemical_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $schedule->crop->name ?? ($schedule->field->name ?? 'N/A') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $schedule->scheduled_date?->format('M d, Y') ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold
                                @if($schedule->status === 'completed') bg-green-100 text-green-800
                                @elseif($schedule->status === 'scheduled') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($schedule->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('spraying_schedules.show', $schedule->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-emerald-200 text-emerald-600 hover:bg-emerald-50 transition-all duration-200" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('spraying_schedules.edit', $schedule->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-yellow-200 text-yellow-600 hover:bg-yellow-50 transition-all duration-200" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('spraying_schedules.destroy', $schedule->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-red-200 text-red-600 hover:bg-red-50 transition-all duration-200" title="Delete" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-spray-can text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500 font-medium mb-4">No spraying schedules found. Create one to get started.</p>
                                <a href="{{ route('spraying_schedules.create') }}" class="inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 text-white px-5 py-2.5 rounded-lg font-semibold text-sm">
                                    <i class="fas fa-plus"></i>
                                    <span>Add First Schedule</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($schedules->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>
</div>
@endsection