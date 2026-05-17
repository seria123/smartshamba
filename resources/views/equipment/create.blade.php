@extends('layouts.MainLayout')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

    <h1 class="text-2xl font-bold mb-6">Add Equipment ⚙️</h1>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('equipment.store') }}" method="POST"
          class="bg-white p-6 rounded shadow-md space-y-4">

        @csrf

        {{-- Farm Selection --}}
        <div>
            <label class="block text-sm font-medium mb-1">Farm</label>
            <select name="farm_id" class="form-select-modern" required>
                <option value="">-- Select Farm --</option>
                @foreach($farms as $farm)
                    <option value="{{ $farm->id }}" {{ old('farm_id') == $farm->id ? 'selected' : '' }}>
                        {{ $farm->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium mb-1">Equipment Name</label>
            <input type="text"
                   name="name"
                   value="{{ old('name') }}"
                   class="form-input-modern"
                   placeholder="e.g Tractor"
                   required>
        </div>

        {{-- Type --}}
        <div>
            <label class="block text-sm font-medium mb-1">Type</label>
            <input type="text"
                   name="type"
                   value="{{ old('type') }}"
                   class="form-input-modern"
                   placeholder="e.g Heavy Machinery"
                   required>
        </div>

        {{-- Status --}}
        <div>
            <label class="block text-sm font-medium mb-1">Status</label>
            <select name="status" class="form-select-modern" required>
                <option value="">-- Select Status --</option>
                <option value="available">Available</option>
                <option value="in_use">In Use</option>
                <option value="maintenance">Maintenance</option>
            </select>
        </div>

        {{-- Purchase Date --}}
        <div>
            <label class="block text-sm font-medium mb-1">Purchase Date</label>
            <input type="date"
                   name="purchase_date"
                   value="{{ old('purchase_date') }}"
                   class="form-input-modern">
        </div>

        {{-- Buttons --}}
        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary">
                Save Equipment
            </button>

            <a href="{{ route('equipment.index') }}" class="text-gray-600 hover:underline">
                Cancel
            </a>
        </div>

    </form>

</div>
@endsection