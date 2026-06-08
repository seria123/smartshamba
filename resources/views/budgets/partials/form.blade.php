@php
    $budget = $budget ?? null;
    $value = fn ($key, $default = null) => old($key, $budget?->{$key} ?? $default);
    $lineItemsText = old('line_items_text', collect($budget?->line_items ?? [])->map(fn ($item) => ($item['category'] ?? '').' | '.($item['planned'] ?? '').' | '.($item['owner'] ?? '').' | '.($item['notes'] ?? ''))->implode("\n"));
    $thresholdsText = old('alert_thresholds_text', implode(',', $budget?->alert_thresholds ?? [50, 80, 100]));
    $whatIfText = old('what_if_scenarios_text', implode("\n", $budget?->what_if_scenarios ?? []));
    $adjustmentText = old('adjustment_log_text', implode("\n", $budget?->adjustment_log ?? []));
@endphp

@if($errors->any())
    <div class="rounded-md bg-rose-50 p-3 text-sm text-rose-700">{{ $errors->first() }}</div>
@endif

<section>
    <h2 class="mb-4 text-lg font-semibold text-gray-900">Budget Creation & Ownership</h2>
    <div class="grid gap-4 md:grid-cols-3">
        <label class="block text-sm font-medium text-gray-700">Farm
            <select name="farm_id" class="mt-1 w-full rounded-md border-gray-300" required>
                <option value="">Select farm</option>
                @foreach($farms as $farm)<option value="{{ $farm->id }}" @selected($value('farm_id') == $farm->id)>{{ $farm->name }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Budget Name
            <input name="name" value="{{ $value('name') }}" class="mt-1 w-full rounded-md border-gray-300" placeholder="Long Rains 2026" required>
        </label>
        <label class="block text-sm font-medium text-gray-700">Season / Project Label
            <input name="season_name" value="{{ $value('season_name') }}" class="mt-1 w-full rounded-md border-gray-300" placeholder="Long Rains, Poultry Q2, Irrigation setup">
        </label>
        <label class="block text-sm font-medium text-gray-700">Budget Type
            <select name="budget_type" class="mt-1 w-full rounded-md border-gray-300" required>
                @foreach(['seasonal','crop','livestock','monthly','annual','project'] as $type)<option value="{{ $type }}" @selected($value('budget_type', 'seasonal') === $type)>{{ Str::headline($type) }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Owner User
            <select name="owner_user_id" class="mt-1 w-full rounded-md border-gray-300">
                <option value="">No individual owner</option>
                @foreach($users as $user)<option value="{{ $user->id }}" @selected($value('owner_user_id') == $user->id)>{{ $user->name }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Owner Group
            <input name="owner_group_name" value="{{ $value('owner_group_name') }}" class="mt-1 w-full rounded-md border-gray-300" placeholder="SACCO group, dairy team, cooperative">
        </label>
    </div>
</section>

<section>
    <h2 class="mb-4 text-lg font-semibold text-gray-900">Crop, Livestock, Loan & Inventory Links</h2>
    <div class="grid gap-4 md:grid-cols-3">
        <label class="block text-sm font-medium text-gray-700">Crop
            <select name="crop_id" class="mt-1 w-full rounded-md border-gray-300">
                <option value="">None</option>
                @foreach($crops as $crop)<option value="{{ $crop->id }}" @selected($value('crop_id') == $crop->id)>{{ $crop->name }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Crop Cycle
            <select name="crop_cycle_id" class="mt-1 w-full rounded-md border-gray-300">
                <option value="">None</option>
                @foreach($cropCycles as $cycle)<option value="{{ $cycle->id }}" @selected($value('crop_cycle_id') == $cycle->id)>{{ $cycle->code ?? $cycle->crop_name ?? 'Crop Cycle #'.$cycle->id }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Livestock / Project Animal
            <select name="livestock_id" class="mt-1 w-full rounded-md border-gray-300">
                <option value="">None</option>
                @foreach($livestock as $animal)<option value="{{ $animal->id }}" @selected($value('livestock_id') == $animal->id)>{{ $animal->tag_number }} {{ $animal->name ? '- '.$animal->name : '' }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Linked Loan
            <select name="loan_id" class="mt-1 w-full rounded-md border-gray-300">
                <option value="">No loan funding</option>
                @foreach($loans as $loan)<option value="{{ $loan->id }}" @selected($value('loan_id') == $loan->id)>{{ $loan->loan_name ?: $loan->lender_name }} - KES {{ number_format($loan->principal_amount, 0) }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700 md:col-span-2">Inventory Link Notes
            <input name="inventory_link_notes" value="{{ $value('inventory_link_notes') }}" class="mt-1 w-full rounded-md border-gray-300" placeholder="Feeds, seeds, fertilizer stock consumed by this budget">
        </label>
    </div>
</section>

<section>
    <h2 class="mb-4 text-lg font-semibold text-gray-900">Budget Categories & Amounts</h2>
    <div class="grid gap-4 md:grid-cols-4">
        <label class="block text-sm font-medium text-gray-700">Main Category
            <input name="category" value="{{ $value('category') }}" class="mt-1 w-full rounded-md border-gray-300" placeholder="fertilizer, feed, labor">
        </label>
        <label class="block text-sm font-medium text-gray-700">Total Planned Amount
            <input type="number" step="0.01" name="planned_amount" value="{{ $value('planned_amount') }}" class="mt-1 w-full rounded-md border-gray-300" required>
        </label>
        <label class="block text-sm font-medium text-gray-700">Actual Override
            <input type="number" step="0.01" name="actual_amount_override" value="{{ $value('actual_amount_override') }}" class="mt-1 w-full rounded-md border-gray-300" placeholder="Leave blank to use expenses">
        </label>
        <label class="block text-sm font-medium text-gray-700">Forecast Amount
            <input type="number" step="0.01" name="forecast_amount" value="{{ $value('forecast_amount') }}" class="mt-1 w-full rounded-md border-gray-300">
        </label>
    </div>
    <label class="mt-4 block text-sm font-medium text-gray-700">Budget Line Items
        <textarea name="line_items_text" rows="5" class="mt-1 w-full rounded-md border-gray-300" placeholder="Seeds & planting | 12000 | Manager | Certified maize seed&#10;Labor | 18000 | Worker | Land prep and weeding">{{ $lineItemsText }}</textarea>
    </label>
</section>

<section>
    <h2 class="mb-4 text-lg font-semibold text-gray-900">Dates, Alerts & Controls</h2>
    <div class="grid gap-4 md:grid-cols-4">
        <label class="block text-sm font-medium text-gray-700">Start Date
            <input type="date" name="start_date" value="{{ old('start_date', $budget?->start_date?->toDateString() ?? now()->toDateString()) }}" class="mt-1 w-full rounded-md border-gray-300" required>
        </label>
        <label class="block text-sm font-medium text-gray-700">End Date
            <input type="date" name="end_date" value="{{ old('end_date', $budget?->end_date?->toDateString() ?? now()->addMonths(4)->toDateString()) }}" class="mt-1 w-full rounded-md border-gray-300" required>
        </label>
        <label class="block text-sm font-medium text-gray-700">Hard Alert Threshold %
            <input type="number" step="0.01" name="alert_threshold_percent" value="{{ $value('alert_threshold_percent', 100) }}" class="mt-1 w-full rounded-md border-gray-300" required>
        </label>
        <label class="block text-sm font-medium text-gray-700">Alert Steps
            <input name="alert_thresholds_text" value="{{ $thresholdsText }}" class="mt-1 w-full rounded-md border-gray-300" placeholder="50,80,100">
        </label>
        <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
            <input type="checkbox" name="allow_overspend" value="1" class="rounded border-gray-300" @checked(old('allow_overspend', $budget?->allow_overspend ?? true))>
            Allow overspending after warning
        </label>
    </div>
</section>

<section>
    <h2 class="mb-4 text-lg font-semibold text-gray-900">Smart Budget Intelligence</h2>
    <div class="grid gap-4 md:grid-cols-3">
        <label class="block text-sm font-medium text-gray-700">Expected Income
            <input type="number" step="0.01" name="expected_income" value="{{ $value('expected_income') }}" class="mt-1 w-full rounded-md border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Generated Profit
            <input type="number" step="0.01" name="generated_profit" value="{{ $value('generated_profit') }}" class="mt-1 w-full rounded-md border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Market Price Assumption
            <input type="number" step="0.01" name="market_price_assumption" value="{{ $value('market_price_assumption') }}" class="mt-1 w-full rounded-md border-gray-300">
        </label>
    </div>
    <label class="mt-4 block text-sm font-medium text-gray-700">AI Suggestions / Forecast Notes
        <textarea name="ai_suggestions" rows="3" class="mt-1 w-full rounded-md border-gray-300" placeholder="Labor budget may be low for this farm size. Fertilizer costs could spike next month.">{{ $value('ai_suggestions') }}</textarea>
    </label>
    <label class="mt-4 block text-sm font-medium text-gray-700">Cost Efficiency Insights
        <textarea name="cost_efficiency_notes" rows="3" class="mt-1 w-full rounded-md border-gray-300" placeholder="Tomatoes use more budget than cabbage but yield higher profit.">{{ $value('cost_efficiency_notes') }}</textarea>
    </label>
    <label class="mt-4 block text-sm font-medium text-gray-700">What-If Simulator Scenarios
        <textarea name="what_if_scenarios_text" rows="3" class="mt-1 w-full rounded-md border-gray-300" placeholder="Fertilizer price rises by 15%&#10;Reduce labor by 10%">{{ $whatIfText }}</textarea>
    </label>
</section>

<section>
    <h2 class="mb-4 text-lg font-semibold text-gray-900">Approvals, Adjustments & Proofs</h2>
    <div class="grid gap-4 md:grid-cols-3">
        <label class="block text-sm font-medium text-gray-700">Responsible Role
            <select name="responsible_role" class="mt-1 w-full rounded-md border-gray-300">
                <option value="">Unassigned</option>
                @foreach(['manager','worker','agronomist','vet','finance','owner'] as $role)<option value="{{ $role }}" @selected($value('responsible_role') === $role)>{{ Str::headline($role) }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Approval Status
            <select name="approval_status" class="mt-1 w-full rounded-md border-gray-300" required>
                @foreach(['draft','review','approved','rejected','adjusted'] as $status)<option value="{{ $status }}" @selected($value('approval_status', 'draft') === $status)>{{ Str::headline($status) }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Approved By
            <select name="approved_by" class="mt-1 w-full rounded-md border-gray-300">
                <option value="">Not approved</option>
                @foreach($users as $user)<option value="{{ $user->id }}" @selected($value('approved_by') == $user->id)>{{ $user->name }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700 md:col-span-3">Attach Receipts / Proofs
            <input type="file" name="documents[]" multiple class="mt-1 w-full rounded-md border-gray-300">
        </label>
    </div>
    @if(! empty($budget?->document_paths))
        <div class="mt-3 flex flex-wrap gap-2 text-sm">
            @foreach($budget->document_paths as $path)
                <a href="{{ Storage::url($path) }}" target="_blank" class="rounded-md bg-gray-100 px-3 py-1 text-gray-700 hover:bg-gray-200">Document {{ $loop->iteration }}</a>
            @endforeach
        </div>
    @endif
    <label class="mt-4 block text-sm font-medium text-gray-700">Adjustment History
        <textarea name="adjustment_log_text" rows="3" class="mt-1 w-full rounded-md border-gray-300" placeholder="2026-07-15: Increased fertilizer budget by KES 4,000">{{ $adjustmentText }}</textarea>
    </label>
    <label class="mt-4 block text-sm font-medium text-gray-700">Notes
        <textarea name="notes" rows="3" class="mt-1 w-full rounded-md border-gray-300">{{ $value('notes') }}</textarea>
    </label>
</section>
