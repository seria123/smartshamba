@php
    $feedType = $feedType ?? null;
    $value = fn ($key, $default = null) => old($key, $feedType?->{$key} ?? $default);
    $listText = fn ($key) => old($key.'_text', implode(', ', $feedType?->{$key} ?? []));
@endphp

@if($errors->any())
    <div class="rounded-lg bg-rose-50 p-3 text-sm text-rose-700">{{ $errors->first() }}</div>
@endif

<section>
    <h2 class="mb-4 text-lg font-semibold text-gray-900">Basic Identification</h2>
    <div class="grid gap-4 md:grid-cols-3">
        <label class="block text-sm font-medium text-gray-700">Food Name
            <input name="name" value="{{ $value('name') }}" class="mt-1 w-full rounded-lg border-gray-300" placeholder="Maize Flour, Milk, Eggs" required>
        </label>
        <label class="block text-sm font-medium text-gray-700">Category
            <select name="category" class="mt-1 w-full rounded-lg border-gray-300" required>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" @selected($value('category', 'crop_based') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Sub-category
            <input name="sub_category" list="sub-category-options" value="{{ $value('sub_category') }}" class="mt-1 w-full rounded-lg border-gray-300" placeholder="Dairy, grains, vegetables">
            <datalist id="sub-category-options">
                @foreach($subCategories as $item)<option value="{{ $item }}">@endforeach
            </datalist>
        </label>
    </div>
    <label class="mt-4 block text-sm font-medium text-gray-700">Description
        <textarea name="description" rows="3" class="mt-1 w-full rounded-lg border-gray-300">{{ $value('description') }}</textarea>
    </label>
</section>

<section>
    <h2 class="mb-4 text-lg font-semibold text-gray-900">Units & Pricing</h2>
    <div class="grid gap-4 md:grid-cols-4">
        <label class="block text-sm font-medium text-gray-700">Default Unit
            <input name="default_unit" list="unit-options" value="{{ $value('default_unit', 'kg') }}" class="mt-1 w-full rounded-lg border-gray-300" required>
            <datalist id="unit-options">@foreach($units as $unit)<option value="{{ $unit }}">@endforeach</datalist>
        </label>
        <label class="block text-sm font-medium text-gray-700">Conversion Label
            <input name="unit_conversion_label" value="{{ $value('unit_conversion_label') }}" class="mt-1 w-full rounded-lg border-gray-300" placeholder="1 bag">
        </label>
        <label class="block text-sm font-medium text-gray-700">Conversion Factor
            <input type="number" step="0.0001" name="unit_conversion_factor" value="{{ $value('unit_conversion_factor') }}" class="mt-1 w-full rounded-lg border-gray-300" placeholder="90">
        </label>
        <label class="block text-sm font-medium text-gray-700">Low Stock Threshold
            <input type="number" step="0.01" name="min_threshold" value="{{ $value('min_threshold', 10) }}" class="mt-1 w-full rounded-lg border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Selling Price
            <input type="number" step="0.01" name="selling_price" value="{{ $value('selling_price') }}" class="mt-1 w-full rounded-lg border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Minimum Price
            <input type="number" step="0.01" name="minimum_price" value="{{ $value('minimum_price') }}" class="mt-1 w-full rounded-lg border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Market Price
            <input type="number" step="0.01" name="market_price" value="{{ $value('market_price') }}" class="mt-1 w-full rounded-lg border-gray-300">
        </label>
        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 pt-7">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked($value('is_active', true)) class="rounded border-gray-300">
            Active
        </label>
    </div>
</section>

