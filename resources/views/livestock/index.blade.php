@extends('layouts.MainLayout')

@section('title', 'Livestock - SmartShamba')

@section('content')
<div class="p-4">
    <div class="flex justify-between items-center mb-4">
        <h4 class="text-lg font-semibold"><i class="fas fa-paw mr-2"></i>Livestock</h4>
        <a href="{{ route('livestock.create') }}" class="bg-primary text-white px-3 py-1 rounded text-sm">
            <i class="fas fa-plus mr-1"></i>Add
        </a>
    </div>

    @php
    $types = \App\Models\LivestockType::withCount([
        'livestock as total' => function ($q) { $q->whereIn('status', ['healthy', 'sick']); },
        'livestock as healthy' => function ($q) { $q->where('status', 'healthy'); },
        'livestock as sick' => function ($q) { $q->where('status', 'sick'); },
    ])->get();
    @endphp
    
    @if($types->isNotEmpty())
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
        @foreach($types as $type)
        <div class="bg-white rounded shadow p-3 text-center">
            <div class="text-xs text-gray-500">{{ $type->name }}</div>
            <div class="text-xl font-bold">{{ $type->total }}</div>
            <div class="text-xs">
                <span class="text-green-600">{{ $type->healthy }} ok</span> | 
                <span class="text-red-600">{{ $type->sick }} sick</span>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 text-left">Tag</th>
                    <th class="px-3 py-2 text-left">Name</th>
                    <th class="px-3 py-2 text-left">Type</th>
                    <th class="px-3 py-2 text-left">Status</th>
                    <th class="px-3 py-2 text-left">Gender</th>
                    <th class="px-3 py-2 text-left">Weight</th>
                </tr>
            </thead>
            <tbody>
                @forelse($livestock ?? [] as $animal)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-3 py-2">{{ $animal->tag_number ?? '-' }}</td>
                    <td class="px-3 py-2 font-medium">{{ $animal->name ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $animal->type->name ?? '-' }}</td>
                    <td class="px-3 py-2">
                        @switch($animal->status)
                            @case('healthy')
                                <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs">Healthy</span>
                                @break
                            @case('sick')
                                <span class="bg-red-100 text-red-800 px-2 py-0.5 rounded text-xs">Sick</span>
                                @break
                            @case('sold')
                                <span class="bg-gray-100 text-gray-800 px-2 py-0.5 rounded text-xs">Sold</span>
                                @break
                            @case('dead')
                                <span class="bg-dark text-white px-2 py-0.5 rounded text-xs">Dead</span>
                                @break
                        @endswitch
                    </td>
                    <td class="px-3 py-2">{{ $animal->gender ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $animal->weight ? $animal->weight . ' kg' : '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-3 py-4 text-center text-gray-500">
                        No livestock. <a href="{{ route('livestock.create') }}" class="text-primary">Add one</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection