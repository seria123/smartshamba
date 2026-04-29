@extends('layouts.MainLayout')

@section('title', 'Movement History - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('livestock.show', $livestock) }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Movement History</h1>
            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">{{ $livestock->name ?? 'Unnamed' }}</span>
        </div>
    </div>

    <!-- Movements Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800">Transfers & Movements</h2>
        </div>
        @if($movements->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recorded By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($movements as $movement)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $movement->movement_date->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($movement->movement_type == 'transfer') bg-blue-100 text-blue-800
                                @elseif($movement->movement_type == 'sale') bg-red-100 text-red-800
                                @elseif($movement->movement_type == 'treatment') bg-green-100 text-green-800
                                @elseif($movement->movement_type == 'birth') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $movement->movement_type)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            @if($movement->fromFarm)
                                {{ $movement->fromFarm->name }}
                                @if($movement->fromField)
                                    <br><small class="text-gray-500">{{ $movement->fromField->name }}</small>
                                @endif
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            @if($movement->toFarm)
                                {{ $movement->toFarm->name }}
                                @if($movement->toField)
                                    <br><small class="text-gray-500">{{ $movement->toField->name }}</small>
                                @endif
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $movement->user->name ?? 'System' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ Str::limit($movement->reason, 50) ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $movements->links() }}
        </div>
        @else
        <div class="p-8 text-center">
            <i class="fas fa-route text-gray-300 text-5xl mb-4"></i>
            <p class="text-gray-500">No movement records yet.</p>
        </div>
        @endif
    </div>
</div>
@endsection
