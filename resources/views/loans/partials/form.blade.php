@php
    $loan = $loan ?? null;
    $value = fn ($key, $default = null) => old($key, $loan?->{$key} ?? $default);
    $paymentsText = old('payments_text', collect($loan?->payments ?? [])->map(fn ($p) => ($p['date'] ?? '').' | '.($p['amount'] ?? '').' | '.($p['method'] ?? '').' | '.($p['reference'] ?? ''))->implode("\n"));
    $membersText = old('group_loan_members_text', implode("\n", $loan?->group_loan_members ?? []));
@endphp

@if($errors->any())
    <div class="rounded-md bg-rose-50 p-3 text-sm text-rose-700">{{ $errors->first() }}</div>
@endif

<section>
    <h2 class="mb-4 text-lg font-semibold text-gray-900">Loan Profile</h2>
    <div class="grid gap-4 md:grid-cols-3">
        <label class="block text-sm font-medium text-gray-700">Farm
            <select name="farm_id" class="mt-1 w-full rounded-md border-gray-300" required>
                <option value="">Select farm</option>
                @foreach($farms as $farm)<option value="{{ $farm->id }}" @selected($value('farm_id') == $farm->id)>{{ $farm->name }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Loan Name / Purpose
            <input name="loan_name" value="{{ $value('loan_name') }}" class="mt-1 w-full rounded-md border-gray-300" placeholder="Fertilizer Loan 2026">
        </label>
        <label class="block text-sm font-medium text-gray-700">Purpose Tag
            <select name="purpose_tag" class="mt-1 w-full rounded-md border-gray-300">
                <option value="">Select purpose</option>
                @foreach(['crop_input' => 'Crop input', 'livestock' => 'Livestock', 'equipment' => 'Equipment', 'working_capital' => 'Working capital', 'other' => 'Other'] as $key => $label)
                    <option value="{{ $key }}" @selected($value('purpose_tag') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Lender Name
            <input name="lender_name" value="{{ $value('lender_name') }}" class="mt-1 w-full rounded-md border-gray-300" required>
        </label>
        <label class="block text-sm font-medium text-gray-700">Lender Type
            <select name="lender_type" class="mt-1 w-full rounded-md border-gray-300">
                <option value="">Select lender type</option>
                @foreach(['bank' => 'Bank', 'sacco' => 'SACCO', 'individual' => 'Individual', 'cooperative' => 'Cooperative', 'other' => 'Other'] as $key => $label)
                    <option value="{{ $key }}" @selected($value('lender_type') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Lender Contact
            <input name="lender_contact" value="{{ $value('lender_contact') }}" class="mt-1 w-full rounded-md border-gray-300" placeholder="Phone, email, branch">
        </label>
    </div>
</section>

<section>
    <h2 class="mb-4 text-lg font-semibold text-gray-900">Interest Engine & Terms</h2>
    <div class="grid gap-4 md:grid-cols-4">
        <label class="block text-sm font-medium text-gray-700">Principal Amount (KES)
            <input type="number" step="0.01" name="principal_amount" value="{{ $value('principal_amount') }}" class="mt-1 w-full rounded-md border-gray-300" required>
        </label>
        <label class="block text-sm font-medium text-gray-700">Interest Rate %
            <input type="number" step="0.01" name="interest_rate" value="{{ $value('interest_rate', 0) }}" class="mt-1 w-full rounded-md border-gray-300" required>
        </label>
        <label class="block text-sm font-medium text-gray-700">Interest Method
            <select name="interest_method" class="mt-1 w-full rounded-md border-gray-300" required>
                @foreach(['flat' => 'Flat rate', 'reducing_balance' => 'Reducing balance', 'compound' => 'Compound'] as $key => $label)
                    <option value="{{ $key }}" @selected($value('interest_method', 'flat') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Duration Months
            <input type="number" name="duration_months" value="{{ $value('duration_months', 12) }}" class="mt-1 w-full rounded-md border-gray-300" required>
        </label>
        <label class="block text-sm font-medium text-gray-700">Start Date
            <input type="date" name="loan_date" value="{{ old('loan_date', $loan?->loan_date?->toDateString() ?? now()->toDateString()) }}" class="mt-1 w-full rounded-md border-gray-300" required>
        </label>
        <label class="block text-sm font-medium text-gray-700">Final Due Date
            <input type="date" name="due_date" value="{{ old('due_date', $loan?->due_date?->toDateString()) }}" class="mt-1 w-full rounded-md border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Repayment Frequency
            <select name="repayment_frequency" class="mt-1 w-full rounded-md border-gray-300">
                <option value="">Select frequency</option>
                @foreach(['weekly','monthly','seasonal','annual','one_time'] as $frequency)<option value="{{ $frequency }}" @selected($value('repayment_frequency') === $frequency)>{{ Str::headline($frequency) }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Status
            <select name="status" class="mt-1 w-full rounded-md border-gray-300" required>
                @foreach(['active','paid','defaulted','restructured'] as $status)<option value="{{ $status }}" @selected($value('status', 'active') === $status)>{{ Str::headline($status) }}</option>@endforeach
            </select>
        </label>
    </div>
</section>

<section>
    <h2 class="mb-4 text-lg font-semibold text-gray-900">Payments, Reminders & Fees</h2>
    <div class="grid gap-4 md:grid-cols-4">
        <label class="block text-sm font-medium text-gray-700">Amount Repaid (KES)
            <input type="number" step="0.01" name="amount_repaid" value="{{ $value('amount_repaid', 0) }}" class="mt-1 w-full rounded-md border-gray-300" required>
        </label>
        <label class="block text-sm font-medium text-gray-700">Payment Method
            <select name="payment_method" class="mt-1 w-full rounded-md border-gray-300">
                <option value="">Select method</option>
                @foreach(['mpesa' => 'M-Pesa', 'cash' => 'Cash', 'bank' => 'Bank', 'cheque' => 'Cheque', 'other' => 'Other'] as $key => $label)
                    <option value="{{ $key }}" @selected($value('payment_method') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Late Penalty %
            <input type="number" step="0.01" name="late_penalty_rate" value="{{ $value('late_penalty_rate', 0) }}" class="mt-1 w-full rounded-md border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Extra Charges
            <input type="number" step="0.01" name="extra_charges" value="{{ $value('extra_charges', 0) }}" class="mt-1 w-full rounded-md border-gray-300">
        </label>
    </div>
    <label class="mt-4 block text-sm font-medium text-gray-700">Payment Records
        <textarea name="payments_text" rows="4" class="mt-1 w-full rounded-md border-gray-300" placeholder="2026-07-01 | 5000 | mpesa | QH123ABC">{{ $paymentsText }}</textarea>
    </label>
</section>

<section>
    <h2 class="mb-4 text-lg font-semibold text-gray-900">Farm Links & Cost Allocation</h2>
    <div class="grid gap-4 md:grid-cols-3">
        <label class="block text-sm font-medium text-gray-700">Crop Cycle
            <select name="crop_cycle_id" class="mt-1 w-full rounded-md border-gray-300">
                <option value="">None</option>
                @foreach($cropCycles as $cycle)<option value="{{ $cycle->id }}" @selected($value('crop_cycle_id') == $cycle->id)>{{ $cycle->name ?? 'Crop Cycle #'.$cycle->id }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Livestock Project
            <select name="livestock_id" class="mt-1 w-full rounded-md border-gray-300">
                <option value="">None</option>
                @foreach($livestock as $animal)<option value="{{ $animal->id }}" @selected($value('livestock_id') == $animal->id)>{{ $animal->tag_number }} {{ $animal->name ? '- '.$animal->name : '' }}</option>@endforeach
            </select>
        </label>
        <label class="block text-sm font-medium text-gray-700">Farm Income Snapshot
            <input type="number" step="0.01" name="farm_income_snapshot" value="{{ $value('farm_income_snapshot') }}" class="mt-1 w-full rounded-md border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Inputs Allocation
            <input type="number" step="0.01" name="allocation_inputs" value="{{ $value('allocation_inputs', 0) }}" class="mt-1 w-full rounded-md border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Labor Allocation
            <input type="number" step="0.01" name="allocation_labor" value="{{ $value('allocation_labor', 0) }}" class="mt-1 w-full rounded-md border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Equipment Allocation
            <input type="number" step="0.01" name="allocation_equipment" value="{{ $value('allocation_equipment', 0) }}" class="mt-1 w-full rounded-md border-gray-300">
        </label>
    </div>
</section>

<section>
    <h2 class="mb-4 text-lg font-semibold text-gray-900">ROI, Simulation, Documents & Group Loan</h2>
    <div class="grid gap-4 md:grid-cols-3">
        <label class="block text-sm font-medium text-gray-700">Expected Profit
            <input type="number" step="0.01" name="expected_profit" value="{{ $value('expected_profit') }}" class="mt-1 w-full rounded-md border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Generated Profit
            <input type="number" step="0.01" name="generated_profit" value="{{ $value('generated_profit') }}" class="mt-1 w-full rounded-md border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700">Extra Early Payment
            <input type="number" step="0.01" name="early_repayment_extra" value="{{ $value('early_repayment_extra') }}" class="mt-1 w-full rounded-md border-gray-300">
        </label>
        <label class="block text-sm font-medium text-gray-700 md:col-span-3">Documents
            <input type="file" name="documents[]" multiple class="mt-1 w-full rounded-md border-gray-300">
        </label>
    </div>
    @if(! empty($loan?->document_paths))
        <div class="mt-3 flex flex-wrap gap-2 text-sm">
            @foreach($loan->document_paths as $path)
                <a href="{{ Storage::url($path) }}" target="_blank" class="rounded-md bg-gray-100 px-3 py-1 text-gray-700 hover:bg-gray-200">Document {{ $loop->iteration }}</a>
            @endforeach
        </div>
    @endif
    <label class="mt-4 block text-sm font-medium text-gray-700">Group Loan Members / Contributions
        <textarea name="group_loan_members_text" rows="3" class="mt-1 w-full rounded-md border-gray-300" placeholder="Mary - KES 10,000">{{ $membersText }}</textarea>
    </label>
    <label class="mt-4 block text-sm font-medium text-gray-700">Loan Comparison Notes
        <textarea name="comparison_notes" rows="2" class="mt-1 w-full rounded-md border-gray-300">{{ $value('comparison_notes') }}</textarea>
    </label>
    <label class="mt-4 block text-sm font-medium text-gray-700">Notes
        <textarea name="notes" rows="3" class="mt-1 w-full rounded-md border-gray-300">{{ $value('notes') }}</textarea>
    </label>
</section>
