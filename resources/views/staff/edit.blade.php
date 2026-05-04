@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user-edit mr-2"></i> Edit Staff Member
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('staff.update', $staff) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Personal Information -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3"><i class="fas fa-id-card mr-2"></i>Personal Information</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label fw-bold">First Name *</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                           id="first_name" name="first_name" value="{{ old('first_name', $staff->first_name) }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label fw-bold">Last Name *</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                           id="last_name" name="last_name" value="{{ old('last_name', $staff->last_name) }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $staff->phone) }}" placeholder="+254 ...">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="national_id" class="form-label fw-bold">National ID / Passport *</label>
                                    <input type="text" class="form-control @error('national_id') is-invalid @enderror" 
                                           id="national_id" name="national_id" value="{{ old('national_id', $staff->national_id) }}" required>
                                    @small class="text-muted">Leave unchanged unless updating</small>
                                    @error('national_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $staff->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $staff->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $staff->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                           id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $staff->date_of_birth?->toDateString()) }}">
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Employment Details -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3"><i class="fas fa-briefcase mr-2"></i>Employment Details</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="farm_id" class="form-label fw-bold">Assign to Farm *</label>
                                    <select class="form-select @error('farm_id') is-invalid @enderror" id="farm_id" name="farm_id" required>
                                        <option value="">Select Farm</option>
                                        @foreach($farms as $farm)
                                            <option value="{{ $farm->id }}" {{ old('farm_id', $staff->farm_id) == $farm->id ? 'selected' : '' }}>
                                                {{ $farm->name }} ({{ $farm->location }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('farm_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="role" class="form-label fw-bold">Role / Position *</label>
                                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                        <option value="">Select Role</option>
                                        <option value="general_worker" {{ old('role', $staff->role) == 'general_worker' ? 'selected' : '' }}>General Worker</option>
                                        <option value="supervisor" {{ old('role', $staff->role) == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                        <option value="technician" {{ old('role', $staff->role) == 'technician' ? 'selected' : '' }}>Technician</option>
                                        <option value="driver" {{ old('role', $staff->role) == 'driver' ? 'selected' : '' }}>Driver</option>
                                        <option value="harvester" {{ old('role', $staff->role) == 'harvester' ? 'selected' : '' }}>Harvester</option>
                                        <option value="planting" {{ old('role', $staff->role) == 'planting' ? 'selected' : '' }}>Planting</option>
                                        <option value="irrigation" {{ old('role', $staff->role) == 'irrigation' ? 'selected' : '' }}>Irrigation</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="hire_date" class="form-label fw-bold">Hire Date *</label>
                                    <input type="date" class="form-control @error('hire_date') is-invalid @enderror" 
                                           id="hire_date" name="hire_date" value="{{ old('hire_date', $staff->hire_date?->toDateString()) }}" required>
                                    @error('hire_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="daily_wage" class="form-label fw-bold">Daily Wage (KES) *</label>
                                    <input type="number" step="0.01" class="form-control @error('daily_wage') is-invalid @enderror" 
                                           id="daily_wage" name="daily_wage" value="{{ old('daily_wage', $staff->daily_wage) }}" required>
                                    @error('daily_wage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="payment_type" class="form-label fw-bold">Payment Type *</label>
                                    <select class="form-select @error('payment_type') is-invalid @enderror" id="payment_type" name="payment_type" required>
                                        <option value="daily" {{ old('payment_type', $staff->payment_type) == 'daily' ? 'selected' : '' }}>Daily</option>
                                        <option value="weekly" {{ old('payment_type', $staff->payment_type) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                        <option value="monthly" {{ old('payment_type', $staff->payment_type) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="piece_rate" {{ old('payment_type', $staff->payment_type) == 'piece_rate' ? 'selected' : '' }}>Piece Rate</option>
                                    </select>
                                    @error('payment_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="status" class="form-label fw-bold">Status *</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="active" {{ old('status', $staff->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $staff->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="terminated" {{ old('status', $staff->status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="termination_date" class="form-label">Termination Date</label>
                                    <input type="date" class="form-control @error('termination_date') is-invalid @enderror" 
                                           id="termination_date" name="termination_date" value="{{ old('termination_date', $staff->termination_date?->toDateString()) }}">
                                    @error('termination_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Additional Info -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3"><i class="fas fa-sticky-note mr-2"></i>Additional Information</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="address" class="form-label">Address / Location</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="3">{{ old('address', $staff->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3">{{ old('notes', $staff->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Emergency Contact -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3"><i class="fas fa-phone-alt mr-2"></i>Emergency Contact</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="emergency_contact" class="form-label">Contact Name</label>
                                    <input type="text" class="form-control @error('emergency_contact') is-invalid @enderror" 
                                           id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact', $staff->emergency_contact) }}">
                                    @error('emergency_contact')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="emergency_phone" class="form-label">Contact Phone</label>
                                    <input type="text" class="form-control @error('emergency_phone') is-invalid @enderror" 
                                           id="emergency_phone" name="emergency_phone" value="{{ old('emergency_phone', $staff->emergency_phone) }}">
                                    @error('emergency_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('staff.show', $staff) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save mr-2"></i>Update Staff
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
