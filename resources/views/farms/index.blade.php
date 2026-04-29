@extends('layouts.MainLayout')

@section('title', 'Farms - SmartShamba')

@section('content')
<div class="space-y-6">
     <!-- Page Header -->
     <div class="flex items-center justify-between">
         <h1 class="text-3xl font-bold text-gray-800 tracking-wide">Farms</h1>
         <a href="{{ route('farms.create') }}" class="inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white px-6 py-4 rounded-xl border-2 border-emerald-800 hover:border-emerald-600 transition-all duration-200 shadow-sm hover:shadow-md font-semibold">
             <i class="fas fa-plus text-lg"></i>
             <span>Add Farm</span>
         </a>
     </div>

     <!-- Farms Table -->
     <div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm overflow-hidden">
         <table class="min-w-full divide-y divide-gray-200">
             <thead class="bg-gray-50">
                 <tr>
                     <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Name</th>
                     <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Location</th>
                     <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Size (ha)</th>
                     <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Fields</th>
                     <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                 </tr>
             </thead>
             <tbody class="bg-white divide-y divide-gray-200">
                @forelse($farms as $farm)
                    <tr>
                         <td class="px-6 py-4 whitespace-nowrap">
                             <div class="text-sm font-semibold text-gray-900">{{ $farm->name }}</div>
                             @if($farm->description)
                                 <div class="text-sm text-gray-500 leading-relaxed">{{ Str::limit($farm->description, 50) }}</div>
                             @endif
                         </td>
                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 leading-relaxed">
                             {{ $farm->location ?? 'N/A' }}
                         </td>
                         <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700">
                             {{ $farm->size_hectares ?? 'N/A' }} ha
                         </td>
                         <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700">
                             {{ $farm->fields->count() }}
                         </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                             <a href="{{ route('farms.show', $farm->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-emerald-200 text-emerald-600 hover:bg-emerald-50 hover:border-emerald-400 transition-all duration-200" title="View">
                                 <i class="fas fa-eye"></i>
                             </a>
                             <a href="{{ route('farms.edit', $farm->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-yellow-200 text-yellow-600 hover:bg-yellow-50 hover:border-yellow-400 transition-all duration-200" title="Edit">
                                 <i class="fas fa-edit"></i>
                             </a>
                             <form action="{{ route('farms.destroy', $farm->id) }}" method="POST" class="inline">
                                 @csrf
                                 @method('DELETE')
                                 <button type="submit" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-red-200 text-red-600 hover:bg-red-50 hover:border-red-400 transition-all duration-200" title="Delete" onclick="return confirm('Are you sure you want to delete this farm?')">
                                     <i class="fas fa-trash"></i>
                                 </button>
                             </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No farms found. Create one to get started.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
