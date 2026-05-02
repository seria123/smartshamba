@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Edit Expense</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('expenses.update', $expense) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Farm *</label>
                                    <select name="farm_id" class="form-select" required>
                                        <option value="">Select Farm</option>
                                        @foreach(\App\Models\Farm::all() as $farm)
                                        <option value="{{ $farm->id }}" {{ $expense->farm_id == $farm->id ? 'selected' : '' }}>
                                            {{ $farm->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Crop (Optional)</label>
                                    <select name="crop_id" class="form-select">
                                        <option value="">Select Crop</option>
                                        @foreach(\App\Models\Crop::all() as $crop)
                                        <option value="{{ $crop->id }}" {{ $expense->crop_id == $crop->id ? 'selected' : '' }}>
                                            {{ $crop->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Expense Type *</label>
                                    <select name="expense_type" class="form-select" required>
                                        <option value="">Select Type</option>
                                        <option value="inputs" {{ $expense->expense_type === 'inputs' ? 'selected' : '' }}>Inputs</option>
                                        <option value="labor" {{ $expense->expense_type === 'labor' ? 'selected' : '' }}>Labor</option>
                                        <option value="equipment" {{ $expense->expense_type === 'equipment' ? 'selected' : '' }}>Equipment</option>
                                        <option value="fertilizer" {{ $expense->expense_type === 'fertilizer' ? 'selected' : '' }}>Fertilizer</option>
                                        <option value="seeds" {{ $expense->expense_type === 'seeds' ? 'selected' : '' }}>Seeds</option>
                                        <option value="pesticides" {{ $expense->expense_type === 'pesticides' ? 'selected' : '' }}>Pesticides</option>
                                        <option value="fuel" {{ $expense->expense_type === 'fuel' ? 'selected' : '' }}>Fuel</option>
                                        <option value="maintenance" {{ $expense->expense_type === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="transport" {{ $expense->expense_type === 'transport' ? 'selected' : '' }}>Transport</option>
                                        <option value="other" {{ $expense->expense_type === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Description *</label>
                                    <input type="text" name="description" class="form-control" value="{{ old('description', $expense->description) }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Amount (KES) *</label>
                                    <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount', $expense->amount) }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Date *</label>
                                    <input type="date" name="expense_date" class="form-control" value="{{ $expense->expense_date->format('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Category</label>
                                    <select name="category" class="form-select">
                                        <option value="crop_production" {{ $expense->category === 'crop_production' ? 'selected' : '' }}>Crop Production</option>
                                        <option value="livestock" {{ $expense->category === 'livestock' ? 'selected' : '' }}>Livestock</option>
                                        <option value="operations" {{ $expense->category === 'operations' ? 'selected' : '' }}>Operations</option>
                                        <option value="administrative" {{ $expense->category === 'administrative' ? 'selected' : '' }}>Administrative</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Payment Method</label>
                                    <select name="payment_method" class="form-select">
                                        <option value="">Select Method</option>
                                        <option value="cash" {{ $expense->payment_method === 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="bank_transfer" {{ $expense->payment_method === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="mobile_money" {{ $expense->payment_method === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                        <option value="cheque" {{ $expense->payment_method === 'cheque' ? 'selected' : '' }}>Cheque</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Receipt Number</label>
                                    <input type="text" name="receipt_number" class="form-control" value="{{ old('receipt_number', $expense->receipt_number) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Worker (Optional)</label>
                                    <select name="worker_id" class="form-select">
                                        <option value="">Select Worker</option>
                                        @foreach(\App\Models\Worker::all() as $worker)
                                        <option value="{{ $worker->id }}" {{ $expense->worker_id == $worker->id ? 'selected' : '' }}>
                                            {{ $worker->fullName() }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $expense->notes) }}</textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('expenses.show', $expense) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Expense</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection