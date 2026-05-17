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

                  <!-- Subcounty -->
                  <div>
                      <label for="subcounty" class="block text-sm font-medium text-gray-700 mb-2">Subcounty</label>
                      <input type="text" name="subcounty" id="subcounty" value="{{ old('subcounty', $farm->subcounty) }}"
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
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">{{ old('physical_address', $farm->physical_address) }}</textarea>
                      @error('physical_address')
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

                   <!-- Storage Facilities -->
                   <div>
                       <label for="storage_facilities" class="block text-sm font-medium text-gray-700 mb-2">Storage Facilities *</label>
                       <select name="storage_facilities" id="storage_facilities"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('storage_facilities') border-red-500 @enderror"
                           required>
                           <option value="">Select...</option>
                           <option value="1" {{ old('storage_facilities', $farm->storage_facilities) == '1' ? 'selected' : '' }}>Yes</option>
                           <option value="0" {{ old('storage_facilities', $farm->storage_facilities) == '0' ? 'selected' : '' }}>No</option>
                       </select>
                       @error('storage_facilities')
                           <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                       @enderror
                   </div>

                   <!-- Estimated Budget -->
                   <div>
                       <label for="estimated_budget" class="block text-sm font-medium text-gray-700 mb-2">Estimated Budget (KES)</label>
                       <input type="number" name="estimated_budget" id="estimated_budget" value="{{ old('estimated_budget', $farm->estimated_budget) }}" step="0.01" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           placeholder="e.g., 500000">
                       @error('estimated_budget')
                           <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                       @enderror
                   </div>

                   <!-- Main Purpose -->
                   <div>
                       <label for="main_purpose" class="block text-sm font-medium text-gray-700 mb-2">Main Purpose</label>
                       <select name="main_purpose" id="main_purpose"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent @error('main_purpose') border-red-500 @enderror">
                           <option value="">Select purpose...</option>
                           <option value="commercial" {{ old('main_purpose', $farm->main_purpose) == 'commercial' ? 'selected' : '' }}>Commercial</option>
                           <option value="subsistence" {{ old('main_purpose', $farm->main_purpose) == 'subsistence' ? 'selected' : '' }}>Subsistence</option>
                       </select>
                       @error('main_purpose')
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
                   <div class="md:col-span-2 border-2 border-dashed border-gray-200 rounded-lg p-6 bg-gray-50">
                       <div class="flex items-center justify-between mb-4">
                           <label class="block text-sm font-medium text-gray-700 flex items-center">
                               <i class="fas fa-satellite-dish text-emerald-600 mr-2"></i>
                               GPS Coordinates
                           </label>
                           <button type="button" id="getLocationBtn"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                               <i class="fas fa-location-arrow mr-2"></i>
                               Get My Location
                           </button>
                       </div>

                       <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                           <div>
                               <label for="latitude" class="block text-sm font-medium text-gray-600 mb-1">
                                   Latitude
                               </label>
                               <input type="number" name="latitude" id="latitude"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent font-mono text-lg"
                                   step="any" placeholder="-90.00000000" min="-90" max="90"
                                   value="{{ old('latitude', $farm->latitude) }}">
                           </div>
                           <div>
                               <label for="longitude_edit" class="block text-sm font-medium text-gray-600 mb-1">
                                   Longitude
                               </label>
                               <input type="number" name="longitude" id="longitude_edit"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent font-mono text-lg"
                                   step="any" placeholder="-180.00000000" min="-180" max="180"
                                   value="{{ old('longitude', $farm->longitude) }}">
                           </div>
                       </div>

                       <div id="locationStatus" class="mt-3 text-sm">
                           <span class="text-gray-500">
                               <i class="fas fa-info-circle mr-1"></i>
                               Click "Get My Location" to automatically capture GPS coordinates
                           </span>
                       </div>
                   </div>
               </div>

              <!-- Crop Farming Fields -->
              <div id="cropFieldsEdit" style="display: none;" class="mt-6 p-4 border rounded bg-gray-50">
                 <div class="flex items-center justify-between mb-4">
                     <h3 class="font-medium text-gray-700">Crop Farming Details</h3>
                     <button type="button" id="addCropBtnEdit"
                         class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                         <i class="fas fa-plus mr-1"></i>Add Crop
                     </button>
                 </div>

                 <div id="cropRowsEdit" class="space-y-4">
                     <!-- Dynamic crop rows -- pre-filled from existing data -->
                     @php $opd = old('crops_details', $farm->farm_operation_details['crops_details'] ?? []); @endphp
                     @if(!empty($opd))
                         @foreach($opd as $idx => $cropDetail)
                             <div class="crop-row grid grid-cols-1 md:grid-cols-5 gap-4 p-4 border rounded-lg bg-white relative" data-index="{{ $idx }}">
                                 <button type="button" class="remove-row-btn absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full text-xs hover:bg-red-600 transition" title="Remove">&times;</button>
                                 <input type="hidden" name="crops[{{ $idx }}][name]" value="{{ $cropDetail['name'] ?? '' }}">
                                 <div>
                                     <label class="block text-sm font-medium text-gray-700 mb-1">Crop</label>
                                     <select name="crops[{{ $idx }}][name]" class="crop-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                                         <option value="">Select crop…</option>
                                         @foreach($crops as $crop)
                                             <option value="{{ $crop->name }}" {{ ($cropDetail['name'] ?? '') == $crop->name ? 'selected' : '' }}>{{ $crop->name }}</option>
                                         @endforeach
                                     </select>
                                 </div>
                                 <div>
                                     <label class="block text-sm font-medium text-gray-700 mb-1">Season Type</label>
                                     <select name="crops[{{ $idx }}][season_type]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                         <option value="">Select season…</option>
                                         <option value="short_rain" {{ ($cropDetail['season_type'] ?? '') == 'short_rain' ? 'selected' : '' }}>Short Rains</option>
                                         <option value="long_rain" {{ ($cropDetail['season_type'] ?? '') == 'long_rain' ? 'selected' : '' }}>Long Rains</option>
                                         <option value="year-round" {{ ($cropDetail['season_type'] ?? '') == 'year-round' ? 'selected' : '' }}>Year-round</option>
                                     </select>
                                 </div>
                                 <div>
                                     <label class="block text-sm font-medium text-gray-700 mb-1">Variety</label>
                                     <input type="text" name="crops[{{ $idx }}][variety]" value="{{ $cropDetail['variety'] ?? '' }}"
                                         class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                 </div>
                                 <div>
                                     <label class="block text-sm font-medium text-gray-700 mb-1">Qty / Area (ha / bags)</label>
                                     <input type="number" name="crops[{{ $idx }}][quantity]" value="{{ $cropDetail['quantity'] ?? '' }}" step="0.01" min="0"
                                         class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                 </div>
                                 <div>
                                     <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                     <input type="text" name="crops[{{ $idx }}][notes]" value="{{ $cropDetail['notes'] ?? '' }}"
                                         class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                 </div>
                             </div>
                         @endforeach
                     @endif
                 </div>
              </div>

              <!-- Livestock Fields -->
              <div id="livestockFieldsEdit" style="display: none;" class="mt-6 p-4 border rounded bg-gray-50">
                 <div class="flex items-center justify-between mb-4">
                     <h3 class="font-medium text-gray-700">Livestock Details</h3>
                     <button type="button" id="addLivestockBtnEdit"
                         class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                         <i class="fas fa-plus mr-1"></i>Add Livestock
                     </button>
                 </div>

                 <div id="livestockRowsEdit" class="space-y-4">
                     @php $opdL = old('livestock_details', $farm->farm_operation_details['livestock_details'] ?? []); @endphp
                     @if(!empty($opdL))
                         @foreach($opdL as $idx => $lsDetail)
                             <div class="livestock-row grid grid-cols-1 md:grid-cols-4 gap-4 p-4 border rounded-lg bg-white relative" data-index="{{ $idx }}">
                                 <button type="button" class="remove-row-btn absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full text-xs hover:bg-red-600 transition" title="Remove">&times;</button>
                                 <div>
                                     <label class="block text-sm font-medium text-gray-700 mb-1">Animal Type</label>
                                     <select name="livestock[{{ $idx }}][name]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                                         <option value="">Select animal…</option>
                                         @foreach($livestockTypes as $type)
                                             <option value="{{ $type->name }}" {{ ($lsDetail['name'] ?? '') == $type->name ? 'selected' : '' }}>{{ $type->name }}</option>
                                         @endforeach
                                     </select>
                                 </div>
                                 <div>
                                     <label class="block text-sm font-medium text-gray-700 mb-1">Head Count (Qty)</label>
                                     <input type="number" name="livestock[{{ $idx }}][quantity]" value="{{ $lsDetail['quantity'] ?? '' }}" min="0"
                                         class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                 </div>
                                 <div>
                                     <label class="block text-sm font-medium text-gray-700 mb-1">Production Goal</label>
                                     <select name="livestock[{{ $idx }}][production_goal]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                         <option value="">Select goal…</option>
                                         <option value="meat" {{ ($lsDetail['production_goal'] ?? '') == 'meat' ? 'selected' : '' }}>Meat</option>
                                         <option value="milk" {{ ($lsDetail['production_goal'] ?? '') == 'milk' ? 'selected' : '' }}>Milk</option>
                                         <option value="eggs" {{ ($lsDetail['production_goal'] ?? '') == 'eggs' ? 'selected' : '' }}>Eggs</option>
                                         <option value="breeding" {{ ($lsDetail['production_goal'] ?? '') == 'breeding' ? 'selected' : '' }}>Breeding</option>
                                     </select>
                                 </div>
                                 <div>
                                     <label class="block text-sm font-medium text-gray-700 mb-1">Breed / Notes</label>
                                     <input type="text" name="livestock[{{ $idx }}][notes]" value="{{ $lsDetail['notes'] ?? '' }}"
                                         class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                 </div>
                             </div>
                         @endforeach
                     @endif
                 </div>
              </div>

              <!-- Staff Section -->
              <div class="mt-6 p-4 border rounded bg-blue-50">
                 <h3 class="font-medium text-gray-700 mb-4">👥 Staff / Labor</h3>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                     <div>
                         <label for="staff_permanent" class="block text-sm font-medium text-gray-700 mb-2">Number of Permanent Staff</label>
                         <input type="number" name="staff_permanent" id="staff_permanent" value="{{ old('staff_permanent', $farm->farm_operation_details['staff_permanent'] ?? 0) }}" min="0"
                             class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                         @error('staff_permanent')
                             <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                         @enderror
                     </div>

                     <div>
                         <label for="staff_casual" class="block text-sm font-medium text-gray-700 mb-2">Number of Casual Staff</label>
                         <input type="number" name="staff_casual" id="staff_casual" value="{{ old('staff_casual', $farm->farm_operation_details['staff_casual'] ?? 0) }}" min="0"
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
          const addCropBtnEdit = document.getElementById('addCropBtnEdit');
          const addLivestockBtnEdit = document.getElementById('addLivestockBtnEdit');
          const livestockRowsContainer = document.getElementById('livestockRowsEdit');
          const cropRowsContainer = document.getElementById('cropRowsEdit');

          function buildCropRow(index) {
              return `
              <div class="crop-row grid grid-cols-1 md:grid-cols-5 gap-4 p-4 border rounded-lg bg-white relative" data-index="${index}">
                  <button type="button" class="remove-row-btn absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full text-xs hover:bg-red-600 transition" title="Remove">&times;</button>

                  <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Crop</label>
                      <select name="crops[${index}][name]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                          <option value="">Select crop…</option>
                          @foreach($crops as $crop)
                              <option value="{{ $crop->name }}">{{ $crop->name }}</option>
                          @endforeach
                      </select>
                  </div>

                  <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Season Type</label>
                      <select name="crops[${index}][season_type]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                          <option value="">Select season…</option>
                          <option value="short_rain">Short Rains</option>
                          <option value="long_rain">Long Rains</option>
                          <option value="year-round">Year-round</option>
                      </select>
                  </div>

                  <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Variety</label>
                      <input type="text" name="crops[${index}][variety]" placeholder="e.g., HC 334"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                  </div>

                  <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Qty / Area (ha / bags)</label>
                      <input type="number" name="crops[${index}][quantity]" step="0.01" min="0" placeholder="0.00"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                  </div>

                  <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                      <input type="text" name="crops[${index}][notes]" placeholder="Optional"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                  </div>
              </div>`;
          }

          function buildLivestockRow(index) {
              return `
              <div class="livestock-row grid grid-cols-1 md:grid-cols-4 gap-4 p-4 border rounded-lg bg-white relative" data-index="${index}">
                  <button type="button" class="remove-row-btn absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full text-xs hover:bg-red-600 transition" title="Remove">&times;</button>

                  <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Animal Type</label>
                      <select name="livestock[${index}][name]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                          <option value="">Select animal…</option>
                          @foreach($livestockTypes as $type)
                              <option value="{{ $type->name }}">{{ $type->name }}</option>
                          @endforeach
                      </select>
                  </div>

                  <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Head Count (Qty)</label>
                      <input type="number" name="livestock[${index}][quantity]" min="0" placeholder="0"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                  </div>

                  <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Production Goal</label>
                      <select name="livestock[${index}][production_goal]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                          <option value="">Select goal…</option>
                          <option value="meat">Meat</option>
                          <option value="milk">Milk</option>
                          <option value="eggs">Eggs</option>
                          <option value="breeding">Breeding</option>
                      </select>
                  </div>

                  <div>
                      <label class="block text-sm font-medium text-gray-700 mb-1">Breed / Notes</label>
                      <input type="text" name="livestock[${index}][notes]" placeholder="Optional"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                  </div>
              </div>`;
          }

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

          addCropBtnEdit.addEventListener('click', function() {
              const idx = cropRowsContainer.children.length;
              cropRowsContainer.insertAdjacentHTML('beforeend', buildCropRow(idx));
              attachRemoveHandlers();
          });

          addLivestockBtnEdit.addEventListener('click', function() {
              const idx = livestockRowsContainer.children.length;
              livestockRowsContainer.insertAdjacentHTML('beforeend', buildLivestockRow(idx));
              attachRemoveHandlers();
          });

          function attachRemoveHandlers() {
              document.querySelectorAll('.remove-row-btn').forEach(btn => {
                  btn.addEventListener('click', function() {
                      this.closest('[class*="-row"]').remove();
                  });
              });
          }

          function setStatus(message, color = 'gray') {
              document.getElementById('locationStatus').innerHTML =
                  `<span class="text-${color}-600 flex items-center">${message}</span>`;
          }

          const latInput = document.getElementById('latitude');
          const lngInput = document.getElementById('longitude_edit');
          const locationStatus = document.getElementById('locationStatus');
          const getLocationBtn = document.getElementById('getLocationBtn');

          toggleFields();
          farmTypeSelect.addEventListener('change', toggleFields);

          if (getLocationBtn) {
              getLocationBtn.addEventListener('click', function() {
                  const originalHTML = getLocationBtn.innerHTML;
                  getLocationBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Getting...';
                  getLocationBtn.disabled = true;

                  if (!navigator.geolocation) {
                      setStatus('<i class="fas fa-exclamation-circle mr-1"></i>Geolocation not supported by this browser', 'red');
                      getLocationBtn.innerHTML = originalHTML;
                      getLocationBtn.disabled = false;
                      return;
                  }

                  navigator.geolocation.getCurrentPosition(
                      function(pos) {
                          latInput.value = pos.coords.latitude.toFixed(8);
                          lngInput.value = pos.coords.longitude.toFixed(8);
                          setStatus(
                              `<i class="fas fa-check-circle mr-1"></i>
                              Location captured: ${pos.coords.latitude.toFixed(6)}, ${pos.coords.longitude.toFixed(6)} (±${Math.round(pos.coords.accuracy)}m)`,
                              'green'
                          );
                          getLocationBtn.innerHTML = originalHTML;
                          getLocationBtn.disabled = false;
                      },
                      function(err) {
                          let message = 'GPS error';
                          if (err.code === 1) {
                              message = 'Location permission denied. Please allow location access in your browser settings.';
                          } else if (err.code === 2) {
                              message = 'Location unavailable. Ensure GPS/location services are enabled on your device.';
                          } else if (err.code === 3) {
                              message = 'Location request timed out. Move to an open area with clear sky view.';
                          }
                          setStatus(`<i class="fas fa-exclamation-circle mr-1"></i>${message}`, 'red');
                          getLocationBtn.innerHTML = originalHTML;
                          getLocationBtn.disabled = false;
                      },
                      {
                          enableHighAccuracy: true,
                          timeout: 30000,
                          maximumAge: 300000
                      }
                  );
              });
          }
      });
  </script>
@endsection
