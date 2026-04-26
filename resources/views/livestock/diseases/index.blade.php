@extends('layouts.MainLayout')

@section('title', 'Livestock Diseases - SmartShamba')

@section('content')
<div class="p-4">
    <div class="flex justify-between items-center mb-4">
        <h4 class="text-lg font-semibold"><i class="fas fa-stethoscope mr-2"></i>Livestock Diseases</h4>
        <div>
            <a href="{{ route('diseases.create') }}" class="bg-green-500 text-white px-3 py-1 rounded text-sm">
                <i class="fas fa-plus mr-1"></i>Add Disease Template
            </a>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="mb-4">
        <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg">
            <a href="{{ route('diseases.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request('status') ? '' : 'bg-white shadow' }}">All</a>
            <a href="{{ route('diseases.index', ['status' => 'active']) }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request('status') === 'active' ? 'bg-white shadow' : '' }}">Active</a>
            <a href="{{ route('diseases.index', ['status' => 'treated']) }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request('status') === 'treated' ? 'bg-white shadow' : '' }}">Treated</a>
            <a href="{{ route('diseases.highSeverity') }}" class="px-3 py-2 rounded-md text-sm font-medium">High Severity</a>
            <a href="{{ route('diseases.contagious') }}" class="px-3 py-2 rounded-md text-sm font-medium">Contagious</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 text-left">Disease</th>
                    <th class="px-3 py-2 text-left">Livestock</th>
                    <th class="px-3 py-2 text-left">Status</th>
                    <th class="px-3 py-2 text-left">Severity</th>
                    <th class="px-3 py-2 text-left">Diagnosed</th>
                    <th class="px-3 py-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($diseases as $disease)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-3 py-2">
                        <div class="font-medium">{{ $disease->name }}</div>
                        <div class="text-xs text-gray-500">{{ $disease->species ?? 'Unknown species' }}</div>
                    </td>
                    <td class="px-3 py-2">
                        @if($disease->livestock)
                            <div class="font-medium">{{ $disease->livestock->name ?? 'Unnamed' }}</div>
                            <div class="text-xs text-gray-500">Tag: {{ $disease->livestock->tag_number ?? 'N/A' }}</div>
                        @else
                            <span class="text-gray-400">N/A</span>
                        @endif
                    </td>
                    <td class="px-3 py-2">
                        @switch($disease->status)
                            @case('active')
                                <span class="bg-red-500 text-white px-2 py-0.5 rounded text-xs">Active</span>
                                @break
                            @case('treated')
                                <span class="bg-green-500 text-white px-2 py-0.5 rounded text-xs">Treated</span>
                                @break
                            @case('chronic')
                                <span class="bg-orange-500 text-white px-2 py-0.5 rounded text-xs">Chronic</span>
                                @break
                        @endswitch
                    </td>
                    <td class="px-3 py-2">
                        @switch($disease->severity)
                            @case('high')
                                <span class="bg-red-500 text-white px-2 py-0.5 rounded text-xs">High</span>
                                @break
                            @case('medium')
                                <span class="bg-yellow-500 text-white px-2 py-0.5 rounded text-xs">Medium</span>
                                @break
                            @case('low')
                                <span class="bg-blue-500 text-white px-2 py-0.5 rounded text-xs">Low</span>
                                @break
                        @endswitch
                    </td>
                    <td class="px-3 py-2 text-gray-600">
                        {{ $disease->diagnosed_date ? $disease->diagnosed_date->format('M d, Y') : 'N/A' }}
                    </td>
                    <td class="px-3 py-2 text-center">
                        <a href="{{ route('diseases.show', $disease->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-3 py-4 text-center text-gray-500">No livestock diseases found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection