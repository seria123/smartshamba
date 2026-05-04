@extends('layouts.MainLayout')

@section('title', 'Create Farm - SmartShamba')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Create Farm</h1>
        <a href="{{ route('farms.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Farms
        </a>
    </div>

    <!-- Create Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('farms.store') }}" method="POST" id="farmCreateForm">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Farm Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('name') border-red-500 @enderror"
                        required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                 <!-- Location -->
                 <div>
                     <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location (County) *</label>
                     <input type="text" name="location" id="location" value="{{ old('location') }}" 
                         class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                         placeholder="e.g., Nairobi County" required>
                     @error('location')
                         <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                     @enderror
                 </div>

                 <!-- Subcounty -->
                 <div>
                     <label for="subcounty" class="block text-sm font-medium text-gray-700 mb-2">Subcounty</label>
                     <input type="text" name="subcounty" id="subcounty" value="{{ old('subcounty') }}" 
                         class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                         placeholder="e.g., Kasarani">
                     @error('subcounty')
                         <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                     @enderror
                 </div>

                 <!-- Physical Address -->
                 <div>
                     <label for="physical_address" class="block text-sm font-medium text-gray-700 mb-2">Physical Address</label>
                     <textarea name="physical_address" id="physical_address" rows="3" 
                         class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('physical_address') }}</textarea>
                     @error('physical_address')
                         <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                     @enderror
                 </div>

                 <!-- Farm Type -->
                <div>
                    <label for="farm_type" class="block text-sm font-medium text-gray-700 mb-2">Farm Type *</label>
                    <select name="farm_type" id="farm_type" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('farm_type') border-red-500 @enderror"
                        required>
                        <option value="">Select type...</option>
                        <option value="crop" {{ old('farm_type') == 'crop' ? 'selected' : '' }}>🌽 Crop farming</option>
                        <option value="livestock" {{ old('farm_type') == 'livestock' ? 'selected' : '' }}>🐄 Livestock</option>
                        <option value="mixed" {{ old('farm_type') == 'mixed' ? 'selected' : '' }}>🌾🐓 Mixed farming</option>
                    </select>
                    @error('farm_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                 <!-- Ownership Type -->
                 <div>
                     <label for="ownership_type" class="block text-sm font-medium text-gray-700 mb-2">Ownership Type *</label>
                     <select name="ownership_type" id="ownership_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                         <option value="">Select ownership...</option>
                         <option value="owned" {{ old('ownership_type') == 'owned' ? 'selected' : '' }}>Owned</option>
                         <option value="leased" {{ old('ownership_type') == 'leased' ? 'selected' : '' }}>Leased</option>
                         <option value="community" {{ old('ownership_type') == 'community' ? 'selected' : '' }}>Community</option>
                     </select>
                     @error('ownership_type')
                         <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                     @enderror
                 </div>

                  <!-- Storage Facilities -->
                  <div>
                      <label for="storage_facilities" class="block text-sm font-medium text-gray-700 mb-2">Storage Facilities *</label>
                      <select name="storage_facilities" id="storage_facilities" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                          <option value="">Select...</option>
                          <option value="1" {{ old('storage_facilities') == '1' ? 'selected' : '' }}>Yes</option>
                          <option value="0" {{ old('storage_facilities', '0') == '0' ? 'selected' : '' }}>No</option>
                      </select>
                      @error('storage_facilities')
                          <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                      @enderror
                  </div>

                  <!-- Estimated Budget -->
                  <div>
                      <label for="estimated_budget" class="block text-sm font-medium text-gray-700 mb-2">Estimated Budget (KES)</label>
                      <input type="number" name="estimated_budget" id="estimated_budget" value="{{ old('estimated_budget') }}" step="0.01" min="0"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                          placeholder="e.g., 500000">
                      @error('estimated_budget')
                          <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                      @enderror
                  </div>

                  <!-- Main Purpose -->
                  <div>
                      <label for="main_purpose" class="block text-sm font-medium text-gray-700 mb-2">Main Purpose</label>
                      <select name="main_purpose" id="main_purpose" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                          <option value="">Select purpose...</option>
                          <option value="commercial" {{ old('main_purpose') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                          <option value="subsistence" {{ old('main_purpose') == 'subsistence' ? 'selected' : '' }}>Subsistence</option>
                      </select>
                      @error('main_purpose')
                          <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                      @enderror
                  </div>

                  <!-- Size -->
                <div>
                    <label for="size_hectares" class="block text-sm font-medium text-gray-700 mb-2">Size (Hectares)</label>
                    <input type="number" name="size_hectares" id="size_hectares" value="{{ old('size_hectares') }}" step="0.01" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    @error('size_hectares')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- GPS Coordinates -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">GPS Coordinates (optional)</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" step="any" name="latitude" id="latitude" value="{{ old('latitude') }}" 
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                            placeholder="Latitude" min="-90" max="90">
                        <input type="number" step="any" name="longitude" id="longitude_create" value="{{ old('longitude') }}" 
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
            <div id="cropFieldsCreate" style="display: none;" class="mt-6 p-4 border rounded bg-gray-50">
                <h3 class="font-medium text-gray-700 mb-4">Crop Farming Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Crops</label>
                        <select name="crops[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" multiple size="5">
                            @foreach($crops as $crop)
                                <option value="{{ $crop->name }}" {{ in_array($crop->name, old('crops', [])) ? 'selected' : '' }}>{{ $crop->name }}</option>
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
                            <option value="short_rain" {{ old('season_type') == 'short_rain' ? 'selected' : '' }}>Short Rains</option>
                            <option value="long_rain" {{ old('season_type') == 'long_rain' ? 'selected' : '' }}>Long Rains</option>
                            <option value="year-round" {{ old('season_type') == 'year-round' ? 'selected' : '' }}>Year-round</option>
                        </select>
                        @error('season_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Livestock Fields -->
            <div id="livestockFieldsCreate" style="display: none;" class="mt-6 p-4 border rounded bg-gray-50">
                <h3 class="font-medium text-gray-700 mb-4">Livestock Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Animals</label>
                        <select name="livestock_types[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" multiple size="4">
                            @foreach($livestockTypes as $type)
                                <option value="{{ $type->name }}" {{ in_array($type->name, old('livestock_types', [])) ? 'selected' : '' }}>{{ $type->name }}</option>
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
                            <option value="meat" {{ old('production_goal') == 'meat' ? 'selected' : '' }}>Meat</option>
                            <option value="milk" {{ old('production_goal') == 'milk' ? 'selected' : '' }}>Milk</option>
                            <option value="eggs" {{ old('production_goal') == 'eggs' ? 'selected' : '' }}>Eggs</option>
                            <option value="breeding" {{ old('production_goal') == 'breeding' ? 'selected' : '' }}>Breeding</option>
                        </select>
                        @error('production_goal')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                     </div>
                 </div>
             </div>

             <!-- Staff Section -->
             <div class="mt-6 p-4 border rounded bg-blue-50">
                 <h3 class="font-medium text-gray-700 mb-4">👥 Staff / Labor</h3>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                     <div>
                         <label for="staff_permanent" class="block text-sm font-medium text-gray-700 mb-2">Number of Permanent Staff</label>
                         <input type="number" name="staff_permanent" id="staff_permanent" value="{{ old('staff_permanent', 0) }}" min="0"
                             class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                         @error('staff_permanent')
                             <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                         @enderror
                     </div>

                     <div>
                         <label for="staff_casual" class="block text-sm font-medium text-gray-700 mb-2">Number of Casual Staff</label>
                         <input type="number" name="staff_casual" id="staff_casual" value="{{ old('staff_casual', 0) }}" min="0"
                             class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                         @error('staff_casual')
                             <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                         @enderror
                     </div>
                 </div>
             </div>

             <!-- Description -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="4" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="mt-8 flex justify-between items-center">
                <!-- Cancel -->
                <a href="{{ route('farms.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-100 transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Cancel
                </a>

                <!-- Create Button -->
                <button type="submit"
                    class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-xl shadow-md hover:bg-green-700 hover:shadow-lg active:scale-95 transition duration-200">

                    <i class="fas fa-seedling mr-2"></i>
                    Create Farm
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const farmTypeSelect = document.getElementById('farm_type');
        const cropFields = document.getElementById('cropFieldsCreate');
        const livestockFields = document.getElementById('livestockFieldsCreate');

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
