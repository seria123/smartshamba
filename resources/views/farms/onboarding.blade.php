<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">
            🌾 Farm Onboarding - Step 1: Farm Identity & Operations
        </h2>
    </x-slot>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded max-w-xl mb-6">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('farms.onboarding.store') }}" class="space-y-6 max-w-xl" id="farmOnboardingForm">
        @csrf

        <div>
            <label class="block font-medium mb-1">Farm Name</label>
            <input type="text" name="name" class="w-full border p-2 rounded" required value="{{ old('name') }}">
            @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block font-medium mb-1">Location (County)</label>
            <input type="text" name="location" class="w-full border p-2 rounded" placeholder="e.g., Nairobi County" value="{{ old('location') }}" required>
            @error('location')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block font-medium mb-1">Subcounty</label>
            <input type="text" name="subcounty" class="w-full border p-2 rounded" placeholder="e.g., Kasarani" value="{{ old('subcounty') }}">
            @error('subcounty')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block font-medium mb-1">Physical Address</label>
            <textarea name="physical_address" class="w-full border p-2 rounded" rows="3">{{ old('physical_address') }}</textarea>
            @error('physical_address')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block font-medium mb-1">Farm Type</label>
            <select name="farm_type" class="w-full border p-2 rounded" required id="farmTypeSelect">
                <option value="">Select type...</option>
                <option value="crop" {{ old('farm_type') == 'crop' ? 'selected' : '' }}>🌽 Crop farming</option>
                <option value="livestock" {{ old('farm_type') == 'livestock' ? 'selected' : '' }}>🐄 Livestock</option>
                <option value="mixed" {{ old('farm_type') == 'mixed' ? 'selected' : '' }}>🌾🐓 Mixed farming</option>
            </select>
            @error('farm_type')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Crop Farming Fields -->
        <div id="cropFields" style="display: none;" class="space-y-4 p-4 border rounded bg-gray-50">
            <h3 class="font-medium text-gray-700">Crop Farming Details</h3>

            <div>
                <label class="block font-medium mb-1">Select Crops (multi-select)</label>
                <select name="crops[]" class="w-full border p-2 rounded" multiple size="5">
                    @foreach($crops as $crop)
                        <option value="{{ $crop->name }}" {{ in_array($crop->name, old('crops', [])) ? 'selected' : '' }}>
                            {{ $crop->name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-gray-500 text-sm mt-1">Hold Ctrl/Cmd to select multiple crops</p>
                @error('crops')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block font-medium mb-1">Season Type</label>
                <select name="season_type" class="w-full border p-2 rounded">
                    <option value="">Select season...</option>
                    <option value="short_rain" {{ old('season_type') == 'short_rain' ? 'selected' : '' }}>Short Rains</option>
                    <option value="long_rain" {{ old('season_type') == 'long_rain' ? 'selected' : '' }}>Long Rains</option>
                    <option value="year-round" {{ old('season_type') == 'year-round' ? 'selected' : '' }}>Year-round</option>
                </select>
                @error('season_type')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Livestock Fields -->
        <div id="livestockFields" style="display: none;" class="space-y-4 p-4 border rounded bg-gray-50">
            <h3 class="font-medium text-gray-700">Livestock Details</h3>

            <div>
                <label class="block font-medium mb-1">Select Animals (multi-select)</label>
                <select name="livestock_types[]" class="w-full border p-2 rounded" multiple size="4">
                    @foreach($livestockTypes as $type)
                        <option value="{{ $type->name }}" {{ in_array($type->name, old('livestock_types', [])) ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-gray-500 text-sm mt-1">Hold Ctrl/Cmd to select multiple animals</p>
                @error('livestock_types')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block font-medium mb-1">Production Goal</label>
                <select name="production_goal" class="w-full border p-2 rounded">
                    <option value="">Select goal...</option>
                    <option value="meat" {{ old('production_goal') == 'meat' ? 'selected' : '' }}>Meat</option>
                    <option value="milk" {{ old('production_goal') == 'milk' ? 'selected' : '' }}>Milk</option>
                    <option value="eggs" {{ old('production_goal') == 'eggs' ? 'selected' : '' }}>Eggs</option>
                    <option value="breeding" {{ old('production_goal') == 'breeding' ? 'selected' : '' }}>Breeding</option>
                </select>
                @error('production_goal')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label class="block font-medium mb-1">Ownership Type</label>
            <select name="ownership_type" class="w-full border p-2 rounded" required>
                <option value="">Select ownership...</option>
                <option value="owned" {{ old('ownership_type') == 'owned' ? 'selected' : '' }}>Owned</option>
                <option value="leased" {{ old('ownership_type') == 'leased' ? 'selected' : '' }}>Leased</option>
                <option value="community" {{ old('ownership_type') == 'community' ? 'selected' : '' }}>Community</option>
            </select>
            @error('ownership_type')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

         <div>
             <label class="block font-medium mb-1">Storage Facilities</label>
             <select name="storage_facilities" class="w-full border p-2 rounded" required>
                 <option value="">Select...</option>
                 <option value="1" {{ old('storage_facilities') == '1' ? 'selected' : '' }}>✅ Yes</option>
                 <option value="0" {{ old('storage_facilities', '0') == '0' ? 'selected' : '' }}>❌ No</option>
             </select>
             @error('storage_facilities')
                 <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
             @enderror
             <p class="text-gray-500 text-sm mt-1">Do you have storage facilities for produce?</p>
          </div>

          <div>
              <label class="block font-medium mb-1">Estimated Budget (KES)</label>
              <input type="number" name="estimated_budget" class="w-full border p-2 rounded" step="0.01" min="0" placeholder="e.g., 500000" value="{{ old('estimated_budget') }}">
              @error('estimated_budget')
                  <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
              @enderror
          </div>

          <div>
              <label class="block font-medium mb-1">Main Purpose</label>
              <select name="main_purpose" class="w-full border p-2 rounded">
                  <option value="">Select purpose...</option>
                  <option value="commercial" {{ old('main_purpose') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                  <option value="subsistence" {{ old('main_purpose') == 'subsistence' ? 'selected' : '' }}>Subsistence</option>
              </select>
              @error('main_purpose')
                  <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
              @enderror
          </div>

          <!-- Staff Section -->
         <div class="p-4 border rounded bg-blue-50">
             <h3 class="font-medium text-gray-700 mb-3">👥 Staff / Labor</h3>
             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                 <div>
                     <label class="block font-medium mb-1">Permanent Staff Count</label>
                     <input type="number" name="staff_permanent" class="w-full border p-2 rounded" min="0" value="{{ old('staff_permanent', 0) }}">
                     @error('staff_permanent')
                         <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                     @enderror
                 </div>

                 <div>
                     <label class="block font-medium mb-1">Casual Staff Count</label>
                     <input type="number" name="staff_casual" class="w-full border p-2 rounded" min="0" value="{{ old('staff_casual', 0) }}">
                     @error('staff_casual')
                         <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                     @enderror
                 </div>
             </div>
         <div>
             <label class="block font-medium mb-1">Storage Facilities</label>
             <select name="storage_facilities" class="w-full border p-2 rounded" required>
                 <option value="">Select...</option>
                 <option value="1" {{ old('storage_facilities') == '1' ? 'selected' : '' }}>✅ Yes</option>
                 <option value="0" {{ old('storage_facilities', '0') == '0' ? 'selected' : '' }}>❌ No</option>
             </select>
             @error('storage_facilities')
                 <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
             @enderror
             <p class="text-gray-500 text-sm mt-1">Do you have storage facilities for produce?</p>
         </div>

         <div>
             <label class="block font-medium mb-1">Estimated Budget (KES)</label>
             <input type="number" name="estimated_budget" class="w-full border p-2 rounded" step="0.01" min="0" placeholder="e.g., 500000" value="{{ old('estimated_budget') }}">
             @error('estimated_budget')
                 <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
             @enderror
         </div>

         <div>
             <label class="block font-medium mb-1">Main Purpose</label>
             <select name="main_purpose" class="w-full border p-2 rounded">
                 <option value="">Select purpose...</option>
                 <option value="commercial" {{ old('main_purpose') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                 <option value="subsistence" {{ old('main_purpose') == 'subsistence' ? 'selected' : '' }}>Subsistence</option>
             </select>
             @error('main_purpose')
                 <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
             @enderror
         </div>

         <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-medium mb-1">Latitude (optional)</label>
                <input type="number" step="any" name="latitude" class="w-full border p-2 rounded" placeholder="e.g., -1.2921" value="{{ old('latitude') }}">
                @error('latitude')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block font-medium mb-1">Longitude (optional)</label>
                <input type="number" step="any" name="longitude" class="w-full border p-2 rounded" placeholder="e.g., 36.8219" value="{{ old('longitude') }}">
                @error('longitude')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <button class="bg-green-600 text-white px-4 py-2 rounded">
            Continue
        </button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const farmTypeSelect = document.getElementById('farmTypeSelect');
            const cropFields = document.getElementById('cropFields');
            const livestockFields = document.getElementById('livestockFields');

            function toggleFields() {
                const farmType = farmTypeSelect.value;
                
                // Hide both first
                cropFields.style.display = 'none';
                livestockFields.style.display = 'none';

                // Show relevant fields
                if (farmType === 'crop') {
                    cropFields.style.display = 'block';
                } else if (farmType === 'livestock') {
                    livestockFields.style.display = 'block';
                } else if (farmType === 'mixed') {
                    cropFields.style.display = 'block';
                    livestockFields.style.display = 'block';
                }
            }

            // Initial toggle
            toggleFields();

            // On change
            farmTypeSelect.addEventListener('change', toggleFields);
        });
    </script>
</x-app-layout>
