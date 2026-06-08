@php
    $feedUsage = $feedUsage ?? null;
    $value = fn ($key, $default = null) => old($key, $feedUsage?->{$key} ?? $default);
@endphp

@if($errors->any())
    <div class="rounded-lg bg-rose-50 p-3 text-sm text-rose-700">{{ $errors->first() }}</div>
@endif

<section>
    <h2 class="mb-4 text-lg font-semibold text-gray-900">Feed, Quantity & Assignment</h2>
    <div class="grid gap-4 md:grid-cols-3">
        <label class="block text-sm font-medium text-gray-700">Feed Type
            <select name="feed_type_id" class="mt-1 w-full rounded-lg border-gray-300" required>
                <option value="">Select feed</option>
                @foreach($feedTypes as $feedType)
                    <option value="{{ $feedType->id }}" @selected($value('feed_type_id') == $feedType->id)>
                        {{ $feedType->name }} - {{ Str::headline($feedType->category ?? 'feed') }}
                    </option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Individual Animal
            <select name="livestock_id" class="mt-1 w-full rounded-lg border-gray-300">
                <option value="">Group feeding / none</option>
                @foreach($livestocks as $animal)
                    <option value="{{ $animal->id }}" @selected($value('livestock_id') == $animal->id)>{{ $animal->tag_number }} {{ $animal->name ? '- '.$animal->name : '' }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Group Name
            <input name="group_name" value="{{ $value('group_name') }}" class="mt-1 w-full rounded-lg border-gray-300" placeholder="Dairy cows, Broilers">
        </label>
        <label class="block text-sm font-medium text-gray-700">Quantity
            <input type="number" step="0.01" name="quantity" value="{{ $value('quantity') }}" class="mt-1 w-full rounded-lg border-gray-300" required>
        </label>
        <label class="block text-sm font-medium text-gray-700">Unit
            <input name="unit" value="{{ $value('unit', 'kg') }}" class="mt-1 w-full rounded-lg border-gray-300" placeholder="kg, grams, bales">
        </label>
        <label class="block text-sm font-medium text-gray-700">Unit Conversion Factor
            <input type="number" step="0.0001" name="unit_conversion_factor" value="{{ $value('unit_conversion_factor', 1) }}" class="mt-1 w-full rounded-lg border-gray-300" placeholder="1 bale = ? kg">
        </label>
    </div>
</section>

<section>
    <h2 class="mb-4 text-lg font-semibold text-gray-900">Schedule & Cost</h2>
    <div class="grid gap-4 md:grid-cols-4">
        <label class="block text-sm font-medium text-gray-700">Usage Date
            <input type="date" name="usage_date" value="{{ $feedUsage?->usage_date?->toDateString() ?? old('usage_date', now()->toDateString()) }}" class="mt-1 w-full rounded-lg border-gray-300" required>
        </label>
        <label class="block text-sm font-medium text-gray-700">Frequency
            <select name="feeding_frequency" class="mt-1 w-full rounded-lg border-gray-300" required>
                @foreach(['daily' => 'Daily', 'weekly' => 'Weekly', 'custom' => 'Custom'] as $key => $label)
                    <option value="{{ $key }}" @selected($value('feeding_frequency', 'daily') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Feeding Time
            <select name="feeding_time" class="mt-1 w-full rounded-lg border-gray-300">
                <option value="">Not set</option>
                @foreach(['morning','midday','evening','night','custom'] as $time)
                    <option value="{{ $time }}" @selected($value('feeding_time') === $time)>{{ Str::headline($time) }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Reminder At
            <input type="datetime-local" name="reminder_at" value="{{ $feedUsage?->reminder_at?->format('Y-m-d\\TH:i') ?? old('reminder_at') }}" class="mt-1 w-full rounded-lg border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Cost Per Unit
            <input type="number" step="0.01" name="unit_cost" value="{{ $value('unit_cost') }}" class="mt-1 w-full rounded-lg border-gray-300">
        </label>
        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 pt-7">
            <input type="hidden" name="deduct_inventory" value="0">
            <input type="checkbox" name="deduct_inventory" value="1" @checked($value('deduct_inventory', true)) class="rounded border-gray-300">
            Deduct inventory automatically
        </label>
        <label class="block text-sm font-medium text-gray-700">Usage Type
            <input name="usage_type" value="{{ $value('usage_type') }}" class="mt-1 w-full rounded-lg border-gray-300" placeholder="maintenance, lactation, growth">
        </label>
        <label class="block text-sm font-medium text-gray-700">Staff
            <select name="staff_id" class="mt-1 w-full rounded-lg border-gray-300">
                <option value="">Not assigned</option>
                @foreach($staff as $worker)
                    <option value="{{ $worker->id }}" @selected($value('staff_id') == $worker->id)>{{ $worker->fullName() }}</option>
                @endforeach
            </select>
        </label>
    </div>
</section>

<section>
    <h2 class="mb-4 text-lg font-semibold text-gray-900">Production, FCR & Nutrition</h2>
    <div class="grid gap-4 md:grid-cols-4">
        <label class="block text-sm font-medium text-gray-700">Output Type
            <select name="output_type" class="mt-1 w-full rounded-lg border-gray-300">
                <option value="">None</option>
                @foreach(['milk' => 'Milk', 'eggs' => 'Eggs', 'weight_gain' => 'Weight gain', 'none' => 'None'] as $key => $label)
                    <option value="{{ $key }}" @selected($value('output_type') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Output Quantity
            <input type="number" step="0.01" name="output_quantity" value="{{ $value('output_quantity') }}" class="mt-1 w-full rounded-lg border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Output Unit
            <input name="output_unit" value="{{ $value('output_unit') }}" class="mt-1 w-full rounded-lg border-gray-300" placeholder="L, eggs, kg">
        </label>
        <label class="block text-sm font-medium text-gray-700">Weight Gain
            <input type="number" step="0.01" name="weight_gain" value="{{ $value('weight_gain') }}" class="mt-1 w-full rounded-lg border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Season
            <select name="season" class="mt-1 w-full rounded-lg border-gray-300">
                <option value="">Not set</option>
                @foreach(['dry','rainy','cold','hot'] as $season)
                    <option value="{{ $season }}" @selected($value('season') === $season)>{{ Str::headline($season) }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Production Stage
            <input name="production_stage" value="{{ $value('production_stage') }}" class="mt-1 w-full rounded-lg border-gray-300" placeholder="lactating, brooding, finishing">
        </label>
        <label class="block text-sm font-medium text-gray-700">Target Protein
            <input type="number" step="0.01" name="target_protein" value="{{ $value('target_protein') }}" class="mt-1 w-full rounded-lg border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Target Energy
            <input type="number" step="0.01" name="target_energy" value="{{ $value('target_energy') }}" class="mt-1 w-full rounded-lg border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700 md:col-span-2">Minerals
            <input name="minerals_text" value="{{ old('minerals_text', implode(', ', $feedUsage?->minerals ?? [])) }}" class="mt-1 w-full rounded-lg border-gray-300" placeholder="calcium, phosphorus, salt">
        </label>
    </div>
</section>

<section>
    <h2 class="mb-4 text-lg font-semibold text-gray-900">Batch, Supplier & Quality</h2>
    <div class="grid gap-4 md:grid-cols-4">
        <label class="block text-sm font-medium text-gray-700">Batch Number
            <input name="batch_number" value="{{ $value('batch_number') }}" class="mt-1 w-full rounded-lg border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Supplier
            <select name="supplier_id" class="mt-1 w-full rounded-lg border-gray-300">
                <option value="">None</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @selected($value('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Spoilage Status
            <select name="spoilage_status" class="mt-1 w-full rounded-lg border-gray-300">
                <option value="">None</option>
                @foreach(['none' => 'None', 'mold' => 'Mold', 'spoilage' => 'Spoilage', 'poor_quality' => 'Poor quality'] as $key => $label)
                    <option value="{{ $key }}" @selected($value('spoilage_status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Feed Quality Image
            <input type="file" name="quality_image" accept="image/*" class="mt-1 w-full rounded-lg border-gray-300">
        </label>
    </div>
    <label class="mt-4 block text-sm font-medium text-gray-700">Quality Notes
        <textarea name="quality_notes" rows="2" class="mt-1 w-full rounded-lg border-gray-300" placeholder="Mold, spoilage, smell, color...">{{ $value('quality_notes') }}</textarea>
    </label>
    <label class="mt-4 block text-sm font-medium text-gray-700">Notes
        <textarea name="notes" rows="3" class="mt-1 w-full rounded-lg border-gray-300">{{ $value('notes') }}</textarea>
    </label>
</section>
