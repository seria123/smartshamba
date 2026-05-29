@extends('layouts.MainLayout')

@section('content')
<x-ui.card>
    <form action="{{ route('orders.store') }}" method="POST">
        @csrf
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Create New Order</h1>
            <a href="{{ route('orders.index') }}" class="inline-flex items-center space-x-2 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-2"></i>
                <span>Back to List</span>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="form-label">Farm *</label>
                <select name="farm_id" class="form-select-modern" required>
                    <option value="">Select Farm</option>
                    @foreach(\App\Models\Farm::all() as $farm)
                    <option value="{{ $farm->id }}">{{ $farm->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Buyer *</label>
                <select name="buyer_id" class="form-select-modern" required>
                    <option value="">Select Buyer</option>
                    @foreach($buyers as $buyer)
                    <option value="{{ $buyer->id }}">{{ $buyer->name }} {{ $buyer->company_name ? '(' . $buyer->company_name . ')' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Crop (Optional)</label>
                <select name="crop_id" class="form-select-modern">
                    <option value="">Select Crop</option>
                    @foreach($crops as $crop)
                    <option value="{{ $crop->id }}">{{ $crop->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
            <div>
                <label class="form-label">Order Date *</label>
                <input type="date" name="order_date" class="form-input-modern" value="{{ now()->toDateString() }}" required>
            </div>
            <div>
                <label class="form-label">Delivery Date</label>
                <input type="date" name="delivery_date" class="form-input-modern">
            </div>
            <div>
                <label class="form-label">Delivery Address</label>
                <input type="text" name="delivery_address" class="form-input-modern">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-4">
            <div>
                <label class="form-label">Quantity *</label>
                <input type="number" step="0.01" name="quantity" class="form-input-modern" required>
            </div>
            <div>
                <label class="form-label">Unit *</label>
                <input type="text" name="unit" class="form-input-modern" placeholder="e.g. kg, tons" required>
            </div>
            <div>
                <label class="form-label">Price per Unit *</label>
                <input type="number" step="0.01" name="price_per_unit" class="form-input-modern" required>
            </div>
            <div>
                <label class="form-label">Total Amount</label>
                <input type="text" class="form-input-modern" id="total_amount_display" readonly>
            </div>
        </div>
        <div class="mt-4">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-textarea-modern" rows="3"></textarea>
        </div>
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Create Order</button>
        </div>
    </form>
</x-ui.card>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.querySelector('input[name="quantity"]');
        const priceInput = document.querySelector('input[name="price_per_unit"]');
        const totalDisplay = document.getElementById('total_amount_display');

        function calculateTotal() {
            const qty = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            totalDisplay.value = (qty * price).toFixed(2);
        }

        quantityInput.addEventListener('input', calculateTotal);
        priceInput.addEventListener('input', calculateTotal);
    });
</script>
@endpush
