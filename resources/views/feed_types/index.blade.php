@extends('layouts.MainLayout')

@section('title', 'Feed Types - SmartShamba')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex flex-col gap-4">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h1 class="text-2xl font-bold mb-0">Feed Types</h1>
                <small class="text-gray-500">Manage feed types and stock levels</small>
            </div>
            <a href="{{ route('admin/feed-types') }}" class="btn btn-primary whitespace-nowrap">
                <i class="fas fa-cog me-2"></i>Manage Feed Types (Admin)
            </a>
        </div>

        {{-- Feed Types Table --}}
        <div class="card-modern overflow-hidden flex flex-col" style="max-height: calc(100vh - 240px);">
            <div class="overflow-y-auto flex-1">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Default Unit</th>
                            <th>Min Threshold</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feedTypes as $feedType)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $feedType->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $feedType->description ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $feedType->default_unit }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $feedType->min_threshold }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full @if($feedType->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                        {{ $feedType->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center p-4 text-gray-500">
                                    No feed types found.
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