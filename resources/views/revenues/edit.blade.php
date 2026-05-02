@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Edit Revenue</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('revenues.update', $revenue) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Farm *</label>
                                    <select name="farm_id" class="form-select" required>
                                        <option value="">Select Farm</option>
                                        @foreach(\App\Models\Farm::all() as $farm)
                                        <option value="{{ $farm->id }}" {{ $revenue->farm_id == $farm->id ? 'selected' : '' }}>
                                            {{ $farm->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                        <div class="mb-3">
                            <label for="livestock_id" class="form-label">Livestock (for livestock sales)</label>
                            <select class="form-control" id="livestock_id" name="livestock_id">
                                <option value="">-- Select Livestock --</option>
                                @foreach($livestock ?? [] as $animal)
                                    <option value="{{ $animal->id }}" {{ old('livestock_id', $revenue->livestock_id) == $animal->id ? 'selected' : '' }}>
                                        {{ $animal->tag_number ?? 'Untagged' }} - {{ $animal->name ?? 'Unnamed' }} ({{ $animal->type->name ?? 'Unknown' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Buyer (Optional)</label>
                                    <select name="buyer_id" class="form-select">
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
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Amount *</label>
                                    <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount', $revenue->amount) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Sale Date *</label>
                                    <input type="date" name="sale_date" class="form-control" value="{{ old('sale_date', $revenue->sale_date->format('Y-m-d')) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Payment Status</label>
                                    <select name="payment_status" class="form-select">
                                        <option value="pending" {{ $revenue->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="partial" {{ $revenue->payment_status === 'partial' ? 'selected' : '' }}>Partial</option>
                                        <option value="paid" {{ $revenue->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Quantity Sold</label>
                                    <input type="number" step="0.01" name="quantity_sold" class="form-control" value="{{ old('quantity_sold', $revenue->quantity_sold) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Unit</label>
                                    <input type="text" name="unit" class="form-control" value="{{ old('unit', $revenue->unit) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Price per Unit</label>
                                    <input type="number" step="0.01" name="price_per_unit" class="form-control" value="{{ old('price_per_unit', $revenue->price_per_unit) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Invoice Number</label>
                                    <input type="text" name="invoice_number" class="form-control" value="{{ old('invoice_number', $revenue->invoice_number) }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Payment Method</label>
                                    <input type="text" name="payment_method" class="form-control" value="{{ old('payment_method', $revenue->payment_method) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Payment Date</label>
                                    <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', $revenue->payment_date?->format('Y-m-d')) }}">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $revenue->notes) }}</textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('revenues.show', $revenue) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Revenue</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
