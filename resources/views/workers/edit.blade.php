@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Edit Worker: {{ $worker->fullName() }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('workers.update', $worker) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name *</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                        id="first_name" name="first_name" value="{{ old('first_name', $worker->first_name) }}" required>
                                    @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name *</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                        id="last_name" name="last_name" value="{{ old('last_name', $worker->last_name) }}" required>
                                    @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                        id="phone" name="phone" value="{{ old('phone', $worker->phone) }}">
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="national_id" class="form-label">National ID</label>
                                    <input type="text" class="form-control @error('national_id') is-invalid @enderror" 
                                        id="national_id" name="national_id" value="{{ old('national_id', $worker->national_id) }}">
                                    @error('national_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $worker->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $worker->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $worker->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                        id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $worker->date_of_birth?->toDateString()) }}">
                                    @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role *</label>
                                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                        <option value="">Select Role</option>
                                        <option value="general_worker" {{ old('role', $worker->role) == 'general_worker' ? 'selected' : '' }}>General Worker</option>
                                        <option value="supervisor" {{ old('role', $worker->role) == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                        <option value="technician" {{ old('role', $worker->role) == 'technician' ? 'selected' : '' }}>Technician</option>
                                        <option value="driver" {{ old('role', $worker->role) == 'driver' ? 'selected' : '' }}>Driver</option>
                                        <option value="harvester" {{ old('role', $worker->role) == 'harvester' ? 'selected' : '' }}>Harvester</option>
                                        <option value="planting" {{ old('role', $worker->role) == 'planting' ? 'selected' : '' }}>Planting</option>
                                        <option value="irrigation" {{ old('role', $worker->role) == 'irrigation' ? 'selected' : '' }}>Irrigation</option>
                                    </select>
                                    @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="hire_date" class="form-label">Hire Date *</label>
                                    <input type="date" class="form-control @error('hire_date') is-invalid @enderror" 
                                        id="hire_date" name="hire_date" value="{{ old('hire_date', $worker->hire_date->toDateString()) }}" required>
                                    @error('hire_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="daily_wage" class="form-label">Daily Wage *</label>
                                    <input type="number" step="0.01" class="form-control @error('daily_wage') is-invalid @enderror" 
                                        id="daily_wage" name="daily_wage" value="{{ old('daily_wage', $worker->daily_wage) }}" required>
                                    @error('daily_wage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="payment_type" class="form-label">Payment Type *</label>
                                    <select class="form-select @error('payment_type') is-invalid @enderror" id="payment_type" name="payment_type" required>
                                        <option value="daily" {{ old('payment_type', $worker->payment_type) == 'daily' ? 'selected' : '' }}>Daily</option>
                                        <option value="weekly" {{ old('payment_type', $worker->payment_type) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                        <option value="monthly" {{ old('payment_type', $worker->payment_type) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="piece_rate" {{ old('payment_type', $worker->payment_type) == 'piece_rate' ? 'selected' : '' }}>Piece Rate</option>
                                    </select>
                                    @error('payment_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="active" {{ old('status', $worker->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $worker->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="terminated" {{ old('status', $worker->status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                        id="address" name="address" rows="2">{{ old('address', $worker->address) }}</textarea>
                                    @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                        id="notes" name="notes" rows="2">{{ old('notes', $worker->notes) }}</textarea>
                                    @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5>Emergency Contact</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="emergency_contact" class="form-label">Contact Name</label>
                                    <input type="text" class="form-control @error('emergency_contact') is-invalid @enderror" 
                                        id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact', $worker->emergency_contact) }}">
                                    @error('emergency_contact')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="emergency_phone" class="form-label">Contact Phone</label>
                                    <input type="text" class="form-control @error('emergency_phone') is-invalid @enderror" 
                                        id="emergency_phone" name="emergency_phone" value="{{ old('emergency_phone', $worker->emergency_phone) }}">
                                    @error('emergency_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('workers.show', $worker) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Update Worker
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
