@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Create Expense</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('expenses.store') }}" method="POST">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Farm *</label>
                                    <select name="farm_id" class="form-select" required>
                                        <option value="">Select Farm</option>
                                        @foreach(\App\Models\Farm::all() as $farm)
                                        <option value="{{ $farm->id }}">{{ $farm->name }}</option>
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
                                        <option value="{{ $crop->id }}">{{ $crop->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Expense Type *</label>
                                    <select name="expense_type" class="form-select" required>
                                        <option value="">Select Type</option>
                                        <option value="inputs">Inputs</option>
                                        <option value="labor">Labor</option>
                                        <option value="equipment">Equipment</option>
                                        <option value="fertilizer">Fertilizer</option>
                                        <option value="seeds">Seeds</option>
                                        <option value="pesticides">Pesticides</option>
                                        <option value="fuel">Fuel</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="transport">Transport</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Description *</label>
                                    <input type="text" name="description" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Amount (KES) *</label>
                                    <input type="number" step="0.01" name="amount" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Date *</label>
                                    <input type="date" name="expense_date" class="form-control" value="{{ now()->toDateString() }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Category</label>
                                    <select name="category" class="form-select">
                                        <option value="crop_production">Crop Production</option>
                                        <option value="livestock">Livestock</option>
                                        <option value="operations">Operations</option>
                                        <option value="administrative">Administrative</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Payment Method</label>
                                    <select name="payment_method" class="form-select">
                                        <option value="">Select Method</option>
                                        <option value="cash">Cash</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="mobile_money">Mobile Money</option>
                                        <option value="cheque">Cheque</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Receipt Number</label>
                                    <input type="text" name="receipt_number" class="form-control">
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
                                        <option value="{{ $worker->id }}">{{ $worker->fullName() }} - {{ $worker->role }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Expense</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection