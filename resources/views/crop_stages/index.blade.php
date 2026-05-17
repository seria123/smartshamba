@extends('layouts.MainLayout')

@section('title', 'Crop Stages - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">🌱 Crop Stages</h1>
        <a href="{{ route('crop_stages.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center">
            <i class="fas fa-plus mr-2"></i> New Stage
        </a>
    </div>

    <!-- Crop Stages List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($cropStages->count() > 0)
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left py-4 px-6 text-sm font-medium text-gray-700">Stage Name</th>
                    <th class="text-left py-4 px-6 text-sm font-medium text-gray-700">Crop Cycle</th>
                    <th class="text-left py-4 px-6 text-sm font-medium text-gray-700">Start Date</th>
                    <th class="text-left py-4 px-6 text-sm font-medium text-gray-700">End Date</th>
                    <th class="text-left py-4 px-6 text-sm font-medium text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($cropStages as $stage)
                <tr class="hover:bg-gray-50">
                    <td class="py-4 px-6">
                        <p class="font-medium">{{ $stage->stage_name }}</p>
                    </td>
                    <td class="py-4 px-6">
                        <p class="text-sm">{{ $stage->cropCycle->crop_name ?? $stage->cropCycle->crop->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500">{{ $stage->cropCycle->code ?? 'N/A' }}</p>
                    </td>
                    <td class="py-4 px-6">
                        <p class="text-sm">{{ $stage->start_date?->format('M d, Y') ?? 'Not set' }}</p>
                    </td>
                    <td class="py-4 px-6">
                        <p class="text-sm">{{ $stage->end_date?->format('M d, Y') ?? 'Not set' }}</p>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex space-x-2">
                            <a href="{{ route('crop_stages.show', $stage) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('crop_stages.edit', $stage) }}" class="text-green-600 hover:text-green-800" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('crop_stages.destroy', $stage) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="p-6 text-center text-gray-500">
            <p>No crop stages found. Create your first crop stage!</p>
        </div>
        @endif
    </div>
</div>
@endsection
