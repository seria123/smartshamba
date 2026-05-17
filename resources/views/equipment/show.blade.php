@extends('layouts.MainLayout')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Equipment Details ⚙️</h1>

        <a href="{{ route('equipment.create') }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Add Equipment
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Equipment Info Card --}}
    <div class="bg-white shadow-md rounded p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold mb-2">Equipment Information</h3>
                <p class="text-gray-600"><strong>Name:</strong> {{ $equipment->name }}</p>
                <p class="text-gray-600"><strong>Type:</strong> {{ $equipment->type }}</p>
                <p class="text-gray-600">
                    <strong>Status:</strong> 
                    @if($equipment->status == 'available')
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-sm">Available</span>
                    @elseif($equipment->status == 'in_use')
                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-sm">In Use</span>
                    @else
                        <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-sm">Maintenance</span>
                    @endif
                </p>
                <p class="text-gray-600"><strong>Purchase Date:</strong> {{ $equipment->purchase_date ?? 'Not specified' }}</p>
                <p class="text-gray-600"><strong>Created:</strong> {{ $equipment->created_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex gap-3">
        <a href="{{ route('equipment.edit', $equipment->id) }}" 
           class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
            Edit Equipment
        </a>

        <a href="{{ route('equipment.index') }}" 
           class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            Back to List
        </a>

        <form action="{{ route('equipment.destroy', $equipment->id) }}" 
              method="POST" 
              onsubmit="return confirm('Delete this equipment?')"
              class="inline">
            @csrf
            @method('DELETE')

            <button type="submit" 
                    class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                Delete
            </button>
        </form>
    </div>

</div>
@endsection