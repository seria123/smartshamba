@extends('layouts.MainLayout')

@section('title', 'Feed Type Details - SmartShamba')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex flex-col gap-4">
        
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
             <div>
                 <h1 class="text-2xl font-bold mb-0">{{ $feedType->name }}</h1>
                 <small class="text-gray-500">{{ $feedType->description ?? 'No description provided' }}</small>
             </div>
             <div class="flex space-x-3">
                 <a href="{{ route('feed-types.edit', $feedType) }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition flex items-center space-x-2">
                     <i class="fas fa-edit"></i> Edit
                 </a>
                 <form action="{{ route('feed-types.destroy', $feedType) }}" method="POST" class="inline-block">
                     @csrf
                     @method('DELETE')
                     <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition flex items-center space-x-2" onclick="return confirm('Are you sure you want to delete this feed type? This action cannot be undone.');">
                         <i class="fas fa-trash"></i> Delete
                     </button>
                 </form>
             </div>
         </div>

         {{-- Feed Type Details --}}
         <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
             
             {{-- Basic Information --}}
             <div class="bg-white rounded-lg shadow-md p-6">
                 <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                     <span class="bg-green-100 text-green-700 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-2">1</span>
                     Feed Information
                 </h2>
                 
                 <div class="space-y-4">
                     <div class="flex items-center">
                         <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-800 font-bold mr-4">
                             🌾
                         </div>
                         <div>
                             <h3 class="font-semibold text-gray-800">{{ $feedType->name }}</h3>
                             <p class="text-gray-600">{{ $feedType->description ?? 'No description provided' }}</p>
                         </div>
                     </div>
                     
                     <div class="border-t pt-4">
                         <div class="space-y-2">
                             <div class="flex justify-between">
                                 <span class="text-sm font-medium text-gray-500">Default Unit:</span>
                                 <span class="font-medium text-gray-700">{{ $feedType->default_unit }}</span>
                             </div>
                             <div class="flex justify-between">
                                 <span class="text-sm font-medium text-gray-500">Minimum Threshold:</span>
                                 <span class="font-medium text-gray-700">{{ $feedType->min_threshold }} {{ $feedType->default_unit }}</span>
                             </div>
                             <div class="flex justify-between">
                                 <span class="text-sm font-medium text-gray-500">Status:</span>
                                 <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full @if($feedType->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                     {{ $feedType->is_active ? 'Active' : 'Inactive' }}
                                 </span>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
             
             {{-- Stock Information --}}
             <div class="bg-white rounded-lg shadow-md p-6">
                 <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                     <span class="bg-green-100 text-green-700 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-2">2</span>
                     Current Stock Levels
                 </h2>
                 
                 <div class="space-y-4">
                     <div class="flex items-center">
                         <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-800 font-bold mr-4">
                             📦
                         </div>
                         <div>
                             <h3 class="font-semibold text-gray-800">Total Stock</h3>
                             <p class="text-gray-600">Current quantity in storage</p>
                         </div>
                     </div>
                     
                     <div class="border-t pt-4 text-center py-6">
                         <p class="text-3xl font-bold text-gray-800">{{ number_format($feedType->totalQuantity(), 2) }} {{ $feedType->default_unit }}</p>
                         <p class="text-sm text-gray-500 mt-2">Total available quantity</p>
                     </div>
                     
                     @if($feedType->isBelowThreshold())
                         <div class="border-t pt-4 bg-red-50">
                             <div class="flex items-start space-x-3">
                                 <div class="flex-shrink-0">
                                     <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center text-red-800 font-bold mt-0.5">
                                         ⚠️
                                     </div>
                                 </div>
                                 <div>
                                     <p class="mb-1 text-sm font-medium text-red-800">Below Minimum Threshold</p>
                                     <p class="text-sm text-red-600">Current stock ({{ number_format($feedType->totalQuantity(), 2) }} {{ $feedType->default_unit }}) is below minimum threshold of {{ $feedType->min_threshold }} {{ $feedType->default_unit }}</p>
                                 </div>
                             </div>
                         </div>
                     @else
                         <div class="border-t pt-4 bg-green-50">
                             <div class="flex items-start space-x-3">
                                 <div class="flex-shrink-0">
                                     <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center text-green-800 font-bold mt-0.5">
                                         ✅
                                     </div>
                                 </div>
                                 <div>
                                     <p class="mb-1 text-sm font-medium text-green-800">Above Minimum Threshold</p>
                                     <p class="text-sm text-green-600">Current stock ({{ number_format($feedType->totalQuantity(), 2) }} {{ $feedType->default_unit }}) is above minimum threshold of {{ $feedType->min_threshold }} {{ $feedType->default_unit }}</p>
                                 </div>
                             </div>
                         </div>
                     @endif
                 </div>
             </div>
         </div>
    </div>
</div>
@endsection