@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Edit Buyer</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('buyers.update', $buyer) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Name *</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $buyer->name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Company Name</label>
                                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $buyer->company_name) }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $buyer->email) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Phone *</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $buyer->phone) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Buyer Type *</label>
                                    <select name="buyer_type" class="form-select" required>
                                        <option value="individual" {{ $buyer->buyer_type === 'individual' ? 'selected' : '' }}>Individual</option>
                                        <option value="retailer" {{ $buyer->buyer_type === 'retailer' ? 'selected' : '' }}>Retailer</option>
                                        <option value="wholesaler" {{ $buyer->buyer_type === 'wholesaler' ? 'selected' : '' }}>Wholesaler</option>
                                        <option value="processor" {{ $buyer->buyer_type === 'processor' ? 'selected' : '' }}>Processor</option>
                                        <option value="exporter" {{ $buyer->buyer_type === 'exporter' ? 'selected' : '' }}>Exporter</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address', $buyer->address) }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Credit Limit</label>
                                    <input type="number" step="0.01" name="credit_limit" class="form-control" value="{{ old('credit_limit', $buyer->credit_limit) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Rating (0-5)</label>
                                    <input type="number" min="0" max="5" step="0.1" name="rating" class="form-control" value="{{ old('rating', $buyer->rating) }}">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ $buyer->is_active ? 'checked' : '' }}>
                                <label class="form-check-label">Active</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $buyer->notes) }}</textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('buyers.show', $buyer) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Buyer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
