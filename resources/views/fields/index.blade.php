@extends('layouts.MainLayout')

@section('title', 'Fields - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-wide">Fields</h1>
            <p class="text-sm text-gray-500 mt-2">Manage all your farm fields and monitor their activity</p>
        </div>
        <a href="{{ route('fields.create') }}" class="inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white px-6 py-3 rounded-lg border-2 border-emerald-800 hover:border-emerald-600 transition-all duration-200 shadow-sm hover:shadow-md font-semibold text-sm">
            <i class="fas fa-plus"></i>
            <span>Add Field</span>
        </a>
    </div>

    <!-- Fields Table -->
    <div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Farm</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Size (ha)</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Sensors</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Crop</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($fields as $field)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ $field->name }}</div>
                            @if($field->location)
                                <div class="text-xs text-gray-500 mt-1">{{ $field->location }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $field->farm->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            {{ $field->size_hectares ?? 'N/A' }} ha
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="inline-flex items-center px-3 py-1 rounded-lg bg-blue-100 text-blue-800 text-xs font-bold">
                                <i class="fas fa-satellite-dish mr-1.5"></i>
                                {{ $field->sensors->count() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $field->crop->name ?? 'No Crop' }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('fields.show', $field->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-emerald-200 text-emerald-600 hover:bg-emerald-50 hover:border-emerald-400 transition-all duration-200" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('fields.edit', $field->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-yellow-200 text-yellow-600 hover:bg-yellow-50 hover:border-yellow-400 transition-all duration-200" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('fields.destroy', $field->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-red-200 text-red-600 hover:bg-red-50 hover:border-red-400 transition-all duration-200" title="Delete" onclick="return confirm('Are you sure you want to delete this field?')">
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
                                <i class="fas fa-seedling text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500 font-medium mb-4">No fields found. Create one to get started.</p>
                                <a href="{{ route('fields.create') }}" class="inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 text-white px-5 py-2.5 rounded-lg border-2 border-emerald-800 hover:border-emerald-600 transition-all duration-200 font-semibold text-sm">
                                    <i class="fas fa-plus"></i>
                                    <span>Add First Field</span>
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