<section>
    <h2 class="mb-4 text-lg font-semibold text-gray-900">Source Tracking</h2>
    <div class="grid gap-4 md:grid-cols-3">
        <label class="block text-sm font-medium text-gray-700">Linked Crop
            <select name="linked_crop_id" class="mt-1 w-full rounded-lg border-gray-300">
                <option value="">None</option>
                @foreach($crops as $crop)<option value="{{ $crop->id }}" @selected($value('linked_crop_id') == $crop->id)>{{ $crop->name }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Linked Livestock
            <select name="linked_livestock_id" class="mt-1 w-full rounded-lg border-gray-300">
                <option value="">None</option>
                @foreach($livestock as $animal)<option value="{{ $animal->id }}" @selected($value('linked_livestock_id') == $animal->id)>{{ $animal->tag_number }} {{ $animal->name ? '- '.$animal->name : '' }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Supplier
            <select name="supplier_id" class="mt-1 w-full rounded-lg border-gray-300">
                <option value="">Farm produced / none</option>
                @foreach($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected($value('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>@endforeach
            </select>
        </label>
    </div>
</section>

<section>
    <h2 class="mb-4 text-lg font-semibold text-gray-900">Nutrition, Storage & Processing</h2>
    <div class="grid gap-4 md:grid-cols-4">
        <label class="block text-sm font-medium text-gray-700">Protein
            <input type="number" step="0.01" name="protein" value="{{ $value('protein') }}" class="mt-1 w-full rounded-lg border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Carbs
            <input type="number" step="0.01" name="carbohydrates" value="{{ $value('carbohydrates') }}" class="mt-1 w-full rounded-lg border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Fats
            <input type="number" step="0.01" name="fats" value="{{ $value('fats') }}" class="mt-1 w-full rounded-lg border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Calories
            <input type="number" step="0.01" name="energy_calories" value="{{ $value('energy_calories') }}" class="mt-1 w-full rounded-lg border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Vitamins
            <input name="vitamins_text" value="{{ $listText('vitamins') }}" class="mt-1 w-full rounded-lg border-gray-300" placeholder="A, B12, C">
        </label>
        <label class="block text-sm font-medium text-gray-700">Expiry Period Days
            <input type="number" name="expiry_period_days" value="{{ $value('expiry_period_days') }}" class="mt-1 w-full rounded-lg border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700 md:col-span-2">Storage Conditions
            <select name="storage_conditions[]" class="mt-1 w-full rounded-lg border-gray-300" multiple>
                @foreach($storageOptions as $option)<option value="{{ $option }}" @selected(in_array($option, old('storage_conditions', $feedType?->storage_conditions ?? [])))>{{ Str::headline($option) }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Raw Input
            <input name="raw_input_name" value="{{ $value('raw_input_name') }}" class="mt-1 w-full rounded-lg border-gray-300" placeholder="Maize">
        </label>
        <label class="block text-sm font-medium text-gray-700">Finished Output
            <input name="processed_output_name" value="{{ $value('processed_output_name') }}" class="mt-1 w-full rounded-lg border-gray-300" placeholder="Maize flour">
        </label>
        <label class="block text-sm font-medium text-gray-700">Processing Cost
            <input type="number" step="0.01" name="processing_cost" value="{{ $value('processing_cost') }}" class="mt-1 w-full rounded-lg border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Yield Ratio
            <input type="number" step="0.0001" name="yield_ratio" value="{{ $value('yield_ratio') }}" class="mt-1 w-full rounded-lg border-gray-300" placeholder="0.85">
        </label>
    </div>
</section>

<section>
    <h2 class="mb-4 text-lg font-semibold text-gray-900">Cost, Batch, Market & Quality</h2>
    <div class="grid gap-4 md:grid-cols-4">
        <label class="block text-sm font-medium text-gray-700">Input Cost
            <input type="number" step="0.01" name="input_cost" value="{{ $value('input_cost') }}" class="mt-1 w-full rounded-lg border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Labor Cost
            <input type="number" step="0.01" name="labor_cost" value="{{ $value('labor_cost') }}" class="mt-1 w-full rounded-lg border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Batch Number
            <input name="batch_number" value="{{ $value('batch_number') }}" class="mt-1 w-full rounded-lg border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Production Date
            <input type="date" name="production_date" value="{{ $feedType?->production_date?->toDateString() ?? old('production_date') }}" class="mt-1 w-full rounded-lg border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Source Batch
            <input name="source_batch" value="{{ $value('source_batch') }}" class="mt-1 w-full rounded-lg border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Demand Level
            <select name="demand_level" class="mt-1 w-full rounded-lg border-gray-300">
                <option value="">Unknown</option>
                @foreach($demandLevels as $key => $label)<option value="{{ $key }}" @selected($value('demand_level') === $key)>{{ $label }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Quality Grade
            <select name="quality_grade" class="mt-1 w-full rounded-lg border-gray-300">
                <option value="">Not graded</option>
                @foreach($qualityGrades as $key => $label)<option value="{{ $key }}" @selected($value('quality_grade') === $key)>{{ $label }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Image
            <input type="file" name="image" accept="image/*" class="mt-1 w-full rounded-lg border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700 md:col-span-2">Best Selling Periods
            <input name="best_selling_periods_text" value="{{ $listText('best_selling_periods') }}" class="mt-1 w-full rounded-lg border-gray-300" placeholder="April, harvest season, holidays">
        </label>
        <label class="block text-sm font-medium text-gray-700 md:col-span-2">Market Regions
            <input name="market_regions_text" value="{{ $listText('market_regions') }}" class="mt-1 w-full rounded-lg border-gray-300" placeholder="Nairobi, Kiambu, Nakuru">
        </label>
        <label class="block text-sm font-medium text-gray-700 md:col-span-2">Region Pricing
            <input name="region_pricing_text" value="{{ $listText('region_pricing') }}" class="mt-1 w-full rounded-lg border-gray-300" placeholder="Nairobi: 120/kg, Kiambu: 110/kg">
        </label>
        <label class="block text-sm font-medium text-gray-700 md:col-span-2">Certifications
            <select name="certifications[]" class="mt-1 w-full rounded-lg border-gray-300" multiple>
                @foreach($certificationOptions as $option)<option value="{{ $option }}" @selected(in_array($option, old('certifications', $feedType?->certifications ?? [])))>{{ Str::headline($option) }}</option>@endforeach
            </select>
        </label>
    </div>
    <label class="mt-4 block text-sm font-medium text-gray-700">Buyer Preferences
        <textarea name="buyer_preferences" rows="2" class="mt-1 w-full rounded-lg border-gray-300">{{ $value('buyer_preferences') }}</textarea>
    </label>
    <label class="mt-4 block text-sm font-medium text-gray-700">User Notes
        <textarea name="user_notes" rows="3" class="mt-1 w-full rounded-lg border-gray-300" placeholder="This batch had better yield...">{{ $value('user_notes') }}</textarea>
    </label>
</section>
