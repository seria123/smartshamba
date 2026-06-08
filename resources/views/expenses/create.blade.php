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
                    <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
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
                                    <label class="form-label">Animal (Optional)</label>
                                    <select name="livestock_id" class="form-select">
                                        <option value="">Select Animal</option>
                                        @foreach(\App\Models\Livestock::all() as $animal)
                                        <option value="{{ $animal->id }}" {{ old('livestock_id') == $animal->id ? 'selected' : '' }}>
                                            {{ $animal->tag_number }} {{ $animal->name ? '- '.$animal->name : '' }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Staff (Optional - for Labor expenses)</label>
                                    @php
                                        $staffCount = \App\Models\Staff::count();
                                    @endphp
                                    @if($staffCount == 0)
                                        <div class="alert alert-info py-2">
                                            <i class="fas fa-info-circle me-2"></i>
                                            No staff added yet.
                                            <a href="{{ route('staff.create') }}" class="alert-link">Add staff first</a> before assigning to labor expenses.
                                        </div>
                                    @endif
                                    <select name="staff_id" class="form-select" {{ $staffCount == 0 ? 'disabled' : '' }}>
                                        <option value="">Select Staff</option>
                                        @foreach(\App\Models\Staff::all() as $worker)
                                        <option value="{{ $worker->id }}" {{ old('staff_id') == $worker->id ? 'selected' : '' }}>
                                            {{ $worker->fullName() }} - {{ $worker->role }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @if($staffCount > 0)
                                        <small class="text-muted">Manage staff in <a href="{{ route('staff.index') }}">Staff section</a></small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Expense Type *</label>
                                    <div class="border rounded p-3 bg-white" style="max-height: 250px; overflow-y: auto;">
                                        @php
                                            $allTypes = [
                                                        'inputs' => 'Inputs',
                                                        'labor' => 'Labor',
                                                        'equipment' => 'Equipment',
                                                        'fertilizer' => 'Fertilizer',
                                                        'seeds' => 'Seeds',
                                                        'pesticides' => 'Pesticides',
                                                        'animal_feed' => 'Animal Feed',
                                                        'veterinary' => 'Veterinary',
                                                        'fuel' => 'Fuel',
                                                        'maintenance' => 'Maintenance',
                                                        'transport' => 'Transport',
                                                        'utilities' => 'Utilities',
                                                        'other' => 'Other'
                                            ];
                                        @endphp
                                        @foreach($allTypes as $value => $label)
                                            <div class="form-check form-check-sm mb-2">
                                                <input class="form-check-input" type="checkbox"
                                                       name="expense_type[]"
                                                       value="{{ $value }}"
                                                       id="expense_type_{{ $value }}"
                                                       {{ is_array(old('expense_type')) && in_array($value, old('expense_type')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="expense_type_{{ $value }}">
                                                    {{ $label }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
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
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Receipt / Photo</label>
                                    <input type="file" name="receipt" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mt-4">
                                    <input type="hidden" name="is_recurring" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_recurring" value="1" id="is_recurring" {{ old('is_recurring') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_recurring">Recurring expense</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Recurrence</label>
                                    <select name="recurrence_interval" class="form-select">
                                        <option value="">Select interval</option>
                                        @foreach(['weekly','monthly','seasonal','yearly'] as $interval)
                                        <option value="{{ $interval }}" {{ old('recurrence_interval') === $interval ? 'selected' : '' }}>{{ Str::headline($interval) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Next Due Date</label>
                                    <input type="date" name="next_due_date" class="form-control" value="{{ old('next_due_date') }}">
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
