@extends('layouts.MainLayout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-4">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h1 class="text-2xl font-bold mb-0">Equipment List ⚙️</h1>
            <a href="{{ route('equipment.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 whitespace-nowrap">
                + Add Equipment
            </a>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        {{-- Table --}}
        <div class="card-modern overflow-hidden flex flex-col" style="max-height: calc(100vh - 240px);">
            <div class="overflow-y-auto flex-1">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="text-left p-3 border">Actions</th>
                            <th class="text-left p-3 border">#</th>
                            <th class="text-left p-3 border">Name</th>
                            <th class="text-left p-3 border">Type</th>
                            <th class="text-left p-3 border">Status</th>
                            <th class="text-left p-3 border">Purchase Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($equipment as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 border">
                                    <div class="flex gap-2">
                                        <a href="{{ route('equipment.edit', $item->id) }}" class="bg-yellow-500 text-white px-3 py-1 rounded text-sm hover:bg-yellow-600 whitespace-nowrap">Edit</a>
                                        <form action="{{ route('equipment.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete this equipment?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 whitespace-nowrap">Delete</button>
                                        </form>
                                    </div>
                                </td>
                                <td class="p-3 border">{{ $loop->iteration }}</td>
                                <td class="p-3 border">{{ $item->name }}</td>
                                <td class="p-3 border">{{ $item->type }}</td>
                                <td class="p-3 border">
                                    @if($item->status == 'available')
                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-sm">Available</span>
                                    @elseif($item->status == 'in_use')
                                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-sm">In Use</span>
                                    @else
                                        <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-sm">Maintenance</span>
                                    @endif
                                </td>
                                <td class="p-3 border">{{ $item->purchase_date ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center p-4 text-gray-500">
                                    No equipment found 🚜
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-3 py-2 border-t bg-gray-50">
                {{ $equipment->links() }}
            </div>
        </div>
    </div>
</div>
@endsection