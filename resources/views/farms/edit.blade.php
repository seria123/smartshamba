@extends('layouts.MainLayout')

@section('title', 'Edit Farm - SmartShamba')

@section('content')
<div class="space-y-6">
     <!-- Page Header -->
     <div class="flex items-center justify-between">
         <h1 class="text-3xl font-bold text-gray-800">Edit Farm</h1>
         <a href="{{ route('farms.index') }}" class="text-gray-600 hover:text-gray-800">
             <i class="fas fa-arrow-left mr-2"></i>Back to Farms
         </a>
     </div>

     <!-- Edit Form -->
     <div class="bg-white rounded-lg shadow-md p-6">
         <form action="{{ route('farms.update', $farm->id) }}" method="POST" id="farmEditForm">
             @csrf
             @method('PUT')
             
             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                 <!-- Name -->
                 <div>
                     <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Farm Name *</label>
                     <input type="text" name="name" id="name" value="{{ old('name', $farm->name) }}" 
                         class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('name') border-red-500 @enderror"
                         required>
                     @error('name')
                         <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                     @enderror
                 </div>

                 <!-- Location -->
                 <div>
                     <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location (County) *</label>
                     <input type="text" name="location" id="location" value="{{ old('location', $farm->location) }}" 
                         class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                         placeholder="e.g., Nairobi County" required>
                     @error('location')
                         <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                     @enderror
                 </div>

                 <!-- Farm Type -->
                 <div>
                     <label for="farm_type" class="block text-sm font-medium text-gray-700 mb-2">Farm Type *</label>
                     <select name="farm_type" id="farm_type_edit" 
                         class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('farm_type') border-red-500 @enderror"
                         required>
                         <option value="">Select type...</option>
                         <option value="crop" {{ old('farm_type', $farm->farm_type) == 'crop' ? 'selected' : '' }}>🌽 Crop farming</option>
                         <option value="livestock" {{ old('farm_type', $farm->farm_type) == 'livestock' ? 'selected' : '' }}>🐄 Livestock</option>
                         <option value="mixed" {{ old('farm_type', $farm->farm_type) == 'mixed' ? 'selected' : '' }}>🌾🐓 Mixed farming</option>
                     </select>
                     @error('farm_type')
                         <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                     @enderror
                 </div>

                 <!-- Ownership Type -->
                 <div>
                     <label for="ownership_type" class="block text-sm font-medium text-gray-700 mb-2">Ownership Type *</label>
                     <select name="ownership_type" id="ownership_type" 
                         class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('ownership_type') border-red-500 @enderror"
                         required>
                         <option value="">Select ownership...</option>
                         <option value="owned" {{ old('ownership_type', $farm->ownership_type) == 'owned' ? 'selected' : '' }}>Owned</option>
                         <option value="leased" {{ old('ownership_type', $farm->ownership_type) == 'leased' ? 'selected' : '' }}>Leased</option>
                         <option value="community" {{ old('ownership_type', $farm->ownership_type) == 'community' ? 'selected' : '' }}>Community</option>
                     </select>
                     @error('ownership_type')
                         <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                     @enderror
                 </div>

                 <!-- Size -->
                 <div>
                     <label for="size_hectares" class="block text-sm font-medium text-gray-700 mb-2">Size (Hectares)</label>
                     <input type="number" name="size_hectares" id="size_hectares" value="{{ old('size_hectares', $farm->size_hectares) }}" step="0.01" min="0"
                         class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                     @error('size_hectares')
                         <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                     @enderror
                 </div>

                 <!-- GPS Coordinates -->
                 <div>
                     <label class="block text-sm font-medium text-gray-700 mb-2">GPS Coordinates (optional)</label>
                     <div class="grid grid-cols-2 gap-2">
                         <input type="number" step="any" name="latitude" id="latitude" value="{{ old('latitude', $farm->latitude) }}" 
                             class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                             placeholder="Latitude" min="-90" max="90">
                         <input type="number" step="any" name="longitude" id="longitude_edit" value="{{ old('longitude', $farm->longitude) }}" 
                             class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                             placeholder="Longitude" min="-180" max="180">
                     </div>
                     @error('latitude')
                         <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                     @enderror
                     @error('longitude')
                         <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                     @enderror
                 </div>
             </div>

             <!-- Crop Farming Fields -->
             <div id="cropFieldsEdit" style="display: none;" class="mt-6 p-4 border rounded bg-gray-50">
                <h3 class="font-medium text-gray-700 mb-4">Crop Farming Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Crops</label>
                        <select name="crops[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" multiple size="5">
                            @foreach($crops as $crop)
                                <option value="{{ $crop->name }}" {{ (is_array(old('crops', $farm->farm_operation_details['crops'] ?? [])) && in_array($crop->name, old('crops', $farm->farm_operation_details['crops'] ?? []))) ? 'selected' : '' }}>
                                    {{ $crop->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-gray-500 text-sm mt-1">Hold Ctrl/Cmd for multiple</p>
                        @error('crops')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Season Type</label>
                        <select name="season_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Select season...</option>
                            <option value="short_rain" {{ old('season_type', $farm->farm_operation_details['season_type'] ?? '') == 'short_rain' ? 'selected' : '' }}>Short Rains</option>
                            <option value="long_rain" {{ old('season_type', $farm->farm_operation_details['season_type'] ?? '') == 'long_rain' ? 'selected' : '' }}>Long Rains</option>
                            <option value="year-round" {{ old('season_type', $farm->farm_operation_details['season_type'] ?? '') == 'year-round' ? 'selected' : '' }}>Year-round</option>
                        </select>
                        @error('season_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
             </div>

             <!-- Livestock Fields -->
             <div id="livestockFieldsEdit" style="display: none;" class="mt-6 p-4 border rounded bg-gray-50">
                <h3 class="font-medium text-gray-700 mb-4">Livestock Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Animals</label>
                        <select name="livestock_types[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" multiple size="4">
                            @foreach($livestockTypes as $type)
                                <option value="{{ $type->name }}" {{ (is_array(old('livestock_types', $farm->farm_operation_details['livestock_types'] ?? [])) && in_array($type->name, old('livestock_types', $farm->farm_operation_details['livestock_types'] ?? []))) ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-gray-500 text-sm mt-1">Hold Ctrl/Cmd for multiple</p>
                        @error('livestock_types')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Production Goal</label>
                        <select name="production_goal" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">Select goal...</option>
                            <option value="meat" {{ old('production_goal', $farm->farm_operation_details['production_goal'] ?? '') == 'meat' ? 'selected' : '' }}>Meat</option>
                            <option value="milk" {{ old('production_goal', $farm->farm_operation_details['production_goal'] ?? '') == 'milk' ? 'selected' : '' }}>Milk</option>
                            <option value="eggs" {{ old('production_goal', $farm->farm_operation_details['production_goal'] ?? '') == 'eggs' ? 'selected' : '' }}>Eggs</option>
                            <option value="breeding" {{ old('production_goal', $farm->farm_operation_details['production_goal'] ?? '') == 'breeding' ? 'selected' : '' }}>Breeding</option>
                        </select>
                        @error('production_goal')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
             </div>

             <!-- Description -->
             <div class="mt-6">
                 <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                 <textarea name="description" id="description" rows="4" 
                     class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('description', $farm->description) }}</textarea>
                 @error('description')
                     <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                 @enderror
             </div>

             <!-- Submit Buttons -->
             <div class="mt-6 flex justify-end space-x-3">
                 <a href="{{ route('farms.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                     Cancel
                 </a>
                 <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition">
                     <i class="fas fa-save mr-2"></i>Update Farm
                 </button>
             </div>
         </form>
     </div>
 </div>

 <script>
     document.addEventListener('DOMContentLoaded', function() {
         const farmTypeSelect = document.getElementById('farm_type_edit');
         const cropFields = document.getElementById('cropFieldsEdit');
         const livestockFields = document.getElementById('livestockFieldsEdit');

         function toggleFields() {
             const farmType = farmTypeSelect.value;
             
             cropFields.style.display = 'none';
             livestockFields.style.display = 'none';

             if (farmType === 'crop') {
                 cropFields.style.display = 'block';
             } else if (farmType === 'livestock') {
                 livestockFields.style.display = 'block';
             } else if (farmType === 'mixed') {
                 cropFields.style.display = 'block';
                 livestockFields.style.display = 'block';
             }
         }

         toggleFields();
         farmTypeSelect.addEventListener('change', toggleFields);
     });
 </script>
 @endsection
