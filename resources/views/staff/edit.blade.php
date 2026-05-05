@extends('layouts.MainLayout')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div>
                                <h4 class="card-title mb-0 fw-semibold">Edit Staff Member</h4>
                                <small class="text-white opacity-75">Update staff information</small>
                            </div>
                        </div>
                        <a href="{{ route('staff.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-2"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('staff.update', $staff) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <!-- Personal Information Section -->
                        <div class="mb-5">
                            <h5 class="text-primary fw-bold mb-3">
                                <i class="fas fa-user me-2"></i>Personal Information
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control form-control-lg @error('first_name') is-invalid @enderror" 
                                            id="first_name" name="first_name" value="{{ old('first_name', $staff->first_name) }}" required>
                                    </div>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control form-control-lg @error('last_name') is-invalid @enderror" 
                                            id="last_name" name="last_name" value="{{ old('last_name', $staff->last_name) }}" required>
                                    </div>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Section -->
                        <div class="mb-5">
                            <h5 class="text-primary fw-bold mb-3">
                                <i class="fas fa-address-card me-2"></i>Contact & Identity
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="phone" class="form-label fw-semibold">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-phone"></i></span>
                                        <input type="text" class="form-control form-control-lg @error('phone') is-invalid @enderror" 
                                            id="phone" name="phone" value="{{ old('phone', $staff->phone) }}" placeholder="254-123456789">
                                    </div>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="national_id" class="form-label fw-semibold">National ID</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-id-card"></i></span>
                                        <input type="text" class="form-control form-control-lg @error('national_id') is-invalid @enderror" 
                                            id="national_id" name="national_id" value="{{ old('national_id', $staff->national_id) }}" placeholder="Enter ID number">
                                    </div>
                                    @error('national_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="gender" class="form-label fw-semibold">Gender</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-venus-mars"></i></span>
                                        <select class="form-select form-select-lg @error('gender') is-invalid @enderror" id="gender" name="gender">
                                            <option value="">Select Gender</option>
                                            <option value="male" {{ old('gender', $staff->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender', $staff->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                            <option value="other" {{ old('gender', $staff->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="date_of_birth" class="form-label fw-semibold">Date of Birth</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-calendar"></i></span>
                                        <input type="date" class="form-control form-control-lg @error('date_of_birth') is-invalid @enderror" 
                                            id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $staff->date_of_birth?->toDateString()) }}">
                                    </div>
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="address" class="form-label fw-semibold">Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-home"></i></span>
                                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                            id="address" name="address" rows="2" placeholder="Enter residential address">{{ old('address', $staff->address) }}</textarea>
                                    </div>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Assignment & Role Section -->
                        <div class="mb-5">
                            <h5 class="text-primary fw-bold mb-3">
                                <i class="fas fa-briefcase me-2"></i>Assignment Details
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="farm_id" class="form-label fw-semibold">Assigned Farm <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-seedling"></i></span>
                                        <select class="form-select form-select-lg @error('farm_id') is-invalid @enderror" id="farm_id" name="farm_id" required>
                                            <option value="">-- Select Farm --</option>
                                            @foreach(\App\Models\Farm::all() as $farm)
                                                <option value="{{ $farm->id }}" {{ old('farm_id', $staff->farm_id) == $farm->id ? 'selected' : '' }}>
                                                    {{ $farm->name }} ({{ $farm->location }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('farm_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="role" class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-user-tag"></i></span>
                                        <select class="form-select form-select-lg @error('role') is-invalid @enderror" id="role" name="role" required>
                                            <option value="">-- Select Role --</option>
                                            <option value="general_worker" {{ old('role', $staff->role) == 'general_worker' ? 'selected' : '' }}>General Worker</option>
                                            <option value="supervisor" {{ old('role', $staff->role) == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                            <option value="technician" {{ old('role', $staff->role) == 'technician' ? 'selected' : '' }}>Technician</option>
                                            <option value="driver" {{ old('role', $staff->role) == 'driver' ? 'selected' : '' }}>Driver</option>
                                            <option value="harvester" {{ old('role', $staff->role) == 'harvester' ? 'selected' : '' }}>Harvester</option>
                                            <option value="planting" {{ old('role', $staff->role) == 'planting' ? 'selected' : '' }}>Planting Specialist</option>
                                            <option value="irrigation" {{ old('role', $staff->role) == 'irrigation' ? 'selected' : '' }}>Irrigation Specialist</option>
                                        </select>
                                    </div>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label for="hire_date" class="form-label fw-semibold">Hire Date <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-calendar-day"></i></span>
                                        <input type="date" class="form-control form-control-lg @error('hire_date') is-invalid @enderror" 
                                            id="hire_date" name="hire_date" value="{{ old('hire_date', $staff->hire_date?->toDateString()) }}" required>
                                    </div>
                                    @error('hire_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="daily_wage" class="form-label fw-semibold">Daily Wage (KES) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">KES</span>
                                        <input type="number" step="0.01" class="form-control form-control-lg text-end @error('daily_wage') is-invalid @enderror" 
                                            id="daily_wage" name="daily_wage" value="{{ old('daily_wage', $staff->daily_wage) }}" placeholder="0.00" required>
                                    </div>
                                    @error('daily_wage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="payment_type" class="form-label fw-semibold">Payment Type <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-money-bill-wave"></i></span>
                                        <select class="form-select form-select-lg @error('payment_type') is-invalid @enderror" id="payment_type" name="payment_type" required>
                                            <option value="">-- Select Type --</option>
                                            <option value="daily" {{ old('payment_type', $staff->payment_type) == 'daily' ? 'selected' : '' }}>Daily</option>
                                            <option value="weekly" {{ old('payment_type', $staff->payment_type) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                            <option value="monthly" {{ old('payment_type', $staff->payment_type) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                            <option value="piece_rate" {{ old('payment_type', $staff->payment_type) == 'piece_rate' ? 'selected' : '' }}>Piece Rate</option>
                                        </select>
                                    </div>
                                    @error('payment_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Status Section -->
                        <div class="mb-5">
                            <h5 class="text-primary fw-bold mb-3">
                                <i class="fas fa-user-shield me-2"></i>Status & Termination
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-circle"></i></span>
                                        <select class="form-select form-select-lg @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="active" {{ old('status', $staff->status) == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status', $staff->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            <option value="terminated" {{ old('status', $staff->status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
                                        </select>
                                    </div>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label for="termination_date" class="form-label">Termination Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-calendar-times"></i></span>
                                        <input type="date" class="form-control form-control-lg @error('termination_date') is-invalid @enderror" 
                                            id="termination_date" name="termination_date" value="{{ old('termination_date', $staff->termination_date?->toDateString()) }}">
                                    </div>
                                    @error('termination_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact Section -->
                        <div class="mb-5">
                            <h5 class="text-primary fw-bold mb-3">
                                <i class="fas fa-exclamation-circle me-2"></i>Emergency Contact
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="emergency_contact" class="form-label fw-semibold">Contact Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-user-friends"></i></span>
                                        <input type="text" class="form-control form-control-lg @error('emergency_contact') is-invalid @enderror" 
                                            id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact', $staff->emergency_contact) }}" placeholder="Emergency contact name">
                                    </div>
                                    @error('emergency_contact')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="emergency_phone" class="form-label fw-semibold">Contact Phone</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-phone"></i></span>
                                        <input type="text" class="form-control form-control-lg @error('emergency_phone') is-invalid @enderror" 
                                            id="emergency_phone" name="emergency_phone" value="{{ old('emergency_phone', $staff->emergency_phone) }}" placeholder="Emergency contact number">
                                    </div>
                                    @error('emergency_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Available Equipment Section -->
                        <div class="mb-5">
                            <h5 class="text-primary fw-bold mb-3">
                                <i class="fas fa-tools me-2"></i>Available Equipment
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="d-flex flex-wrap gap-4 p-3 bg-light rounded">
                                        <div class="form-check form-check-lg">
                                            <input class="form-check-input" type="checkbox" name="available_equipment[]" value="tractor" id="equipment_tractor" {{ in_array('tractor', old('available_equipment', $staff->available_equipment ?? [])) ? 'checked' : '' }} style="width: 24px; height: 24px;">
                                            <label class="form-check-label ms-2" for="equipment_tractor" style="font-size: 1.1rem;">
                                                <i class="fas fa-tractor text-success"></i> Tractor
                                            </label>
                                        </div>
                                        <div class="form-check form-check-lg">
                                            <input class="form-check-input" type="checkbox" name="available_equipment[]" value="irrigation_system" id="equipment_irrigation" {{ in_array('irrigation_system', old('available_equipment', $staff->available_equipment ?? [])) ? 'checked' : '' }} style="width: 24px; height: 24px;">
                                            <label class="form-check-label ms-2" for="equipment_irrigation" style="font-size: 1.1rem;">
                                                <i class="fas fa-tint text-info"></i> Irrigation System
                                            </label>
                                        </div>
                                        <div class="form-check form-check-lg">
                                            <input class="form-check-input" type="checkbox" name="available_equipment[]" value="storage_facilities" id="equipment_storage" {{ in_array('storage_facilities', old('available_equipment', $staff->available_equipment ?? [])) ? 'checked' : '' }} style="width: 24px; height: 24px;">
                                            <label class="form-check-label ms-2" for="equipment_storage" style="font-size: 1.1rem;">
                                                <i class="fas fa-warehouse text-warning"></i> Storage Facilities
                                            </label>
                                        </div>
                                    </div>
                                    @error('available_equipment')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Additional Notes -->
                        <div class="mb-5">
                            <h5 class="text-primary fw-bold mb-3">
                                <i class="fas fa-sticky-note me-2"></i>Additional Notes
                            </h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                        id="notes" name="notes" rows="4" placeholder="Enter any additional notes...">{{ old('notes', $staff->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-3 pt-4 border-top">
                            <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                                <i class="fas fa-times me-2"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <i class="fas fa-save me-2"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection