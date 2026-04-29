@extends('layouts.MainLayout')

@section('title', 'Fertilizer Types - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-wide">Fertilizer Types</h1>
            <p class="text-sm text-gray-500 mt-2">Manage fertilizer types and track inventory levels</p>
        </div>
        <a href="{{ route('fertilizer_types.create') }}" class="inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white px-6 py-3 rounded-lg border-2 border-emerald-800 hover:border-emerald-600 transition-all duration-200 shadow-sm hover:shadow-md font-semibold text-sm">
            <i class="fas fa-plus"></i>
            <span>Add Fertilizer</span>
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border-2 border-green-200 text-green-800 px-6 py-4 rounded-lg">
            <div class="flex items-center space-x-2">
                <i class="fas fa-check-circle text-green-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if ($fertilizerTypes->isEmpty())
        <div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm p-12 text-center">
            <i class="fas fa-flask text-5xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-bold text-gray-700 mb-2">No Fertilizer Types Yet</h3>
            <p class="text-gray-500 mb-6">Create your first fertilizer type to start tracking nutrient inputs.</p>
            <a href="{{ route('fertilizer_types.create') }}" class="inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 text-white px-6 py-3 rounded-lg border-2 border-emerald-800 hover:border-emerald-600 transition-all duration-200 font-semibold">
                <i class="fas fa-plus"></i>
                <span>Add First Fertilizer</span>
            </a>
        </div>
    @else
        <div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Unit
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Min Threshold
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($fertilizerTypes as $type)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 font-semibold text-gray-900">
                                    {{ $type->name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg bg-blue-100 text-blue-800 text-xs font-bold">
                                        {{ ucfirst($type->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $type->default_unit }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $type->min_threshold }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($type->is_active)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-300">
                                            <i class="fas fa-check-circle mr-1.5"></i>Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-300">
                                            <i class="fas fa-times-circle mr-1.5"></i>Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('fertilizer_types.show', $type) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-emerald-200 text-emerald-600 hover:bg-emerald-50 hover:border-emerald-400 transition-all duration-200" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('fertilizer_types.edit', $type) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-yellow-200 text-yellow-600 hover:bg-yellow-50 hover:border-yellow-400 transition-all duration-200" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('fertilizer_types.destroy', $type) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-red-200 text-red-600 hover:bg-red-50 hover:border-red-400 transition-all duration-200" title="Delete" onclick="return confirm('Are you sure you want to delete this fertilizer type?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection