@extends('layouts.MainLayout')

@section('title', 'Edit Livestock - SmartShamba')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-wide">Edit Livestock</h1>
            <p class="text-sm text-gray-500 mt-1">Update animal information</p>
        </div>
        <a href="{{ route('livestock.index') }}" class="inline-flex items-center space-x-2 text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>
            <span>Back to List</span>
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <x-ui.card>
        <form action="{{ route('livestock.update', $livestock->id) }}" method="POST" id="livestockEditForm">
            @csrf
            @method('PUT')

            <!-- Animal Type -->
            <div class="mb-6">
                <label for="livestock_type_id" class="block text-sm font-medium text-gray-700 mb-2">Animal Type *</label>
                <select name="livestock_type_id" id="livestock_type_id" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('livestock_type_id') border-red-500 @enderror">
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ $livestock->livestock_type_id == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                            @if($type->requires_individual_tracking)
                                <span class="text-xs text-emerald-600">(Individual tracking required)</span>
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('livestock_type_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Basic Information -->
            <h3 class="font-bold text-lg text-gray-800 mb-4 border-b border-gray-200 pb-2">🐾 Animal Details</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $livestock->name) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('name') border-red-500 @enderror"
                        placeholder="e.g., Bessie, Daisy">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Breed -->
                <div>
                    <label for="breed" class="block text-sm font-medium text-gray-700 mb-2">Breed</label>
                    <input type="text" name="breed" id="breed" value="{{ old('breed', $livestock->breed) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('breed') border-red-500 @enderror"
                        placeholder="e.g., Holstein, Jersey">
                    @error('breed')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gender -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender *</label>
                    <select name="gender" id="gender" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('gender') border-red-500 @enderror">
                        <option value="">Select gender...</option>
                        <option value="male" {{ $livestock->gender == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ $livestock->gender == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date of Birth / Age -->
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                    <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $livestock->birth_date?->format('Y-m-d')) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('birth_date') border-red-500 @enderror">
                    @error('birth_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Identification -->
            <h3 class="font-bold text-lg text-gray-800 mb-4 mt-6 border-b border-gray-200 pb-2">Identification</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tag Number (for cows, goats, sheep) -->
                <div>
                    <label for="tag_number" class="block text-sm font-medium text-gray-700 mb-2">Tag Number</label>
                    <input type="text" name="tag_number" id="tag_number" value="{{ old('tag_number', $livestock->tag_number) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('tag_number') border-red-500 @enderror"
                        placeholder="e.g., TAG-001 (for cattle, goats, sheep)">
                    <p class="text-xs text-gray-500 mt-1">For individual animals (cattle, goats, sheep)</p>
                    @error('tag_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Group Name (for poultry) -->
                <div>
                    <label for="group_name" class="block text-sm font-medium text-gray-700 mb-2">Group Name</label>
                    <input type="text" name="group_name" id="group_name" value="{{ old('group_name', $livestock->group_name) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('group_name') border-red-500 @enderror"
                        placeholder="e.g., Broiler Group A (for poultry)">
                    <p class="text-xs text-gray-500 mt-1">For group animals (poultry)</p>
                    @error('group_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Farm & Location -->
            <h3 class="font-bold text-lg text-gray-800 mb-4 mt-6 border-b border-gray-200 pb-2">Farm Assignment</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Farm -->
                <div>
                    <label for="farm_id" class="block text-sm font-medium text-gray-700 mb-2">Farm</label>
                    <select name="farm_id" id="farm_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('farm_id') border-red-500 @enderror">
                        <option value="">Select farm...</option>
                        @foreach($farms as $farm)
                            <option value="{{ $farm->id }}" {{ $livestock->farm_id == $farm->id ? 'selected' : '' }}>
                                {{ $farm->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('farm_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Field -->
                <div>
                    <label for="field_id" class="block text-sm font-medium text-gray-700 mb-2">Field/Pasture</label>
                    <select name="field_id" id="field_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('field_id') border-red-500 @enderror">
                        <option value="">Select field...</option>
                        @foreach($fields as $field)
                            <option value="{{ $field->id }}" {{ $livestock->field_id == $field->id ? 'selected' : '' }}>
                                {{ $field->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('field_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Acquisition & Cost -->
            <h3 class="font-bold text-lg text-gray-800 mb-4 mt-6 border-b border-gray-200 pb-2">Acquisition Details 📊</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Acquisition Type -->
                <div>
                    <label for="acquisition_type" class="block text-sm font-medium text-gray-700 mb-2">Acquisition Type *</label>
                    <select name="acquisition_type" id="acquisition_type" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('acquisition_type') border-red-500 @enderror">
                        <option value="">Select acquisition type...</option>
                        <option value="purchased" {{ $livestock->acquisition_type == 'purchased' ? 'selected' : '' }}>Purchased</option>
                        <option value="born_on_farm" {{ $livestock->acquisition_type == 'born_on_farm' ? 'selected' : '' }}>Born on Farm</option>
                    </select>
                    @error('acquisition_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Purchase Cost -->
                <div>
                    <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-2">Purchase Cost</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                        <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', $livestock->purchase_price) }}" 
                            step="0.01" min="0"
                            class="w-full pl-7 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('purchase_price') border-red-500 @enderror"
                            placeholder="0.00">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Enter purchase price if acquired</p>
                    @error('purchase_price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date Acquired -->
                <div>
                    <label for="date_acquired" class="block text-sm font-medium text-gray-700 mb-2">Date Acquired</label>
                    <input type="date" name="date_acquired" id="date_acquired" value="{{ old('date_acquired', $livestock->date_acquired?->format('Y-m-d')) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('date_acquired') border-red-500 @enderror">
                    @error('date_acquired')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Physical Details -->
            <h3 class="font-bold text-lg text-gray-800 mb-4 mt-6 border-b border-gray-200 pb-2">Physical Details</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Weight -->
                <div>
                    <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Weight</label>
                    <div class="relative">
                        <input type="number" name="weight" id="weight" value="{{ old('weight', $livestock->weight) }}" 
                            step="0.01" min="0"
                            class="w-full pl-3 pr-8 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('weight') border-red-500 @enderror"
                            placeholder="0.00">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">kg</span>
                    </div>
                    @error('weight')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Status -->
            <h3 class="font-bold text-lg text-gray-800 mb-4 mt-6 border-b border-gray-200 pb-2">Health Status</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" id="status" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('status') border-red-500 @enderror">
                        <option value="">Select status...</option>
                        <option value="healthy" {{ $livestock->status == 'healthy' ? 'selected' : '' }}>Healthy</option>
                        <option value="sick" {{ $livestock->status == 'sick' ? 'selected' : '' }}>Sick</option>
                        <option value="sold" {{ $livestock->status == 'sold' ? 'selected' : '' }}>Sold</option>
                        <option value="dead" {{ $livestock->status == 'dead' ? 'selected' : '' }}>Dead</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" id="notes" rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('notes') border-red-500 @enderror"
                    placeholder="Additional notes about this livestock...">{{ old('notes', $livestock->notes) }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="mt-8 flex justify-between items-center">
                <a href="{{ route('livestock.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-100 transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white font-semibold rounded-xl shadow-md hover:bg-emerald-700 hover:shadow-lg active:scale-95 transition">
                    <i class="fas fa-save mr-2"></i>
                    Update Livestock
                </button>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection
