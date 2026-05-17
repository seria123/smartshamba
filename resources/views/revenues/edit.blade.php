@extends('layouts.MainLayout')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex flex-col gap-4">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h1 class="text-2xl font-bold mb-0">Edit Revenue</h1>
            <a href="{{ route('revenues.index') }}" class="btn btn-outline-secondary whitespace-nowrap">
                <i class="fas fa-arrow-left me-2"></i> Back to List
            </a>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 p-3 rounded">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form Card --}}
        <div class="card-modern p-4 sm:p-6">
            <form action="{{ route('revenues.update', $revenue) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Transaction Details --}}
                <div>
                    <h5 class="text-gray-800 fw-bold mb-3 flex items-center gap-2">
                        <i class="fas fa-exchange-alt text-emerald-600"></i> Transaction Details
                    </h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="farm_id" class="form-label">Farm <span class="text-danger">*</span></label>
                            <select name="farm_id" class="form-select-modern" required>
                                <option value="">Select Farm</option>
                                @foreach(\App\Models\Farm::all() as $farm)
                                <option value="{{ $farm->id }}" {{ $revenue->farm_id == $farm->id ? 'selected' : '' }}>
                                    {{ $farm->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="livestock_id" class="form-label">Livestock (for livestock sales)</label>
                            <select class="form-select-modern" id="livestock_id" name="livestock_id">
                                <option value="">-- Select Livestock --</option>
                                @foreach($livestock ?? [] as $animal)
                                    <option value="{{ $animal->id }}" {{ old('livestock_id', $revenue->livestock_id) == $animal->id ? 'selected' : '' }}>
                                        {{ $animal->tag_number ?? 'Untagged' }} - {{ $animal->name ?? 'Unnamed' }} ({{ $animal->type->name ?? 'Unknown' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="buyer_id" class="form-label">Buyer (Optional)</label>
                            <select name="buyer_id" class="form-select-modern">
                                <option value="">Select Buyer</option>
                                @foreach(\App\Models\Buyer::all() as $buyer)
                                <option value="{{ $buyer->id }}" {{ $revenue->buyer_id == $buyer->id ? 'selected' : '' }}>
                                    {{ $buyer->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Financial Details --}}
                <div>
                    <h5 class="text-gray-800 fw-bold mb-3 flex items-center gap-2">
                        <i class="fas fa-dollar-sign text-amber-600"></i> Financial Details
                    </h5>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="amount" class="form-label">Amount (KES) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">KES</span>
                                <input type="number" step="0.01" name="amount"
                                    class="form-input-modern @error('amount') is-invalid @enderror"
                                    value="{{ old('amount', $revenue->amount) }}" required>
                            </div>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="quantity_sold" class="form-label">Quantity Sold</label>
                            <input type="number" step="0.01" name="quantity_sold" id="quantity_sold"
                                class="form-input-modern" value="{{ old('quantity_sold', $revenue->quantity_sold) }}" placeholder="e.g. 50">
                        </div>
                        <div>
                            <label for="unit" class="form-label">Unit</label>
                            <input type="text" name="unit" id="unit" list="unit-suggestions"
                                class="form-input-modern" value="{{ old('unit', $revenue->unit) }}" placeholder="kg, tons, bags...">
                            <datalist id="unit-suggestions">
                                <option value="kg"><option value="grams"><option value="tons">
                                <option value="bags"><option value="sacks"><option value="cartons">
                                <option value="liters"><option value="ml"><option value="pieces">
                                <option value="bunches">
                            </datalist>
                        </div>
                        <div>
                            <label for="price_per_unit" class="form-label">Price per Unit (KES)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">KES</span>
                                <input type="number" step="0.01" name="price_per_unit" id="price_per_unit"
                                    class="form-input-modern" value="{{ old('price_per_unit', $revenue->price_per_unit) }}"
                                    placeholder="Auto-calculated">
                            </div>
                            <small id="price-hint" class="text-muted">Will auto-calc if left empty</small>
                        </div>
                    </div>
                </div>

                {{-- Sale Date & Payment --}}
                <div>
                    <h5 class="text-gray-800 fw-bold mb-3 flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-blue-600"></i> Sale & Payment
                    </h5>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="sale_date" class="form-label">Sale Date <span class="text-danger">*</span></label>
                            <input type="date" name="sale_date"
                                class="form-input-modern @error('sale_date') is-invalid @enderror"
                                value="{{ old('sale_date', $revenue->sale_date?->format('Y-m-d')) }}" required>
                            @error('sale_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="invoice_number" class="form-label">Invoice Number</label>
                            <input type="text" name="invoice_number" id="invoice_number"
                                class="form-input-modern" value="{{ old('invoice_number', $revenue->invoice_number) }}"
                                placeholder="INV-2026-001">
                        </div>
                        <div>
                            <label for="payment_status" class="form-label">Payment Status</label>
                            <select name="payment_status" class="form-select-modern">
                                <option value="pending" {{ $revenue->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="partial" {{ $revenue->payment_status === 'partial' ? 'selected' : '' }}>Partial</option>
                                <option value="paid" {{ $revenue->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="overdue" {{ $revenue->payment_status === 'overdue' ? 'selected' : '' }}>Overdue</option>
                            </select>
                        </div>
                        <div>
                            <label for="payment_date" class="form-label">Payment Date</label>
                            <input type="date" name="payment_date"
                                class="form-input-modern" value="{{ old('payment_date', $revenue->payment_date?->format('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <input type="text" name="payment_method" id="payment_method"
                                class="form-input-modern" value="{{ old('payment_method', $revenue->payment_method) }}"
                                placeholder="Cash, Bank Transfer, Mobile Money...">
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div>
                    <h5 class="text-gray-800 fw-bold mb-3 flex items-center gap-2">
                        <i class="fas fa-sticky-note text-purple-600"></i> Notes
                    </h5>
                    <div>
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-textarea-modern @error('notes') is-invalid @enderror"
                            id="notes" name="notes" rows="3" placeholder="Additional notes...">{{ old('notes', $revenue->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('revenues.show', $revenue) }}" class="btn btn-outline-secondary text-center">
                        <i class="fas fa-times me-2"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary text-center">
                        <i class="fas fa-save me-2"></i> Update Revenue
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.querySelector('input[name="amount"]');
    const quantityInput = document.getElementById('quantity_sold');
    const priceInput = document.getElementById('price_per_unit');
    const priceHint = document.getElementById('price-hint');

    if (!amountInput || !quantityInput || !priceInput) return;

    function calculatePricePerUnit(force = false) {
        const amount = parseFloat(amountInput.value);
        const quantity = parseFloat(quantityInput.value);

        if (amount && quantity && quantity > 0) {
            const price = amount / quantity;
            if (force || priceInput.value === '') {
                priceInput.value = price.toFixed(2);
                if (priceHint) {
                    priceHint.textContent = 'Auto-calculated';
                    priceHint.classList.add('text-success');
                }
            }
        } else if (force) {
            priceInput.value = '';
            if (priceHint) {
                priceHint.textContent = 'Will auto-calc if left empty';
                priceHint.classList.remove('text-success');
            }
        }
    }

    amountInput.addEventListener('input', () => calculatePricePerUnit(true));
    quantityInput.addEventListener('input', () => calculatePricePerUnit(true));
    calculatePricePerUnit(false);
});
</script>
@endsection