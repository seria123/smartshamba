@extends('layouts.MainLayout')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div class="flex items-center gap-3">
                <div class="bg-primary text-white rounded-lg p-2">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold mb-0">Add New Staff Member</h1>
                    <small class="text-gray-500">Register a new staff member to the system</small>
                </div>
            </div>
            <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary whitespace-nowrap">
                <i class="fas fa-arrow-left me-2"></i> Back to List
            </a>
        </div>

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

        <div class="card-modern p-4 sm:p-6">
            <form action="{{ route('staff.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <h5 class="text-primary fw-bold mb-3 flex items-center gap-2">
                        <i class="fas fa-user"></i> Personal Information
                    </h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-input-modern @error('first_name') is-invalid @enderror"
                                    id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="Enter first name" required>
                            </div>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-input-modern @error('last_name') is-invalid @enderror"
                                    id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Enter last name" required>
                            </div>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-input-modern @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}" placeholder="email@example.com">
                            </div>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="employee_id" class="form-label">Employee ID</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-id-badge"></i></span>
                                <input type="text" class="form-input-modern @error('employee_id') is-invalid @enderror"
                                    id="employee_id" name="employee_id" value="{{ old('employee_id') }}" placeholder="EMP-001">
                            </div>
                            @error('employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <h5 class="text-primary fw-bold mb-3 flex items-center gap-2">
                        <i class="fas fa-address-card"></i> Contact & Identity
                    </h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label for="phone" class="form-label">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-phone"></i></span>
                                <input type="text" class="form-input-modern @error('phone') is-invalid @enderror"
                                    id="phone" name="phone" value="{{ old('phone') }}" placeholder="254-123456789">
                            </div>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="national_id" class="form-label">National ID</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-id-card"></i></span>
                                <input type="text" class="form-input-modern @error('national_id') is-invalid @enderror"
                                    id="national_id" name="national_id" value="{{ old('national_id') }}" placeholder="Enter ID number">
                            </div>
                            @error('national_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="gender" class="form-label">Gender</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-venus-mars"></i></span>
                                <select class="form-select-modern @error('gender') is-invalid @enderror" id="gender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-calendar"></i></span>
                                <input type="date" class="form-input-modern @error('date_of_birth') is-invalid @enderror"
                                    id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                            </div>
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-textarea-modern @error('address') is-invalid @enderror"
                                id="address" name="address" rows="2" placeholder="Enter residential address">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <h5 class="text-primary fw-bold mb-3 flex items-center gap-2">
                        <i class="fas fa-briefcase"></i> Assignment Details
                    </h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="farm_id" class="form-label">Assigned Farm <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-seedling"></i></span>
                                <select class="form-select-modern @error('farm_id') is-invalid @enderror" id="farm_id" name="farm_id" required>
                                    <option value="">-- Select Farm --</option>
                                    @foreach(\App\Models\Farm::all() as $farm)
                                        <option value="{{ $farm->id }}" {{ old('farm_id') == $farm->id ? 'selected' : '' }}>
                                            {{ $farm->name }} ({{ $farm->location }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('farm_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-user-tag"></i></span>
                                <select class="form-select-modern @error('role') is-invalid @enderror" id="role" name="role" required>
                                    <option value="">-- Select Role --</option>
                                    <option value="manager">Manager</option>
                                    <option value="supervisor">Supervisor</option>
                                    <option value="worker">Worker</option>
                                    <option value="agronomist">Agronomist</option>
                                    <option value="general_worker">General Worker</option>
                                    <option value="technician">Technician</option>
                                    <option value="driver">Driver</option>
                                    <option value="harvester">Harvester</option>
                                    <option value="planting">Planting Specialist</option>
                                    <option value="irrigation">Irrigation Specialist</option>
                                </select>
                            </div>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <div>
                            <label for="hire_date" class="form-label">Hire Date <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-calendar-day"></i></span>
                                <input type="date" class="form-input-modern @error('hire_date') is-invalid @enderror"
                                    id="hire_date" name="hire_date" value="{{ old('hire_date', now()->toDateString()) }}" required>
                            </div>
                            @error('hire_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="employment_type" class="form-label">Employment Type <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-file-contract"></i></span>
                                <select class="form-select-modern @error('employment_type') is-invalid @enderror" id="employment_type" name="employment_type" required>
                                    <option value="permanent">Permanent</option>
                                    <option value="casual">Casual</option>
                                    <option value="seasonal">Seasonal</option>
                                </select>
                            </div>
                            @error('employment_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="daily_wage" class="form-label">Daily Wage (KES) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">KES</span>
                                <input type="number" step="0.01" class="form-input-modern @error('daily_wage') is-invalid @enderror text-end"
                                    id="daily_wage" name="daily_wage" value="{{ old('daily_wage', 0) }}" placeholder="0.00" required>
                            </div>
                            @error('daily_wage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <h5 class="text-primary fw-bold mb-3 flex items-center gap-2">
                        <i class="fas fa-user-shield"></i> Status & Termination
                    </h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-circle"></i></span>
                                <select class="form-select-modern @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="terminated" {{ old('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                                </select>
                            </div>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="termination_date" class="form-label">Termination Date</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-calendar-times"></i></span>
                                <input type="date" class="form-input-modern @error('termination_date') is-invalid @enderror"
                                    id="termination_date" name="termination_date" value="{{ old('termination_date') }}">
                            </div>
                            @error('termination_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <h5 class="text-primary fw-bold mb-3 flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i> Emergency Contact
                    </h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="emergency_contact" class="form-label">Contact Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-user-friends"></i></span>
                                <input type="text" class="form-input-modern @error('emergency_contact') is-invalid @enderror"
                                    id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact') }}" placeholder="Emergency contact name">
                            </div>
                            @error('emergency_contact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="emergency_phone" class="form-label">Contact Phone</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-phone"></i></span>
                                <input type="text" class="form-input-modern @error('emergency_phone') is-invalid @enderror"
                                    id="emergency_phone" name="emergency_phone" value="{{ old('emergency_phone') }}" placeholder="Emergency contact number">
                            </div>
                            @error('emergency_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <h5 class="text-primary fw-bold mb-3 flex items-center gap-2">
                        <i class="fas fa-sticky-note"></i> Additional Notes
                    </h5>
                    <div>
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-textarea-modern @error('notes') is-invalid @enderror"
                            id="notes" name="notes" rows="3" placeholder="Enter any additional notes...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary text-center">
                        <i class="fas fa-times me-2"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary text-center">
                        <i class="fas fa-save me-2"></i> Save Staff Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
